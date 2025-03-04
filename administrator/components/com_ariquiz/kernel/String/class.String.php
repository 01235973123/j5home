<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

class AriString
{
	static public function htmlSpecialChars($value)
	{
		if (empty($value))
			return $value;
			
		$transTable = get_html_translation_table(HTML_SPECIALCHARS);
		$transTable['&'] = '&';
			
		return strtr($value, $transTable);
	}	
}