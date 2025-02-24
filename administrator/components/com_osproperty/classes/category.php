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
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class OspropertyCategories{
	/**
	 * Display
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $jinput, $mainframe,$languages;
		$languages = OSPHelper::getLanguages();
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
		$cid = $jinput->get('cid',array(),'ARRAY');
		switch ($task){
			case "categories_list":
				OspropertyCategories::categories_list($option);
			break;
			case "categories_add":
				OspropertyCategories::categories_edit($option,0);
			break;
			case "categories_edit":
				OspropertyCategories::categories_edit($option,$cid[0]);
			break;
			case "categories_save":
				OspropertyCategories::save($option,1);
			break;
			case "categories_apply":
				OspropertyCategories::save($option,0);
			break;
			case "categories_new":
				OspropertyCategories::save($option,2);
			break;
			case "categories_gotolist":
				OspropertyCategories::gotolist($option);
			break;
			case "categories_remove":
				OspropertyCategories::removeList($option,$cid);
			break;
			case "categories_publish":
				OspropertyCategories::changState($option,$cid,1);
			break;
			case "categories_unpublish":
				OspropertyCategories::changState($option,$cid,0);
			break;
			case "categories_saveorder":
				OspropertyCategories::saveorder($option);
			break;
			case "categories_saveorderAjax":
				OspropertyCategories::saveorderAjax($option);
			break;
			case "categories_orderup":
				OspropertyCategories::orderup($option);
			break;
			case "categories_orderdown":
				OspropertyCategories::orderdown($option);
			break;
		}		
	}
	
	/**
	 * Count properties of the category
	 *
	 */
	static function countProperties($id,&$total){
		global $jinput, $mainframe;
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
	 * Categories list
	 *
	 * @param unknown_type $option
	 */
	static function categories_list($option)
	{
		global $jinput, $mainframe;
		$db								= Factory::getDBO();
		$config = new JConfig();
		$list_limit						= $config->list_limit;
        //Update access level to Public for all existing categories
        $db->setQuery("Update #__osrs_categories set `access` = '1' where `access` = '0'");
        $db->execute();

		$limitstart       				= $jinput->getInt('limitstart',0);
		$limit      	  				= $jinput->getInt('limit', $list_limit);
        $keyword    	 				= $jinput->getString('keyword','');
        $filter_order 	 				= $jinput->getString('filter_order','ordering');
        $filter_order_Dir 				= $jinput->getString('filter_order_Dir','');
        $filter_full_ordering			= $jinput->getString('filter_full_ordering','ordering asc');
		$filter_Arr						= explode(" ",$filter_full_ordering);
		$filter_order					= $filter_Arr[0];
		$filter_order_Dir				= $filter_Arr[1];
		if($filter_order == ""){
			$filter_order				= 'ordering';
		}
		$lists['filter_order'] 			= $filter_order;
		$lists['filter_order_Dir']		= $filter_order_Dir;
		
		$levellimit 					= 10;
		
		$query							= "Select *, category_name AS title from #__osrs_categories where 1=1";
		if($keyword != ""){
			$query .= " and category_name  like '%$keyword%'";
		}
		$query .= " order by $filter_order $filter_order_Dir";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;			
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
		$total = count( $list );
		jimport('joomla.html.pagination');
		$pageNav = new Pagination( $total, $limitstart, $limit );

		// slice out elements based on limits
		$list = array_slice( $list, $pageNav->limitstart, $pageNav->limit);
		$rows = $list;
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$alias = $row->category_alias;
				if($alias == ""){
					$alias = OSPHelper::generateAlias('category',$row->id,'');
					$db->setQuery("Update #__osrs_categories set category_alias = '$alias' where id = '$row->id'");
					$db->execute();
					$row->category_alias = $alias;
				}
			}
		}
		
		HTML_OspropertyCategories::listCategories($option,$rows,$pageNav,$lists,$children);
	}
	
	
	/**
	 * Category edit
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function categories_edit($option,$id){
		global $jinput, $mainframe,$languages;
		$db = Factory::getDBO();
		$row = Table::getInstance('Category','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
			$row->access = 0;
		}
		$lists['state']   = OSPHelper::getBooleanInput('published',$row->published);

        $lists['access'] = OSPHelper::accessDropdown('access',$row->access);
		$lists['parent'] = OspropertyCategories::listParentCategories($row);

		$optionArr = array();
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_FEATURED_PROPERTIES_FIRST')." & ".Text::_('OS_NEWEST_PROPERTIES_FIRST'));
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_NEWEST_PROPERTIES_FIRST'));
		$optionArr[] = HTMLHelper::_('select.option',2,Text::_('OS_OLDEST_PROPERTIES_FIRST'));
		$optionArr[] = HTMLHelper::_('select.option',3,Text::_('OS_CHEAPEST_PROPERTIES_FIRST'));
		$optionArr[] = HTMLHelper::_('select.option',4,Text::_('OS_MOST_EXPENSIVE_PROPERTIES_FIRST'));
		$lists['ordering']   = HTMLHelper::_('select.genericlist',$optionArr,'category_ordering','class="input-xlarge"','value','text',$row->category_ordering);
		
		$translatable = Multilanguage::isEnabled() && count($languages);
		HTML_OspropertyCategories::editCategory($option,$row,$lists,$translatable);
	}
	
	/**
	 * Save data
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	static function save($option,$save){
		global $jinput, $mainframe,$configClass,$languages;
		$db = Factory::getDBO();
		jimport('joomla.filesystem.file');
		$remove_photo = $jinput->getInt('remove_photo',0);
		
		$row = Table::getInstance('Category','OspropertyTable');
		$post = $jinput->post->getArray();
		if(is_uploaded_file($_FILES['photo']['tmp_name']))
		{
			if(!HelperOspropertyCommon::checkIsPhotoFileUploaded('photo'))
			{
				//return to previous page
				//do nothing
				$row->category_image = "";
			}
			else
			{
				$filename = OSPHelper::processImageName(time()."_".$_FILES['photo']['name']);
				$dest     = JPATH_ROOT.DS."images".DS."osproperty".DS."category".DS.$filename;
				$thumb	  = JPATH_ROOT.DS."images".DS."osproperty".DS."category".DS."thumbnail".DS.$filename;
				File::upload($_FILES['photo']['tmp_name'],$dest);
				//resize
				@copy($dest,$thumb);
				$nwidth = $configClass['images_thumbnail_width'];
				$nheight = $configClass['images_thumbnail_height'];
				OSPHelper::resizePhoto($thumb,$nwidth,$nheight);
				$row->category_image = $filename;
			}
		}
		elseif($remove_photo == 1)
		{
			$row->category_image = "";
		}
		$row->bind($post);
		$category_description = $_POST['category_description'];
		$row->category_description = $category_description;
		$category_meta		  = $_POST['category_meta'];
		$row->category_meta	  = $category_meta;
		$id = $jinput->getInt('id',0);
		if($id == 0){
			//get the ordering
			$db->setQuery("Select ordering from #__osrs_categories where parent_id = '$row->parent_id' order by ordering desc limit 1");
			$ordering = $db->loadResult();
			$row->ordering = $ordering + 1;
		}
		if (!$row->store())
	 	{
		 	throw new Exception($row->getError(), 500);
		}
		if($id == 0){
			$id = $db->insertID();
		}
		$category_alias = $jinput->getString('category_alias','');
		$category_alias = OSPHelper::generateAlias('category',$id,$category_alias);
		$db->setQuery("Update #__osrs_categories set category_alias = '$category_alias' where id = '$id'");
		$db->execute();
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			foreach ($languages as $language){	
				$sef = $language->sef;
				$category_name_language = $jinput->getString('category_name_'.$sef,'');
				$category_description_language = $_POST['category_description_'.$sef];
				if($category_name_language == ""){
					$category_name_language = $row->category_name;
				}
				if($category_name_language != ""){
					$category = Table::getInstance('Category','OspropertyTable');
					$category->id = $id;
					$category->access = $row->access;
					$category->{'category_name_'.$sef} = $category_name_language;
					$category->store();
				}
				if($category_description_language == ""){
					$category_description_language = $row->category_description;
				}
				if($category_description_language != ""){
					$category = Table::getInstance('Category','OspropertyTable');
					$category->id = $id;
					$category->access = $row->access;
					$category->{'category_description_'.$sef} = $category_description_language;
					$category->store();
				}
				
				$category_alias = $jinput->getString('category_alias_'.$sef,'');
				$category_alias = OSPHelper::generateAliasMultipleLanguages('category',$id,$category_alias,$sef);
				$db->setQuery("Update #__osrs_categories set category_alias_".$sef." = '$category_alias' where id = '$id'");
				$db->execute();
			}
		}
		$msg = Text::_('OS_ITEM_HAS_BEEN_SAVED');
		$mainframe->enqueueMessage($msg);
		if($save == 1){
			$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
		}elseif($save == 2){
			$mainframe->redirect("index.php?option=com_osproperty&task=categories_add");
		}else{
			$mainframe->redirect("index.php?option=com_osproperty&task=categories_edit&cid[]=$id");
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
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				$db->setQuery("Select category_image from #__osrs_categories where id = '$id'");
				$category_image = $db->loadResult();
				$imagelink = JPATH_ROOT.DS."components".DS."com_osproperty".DS."images".DS."category";
				unlink($imagelink.DS.$category_image);
				unlink($imagelink.DS."thumbnail".DS.$category_image);
			}
			$cids = implode(",",$cid);
			$db->setQuery("Delete from #__osrs_categories where id in ($cids)");
			$db->execute();
			//remove fields
			$db->setQuery("Select pid from #__osrs_property_categories where category_id = '$id'");
			$pids = $db->loadObjectList();
			$property_id_array = array();
			if(count($pids)){
				foreach($pids as $pid){
					$db->setQuery("Delete from #___osrs_property_categories where pid = '$pid' and category_id = '$id'");
					$db->execute();
					$db->setQuery("Select count(id) from #__osrs_property_categories where pid = '$pid'");
					$count_pid = $db->loadResult();
					if($count_pid == 0){
						$property_id_array[] = $pid;
					}
				}
				OspropertyProperties::remove($option,$property_id_array,0);
			}
		}
		$msg = Text::_('OS_ITEM_HAS_BEEN_DELETED');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
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
			$db->setQuery("Update #__osrs_categories set published = '$state' where id in ($cids)");
			$db->execute();
		}
		$msg = Text::_("OS_ITEM_STATUS_HAS_BEEN_CHANGED");
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
	}
	

	static function saveorderAjax($option){
		global $jinput, $mainframe;

		$db				= Factory::getDBO();
		$cid 			= $jinput->get( 'cid', array(), 'array' );
		
		$order			= $jinput->get( 'order', array(), 'array' );
		
		$row			= Table::getInstance('Category','OspropertyTable');
		$groupings		= array();
		// update ordering values
		$txt = "";
		for( $i=0; $i < count($cid); $i++ ) {
			$row->load( $cid[$i] );
			// track parents
			$groupings[] = $row->parent_id;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				$txt .= $cid[$i]." ".$row->ordering."/n";
				$row->store();
			} // if
		} // for
		
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder(' parent_id = '.(int) $group.' AND published = 1');
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
		$cid 	= $jinput->get( 'cid', array(),'array' );

		$row = Table::getInstance('Category','OspropertyTable');
		
		$groupings	= array();

		$order		= $jinput->get( 'order', array(),'array' );

		// update ordering values
		for( $i=0; $i < count($cid); $i++ ) {
			$row->load( $cid[$i] );
			// track parents
			$groupings[] = $row->parent_id;
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
			$row->reorder(' parent_id = '.(int) $group.' AND published = 1');
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
	}
	
	
	
	/**
	 * Order up
	 *
	 * @return unknown
	 */
	static function orderup(){
		global $jinput, $mainframe,$_jversion;

		$cid	= $jinput->get( 'cid', array(), 'array' );

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect(
				'index.php?option=com_osproperty&task=categories_list',
				Text::_('OS_NO_ITEM_SELECTED')
			);
			return false;
		}

		if (OspropertyCategories::orderItem($id, -1)) {
			$msg = Text::_( 'OS_MENU_ITEM_MOVED_UP' );
		} else {
			//$msg = $model->getError();
		}
		
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
	}
	
	/**
	 * Order down
	 *
	 * @param unknown_type $option
	 */
	static function orderdown($option){
		global $jinput, $mainframe,$_jversion;

		$cid	= $jinput->get( 'cid', array(), 'array' );

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect(
				'index.php?option=com_osproperty&task=categories_list',
				Text::_('OS_NO_ITEM_SELECTED')
			);
			return false;
		}

		if (OspropertyCategories::orderItem($id, 1)) {
			$msg = Text::_( 'OS_MENU_ITEM_MOVED_DOWN' );
		} else {
			//$msg = $model->getError();
		}
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
	}
	
	/**
	 * Order Item
	 *
	 * @param unknown_type $item
	 * @param unknown_type $movement
	 * @return unknown
	 */
	static function orderItem($item, $movement){
		$row = Table::getInstance('Category','OspropertyTable');
		$row->load( $item );
		if (!$row->move( $movement, ' parent_id = '.(int) $row->parent_id )) {
			$this->setError($row->getError());
			return false;
		}
		return true;
	}
	
	/**
	 * Build the select list for parent menu item
	 */
	static function listParentCategories( $row ){
		$db =& Factory::getDBO();

		// If a not a new item, lets set the menu item id
		if ( $row->id ) {
			$id = ' AND id != '.(int) $row->id;
		} else {
			$id = null;
		}

		// In case the parent was null
		if (!$row->parent_id) {
			$row->parent_id = 0;
		}

		// get a list of the menu items
		// excluding the current cat item and its child elements
		$query = 'SELECT *, category_name AS title ' .
				 ' FROM #__osrs_categories ' .
				 ' WHERE published = 1' .
				 $id .
			 	 ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems )
		{
			// first pass - collect children
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		// assemble menu items to the array
		$parentArr 	= array();
		$parentArr[] 	= HTMLHelper::_('select.option',  '0', Text::_( 'Top' ) );
		
		foreach ( $list as $item ) {
			if($item->treename != ""){
				$item->treename = str_replace("&nbsp;","",$item->treename);
			}
			$var = explode("-",$item->treename);
			$treename = "";
			for($i=0;$i<count($var)-1;$i++){
				$treename .= " - ";
			}
			$text = $item->treename;
			$parentArr[] = HTMLHelper::_('select.option',  $item->id,$text);
		}
		$output = HTMLHelper::_('select.genericlist', $parentArr, 'parent_id', 'class="inputbox ilarge form-select"', 'value', 'text', $row->parent_id );
		return $output;
	}
	
	
	/**
	 * Go to list
	 *
	 * @param unknown_type $option
	 */
	static function gotolist($option){
		global $jinput, $mainframe;
		$mainframe->redirect("index.php?option=com_osproperty&task=categories_list");
	}
}
?>
