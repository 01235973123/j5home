<?php
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
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

class JFormFieldPropertymarketstatus extends FormField
{
	var	$_name = 'Propertymarketstatus';
	function getInput()
	{   
		if ($this->element['value'] > 0) {
    	    $selectedValue = (int) $this->element['value'] ;
    	} else {
    	    $selectedValue = (int) $this->value ;
    	}
		include_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
		$configClass = OSPHelper::loadConfig();
		$marketArr[] = JHTML::_('select.option','0','Select Market status');
       	$market_status 		= $configClass['market_status'];
		if($market_status != ""){
			$market_status_array = explode(",",$market_status);
			if(in_array('1',$market_status_array)){
				$marketArr[] = HTMLHelper::_('select.option',1,Text::_('OS_SOLD'));
			}
			if(in_array('2',$market_status_array)){
				$marketArr[] = HTMLHelper::_('select.option',2,Text::_('OS_CURRENT'));
			}
			if(in_array('3',$market_status_array)){
				$marketArr[] = HTMLHelper::_('select.option',3,Text::_('OS_RENTED'));
			}
		}
		return HTMLHelper::_('select.genericlist',$marketArr, $this->name, array(
			'option.text.toHtml' => false ,
			'option.value' => 'value', 
			'option.text' => 'text', 
			'list.attr' => ' class="input-large form-control form-select" ',
			'list.select' => $selectedValue    		        		
		));	
	}
}
