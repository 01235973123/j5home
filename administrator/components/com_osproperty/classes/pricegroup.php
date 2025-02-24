<?php

/*------------------------------------------------------------------------
# pricegroup.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class OspropertyPricegroup{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			case "pricegroup_list":
				OspropertyPricegroup::pricegroup_list($option);
			break;
			case "pricegroup_unpublish":
				OspropertyPricegroup::pricegroup_change_publish($option,$cid,0);	
			break;
			case "pricegroup_publish":
				OspropertyPricegroup::pricegroup_change_publish($option,$cid,1);
			break;
			case "pricegroup_remove":
				OspropertyPricegroup::pricegroup_remove($option,$cid);
			break;
			case "pricegroup_orderup":
				OspropertyPricegroup::pricegroup_change_order($option,$cid[0],-1);
			break;
			case "pricegroup_orderdown":
				OspropertyPricegroup::pricegroup_change_order($option,$cid[0],1);
			break;
			case "pricegroup_saveorder":
				OspropertyPricegroup::pricegroup_saveorder($option,$cid);
			break;
			case "pricegroup_add":
				OspropertyPricegroup::pricegroup_edit($option,0);
			break;
			case "pricegroup_edit":
				OspropertyPricegroup::pricegroup_edit($option,$cid[0]);
			break;
			case 'pricegroup_cancel':
				$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
			break;	
			case "pricegroup_save":
				OspropertyPricegroup::pricegroup_save($option,0);
			break;
			case "pricegroup_apply":
				OspropertyPricegroup::pricegroup_save($option,1);
			break;
			case "pricegroup_new":
				OspropertyPricegroup::pricegroup_save($option,2);
			break;
		}
	}
	
	/**
	 * Pricegroup list
	 *
	 * @param unknown_Pricegroup $option
	 */
	static function pricegroup_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$lists = array();
		$condition = '';
		
		$filter_order = $jinput->getString('filter_order','ordering');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
		$keyword = $jinput->getString('keyword','');
		if($keyword != ""){
			$condition .= " AND (a.price LIKE '%$keyword%')";
		}
		
		$count = "SELECT count(id) FROM #__osrs_pricegroups WHERE 1=1";
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = "SELECT a.*,b.type_name FROM #__osrs_pricegroups as a";
		$list .= " LEFT JOIN #__osrs_types as b on b.id = a.type_id";
		$list .= " WHERE 1=1 ";
		$list .= $condition;
		$list .= " ORDER BY b.type_name,$filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		HTML_OspropertyPricegroup::pricegroup_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or Unpublish
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function pricegroup_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("UPDATE #__osrs_pricegroups SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
	}
	
	/**
	 * remove price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function pricegroup_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__osrs_pricegroups WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
	}
	
	/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function pricegroup_change_order($option,$id,$direction){
		global $jinput, $mainframe;
		
		$row = Table::getInstance('Pricegroup','OspropertyTable');
		$row->load($id);
		$row->move( $direction, ' published >= 0 ' );
		$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function pricegroup_saveorder($option,$cid){
		global $jinput, $mainframe;
		$order 	= $jinput->get( 'order', array(), 'array' );
		$row = Table::getInstance('Pricegroup','OspropertyTable');
		
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			$groupings[] = $row->type_id;
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = Text::_( 'OS_ERROR_SAVING_ORDERING' );
					$mainframe->enqueueMessage($msg);
					$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
				}
			}
		}
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder(' type_id = '.(int) $group.' AND published = 1');
		}
		$msg = Text::_( 'OS_NEW_ORDERING_SAVED' );
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=pricegroup_list");
	}
	
	
	/**
	 * Pricegroup Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function pricegroup_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$lists = array();
		
		$row = Table::getInstance('Pricegroup','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		
		//$lists['state'] = HTMLHelper::_('select.booleanlist', 'published', 'class="input-mini"', $row->published);
		$lists['state']   = OSPHelper::getBooleanInput('published',$row->published);
		
		$db->setQuery("Select id as value, type_name as text from #__osrs_types where published = '1' order by type_name");
		$types = $db->loadOBjectList();
		$typeArr = array();
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_TYPES'));
		$typeArr   = array_merge($typeArr,$types);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'type_id','class="input-large form-select" style="width:250px;"','value','text',$row->type_id);
		// build the html select list for ordering
		$query = 'SELECT ordering AS value, CONCAT(price_from,"-",price_to) AS text'
			. ' FROM #__osrs_pricegroups'
			. ' ORDER BY ordering';
		$lists['ordering'] = HTMLHelper::_('list.ordering', 'ordering', $query ,'class="input-large form-select" style="width:250px;"',$row->ordering);
	
		HTML_OspropertyPricegroup::editHTML($option,$row,$lists);
	}
	
	/**
	 * save Pricegroup
	 *
	 * @param unknown_type $option
	 */
	static function pricegroup_save($option,$save)
	{
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$type_id = $jinput->getInt('type_id',0);
		$post = $jinput->post->getArray();
		$row = Table::getInstance('Pricegroup','OspropertyTable');
		$row->bind($post);
		if (!$row->id){
			if (empty($row->ordering)){
				$db->setQuery("SELECT MAX(ordering) FROM #__osrs_pricegroups where type_id = '$type_id'");
				$row->ordering = $db->loadResult() + 1;
			}
		}
		$row->check();
		$msg = Text::_('OS_ITEM_HAS_BEEN_SAVED'); 
	 	if (!$row->store())
		{
		 	throw new Exception($row->getError(), 500);	 			 	
		}
		$mainframe->enqueueMessage($msg);
		if($save == 0)
		{
			
			$mainframe->redirect("index.php?option=com_osproperty&task=pricegroup_list");
		}
		elseif($save == 2)
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=pricegroup_add");
		}
		else
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=pricegroup_edit&cid[]=$row->id");
		}
	}
}
?>
