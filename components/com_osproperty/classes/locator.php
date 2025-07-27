<?php

/*------------------------------------------------------------------------
# locator.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
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
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class OspropertyLocator{
	/**
	 * Display Default agent layout
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$language = Factory::getLanguage();
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
		switch ($task){
			case "locator_getstate":
				OspropertyLocator::getstateforcountry($option);
			break;
			default:
				echo HelperOspropertyCommon::buildToolbar('locator');
				OspropertyLocator::locatorSearch($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
		}
	}
	
	/**
	 * Locator searching layout
	 *
	 * @param unknown_type $option
	 */
	static function locatorSearch($option)
	{
		global $jinput, $mainframe,$configClass,$lang_suffix;
		$max_locator_results = (intval($configClass['max_locator_results']) > 0)? $configClass['max_locator_results']:'100';
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_SEARCH_LOCATOR'));
		$db = Factory::getDBO();
		$lists = [];
		$radius_arr = array(5,10,20,50,100,200);
		$no_search = true;
		
		$locator_type_ids = $configClass['adv_type_ids'];
		if($locator_type_ids != ""){
			$locator_type_idArrs = explode("|",$locator_type_ids);
			if(count($locator_type_idArrs) > 0){
				$locator_type_ids = $locator_type_idArrs[0];
			}
		}
		$menus			= Factory::getApplication()->getMenu();
        $menu			= $menus->getActive();
		$locator_style  = 'style1';

		$search_my_location	= $jinput->getInt('search_my_location',0);
		$lists['search_my_location'] = $search_my_location;
		$my_lat			= OSPHelper::getStringRequest('my_lat','');
		$my_long		= OSPHelper::getStringRequest('my_long','');
		$locator_type   = $jinput->getInt('locator_type',$locator_type_ids);
		$locator_search	= $jinput->getInt('locator_search',0);
		$location		= OSPHelper::getStringRequest('location',$configClass['default_location'],'');
		$lists['location'] = $location;
		$default_search	= $jinput->getInt('default_search',0);
		$keyword		= OSPHelper::getStringRequest('keyword','','');
		$keyword		= $db->escape($keyword);
		$categoryArr	= $jinput->get('category_ids',array(),'ARRAY');
        $categoryArr    = ArrayHelper::toInteger($categoryArr);
		$property_type	= $jinput->getInt('property_type',$locator_type);
		$country_id		= $jinput->getInt('country_id',HelperOspropertyCommon::getDefaultCountry());

		$price			= $jinput->getInt('price',0);
		$agent_id    	= $jinput->getInt('agent_id','');
		$radius_search  = $jinput->getInt('radius_search',isset($configClass['default_radius'])? $configClass['default_radius']:'20');
		$ajax			= $jinput->getInt('ajax',0);
		$sqft_min		= OSPHelper::getStringRequest('sqft_min',0,'');
		$lists['sqft_min'] = $sqft_min;
		$sqft_max		= OSPHelper::getStringRequest('sqft_max',0,'');
		$lists['sqft_max'] = $sqft_max;
		$nbath			= $jinput->getInt('nbath',0,'');
		$nbed			= $jinput->getInt('nbed',0,'');
		$nroom			= $jinput->getInt('nroom',0,'');
		$isSold			= $jinput->getInt('isSold',0);
		
		$min_price		= $jinput->getInt('min_price',0);
		$max_price		= $jinput->getInt('max_price',0);
		
		$radius_search_cal = 0;
		
		if(intval($radius_search) > 0)
		{
			if($configClass['locator_radius_type'] == 1)
			{
				$radius_search_cal = 0.62*$radius_search;
			}
			else
			{
				$radius_search_cal = $radius_search;
			}
		}
		// condition free	
		$dosearch = 0;
		if(!HelperOspropertyCommon::checkCountry()){
			$dosearch = 0;
		}elseif($country_id > 0){
			$dosearch = 1;
		}
		if($location != ""){
			$dosearch = 1;
		}
		if($my_lat !== "" && $my_long != ""){
			$dosearch = 1;
		}
		if(intval($price) > 0 || $min_price > 0 || $max_price > 0)
		{
			$dosearch = 1;
		}
		//if($state_id > 0){
			//$dosearch = 1;
		//}
		if($keyword != ""){
			$dosearch = 1;
		}
		if($property_type > 0){
			$dosearch = 1;
		}
		if(count($categoryArr) > 0){
			$dosearch = 1;
		}
		if($ajax == 1){
			$dosearch = 1;
		}
		
		if($radius_search > 0)
		{
			$google_address_search_encode = urldecode($location);
			if($configClass['map_type'] == 0)
			{
				$return				= HelperOspropertyGoogleMap::findAddress($option,'',$google_address_search_encode,1);
			}
			else
			{
				$return				= HelperOspropertyOpenStreetMap::findAddress($option,'',$google_address_search_encode,1);
			}
			$search_lat				= $return[0];
			$search_long			= $return[1];
			$status					= $return[2];
			//$search_lat				= 40.7127281;
			//$search_long			= -74.0060152;
			if($my_lat!= "" && $my_long != "")
			{
				$search_lat			= $my_lat;
				$search_long		= $my_long;
				$status				= "OK";
			}
		}
		
		if (($location != '' || $my_lat!= "" && $my_long != "") && $radius_search > 0 && $status == "OK")
		{
			$sortby			= OSPHelper::getStringRequest('sortby','distance','');
			$orderby 		= OSPHelper::getStringRequest('orderby','asc','');
		}
		else
		{
			$sortby			= OSPHelper::getStringRequest('sortby','a.created','');
			$orderby 		= OSPHelper::getStringRequest('orderby','desc','');
		}

		if($location == '' || $radius_search == 0 || $status != "OK")
		{
			if($sortby == "distance")
			{
				$sortby = "id";
			}
		}

		if($sortby == '')
		{
			$sortby = "id";
		}
		$lists['sortby'] = $sortby;
		$lists['orderby'] = $orderby;


        $optionArr = [];
        $optionArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_SORT_BY'));
        $optionArr[] = HTMLHelper::_('select.option','a.created',Text::_('OS_CREATED'));
        $optionArr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
        $optionArr[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
        $optionArr[] = HTMLHelper::_('select.option','distance',Text::_('OS_DISTANCE'));
        $lists['sort'] = HTMLHelper::_('select.genericlist',$optionArr,'sortby','class="input-medium form-select imedium"','value','text',$lists['sortby']);

        $optionArr = [];
        $optionArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_ORDER_BY'));
        $optionArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
        $optionArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
        $lists['order'] = HTMLHelper::_('select.genericlist',$optionArr,'orderby','class="input-medium form-select imedium"','value','text',$lists['orderby']);
		// creat option search
		// keyword
		$lists['keyword'] = $keyword;
			
		//categories
		//$lists['category'] = OSPHelper::listCategoriesCheckboxes($categoryArr);
        //$lists['category'] = OSPHelper::listCategoriesInMultiple($categoryArr,'');

		$lists['categoryArr'] = $categoryArr;
		//property types
		
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'property_type','class="input-large form-select ilarge"','value','text',$property_type);

		$lists['market_status'] = OSPHelper::buildDropdownMarketStatus($isSold);

		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country_id','onchange="change_country_company(this.value)"',Text::_('OS_ALL_COUNTRIES'),'','inputlarge');
	
		$lists['state'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onchange="change_state(this.value,'.intval($city).')"',Text::_('OS_ALL_STATES'),'','class="input-large"');
			
		//list city
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$state_id, $city);
		
		//city
		$lists['city_name'] = $city;
			
		// postcode
		$lists['postcode'] = $postcode;
			
		// region
		$lists['region'] = $region;
			
		// province
		//$lists['province'] = $province;
			
		//number bed room
		$lists['nbed'] = $nbed;
		for($i=0;$i<=5;$i++){
			$bedArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nbed'] = HTMLHelper::_('select.genericlist',$bedArr,'nbed','class="input-mini form-select imini" ','value','text',$nbed);
			
		// number bath room
		for($i=0;$i<=5;$i++){
			$bathArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nbath'] = HTMLHelper::_('select.genericlist',$bathArr,'nbath',' class="input-mini form-select imini"','value','text',$nbath);
			
		// number bath room
		for($i=0;$i<=5;$i++){
			$roomArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nroom'] = HTMLHelper::_('select.genericlist',$roomArr,'nroom',' class="input-mini form-select imini"','value','text',$nroom);
		
		
		// address
		$lists['address_search'] = $address_search;
		
		$lists['location'] = $location;
			
		// distance radius
		$radiusArr = [];
		$radius_type = ($configClass['locator_radius_type'] == 0) ? Text::_('OS_MILES') : Text::_('OS_KILOMETRE');
		foreach ($radius_arr as $radius) {
			$radiusArr[] = HTMLHelper::_('select.option',$radius, $radius. ' '. $radius_type);
		}
		$lists['radius'] = HTMLHelper::_('select.genericlist',$radiusArr,'radius_search','class="input-medium  search-query marginbottom20 form-select imedium"', 'value', 'text',$radius_search);
		
			
		// Query database
		$select = "SELECT distinct a.id, a.*, c.name as agent_name, d.id as typeid, d.type_name$lang_suffix as type_name, e.country_name"; 
		$from =	 " FROM #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." INNER JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as g on g.id = a.state"
				." LEFT JOIN #__osrs_cities as h on h.id = a.city";
		$where = " WHERE a.published = '1' AND a.approved = '1' AND a.lat_add <> '' AND a.long_add <> '' AND a.show_address = '1'";
		$user = Factory::getUser();
        $agent_id = 0;
        if($user->id > 0){
            if(HelperOspropertyCommon::isAgent())
			{
                $agent_id = HelperOspropertyCommon::getAgentID();
            }
        }
        if($agent_id > 0){
            $where .= ' and ((a.`access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')) || (a.agent_id = "'.$agent_id.'"))';
        }else{
            $where .= ' and a.`access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        }
		$Order_by = "ORDER BY $sortby $orderby limit $max_locator_results";
		
		if($keyword != ""){
			$where .= " AND (";
			$where .= " a.ref like '%$keyword%' OR";
			$where .= " a.pro_name$lang_suffix like '%$keyword%' OR";
			$where .= " a.pro_small_desc$lang_suffix like '%$keyword%' OR";
			$where .= " a.pro_full_desc$lang_suffix like '%$keyword%' OR";
			$where .= " a.region like '%$keyword%' OR";
			$where .= " a.address like '%$keyword%' OR ";
			$where .= " g.state_name$lang_suffix like '%$keyword%' OR ";
			$where .= " a.postcode like '%$keyword%' OR";
			$where .= " h.city$lang_suffix like '%$keyword%'";
			$where .= " )";
			$no_search = false;
		}
		
		if($address_search != "")
		{
			$no_search = false;
		}
		
		$lists['orderby'] = $orderby;
		if(count($categoryArr) > 0){
			$catids      = implode(",",$categoryArr);
            if($catids != "") {
                $where .= " AND a.id in (Select pid from #__osrs_property_categories where category_id IN ($catids))";
                $no_search = false;
            }
		}
		//if($ajax = 0){
		if($property_type > 0) 	{$where .= " AND a.pro_type = '$property_type'";	$no_search = false;}
		//}
		
		if ($country_id > 0){
			$where .= " AND a.country = '$country_id'";	
			if((!HelperOspropertyCommon::checkCountry()) and ($no_search == true))	{
				$no_search = true;
			}else{
				$no_search = false;
			}
		}
		
		if($nbed > 0)			{$where .= " AND a.bed_room >= '$nbed'";			$no_search = false;}
		
		if($nbath > 0)			{$where .= " AND a.bath_room >= '$nbath'";			$no_search = false;}	
		
		if($nroom > 0)			{$where .= " AND a.rooms >= '$nroom'";				$no_search = false;}	
		
		if($sqft_min > 0)		{$where .= " AND a.square_feet >= '$sqft_min'";		$no_search = false;}	
		
		if($sqft_max > 0)		{$where .= " AND a.square_feet <= '$sqft_max'";		$no_search = false;}
		
		if($isSold > 0)			{$where .= " AND a.isSold = '$isSold'";				$no_search = false;}
		
		if($price > 0){
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
    		$dosearch = 1;
			$no_search = false;
		}
		
		if($min_price > 0){
			$where .= " AND a.price >= '$min_price'";
		}
		
		if($max_price > 0){
			$where .= " AND a.price <= '$max_price'";
		}
		
		if ($default_search) $no_search = false;
		if (( $location != '' || ($my_lat!= "" && $my_long != "") ) && $radius_search > 0){
			if ($status == "OK") {
				$multiFactor = 3959;
				// Search the rows in the table
				$select .= sprintf(", ( %s * acos( cos( radians('%s') ) * 
									cos( radians( a.lat_add ) ) * cos( radians( a.long_add ) - radians('%s') ) + 
									sin( radians('%s') ) * sin( radians( a.lat_add ) ) ) ) 
									AS distance",
									$multiFactor,
									doubleval($search_lat),
									doubleval($search_long),
									doubleval($search_lat)
									);
				$where .= sprintf("	HAVING distance < '%s'", doubleval($radius_search_cal));
				$no_search = false;
			}
		}
		$rows = [];
		//$group_by = " GROUP BY a.id ";
		$group_by = "";
		if (($dosearch == 1) && ( ($location != '') || (($my_lat!= "") && ($my_long != "")) )){
			$db->setQuery($select.' '.$from.' '.$where.' '.$Order_by);
			$rows = $db->loadObjectList();
		}

		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
				$row->pro_name = $pro_name;
				$pro_small_desc = OSPHelper::getLanguageFieldValue($row,'pro_small_desc');
				$row->pro_small_desc = $pro_small_desc;
				$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id' order by ordering");
				$image = $db->loadResult();
				$row->image = $image;
				$row->category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($row->id);
                $need = [];
                $need[] = "property_details";
                $need[] = $row->id;
                $itemid = OSPRoute::getItemid($need);
                $row->itemid = $itemid;

				if($row->image == ""){
					$imgLink = Uri::root().'media/com_osproperty/assets/images/nopropertyphoto.png';
				}elseif(!file_exists(JPATH_ROOT.DS.'images/osproperty/properties/'.$row->id.'/thumb/'.$row->image)){
					$imgLink = Uri::root().'media/com_osproperty/assets/images/nopropertyphoto.png';
				}else{
					$imgLink = Uri::root().'images/osproperty/properties/'.$row->id.'/thumb/'.$row->image;
				}
				$row->photo = $imgLink;

				$small_desc = $row->pro_small_desc;
				$small_descArr = explode(" ",$small_desc);
				$small_desc_new = "";
				if(count($small_descArr) > 10){
					for($j=0;$j<=10;$j++){
						$small_desc_new .= $small_descArr[$j]." ";
					}
					$row->pro_small_desc = $small_desc_new;
				}
			}
		}
		
		if($ajax == 1){
			if(count($rows) > 0){
				$ptype = $rows[0]->pro_type;
				if($ptype > 0){
					if(in_array($ptype,$locator_type_idArrs)){
						$property_type = $ptype;
						$locator_type = $ptype;
					}
				}
			}
		}
		
		$lists['price_value']		= $price;
		$lists['locator_type']		= $locator_type;
		if($min_price == 0)
		{
			$min_price				= $configClass['min_price_slider'];
		}
		if($max_price == 0)
		{
			$max_price				= $configClass['max_price_slider'];
		}
		$lists['min_price']			= $min_price;
		$lists['max_price']			= $max_price;
		
		// change vote
		if ($locator_search || $default_search){
		}
		
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY ordering");
		$protypes = $db->loadObjectList();
		$typeArr   = [];
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ANY'));
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'property_type','class="input-large form-select ilarge"','value','text',$property_type);
		$lists['locator_type_idArrs'] = $locator_type_idArrs;


		HTML_OspropertyLocator::locatorSearchHtml($option,$rows,$lists,$locator_type,$search_lat,$search_long,$locator_style);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 */
	static function getstateforcountry($option){
		global $jinput, $mainframe,$lang_suffix;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$option_state = [];
		$option_state[]= HTMLHelper::_('select.option','',' - '.Text::_('OS_SELECT_STATE').' - ');
		
		if ($country_id){
			$db->setQuery("SELECT id AS value, state_name$lang_suffix AS text FROM #__osrs_states WHERE `country_id` = '$country_id' ORDER BY state_name$lang_suffix");		
			$states = $db->loadObjectList();
			if (count($states)){
				$option_state = array_merge($option_state,$states);
			}
			$disable = '';
		}else{
			$disable = 'disabled="disabled"';
		}
		
		echo HTMLHelper::_('select.genericlist',$option_state,'state_id','class="input-small" '.$disable,'value','text');
	}
}

?>
