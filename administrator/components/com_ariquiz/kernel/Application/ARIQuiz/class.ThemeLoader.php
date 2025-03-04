<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class AriQuizThemeLoader 
{
	var $_name;
	
	function load()
	{
		$doc =& JFactory::getDocument();
		
		$theme = $this->getName();
		$doc->addStyleSheet(JURI::root(true) . '/components/com_ariquiz/themes/' . $theme . '/css/style.css?v=' . ARIQUIZ_VERSION);
	}
	
	function getName()
	{
		$name = $this->_name;

		if (empty($name))
		{
			$r = null;
			if (!preg_match( '/AriQuizThemeLoader_(.*)/i', get_class($this), $r)) 
			{
				throw new Exception("AriQuizThemeLoader::getName() : Cannot get or parse class name.", E_USER_ERROR);
			}
			
			$name = strtolower($r[1]);
		}

		return $name;
	} 
}