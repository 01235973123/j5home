<?php

/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	OS Property
 * @author  	Dang Thuc Dam
 * @copyright	Copyright (C) 2023 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\Router\RouterBase;
use Joomla\Utilities\ArrayHelper;

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR);

/**
 * Routing class from com_eventbooking
 *
 * @since  2.8.1
 */
class OspropertyRouter extends RouterBase
{
	/**
	 *
	 * Build the route for the com_osproperty component
	 * @param	array	An array of URL arguments
	 * @return	array	The URL arguments to use to assemble the subsequent URL.
	 * @since	1.5
	 */
	function build(&$query)
	{
		$segments		= array();
		require_once JPATH_ROOT . '/components/com_osproperty/helpers/helper.php';
		$configClass	= OSPHelper::loadConfig();
		$db				= Factory::getDbo();
		$queryArr		= $query;
		if (isset($queryArr['option']))
			unset($queryArr['option']);
		if (isset($queryArr['Itemid']))
			unset($queryArr['Itemid']);
		//Store the query string to use in the parseRouter method
		$queryString	= http_build_query($queryArr);
		$app			= Factory::getApplication();
		$menu			= $app->getMenu();

		//We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
			$menuItem	= $menu->getActive();
		else
			$menuItem	= $menu->getItem($query['Itemid']);

		if ($menuItem && empty($menuItem->query['view']))
		{
			$menuItem = new StdClass;
			$menuItem->query['view'] = '';
		}

		$view = isset($query['view']) ? $query['view'] : '';
		$id = 	isset($query['id']) ? (int) $query['id'] : 0;
		$task = isset($query['task']) ? $query['task'] : '';

		if($task == ""){
			switch ($view)
			{
				case "lcategory":
					$task = "category_listing";
					break;
				case "lagents":
					$task = "agent_layout";
					break;
				case "lcompanies":
					$task = "company_listing";
					break;
				case "ldefault":
					$task = "default_page";
					break;
				case "lsearch":
					$task = "locator_search";
					break;
				case "aaddproperty":
					$task = "property_new";
					break;
				case "aeditdetails":
					$task = "agent_default";
					break;
				case "rfavoriteproperties":
					$task = "property_favorites";
					break;
				case "ltype":
					$task = "property_type";
					break;
				case "lcity":
					$task = "property_city";
					break;
				case "ccompanydetails":
					$task = "company_edit";
					break;
				case "ladvsearch":
					$task = "property_advsearch";
					break;
				case "rsearchlist":
					$task = "property_searchlist";
					break;
				case "aagentregistration":
					$task = "agent_register";
					break;
				case "rcompare":
					$task = "compare_properties";
					break;
				case "ccompanyregistration":
					$task = "company_register";
					break;
				case "lmembership":
					$task = "membership_listsubscriptions";
					break;
			}
		}
		switch ($task){
			//category
			case "category_listing":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = Text::_('OS_LIST_CATEGORIES');
					unset($query['Itemid']);
				}
				break;
			case "category_details":
				$id = $query['id'];
				$db->setQuery("Select * from #__osrs_categories where id = '$id' ");
				$category = $db->loadObject();
				$category_alias = OSPHelper::getLanguageFieldValue($category,'category_alias');
				$category_name  = OSPHelper::getLanguageFieldValue($category,'category_name');
				if($category_alias != ""){
					$segments[] = $id ."-" .$category_alias;
				}else{
					$segments[] = $id ."-" .$category_name;
				}

				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			//company
			case "company_register":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[] = Text::_('OS_COMPANY_REGISTRATION');
					unset($query['Itemid']);
				}
				break;
			case "company_listing":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_COMPANIES_LISTING');
					unset($query['Itemid']);
				}
				break;
			case "company_info":
				$id = (int)$query['id'];
				$db->setQuery("Select id, company_alias, company_name from #__osrs_companies where id = '$id' ");
				$company = $db->loadObject();
				$company_alias = $company->company_alias;
				$company_name = $company->company_name;
				if($company_alias != ""){
					$segments[] = $company_alias."-".$id;
				}else{
					$segments[] = $company_name."-".$id;
				}

				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "company_listproperties":
				$id = (int)$query['id'];
				$db->setQuery("Select id, company_alias, company_name from #__osrs_companies where id = '$id' ");
				$company = $db->loadObject();
				$company_alias = $company->company_alias;
				$company_name = $company->company_name;
				if($company_alias != ""){
					$company_name = $company_alias;
				}
				$segments[] = Text::_('OS_LIST_PROPERTIES')."-".$company_name;
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "company_edit":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_EDIT_COMPANY');
					unset($query['Itemid']);
				}
				break;

			case "company_agent":
				$segments[]	= Text::_('OS_MANAGE_AGENTS');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			case "company_addnew":
				$segments[]	= Text::_('OS_ASSIGN_AGENTS_TO_COMPANY');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			case "company_properties":
				$segments[]	= Text::_('OS_MANAGE_PROPERTIES');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			case "company_plans":
				$segments[]	= Text::_('OS_MY_PLANS');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			
			case "company_ordershistory":
				$segments[]	= Text::_('OS_MY_ORDERS_HISTORY');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			case "company_addagents":
				$segments[]	= Text::_('OS_ADD_AGENT');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "company_editagent":
				$segments[] = Text::_('OS_MODIFY_AGENT');
				$id = (int)$query['id'];
				$db->setQuery("Select id, alias, name from #__osrs_agents where id = '$id' ");
				$agent = $db->loadObject();
				$agent_alias = $agent->alias;
				$agent_name = $agent->name;
				if($agent_alias != ""){
					$segments[] = $agent_alias;
				}else{
					$segments[] = $agent_name;
				}

				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			//agent
			case "agent_default":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_EDIT_MY_PROFILE');
					unset($query['Itemid']);
				}
				break;
			case "agent_layout":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_LIST_AGENTS');
					unset($query['Itemid']);
				}
				break;
			case "agent_register":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_AGENT_REGISTER');
					unset($query['Itemid']);
				}
				break;
			case "agent_info":
				$id = (int)$query['id'];
				$db->setQuery("Select id, alias, name from #__osrs_agents where id = '$id' ");
				$agent = $db->loadObject();
				$agent_alias = $agent->alias;
				$agent_name = $agent->name;
				if($agent_alias != ""){
					$segments[] = $agent_alias;
				}else{
					$segments[] = $agent_name;
				}

				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "agent_editprofile":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_EDIT_PROFILE');
					unset($query['Itemid']);
				}
				break;
			case "agent_completeregistration":
				$id = (int)$query['id'];
				$segments[]	= Text::_('OS_COMPLETE_REGISTRATION');
				unset($query['id']);
				break;
			//property
			case "property_new":
				$segments[]	= Text::_('OS_ADD_PRO');
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "property_thankyou":
				$id = (int)$query['id'];
				$segments[] = Text::_('OS_ITEM_HAS_BEEN_SAVE');
				$segments[] = $id;
				unset($query['id']);
			break;
			case "agent_unpublishproperties":
				$id = (int)$query['id'];
				$segments[] = Text::_('OS_UNPUBLISH');
				$segments[] = $id;
				unset($query['id']);
			break;
			case "agent_publishproperties":
				$id = (int)$query['id'];
				$segments[] = Text::_('OS_PUBLISH');
				$segments[] = $id;
				unset($query['id']);
			break;
			case "property_details":
				$add_title = 1;
				$id = (int)$query['id'];
				include_once(JPATH_ROOT.'/components/com_osproperty/helpers/route.php');
				if(isset($query['Itemid'])){
					$itemid = $query['Itemid'];
					if(OSPRoute::checkDirectPropertyLink($itemid, $id)){
						$add_title = 0;
					}
				}
				if($add_title == 1)
				{
					include_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
					$default_language = OSPHelper::getDefaultLanguage();
					$default_language = explode("-",$default_language);
					$default_language = $default_language[0];
					$default_language = "_".$default_language;
					$lang = isset($query['l']) ? $query['l'] : "";
					
					if($lang != "")
					{
						if($default_language == $lang)
						{
							$db->setQuery("Select ref,pro_name,pro_alias from #__osrs_properties where id = '$id'");
						}
						else
						{
							$db->setQuery("Select ref,pro_name$lang as pro_name,pro_alias$lang as pro_alias from #__osrs_properties where id = '$id'");
						}
						$property = $db->loadObject();
						$pro_alias  = $property->pro_alias;
						$pro_name   = $property->pro_name;
						if(($pro_alias == "") AND ($pro_name == "")){
							$db->setQuery("Select * from #__osrs_properties where id = '$id'");
							$property   = $db->loadObject();
							$pro_alias  = OSPHelper::getLanguageFieldValue($property,'pro_alias');
							$pro_name   = OSPHelper::getLanguageFieldValue($property,'pro_name');
						}
					}
					else
					{
						$db->setQuery("Select * from #__osrs_properties where id = '$id'");
						$property   = $db->loadObject();
						$pro_alias  = OSPHelper::getLanguageFieldValue($property,'pro_alias');
						$pro_name   = OSPHelper::getLanguageFieldValue($property,'pro_name');
					}
					if($pro_alias != "")
					{
						$segs = $pro_alias;
					}
					else
					{
						$segs = $pro_name;
					}
					if($configClass['sef_configure'] == 1)
					{
						$segs = $property->ref." ".$segs;
					}
					elseif($configClass['sef_configure'] == 2)
					{
						$segs = $property->ref." ".$segs." ".$id;
					}
					$segments[] = $segs;
				}
				if(isset($query['l'])) unset($query['l']);
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "property_city":
				$id = (int)$query['id'];
				if (!isset($query['Itemid'])){
					$db->setQuery("Select id, city from #__osrs_cities where id = '$id'");
					$city = $db->loadObject();
					$segments[] = $city->city;
				} 
				else 
				{
					$app = Factory::getApplication();
					$menu = $app->getMenu();
					$item = $menu->getItem($query['Itemid']);
					$link = $item->link;
					if($link != "")
					{
						if(strpos($link,"view=ltype") !== false)
						{
							$db->setQuery("Select id, city from #__osrs_cities where id = '$id'");
							$city = $db->loadObject();
							$segments[] = $city->city;
						}
					}
					else
					{
						$db->setQuery("Select id, city from #__osrs_cities where id = '$id'");
						$city = $db->loadObject();
						$segments[] = $city->city;
						unset($query['Itemid']);
					}
				}
				unset($query['id']);
				break;
			case "property_favorites":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_MY_FAVORITES');
					unset($query['Itemid']);
				}
				break;
			case "property_tag":
				if(isset($query['tag_id'])){
					$db = Factory::getDbo();
					$db->setQuery("Select keyword from #__osrs_tags where id = '".(int)$query['tag_id']."'");
					$keyword = $db->loadResult();
					$segments[] = Text::_('OS_LIST_PROPERTIES_BY_TAG').' '.$keyword;
				}
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				unset($query['tag_id']);
				break;
			case "property_listing":
			case "property_type":
				//print_r($query);
				$segmentArr = array();
				$allproperty = 1;
				$catIds				= $query['catIds'];
				$type_id			= $query['type_id'];
				$country_id			= $query['country_id'];
				$company_id			= $query['company_id'];
				$state_id			= $query['state_id'];
				$city_id			= $query['city_id'];

				if (!isset($query['Itemid']) || $query['Itemid'] == 0 || $query['Itemid'] == 99999 || $query['Itemid'] == 9999)
				{
					if(isset($query['catIds']) && is_array($query['catIds']) && count($query['catIds']) > 0)
					{
						$query['catIds'] = ArrayHelper::toInteger($query['catIds']);
						$allproperty = 0;
						$db->setQuery("Select id,category_alias,category_name from #__osrs_categories where id in (".implode(",",$query['catIds']).")");
						$categories = $db->loadObjectList();
						foreach ($categories as $category)
						{
							if($category->category_alias != "")
							{
								$category_alias[] = $category->category_alias;
							}
							else
							{
								$category_alias[] = $category->category_name;
							}
						}
						if(count($category_alias) > 0)
						{
							$segmentArr[] = Text::_('OS_CATEGORY')."_".implode("-",$category_alias);
						}
						unset($query['catIds']);
					}
					if(isset($query['type_id']))
					{
						$allproperty = 0;
						$db->setQuery("Select id,type_alias,type_name from #__osrs_types where id = '".(int)$query['type_id']."'");
						$type = $db->loadObject();
						$type_alias = $type->type_alias;
						$type_name = $type->type_name;
						if($type_alias != ""){
							$segmentArr[] = Text::_('OS_TYPE')."_".$type_alias;
						}else{
							$segmentArr[] = Text::_('OS_TYPE')."_".$type_name;
						}
						unset($query['type_id']);
					}
				}
				if (!isset($query['Itemid']) || $query['Itemid'] > 0 || $query['Itemid'] != 99999 || $query['Itemid'] != 9999)
				{
					if(isset($query['catIds']) && is_array($query['catIds']) && count($query['catIds']) > 0)
					{
						$allproperty = 0;
					}
					if(isset($query['type_id']))
					{
						$allproperty = 0;
					}
					if(isset($query['country_id']))
					{
						$allproperty = 0;
					}
					if(isset($query['company_id']))
					{
						$allproperty = 0;
					}
				}
				if(isset($query['company_id']))
				{
					$allproperty = 0;
					$db->setQuery("Select id, company_alias,company_name from #__osrs_companies where id = '".(int)$query['company_id']."'");
					$company = $db->loadObject();
					$company_alias =$company->company_alias;
					$company_name = $company->company_name;
					if($company_alias != ""){
						$segmentArr[] = Text::_('OS_COMPANY')."_".$company_alias;
					}else{
						$segmentArr[] = Text::_('OS_COMPANY')."_".$company_name;
					}
					unset($query['company_id']);
				}
				if(isset($query['country_id']))
				{
					$allproperty = 0;
					$db->setQuery("Select id, country_name from #__osrs_countries where id = '".(int)$query['country_id']."'");
					$country = $db->loadObject();
					$segmentArr[] = Text::_('OS_COUNTRY')."_".$country->country_name;
					unset($query['country_id']);
				}
				if(isset($query['state_id']))
				{
					$allproperty = 0;
					$db->setQuery("Select id, state_name from #__osrs_states where id = '".(int)$query['state_id']."'");
					$state = $db->loadObject();
					$segmentArr[] = Text::_('OS_STATE')."_".$state->state_name;
					unset($query['state_id']);
				}
				if(isset($query['city']))
				{
					$allproperty = 0;
					$db->setQuery("Select id, city from #__osrs_cities where id = '".(int)$query['city']."'");
					$city = $db->loadObject();
					$segmentArr[] = Text::_('OS_CITY')."_".$city->city;
					unset($query['city']);
				}
				if(isset($query['min_price']) && (float) $query['min_price'] > 0)
				{
					$allproperty = 0;
					$segmentArr[] = Text::_('OS_FROM')."_".$query['min_price'];
					unset($query['min_price']);
				}
				if(isset($query['max_price']) && (float) $query['max_price'] > 0)
				{
					$allproperty = 0;
					$segmentArr[] = Text::_('OS_TO')."_".$query['max_price'];
					unset($query['max_price']);
				}
				if($allproperty == 1)
				{
					if (!isset($query['Itemid']) || $query['Itemid'] == 0 || $query['Itemid'] == 99999 || $query['Itemid'] == 9999)
					{
						$segments[] = Text::_('OS_ALL_PROPERTIES');
					}                
				}
				else
				{
					if(isset($query['Itemid']))
					{
						//compare menu Itemid with parameters
						include_once(JPATH_ROOT.'/components/com_osproperty/helpers/route.php');
						if(!OSPRoute::compareMenuItem($query['Itemid'],(array)$catIds,(int)$type_id,(int)$country_id,(int)$company_id,(int)$state_id,(int)$city_id))
						{
							$segments[] = implode(" ".Text::_('OS_AND')." ",$segmentArr);
						}
					}
					else
					{
						$segments[] = implode(" ".Text::_('OS_AND')." ",$segmentArr);
					}
				}

				if (!isset($query['Itemid']) || $query['Itemid'] == 0 || $query['Itemid'] == 99999 || $query['Itemid'] == 9999)
				{
					unset($query['Itemid']);
				}
				unset($query['type_id']);
				unset($query['catIds']);
				unset($query['category']);
				unset($query['company_id']);
				unset($query['country_id']);
				unset($query['state_id']);
				unset($query['city']);
				break;
			case "property_edit":
				$id = (int)$query['id'];
				$db->setQuery("Select id, pro_alias, pro_name from #__osrs_properties where id = '$id'");
				$property   = $db->loadObject();
				$pro_alias  = $property->pro_alias;
				$pro_name   = $property->pro_name;

				if($pro_alias != ""){
					$segs = Text::_('OS_EDIT_PROPERTY')."-".$pro_alias;
				}else{
					$segs = Text::_('OS_EDIT_PROPERTY')."-".$pro_name;
				}
				$segments[] = $segs;
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "property_edit_activelisting":
				$id = (int)$query['id'];
				$db->setQuery("Select id, pro_alias, pro_name from #__osrs_properties where id = '$id'");
				$property   = $db->loadObject();
				$pro_alias  = $property->pro_alias;
				$pro_name   = $property->pro_name;

				if($pro_alias != ""){
					$segs = Text::_('OS_ACTIVE_LISTING')."-".$pro_alias;
				}else{
					$segs = Text::_('OS_ACTIVE_LISTING')."-".$pro_name;
				}
				$segments[] = $segs;
				if(isset($query['new'])){
					if($query['new'] == 1){
						$segments[] = Text::_('OS_NEW');
					}else{
						$segments[] = Text::_('OS_UPDATE');
					}
					unset($query['new']);
				}
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "membership_activelisting":
				$id = (int)$query['id'];
				$db->setQuery("Select id, pro_alias, pro_name from #__osrs_properties where id = '$id'");
				$property   = $db->loadObject();
				$pro_alias  = $property->pro_alias;
				$pro_name   = $property->pro_name;

				if($pro_alias != ""){
					$segs = Text::_('OS_ACTIVE_LISTING_MEMBERSHIP')."-".$pro_alias;
				}else{
					$segs = Text::_('OS_ACTIVE_LISTING_MEMBERSHIP')."-".$pro_name;
				}
				$segments[] = $segs;
				if(isset($query['new'])){
					if($query['new'] == 1){
						$segments[] = Text::_('OS_NEW');
					}else{
						$segments[] = Text::_('OS_UPDATE');
					}
					unset($query['new']);
				}
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "property_stas":
				$id = (int)$query['id'];
				$db->setQuery("Select id, pro_alias, pro_name from #__osrs_properties where id = '$id'");
				$property   = $db->loadObject();
				$pro_alias  = $property->pro_alias;
				$pro_name   = $property->pro_name;

				if($pro_alias != ""){
					$segs = $id."-".$pro_alias;
				}else{
					$segs = $id."-".$pro_name;
				}
				$segments[] = $segs;
				unset($query['id']);
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;

			//compare properties
			case "compare_layout":
			case "compare_properties":
			case "compare_list":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_COMPARE_PROPERTIES');
					unset($query['Itemid']);
				}
				break;
			//advanced search
			case "property_advsearch":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_ADV_SEARCH');
					unset($query['Itemid']);
				}else{
					$app = Factory::getApplication();
					$menu = $app->getMenu();
					$item = $menu->getItem($query['Itemid']);
					$link = $item->link;
					if((strpos($link,"view=ladvsearch") !== false) or (strpos($link,"task=property_advsearch") !== false)){
						//do nothing
						//echo "1";die();
					}else{
						//echo "2";die();
						$segments[]	= Text::_('OS_ADV_SEARCH');
						//unset($query['Itemid']);
					}
				}
				if(isset($query['adv_type'])){
					$db->setQuery("Select * from #__osrs_types where id = '".(int)$query['adv_type']."'");
					$type = $db->loadObject();
					$type_alias = OSPHelper::getLanguageFieldValue($type,'type_alias');
					$type_name = OSPHelper::getLanguageFieldValue($type,'type_name');
					if($type_alias != ""){
						$segments[] = "adv_".$type_alias;
					}else{
						$segments[] = "adv_".$type_name;
					}
					unset($query['adv_type']);
				}
				if(isset($query['keyword'])){
					$segments[] = Text::_('OS_KEYWORD').' '.$query['keyword'];
					unset($query['keyword']);
				}
				break;
			case "locator_search":
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					$segments[]	= Text::_('OS_LOCATOR_SEARCHING');
					unset($query['Itemid']);
				}
				if(isset($query['locator_type'])){
					$db->setQuery("Select id,type_alias,type_name from #__osrs_types where id = '".(int)$query['locator_type']."'");
					$type = $db->loadObject();
					$type_alias = $type->type_alias;
					$type_name = $type->type_name;
					if($type_alias != ""){
						$segments[] = Text::_('OS_LOCATOR_SEARCHING')."-".$type_alias;
					}else{
						$segments[] = Text::_('OS_LOCATOR_SEARCHING')."-".$type_name;
					}
					unset($query['locator_type']);
				}
				break;

			case "property_upgrade":
					$segments[]	= Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE');
					if(isset($query['cid'])){
						$query['cid'] = ArrayHelper::toInteger($query['cid']);
						$cids = implode(",",$query['cid']);
						$db->setQuery("Select pro_name from #__osrs_properties where id in (".$cids.")");
						$properties = $db->loadColumn(0);
						$properties = implode("_",$properties);
						$segments[] = $properties;
						unset($query['cid']);
					}
				break;
			case "property_paymentprocess":
					$segments[]	= Text::_('OS_PROCESS_PAYMENT');
				break;
				
			case "direction_map":
				$segments[]	= Text::_('OS_GET_DIRECTIONS');
				$id = (int)$query['id'];
				$db->setQuery("Select id, pro_alias, pro_name from #__osrs_properties where id = '$id'");
				$property   = $db->loadObject();
				$pro_alias  = $property->pro_alias;
				$pro_name   = $property->pro_name;

				if($pro_alias != ""){
					$segs = $pro_alias;
				}else{
					$segs = $pro_name;
				}
				$segments[] = $segs;
				if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
					unset($query['Itemid']);
				}
				break;
			case "property_manageallproperties":
					$segments[]	= Text::_('OS_MANAGE_ALL_PROPERTIES');
					if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
						unset($query['Itemid']);
					}
				break;
			case "membership_listsubscriptions":
					$segments[]	= Text::_('OS_PURCHASE_SUBSCRIPTION');
					if (!isset($query['Itemid']) or ($query['Itemid'] == 0) or ($query['Itemid'] == 99999) or ($query['Itemid'] == 9999)){
						unset($query['Itemid']);
					}
				break;
			case "payment_return":
					$segments[]	= Text::_('OS_PAYMENT_COMPLETED');
					$segments[]	= $query['order_id'];
					unset($query['order_id']);
				break;
		}

		if (isset($query['start']) || isset($query['limitstart']))
		{
			$limit = $configClass['general_number_properties_per_page'];
			$limitStart = isset($query['limitstart']) ? (int)$query['limitstart'] : (int)$query['start'];
			$page = ceil(((int)$limitStart + 1) / (int)$limit);
			if($page > 1)
			{
				$segments[] = Text::_('OS_PAGE').'-'.$page;
			}
		}

		if (isset($query['task']))
			unset($query['task']);

		if (isset($query['view']))
			unset($query['view']);

		if (isset($query['id']))
			unset($query['id']);

		if (isset($query['category_id']))
			unset($query['category_id']);

		if (isset($query['type_id']))
			unset($query['type_id']);

		if (isset($query['country_id']))
			unset($query['country_id']);

		if (isset($query['state_id']))
			unset($query['state_id']);

		if (isset($query['layout']))
			unset($query['layout']);

		if (isset($query['start']))
			unset($query['start']);

		if (isset($query['limitstart']))
			unset($query['limitstart']);


		if (count($segments))
		{
			$segments = array_map('JApplicationHelper::stringURLSafe', $segments);
			$key = md5(implode('/', $segments));
			$q = $db->getQuery(true);
			$q->select('COUNT(id)')
				->from('#__osrs_urls')
				->where('md5_key like "'.$key.'"');
			$db->setQuery($q);
			$total = $db->loadResult();
			if ((int)$total == 0)
			{
				$db->setQuery("Insert into #__osrs_urls (`id`,`md5_key`,`query`) values (NULL, '".$key."', '".$queryString."')");
				$db->execute();
			}
		}
		return $segments;
	}

	/**
	 *
	 * Parse the segments of a URL.
	 * @param	array	The segments of the URL to parse.
	 * @return	array	The URL attributes to be used by the application.
	 * @since	1.5
	 */
	function parse(& $segments)
	{
		$vars = array();
		if (count($segments))
		{
			$db = Factory::getDbo();
			$key = md5(str_replace(':', '-', implode('/', $segments)));
			$query = $db->getQuery(true);
			$query->select('`query`')
				->from('#__osrs_urls')
				->where('md5_key="'.$key.'"');
			$db->setQuery($query);
			$queryString = $db->loadResult();
			if ($queryString)
			{
				parse_str(html_entity_decode($queryString), $vars);
			}
			else
			{
				$method = strtoupper(Factory::getApplication()->input->getMethod());
				if ($method == 'GET')
				{
					throw new Exception('Page not found', 404);
				}
			}

			if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
			{
				$segments = [];
			}
		}

		$app		= Factory::getApplication();
		$menu		= $app->getMenu();
		if ($item = $menu->getActive())
		{
			foreach ($item->query as $key=>$value)
			{
				if ($key != 'option' && $key != 'Itemid' && !isset($vars[$key]))
					$vars[$key] = $value;
			}
		}
		return $vars;
	}
}

/**
 * OS Property router functions
 *
 * These functions are proxies for the new router interface
 * for old SEF extensions.
 *
 * @param   array &$query An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 */
function OspropertyBuildRoute(& $query)
{
	$router = new OspropertyRouter();

	return $router->build($query);
}

function OspropertyParseRoute(& $segments)
{
	$router = new OspropertyRouter();

	return $router->parse($segments);
}
