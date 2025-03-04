<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

require_once dirname(__FILE__) . DS . 'files.php';

class AriQuizControllerImages extends AriQuizControllerBaseFiles
{
	function getGroup()
	{
		return ARIQUIZ_FOLDER_IMAGES;
	}
	
	function _isAcceptableFile($file)
	{
		return !!getimagesize($file);
	}
} 