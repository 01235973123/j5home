<?php
/*------------------------------------------------------------------------
# report.php - Ossolution Property
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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class OspropertyReport{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array(), 'ARRAY');
		switch ($task){
			case "report_listing":
				OspropertyReport::report_list($option);
			break;
			case "report_remove":
				OspropertyReport::removeReport($option,$cid);
			break;
		}
	}
	
	/**
	 * List reports
	 *
	 * @param unknown_type $option
	 */
	static function report_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$item_type = $jinput->getInt('item_type',-1);
		$limitstart = $jinput->getInt('limitstart',0);
		$limit = $jinput->getInt('limit',20);
		$filter_order 	 	= $jinput->getString('filter_order','report_on');
		$filter_order_Dir 	= $jinput->getString('filter_order_Dir','desc');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		
		$query = "Select count(id) from #__osrs_report where 1=1";
		if($item_type >= 0){
			$query .= " and item_type = '$item_type'";
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		$query = "Select * from #__osrs_report where 1=1";
		if($item_type >= 0){
			$query .= " and item_type = '$item_type'";
		}
		$query .= " order by $filter_order $filter_order_Dir";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$db->setQuery("Update #__osrs_report set is_checked = '1' where id = '$row->id'");
				$db->execute();
			}
		}
		
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',-1,Text::_('OS_ANY'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_PROPERTY'));
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_AGENT').'/ '.Text::_('OS_OWNER'));
		$optionArr[] = HTMLHelper::_('select.option',2,Text::_('OS_COMPANY'));
		$lists['item_type'] = HTMLHelper::_('select.genericlist',$optionArr,'item_type','class="input-medium" onChange="javascript:adminForm.submit()"','value','text',$item_type);
		
		HTML_OspropertyReport::listReports($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Remove Report
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function removeReport($option,$cid){
		global $jinput, $mainframe;
		$db= Factory::getDbo();
		if(count($cid) > 0){
			$db->setQuery("Delete from #__osrs_report where id in (".implode(",",$cid).")");
			$db->execute();
		}
		$mainframe->enqueueMessage(Text::_('OS_ITEM_HAVE_BEEN_REMOVED'));
		$mainframe->redirect("index.php?option=com_osproperty&task=report_listing");
	}
}
?>
