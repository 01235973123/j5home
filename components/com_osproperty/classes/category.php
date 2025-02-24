<?php
/*------------------------------------------------------------------------
# category.php - Ossolution Property
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
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Pagination\Pagination;


class OspropertyCategories{
	static function display($option,$task){
		global $mainframe,$jinput,$configClass;
		$cid = $jinput->getInt('cid',0);
        $cid = ArrayHelper::toInteger($cid);
		$id = $jinput->getInt('id',0);
		$show_top_menus_in = $configClass['show_top_menus_in'];
		$show_top_menus_in = explode("|",$show_top_menus_in);
		if(in_array('category',$show_top_menus_in)){
			echo HelperOspropertyCommon::buildToolbar('category');
		}
		switch ($task){
			case "category_listing":
				OspropertyCategories::listCategories($option);
			break;
			case "category_details":
				OspropertyCategories::categoryDetails($option,$id);
			break;
		}
		HelperOspropertyCommon::loadFooter($option);
	}
	
	/**
	 * List categories
	 *
	 * @param unknown_type $option
	 */
	static function listCategories($option){
		global $mainframe,$jinput,$configClass;
		$document		= Factory::getDocument();
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_LIST_CATEGORIES'));
		$db				= Factory::getDbo();
		$user			= Factory::getUser();
		//access
		$access			= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		$limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
		$limitstart		= OSPHelper::getLimitStart();
		$query			= "select count(id) from #__osrs_categories where published = '1' $access and parent_id = '0'";
		$db->setQuery($query);
		$total			= $db->loadResult();
		if($configClass['overrides_pagination'] == 1){
			$pageNav	= new OSPJPagination($total,$limitstart,$limit);
		}else{
			$pageNav	= new Pagination($total,$limitstart,$limit);
		}
		$query			= "select * from #__osrs_categories where published = '1' and parent_id = '0' $access order by ordering";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows			= $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row	= $rows[$i];
				$total	= 0;
				$total	= OspropertyCategories::countProperties($row->id,$total);
				$row->nlisting = $total;
			}
		}
		HTML_OspropertyCategories::listCategories($option,$rows,$pageNav);
	}
	
	/**
	 * Count properties of the category
	 *
	 */
	static function countProperties($id,&$total){
		global $mainframe,$jinput;
		$db = Factory::getDbo();
		$db->setQuery("Select count(a.id) from #__osrs_properties as a left join #__osrs_property_categories as b on b.pid = a.id where a.approved = '1' and a.published = '1' and b.category_id = '$id'");
		$count = $db->loadResult();
		$total += $count;
		//echo $total;
		$db->setQuery("Select * from #__osrs_categories where parent_id = '$id'");
		$categories = $db->loadObjectList();
		for($i=0;$i<count($categories);$i++){
			$cat = $categories[$i];
			$total = OspropertyCategories::countProperties($cat->id,$total);
		}
		return $total;
	}
	
	
	/**
	 * Category details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function categoryDetails($option,$id){
		global $mainframe,$jinput,$configClass;
		$db = Factory::getDBO();
		
		$user = Factory::getUser();
		//access
		$access = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$db->setQuery("Select count(id) from #__osrs_categories where id = '$id' and published = '1' $access");
		$count = $db->loadResult();
		if($count == 0){
			OSPHelper::redirect("index.php",Text::_('OS_YOU_DO_NOT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA'));
		}
		$db->setQuery("Select * from #__osrs_categories where id = '$id' and published = '1' $access");
		$cat = $db->loadObject();
		
		//pathway
		$pathway = $mainframe->getPathway();
		if($cat->parent_id > 0){
			$db->setQuery("Select category_name from #__osrs_categories where id = '$cat->parent_id'");
			$parent_category_name = $db->loadResult();
			$pathway->addItem($parent_category_name,Route::_('index.php?option=com_osproperty&task=category_details&id='.$cat->parent_id.'&Itemid='.$jinput->getInt('Itemid',0)));
		}
		$pathway->addItem($cat->category_name,Route::_('index.php?option=com_osproperty&task=category_details&id='.$cat->id.'&Itemid='.$jinput->getInt('Itemid',0)));
		
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_CATEGORY')." - ".OSPHelper::getLanguageFieldValue($cat,'category_name'));

		if($cat->category_meta != ""){
			$document->setMetaData( "description", $cat->category_meta ); 
		}
		
		//get the subcates
		$query = "select * from #__osrs_categories where published = '1' and parent_id = '$id' $access order by ordering";
		$db->setQuery($query);
		$subcats = $db->loadObjectList();
		if(count($subcats) > 0){
			for($i=0;$i<count($subcats);$i++){
				$row = $subcats[$i];
				$total = 0;
				//$db->setQuery("Select count(id) from #__osrs_properties where approved = '1' and published = '1' and id in (select pid from #__osrs_property_categories where category_id = '$row->id')");
				///$row->nlisting = $db->loadResult();
				$total	= OspropertyCategories::countProperties($row->id,$total);
				$row->nlisting = $total;
			}
		}
		
		HTML_OspropertyCategories::categoryDetailsForm($option,$cat,$subcats);
	}
	/**
	 * Show category details
	 * And 
	 * Show properties of the category
	 *
	 * @param unknown_type $option
	 */
	static function listProperties($option){
		global $mainframe,$jinput,$_jversion;
		$db = Factory::getDBO();
		$id = $jinput->getInt('id',0);
		if($id == 0){
			OSPHelper::redirect("index.php",Text::_(OS_YOU_DO_NOT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA));
		}
		$user = Factory::getUser();
		$db->setQuery("Select * from #__osrs_categories where id = '$id'");
		$category = $db->loadObject();
		$access = $category->access;
		if(!HelperOspropertyCommon::checkAccessPersmission($access)){
			OSPHelper::redirect("index.php",Text::_(OS_YOU_DO_NOT_HAVE_PERMISSION_TO_GO_TO_THIS_AREA));
		}
		
		OspropertyListing::listProperties($option,0,$id,0,0,'',0,0,0,0,0,'a.isFeatured desc,a.id desc',0,20);
	}
}
?>
