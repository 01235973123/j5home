<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/**
 * @subpackage  mod_osquicksearchrealhomes
 * @author      Dang Thuc Dam
 * @copyright   Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
class modOspropertySearchRealHomesHelper
{
	static function listCategories($category_id,$onChangeScript)
	{
        global $mainframe;
        $jinput = Factory::getApplication()->input;        
        $parentArr = self::loadCategoryOptions($onChangeScript);
        $firstoption = ARRAY();
        $firstoption[] = HTMLHelper::_('select.option','',Text::_('OS_CATEGORY'));
        $firstoption   = array_merge($firstoption,$parentArr);
        $output = JHTML::_('select.genericlist', $firstoption, 'category_id', 'class="input-medium form-select imedium" '.$onChangeScript, 'value', 'text', $category_id );
        return $output;
    }


    static function loadCategoryOptions($onChangeScript){
        global $mainframe;
        $db = Factory::getDBO();
        $lang_suffix = OSPHelper::getFieldSuffix();
        // get a list of the menu items
        // excluding the current cat item and its child elements
        $query = 'SELECT *, category_name'.$lang_suffix.' AS title,category_name'.$lang_suffix.' as category_name,parent_id as parent ' .
            ' FROM #__osrs_categories ' .
            ' WHERE published = 1' ;
        $query .= ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query.= ' ORDER BY parent_id, ordering';
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
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
        // assemble menu items to the array
        $parentArr 	= array();

        foreach ( $list as $item ) {
            if($item->treename != ""){
                //$item->treename = str_replace("&nbsp;","",$item->treename);
            }
            $var = explode("-",$item->treename);
            $treename = "";
            for($i=0;$i<count($var)-1;$i++){
                $treename .= " - ";
            }
            $text = $item->treename;
            $parentArr[] = JHTML::_('select.option',  $item->id,$text);
        }
        return $parentArr;
    }
}
?>


