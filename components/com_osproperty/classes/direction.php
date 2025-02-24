<?php
/*------------------------------------------------------------------------
# direction.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OspropertyDirection{
	/**
	 * Payment process
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $mainframe,$configClass,$jinput;
		$id = $jinput->getInt('id',0);
		$show_top_menus_in = $configClass['show_top_menus_in'];
		$show_top_menus_in = explode("|",$show_top_menus_in);
		if(in_array('direction',$show_top_menus_in)){
			echo HelperOspropertyCommon::buildToolbar();
		}
		switch ($task){
			case "direction_map":
				OspropertyDirection::getDirections($option,$id);
			break;
			case "direction_getmap":
				OspropertyDirection::doGetDirections($option,$id);
			break;
			default:
				OspropertyDirection::showTestMap($option);
			break;
		}
	}
	
	/**
	 * Get direction
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function getDirections($option,$id){
		global $mainframe,$configClass,$jinput;
		OSPHelper::loadGoogleJS();
		$document = Factory::getDocument();
		$db = Factory::getDbo();
		$routeStyle = $jinput->getString('routeStyle','');
		if($routeStyle != ""){
			$mode = "mode=".$routeStyle;
		}else{
			$mode = "";
		}
		$address = OSPHelper::getStringRequest('address','','post');
		
		$routeStyle = $jinput->getString('routeStyle');
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$document->setTitle(Text::_('OS_GET_DIRECTIONS').' ['.$property->pro_name.']');
		$document->setMetaData( 'robots', 'noindex,nofollow' );
		$pro_address = $property->address;
		$pro_address .= ", ".$property->city;
		$pro_address .= ", ".$property->zipcode;
		if($property->state > 0){
			$db->setQuery("Select state_name from #__osrs_states where id = '$property->state'");
			$state = $db->loadResult();
			$pro_address .= ", ".$state;
		}
		
		if($property->country > 0){
			$db->setQuery("Select country_name from #__osrs_countries where id = '$property->country'");
			$country = $db->loadResult();
			$pro_address .= ", ".$country;
		}
		$pro_address = str_replace("'","",$pro_address);
		$optionArr[] = HTMLHelper::_('select.option','',Text::_('OS_BY_CAR'));
		$optionArr[] = HTMLHelper::_('select.option','walking',Text::_('OS_BY_FOOT'));
		$lists['routeStyle'] = HTMLHelper::_('select.genericlist',$optionArr,'routeStyle','class="input-large form-select" style="width:180px;"','value','text',$routeStyle);
		HTML_OspropertyDirection::getDirectionForm($option,$property,$lists,$address,$pro_address);
	}
	
	/**
	 * Show Test map
	 *
	 * @param unknown_type $option
	 */
	static function showTestMap($option){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$param = new stdClass;
		$param->api_key = $configClass['goole_aip_key'];
		$param->width =  400;
		$param->height =  480;
		$param->zoom =  15;
		$param->dir_width = 275;
		$param->header_map = 'asdasda';
		$param->header_dir = 'dasdada';
		$param->map_on_right = 1;


		$row->text = '{googleDir width=400 height=360 dir_width=275 from="Hanoi" to="Haiphong"}' ;
		$plugin = new Plugin_googleDirections($row, $param, $is_mod);
		echo $row->text;
		
	}
}

?>