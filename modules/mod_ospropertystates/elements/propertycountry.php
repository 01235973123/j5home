<?php
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * @version	1.5
 * @package	Osproperty
 * @author 	Dang Thuc Dam
 * @link 	http://www.joomdonation.com
 * @copyright Copyright (C) 2007 Ossoltion Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
define('DS',DIRECTORY_SEPARATOR);
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


	class JElementPropertycountry extends JElement
	{
		var	$_name = 'Propertycountry';
	
		function fetchElement($name, $value, &$node, $control_name)
		{
			
			$db = Factory::getDBO();
			$db->setQuery("SELECT `id` AS value, `country_name` AS text FROM #__osrs_countries order by country_name");
			$options = $db->loadObjectList();
			
			array_unshift($options,JHTML::_("select.option",'',Text::_('Select country')));
			
		    return JHTML::_('select.genericlist',  $options, $this->name, 'class= "input-large form-select form-control"', 'value', 'text', $value, $control_name.$name );
		}
	}
}else{
	class JFormFieldPropertycountry extends FormField
    {
    	var	$_name = 'Propertycountry';
    	function getInput()
    	{    
    		$typeArr[] = JHTML::_('select.option','','Select country');
	       	$db = Factory::getDbo();
	       	$db->setQuery("SELECT `id` AS value, `country_name` AS text FROM #__osrs_countries order by country_name");
	       	$typeObjects = $db->loadObjectList();
	       	$typeArr = array_merge($typeArr,$typeObjects);
			return HTMLHelper::_('select.genericlist',$typeArr, 'jform[params][country]', array(
    		    'option.text.toHtml' => false ,
    		    'option.value' => 'value', 
    		    'option.text' => 'text', 
    		    'list.attr' => ' class="input-large form-select form-control" ',
    		    'list.select' => $this->value    		        		
    		));	
    	}
    	
    }
}
