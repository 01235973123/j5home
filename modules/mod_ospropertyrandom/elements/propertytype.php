<?php
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
/*------------------------------------------------------------------------
# propertytype.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

global $_jversion;
$version = new Version();
$current_joomla_version = $version->getShortVersion();
$three_first_char = substr($current_joomla_version,0,3);
switch($three_first_char){
	case "1.5":
		global $mainframe;
		$_jversion = "1.5";
	break;
	case "2.5":
	case "1.6":
	case "1.7":
		global $mainframe;
		$mainframe = Factory::getApplication();
		$_jversion = "1.6";
	break;
}
include_once(JPATH_ADMINISTRATOR."/components/com_osproperty/classes/property.php");

if($_jversion == "1.5"){


	class JElementPropertytype extends JElement
	{
		var	$_name = 'Propertytype';
	
		function fetchElement($name, $value, &$node, $control_name)
		{
			
			$db = Factory::getDBO();
			$db->setQuery("SELECT `id` AS value, `type_name` AS text FROM #__osrs_types WHERE `published` = 1 ");
			$options = $db->loadObjectList();
			
			array_unshift($options,JHTML::_("select.option",'',Text::_('OSPROPERTY_SELECT_TYPE')));
			
		    return JHTML::_('select.genericlist',  $options, $this->name, 'class= "inputbox"', 'value', 'text', $value, $control_name.$name );
		}
	}
}else{
	class JFormFieldPropertytype extends FormField
    {
    	var	$_name = 'Propertytype';
    	function getInput()
    	{    
    		$typeArr[] = JHTML::_('select.option','','Select property type');
	       	$db = Factory::getDbo();
	       	$db->setQuery("Select id as value, type_name as text from #__osrs_types where published = '1' order by type_name");
	       	$typeObjects = $db->loadObjectList();
	       	$typeArr = array_merge($typeArr,$typeObjects);
			return HTMLHelper::_('select.genericlist',$typeArr, 'jform[params][type]', array(
    		    'option.text.toHtml' => false ,
    		    'option.value' => 'value', 
    		    'option.text' => 'text', 
    		    'list.attr' => ' class="input-large form-control form-select" ',
    		    'list.select' => $this->value    		        		
    		));	
    	}
    	
    }
}
