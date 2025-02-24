<?php
/*------------------------------------------------------------------------
# cronhelper.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OSPHelperCron{
    /**
     * This function is used to check the property is suitable with the list
     * @param $list
     */
    static function checkProperty($property,$list){
		global $jinput;
		$configClass = OSPHelper::loadConfig();
		$jinput = Factory::getApplication()->input;
        $db = Factory::getDbo();
        $db->setQuery("Select * from #__osrs_user_list_details where list_id = '$list->id'");
        $list_details = $db->loadObjectList();
        for($i=0;$i<count($list_details);$i++) {
            $list_detail = $list_details[$i];
            switch ($list_detail->field_id) {
                case "keyword":
                    $jinput->set('keyword',$list_detail->search_param);
                    break;
                case "add":
                    $jinput->set('address', $list_detail->search_param);
                    break;
                case "agent_type":
                    $jinput->set('agent_type', $list_detail->search_param);
                    break;
                case "catid":
                    $temp_category_ids[] = $list_detail->search_param;
                    break;
                case "type":
                    //$jinput->set('property_type',$list_detail->search_param);
                    $temp_type_ids[] = $list_detail->search_param;
                    break;
                case "amenity":
                    $temp_amen_ids[] = $list_detail->search_param;
                    break;
                case "country":
                    $jinput->set('country_id', $list_detail->search_param);
                    break;
                case "state":
                    $jinput->set('state_id', $list_detail->search_param);
                    break;
                case "city":
                    $jinput->set('city', $list_detail->search_param);
                    break;
                case "nbath":
                    $jinput->set('nbath', $list_detail->search_param);
                    break;
                case "nbed":
                    $jinput->set('nbed', $list_detail->search_param);
                    break;
                case "price":
                    $jinput->set('price', $list_detail->search_param);
                    break;
                case "min_price":
                    $jinput->set('min_price', $list_detail->search_param);
                    break;
                case "max_price":
                    $jinput->set('max_price', $list_detail->search_param);
                    break;
                case "nroom":
                    $jinput->set('nroom', $list_detail->search_param);
                    break;
                case "nfloors":
                    $jinput->set('nfloors', $list_detail->search_param);
                    break;
                case "sqft_min":
                    $jinput->set('sqft_min', $list_detail->search_param);
                    break;
                case "sqft_max":
                    $jinput->set('sqft_max', $list_detail->search_param);
                    break;
                case "lotsize_min":
                    $jinput->set('lotsize_min', $list_detail->search_param);
                    break;
                case "lotsize_max":
                    $jinput->set('lotsize_max', $list_detail->search_param);
                    break;
                case "featured":
                    $jinput->set('isFeatured', $list_detail->search_param);
                    break;
                case "sold":
                    $jinput->set('isSold', $list_detail->search_param);
                    break;
                case "sortby":
                    $jinput->set('sortby', $list_detail->search_param);
                    break;
                case "orderby":
                    $jinput->set('orderby', $list_detail->search_param);
                    break;
                default:
                    HelperOspropertyFields::setFieldValue($list_detail);
                    break;
            }
        }
        if(count($temp_category_ids) > 0){
            $jinput->set('category_ids',$temp_category_ids);
        }
        if(count($temp_type_ids) > 0){
            $jinput->set('property_types',$temp_type_ids);
        }
        if(count($temp_amen_ids) > 0){
            $jinput->set('amenities',$temp_amen_ids);
        }
        $category_ids	= $jinput->get('category_ids',array(),'ARRAY');//JRequest::getVar('category_ids');//array
        $agent_type		= $jinput->getInt('agent_type',-1);
        $country_id		= OSPHelper::getStringRequest('country_id',HelperOspropertyCommon::getDefaultCountry());
        $city			= $jinput->getInt('city',0);
        $state_id		= $jinput->getInt('state_id',0);
        $nbed			= $jinput->getInt('nbed',0);
        $nbath			= $jinput->getInt('nbath',0);
        $price			= $jinput->getInt('price',0);
        $nroom 			= $jinput->getInt('nroom',0);
        $nfloors		= $jinput->getInt('nfloors',0);
        $address		= OSPHelper::getStringRequest('address','','');
        $address		= $db->escape($address);
        $keyword		= OSPHelper::getStringRequest('keyword','',''); //JRequest::getVar('keyword','','','string');
        $keyword		= $db->escape($keyword);
        $isFeatured		= $jinput->getInt('isFeatured',0);
        $isSold			= $jinput->getInt('isSold',0);
        $sortby			= OSPHelper::getStringRequest('sortby',$configClass['adv_sortby']);
        $orderby		= OSPHelper::getStringRequest('orderby',$configClass['adv_orderby']);
        $min_price		= $jinput->getInt('min_price',0);
        $max_price   	= $jinput->getInt('max_price',0);
        $sqft_min		= $jinput->getInt('sqft_min',0);
        $sqft_max		= $jinput->getInt('sqft_max',0);
        $lotsize_min	= $jinput->getInt('lotsize_min',0);
        $lotsize_max	= $jinput->getInt('lotsize_max',0);
        $amenities		= $jinput->getVar('amenities','','','array');

        $property_types	= $jinput->get('property_types',array(),'ARRAY');//JRequest::getVar('property_types',null);//array
        //$category_ids	= $jinput->get('category_ids',array(),'ARRAY');//JRequest::getVar('category_ids');//array

        if(count($amenities) > 0){

            $amenities_str = implode(",",$amenities);

            if($amenities_str != ""){
                $amenities_sql = " AND a.id in (SELECT pro_id FROM #__osrs_property_amenities WHERE amen_id in ($amenities_str) group by pro_id having count(pro_id) = ".count($amenities).")";
                $dosearch = 1;
            }else{
                $amenities_sql = "";
            }
        }else{
            $amenities_sql = "";
        }



        $access_sql = "";

        $db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
        $groups = $db->loadObjectList();
        if(count($groups) > 0){
            $extrafieldSql = array();
            for($i=0;$i<count($groups);$i++){
                $group = $groups[$i];
                $extraSql = "";
                if(count($types) > 0){
                    $extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id in (".implode(",",$types).")) ";
                }elseif($adv_type > 0){
                    $extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id = '$adv_type')";
                }
                $db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group->id' $extraSql and published = '1' and searchable = '1' $access_sql order by ordering");
                //echo $db->getQuery();
                $fields = $db->loadObjectList();
                $group->fields = $fields;
                if(count($fields) > 0){
                    for($j=0;$j<count($fields);$j++){
                        $field = $fields[$j];
                        //check do search
                        $check = HelperOspropertyFields::checkField($field);
                        if($check){
                            $dosearch = 1;
                            $sql = HelperOspropertyFields::buildQuery($field);
                            if($sql != ""){
                                $extrafieldSql[] = $sql;
                                $param[]		 = HelperOspropertyFields::getFieldParam($field);
                            }
                        }
                    }
                }
            }
        }

        //$select = "SELECT distinct a.id, a.*, c.name as agent_name,c.photo as agent_photo, d.id as type_id,d.type_name$lang_suffix as type_name, e.country_name";
        $count  = "SELECT count(distinct a.id) ";
        $from =	 " FROM #__osrs_properties as a"
            ." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
            ." INNER JOIN #__osrs_types as d on d.id = a.pro_type"
            ." INNER JOIN #__osrs_states as g on g.id = a.state"
            ." LEFT JOIN #__osrs_cities as h on h.id = a.city"
            ." LEFT JOIN #__osrs_countries as e on e.id = a.country";
        $where = " WHERE a.published = '1' AND a.approved = '1' ";
        //important point
        $where .= " AND a.id = '$property->id'";
        $where .= ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

        if($isFeatured == 1){
            $where .= " AND a.isFeatured = '1'";
        }

        if($isSold == 1){
            $where .= " AND a.isSold = '1'";
        }

        if($address != ""){
            $address = str_replace(";","",$address);
            if(strpos($address,",")){
                $addressArr = explode(",",$address);
                if(count($addressArr) > 0){
                    $where .= " AND (";
                    foreach ($addressArr as $address_item){
                        $where .= " a.ref like '%$address_item%' OR";
                        $where .= " a.pro_name$lang_suffix like '%$address_item%' OR";
                        $where .= " a.address like '%$address_item%' OR";
                        $where .= " a.region like '%$address_item%' OR";
                        $where .= " a.postcode like '%$address_item%' OR";
                        $where .= " g.state_name$lang_suffix like '%$address_item%' OR";
                        $where .= " h.city$lang_suffix like '%$address_item%' OR";
                    }
                    $where = substr($where,0,strlen($where)-2);
                    $where .= " )";
                }
            }else{
                $where .= " AND (";
                $where .= " a.ref like '%$address%' OR";
                $where .= " a.pro_name$lang_suffix like '%$address%' OR";
                $where .= " a.address like '%$address%' OR";
                $where .= " a.region like '%$address%' OR";
                $where .= " g.state_name$lang_suffix like '%$address%' OR";
                $where .= " h.city$lang_suffix like '%$address%' OR";
                $where .= " a.postcode like '%$address%'";
                $where .= " )";
            }
            $no_search = false;
        }

        if($keyword != ""){
            $where .= " AND (";
            $where .= " a.ref like '%$keyword%' OR";
            $where .= " a.pro_name$lang_suffix like '%$keyword%' OR";
            $where .= " a.pro_small_desc$lang_suffix like '%$keyword%' OR";
            $where .= " a.pro_full_desc$lang_suffix like '%$keyword%' OR";
            $where .= " a.note like '%$keyword%' OR";
            $where .= " a.postcode like '%$keyword%' OR";
            $where .= " g.state_name$lang_suffix like '%$keyword%' OR";
            $where .= " h.city$lang_suffix like '%$keyword%' OR";
            $where .= " a.ref like '%$keyword%'";

            $where .= " )";
            $no_search = false;
        }
        if (count($category_ids) >  0){
            $categoryArr = array();
            foreach ($category_ids as $category_id){
                if($category_id > 0){
                    $categoryArr = HelperOspropertyCommon::getSubCategories($category_id,$categoryArr);
                    $no_search = false;
                }
            }
            $catids = implode(",",$categoryArr);
            if($catids != ""){
                $where .= " AND a.id in (Select pid from #__osrs_property_categories where category_id in ($catids)) ";
            }
        }

        if (count($property_types) >  0){
            $no_search = false;
            //$type_ids = implode(",",$property_types);
            $tempArr = array();
            foreach ($property_types as $type_id){
                if($type_id > 0){
                    $tempArr[] = "$type_id";
                }
            }
            if(count($tempArr) > 0){
                $temp_sql = implode(",",$tempArr);
                $where .= " AND a.pro_type in (".$temp_sql.")";
            }
        }

        //if ($property_type > 0) 	{$where .= " AND a.pro_type = '$property_type'";	$no_search = false;}
        if ($country_id > 0)		{$where .= " AND a.country = '$country_id'";		$no_search = false;}
        if ($city > 0)				{$where .= " AND a.city = '$city'";					$no_search = false;}
        if ($state_id >0)			{$where .= " AND a.state = '$state_id'";			$no_search = false;}
        if ($nbed > 0)				{$where .= " AND a.bed_room >= '$nbed'";			$no_search = false;}
        if ($nbath > 0)				{$where .= " AND a.bath_room >= '$nbath'";			$no_search = false;}
        if ($nroom > 0)				{$where .= " AND a.rooms >= '$nroom'";				$no_search = false;}
        if ($nfloors > 0)			{$where .= " AND a.number_of_floors >= '$nfloors'";	$no_search = false;}
        if ($agent_type >= 0)		{$where .= " AND c.agent_type = '$agent_type'";		$no_search = false;}

        if($price > 0)
		{
            $db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
            $pricegroup = $db->loadObject();
            $price_from = $pricegroup->price_from;
            $price_to	= $pricegroup->price_to;
            if($price_from  > 0){
                $where .= " AND (a.price >= '$price_from')";
            }
            if($price_to > 0){
                $where .= " AND (a.price <= '$price_to')";
            }
            $no_search = false;
        }

        if($min_price > 0){
            $where .= " AND a.price >= '$min_price'";
        }
        if($max_price > 0){
            $where .= " AND a.price <= '$max_price'";
        }
        if($sqft_min > 0){
            $where .= " AND a.square_feet >= '$sqft_min'";
            $lists['sqft_min'] = $sqft_min;
        }
        if($sqft_max > 0){
            $where .= " AND a.square_feet <= '$sqft_max'";
            $lists['sqft_max'] = $sqft_max;
        }
        if($lotsize_min > 0){
            $where .= " AND a.lot_size >= '$lotsize_min'";
            $lists['lotsize_min'] = $lotsize_min;
        }
        if($lotsize_max > 0){
            $where .= " AND a.lot_size <= '$lotsize_max'";
            $lists['lotsize_max'] = $lotsize_max;
        }
        if(isset($extrafieldSql) && count($extrafieldSql) > 0)
		{
            $extrafieldSql = implode(" AND ",$extrafieldSql);
            if(trim($extrafieldSql) != "")
			{
                $where .= " AND ".$extrafieldSql;
            }
        }
        $where .= $amenities_sql;
        $where .= $rangeDateQuery;
        $db->setQuery($count.' '.$from.' '.$where.' '.$group_by);
        $total = $db->loadResult();
        if(intval($total) > 0) 
		{
            //insert into MySQL #__osrs_list_properties
            $db->setQuery("Select count(id) from #__osrs_list_properties where pid = '$property->id' and list_id = '$list->id' and is_new = '$property->is_new'");
            $count = $db->loadResult();
            if ($count == 0) 
			{
                $db->setQuery("Insert into #__osrs_list_properties (id,pid,list_id,sent_notify,is_new) values (NULL,'$property->id','$list->id',1,'$property->is_new')");
                $db->execute();
            } 
			else 
			{
                $db->setQuery("Insert into #__osrs_list_properties (id,pid,list_id,sent_notify,is_new) values (NULL,'$property->id','$list->id','0','$property->is_new')");
                $db->execute();
            }
        }
		else
		{
            $db->setQuery("Insert into #__osrs_list_properties (id,pid,list_id,sent_notify,is_new) values (NULL,'$property->id','$list->id','0','$property->is_new')");
            $db->execute();
        }
    }
}
?>