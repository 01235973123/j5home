<?php
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Client\FtpClient;
use Joomla\CMS\Filesystem\Path;
/*------------------------------------------------------------------------
# upload.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Upload methods for frontend and backend
 *
 * @package OSProperty
 * @since   1.0.0
 */
class JoomUpload extends CMSObject
{
  /**
   * The ID of the category in which
   * the images shall be uploaded
   *
   * @var int
   */
  public $catid = 0;

  /**
   * The title of the image if the original
   * file name shouldn't be used
   *
   * @var string
   */
  public $imgtitle = '';

  /**
   * The number of images that
   * a user has already uploaded
   *
   * @var int
   */
  public $counter = 0;

  /**
   * Set to true if a error occured
   * and the debugoutput should be displayed
   *
   * @var boolean
   */
  public $debug = false;

  /**
   * Holds information about the upload procedure
   *
   * @var string
   */
  protected $_debugoutput = '';

  /**
   * Determines whether we are in frontend
   *
   * @var boolean
   */
  protected $_site = true;

  /**
   * JApplication object
   *
   * @var object
   */
  protected $_mainframe;

  /**
   * JUser object
   *
   * @var object
   */
  protected $_user;

  /**
   * JDatabase object
   *
   * @var object
   */
  protected $_db;

  /**
   * Folder for saving image chunks
   *
   * @var string
   */
  protected $chunksFolder;

  /**
   * Probability for cleaning up the chunks folder
   * (e.g. 0.001 means every 1000 requests a cleanup is triggered)
   *
   * @var float
   */
  protected $chunksCleanupProbability = 0.01;

  /**
   * Expiration time of chunk folders in seconds
   *
   * @var int
   */
  protected $chunksExpireIn = 86400;

  /**
   * Constructor
   *
   * @return  void
   * @since   1.0.0
   */
  public function __construct(){
    $this->_mainframe = Factory::getApplication();    
    $this->_user      = Factory::getUser();
    $this->_db        = Factory::getDBO();
    $this->debug        = $this->_mainframe->getUserStateFromRequest('joom.upload.debug', 'debug', false, 'post', 'bool');
    $this->_debugoutput = $this->_mainframe->getUserStateFromRequest('joom.upload.debugoutput', 'debugoutput', '', 'post', 'string');
    $this->imgtitle     = $this->_mainframe->getUserStateFromRequest('joom.upload.title', 'imgtitle', '', 'string');

    $this->counter = $this->getImageNumber();

    $this->_site = $this->_mainframe->isSite();

    // TODO Parameter in OS Property configuration neccessary ?
    // Create folder for image chunks
    //$this->chunksFolder = JPATH_ROOT.'/tmp/ospropertychunks';
    //if(!Folder::exists($this->chunksFolder)){
    //	Folder::create($this->chunksFolder);
    //	File::copy(JPATH_ROOT.'/components/com_osproperty/index.html',JPATH_ROOT.'/tmp/ospropertychunks/index.html');
    //}
  }

  /**
   * Returns the debug output
   *
   * @return  mixed  The debug output or false if debug is not enabled or debug output is empty.
   * @since   3.0
   */
  public function getDebugOutput()
  {
    if($this->debug && !empty($this->_debugoutput))
    {
      return $this->_debugoutput;
    }

    return false;
  }

  /**
   * Calls the correct upload method according to the specified type
   *
   * @return  boolean True on success, false otherwise
   * @since   1.5.0
   */
  public function upload($type = 'single',$pid)
  {
    jimport('joomla.filesystem.file');

    switch($type)
    {
      default:	
      case 'ajax':
        return $this->uploadAJAX($pid);
        break;
     }
  }

  /**
   * AJAX upload
   *
   * An image is chosen and uploaded afore.
   *
   * @return  void
   * @since   3.0
   */
  protected function uploadAJAX($pid)
  {
  	global $configClass;
  	$this->chunksFolder = JPATH_ROOT.'/images/osproperty/properties/'.$pid;
    if(!Folder::exists($this->chunksFolder)){
    	Folder::create($this->chunksFolder);
    	File::copy(JPATH_ROOT.'/components/com_osproperty/index.html',$this->chunksFolder.'/index.html');
    }
    $mediumFolder = JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/medium';
    if(!Folder::exists($mediumFolder)){
    	Folder::create($mediumFolder);
    	File::copy(JPATH_ROOT.'/components/com_osproperty/index.html',$mediumFolder.'/index.html');
    }
    $thumbFolder = JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/thumb';
    if(!Folder::exists($thumbFolder)){
    	Folder::create($thumbFolder);
    	File::copy(JPATH_ROOT.'/components/com_osproperty/index.html',$thumbFolder.'/index.html');
    }
    
    $image               = Factory::getApplication()->input->files->get('qqfile');
    $qqtotalfilesize     = Factory::getApplication()->input->getInt('qqtotalfilesize', -1);
    $totalParts          = Factory::getApplication()->input->getInt('qqtotalparts', 1);
    $screenshot          = $image['tmp_name'];
    $origfilename        = Factory::getApplication()->input->getString('qqfilename', '');
    
    $screenshot_filesize = $image['size'];
    if(empty($origfilename))
    {
      $origfilename = $image['name'];
    }

    if($totalParts == 1 && $qqtotalfilesize > 0 && $screenshot_filesize != $qqtotalfilesize)
    {
      $this->setError(Text::_('OS_UPLOAD_ERROR_FILE_PARTLY_UPLOADED'));
      return false;
    }

    if($image['error'] > 0)
    {
      $errorMsg = Text::_('OS_AJAXUPLOAD_UPLOAD_FAILED').' '.Text::sprintf('OS_UPLOAD_ERROR_CODE', $image['error']);
      $this->setError($errorMsg);
      return false;
    }

    $cleanChunkDir = false;
    // Save a chunk
    if($totalParts > 1)
    {
      $partIndex    = Factory::getApplication()->input->getInt('qqpartindex',1);
      $uuid         = Factory::getApplication()->input->getInt('qquuid',1);

      if(!is_writable($this->chunksFolder))
      {
        $errorMsg = Text::sprintf('OS_AJAXUPLOAD_ERROR_CHUNKSDIR_NOTWRITABLE', $this->chunksFolder);
        $this->setError($errorMsg);
        return false;
      }

      // Create unique target folder for chunks
      $targetFolder = $this->chunksFolder.'/'.$uuid;
      
      if(!Folder::exists($targetFolder))
      {
        if(!Folder::create($targetFolder))
        {
          return false;
        }
      }
      // Save chunk in target folder
      $target  = $targetFolder.'/'.$partIndex;
      
      if(File::upload($screenshot, $target) === true)
      {
        // Last chunk
        if(($totalParts - 1) == $partIndex)
        {
          $target        = $targetFolder.'/'.($partIndex + 1);
          $cleanChunkDir = $targetFolder;
          $screenshot    = $target;

          if($fp_target = fopen($target, 'wb'))
          {
            for($parts = 0; $parts < $totalParts; $parts++)
            {
              $fp_chunk            = fopen($targetFolder.'/'.$parts, "rb");
              $screenshot_filesize = stream_copy_to_stream($fp_chunk, $fp_target);
              fclose($fp_chunk);
            }
            fclose($fp_target);
          }
          else
          {
            // Complete image could not be created
            return false;
          }
        }
        else
        {
          // Another chunk will arrive later
          return true;
        }
      }
      else
      {
        // Chunk could not be saved
        return false;
      }
    }

    $this->_debugoutput = '<hr />';
    $this->_debugoutput .= Text::sprintf('OS_UPLOAD_FILENAME', $origfilename).'<br />';

    // Image size must not exceed the setting in backend if we are in frontend
    $post_max_size = @ini_get('post_max_size');
    if(!empty($post_max_size))
    {
      $post_max_size   = OSPHelper::iniToBytes($post_max_size);
      $chunkSize = (int) min(500000, (int)(0.8 * $post_max_size));
    }
    
    if($this->_site && $screenshot_filesize >$chunkSize)
    {
      $errorMsg = Text::sprintf('OS_UPLOAD_OUTPUT_MAX_ALLOWED_FILESIZE', 20);
      
      $this->setError($errorMsg);
      $this->_debugoutput .= $errorMsg.'<br />';
      $this->debug = true;
      return false;
    }
	
    // Get extension
    $tag = strtolower(File::getExt($origfilename));

    // Check for right format
    if(   (($tag != 'jpeg') && ($tag != 'jpg') && ($tag != 'png') && ($tag != 'gif'))
            || strlen($screenshot) == 0
            || $screenshot == 'none'
    )
    {
      $errorMsg = Text::_('OS_UPLOAD_OUTPUT_INVALID_IMAGE_TYPE');
      $this->setError($errorMsg);
      $this->_debugoutput .= $errorMsg.'<br />';
      $this->debug = true;
      return false;
    }
    
    $filecounter = null;
    if(    ($this->_site )
            ||
            (!$this->_site )
    )
    {
      $filecounter = $this->_getSerial();
    }

    // Create new filename
    // If generic filename set in backend use them
    if(    ($this->_site )
            ||
            (!$this->_site )
    )
    {
      $oldfilename = $origfilename;
      $newfilename = $origfilename;
    }
    else
    {
      $oldfilename = $this->imgtitle;
      $newfilename = $this->imgtitle;
    }

    $newfilename = $this->_genFilename($newfilename, $tag, $filecounter);
   
	// We'll assume that this file is ok because with open_basedir,
	// we can move the file, but may not be able to access it until it's moved
    //$return = File::upload($screenshot,$this->chunksFolder.'/'.$newfilename);
    if($cleanChunkDir !== false)
    {
      $return = File::move($screenshot,$this->chunksFolder.'/'.$newfilename);
      // Clean up chunk directory
      Folder::delete($cleanChunkDir);
    }
    else
    {
      // We'll assume that this file is ok because with open_basedir,
      // we can move the file, but may not be able to access it until it's moved
      $return = File::upload($screenshot,$this->chunksFolder.'/'.$newfilename);
    }
    //move_uploaded_file($screenshot,$this->chunksFolder.'/'.$newfilename);
    if(!$return)
    {
      $errorMsg = Text::sprintf('OS_UPLOAD_ERROR_UPLOADING', $this->chunksFolder.'/'.$newfilename);
      
      $this->setError($errorMsg);
      $this->_debugoutput .= $errorMsg.'<br />';
      $this->debug = true;
      return false;
    }

    
    // Set permissions of uploaded file
    $return = self::chmod($this->chunksFolder.'/'.$newfilename, '0644');
    if(!$return){
    	$errorMsg = $this->_ambit->getImg('orig_path', $newfilename, null, $this->catid).' '.Text::_('OS_COMMON_CHECK_PERMISSIONS');
        $this->_debugoutput .= $errorMsg.'<br />';
        $this->debug = true;
        return false;
    }
    
    //check mine type
    $size = getimagesize($this->chunksFolder.'/'.$newfilename);
    $mine = $size['mime'];
    if(($mine != "image/jpeg") and ($mine != "image/jpg") and ($mine != "image/png") and ($mine != "image/gif")){
    	  File::delete($this->chunksFolder.'/'.$newfilename);
    	  $errorMsg = Text::_('OS_UPLOAD_OUTPUT_INVALID_IMAGE_TYPE');
	      $this->setError($errorMsg);
	      $this->_debugoutput .= $errorMsg.'<br />';
	      $this->debug = true;
	      return false;
    }
    
    $db = Factory::getDbo();
    $db->setQuery("Select count(id) from #__osrs_photos where pro_id = '$pid' order by ordering desc");
    $total_photo_uploaded = $db->loadResult();
    
    $config = OSPHelper::loadConfig();
    $limit_photos = $config['limit_upload_photos'];
    
    $mainframe = Factory::getApplication();
    if(!$mainframe->isClient('administrator'))
	{
    	if($limit_photos <= $total_photo_uploaded){
    		  File::delete($this->chunksFolder.'/'.$newfilename);
	    	  $errorMsg = Text::_('OS_LIMIT_UPLOADED');
		      $this->setError($errorMsg);
		      $this->_debugoutput .= $errorMsg.'<br />';
		      $this->debug = true;
		      return false;
    	}
    }

    $this->_debugoutput .= Text::_('OS_UPLOAD_OUTPUT_UPLOAD_COMPLETE').'<br />';
    //move to medium and thumb folder
    File::copy($this->chunksFolder.'/'.$newfilename,$mediumFolder.'/'.$newfilename);
    File::copy($this->chunksFolder.'/'.$newfilename,$thumbFolder.'/'.$newfilename);
    
    // Create thumbnail and detail image
    
    $medium_width = $configClass['images_large_width'];
	$medium_height = $configClass['images_large_height'];
	OSPHelper::resizePhoto($mediumFolder.'/'.$newfilename,$medium_width,$medium_height);
    $thumb_width = $configClass['images_thumbnail_width'];
	$thumb_height = $configClass['images_thumbnail_height'];
	OSPHelper::resizePhoto($thumbFolder.'/'.$newfilename,$thumb_width,$thumb_height);
	
	HelperOspropertyCommon::returnMaxsize($this->chunksFolder.'/'.$newfilename);
	
    // Insert database entry
    $db->setQuery("Select ordering from #__osrs_photos where pro_id = '$pid' order by ordering desc");
    $ordering = $db->loadResult();
    $ordering++;
    $db->setQuery("Insert into #__osrs_photos (id,pro_id,image,ordering) values (NULL,'$pid','$newfilename','$ordering')");
    $db->execute();

	//generate water maker image
	OSPHelper::generateWaterMark($pid);
    
    $this->_debugoutput .= Text::_('OS_UPLOAD_OUTPUT_IMAGE_SUCCESSFULLY_ADDED').'<br />';
    $this->_debugoutput .= Text::sprintf('OS_UPLOAD_NEW_FILENAME', $newfilename).'<br />';

    // Reset file counter, delete original and create special gif selection and debug information
    $this->_mainframe->setUserState('joom.upload.filecounter', 0);
    $this->_mainframe->setUserState('joom.upload.delete_original', false);
    $this->_mainframe->setUserState('joom.upload.create_special_gif', false);
    $this->_mainframe->setUserState('joom.upload.debug', false);
    $this->_mainframe->setUserState('joom.upload.debugoutput', null);
    
    $row = new stdClass();
    $row->id = $filecounter;
    $row->imgtitle = $oldfilename;
    $row->url = Uri::root().'images/osproperty/properties/'.$pid.'/'.$newfilename;
    return $row;
  }
  
  /**
   * Changes the permissions of a directory (or file)
   * either by the FTP-Layer if enabled
   * or by JPath::setPermissions (chmod()).
   *
   * Not sure but probable: J! 1.6 will use
   * FTP-Layer automatically in setPermissions
   * so JoomFile::chmod will become unnecessary.
   *
   * @param   string  $path   Directory or file for which the permissions will be changed
   * @param   string  $mode   Permissions which will be applied to $path
   * @param   boolean $is_dir True if the given path is a directory, false if it is a file
   * @return  boolean True on success, false otherwise
   * @since   1.5.0
   */
  public static function chmod($path, $mode, $is_dir = false)
  {
    static $ftpOptions;

    if(!isset($ftpOptions))
    {
      // Initialize variables
      jimport('joomla.client.helper');
      $ftpOptions = ClientHelper::getCredentials('ftp');
    }

    if($ftpOptions['enabled'] == 1)
    {
      // Connect the FTP client
      jimport('joomla.client.ftp');
      $ftp = FtpClient::getInstance($ftpOptions['host'], $ftpOptions['port'], null, $ftpOptions['user'], $ftpOptions['pass']);
      // Translate path to FTP path
      $path = Path::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');

      return $ftp->chmod($path, $mode);
    }
    else
    {
      if($is_dir)
      {
        return Path::setPermissions(Path::clean($path), null, $mode);
      }

      return Path::setPermissions(Path::clean($path), $mode, null);
    }
  }

  /**
   * Generates filenames
   * e.g. <Name/gen. Title>_<opt. Filecounter>_<Date>_<Random Number>.<Extension>
   *
   * @param   string    $filename     Original upload name e.g. 'malta.jpg'
   * @param   string    $tag          File extension e.g. 'jpg'
   * @param   int       $filecounter  Optinally a filecounter
   * @return  string    The generated filename
   * @since   1.0.0
   */
  protected function _genFilename($filename, $tag, $filecounter = null)
  {
    $filedate = date('Ymd');

    // Remove filetag = $tag incl '.'
    // Only if exists in filename
    if(stristr($filename, $tag))
    {
      $filename = substr($filename, 0, strlen($filename)-strlen($tag)-1);
    }

      mt_srand();
      $randomnumber = mt_rand(1000000000, 2099999999);

      // New filename
      if(is_null($filecounter))
      {
        $newfilename = $filename.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
      }
      else
      {
        $newfilename = time().$filename.'_'.$filecounter.'_'.$filedate.'_'.$randomnumber.'.'.$tag;
      }
      $newfilename = strtolower(str_replace(" ","",$newfilename));
      return $newfilename;
  }

  /**
   * Calculates the serial number for images file names and titles
   *
   * @return  int       New serial number
   * @since   1.0.0
   */
  protected function _getSerial()
  {
    static $picserial;

    // Check if the initial value is already calculated
    if(isset($picserial))
    {
      $picserial++;

      // Store the next value in the session
      $this->_mainframe->setUserState('joom.upload.filecounter', $picserial + 1);

      return $picserial;
    }

    // Start value set in backend
    $filecounter = $this->_mainframe->getUserStateFromRequest('joom.upload.filecounter', 'filecounter', 0, 'post', 'int');

    // If there is no starting value set, disable numbering
    if(!$filecounter)
    {
      return null;
    }

    // No negative starting value
    if($filecounter < 0)
    {
      $picserial = 1;
    }
    else
    {
      $picserial = $filecounter;
    }

    return $picserial;
  }

  /**
   * Sets new ordering according to $config->jg_uploadorder
   *
   * @param   object    $row  Holds the data of the new image
   * @return  int       The new ordering number
   * @since   1.0.0
   */
  protected function _getOrdering($row)
  {
    switch($this->_config->get('jg_uploadorder'))
    {
      case 1:
        $ordering = $row->getPreviousOrder('catid = '.$row->catid);
        break;
      case 2:
        $ordering = $row->getNextOrder('catid = '.$row->catid);
        break;
      default;
        $ordering = 1;
        break;
    }

    return $ordering;
  }

  /**
   * Calculates whether the memory limit is enough
   * to work on a specific image.
   *
   * @param   string  $filename The filename of the image and the path to it.
   * @param   string  $format   The image file type (e.g. 'gif', 'jpg' or 'png')
   * @return  boolean True, if we have enough memory to work, false otherwise
   * @since   1.0.0
   */
  protected function checkMemory($filename, $format)
  {
    if($this->_config->get('jg_thumbcreation') == 'im')
    {
      // ImageMagick isn't dependent on memory_limit
      return true;
    }

    if((function_exists('memory_get_usage')) && (ini_get('memory_limit')))
    {
      $imageInfo = getimagesize($filename);
      $jpgpic = false;
      switch(strtoupper($format))
      {
        case 'GIF':
          // Measured factor 1 is better
          $channel = 1;
          break;
        case 'JPG':
        case 'JPEG':
        case 'JPE':
          $channel = $imageInfo['channels'];
          $jpgpic=true;
          break;
        case 'PNG':
          // No channel for png
          $channel = 3;
          break;
      }
      $MB  = 1048576;
      $K64 = 65536;

      if($this->_config->get('jg_fastgd2thumbcreation') && $jpgpic && $this->_config->get('jg_thumbcreation') == 'gd2')
      {
        // Function of fast gd2 creation needs more memory
        $corrfactor = 2.1;
      }
      else
      {
        $corrfactor = 1.7;
      }

      $memoryNeeded = round(($imageInfo[0]
                             * $imageInfo[1]
                             * $imageInfo['bits']
                             * $channel / 8
                             + $K64)
                             * $corrfactor);

      $memoryNeeded = memory_get_usage() + $memoryNeeded;
      // Get memory limit
      $memory_limit = @ini_get('memory_limit');
      if(!empty($memory_limit) && $memory_limit != 0)
      {
        $memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
      }

      if($memory_limit != 0 && $memoryNeeded > $memory_limit)
      {
        $memoryNeededMB = round ($memoryNeeded / 1024 / 1024, 0);
        $this->_debugoutput .= Text::_('OS_UPLOAD_OUTPUT_ERROR_MEM_EXCEED').
                        $memoryNeededMB." MByte ("
                        .$memoryNeeded.") Serverlimit: "
                        .$memory_limit/$MB."MByte (".$memory_limit.")<br />" ;
        return false;
      }
    }

    return true;
  }

  /**
   * Rollback an erroneous upload
   *
   * @param   string  $original Path to original image
   * @param   string  $detail   Path to detail image
   * @param   string  $thumb    Path to thumbnail
   * @return  void
   * @since   1.0.0
   */
  protected function rollback($original, $detail, $thumb)
  {
    if(!is_null($original) && File::exists($original))
    {
      $return = File::delete($original);
      if($return)
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_ORGDEL_OK').'</p>';
      }
      else
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_ORGDEL_NOK').'</p>';
      }
    }

    if(!is_null($detail) && File::exists($detail))
    {
      $return = File::delete($detail);
      if($return)
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_DTLDEL_OK').'</p>';
      }
      else
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_DTLDEL_NOK').'</p>';
      }
    }

    if(!is_null($thumb) && File::exists($thumb))
    {
      $return = File::delete($thumb);
      if($return)
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_THBDEL_OK').'</p>';
      }
      else
      {
        $this->_debugoutput .= '<p>'.Text::_('OS_UPLOAD_OUTPUT_RB_THBDEL_NOK').'</p>';
      }
    }
  }
  /**
   * Returns the number of images of the current user
   *
   * @return  int     The number of images of the current user
   * @since   1.5.5
   */
  protected function getImageNumber()
  {
   
    return rand(10000,99999);
  }

  /**
   * Creates the database entry for a successfully uploaded image
   *
   * @param   object  $row          The HTMLHelper object of the images table to work with
   * @param   string  $origfilename The original file name of the uploaded image
   * @param   string  $newfilename  The new file name for the image
   * @param   string  $tag          The extension of the uploaded image
   * @param   int     $serial       The counter for the numbering of the image titles
   * @return  boolean True on success, false otherwise
   * @since   1.5.7
   */
  protected function registerImage($row, $origfilename, $newfilename, $tag, $serial = null)
  {
    // Get the specified image information (either from session or from post)
    $old_info = $this->_mainframe->getUserState('joom.upload.post');
    $cur_info = (!is_null($old_info)) ? $old_info : array();
    $new_info = JRequest::get('post');

    // Prevent setting access level in frontend
    if(isset($new_info['access']) && $this->_site)
    {
      unset($new_info['access']);
    }

    // Save the new value only if it was set in this request
    if(count($new_info))
    {
      $this->_mainframe->setUserState('joom.upload.post', $new_info);
      $data = $new_info;
    }
    else
    {
      $data = $cur_info;
    }

    return true;
  }

  /**
   * Analyses an error code and returns its text
   *
   * @param   int     $uploaderror  The errorcode
   * @return  string  The error message
   * @since   1.0.0
   */
  protected function checkError($uploaderror)
  {
    // Common PHP errors
    $uploadErrors = array(
      1 => Text::_('OS_UPLOAD_ERROR_PHP_MAXFILESIZE'),
      2 => Text::_('OS_UPLOAD_ERROR_HTML_MAXFILESIZE'),
      3 => Text::_('OS_UPLOAD_ERROR_FILE_PARTLY_UPLOADED'),
      4 => Text::_('OS_UPLOAD_ERROR_FILE_NOT_UPLOADED')
    );

    if(in_array($uploaderror, $uploadErrors))
    {
      return Text::sprintf('OS_UPLOAD_ERROR_CODE', $uploadErrors[$uploaderror]);
    }
    else
    {
      return Text::sprintf('OS_UPLOAD_ERROR_CODE', Text::_('OS_UPLOAD_ERROR_UNKNOWN'));
    }
  }
}
