<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

jimport('joomla.filesystem.folder');

class JElementQuiztheme extends JElement
{
	var	$_name = 'Quiztheme';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$folders = JFolder::folders(JPATH_ROOT . DS . 'components' . DS . 'com_ariquiz' . DS . 'themes', '^[^_]');
		$themes = array();
		if (is_array($folders))
		{
			foreach ($folders as $folder)
			{
				$themes[] = array(
					'text' => ucfirst($folder),
					'value' => $folder
				);
			}
		}
		
		return JHTML::_(
			'select.genericlist', 
			$themes, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select"', 
			'value',
			'text',
			$value,
			$control_name . $name);
	}
}