<?php
/*------------------------------------------------------------------------
# listing.php - Ossolution Property
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
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Mail\MailHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;


class OspropertyListing
{
	static function advSearch($option)
	{
		global $bootstrapHelper, $mainframe, $configClass, $lang_suffix, $jinput, $bootstrapHelper;
		$session = Factory::getSession();
		$db = Factory::getDbo();
		$prefix_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
		$url = $prefix_url . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


		$user = Factory::getUser();
		$agent_id_permission = 0;
		if ($user->id > 0) {
			if (HelperOspropertyCommon::isAgent()) {
				$agent_id_permission = HelperOspropertyCommon::getAgentID();
			}
		}
		//condition in config
		//check to see if OS Calendar intergrated
		if (file_exists(JPATH_ROOT . "/components/com_oscalendar/oscalendar.php")) {
			if ($configClass['integrate_oscalendar'] == 1) {
				$start_time		= $db->escape($jinput->getString('start_time', ''));
				$end_time		= $db->escape($jinput->get('end_time', ''));
				if ($start_time != "") {
					@setcookie('start_time', $start_time, time() + 3600, '/');
				} else {
					@setcookie('start_time', '', time() - 3600, '/');
				}
				if ($end_time != "") {
					@setcookie('end_time', $end_time, time() + 3600, '/');
				} else {
					@setcookie('start_time', '', time() - 3600, '/');
				}
			}
		}

		OSPHelper::generateHeading(1, $configClass['general_bussiness_name'] . " - " . Text::_('OS_ADVSEARCH'));
		$dosearch  		= 0;
		$list = $jinput->getInt('list', 0);
		if ($list == 1) {
			$temp_category_ids = [];
			$temp_type_ids	   = [];
			$temp_amen_ids     = [];
			$list_id = $jinput->getInt('list_id', 0);
			$db->setQuery("Select * from #__osrs_user_list_details where list_id = '$list_id'");
			$list_details = $db->loadObjectList();
			for ($i = 0; $i < count($list_details); $i++) {
				$list_detail = $list_details[$i];
				switch ($list_detail->field_id) {
					case "keyword":
						$jinput->set('keyword', $list_detail->search_param);
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
						//JRequest::setVar('property_type',$list_detail->search_param);
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
					case "radius":
						$jinput->set('radius_search', $list_detail->search_param);
						break;
					case "postcode":
						$jinput->set('postcode', $list_detail->search_param);
						break;
					case "created_from":
						$jinput->set('created_from', $list_detail->search_param);
						break;
					case "created_to":
						$jinput->set('created_to', $list_detail->search_param);
						break;
					case "se_geoloc":
						$jinput->set('se_geoloc', $list_detail->search_param);
						break;
					default:
						HelperOspropertyFields::setFieldValue($list_detail);
						break;
				}
			}

			if (count($temp_category_ids) > 0) {
				$jinput->set('category_ids', $temp_category_ids);
			}
			if (count($temp_type_ids) > 0) {
				$jinput->set('property_types', $temp_type_ids);
			}
			if (count($temp_amen_ids) > 0) {
				$jinput->set('amenities', $temp_amen_ids);
			}
		}

		$adv_type_ids = $configClass['adv_type_ids'];
		if (($adv_type_ids != "") && ($adv_type_ids != "0")) {
			$adv_type_ids = explode("|", $adv_type_ids);
			if (count($adv_type_ids) > 0) {
				$adv_type_ids = $adv_type_ids[0];
			}
		}

		$property_type	= $jinput->getInt('property_type', 0);
		if ($property_type > 0) {
			$adv_type		= $jinput->getInt('adv_type', $property_type);
		} else {
			$adv_type		= $jinput->getInt('adv_type', $adv_type_ids);
		}

		if ($adv_type > 0) {
			$adv_type_ids = $adv_type;
		}

		$property_types	= [];
		$property_types	= $jinput->get('property_types', array(), 'ARRAY');
		$property_types	= ArrayHelper::toInteger($property_types);
		if (!in_array($property_type, $property_types) && $property_type > 0) {
			$property_types[count($property_types)] = $property_type;
		}

		if (count($property_types) == 0 && $adv_type_ids > 0) {
			$property_types[] = $adv_type_ids;
		}


		$category_id 	= $jinput->getInt('category_id', 0);
		$category_ids	=  $jinput->get('category_ids', array(), 'ARRAY');
		$category_ids	= ArrayHelper::toInteger($category_ids);
		if (!in_array($category_id, $category_ids)) {
			$category_ids[count($category_ids)] = $category_id;
		}

		$agent_type		= $jinput->getInt('agent_type', -1);
		$country_id		= $jinput->getInt('country_id', HelperOspropertyCommon::getDefaultCountry());
		$city			= $jinput->getInt('city', 0);
		$state_id		= $jinput->getInt('state_id', 0);
		$nbed			= $jinput->getInt('nbed', 0);
		$nbath			= $jinput->getInt('nbath', 0);
		$price			= $jinput->getInt('price', 0);
		$nroom 			= $jinput->getInt('nroom', 0);
		$nfloors		= $jinput->getInt('nfloors', 0);
		$address		= OSPHelper::getStringRequest('address', '', 'get');
		if ($address == "") {
			$address		= OSPHelper::getStringRequest('address', '', 'post');
		}
		$address		= $db->escape($address);
		$keyword		= OSPHelper::getStringRequest('keyword', '', 'get'); //JRequest::getVar('keyword','','','string');
		if ($keyword == "") {
			$keyword		= OSPHelper::getStringRequest('keyword', '', 'post');
		}
		$keyword		= $db->escape($keyword);
		$isFeatured		= $jinput->getInt('isFeatured', 0);
		$isSold			= $jinput->getInt('isSold', 0);
		$sortby			= OSPHelper::getStringRequest('sortby', $configClass['adv_sortby']);
		$orderby		= OSPHelper::getStringRequest('orderby', $configClass['adv_orderby']);
		$orderbyArray   = array('a.pro_name', 'a.created', 'a.modified', 'a.price', 'a.isFeatured', 'a.square_feet');
		if ($configClass['show_ref'] == 1) {
			$orderbyArray[] = 'a.ref';
		}
		if (!in_array($sortby, $orderbyArray)) {
			$sortby = "a.created";
			$orderby = "desc";
		}
		$min_price		= $jinput->getInt('min_price', 0);
		$max_price   	= $jinput->getInt('max_price', 0);
		$sqft_min		= $jinput->getInt('sqft_min', 0);
		$sqft_max		= $jinput->getInt('sqft_max', 0);
		$lotsize_min	= $jinput->getInt('lotsize_min', 0);
		$lotsize_max	= $jinput->getInt('lotsize_max', 0);
		$postcode		= OSPHelper::getStringRequest('postcode', '');
		$se_geoloc      = $jinput->getInt('se_geoloc', 0);
		$radius			= $jinput->getInt('radius_search', 50);
		$created_from	= $jinput->getString('created_from', '');
		$created_to		= $jinput->getString('created_to', '');

		$amenities		= $jinput->get('amenities', array(), 'ARRAY');
		$amenities		= ArrayHelper::toInteger($amenities);
		if (count($amenities) > 0) {

			$amenities_str = implode(",", $amenities);

			if ($amenities_str != "") {
				$amenities_sql = " AND a.id in (SELECT pro_id FROM #__osrs_property_amenities WHERE amen_id in ($amenities_str) group by pro_id having count(pro_id) = " . count($amenities) . ")";
				$dosearch = 1;
			} else {
				$amenities_sql = "";
			}
		} else {
			$amenities_sql = "";
		}
		$limitstart		= $jinput->getInt('limitstart', 0);
		$limit			= $jinput->getInt('limit', $configClass['general_number_properties_per_page']);

		$lists['address_value'] = $address;
		$lists['keyword_value'] = $keyword;
		$param = [];
		if ($address != "") {
			$dosearch = 1;
			$param[] = "add:" . $address;
		}
		if ($keyword != "") {
			$dosearch = 1;
			$param[] = "keyword:" . $keyword;
		}
		if ($category_id > 0) {
			$dosearch = 1;
			$param[] = "catid:" . $category_id;
		}
		if (count($category_ids) > 0) {
			foreach ($category_ids as $cat_id) {
				if ($cat_id > 0) {
					$dosearch = 1;
					$param[] = "catid:" . $cat_id;
				}
			}
		}
		$types = [];
		if ($property_type > 0) {
			$dosearch = 1;
			$param[] = "type:" . $property_type;
			$types[] = $property_type;
		}
		if (count($property_types) > 0) {
			foreach ($property_types as $type_id) {
				if ($type_id > 0) {
					$dosearch = 1;
					$param[] = "type:" . $type_id;
					$types[] = $type_id;
				}
			}
		}
		if (($country_id > 0) and (HelperOspropertyCommon::checkCountry())) {
			$dosearch = 1;
			$param[] = "country:" . $country_id;
		} else {
			//$dosearch = 0;
			$param[] = "country:" . $country_id;
		}
		if ($city > 0) {
			$dosearch = 1;
			$param[] = "city:" . $city;
		}
		if ($state_id > 0) {
			$dosearch = 1;
			$param[] = "state:" . $state_id;
		}
		if ($nbath > 0) {
			$dosearch = 1;
			$param[] = "nbath:" . $nbath;
		}
		if ($nbed > 0) {
			$dosearch = 1;
			$param[] = "nbed:" . $nbed;
		}
		if ($nroom > 0) {
			$dosearch = 1;
			$param[] = "nroom:" . $nroom;
		}
		if ($nfloors > 0) {
			$dosearch = 1;
			$param[] = "nfloors:" . $nfloors;
		}
		if ($price > 0) {
			$dosearch = 1;
			$param[] = "price:" . $price;
		}
		if ($min_price > 0) {
			$dosearch = 1;
			$param[] = "min_price:" . $min_price;
		}
		if ($max_price > 0) {
			$dosearch = 1;
			$param[] = "max_price:" . $max_price;
		}
		if ($sqft_min > 0) {
			$dosearch = 1;
			$param[] = "sqft_min:" . $sqft_min;
		}
		if ($sqft_max > 0) {
			$dosearch = 1;
			$param[] = "sqft_max:" . $sqft_max;
		}
		if ($lotsize_min > 0) {
			$dosearch = 1;
			$param[] = "lotsize_min:" . $lotsize_min;
		}
		if ($lotsize_max > 0) {
			$dosearch = 1;
			$param[] = "lotsize_max:" . $lotsize_max;
		}
		if ($isFeatured == 1) {
			$dosearch = 1;
			$param[] = "featured:" . $isFeatured;
		}
		if ($isSold > 0) {
			$dosearch = 1;
			$param[] = "sold:" . $isSold;
		}
		if ($agent_type >= 0) {
			$dosearch = 1;
			$param[] = "agent_type:" . $agent_type;
		}
		if ($created_from != "") {
			$dosearch = 1;
			$param[] = "created_from:" . $created_from;
		}
		if ($created_to != "") {
			$dosearch = 1;
			$param[] = "created_to:" . $created_to;
		}
		$search_radius_v = "";
		$search_radius_h = "";
		if ($configClass['locator_radius_type'] == 0) {
			$radius_unit_v = 3959;
		} else {
			$radius_unit_v = 6371;
		}
		if ($se_geoloc == 1) {
			$param[] = "mylocation:" . $se_geoloc;
			$dosearch = 1;
			$user_latlog = explode('_', $_COOKIE["djcf_latlon"]);
			$search_radius_v = ', ( ' . $radius_unit_v . ' * acos( cos( radians(' . $user_latlog[0] . ') ) * cos( radians( a.lat_add ) ) * cos( radians( a.long_add ) - radians(' . $user_latlog[1] . ') ) + sin( radians(' . $user_latlog[0] . ') ) * sin( radians( a.lat_add ) ) ) ) AS distance ';
			$search_radius_h = ' HAVING distance < ' . $radius . ' ';
			$lists['se_geoloc'] = $se_geoloc;
		} elseif ($postcode != "") {
			$lists['postcode'] = $postcode;
			$param[] = "postcode:" . $agent_type;
			$dosearch = 1;
			$geocode = HelperOspropertyGoogleMap::getLocationPostCode($postcode, $country_id);
			if ($geocode) {
				$search_radius_v = ', ( ' . $radius_unit_v . ' * acos( cos( radians(' . $geocode['lat'] . ') ) * cos( radians( a.lat_add ) ) * cos( radians( a.long_add ) - radians(' . $geocode['lng'] . ') ) + sin( radians(' . $geocode['lat'] . ') ) * sin( radians( a.lat_add ) ) ) ) AS distance ';
				$search_radius_h = ' HAVING distance < ' . $radius . ' ';
			} else {
				Factory::getApplication()->enqueueMessage(Text::_('OS_SORRY_WE_CANT_FIND_COORDINATES_FROM_POSTCODE_WE_OMIITED_RANGE_RESTRICTION'), 'notice');
			}
		}

		$param[] = "sortby:" . $sortby;
		$param[] = "orderby:" . $orderby;

		$lists['show_date_range'] = 0;
		$rangeDateQuery = "";
		if ($configClass['integrate_oscalendar'] == 1) {
			if (file_exists(JPATH_ROOT . "/components/com_oscalendar/oscalendar.php")) {
				if (($start_time != "") and ($end_time != "")) {
					$dosearch = 1;
					$rangeDateQuery = OSCHelper::buildDateRangeQuery($start_time, $end_time);
				}
			}
		}
		//checked do search through extra field
		//get the list of the field groups
		$access_sql = ' and access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$groups = $db->loadObjectList();
		if (count($groups) > 0) {
			$extrafieldSql = [];
			for ($i = 0; $i < count($groups); $i++) {
				$group = $groups[$i];
				$extraSql = "";
				if (count($types) > 0) {
					$extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id in (" . implode(",", $types) . ")) ";
				} elseif ($adv_type > 0) {
					$extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id = '$adv_type')";
				}
				$db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group->id' $extraSql and published = '1' and searchable = '1' $access_sql order by ordering");
				//echo $db->getQuery();
				$fields = $db->loadObjectList();
				$group->fields = $fields;
				if (count($fields) > 0) {
					for ($j = 0; $j < count($fields); $j++) {
						$field = $fields[$j];
						//check do search
						$check = HelperOspropertyFields::checkField($field);
						if ($check) {
							$dosearch = 1;
							$sql = HelperOspropertyFields::buildQuery($field);
							if ($sql != "") {
								$extrafieldSql[] = $sql;
								$param[]		 = HelperOspropertyFields::getFieldParam($field);
							}
						}
					}
				}
			}
		}
		//build query for searching
		if ($dosearch == 1) {
			// Query database
			$select = "SELECT distinct a.id " . $search_radius_v . ", a.*, c.name as agent_name, c.mobile as agent_mobile, c.phone as agent_phone,c.photo as agent_photo, c.optin, d.id as type_id,d.type_name$lang_suffix as type_name, e.country_name";
			//$count  = "SELECT count(a.id) ".$search_radius_v;
			$from =	 " FROM #__osrs_properties as a"
				. " INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				. " INNER JOIN #__osrs_types as d on d.id = a.pro_type"
				. " INNER JOIN #__osrs_states as g on g.id = a.state"
				. " LEFT JOIN #__osrs_cities as h on h.id = a.city"
				. " LEFT JOIN #__osrs_property_categories as i on i.pid = a.id"
				. " LEFT JOIN #__osrs_countries as e on e.id = a.country";
			$where = " WHERE a.published = '1' AND a.approved = '1' ";
			if ($agent_id_permission > 0) {
				$where .= ' and ((a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')) or (a.agent_id = "' . $agent_id_permission . '"))';
			} else {
				$where .= ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
			}
			if ($sortby == "a.isFeatured") {
				$Order_by = " ORDER BY $sortby $orderby,a.created desc";
			} else {
				$Order_by = " ORDER BY $sortby $orderby";
			}

			if ($isFeatured == 1) {
				$where .= " AND a.isFeatured = '1'";
			}

			if (($configClass['active_market_status'] == 1) && ($isSold > 0)) {
				switch ($isSold) {
					case "1":
					case "2":
					case "3":
						$where .= " AND a.isSold = '$isSold'";
						break;
					case "4":
					case "5":
					case "6":
						$where .= " AND a.isSold <> '$isSold'";
						break;
				}
			}

			if ($address != "") {
				$address = str_replace(";", "", $address);
				if (strpos($address, ",")) {
					$addressArr = explode(",", $address);
					if (count($addressArr) > 0) {
						$where .= " AND (";
						foreach ($addressArr as $address_item) {
							$where .= " a.ref like '%$address_item%' OR";
							$where .= " a.pro_name$lang_suffix like '%$address_item%' OR";
							$where .= " a.address like '%$address_item%' OR";
							$where .= " a.region like '%$address_item%' OR";
							$where .= " a.postcode like '%$address_item%' OR";
							$where .= " g.state_name$lang_suffix like '%$address_item%' OR";
							$where .= " h.city$lang_suffix like '%$address_item%' OR";
						}
						$where = substr($where, 0, strlen($where) - 2);
						$where .= " )";
					}
				} else {
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

			if ($keyword != "") {
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
			if (count($category_ids) >  0) {
				$categoryArr = [];
				foreach ($category_ids as $category_id) {
					if ($category_id > 0) {
						$categoryArr = HelperOspropertyCommon::getSubCategories($category_id, $categoryArr);
						$no_search = false;
					}
				}
				$temp = [];
				if (count($categoryArr) > 0) {
					foreach ($categoryArr as $cat_id) {
						$temp[] = " i.category_id = '$cat_id'";
					}
					$tempStr = implode(" or ", $temp);
					$where .= " AND (" . $tempStr . ")";
				}
			}

			if (count($property_types) >  0) {
				$no_search = false;
				//$type_ids = implode(",",$property_types);
				$tempArr = [];
				foreach ($property_types as $type_id) {
					if ($type_id > 0) {
						$tempArr[] = "$type_id";
					}
				}
				if (count($tempArr) > 0) {
					$temp_sql = implode(",", $tempArr);
					$where .= " AND a.pro_type in (" . $temp_sql . ")";
				}
			}

			if ($country_id > 0) {
				$where .= " AND a.country = '$country_id'";
				$no_search = false;
			}
			if ($city > 0) {
				$where .= " AND a.city = '$city'";
				$no_search = false;
			}
			if ($state_id > 0) {
				$where .= " AND a.state = '$state_id'";
				$no_search = false;
			}
			if ($nbed > 0) {
				$where .= " AND a.bed_room >= '$nbed'";
				$no_search = false;
			}
			if ($nbath > 0) {
				$where .= " AND a.bath_room >= '$nbath'";
				$no_search = false;
			}
			if ($nroom > 0) {
				$where .= " AND a.rooms >= '$nroom'";
				$no_search = false;
			}
			if ($nfloors > 0) {
				$where .= " AND a.number_of_floors >= '$nfloors'";
				$no_search = false;
			}
			if ($agent_type >= 0) {
				$where .= " AND c.agent_type = '$agent_type'";
				$no_search = false;
			}

			if ($price > 0) {
				$db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
				$pricegroup = $db->loadObject();
				$price_from = $pricegroup->price_from;
				$price_to	= $pricegroup->price_to;
				if ($price_from  > 0) {
					$where .= " AND (a.price >= '$price_from')";
				}
				if ($price_to > 0) {
					$where .= " AND (a.price <= '$price_to')";
				}
				$no_search = false;
			}

			if ($min_price > 0) {
				$where .= " AND a.price >= '$min_price'";
			}
			if ($max_price > 0) {
				$where .= " AND a.price <= '$max_price'";
			}
			if ($sqft_min > 0) {
				$where .= " AND a.square_feet >= '$sqft_min'";
				$lists['sqft_min'] = $sqft_min;
			}
			if ($sqft_max > 0) {
				$where .= " AND a.square_feet <= '$sqft_max'";
				$lists['sqft_max'] = $sqft_max;
			}
			if ($lotsize_min > 0) {
				$where .= " AND a.lot_size >= '$lotsize_min'";
				$lists['lotsize_min'] = $lotsize_min;
			}
			if ($lotsize_max > 0) {
				$where .= " AND a.lot_size <= '$lotsize_max'";
				$lists['lotsize_max'] = $lotsize_max;
			}
			if ((isset($extrafieldSql)) and (count($extrafieldSql)) > 0) {
				$extrafieldSql = implode(" AND ", $extrafieldSql);
				if (trim($extrafieldSql) != "") {
					$where .= " AND " . $extrafieldSql;
				}
			}
			if ($created_from != "") {
				$lists['created_from'] = $created_from;
				$where .= " and a.created >= '$created_from'";
			}
			if ($created_to != "") {
				$lists['created_to'] = $created_to;
				$where .= " and a.created <= '$created_to'";
			}
			$where .= $amenities_sql;

			$where .= $rangeDateQuery;
			$where .= $search_radius_h;
			$query = "SELECT count(a.id) FROM (SELECT distinct a.id " . $search_radius_v . " FROM #__osrs_properties a "
				. " INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				. " INNER JOIN #__osrs_types as d on d.id = a.pro_type"
				. " INNER JOIN #__osrs_states as g on g.id = a.state"
				. " LEFT JOIN #__osrs_cities as h on h.id = a.city"
				. " LEFT JOIN #__osrs_property_categories as i on i.pid = a.id"
				. " LEFT JOIN #__osrs_countries as e on e.id = a.country"
				. $where
				. " ) as a ";
			//echo $query;die();
			$db->setQuery($query);
			//echo $db->getQuery();
			$total = $db->loadResult();
			$pageNav = new OSPJPagination($total, $limitstart, $limit);
			$view_type_cookie = $jinput->getString('listviewtype', '');
			if ($view_type_cookie == "") {
				$view_type_cookie = $_COOKIE['viewtypecookie'];
			}
			if ($view_type_cookie == 2) {
				$db->setQuery("Select * from #__osrs_themes where published = '1'");
				$theme = $db->loadObject();
				$themename = ($theme->name != "") ? $theme->name : "default";
				$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
				$themeobj = $db->loadObject();
				$params = $themeobj->params;
				$params = new Registry($params);
				$max_properties_google_map = $params->get('max_properties_map', 50);
				$db->setQuery($select . ' ' . $from . ' ' . $where . ' ' . $Order_by, 0, $max_properties_google_map);
			} else {
				$select_session = "Select distinct a.id" . $search_radius_v;
				$db->setQuery($select_session . ' ' . $from . ' ' . $where . ' ' . $Order_by);
				//echo $db->getQuery();
				$sessionObjs = $db->loadColumn(0);
				//print_r($sessionObjs);
				if (count($sessionObjs) > 0) {
					$session->set('advsearchresult', $sessionObjs);
				} else {
					$sessionVar = [];
					$session->set('advsearchresult', $sessionVar);
				}
				//print_r($session->get('advsearchresult'));
				$db->setQuery($select . ' ' . $from . ' ' . $where . ' ' . $Order_by, $pageNav->limitstart, $pageNav->limit);
				//echo "<BR />";
			}
			$rows = $db->loadObjectList();
			$session->set('advurl', $url);
			if (count($rows) > 0) {
				$fields = HelperOspropertyCommon::getExtrafieldInList();
				//get the list of extra fields that show at the list
				for ($i = 0; $i < count($rows); $i++) { //for
					$row = $rows[$i];
					$pro_name = OSPHelper::getLanguageFieldValue($row, 'pro_name');
					$row->pro_name = $pro_name;

					$pro_small_desc = OSPHelper::getLanguageFieldValue($row, 'pro_small_desc');
					$row->pro_small_desc = $pro_small_desc;

					$alias = $row->pro_alias;
					$new_alias = OSPHelper::generateAlias('property', $row->id, $row->pro_alias);
					if ($alias != $new_alias) {
						$db->setQuery("Update #__osrs_properties set pro_alias = '$new_alias' where id = '$row->id'");
						$db->execute();
					}

					$category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($row->id);
					$row->category_name = $category_name;

					$category_name = OSPHelper::getCategoryNamesOfProperty($row->id);
					$category_nameArr = explode(" ", $category_name);
					$row->category_name_short = "";
					//echo count($category_nameArr);
					//echo "<BR />";
					if (count($category_nameArr) > 4) {
						for ($j = 0; $j < 4; $j++) {
							$row->category_name_short .= $category_nameArr[$j] . " ";
						}
						$row->category_name_short .= "...";
						//echo $row->category_name;
					} else {
						$row->category_name_short = $category_name;
					}

					$query = $db->getQuery(true);
					$query->select("*")->from("#__osrs_property_open")->where("pid='" . $row->id . "' and end_to > '" . date("Y-m-d H:i:s", time()) . "'")->order("start_from");
					$db->setQuery($query);
					$openInformation = $db->loadObjectList();
					$row->openInformation = $openInformation;

					if ($row->number_votes > 0) {
						$rate = round($row->total_points / $row->number_votes, 2);
						if ($rate <= 1) {
							$row->cmd = Text::_('OS_POOR');
						} elseif ($rate <= 2) {
							$row->cmd = Text::_('OS_BAD');
						} elseif ($rate <= 3) {
							$row->cmd = Text::_('OS_AVERGATE');
						} elseif ($rate <= 4) {
							$row->cmd = Text::_('OS_GOOD');
						} elseif ($rate <= 5) {
							$row->cmd = Text::_('OS_EXCELLENT');
						}
						$row->rate = $rate;
					} else {
						$row->rate = '';
						$row->cmd  = Text::_('OS_NOT_SET');
					}

					$db->setQuery("Select * from #__osrs_comments where pro_id = '$row->id' and published = '1' order by created_on desc");
					$row->commentObject = $db->loadObject();

					//get field data
					if (count($fields) > 0) {
						$fieldArr = [];
						$k 		  = 0;
						for ($j = 0; $j < count($fields); $j++) {
							$field = $fields[$j];
							if (OSPHelper::checkFieldWithPropertType($field->id, $row->id)) {
								$value = HelperOspropertyFieldsPrint::showField($field, $row->id);
								if ($value != "") {
									$tmp				= new stdClass();
									if ($field->displaytitle == 1) {
										$tmp->label		= OSPHelper::getLanguageFieldValue($field, 'field_label');
									}
									$tmp->fieldvalue	= $value;
									$fieldArr[$k]		= $tmp;
									$k++;
								}
							}
						}
						$row->fieldarr = $fieldArr;
					}
					//process photo
					$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
					$count = $db->loadResult();
					if ($count > 0) {
						$row->count_photo = $count;
						$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
						$picture = $db->loadResult();
						if ($picture != "") {

							if (file_exists(JPATH_ROOT . '/images/osproperty/properties/' . $row->id . '/medium/' . $picture)) {
								$row->photo = Uri::root() . 'images/osproperty/properties/' . $row->id . '/medium/' . $picture;
							} else {
								$row->photo = Uri::root() . "/media/com_osproperty/assets/images/nopropertyphoto.png";
							}
						} else {
							$row->photo = Uri::root() . "/media/com_osproperty/assets/images/nopropertyphoto.png";
						}
					} else {
						$row->count_photo = 0;
						$row->photo = $row->photo = Uri::root() . "/media/com_osproperty/assets/images/nopropertyphoto.png";;
					} //end photo

					$count = 0;
					if ($row->count_photo > 0) {
						$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id'");
						$photos = $db->loadObjectList();
						$photoArr = [];
						for ($j = 0; $j < count($photos); $j++) {
							$photoArr[$j] = $photos[$j]->image;
							if (file_exists(JPATH_ROOT . '/images/osproperty/properties/' . $row->id . '/medium/' . $photos[$j]->image)) {
								$count++;
							}
						}
						$row->photoArr = $photoArr;
						$row->count_photo = $count;
					}

					//get state
					//$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
					$row->state_name = OSPHelper::loadSateName($row->state); //$db->loadResult();

					//get country
					$row->country_name = OSPHelper::getCountryName($row->country);
					//$db->setQuery("Select country_name from #__osrs_countries where id = '$row->country'");
					//$row->country_name = $db->loadResult();

					//rating
					if ($configClass['show_rating'] == 1) {
						if ($row->number_votes > 0) {
							$points = round($row->total_points / $row->number_votes);
							ob_start();
?>
							<img src="<?php echo Uri::root() ?>media/com_osproperty/assets/images/stars-<?php echo $points; ?>.png" />
						<?php
							$row->rating = ob_get_contents();
							ob_end_clean();
						} else {
							ob_start();

						?>
							<img src="<?php echo Uri::root() ?>media/com_osproperty/assets/images/stars-0.png" />
<?php

							$row->rating = ob_get_contents();
							ob_end_clean();
						} //end rating
					}

					//comments
					$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$row->id'");
					$ncomment = $db->loadResult();
					if ($ncomment > 0) {
						$row->comment = $ncomment;
					} else {
						$row->comment = 0;
					}
					//show icon for featured, just added, just updated
					$row->just_added_ico		= "";
					$row->just_updated_ico		= "";
					$row->featured_ico			= "";
					$row->market_ico			= "";
					$created_on					= $row->created;
					$modified_on				= $row->modified;
					$created_on					= strtotime($created_on);
					$modified_on				= strtotime($modified_on);
					if ($created_on > time() - 3 * 24 * 3600) { //new
						if ($configClass['show_just_add_icon'] == 1) {
							$row->just_added_ico = '<span class="justaddedproperty">' . Text::_("OS_JUSTADDED") . '</span> ';
						}
					} elseif ($modified_on > time() - 2 * 24 * 3600) {
						if ($configClass['show_just_update_icon'] == 1) {
							$row->just_updated_ico = '<span class="justupdatedproperty">' . Text::_("OS_JUSTUPDATED") . '</span> ';
						}
					}
					if ($row->isFeatured == 1) {
						$row->featured_ico = '<span class="featuredproperty">' . Text::_('OS_FEATURED') . '</span> ';
					}
					if (($configClass['active_market_status'] == 1) && ($row->isSold > 0)) {
						$row->market_ico = '<span class="marketstatuspropertydetails">' . OSPHelper::returnMarketStatus($row->isSold) . '</span> ';
					}
				} //for
			} //if rows > 0
		}

		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option', 'a.isFeatured', Text::_('OS_FEATURED'));
		if ($configClass['show_ref'] == 1) {
			$optionArr[] = HTMLHelper::_('select.option', 'a.ref', Text::_('Ref'));
		}
		$optionArr[] = HTMLHelper::_('select.option', 'a.pro_name', Text::_('OS_PROPERTY_TITLE'));
		$optionArr[] = HTMLHelper::_('select.option', 'a.created', Text::_('OS_LISTDATE'));
		$optionArr[] = HTMLHelper::_('select.option', 'a.modified', Text::_('OS_MODIFIED'));
		$optionArr[] = HTMLHelper::_('select.option', 'a.price', Text::_('OS_PRICE'));
		if ($configClass['use_squarefeet'] == 1) {
			if ($configClass['use_square'] == 0) {
				$optionArr[] = HTMLHelper::_('select.option', 'a.square_feet', Text::_('OS_SQUARE_FEET'));
			} else {
				$optionArr[] = HTMLHelper::_('select.option', 'a.square_feet', Text::_('OS_SQUARE_METER'));
			}
		}
		$lists['sortby'] = HTMLHelper::_('select.genericlist', $optionArr, 'sortby', 'class="' . $bootstrapHelper->getClassMapping('input-medium') . ' form-select ilarge"', 'value', 'text', $sortby);

		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option', 'desc', Text::_('OS_DESC'));
		$optionArr[] = HTMLHelper::_('select.option', 'asc', Text::_('OS_ASC'));
		$lists['orderby'] =  HTMLHelper::_('select.genericlist', $optionArr, 'orderby', 'class="' . $bootstrapHelper->getClassMapping('input-medium') . ' form-select imedium"', 'value', 'text', $orderby);

		ob_start();
		OSPHelper::loadAgentTypeDropdownFilter($agent_type, $bootstrapHelper->getClassMapping('input-medium') . ' selectpicker', '');
		$lists['agenttype'] = ob_get_contents();
		ob_end_clean();

		$lists['category'] = OSPHelper::listCategoriesInMultiple($category_ids, '');

		$document = Factory::getDocument();

		//$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/chosen/chosen.css");
		//property types
		//$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY ordering");
		$protypes = $db->loadObjectList();
		//$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist', $protypes, 'property_types[]', 'class="' . $bootstrapHelper->getClassMapping('input-large') . ' chosen" multiple', 'value', 'text', $property_types);

		$lists['marketstatus'] = OSPHelper::buildDropdownMarketStatus($isSold);

		//price
		//$lists['price'] = HelperOspropertyCommon::generatePriceList($adv_type,$price);
		$lists['price_value'] = $price;
		$lists['adv_type'] = $adv_type;
		$lists['min_price'] = $min_price;
		$lists['max_price'] = $max_price;
		//$lists['price'] = $price;
		// number bath room
		$bathArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ANY'));
		for ($i = 1; $i <= 5; $i++) {
			$bathArr[] = HTMLHelper::_('select.option', $i, $i . '+');
		}
		$lists['nbath'] = HTMLHelper::_('select.genericlist', $bathArr, 'nbath', ' class="' . $bootstrapHelper->getClassMapping('input-small') . ' form-select"', 'value', 'text', $nbath);


		//number bed room
		$lists['nbed'] = $nbed;
		$bedArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ANY'));
		for ($i = 1; $i <= 5; $i++) {
			$bedArr[] = HTMLHelper::_('select.option', $i, $i . '+');
		}
		$lists['nbed'] = HTMLHelper::_('select.genericlist', $bedArr, 'nbed', 'class="' . $bootstrapHelper->getClassMapping('input-small') . ' form-select"', 'value', 'text', $nbed);

		//number bed room
		$lists['room'] = $nroom;
		$roomArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ANY'));
		for ($i = 1; $i <= 5; $i++) {
			$roomArr[] = HTMLHelper::_('select.option', $i, $i . '+');
		}
		$lists['nroom'] = HTMLHelper::_('select.genericlist', $roomArr, 'nroom', 'class="' . $bootstrapHelper->getClassMapping('input-small') . ' form-select"', 'value', 'text', $nroom);


		//number bed floors
		$lists['nfloors'] = $nfloors;
		$floorArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ANY'));
		for ($i = 1; $i <= 5; $i++) {
			$floorArr[] = HTMLHelper::_('select.option', $i, $i . '+');
		}
		$lists['nfloor'] = HTMLHelper::_('select.genericlist', $floorArr, 'nfloors', 'class="' . $bootstrapHelper->getClassMapping('input-small') . ' form-select"', 'value', 'text', $nfloors);


		//country
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id, 'country_id', 'onchange="change_country_company(this.value)"', Text::_('OS_ALL_COUNTRIES'), '', $bootstrapHelper->getClassMapping('input-large'));

		//$lists['state'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onchange="change_state(this.value,'.intval($city).')"',Text::_('OS_ALL_STATES'),'');
		//list city
		//$lists['city'] = HelperOspropertyCommon::loadCity($option,$state_id, $city);
		if (OSPHelper::userOneState()) {
			$lists['state'] = "<input type='hidden' name='state_id' id='state_id' value='" . OSPHelper::returnDefaultState() . "'/>";
		} else {
			$lists['state'] = HelperOspropertyCommon::makeStateList($country_id, $state_id, 'state_id', 'onchange="change_state(this.value,' . intval($city) . ')"', Text::_('OS_ALL_STATES'), '');
		}

		if (OSPHelper::userOneState()) {
			$default_state = OSPHelper::returnDefaultState();
		} else {
			$default_state = $state_id;
		}

		$lists['city'] = HelperOspropertyCommon::loadCity($option, $default_state, $city);

		$db->setQuery("Select * from #__osrs_amenities where published = '1' order by ordering");
		$amenities = $db->loadObjectList();
		$lists['amenities'] = $amenities;

		$radius_arr = array(5, 10, 20, 50, 100, 200);
		$radiusArr = [];
		$radius_type = ($configClass['locator_radius_type'] == 0) ? Text::_('OS_MILES') : Text::_('OS_KILOMETRE');
		foreach ($radius_arr as $radius) {
			$radiusArr[] = HTMLHelper::_('select.option', $radius, $radius . ' ' . $radius_type);
		}
		$lists['radius'] = HTMLHelper::_('select.genericlist', $radiusArr, 'radius_search', 'class="' . $bootstrapHelper->getClassMapping('input-medium') . ' form-select imedium"', 'value', 'text', $radius_search);

		HTML_OspropertyListing::advSearchForm($option, $groups, $lists, $rows, $pageNav, $param, $adv_type, $dosearch);
	}
}
