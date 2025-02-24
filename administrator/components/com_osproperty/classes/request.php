<?php
/*------------------------------------------------------------------------
# type.php - Ossolution Property
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

class OspropertyRequest{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array() ,'ARRAY');
		switch ($task){
			case "request_logs":
				OspropertyRequest::request_list($option);
			break;
			case "request_remove":
				OspropertyRequest::request_remove($option,$cid);
			break;
		}
	}
	
	/**
	 * Type list
	 *
	 * @param unknown_type $option
	 */
	static function request_list($option){
		global $jinput, $mainframe;
		$db								= Factory::getDBO();
		$lists							= array();
        $filter_order					= $jinput->getString('filter_order','a.requested_on');
        $filter_order_Dir				= $jinput->getString('filter_order_Dir','desc');
        $filter_full_ordering			= $jinput->getString('filter_full_ordering','a.requested_on desc');
		$filter_Arr						= explode(" ",$filter_full_ordering);
		$filter_order					= $filter_Arr[0];
		$filter_order_Dir				= $filter_Arr[1];

		if($filter_order == ""){
			$filter_order				= 'a.requested_on';
		}
		$lists['filter_order']			= $filter_order;
		$lists['filter_order_Dir']		= $filter_order_Dir;
        $limit							= $jinput->getInt('limit',20);
        $limitstart						= $jinput->getInt('limitstart',0);
        $keyword						= $jinput->getString('keyword','');
		$condition						= '';
		
		$count = "SELECT count(id) FROM #__osrs_request_logs WHERE 1=1";
		if($keyword != ""){
			$condition .= " AND (type_name LIKE '%$keyword%' OR type_description LIKE '%$keyword%')";
		}
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = "SELECT a.*, b.pro_name FROM #__osrs_request_logs as a inner join #__osrs_properties as b on a.pid = b.id WHERE 1=1 ";
		$list .= $condition;
		$list .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		HTML_OspropertyRequest::requests_list($option,$rows,$pageNav,$lists);
	}
	
	
	/**
	 * remove request
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function request_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__osrs_request_logs WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_HAS_BEEN_DELETED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=request_logs");
	}
}
?>
