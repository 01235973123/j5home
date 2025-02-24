<?php

/*------------------------------------------------------------------------
# state.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class OspropertyState{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $mainframe,$languages,$jinput;
		$cid = $jinput->get( 'cid', array() ,'ARRAY');
		$translatable = Multilanguage::isEnabled() && count($languages);
		if((count($languages) > 0) and ($translatable)){
			$db = Factory::getDbo();
			foreach ($languages as $language){
				$sef = $language->sef;
				$db->setQuery("Update #__osrs_states set state_name_".$sef." = state_name where state_name_".$sef." Is NULL or state_name_".$sef." = ''");
				$db->execute();
			}
		}
		
		switch ($task){
			case "state_list":
				OspropertyState::state_list($option);
			break;
			case "state_remove":
				OspropertyState::state_remove($option,$cid);
			break;
			case "state_add":
				OspropertyState::state_edit($option,0);
			break;
			case "state_edit":
				OspropertyState::state_edit($option,$cid[0]);
			break;
			case 'state_cancel':
				$mainframe->redirect("index.php?option=$option&task=state_list");
			break;
			case "state_apply":
				OspropertyState::state_save($option,0);
			break;		
			case "state_save":
				OspropertyState::state_save($option,1);
			break;
			case "state_new":
				OspropertyState::state_save($option,2);
			break;
			case "state_publish":
				OspropertyState::changeState($option,$cid,1);
			break;
			case "state_unpublish":
				OspropertyState::changeState($option,$cid,0);
			break;
		}
	}
	
	/**
	 * Change state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $newstate
	 */
	static function changeState($option,$cid,$newstate){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$cids = implode(",",$cid);
		$db->setQuery("UPDATE #__osrs_states SET published = '$newstate' WHERE id IN ($cids)");
		$db->execute();
		$msg = Text::_('OS_STATE')." ".Text::_('OS_STATUS_CHANGED');
		//$mainframe->redirect("index.php?option=com_osproperty&task=state_list",$msg);
		OspropertyState::state_list($option);
	}
	/**
	 * state list
	 *
	 * @param unknown_type $option
	 */
	static function state_list($option){
		global $jinput, $mainframe,$configClass;
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
        $config = new JConfig();
        $list_limit = $config->list_limit;
        $limitstart						= $jinput->get('limitstart','');
        if($limitstart == ""){
            $limitstart					= $mainframe->getUserStateFromRequest('state_list.filter.limitstart','limit_start',0);
        }
        $mainframe->setUserState('state_list.filter.limitstart',$limitstart);
        $limit							= $jinput->getInt('limit',$list_limit);
        if($limit == 0){
            $limit     	  	 			= $mainframe->getUserStateFromRequest('state_list.filter.limit','limit',$list_limit);
        }
        $mainframe->setUserState('state_list.filter.limitstart',$limitstart);
        $mainframe->setUserState('state_list.filter.limit',$limit);
		// search
		$keyword = $mainframe->getUserStateFromRequest('state_list.filter.keyword','keyword','');
		if($keyword != ""){
			$condition .= " AND (`state_name` LIKE '%$keyword%')";
		}
		
		// filter country
		$defaultcountry = HelperOspropertyCommon::checkCountry();
		if(!$defaultcountry){
			$defaultcountry = HelperOspropertyCommon::getDefaultCountry();
		}else{
			$defaultcountry = "";
		}
		
		$filter_country = $jinput->getInt('filter_country',$defaultcountry);
		if ($filter_country){
			$condition .= " AND (`country_id` = '$filter_country')";
		} 
		$option_country = array();
		$option_country[] = HTMLHelper::_('select.option',0,' - '.Text::_("Select Country").' - ');
		$db->setQuery("SELECT id AS value, country_name AS text FROM #__osrs_countries ORDER BY country_name");
		$contries = $db->loadObjectList();
		if (count($contries)){
			$option_country = array_merge($option_country,$contries);
		}
		$lists['country'] = HTMLHelper::_('select.genericlist',$option_country,'filter_country','class="chosen input-large form-select" onchange="document.adminForm.submit();"','value','text',$filter_country);
		
		// get database
		$count = "SELECT count(id) FROM #__osrs_states WHERE 1=1";
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = " SELECT s.*, c.country_name FROM #__osrs_states AS s "
				."\n INNER JOIN #__osrs_countries AS c ON c.id = s.country_id "
				."\n WHERE 1=1 "
				;
		$list .= $condition;
		$list .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		HTML_OspropertyState::state_list($option,$rows,$pageNav,$lists,$modal,$keyword);
	}
	
	/**
	 * remove state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function state_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__osrs_states WHERE id IN ($cids)");
			$db->execute();
		}
		//$mainframe->redirect("index.php?option=$option&task=state_list");
		OspropertyState::state_list($option);
	}
	
	
	/**
	 * state Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function state_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$lists = array();
		$row = Table::getInstance('State','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published =  1;
		}
		//$lists['published'] = HTMLHelper::_('select.booleanlist', 'published', '', $row->published);
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);
		
		// creat drop country
		$db->setQuery("SELECT id AS value, country_name AS text FROM #__osrs_countries ORDER BY country_name");
		$contries = $db->loadObjectList();
		$option_country = array();
		$option_country[] = HTMLHelper::_('select.option',0,' - '.Text::_("OS_SELECT_COUNTRY").' - ');
		if (count($contries)){
			$option_country = array_merge($option_country,$contries);
		}
		$lists['country_id'] = HTMLHelper::_('select.genericlist',$option_country,'country_id','class="input-medium form-select"','value','text',$row->country_id);
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyState::editHTML($option,$row,$lists,$translatable);
	}
	
	/**
	 * save state
	 *
	 * @param unknown_type $option
	 */
	static function state_save($option,$save){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$post = $jinput->post->getArray();
		$post['state_code'] = strtoupper($post['state_code']);
		
		$row = Table::getInstance('State','OspropertyTable');
		$row->bind($post);		 
		$row->check();
		$msg = Text::_('OS_ITEM_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING'); ;		 			 	
		}
		$mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=$option&task=state_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=$option&task=state_add");
		}else{
			$mainframe->redirect("index.php?option=$option&task=state_edit&cid[]=".$row->id);
		}
	}
}
?>
