<?php

/**
 * @version $Id: upload.php 24 2013-12-18 09:34:54Z szymon $
 * @package DJ-MediaTools
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 * DJ-MediaTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-MediaTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-MediaTools. If not, see <http://www.gnu.org/licenses/>.
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

abstract class DJUploadHelper {
	
	// default uploader settings
	private static $settings = array(
		'max_file_size' => '10mb',
		'chunk_size' => '1mb',
		'resize' => true, // resize image before upload - for html5 runtime image resizing is only possible on Firefox 3.5+ (with fixed quality) and Chrome
		'width' => 1600, // max resize image width
		'height' => 1200, //max resize image height
		'quality' => 90, // resize quality
		'filter' => 'jpg,gif,png,jpeg,webp', // Filter to apply when the user selects files. This is currently file extension filter
		'debug' => false,
		'url' => null,
		// events	
		'onUploadedEvent' => null,
		'onAddedEvent' => null
	);
	
	public static function getUploader($id = 'uploader', $settings = array()) {
		
		$session = Factory::getSession();
		$randomText = md5(rand(0,99));
		$session->set('randomText',$randomText);
		$settings = array_merge(self::$settings, $settings);
		$debug = $settings['debug'];
		$config = Factory::getConfig();
		
		$doc = Factory::getDocument();
		$doc->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
		$doc->addStyleSheet(Uri::root(true).'/media/com_osproperty/assets/js/upload/jquery.ui.plupload/css/jquery.ui.plupload.css');
		//HTMLHelper::_('behavior.framework', true);
		$version = new Version;
		if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
			$doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
			$doc->addScript(Uri::root(true).'/media/com_osproperty/assets/js/upload/jquery.noconflict.js');
		}else{
			HTMLHelper::_('bootstrap.framework');
		}
		//HTMLHelper::_('jquery.framework');
		$doc->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
		$doc->addScript(Uri::root(true).'/media/com_osproperty/assets/js/upload/plupload.full.js');
		$doc->addScript(Uri::root(true).'/media/com_osproperty/assets/js/upload/jquery.ui.plupload/plupload.js');
		//$doc->addScript(Uri::root().'media/com_osproperty/assets/js/djuploader.js');
		$component = 'com_osproperty';
		//$url = $settings['url'];
		if(Factory::getApplication()->isClient('administrator'))
		{
			$url = Uri::root().'administrator/index.php?option=com_osproperty&task=properties_newupload&tmpl=component&format=raw&randomText='.$randomText;
		}else{
			$url = Uri::root().'index.php?option=com_osproperty&task=ajax_newupload&tmpl=component&format=raw&randomText='.$randomText;
		}
		
		$js = "			
			jQuery(function(){
				
				plupload.addI18n({
					'Select files' : '".addslashes(Text::_('OS_UPLOADER_HEADER'))."',
					'Add files to the upload queue and click the start button.' : '".addslashes(Text::_('OS_UPLOADER_DESC'))."',
					'Filename' : '".addslashes(Text::_('OS_UPLOADER_FILENAME'))."',
					'Status' : '".addslashes(Text::_('OS_UPLOADER_STATUS'))."',
					'Size' : '".addslashes(Text::_('OS_UPLOADER_SIZE'))."',
					'Add Files' : '".addslashes(Text::_('OS_UPLOADER_ADD_FILES'))."',
					'Stop current upload' : '".addslashes(Text::_('OS_UPLOADER_STOP_CURRENT_UPLOAD'))."',
					'Start uploading queue' : '".addslashes(Text::_('OS_UPLOADER_START_UPLOADING_QUEUE'))."',
					'Uploaded %d/%d files': '".addslashes(Text::_('OS_UPLOADER_UPLOADED_N_FILES'))."',
					'N/A' : '".addslashes(Text::_('OS_UPLOADER_NA'))."',
					'Drag files here.' : '".addslashes(Text::_('OS_UPLOADER_DRAG_AND_DROP_TEXT'))."',
					'Stop Upload': '".addslashes(Text::_('OS_UPLOADER_STOP_UPLOAD'))."',
					'Start Upload': '".addslashes(Text::_('OS_UPLOADER_START_UPLOAD'))."',
					'%d files queued': '".addslashes(Text::_('OS_UPLOADER_N_FILES_QUEUED'))."',
					'File extension error.': '".addslashes(Text::_('OS_UPLOADER_EXT_ERROR'))."',
					'File size error.': '".addslashes(Text::_('OS_UPLOADER_SIZE_ERROR'))."'
				});
		";
			$js .= "
				var uploader$id = jQuery('#$id').plupload({
					// General settings
					runtimes : 'gears,browserplus,html5,silverlight,flash,html4',
					url : '$url',
					max_file_size : '".$settings['max_file_size']."',
					chunk_size : '".$settings['chunk_size']."',
					unique_names : true,";
			if($settings['resize']) $js .= "
					// Resize images on clientside if we can
					resize : {width : ".$settings['width'].", height : ".$settings['height'].", quality : ".$settings['quality']."},";
			$js .= "
					// Specify what files to browse for
					filters : [
						{title : 'Allowed files', extensions : '".$settings['filter']."'}
					],

					// Flash settings
					flash_swf_url : '".Uri::root(true)."/media/com_osproperty/assets/js/upload/plupload.flash.swf',

					// Silverlight settings
					silverlight_xap_url : '".Uri::root(true)."/media/com_osproperty/assets/js/upload/plupload.silverlight.xap',";
			
			
			$js .= "
					// Post init events, bound after the internal events
					init: {
						FilesAdded: function(up, files) {
							".($settings['onAddedEvent'] ? $settings['onAddedEvent'].'(up,files);':'');
			$js .= "
						},
						FileUploaded: function(up, file, info) {
							// Called when a file has finished uploading
							".($debug ? "eventlog('[FileUploaded] File:', file, 'Info:', info);":"")."
							".($settings['onUploadedEvent'] ? 'return '.$settings['onUploadedEvent'].'(up,file,info,"'.Uri::root().'","'.@$settings['label_generate'].'");':'')."
						}";
			
			$js .= "
					}
				});
			});
		";
		
		$doc->addScriptDeclaration($js);
		
		$html = '<div id="'.$id.'"><p>Unfortunately images multiupload was unable to start, there are probably some script errors on site.</p></div>';
		
		return $html;
	}
	
	public static function upload() {
		
		$app = Factory::getApplication();
		$config = Factory::getConfig();
		
		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		// Settings
		$targetDir = JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'osupload';
		//$targetDir = 'uploads';
		
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 12 * 3600; // Temp file age in seconds
		
		// 5 minutes execution time
		@set_time_limit(5 * 60);
		
		// Uncomment this one to fake upload time
		// usleep(5000);
		
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		
		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		
		// Make sure the fileName is unique but only if chunking is disabled

		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
		
			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;
		
			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}
		
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);
		
		// Remove old temp files
		if ($cleanupTargetDir) {
			if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
				while (($file = readdir($dir)) !== false) {
					$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
		
					// Remove temp file if it is older than the max age and is not the current file
					if (filemtime($tmpfilePath) < time() - $maxFileAge && $tmpfilePath != "{$filePath}.part") {
						@unlink($tmpfilePath);
					}
				}
				closedir($dir);
			} else {
				jexit('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}
		}
		
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen($_FILES['file']['tmp_name'], "rb");
		
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						jexit('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					@fclose($in);
					@fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					jexit('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				jexit('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = @fopen("php://input", "rb");
		
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					jexit('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		
				@fclose($in);
				@fclose($out);
			} else
				jexit('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
		
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);			
			
			if(strstr($filePath, '.php')){
				@unlink($filePath);
				jexit('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Wrong file format."}, "id" : "id"}');
			}
			
			if(strstr($filePath, '.jpg') || strstr($filePath, '.png') || strstr($filePath, '.gif') || strstr($filePath, '.jpeg') || strstr($filePath, '.webp'))
			{
				$imgInfo = getimagesize($filePath);				
				if(!isset($imgInfo[2])) { // not an image
					@unlink($filePath);
					jexit('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "File is not an image."}, "id" : "id"}');
				} else { // looking for evel, base64, php inside image
					
					$infected = 0;
					$fhandler = fopen($filePath, "r");
					while (!feof($fhandler))
					{
					    // Get the current line that the file is reading
					    $fline = fgets($fhandler) ;
					    if(strstr($fline, "eval")) {					    	
							$infected++;
							break;
						} else if(strstr($fline, "base64")) {
							$infected++;
							break;
						} else if(strstr($fline, "<?php")) {
							$infected++;
							break;
						}
					}
					fclose($fhandler);
					
					if($infected){
						@unlink($filePath);
						jexit('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "File is not an image."}, "id" : "id"}');
					}					
				}	
			}
		}
		jexit('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
		
	}
		
}
