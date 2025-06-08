<?php
/*------------------------------------------------------------------------
# helper.php - mod_ospropertysearch
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

class modOspropertySearchHelper
{
    public static function loadCity($option,$state_id,$city_id,$random_id,$show_labels){
        global $mainframe;
        $db = Factory::getDBO();
        $lgs = OSPHelper::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        $app = Factory::getApplication();
        if ($translatable){
            $suffix = OSPHelper::getFieldSuffix();
        } else {
            $suffix = "";
        }
        $cityArr = array();
        if($show_labels == 0){
            $cityArr[]= JHTML::_('select.option',0,Text::_('OS_CITY'));
        }else{
            $cityArr[]= JHTML::_('select.option',0,Text::_('OS_ANY'));
        }
        if($state_id > 0){
            $db->setQuery("Select id as value, city".$suffix." as text from #__osrs_cities where  published = '1' and state_id = '$state_id' order by city");
            $cities = $db->loadObjectList();
            $cityArr   = array_merge($cityArr,$cities);
            $disabled  = "";
        }else{
            $disabled  = "disabled";
        }
        return JHTML::_('select.genericlist',$cityArr,'city'.$random_id,'class="input-medium form-control form-select" '.$disabled,'value','text',$city_id);
    }

    static function listCategories($category_ids,$onChangeScript,$inputbox_width_site){
        global $mainframe;
        $jinput = Factory::getApplication()->input;
        if($inputbox_width_site != ""){
            $width_style = "width: ".$inputbox_width_site."px !important;";
        }

        if(count($category_ids) == 0){
            $view = OSPHelper::getStringRequest('view','');
            switch($view){
                case "ltype":
                    $category_ids = $jinput->get('catIds',array(),'ARRAY');
                    $category_ids = ArrayHelper::toInteger($category_ids);
                    break;
                case "lcity":
                    $menus = Factory::getApplication()->getMenu();
                    $menu = $menus->getActive();
                    if (is_object($menu)) {
                        $params = new Registry();
                        $params->loadString($menu->params);
                        $category_ids = $params->get('catIds', 0);
                    }
                    break;
            }
        }

        $parentArr = self::loadCategoryOptions($onChangeScript);
        //print_r($parentArr);
        $output = JHTML::_('select.genericlist', $parentArr, 'category_ids[]', 'style="min-height:100px;'.$width_style.'" class="input-large chosen" multiple '.$onChangeScript, 'value', 'text', $category_ids );
        return $output;
    }

    static function listCategoriesHorizontal($category_id,$onChangeScript,$inputbox_width_site)
	{
        global $mainframe;
        $jinput = Factory::getApplication()->input;        
        $parentArr = self::loadCategoryOptions($onChangeScript);
        $firstoption = ARRAY();
        $firstoption[] = HTMLHelper::_('select.option','',Text::_('OS_CATEGORY'));
        $firstoption   = array_merge($firstoption,$parentArr);
        $output = JHTML::_('select.genericlist', $firstoption, 'category_id', 'class="input-medium form-select" '.$onChangeScript, 'value', 'text', $category_id );
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
