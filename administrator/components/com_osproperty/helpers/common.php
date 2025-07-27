<?php

/*------------------------------------------------------------------------
# common.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\User\User;

class HelperOspropertyCommon{
	/**
	*	Load Footer content
	**/
	static function loadFooter($option)
	{
		global $mainframe,$configClass;
		if(file_exists(JPATH_ROOT."/components/com_osproperty/version.txt"))
		{												
			$fh = fopen(JPATH_ROOT."/components/com_osproperty/version.txt","r");
			$version = fread($fh,filesize(JPATH_ROOT."/components/com_osproperty/version.txt"));
			@fclose($fh);
		}
		?>
		<div style="clear:both;">
		</div>
		<div style="width:100%;text-align:center;margin:15px;">
			<strong><a href="http://joomdonation.com/joomla-extensions/os-property-joomla-real-estate.html" target="_blank">OS Property</a></strong> Version <?php echo $version;?>, Copyright (C) 2011 - <?php echo date('Y'); ?> <strong><a href="https://www.joomdonation.com" target="_blank">Ossolution Team</a></strong>
		</div>
		<?php
	}
    /**
     * Download pdf file
     *
     * @param unknown_type $filelink
     */
    static function downloadxmlfile($filelink){
        while (@ob_end_clean());
        define('ALLOWED_REFERRER', '');
        // MUST end with slash (i.e. "/" )
        define('BASE_DIR',JPATH_ROOT."/tmp");

        // log downloads? true/false
        define('LOG_DOWNLOADS',false);

        // log file name
        define('LOG_FILE','downloads.log');

        // Allowed extensions list in format 'extension' => 'mime type'
        // If myme type is set to empty string then script will try to detect mime type
        // itself, which would only work if you have Mimetype or Fileinfo extensions
        // installed on server.
        $allowed_ext = array (
            'xml' => 'application/xml'
        );

        ################################################## ##################
        ### DO NOT CHANGE BELOW
        ################################################## ##################

        // If hotlinking not allowed then make hackers think there are some server problems
        if (ALLOWED_REFERRER !== ''
            && (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
        ) {
            die(Text::_("Internal server error. Please contact system administrator."));
        }

        // Make sure program execution doesn't time out
        // Set maximum script execution time in seconds (0 means no limit)
        //set_time_limit(0);

        if (!isset($filelink)) {
            die(Text::_("Please specify file name for download."));
        }

        // Get real file name.
        // Remove any path info to avoid hacking by adding relative path, etc.
        $fname = basename($filelink);

        // Check if the file exists
        // Check in subfolders too
        function find_file ($dirname, $fname, &$file_path) {
            $dir = opendir($dirname);
            while ($file = readdir($dir)) {
                if (empty($file_path) && $file != '.' && $file != '..') {
                    if (is_dir($dirname.'/'.$file)) {
                        find_file($dirname.'/'.$file, $fname, $file_path);
                    }
                    else {
                        if (file_exists($dirname.'/'.$fname)) {
                            $file_path = $dirname.'/'.$fname;
                            return;
                        }
                    }
                }
            }//end while

        } // find_file

        // get full file path (including subfolders)
        $file_path = '';
        find_file(BASE_DIR, $fname, $file_path);

        if (!is_file($file_path)) {
            die(Text::_("File does not exist. Make sure you specified correct file name."));
        }

        // file size in bytes
        $fsize = filesize($file_path);

        // file extension
        $fext = strtolower(substr(strrchr($fname,"."),1));

        // check if allowed extension
        if (!array_key_exists($fext, $allowed_ext)) {
            die(Text::_("Not allowed file type."));
        }

        // get mime type
        if ($allowed_ext[$fext] == '') {
            $mtype = '';
            // mime type is not set, get from server settings
            if (function_exists('mime_content_type')) {
                $mtype = mime_content_type($file_path);
            }
            else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME); // return mime type
                $mtype = finfo_file($finfo, $file_path);
                finfo_close($finfo);
            }
            if ($mtype == '') {
                $mtype = "application/force-download";
            }
        }
        else {
            // get mime type defined by admin
            $mtype = $allowed_ext[$fext];
        }

        // Browser will try to save file with this filename, regardless original filename.
        // You can override it if needed.


        $asfname = $fname;

        // set headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Type: $mtype");
        header("Content-Disposition: attachment; filename=\"$asfname\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $fsize);


        if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
            @set_time_limit(0);
        }

        HelperOspropertyCommon::readfile_chunked($file_path);
        exit();
    }
	/**
     * Download pdf file
     *
     * @param unknown_type $filelink
     */
    static function downloadfile($filelink){
    	while (@ob_end_clean());
    	define('ALLOWED_REFERRER', '');
		// MUST end with slash (i.e. "/" )
		define('BASE_DIR',JPATH_ROOT."/components/com_osproperty/images/csvform");
		
		// log downloads? true/false
		define('LOG_DOWNLOADS',false);
		
		// log file name
		define('LOG_FILE','downloads.log');
		
		// Allowed extensions list in format 'extension' => 'mime type'
		// If myme type is set to empty string then script will try to detect mime type
		// itself, which would only work if you have Mimetype or Fileinfo extensions
		// installed on server.
		$allowed_ext = array (
			// archives
			'zip' => 'application/zip',
			// documents
			'pdf' => 'application/pdf',
			'doc' => 'application/msword',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			// executables
			'exe' => 'application/octet-stream',
			// images
			'gif' => 'image/gif',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			// audio
			'mp3' => 'audio/mpeg',
			'wav' => 'audio/x-wav',
			// video
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'mov' => 'video/quicktime',
			'avi' => 'video/x-msvideo',
			'csv' => 'application/octet-stream'
		);
		
		################################################## ##################
		### DO NOT CHANGE BELOW
		################################################## ##################
		
		// If hotlinking not allowed then make hackers think there are some server problems
		if (ALLOWED_REFERRER !== ''
		&& (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false)
		) {
			die(Text::_("Internal server error. Please contact system administrator."));
		}
		
		// Make sure program execution doesn't time out
		// Set maximum script execution time in seconds (0 means no limit)
		//set_time_limit(0);
		
		if (!isset($filelink)) {
			die(Text::_("Please specify file name for download."));
		}
		
		// Get real file name.
		// Remove any path info to avoid hacking by adding relative path, etc.
		$fname = basename($filelink);
		
		// Check if the file exists
		// Check in subfolders too
		function find_file ($dirname, $fname, &$file_path) {
			$dir = opendir($dirname);
			while ($file = readdir($dir)) {
				if (empty($file_path) && $file != '.' && $file != '..') {
					if (is_dir($dirname.'/'.$file)) {
						find_file($dirname.'/'.$file, $fname, $file_path);
					}
					else {
						if (file_exists($dirname.'/'.$fname)) {
							$file_path = $dirname.'/'.$fname;
							return;
						}
					}	
				}
			}//end while
		
		} // find_file
		
		// get full file path (including subfolders)
		$file_path = '';
		find_file(BASE_DIR, $fname, $file_path);

		if (!is_file($file_path)) {
			die(Text::_("File does not exist. Make sure you specified correct file name."));
		}
		
		// file size in bytes
		$fsize = filesize($file_path);
		
		// file extension
		$fext = strtolower(substr(strrchr($fname,"."),1));
		
		// check if allowed extension
		if (!array_key_exists($fext, $allowed_ext)) {
			die(Text::_("Not allowed file type."));
		}
		
		// get mime type
		if ($allowed_ext[$fext] == '') {
			$mtype = '';
			// mime type is not set, get from server settings
			if ( function_exists('mime_content_type')) {
				$mtype = mime_content_type($file_path);
			}
			else if ( function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME); // return mime type
				$mtype = finfo_file($finfo, $file_path);
				finfo_close($finfo);
			}
			if ($mtype == '') {
				$mtype = "application/force-download";
			}
		}
		else {
			// get mime type defined by admin
			$mtype = $allowed_ext[$fext];
		}
		
		// Browser will try to save file with this filename, regardless original filename.
		// You can override it if needed.
		
		
		$asfname = $fname;
		
		// set headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: $mtype");
		header("Content-Disposition: attachment; filename=\"$asfname\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $fsize);
		
		
		if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
		    @set_time_limit(0);
	    }
		
		HelperOspropertyCommon::readfile_chunked($file_path);
		exit();
    }
    
    static function downloadfile2($file_path,$id){
    	while (@ob_end_clean());
    	$len = @ filesize($file_path);
		$cont_dis ='attachment';

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}
	
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Expires: 0");
	
	    header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="csv' .$id . '.csv";'
			. ' size=' . $len .';'
			); //RFC2183
	    header("Content-Length: "  . $len);
	    if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
		    @set_time_limit(0);
	    }
	    HelperOspropertyCommon::readfile_chunked($file_path);
		exit();
    }
    
    
    static function readfile_chunked($filename,$retbytes=true){
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
   			return false;
		}
		while (!feof($handle)) {
	   		$buffer = fread($handle, $chunksize);
	   		echo $buffer;
			@ob_flush();
			flush();
	   		if ($retbytes) {
	       		$cnt += strlen($buffer);
	   		}
		}
   		$status = fclose($handle);
	    if ($retbytes && $status) {
   			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
	/**
	 * Create New User
	 *
	 * @param unknown_type $username
	 * @param unknown_type $name
	 */
	static function createUser($username,$name){
		global $mainframe,$_jversion;
		$db = Factory::getDbo();
		//$authorize	=& Factory::getACL();
		$user 		= clone(Factory::getUser());
		
		// Get the form data.
		$config = Factory::getConfig();
		$app	= Factory::getApplication();
		$componentParams = ComponentHelper::getParams('com_users');
		$new_usertype = $componentParams->get('new_usertype', '2');

		// Initialise the table with JUser.
		$user = new User;
	
		// Prepare the data for the user object.
		$data['username']	= $username;
		$data['email']		= $username."@osproperty.com";
		$data['email2']		= $username."@osproperty.com";
		$data['password']	= $username;
		$data['password2']	= $username;
		$data['name']		= $name;
		$groups[0]			= $new_usertype;
		$data['groups']	 	= $groups;
		$useractivation = 0; //auto approval
		// Bind the data.
		if (!$user->bind($data)) {
			return false;
		}
		$user->sendEmail = 0;
		// Store the data.
		if (!$user->save()) {
			return false;
		}
		
		return $user->id;
	}
	/**
	 * remove white space in begin and end of the option in one array
	 *
	 * @param unknown_type $a
	 */
	static function stripSpaceArrayOptions($a){
		global $mainframe;
		if(count($a) > 0){
			for($i=0;$i<count($a);$i++){
				$a[$i] = trim($a[$i]);
			}
		}
		return $a;
	}

	
	/**
	 * Load city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $state_id
	 * @param unknown_type $city_id
	 * @return unknown
	 */
	static function loadCity($option,$state_id,$city_id,$class="input-medium form-select ilarge"){
		global $mainframe;
		$db = Factory::getDBO();
		$cityArr = array();
		$cityArr[]= HTMLHelper::_('select.option','0',' - '.Text::_('OS_SELECT_CITY').' - ');
		if($state_id > 0){
			$db->setQuery("Select id as value, city as text from #__osrs_cities where state_id = '$state_id' order by city");
			$cities = $db->loadObjectList();
			//$cityArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_CITY'));
			$cityArr   = array_merge($cityArr,$cities);
			$disabled  = "";
		}else{
			$disabled  = "disabled";
		}
		return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$cityArr,'city','class="'.$class.'"'.$disabled,'value','text',$city_id));
	}
	
	
	static function loadCityName($city){
		global $mainframe;
		$db = Factory::getDBO();
		$db->setQuery("Select city from #__osrs_cities where id = '$city'");
		$city_name = $db->loadResult();
		return $city_name;
	}
	
	
	static function loadNeighborHood($pid){
		$db = Factory::getDbo();
		$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
				." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
				." where a.pid = '$pid'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			?>
			<table width="100%">
				<?php
				for($i=0;$i<count($rows);$i++){
					$row = $rows[$i];
					?>
					<tr>
						<td width="30%" style="text-align:left;padding:5px;">
						<b><?php echo Text::_($row->neighborhood)?> </b></td> 
						<td width="70%" style="text-align:left;padding:5px;">
						<?php echo $row->mins?> <?php echo Text::_('OS_MINS')?> <?php echo Text::_('OS_BY')?> &nbsp;
						<b>
						<?php
						switch ($row->traffic_type){
							case "1":
								echo Text::_('OS_WALK');
							break;
							case "2":
								echo Text::_('OS_CAR');
							break;
							case "3":
								echo Text::_('OS_TRAIN');
							break;
						}
						?>
						</b>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<?php
		}
	}
	
	/**
	 * Show access value
	 *
	 * @param unknown_type $option
	 */
	static function loadAccessName($access){
		switch ($access){
			case "0":
				return Text::_('OS_PUBLIC');
			break;
			case "1":
				return Text::_('OS_REGISTERED');
			break;
			case "2":
				return Text::_('OS_SPECIAL');
			brea;
			default:
				return "";
			break;
		}
	}
	
	
	/**
	 * Show the currency Select list
	 *
	 * @param unknown_type $curr
	 */
	static function showCurrencySelectList($curr)
	{
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select id as value, concat(currency_name,' - ',currency_code,' - ',currency_symbol) as text from #__osrs_currencies where published = '1' order by currency_name");
		$currencies = $db->loadObjectList();
		if(intval($curr) == 0)
		{
			$curr = $configClass['general_currency_default'];
		}
		if(count($currencies) == 1)
		{
			echo "<input type='hidden' name='curr' value='".$curr."' />";
		}
		else
		{
			echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$currencies,'curr','class="input-large form-select" style="width:220px;"','value','text',$curr));
		}
	}
	
	/**
	 * Load currency
	 *
	 * @param unknown_type $curr
	 */
	static function loadCurrency($curr = 0){
		global $mainframe,$configClass;
		if(intval($curr) == 0){
			$curr = $configClass['general_currency_default'];
		}
		$db = Factory::getDbo();
		$db->setQuery("Select currency_symbol from #__osrs_currencies where id = '$curr'");
		$curr = $db->loadResult();
		return $curr;
	}
	
	/**
	 * Get the country_id in the filter page or edit item details page
	 *
	 * @return unknown
	 */
	static function getDefaultCountry(){
		global $configClass;
		if($configClass['show_country_id'] != ""){
			$countryArr = explode(",",$configClass['show_country_id']);
			if(count($countryArr) == 1){
				return $countryArr[0];
			}
		}
		return 0;
	}
	
	/**
	 * Check default country
	 * 
	 * @return boolean
	 * false : Use for one country
	 * true  : use for multiple countries
	 * 
	 */
	static function checkCountry(){
		global $configClass;
		if($configClass['show_country_id'] != ""){
			$countryArr = explode(",",$configClass['show_country_id']);
			if(count($countryArr) == 1){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Make the country list
	 *
	 * @param unknown_type $req_country_id
	 * @param unknown_type $name
	 * @param unknown_type $onChange
	 */
	public static function makeCountryList($req_country_id,$name,$onChange,$firstOption,$style){
		global $configClass;
		$db = Factory::getDbo();
		if($configClass['show_country_id'] != "")
		{
			if(HelperOspropertyCommon::checkCountry())
			{
				
				$db->setQuery("Select id as value, country_name as text from #__osrs_countries where 1=1 and id in (".$configClass['show_country_id'].") order by country_name");
				$countries = $db->loadObjectList();
				
				if($firstOption != ""){
					$countryArr[] = HTMLHelper::_('select.option','',$firstOption);
					$countryArr = array_merge($countryArr,$countries);
				}else{
					$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
					$countryArr = array_merge($countryArr,$countries);
				}
				
				return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$countryArr,$name,'class="form-select input-large ilarge" '.$onChange.' '.$style,'value','text',$req_country_id));
				
			}else{
				return "<input type='hidden' name='$name' value='".$configClass['show_country_id']."' id='$name'>";
			}
		}
		else
		{
			$db->setQuery("Select id as value, country_name as text from #__osrs_countries where 1=1 order by country_name");
			$countries = $db->loadObjectList();
			
			if($firstOption != ""){
				$countryArr[] = HTMLHelper::_('select.option','',$firstOption);
				$countryArr = array_merge($countryArr,$countries);
			}else{
				$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
				$countryArr = array_merge($countryArr,$countries);
			}
			return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$countryArr,$name,'class="form-select input-large ilarge" '.$onChange.' '.$style,'value','text',$req_country_id));
		}
	}
	
	/**
	 * Make the state list
	 *
	 * @param unknown_type $req_country_id
	 * @param unknown_type $req_state_id
	 * @param unknown_type $name
	 * @param unknown_type $onChange
	 * @param unknown_type $firstOption
	 * @return unknown
	 */
	static function makeStateList($req_country_id,$req_state_id,$name,$onChange,$firstOption,$style){
		global $configClass;
		$db = Factory::getDbo();
		$stateArr = array();
		if((!HelperOspropertyCommon::checkCountry()) or ($req_country_id > 0)){
			
			$query  = "Select id as value,state_name as text from #__osrs_states where published = 1 ";
			if($req_country_id > 0){
				$query .= " and country_id = '$req_country_id'";
			}else{
				$query .= " and country_id = '".$configClass['show_country_id']."'";
			}
			$query .= " order by state_name";
			$db->setQuery($query);
			$states = $db->loadObjectList();
			if($firstOption != ""){
				$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
				$stateArr   = array_merge($stateArr,$states);
			}else{
				$stateArr   = $states;
			}
			return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$stateArr,$name,' '.$onChange.' '.$style,'value','text',$req_state_id));
			
		}else{
			$stateArr[] = HTMLHelper::_('select.option','',$firstOption);
			return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$stateArr,$name,$style.' disabled','value','text'));
		}
	}

	static function makeCityList($req_country_id,$req_state_id,$req_city_id,$name,$onChange,$firstOption,$style){
        global $configClass;
        $db = Factory::getDbo();
        $cityArr = array();
        if($req_state_id > 0){
            $query  = "Select id as value, city as text from #__osrs_cities where published = 1 ";
            $query .= " and state_id = '$req_state_id'";
            $query .= " order by city";
            $db->setQuery($query);
            $cities = $db->loadObjectList();
            if($firstOption != ""){
                $cityArr[] = HTMLHelper::_('select.option','',$firstOption);
                $cityArr   = array_merge($cityArr, $cities);
            }else{
                $cityArr   = $cities;
            }
            return OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$cityArr, $name,' '.$onChange.' '.$style,'value','text',$req_city_id));
        }else{
            $cityArr[] = HTMLHelper::_('select.option','',$firstOption);
            return HTMLHelper::_('select.genericlist',$cityArr, $name,$style.' disabled','value','text');
        }
    }
	
	/**
	 * Check is Photo file
	 * Return false : if it is not the JPEG photo
	 * Return true  : if it is JPEG photo
	 */
	static function checkIsPhotoFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('jpg','jpeg','gif','png','webp','bmp');
		if(!in_array($ftype,$allowtype))
		{
			return false;
		}
		else
		{
			//return true;
			$imageinfo = getimagesize($_FILES[$element_name]['tmp_name']);
			if(strtolower($imageinfo['mime']) != 'image/jpeg'&& strtolower($imageinfo['mime']) != 'image/jpg'&& strtolower($imageinfo['mime']) != 'image/png' && strtolower($imageinfo['mime']) != 'image/gif' && strtolower($imageinfo['mime']) != 'image/webp') 
			{
			    return false;
			}
			else
			{
				return true;
			}
		}
	}
	
	/**
	 * Check is Document file
	 * Return false : if it is not Doc or PDF file
	 * Return true  : if it is Doc or PDF file
	 */
	static function checkIsDocumentFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('pdf','doc','docx');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			$type = strtolower($_FILES[$element_name]['type']);
			if (($type == "application/msword") || ($type == "application/pdf")){  
				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * Check is Zip file
	 * Return false : if it is not zip
	 * Return true  : if it is zip file
	 */
	static function checkIsArchiveFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('zip');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * Check is Zip file
	 * Return false : if it is not csv file
	 * Return true  : if it is csv file
	 */
	static function checkIsCsvFileUploaded($element_name){
		$file = $_FILES[$element_name];
		$fname = $file['name'];
		$ftype = end(explode('.', strtolower($fname)));
		$ftype = strtolower($ftype);
		$allowtype = array('csv');
		if(!in_array($ftype,$allowtype)){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * Check to see if my server has curl
	 *
	 * @return unknown
	 */
	static function _iscurlinstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Create the photo from main photo
	 *
	 * @param unknown_type $t
	 * @param unknown_type $l
	 * @param unknown_type $h
	 * @param unknown_type $w
	 * @param unknown_type $wall_image
	 */
	static function create_photo($t,$l,$h,$w,$photo_name,$type,$pid){
		global $configClass;
		jimport('joomla.filesystem.file');
		$configClass = OSPHelper::loadConfig();
		$ext = $ext[count($ext)-1];
		$path = JPATH_ROOT."/images/osproperty/properties/".$pid;
		$SourceFileArr = explode(".",$photo_name);
		$source_ext = strtolower($SourceFileArr[count($SourceFileArr) - 1]);

		switch ($source_ext) {
            case "jpg":
                $srcImg  = imagecreatefromjpeg($path.DS.$photo_name);
                break;
            case "png":
                $srcImg  = imagecreatefrompng($path.DS.$photo_name);
                break;
            case "gif":
                $srcImg  = imagecreatefromgif($path.DS.$photo_name);
                break;
        }
		
		$newImg  = imagecreatetruecolor($w, $h);
		imagecopyresampled($newImg, $srcImg, 0, 0, $l, $t, $w, $h, $w, $h);

		if($type == 0){
			switch ($source_ext) {
				case "jpg":
					//$srcImg  = imagecreatefromjpeg($path.DS.$photo_name);
					imagejpeg($newImg,$path."/thumb/".$photo_name);
					break;
				case "png":
					//$srcImg  = imagecreatefrompng($path.DS.$photo_name);
					imagepng($newImg,$path."/thumb/".$photo_name);
					break;
				case "gif":
					//$srcImg  = imagecreatefromgif($path.DS.$photo_name);
					imagegif($newImg,$path."/thumb/".$photo_name);
					break;
			}
			//copy file to resize
			//File::copy($path.DS.$photo_name,$path."/thumb/".$photo_name);
			
			//resize if the photo has big size
			$images_thumbnail_width = $configClass['images_thumbnail_width'];
			$images_thumbnail_height = $configClass['images_thumbnail_height'];
			$info = getimagesize($path."/thumb/".$photo_name);
			$width = $info[0];
			$height = $info[1];
			if($width > $images_thumbnail_width){
				//resize image to the original thumb width
				$image = new SimpleImage();
			    $image->load($path."/thumb/".$photo_name);
			    $image->resize($images_thumbnail_width,$images_thumbnail_height);
			    $image->save($path."/thumb/".$photo_name,$configClass['images_quality']);
			}
		}else{
			switch ($source_ext) {
				case "jpg":
					//$srcImg  = imagecreatefromjpeg($path.DS.$photo_name);
					imagejpeg($newImg,$path."/medium/".$photo_name);
					break;
				case "png":
					//$srcImg  = imagecreatefrompng($path.DS.$photo_name);
					imagepng($newImg,$path."/medium/".$photo_name);
					break;
				case "gif":
					//$srcImg  = imagecreatefromgif($path.DS.$photo_name);
					imagegif($newImg,$path."/medium/".$photo_name);
					break;
			}
			//copy file to resize
			//File::copy($path.DS.$photo_name,$path."/medium/".$photo_name);

			//resize if the photo has big size
			$images_large_width = $configClass['images_large_width'];
			$images_large_height = $configClass['images_large_height'];
			$info = getimagesize($path."/medium/".$photo_name);
			$width = $info[0];
			$height = $info[1];
			if($width > $images_large_width){
				//resize image to the original thumb width
				$image = new SimpleImage();
			    $image->load($path."/medium/".$photo_name);
			    $image->resize($images_large_width,$images_large_height);
			    $image->save($path."/medium/".$photo_name,$configClass['images_quality']);
			}
		}
	}
	
	/**
	 * Check max size of the image
	 *
	 * @param unknown_type $image_path
	 */
	static function returnMaxsize($image_path)
	{
		global $bootstrapHelper, $jinput, $mainframe,$configClass;

		$info			= getimagesize($image_path);
		
		$width			= $info[0];
		$height			= $info[1];
		$max_width_allowed = $configClass['max_width_size'];
		$max_height_allowed = $configClass['max_height_size'];
		
		if($height > $max_height_allowed && $width > $max_width_allowed)
		{
			$resize = 1;
			//resize to both
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$max_width_allowed,$max_height_allowed);
		}elseif(($height > $max_height_allowed) && ($width <= $max_width_allowed)){
			$resize = 2;
			//resize to height
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$width,$max_height_allowed);
		}elseif(($height <= $max_height_allowed) && ($width > $max_width_allowed)){
			$resize = 3;
			//resize to width
			/*
			$return = HelperOspropertyCommon::calResizePhoto($width,$height,$max_width_allowed,$max_height_allowed,$resize);
			//resize image
			$image = new SimpleImage();
		    $image->load($image_path);
		    $image->resize($return[0],$return[1]);
		    $image->save($image_path,100);
		    */
			OSPHelper::resizePhoto($image_path,$max_width_allowed,$height);
		}else{
			//do nothing
		}
	}
	
	
	static function calResizePhoto($width,$height, $maxwidth,$maxheight,$resize){
		global $mainframe;
		switch ($resize){
			case "1":
				$return 	= HelperOspropertyCommon::calResizeWidth($width,$height,$maxwidth,$maxheight);
				$newwidth 	= $return[0];
				$newheight 	= $return[1];
				if($newheight > $maxheight){
					$return 	= HelperOspropertyCommon::calResizeHeight($width,$height,$maxwidth,$maxheight);
				}
			break;
			case "2":
				$return 	= HelperOspropertyCommon::calResizeHeight($width,$height,$maxwidth,$maxheight);
			break;
			case "3":
				$return 	= HelperOspropertyCommon::calResizeWidth($width,$height,$maxwidth,$maxheight);
			break;
		}
		return $return;
	}
	
	static function calResizeWidth($width,$height,$maxwidth,$maxheight){
		$return = array();
		if($width > $maxwidth){
			$newwidth  = $maxwidth;
			$newheight = round($height*$maxwidth/$width);
			$return[0] = $newwidth;
			$return[1] = $newheight;
		}else{
			$return[0] = $width;
			$return[1] = $height;
		}
		return $return;
	}
	
	static function calResizeHeight($width,$height,$maxwidth,$maxheight){
		$return = array();
		if($height > $maxheight){
			$newheight = $maxheight;
			$newwidth  = round($width*$maxheight/$height);
			$return[0] = $newwidth;
			$return[1] = $newheight;
		}else{
			$return[0] = $width;
			$return[1] = $height;
		}
		return $return;
	}
	
	static function checkSpecial(){
		global $mainframe;$_jversion;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		$specialArr = array("Super Users","Super Administrator","Administrator","Manager");
		if($_jversion == "1.5"){
			if(in_array($user->usertype,$specialArr)){
				return true;
			}else{
				return false;
			}
		}else{
			$db->setQuery("Select b.title from #__user_usergroup_map as a inner join #__usergroups as b on b.id = a.group_id where a.user_id = '$user->id'");
			$usertype = $db->loadResult();
			if(in_array($usertype,$specialArr)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * Set Expired
	 *
	 * @param unknown_type $id
	 * @param unknown_type $type
	 * @param unknown_type $isNew
	 */
	public static function setExpiredTime($id,$type,$isNew){
		global $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$current_time 	= self::getRealTime();
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();

		if($count == 0){
			//check and calculate the expired and clean db time
			$unpublish_time = intval($configClass['general_time_in_days']);
			$remove_time	= intval($configClass['general_unpublished_days']);
			$feature_time	= intval($configClass['general_time_in_days_featured']);
			if($type == "f"){
				$unpublish_time = $feature_time;
				//calculate the unfeature time
				$feature_time    = $current_time + $feature_time*24*3600;
			}
			$send_appro		= $configClass['send_approximates'];
			$appro_days		= $configClass['approximates_days'];

			$unpublish_time = $current_time + $unpublish_time*24*3600;
			//calculate remove time
			$remove_time    = $unpublish_time + $remove_time*24*3600;

			//allow to send the approximates expired day
			if($send_appro == 1){
				$inform_time = $unpublish_time - $appro_days*24*3600;
				$inform_time = date("Y-m-d H:i:s",$inform_time);
			}else{
				$inform_time = "";
			}
			//change to time stamp
			$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
			$remove_time	= date("Y-m-d H:i:s",$remove_time);
			$feature_time   = date("Y-m-d H:i:s",$feature_time);
			//insert into #__osrs_expired
			$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,expired_feature_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$feature_time','$remove_time')");
			$db->execute();
			//update start publishing today
			OspropertyListing::updateStartPublishing($id);

		}else{//in the case this property is already in the expired table
			//check and calculate the expired and clean db time
			$unpublish_time = intval($configClass['general_time_in_days']);
			$remove_time	= intval($configClass['general_unpublished_days']);
			$feature_time	= intval($configClass['general_time_in_days_featured']);
			$send_appro		= $configClass['send_approximates'];
			$appro_days		= $configClass['approximates_days'];

			$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
			$expired = $db->loadObject();
			$expired_time = $expired->expired_time;
			$expired_feature_time = $expired->expired_feature_time;
			$expired_time_int = strtotime($expired_time);
			$expired_feature_int = strtotime($expired_feature_time);

			if($type == "f"){
				if($expired_feature_int > $current_time){
					$current_time = $expired_feature_int;
				}
				$unpublish_time = $feature_time;
				//calculate the unfeature time
				$feature_time    = $current_time + $feature_time*24*3600;
			}

			if($type == "n"){
				if($expired_time_int > $current_time){
					$current_time = $expired_time_int;
				}
			}

			$unpublish_time = $current_time + $unpublish_time*24*3600;
			if($unpublish_time < $expired_time_int){
				$unpublish_time = $expired_time_int;
			}
			//calculate remove time
			$remove_time    = $unpublish_time + $remove_time*24*3600;
			//allow to send the approximates expired day
			if($send_appro == 1){
				$inform_time = $unpublish_time - $appro_days*24*3600;
				$inform_time = date("Y-m-d H:i:s",$inform_time);
			}else{
				$inform_time = "";
			}
			//change to time stamp
			$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
			$remove_time	= date("Y-m-d H:i:s",$remove_time);
			$feature_time   = date("Y-m-d H:i:s",$feature_time);
			//insert into #__osrs_expired
			$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',expired_feature_time = '$feature_time',remove_from_database='$remove_time' WHERE pid = '$id'");
			$db->execute();
			//update start publishing today
			OspropertyListing::updateStartPublishing($id);
		}
	}
	
	public static function getRealTime(){
		$config = new JConfig();
		$offset = $config->offset;
		return strtotime(Factory::getDate('now',$offset));
	}
	
	/**
	 *
	 * static function to add dropdown menu
	 * @param string $vName        	
	 */
	public static function renderSubmenu($vName = 'dashboard')
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osrs_menus')
			->where('published = 1')
			->where('parent_id = 0')
			->order('ordering');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		$html = '';
		$html .= '<div id="submenu-box"><div class="m">';
		//<ul class="nav nav-tabs">';
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$html .= '<ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover osp-joomla4">';
		}
		else
		{
			$html .= '<ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover">';
		}
		for ($i = 0; $n = count($menus), $i < $n; $i++)
		{
			$menu = $menus[$i];
			$query->clear();
			$query->select('*')
				->from('#__osrs_menus')
				->where('published = 1')
				->where('parent_id = ' . intval($menu->id))
				->order('ordering');
			$db->setQuery($query);
			$subMenus = $db->loadObjectList();
			if (!count($subMenus))
			{
				$class = '';
				$extraClass = '';
				if ($menu->menu_task == $vName)
				{
					$class = ' class="active"';
					$extraClass = 'active';
				}				
				$html .= '<li' . $class . '>' ;
				$html .= '<a class="nav-link dropdown-item ' . $extraClass . '" href="index.php?option=com_osproperty&task=' . $menu->menu_task . '">';
				if($menu->menu_icon != ""){
					$html .= '<i class="'.$menu->menu_icon.'"></i>&nbsp;';
				}
				$html .= Text::_($menu->menu_name) . '</a></li>';
			}
			else
			{
				$class = ' class="dropdown"';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$lName = Factory::getApplication()->input->getString('layout','');
					if ( $vName == $subMenu->menu_task )
					{
						$class = ' class="dropdown active"';
						break;
					}
				}
				$html .= '<li' . $class . '>';

				if(OSPHelper::isJoomla4())
				{
					$dropdownToggle = 'data-bs-toggle="dropdown"';
				}
				else
				{
					$dropdownToggle = 'data-toggle="dropdown"';
				}

				$html .= '<a id="drop_' . $menu->id . '" href="#" '.$dropdownToggle.' role="button" class="dropdown-toggle nav-link dropdown-toggle">' ;
				if($menu->menu_icon != ""){
					$html .= '<i class="'.$menu->menu_icon.'"></i>&nbsp;';
				}
				$html .= Text::_($menu->menu_name) . ' <b class="caret"></b></a>';
				$html .= '<ul aria-labelledby="drop_' . $menu->id . '" role="menu" class="dropdown-menu" id="menu_' . $menu->id . '">';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$layoutLink = '';
					$class = '';
					$extraClass = '';
					$lName = Factory::getApplication()->input->getString('layout','');
					if ((!$subMenu->menu_layout && $vName == $subMenu->menu_task ) || ($lName != '' && $lName == $subMenu->menu_layout))
					{
						$class = ' class="active"';
						$extraClass = 'active';
					}
					else
					{
						$class = '';
						$extraClass = '';
					}
					$html .= '<li' . $class . '><a class="nav-link dropdown-item ' . $extraClass . '" href="index.php?option=com_osproperty&task=' .
						 $subMenu->menu_task . $layoutLink . '" tabindex="-1">' . Text::_($subMenu->menu_name) . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</li>';
			}
		}
		$html .= '</ul></div></div>';
		if (version_compare(JVERSION, '3.0', 'le'))
		{
			Factory::getDocument()->setBuffer($html, array('type' => 'modules', 'name' => 'submenu'));
		}
		else
		{
			echo $html;
		}
	}
	
	public static function getAvailableTags($pid){
		$db = Factory::getDBO();
		$query = "SELECT * FROM #__osrs_tags as tags";
		if (!is_null($itemID))
			$query .= " WHERE tags.id NOT IN (SELECT tagID FROM #__osrs_tag_xref WHERE pid=".(int)$pid.")";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	public static function getCurrentTags($pid){
		$db = Factory::getDBO();
		$itemID = (int)$itemID;
		$query = "SELECT tags.*
			        FROM #__osrs_tags AS tags 
			        JOIN #__osrs_tag_xref AS xref ON tags.id = xref.tag_id 
			        WHERE xref.pid = ".(int)$pid." ORDER BY xref.id ASC";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

	static function redirectPropertyEdit($pid,$msg)
    {
		$app = Factory::getApplication();
		$app->enqueueMessage($msg);
		if($pid > 0){
			$app->redirect("index.php?option=com_osproperty&task=properties_edit&cid[]=".$pid);
		}else{
			$app->redirect("index.php?option=com_osproperty&task=properties_add");
		}
	}

	/**
	 * Set approval and isFeature from jos_osrs_properties table
	 *
	 * @param unknown_type $type
	 * @param unknown_type $id
	 */
	static function setApproval($type,$id){
		global $mainframe;
		$db = Factory::getDbo();
		if($type == "f"){
			$db->setQuery("UPDATE #__osrs_properties SET isFeatured = '1',approved = '1',published = '1' WHERE id = '$id'");
			$db->execute();

		}else{
			$db->setQuery("UPDATE #__osrs_properties SET approved = '1',published = '1' WHERE id = '$id'");
			$db->execute();
		}
	}

	/**
	 * Load Time depend on configuration 
	 *
	 * @param unknown_type $time
	 * @param unknown_type $input_format
	 * @return unknown
	 */
	static function loadTime($time,$input_format){
		$db = Factory::getDbo();
		$db->setQuery("Select fieldvalue from #__osrs_configuration where id = '37'");
		$time_format = $db->loadResult();
		$time_format = str_replace("%","",$time_format);
		if($input_format == 1){
			return date($time_format,$time);
		}else{
			$time = strtotime($time);
			return date($time_format,$time);
		}
	}

	static function showLabel($name, $title, $tooltip = ''){
		$label = '';
		$text  = $title;

		// Build the class for the label.
		$class = !empty($tooltip) ? 'hasTooltip hasTip hasTooltip' : '';

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $name . '-lbl" for="' . $name . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($tooltip))
		{
			$label .= ' title="' . self::tooltipText(trim($text, ':'), $tooltip, 0) . '"';
		}

		$label .= '>' . $text . '</label>';

		return $label;
	}

	/**
	 * Converts a double colon seperated string or 2 separate strings to a string ready for bootstrap tooltips
	 *
	 * @param   string $title     The title of the tooltip (or combined '::' separated string).
	 * @param   string $content   The content to tooltip.
	 * @param   int    $translate If true will pass texts through Text.
	 * @param   int    $escape    If true will pass texts through htmlspecialchars.
	 *
	 * @return  string  The tooltip string
	 *
	 * @since   2.0.7
	 */
	public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1)
	{
		// Initialise return value.
		$result = '';

		// Don't process empty strings
		if ($content != '' || $title != '')
		{
			// Split title into title and content if the title contains '::' (old Mootools format).
			if ($content == '' && !(strpos($title, '::') === false))
			{
				list($title, $content) = explode('::', $title, 2);
			}

			// Pass texts through Text if required.
			if ($translate)
			{
				$title   = Text::_($title);
				$content = Text::_($content);
			}

			// Use only the content if no title is given.
			if ($title == '')
			{
				$result = $content;
			}
			// Use only the title, if title and text are the same.
			elseif ($title == $content)
			{
				$result = '<strong>' . $title . '</strong>';
			}
			// Use a formatted string combining the title and content.
			elseif ($content != '')
			{
				$result = '<strong>' . $title . '</strong><br />' . $content;
			}
			else
			{
				$result = $title;
			}

			// Escape everything, if required.
			if ($escape)
			{
				$result = htmlspecialchars($result);
			}
		}

		return $result;
	}

	/**
	 * Render showon string
	 *
	 * @param array $fields
	 *
	 * @return string
	 */
	public static function renderShowon($fields)
	{
		$output = array();

		$i = 0;

		foreach ($fields as $name => $values)
		{
			$i++;

			$values = (array) $values;

			$data = array(
				'field'  => $name,
				'values' => $values
			);

			if (version_compare(JVERSION, '3.6.99', 'ge'))
			{
				$data['sign'] = '=';
			}

			$data['op'] = $i > 1 ? 'AND' : '';

			$output[] = json_encode($data);
		}

		return '[' . implode(',', $output) . ']';
	}
}
?>
