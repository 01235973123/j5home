<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

class AriResponse
{
	static public function sendContentAsAttach($fileContent, $fileName, $type = 'application/octet-stream')
	{		
		$fileName = rawurldecode($fileName);

		while (@ob_end_clean());
		header('Content-Type: ' . $type);
		header('Content-Disposition: attachment; filename="' . $fileName . '"');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Accept-Ranges: bytes');
		header('Cache-control: private');
		header('Pragma: private');
		header('Content-Length: ' . (string)strlen($fileContent));

		echo $fileContent;
		exit();
	}

	static public function sendBinaryRespose($data, $type = 'application/octet-stream')
	{
		while (@ob_end_clean());
		
		if ($type) 
			header('Content-Type: ' . $type);

		echo $data;
		exit();
	}
}