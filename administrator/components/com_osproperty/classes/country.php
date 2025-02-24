<?php

/*------------------------------------------------------------------------
# country.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OspropertyCountry{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$languages;
		$cid = Factory::getApplication()->input->get( 'cid', array(0),'array');
		$cid = ArrayHelper::toInteger($cid, array(0));
		$translatable = Multilanguage::isEnabled() && count($languages);
		if((count($languages) > 0) and ($translatable)){
			$db = Factory::getDbo();
			foreach ($languages as $language){
				$sef = $language->sef;
				$db->setQuery("Update #__osrs_countries set country_name_".$sef." = country_name where country_name_".$sef." Is NULL or country_name_".$sef." = ''");
				$db->execute();
			}
		}
		
		switch ($task){
			case "country_list":
				OspropertyCountry::country_list($option);
			break;
			case "country_edit":
				OspropertyCountry::country_edit($option,$cid[0]);
			break;
			case 'country_cancel':
				$mainframe->redirect("index.php?option=$option&task=country_list");
			break;
			case "country_apply":
				OspropertyCountry::country_save($option,0);
			break;		
			case "country_save":
				OspropertyCountry::country_save($option,1);
			break;
			case "country_new":
				OspropertyCountry::country_save($option,2);
			break;
		}
	}
	
	/**
	 * state list
	 *
	 * @param unknown_type $option
	 */
	static function country_list($option){
		global $mainframe,$configClass,$jinput;
		$db = Factory::getDBO();
		$lists = array();
		$condition = '';
		$modal = $jinput->getInt('modal',0);
		// filter sort
		$filter_order = $jinput->getString('filter_order','s.id');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
			
		// limit page
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
		
		// search
		$keyword = $mainframe->getUserStateFromRequest('country_list.filter.keyword','keyword','');
		if($keyword != ""){
			$condition .= " AND s.country_name LIKE '%".$db->escape($keyword)."%'";
		}
		
		// get database
		$count = "SELECT count(s.id) FROM #__osrs_countries as s WHERE 1=1";
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = " SELECT s.* FROM #__osrs_countries AS s "
				."\n WHERE 1=1 "
				;
		$list .= $condition;
		$list .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		HTML_OspropertyCountry::country_list($option,$rows,$pageNav,$lists,$modal,$keyword);
	}
	
	
	/**
	 * state Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function country_edit($option,$id){
		global $mainframe,$languages;
		$db = Factory::getDBO();
		$lists = array();
		$row = Table::getInstance('Country','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyCountry::editHTML($option,$row,$lists,$translatable);
	}
	
	/**
	 * save state
	 *
	 * @param unknown_type $option
	 */
	static function country_save($option,$save){
		global $mainframe,$jinput;
		$db = Factory::getDBO();
		$post = $jinput->post->getArray();
		
		$row = Table::getInstance('Country','OspropertyTable');
		$row->bind($post);		 
		$row->check();
		$msg = Text::_('OS_ITEM_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING'); ;		 			 	
		 }
		 $mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=$option&task=country_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=$option&task=country_add");
		}else{
			$mainframe->redirect("index.php?option=$option&task=country_edit&cid[]=".$row->id);
		}
	}
}
?>
