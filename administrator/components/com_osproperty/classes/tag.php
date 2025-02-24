<?php

/*------------------------------------------------------------------------
# tag.php - Ossolution Property
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

class OspropertyTag{
	/**
	 * Default static function
	 *
	 * @param unknown_tag $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		switch ($task){
			case "tag_list":
				OspropertyTag::tag_list($option);
			break;
			case "tag_unpublish":
				OspropertyTag::tag_change_publish($option,$cid,0);	
			break;
			case "tag_publish":
				OspropertyTag::tag_change_publish($option,$cid,1);
			break;
			case "tag_remove":
				OspropertyTag::tag_remove($option,$cid);
			break;
			case "tag_add":
				OspropertyTag::tag_edit($option,0);
			break;
			case "tag_edit":
				OspropertyTag::tag_edit($option,$cid[0]);
			break;
			case 'tag_cancel':
				$mainframe->redirect("index.php?option=$option&task=tag_list");
			break;	
			case "tag_save":
				OspropertyTag::tag_save($option,1);
			break;	
			case "tag_apply":
				OspropertyTag::tag_save($option,0);
			break;
			case "tag_new":
				OspropertyTag::tag_save($option,2);
			break;
		}
	}
	
	/**
	 * Tag list
	 *
	 * @param unknown_tag $option
	 */
	static function tag_list($option){
		global $jinput, $mainframe,$configClass;
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
		$keyword = $jinput->getString('keyword','');
		$status = $jinput->getIntt('state',-1);
		$filter_order = $jinput->getString('filter_order','count_tag');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$db = Factory::getDbo();
		$query = "Select count(id) from #__osrs_tags where 1=1";
		if($status >= 0){
			$query .= " and published = '1'";
		}
		if($keyword != ""){
			$keyword = $db->escape($keyword);
			$query .= " and keyword like '%$keyword%'";
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		$query = "Select a.id,a.keyword,a.published,count(b.id) as count_tag from #__osrs_tags as a left join #__osrs_tag_xref as b on b.tag_id = a.id where 1=1";
		if($status >= 0){
			$query .= " and a.published = '1'";
		}
		if($keyword != ""){
			$keyword = $db->escape($keyword);
			$query .= " and a.keyword like '%$keyword%'";
		}
		$query .= " group by a.id";
		$query .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		
		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',-1,Text::_('OS_ALL_STATUS'));
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_PUBLISHED'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_UNPUBLISHED'));
		$lists['status'] = HTMLHelper::_('select.genericlist',$optionArr,'state','class="input-medium form-select" style="width:220px;" onChange="javascript:document.adminForm.submit()"','value','text',$status);
		HTML_OspropertyTag::listTags($option,$rows,$lists,$pageNav);
	}
	
	/**
	 * publish or unpublish tag
	 *
	 * @param unknown_tag $option
	 * @param unknown_tag $cid
	 * @param unknown_tag $state
	 */
	static function tag_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("Update #__osrs_tags SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=tag_list");
	}
	
	/**
	 * remove tag
	 *
	 * @param unknown_tag $option
	 * @param unknown_tag $cid
	 */
	static function tag_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("DELETE FROM #__osrs_tags WHERE id IN ($cids)");
			$db->execute();
			
			$db->setQuery("Delete from #__osrs_tag_xref where tag_id in ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_HAS_BEEN_DELETED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=$option&task=tag_list");
	}
	
	/**
	 * Type Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function tag_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$lists = array();
		
		$row = Table::getInstance('Tag','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		
		//$lists['state'] = HTMLHelper::_('select.booleanlist', 'published', '', $row->published);
		$lists['state']   = OSPHelper::getBooleanInput('published',$row->published);
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyTag::editHTML($option,$row,$lists,$translatable);
	}
	
	/**
	 * save tag
	 *
	 * @param unknown_type $option
	 */
	static function tag_save($option,$save)
	{
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$post = $jinput->post->getArray();
		$row = Table::getInstance('Tag','OspropertyTable');
		$row->bind($post);
		$row->check();
		if((int) $row->id == 0 && $row->keyword != "")
		{
			$db->setQuery('Select count(id) from #__osrs_tags where `keyword` like '.$db->quote($row->keyword));
			$count = $db->loadResult();
			if($count > 0)
			{
				$msg = Text::_('OS_TAG_IS_EXISTING');
				$mainframe->enqueueMessage($msg,'error');
				$mainframe->redirect("index.php?option=$option&task=tag_add");
			}
		}
		$msg = Text::_('OS_ITEM_SAVED'); 
	 	if (!$row->store()){
		 	$msg = Text::_('OS_ERROR_SAVING');	 			 	
		}
		$id = $jinput->getInt('id',0);
		if($id == 0){
			$id = $db->insertID();
		}
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$tag_language = $jinput->getString('keyword_'.$sef,'');
				if($tag_language == ""){
					$tag_language = $row->keyword;
				}
				if($tag_language != ""){
					$tag = Table::getInstance('Tag','OspropertyTable');
					$tag->id = $id;
					$tag->{'keyword_'.$sef} = $tag_language;
					$tag->store();
				}
			}
		}
		$mainframe->enqueueMessage($msg);
		if($save == 1)
		{
			$mainframe->redirect("index.php?option=$option&task=tag_list");
		}
		elseif($save == 2)
		{
			$mainframe->redirect("index.php?option=$option&task=tag_add");
		}
		else
		{
			$mainframe->redirect("index.php?option=$option&task=tag_edit&cid[]=".$id);
		}
	}
}
?>
