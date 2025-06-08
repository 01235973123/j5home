<?php
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
/*------------------------------------------------------------------------
# propertytype.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2015 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_osproperty".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."property.php");

class JFormFieldPropertytype extends FormField
{
	var	$_name = 'Propertytype';
	function getInput()
	{    
		$typeArr[] = JHTML::_('select.option',0,'Any');
       	$db = Factory::getDbo();
       	$db->setQuery("Select id as value, type_name as text from #__osrs_types where published = '1' order by type_name");
       	$typeObjects = $db->loadObjectList();
       	$typeArr = array_merge($typeArr,$typeObjects);
		return HTMLHelper::_('select.genericlist',$typeArr, 'jform[params][property_type]', array(
		    'option.text.toHtml' => false ,
		    'option.value' => 'value', 
		    'option.text' => 'text', 
		    'list.attr' => ' class="input-large form-control form-select" ',
		    'list.select' => $this->value    		        		
		));	
	}
	
}
