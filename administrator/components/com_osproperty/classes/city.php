<?php

/*------------------------------------------------------------------------
# city.php - Ossolution Property
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
class OspropertyCity{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe,$languages;
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		if((count($languages) > 0) and ($translatable)){
			$db = Factory::getDbo();
			foreach ($languages as $language){
				$sef = $language->sef;
				$db->setQuery("Update #__osrs_cities set city_".$sef." = city where city_".$sef." Is NULL or city_".$sef." = ''");
				$db->execute();
			}
		}
		
		switch ($task){
			case "city_list":
				OspropertyCity::city_list($option);
			break;
			case "city_add":
				OspropertyCity::city_edit($option,0);
			break;
			case "city_edit":
				OspropertyCity::city_edit($option,$cid[0]);
			break;
			case "city_save":
				OspropertyCity::saveCity($option,1);
			break;
			case "city_new":
				OspropertyCity::saveCity($option,2);
			break;
			case "city_apply":
				OspropertyCity::saveCity($option,0);
			break;
			case "city_unpublish":
				OspropertyCity::city_change_publish($option,$cid,0);	
			break;
			case "city_publish":
				OspropertyCity::city_change_publish($option,$cid,1);
			break;
			case "city_remove":
				OspropertyCity::city_remove($option,$cid);
			break;
			case "city_cancel":
				OspropertyCity::cancelEditCity($option);
			break;
			case "city_us":
				OspropertyCity::importUsData($option);
			break;
		}
	}
	
	/**
	 * Import United State data
	 *
	 * @param unknown_type $option
	 */
	static function importUsData($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$db->setQuery("Select id,state_code from #__osrs_states where country_id = '194'");
		$rows = $db->loadObjectList();
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$state_id = $row->id;
			$state_code = $row->state_code;
			$db->setQuery("Select city from cities where state_code like '$state_code'");
			$cities = $db->loadObjectList();
			for($j=0;$j<count($cities);$j++){
				$city = $cities[$j]->city;
				$db->setQuery("Insert into #__osrs_cities (id,city,state_id,country_id,published) values (NULL,'$city','$state_id','194',1)");
				$db->execute();
			}
		}
	}
	
	/**
	 * Cancel edit city
	 *
	 * @param unknown_type $option
	 */
	static function cancelEditCity($option){
		global $jinput, $mainframe;
		$mainframe->redirect("index.php?option=com_osproperty&task=city_list");
	}
	
	/**
	 * publish or unpublish city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function city_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("Update #__osrs_cities SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=city_list");
		//OspropertyCity::city_list($option);
	}
	
	/**
	 * remove city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function city_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__osrs_cities WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_HAS_BEEN_DELETED");
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=city_list");
	}
	
	/**
	 * Save City
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function saveCity($option,$save){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$id = $jinput->getInt('id',0);
		$country_id = $jinput->getInt('country_id',$configClass['show_country_id']);
		$nstate = $jinput->get('nstate','');
		if($nstate != ""){
			//insert into state table
			$db->setQuery("Insert into #__osrs_states (id,country_id,state_name,state_code) values (NULL,'$country_id','$nstate','$nstate')");
			$db->execute();
			$state = $db->insertID();
			$jinput->set('state',$state);
		}
		$post = $jinput->post->getArray();
		$row = Table::getInstance('City','OspropertyTable');
		$row->bind($post);
		$row->country_id = $country_id;
		$row->state_id = $jinput->getInt('state',0);
		$msg = Text::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING'); ;		 			 	
		}
		//update into #__osrs_company_agents
		if($id == 0){
			$id = $db->insertID();
		}
        $mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=$option&task=city_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=$option&task=city_add");
		}else{
			$mainframe->redirect("index.php?option=$option&task=city_edit&cid[]=$id");
		}
	}
	
	/**
	 * City list
	 *
	 * @param unknown_type $option
	 */
	static function city_list($option){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$modal = $jinput->getInt('modal',0);
		$filter_order = $jinput->getString('filter_order','a.city');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		
		//$keyword = JRequest::getVar('keyword','');
		$keyword = $mainframe->getUserStateFromRequest('city_list.filter.keyword','keyword','');
        $config = new JConfig();
        $list_limit = $config->list_limit;
        $limitstart						= $jinput->get('limitstart','');
        if($limitstart == ""){
            $limitstart					= $mainframe->getUserStateFromRequest('city_list.filter.limitstart','limit_start',0);
        }
        $mainframe->setUserState('city_list.filter.limitstart',$limitstart);
        $limit							= $jinput->getInt('limit',$list_limit);
        if($limit == 0){
            $limit     	  	 			= $mainframe->getUserStateFromRequest('city_list.filter.limit','limit',$list_limit);
        }
		$mainframe->setUserState('city_list.filter.limitstart',$limitstart);
        $mainframe->setUserState('city_list.filter.limit',$limit);
		//$keyword = JRequest::getVar('keyword','');
		$country_id = $jinput->getInt('country_id',intval($configClass['show_country_id']));
		//$state_id = JRequest::getVar('state_id',0);
		$state_id = $mainframe->getUserStateFromRequest('city_list.filter.state_id','state_id',0);
		
		$query = "Select count(id) from #__osrs_cities where 1=1";
		if($keyword != ""){
			$query .= " and city like '%$keyword%'";
		}
		if($country_id > 0){
			$query .= " and country_id = '$country_id'";
		}
		if($state_id > 0){
			$query .= " and state_id = '$state_id'";
		}
		
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		$query = "Select a.*,b.country_name, c.state_name from #__osrs_cities as a"
				." inner join #__osrs_countries as b on b.id = a.country_id"
				." left join #__osrs_states as c on c.id = a.state_id"
				." where 1=1";
		if($keyword != ""){
			$query .= " and a.city like '%$keyword%'";
		}
		if($country_id > 0){
			$query .= " and a.country_id = '$country_id'";
		}
		if($state_id > 0){
			$query .= " and a.state_id = '$state_id'";
		}
		$query .= " Order by $filter_order $filter_order_Dir";

		$db->setQuery($query,$pageNav->limitstart,$limit);
		$rows = $db->loadObjectList();
		
		
		//country
		$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
		$db->setQuery("Select id as value, country_name as text from #__osrs_countries order by country_name");
		$countries = $db->loadObjectList();
		$countryArr = array_merge($countryArr,$countries);
		$lists['country'] = HTMLHelper::_('select.genericlist',$countryArr,'country_id','class="input-large form-select" onChange="javascript:document.adminForm.submit();"','value','text',$country_id);
		
		//state
		$stateArr = array();
		$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
		$query  = "Select id as value,state_name as text from #__osrs_states where 1=1 ";
		if($country_id != ""){
			$query .= " and country_id = '$country_id'";
			$disabled = "";
		}else{
			$disabled = "disabled";
		}
		$query .= " order by state_name";
		$db->setQuery($query);
		$states = $db->loadObjectList();
		$stateArr   = array_merge($stateArr,$states);
		$lists['states'] = HTMLHelper::_('select.genericlist',$stateArr,'state_id','class="input-large form-select" onChange="javascript:document.adminForm.submit();" '.$disabled,'value','text',$state_id);
		
		HTML_OspropertyCity::city_list($option,$rows,$pageNav,$lists,$modal,$keyword);
	}
	
	
	/**
	 * Edit city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function city_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$lists = array();
		
		$row = Table::getInstance('City','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);

		
		if(intval($configClass['show_country_id']) == 0){
			// build the html select list for country
			$option_country = array();
			$option_country[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_COUNTRY').' - ');
			$db->setQuery("SELECT id AS value, country_name AS text FROM #__osrs_countries");
			$countries = $db->loadObjectList();
			if (count($countries)){
				$option_country = array_merge($option_country,$countries);
			}
			$lists['country'] = HTMLHelper::_('select.genericlist',$option_country,'country_id','class="input-medium chosen" onchange="change_country_agent(this.value,\''.$row->id.'\');"','value','text',$row->country_id);
			unset($option_country);unset($countries);
				
			// build the html select list for state
			$option_state = array();
			$option_state[] = HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_STATE').' - ');
			if($row->country_id > 0){
				$db->setQuery("SELECT id AS value, state_name AS text FROM #__osrs_states WHERE `country_id` = '$row->country_id'");
				$states = $db->loadObjectList();
				if (count($states)){
					$option_state = array_merge($option_state,$states);
				}
			}
			if ($row->id) $disable = '';
			else $disable = 'disabled="disabled"';
			$lists['state'] = HTMLHelper::_('select.genericlist',$option_state,'state','class="input-medium chosen" '.$disable,'value','text',$row->state_id);
			unset($option_state);unset($states);
		}else{
			$lists['country'] = "<input type='hidden' name='country_id' id='country_id' value='".$configClass['show_country_id']."'>"; 
			$db->setQuery("SELECT id AS value, state_name AS text FROM #__osrs_states WHERE `country_id` = '".$configClass['show_country_id']."' ORDER BY state_name");		
			$states = $db->loadObjectList();
			$option_state = array();
			$option_state[]= HTMLHelper::_('select.option',0,' - '.Text::_('OS_SELECT_STATE').' - ');
			if (count($states)){
				$option_state = array_merge($option_state,$states);
			}
			
			$lists['state'] = HTMLHelper::_('select.genericlist',$option_state,'state','class="input-small"','value','text',$row->state_id);
		}
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyCity::editHTML($option,$row,$lists,$translatable);
	}
}
?>
