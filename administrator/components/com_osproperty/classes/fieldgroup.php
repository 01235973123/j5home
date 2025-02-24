<?php

/*------------------------------------------------------------------------
# fieldgroup.php - Ossolution Property
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class OspropertyFieldgroup{
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get('cid',array(),'ARRAY');
		switch ($task){
			case "fieldgroup_list":
				OspropertyFieldgroup::fieldgroup_list($option);
			break;
			case "fieldgroup_add":
				OspropertyFieldgroup::fieldgroup_edit($option,0);
			break;
			case "fieldgroup_edit":
				OspropertyFieldgroup::fieldgroup_edit($option,$cid[0]);
			break;
			case "fieldgroup_save":
				OspropertyFieldgroup::save($option,1);
			break;
			case "fieldgroup_new":
				OspropertyFieldgroup::save($option,2);
			break;
			case "fieldgroup_apply":
				OspropertyFieldgroup::save($option,0);
			break;
			case "fieldgroup_remove":
				OspropertyFieldgroup::removeList($option,$cid);
			break;
			case "fieldgroup_publish":
				OspropertyFieldgroup::changState($option,$cid,1);
			break;
			case "fieldgroup_unpublish":
				OspropertyFieldgroup::changState($option,$cid,0);
			break;
			case "fieldgroup_saveorder":
				OspropertyFieldgroup::saveorder($option);
			break;
			case "fieldgroup_saveorderAjax":
				OspropertyFieldgroup::saveorderAjax($option);
			break;
			case "fieldgroup_orderup":
				OspropertyFieldgroup::direction($option,$cid[0],-1);
			break;
			case "fieldgroup_orderdown":
				OspropertyFieldgroup::direction($option,$cid[0],1);
			break;
			case "fieldgroup_gotolist":
				OspropertyFieldgroup::gotolist($option);
			break;			
		}
	}
	
	/**
	 * Field Groups listing
	 *
	 * @param unknown_type $option
	 */
	static function fieldgroup_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
        //Update access level to Public for all existing field groups
        $db->setQuery("Update #__osrs_fieldgroups set `access` = '1' where `access` = '0'");
        $db->execute();

		$lists							= array();
		
		$limit							= $jinput->getInt('limit',20);
		$limitstart						= $jinput->getInt('limitstart',0);
		$keyword						= $jinput->getString('keyword','');
		$filter_order					= $jinput->getString('filter_order','ordering');
		$filter_order_Dir				= $jinput->getString('filter_order_Dir','');
		$filter_full_ordering			= $jinput->getString('filter_full_ordering','ordering asc');
		$filter_Arr						= explode(" ",$filter_full_ordering);
		$filter_order					= $filter_Arr[0];
		$filter_order_Dir				= $filter_Arr[1];
		if($filter_order == ""){
			$filter_order				= 'ordering';
		}
		$lists['filter_order']			= $filter_order;
		$lists['filter_order_Dir']		= $filter_order_Dir;
	
		
		$query = "Select count(id) from #__osrs_fieldgroups where 1=1";
		if($keyword != ""){
			$query .= " and group_name like '%$keyword%'";
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		
		jimport('joomla.html.pagination');
		
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$query = "Select * from #__osrs_fieldgroups where 1=1";
		if($keyword != ""){
			$query .= " and group_name like '%$keyword%'";
		}
		$query .= " order by $filter_order $filter_order_Dir";
		
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		
		$rows = $db->loadObjectList();
		
		HTML_OspropertyFieldgroup::listfieldgroup($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Edit extra field groups
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function fieldgroup_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldgroup','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
            $row->access = 0;
		}
		//$lists['state'] = HTMLHelper::_('select.booleanlist', 'published', '', $row->published);
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		$lists['state']   = HTMLHelper::_('select.genericlist',$optionArr,'published','class="input-mini form-select"','value','text',$row->published);

        $lists['access'] = OSPHelper::accessDropdown('access',$row->access);

		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyFieldgroup::editGroup($option,$row,$lists,$translatable);
	}
	
	/**
	 * Save fieldgroup
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function save($option,$save){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldgroup','OspropertyTable');
		$post = $jinput->post->getArray();
		$row->bind($post);
		$id = $jinput->getInt('id',0);
		if($id == 0){
			//get the ordering
			$db->setQuery("Select ordering from #__osrs_fieldgroups order by ordering desc limit 1");
			$ordering = $db->loadResult();
			$row->ordering = $ordering + 1;
		}
		$row->store();
		if($id == 0){
			$id = $db->insertID();
		}
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$group_name_language = $jinput->getString('group_name_'.$sef,'');
				if($group_name_language == ""){
					$group_name_language = $row->group_name;
					$group = Table::getInstance('Fieldgroup','OspropertyTable');
					$group->id = $id;
					$group->{'group_name_'.$sef} = $group_name_language;
					$group->store();
				}
			}
		}
		$msg = Text::_('OS_ITEM_SAVED');
		$mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_add");
		}else{
			$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_edit&cid[]=$id");
		}
   }
		
	/**
	 * Remove field groups
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function removeList($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if($cid){
			$cids = implode(",",$cid);
			$db->setQuery("Delete from #__osrs_fieldgroups where id in ($cids)");
			$db->execute();
			//remove fields
			$db->setQuery("Delete from #__osrs_extra_fields where group_id in ($cids)");
			$db->execute();
		}
		$msg = Text::_('OS_ITEM_HAS_BEEN_DELETED');
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
	}
	
	/**
	 * Change status of the field group(s)
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function changState($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if($cid){
			$cids = implode(",",$cid);
			$db->setQuery("Update #__osrs_fieldgroups set published = '$state' where id in ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
	}

	static function saveorderAjax($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$cid 	= $jinput->get( 'cid', array(), 'array' );
		$order 	= $jinput->get( 'order', array(). 'array' );
		
		$row = Table::getInstance('Fieldgroup','OspropertyTable');
		$groupings	= array();
		// update ordering values
		for( $i=0; $i < count($cid); $i++ ) {
			$row->load( $cid[$i] );
			// track parents
			$groupings[] = $row->ordering;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			} // if
		} // for

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder(' published = 1');
		}		
	}
	
	/**
	 * Save order
	 *
	 * @param unknown_type $option
	 */
	static function saveorder($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$msg = Text::_( 'OS_NEW_ORDERING_SAVED' );
		$cid 	= $jinput->getString( 'cid', array(),'array' );
		$order 	= $jinput->getString( 'order', array(),  'array' );
		
		$row = Table::getInstance('Fieldgroup','OspropertyTable');
		// update ordering values
		$groupings	= array();
		// update ordering values
		for( $i=0; $i < count($cid); $i++ ) {
			$row->load( $cid[$i] );
			// track parents
			$groupings[] = $row->ordering;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}
			} // if
		} // for

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder(' published = 1');
		}
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
	}
	
	/**
	 * Save order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 * @param unknown_type $direction
	 */
	static function direction($option,$id,$direction){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$row = Table::getInstance('Fieldgroup','OspropertyTable');
		
		if (!$row->load($id)) {
			$msg = $db->getErrorMsg();
		}
		if (!$row->move( $direction)) {
			$msg = $db->getErrorMsg();
		}
		
		$msg = Text::_("OS_NEW_ORDERING_SAVED");
        $mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
	}
	
	
	/**
	 * Go to list
	 *
	 * @param unknown_type $option
	 */
	static function gotolist($option){
		global $jinput, $mainframe;
		$mainframe->redirect("index.php?option=com_osproperty&task=fieldgroup_list");
	}
}
?>
