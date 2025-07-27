<?php
/*------------------------------------------------------------------------
# router.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Registry\Registry;


error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR);
class OSPRoute
{
	protected static $lookup = [];
	/**
	 * Check and return Itemid
	 *
	 * @param array $needs
	 */
	public static function getItemid_old($needs)
	{
		global $mainframe,$configClass;
		static $default_itemid;
        $jinput = Factory::getApplication()->input;
		$needs1 = array();
		$user = Factory::getUser();
		$db = Factory::getDBO();
		if($default_itemid == null){
			$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'default_itemid'");
			$default_itemid = $db->loadResult();
		}
		$app		= Factory::getApplication();
		$menus		= $app->getMenu('site');
		$component	= ComponentHelper::getComponent('com_osproperty');
		$items		= $menus->getItems('component_id', $component->id);
		foreach ($items as $item){
			self::$lookup[] = $item->id;
		}
		$lookup_sql = "";
		if(count(self::$lookup) > 0){
			$lookup_sql = " and id in (".implode(",",self::$lookup).")";
		}else{
			$lookup_sql = "";
		}
		$additional_sql = "";
		$language_sql = "";
		if (Multilanguage::isEnabled()){
			$language = Factory::getLanguage();
			$current_lag = $language->getTag();
			$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
		}
		$find_pro_type = 0;
		$find_category_id = array();
		$find_company_id = 0;
		$find_country = 0;
		$find_isFeatured = 0;
		$find_state_id = 0;
		$find_city_id = 0;
		
		if(count((array)$needs) > 0){
			
			if($needs[0] == "property_details")
			{
				$pid				= $needs[1];
				$find_lang			= $needs[2];
				if($pid > 0){
					//$db->setQuery("Select agent_id,pro_type,city,state,country,isFeatured from #__osrs_properties where id = '$pid'");
					$query			= "Select a.agent_id,a.pro_type, a.city, a.state, a.country, a.isFeatured, b.agent_type, c.company_id from #__osrs_properties as a"
						  //." inner join #__osrs_property_categories as d on d.pid = a.id"
						  ." inner join #__osrs_agents as b on b.id = a.agent_id"
						  ." left join #__osrs_company_agents as c on c.agent_id = b.id"
						  ." where a.id = '$pid'";
					$db->setQuery($query);
					$property		= $db->loadObject();
					$pro_type		= $property->pro_type;
					//$category_id	= $property->category_id;
					$state			= $property->state;
					$city			= $property->city;
					$agent_id		= $property->agent_id;
					$country		= $property->country;
					$isFeatured		= $property->isFeatured;
					$company_id		= (int)$property->company_id;
					$agent_type		= (int) $property->agent_type;

                    $category_id    = (array)OSPHelper::getCategoryIdsOfProperty($pid);
					
					$needs			= array();
					$needs[]		= "property_type";
					$needs[]		= "ltype";
					$needs1[]		= "type_id=$pro_type";
					
					if (Multilanguage::isEnabled()){
						if($find_lang != ""){
							$current_lag = $find_lang;
						}else{
							$language = Factory::getLanguage();
							$current_lag = $language->getTag();
						}
						$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
					}

					//checking details link directly
					$db->setQuery("Select id from #__menu where published = '1' and ((`link` like '%view=ldetails&id=".$pid."%') or (`link` like '%view=ldetails&ampid=".$pid."%')) $language_sql and `access` IN (". implode(',', $user->getAuthorisedViewLevels()) .")");
					$founded_menu = $db->loadResult();
					if($founded_menu > 0)
					{
						return $founded_menu;
					}

					$db->setQuery("Select * from #__menu where published = '1' and `home` = '0' and `link` like '%view=ltype%' $language_sql and `access` IN (". implode(',', $user->getAuthorisedViewLevels()) .")");
					$menus_found = $db->loadObjectList();

					if(count($menus_found) == 0)
					{
						$db->setQuery("Select * from #__menu where published = '1' and `home` = '0' and `link` like '%view=lcity%' and `link` like '%id=".$property->city."%' $language_sql and `access` IN (". implode(',', $user->getAuthorisedViewLevels()) .")");
						$menus_found = $db->loadObjectList();
						if(count($menus_found) > 0){
							return $menus_found[0]->id;
						}
					}
					
					$jmenu = Factory::getApplication()->getMenu();
					
					if(count($menus_found) > 0)
					{
						$tmp = [];
						foreach($menus_found as $found)
						{
							$tmp[] = $found->id;
						}
						$menuArr = array();
						$menus	= $app->getMenu('site');
						$active = $menus->getActive();
						/*
						$db->setQuery("Select count(id) from #__menu where published = '1' and `home` = '0' and `link` like '%view=ltype%' $language_sql and id = '".intval($active->id)."'");
						$count = $db->loadResult();
						if($count > 0){
							$menuid_active = $active->id;
						}else{
							$db->setQuery("Select id from #__menu where published = '1' and `home` = '0' and `link` like '%view=ltype%' ".$language_sql);
							$menuid_active = $db->loadResult();
						}
						*/

						if(in_array($active->id, $tmp))
						{
							$menuid_active = $active->id;
						}
						else
						{
							$menuid_active = $tmp[0];
						}


						for($i=0;$i<count($menus_found);$i++)
						{
							$return				= 0;
							$menu				= $menus_found[$i];
							
							$mid				= $menu->id;
    						$mobj				= $jmenu->getItem( $mid );
							//print_r($mobj->query);
							$find_pro_type		=  $mobj->query['type_id'];
							$find_category_id	= (array)$mobj->query['catIds'];
							$find_company_id	= $mobj->query['company_id'];
							$find_country		= $mobj->query['country_id'];
							//echo $find_country;
							$params				= $menu->params;
							$params				= json_decode($params);
							$find_isFeatured	= $params->isFeatured;
							$find_state_id		= $params->state_id;
							$find_city_id		= $params->city_id;
							
							$find_agent_type	= $params->agenttype;

							//$arr1 = array();
							//$arr2 = array();
							//find itemid now
							if($find_pro_type > 0)
							{
								if($find_pro_type == $pro_type){ //ok
									$type = 1;
									$return++;
								}
								else
								{
									$type = 0;
								}
							}else{
								$type = 0;
							}
							if(count($find_category_id) > 0 && count($category_id) > 0)
							{
								$show = 0;
								foreach($category_id as $cid)
								{
									if(in_array($cid,$find_category_id))
									{
										$show = 1;
									}
								}
								if($show == 1){
									$cat = 1;
									$return++;
									
									if(count($find_category_id) == count($category_id)){
										$return++; //use for case: Parent menu contains several sub cats. And there is other link for one sub cat. The system must get Itemid of that sub cat. 
									}
								}else{
									$return = -1000; //we won't care this menu
								}
							}else{
								$cat = 0;
							}
							if($find_country > 0){
								if($find_country == $country){ //ok
									$c = 1;
									$return++;
								}else{
									$c = 0;
								}
							}else{
								$c = 0;
							}
							
							if($find_state_id > 0){
								if($find_state_id == $state){ //ok
									$s = 1;
									$return++;
								}else{
									$s = 0;
								}
							}else{
								$s = 0;
							}

							if($find_city_id > 0){
								if($find_city_id == $city){ //ok
									$s = 1;
									$return++;
								}else{
									$s = 0;
								}
							}else{
								$s = 0;
							}
							
							
							if($find_company_id > 0){
								if($find_company_id == $company_id){ //ok
									$company = 1;
									$return++;
								}else{
									$company = 0;
								}
							}else{
								$company = 0;
							}
							
							if($find_isFeatured > 0){
								if($find_isFeatured == $isFeatured){ //ok
									$featured = 1;
									if($return > 0){
										$return = $return + 2;
									}
								}else{
									$featured = 0;
								}
							}else{
								$featured = 0;
							}

							if($find_agent_type > 0){
								if($find_agent_type == $agent_type){ //ok
									if($return > 0){
										$return = $return + 1;
									}
								}
							}
							
							$count = count($menuArr);
							$menuArr[$count] = new stdClass();
							$menuArr[$count]->point = $return;
							$menuArr[$count]->menu_id = $menu->id;
							
						}//end for
						$max = 0;
						//$menus	= $app->getMenu('site');
						$menuid = $default_itemid;
						if($menuid == 0){
							$menuid = $default_itemid;
						}

						for($i=0;$i<count($menuArr);$i++)
						{
							if($menuArr[$i]->point > $max)
							{
								$max = $menuArr[$i]->point;
								$menuid = $menuArr[$i]->menu_id;
							}
						}
						if($max == 0)
						{
							$menuid = $menuid_active;
						}
						if($menuid > 0)
						{
							return $menuid;
						}
						else
						{
							return 9999;
						}
					}//end menus_found
					else
					{ //checking in category
						$db->setQuery("Select * from #__menu where published = '1' and (`link` like 'view=lcategory' or `link` like 'task=category_listing') $language_sql");
						$menus_found = $db->loadObjectList();
						if(count($menus_found) > 0){
							$menuid = $menus_found[0]->id;
							return $menuid;
						}else{
							$menuid = $default_itemid;
							if($menuid == 0){
								//$active = $menus->getActive();
								$menuid = $default_itemid;
							}
							return $menuid;
						}
					}
				}
				else
				{
					$menuid = $default_itemid;
					if($menuid == 0){
						$menuid = $default_itemid;
					}
					if($menuid > 0){
						return $menuid;
					}else{
						return 9999;
					}
				}
			}
			if($needs[2] == "agent_details" && (int)$needs[3] > 0)
			{
				$agent_id = $needs[3];
				if((int)$agent_id > 0){
					$db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
					$agent_type = $db->loadResult();


					if (Multilanguage::isEnabled()){
						if($find_lang != ""){
							$current_lag = $find_lang;
						}else{
							$language = Factory::getLanguage();
							$current_lag = $language->getTag();
						}
						$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
					}

					$db->setQuery("Select * from #__menu where published = '1' and `home` = '0' and `link` like '%view=lagents%' $language_sql and `access` IN (". implode(',', $user->getAuthorisedViewLevels()) .")");
					$menus_found = $db->loadObjectList();

					$jmenu = Factory::getApplication()->getMenu();
					
					if(count($menus_found) > 0){
						$menuArr = array();
						for($i=0;$i<count($menus_found);$i++){
							$return = 0;
							$menu = $menus_found[$i];
							
							$mid = $menu->id;
    						$mobj = $jmenu->getItem( $mid );
							//print_r($mobj->query);
							$find_agent_type =  $mobj->query['usertype'];
							
							if($find_agent_type >= 0){
								if($find_agent_type == $agent_type){ //ok
									$agenttype = 1;
									$return++;
								}else{
									$agenttype = 0;
								}
							}else{
								$agenttype = 0;
							}
							$count = count($menuArr);
							$menuArr[$count] = new stdClass();
							$menuArr[$count]->point = $return;
							$menuArr[$count]->menu_id = $menu->id;
							
						}//end for
						$max = 0;
						$menuid = $default_itemid;
						if($menuid == 0){
							$menuid = $default_itemid;
						}

						for($i=0;$i<count($menuArr);$i++)
						{
							if($menuArr[$i]->point > $max)
							{
								$max = $menuArr[$i]->point;
								$menuid = $menuArr[$i]->menu_id;
							}
						}

						if($menuid > 0){
							return $menuid;
						}else{
							return 9999;
						}
					}else{
						$itemId = $default_itemid;
						if($itemId == 0){
							$itemId = $jinput->getInt('Itemid',0);
						}
						return $itemId;
					}
				}
			}
			$tempArr = array();
			for($i=0;$i<count($needs);$i++)
			{
				$item = $needs[$i];
				$tempArr[] = '  `link` LIKE "%'.$item.'%"';
			}
			if(count($tempArr) > 0)
			{
				$additional_sql .=" and (";
				$additional_sql .= implode(" or ",$tempArr);
				$additional_sql .= " )";
			
				if(count($needs1) > 0){
					$additional_sql .=" and (`link` LIKE '%".$needs1[0]."%')";
				}
				
				$query = $db->getQuery(true);
				$query->select('id')
					->from('#__menu')
					->where('link LIKE "%index.php?option=com_osproperty%"'.$additional_sql )
					->where('published = 1 '.$lookup_sql . $language_sql)
					->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
					->order('access');
				$db->setQuery($query);
				$itemId = $db->loadResult();
				
				if (intval($itemId) == 0)
				{
					$itemId = $default_itemid;
					if($itemId == 0){
						$itemId = $jinput->getInt('Itemid',0);
					}
				}
				return $itemId;
			}
		}else{
			$itemId = $default_itemid;
			if($itemId > 0){
				return $itemId;
			}else{				
				return $default_itemid;
			}
		}
	}
	

	public static function getItemid($needs)
	{
		global $mainframe, $configClass;
		static $default_itemid = null;
		static $lookup = [];
		
		// Khởi tạo các biến cần thiết
		$jinput = Factory::getApplication()->input;
		$user = Factory::getUser();
		$db = Factory::getDBO();
		$app = Factory::getApplication();
		
		// Cache default_itemid
		if ($default_itemid === null) {
			$db->setQuery("SELECT fieldvalue FROM #__osrs_configuration WHERE fieldname = 'default_itemid'");
			$default_itemid = (int)$db->loadResult();
		}
		
		// Xây dựng lookup menu một lần
		if (empty($lookup)) {
			$menus = $app->getMenu('site');
			$component = ComponentHelper::getComponent('com_osproperty');
			$items = $menus->getItems('component_id', $component->id);
			foreach ($items as $item) {
				$lookup[] = $item->id;
			}
		}
		
		// Xây dựng các điều kiện SQL
		$lookup_sql = !empty($lookup) ? " AND id IN (" . implode(",", $lookup) . ")" : "";
		$language_sql = "";
		
		// Xử lý đa ngôn ngữ
		if (Multilanguage::isEnabled()) {
			$language = Factory::getLanguage();
			$current_lag = $language->getTag();
			$language_sql = " AND (`language` LIKE '$current_lag' OR `language` LIKE '*' OR `language` = '')";
		}
		
		// Nếu không có yêu cầu cụ thể, trả về default_itemid
		if (empty($needs)) {
			return $default_itemid ?: 0;
		}
		
		// Xử lý chi tiết bất động sản
		if ($needs[0] == "property_details") {
			$pid = (int)$needs[1];
			$find_lang = isset($needs[2]) ? $needs[2] : '';
			
			if ($pid <= 0) {
				return $default_itemid ?: 9999;
			}
			
			// Lấy thông tin bất động sản
			$query = "SELECT a.agent_id, a.pro_type, a.city, a.state, a.country, a.isFeatured, b.agent_type, c.company_id 
					 FROM #__osrs_properties AS a
					 INNER JOIN #__osrs_agents AS b ON b.id = a.agent_id
					 LEFT JOIN #__osrs_company_agents AS c ON c.agent_id = b.id
					 WHERE a.id = " . $db->quote($pid);
			$db->setQuery($query);
			$property = $db->loadObject();
			
			if (!$property) {
				return $default_itemid ?: 9999;
			}
			
			// Xử lý đa ngôn ngữ
			if (Multilanguage::isEnabled() && $find_lang) {
				$current_lag = $find_lang;
				$language_sql = " AND (`language` LIKE " . $db->quote($current_lag) . " OR `language` LIKE '*' OR `language` = '')";
			}
			
			// Kiểm tra liên kết chi tiết trực tiếp
			$db->setQuery("SELECT id FROM #__menu WHERE published = 1 AND ((`link` LIKE '%view=ldetails&id=" . $pid . "%') OR (`link` LIKE '%view=ldetails&ampid=" . $pid . "%')) $language_sql AND `access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")");
			$founded_menu = $db->loadResult();
			if ($founded_menu > 0) {
				return $founded_menu;
			}
			
			// Tìm kiếm trong các menu loại bất động sản
			$db->setQuery("SELECT * FROM #__menu WHERE published = 1 AND `home` = 0 AND `link` LIKE '%view=ltype%' $language_sql AND `access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")");
			$menus_found = $db->loadObjectList();
			
			if (empty($menus_found)) {
				// Tìm kiếm theo thành phố
				$db->setQuery("SELECT * FROM #__menu WHERE published = 1 AND `home` = 0 AND `link` LIKE '%view=lcity%' AND `link` LIKE '%id=" . $property->city . "%' $language_sql AND `access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")");
				$menus_found = $db->loadObjectList();
				if (!empty($menus_found)) {
					return $menus_found[0]->id;
				}
				
				// Tìm kiếm trong danh mục
				$db->setQuery("SELECT * FROM #__menu WHERE published = 1 AND (`link` LIKE '%view=lcategory%' OR `link` LIKE '%task=category_listing%') $language_sql");
				$menus_found = $db->loadObjectList();
				if (!empty($menus_found)) {
					return $menus_found[0]->id;
				}
				
				return $default_itemid ?: 9999;
			}
			
			// Lấy danh mục của bất động sản
			$category_id = (array)OSPHelper::getCategoryIdsOfProperty($pid);
			
			// Lấy menu active
			$jmenu = $app->getMenu('site');
			$active = $jmenu->getActive();
			
			// Tìm kiếm menu phù hợp nhất
			$tmp = [];
			foreach ($menus_found as $found) {
				$tmp[] = $found->id;
			}
			
			$menuid_active = in_array($active->id, $tmp) ? $active->id : $tmp[0];
			$menuArr = [];
			
			foreach ($menus_found as $i => $menu) {
				$return = 0;
				$mid = $menu->id;
				$mobj = $jmenu->getItem($mid);
				
				// Lấy các tham số menu
				$find_pro_type = isset($mobj->query['type_id']) ? (int)$mobj->query['type_id'] : 0;
				$find_category_id = isset($mobj->query['catIds']) ? (array)$mobj->query['catIds'] : [];
				$find_company_id = isset($mobj->query['company_id']) ? (int)$mobj->query['company_id'] : 0;
				$find_country = isset($mobj->query['country_id']) ? (int)$mobj->query['country_id'] : 0;
				
				$params = json_decode($menu->params);
				$find_isFeatured = isset($params->isFeatured) ? (int)$params->isFeatured : 0;
				$find_state_id = isset($params->state_id) ? (int)$params->state_id : 0;
				$find_city_id = isset($params->city_id) ? (int)$params->city_id : 0;
				$find_agent_type = isset($params->agenttype) ? (int)$params->agenttype : 0;
				
				// So khớp loại bất động sản
				if ($find_pro_type > 0 && $find_pro_type == $property->pro_type) {
					$return++;
				}
				
				// So khớp danh mục
				if (!empty($find_category_id) && !empty($category_id)) {
					$show = 0;
					foreach ($category_id as $cid) {
						if (in_array($cid, $find_category_id)) {
							$show = 1;
						}
					}
					
					if ($show == 1) {
						$return++;
						
						if (count($find_category_id) == count($category_id)) {
							$return++;
						}
					} else {
						$return = -1000;
					}
				}
				
				// So khớp quốc gia
				if ($find_country > 0 && $find_country == $property->country) {
					$return++;
				}
				
				// So khớp bang/tỉnh
				if ($find_state_id > 0 && $find_state_id == $property->state) {
					$return++;
				}
				
				// So khớp thành phố
				if ($find_city_id > 0 && $find_city_id == $property->city) {
					$return++;
				}
				
				// So khớp công ty
				if ($find_company_id > 0 && $find_company_id == $property->company_id) {
					$return++;
				}
				
				// So khớp tính năng nổi bật
				if ($find_isFeatured > 0 && $find_isFeatured == $property->isFeatured && $return > 0) {
					$return += 2;
				}
				
				// So khớp loại đại lý
				if ($find_agent_type > 0 && $find_agent_type == $property->agent_type && $return > 0) {
					$return++;
				}
				
				$menuArr[] = (object)[
					'point' => $return,
					'menu_id' => $menu->id
				];
			}
			
			// Tìm menu có điểm cao nhất
			$max = 0;
			$menuid = $default_itemid;
			
			foreach ($menuArr as $item) {
				if ($item->point > $max) {
					$max = $item->point;
					$menuid = $item->menu_id;
				}
			}
			
			if ($max == 0) {
				$menuid = $menuid_active;
			}
			
			return $menuid ?: 9999;
		}
		
		// Xử lý chi tiết đại lý
		if (isset($needs[2]) && $needs[2] == "agent_details" && isset($needs[3]) && (int)$needs[3] > 0) {
			$agent_id = (int)$needs[3];
			
			$db->setQuery("SELECT agent_type FROM #__osrs_agents WHERE id = " . $db->quote($agent_id));
			$agent_type = $db->loadResult();
			
			if (!$agent_type) {
				return $default_itemid ?: $jinput->getInt('Itemid', 0);
			}
			
			// Xử lý đa ngôn ngữ nếu cần
			if (Multilanguage::isEnabled() && isset($find_lang) && $find_lang) {
				$current_lag = $find_lang;
				$language_sql = " AND (`language` LIKE " . $db->quote($current_lag) . " OR `language` LIKE '*' OR `language` = '')";
			}
			
			$db->setQuery("SELECT * FROM #__menu WHERE published = 1 AND `home` = 0 AND `link` LIKE '%view=lagents%' $language_sql AND `access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")");
			$menus_found = $db->loadObjectList();
			
			if (!empty($menus_found)) {
				$jmenu = $app->getMenu('site');
				$menuArr = [];
				
				foreach ($menus_found as $i => $menu) {
					$return = 0;
					$mid = $menu->id;
					$mobj = $jmenu->getItem($mid);
					
					$find_agent_type = isset($mobj->query['usertype']) ? (int)$mobj->query['usertype'] : -1;
					
					if ($find_agent_type >= 0 && $find_agent_type == $agent_type) {
						$return++;
					}
					
					$menuArr[] = (object)[
						'point' => $return,
						'menu_id' => $menu->id
					];
				}
				
				// Tìm menu có điểm cao nhất
				$max = 0;
				$menuid = $default_itemid;
				
				foreach ($menuArr as $item) {
					if ($item->point > $max) {
						$max = $item->point;
						$menuid = $item->menu_id;
					}
				}
				
				return $menuid ?: 9999;
			}
			
			return $default_itemid ?: $jinput->getInt('Itemid', 0);
		}
		
		// Xử lý các yêu cầu khác
		$tempArr = [];
		foreach ($needs as $item) {
			$tempArr[] = '`link` LIKE ' . $db->quote('%' . $item . '%');
		}
		
		if (!empty($tempArr)) {
			$additional_sql = " AND (" . implode(" OR ", $tempArr) . ")";
			
			if (!empty($needs1)) {
				$additional_sql .= " AND (`link` LIKE " . $db->quote('%' . $needs1[0] . '%') . ")";
			}
			
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__menu')
				->where('link LIKE ' . $db->quote('%index.php?option=com_osproperty%') . $additional_sql)
				->where('published = 1' . $lookup_sql . $language_sql)
				->where('`access` IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
				->order('access');
			$db->setQuery($query);
			$itemId = $db->loadResult();
			
			if ((int)$itemId == 0) {
				$itemId = $default_itemid ?: $jinput->getInt('Itemid', 0);
			}
			
			return $itemId;
		}
		
		return $default_itemid ?: 0;
	}

	public static function confirmItemid($itemid, $layout)
	{
		$db = Factory::getDbo();

		// Ép kiểu để tránh SQL Injection
		$itemid = (int) $itemid;
		$layout = $db->quote('%' . $db->escape($layout, true) . '%');

		// Xây dựng điều kiện ngôn ngữ nếu cần
		$language_sql = '';
		if (Multilanguage::isEnabled()) {
			$current_lag = $db->quote(Factory::getLanguage()->getTag());
			$language_sql = " AND (`language` IN ('*', '', $current_lag))";
		}

		// Kiểm tra xem Itemid có tồn tại không
		$query = "SELECT COUNT(*) FROM #__menu 
				  WHERE published = 1 
				  AND `link` LIKE $layout 
				  $language_sql 
				  AND id = $itemid";
		$db->setQuery($query);

		if ($db->loadResult() > 0) {
			return $itemid;
		}

		// Lấy Itemid mặc định nếu không tìm thấy
		$db->setQuery("SELECT fieldvalue FROM #__osrs_configuration WHERE fieldname = 'default_itemid'");
		return (int) $db->loadResult();
	}
	
	public static function confirmItemidArr($itemid, $layoutArr)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$language_sql = "";
		
		// Kiểm tra hỗ trợ đa ngôn ngữ
		if (Multilanguage::isEnabled()) {
			$language = Factory::getLanguage()->getTag();
			$language_sql = " AND (`language` = " . $db->quote($language) . " OR `language` = '*' OR `language` = '')";
		}
		
		// Xử lý điều kiện layout
		$layoutSql = "";
		$layoutArr = (array) $layoutArr;
		if (!empty($layoutArr)) {
			$tempArr = array();
			foreach ($layoutArr as $layout) {
				$tempArr[] = "`link` LIKE " . $db->quote('%' . $db->escape($layout) . '%');
			}
			$layoutSql = " AND (" . implode(" OR ", $tempArr) . ")";
		}
		
		// Tạo và thực thi truy vấn kiểm tra itemid
		$query->select('COUNT(id)')
			  ->from('#__menu')
			  ->where("published = 1" . $layoutSql . $language_sql)
			  ->where("id = " . $db->quote((int)$itemid));
		
		$db->setQuery($query);
		$count = $db->loadResult();
		
		// Trả về itemid nếu tìm thấy, ngược lại trả về default_itemid
		if ($count > 0) {
			return (int)$itemid;
		} else {
			$query = $db->getQuery(true);
			$query->select('fieldvalue')
				  ->from('#__osrs_configuration')
				  ->where("fieldname = " . $db->quote('default_itemid'));
			
			$db->setQuery($query);
			$default_itemid = $db->loadResult();
			
			return (int)$default_itemid;
		}
	}
	
	public static function reCheckItemid($itemid, $check){
		$jmenu = Factory::getApplication()->getMenu();
		$menuObj = $jmenu->getItem($itemid);
		$menuQuery = $menuObj->query;
		$task = $menuQuery['task'];
		$view = $menuQuery['view'];
		$return = false;
		foreach($check as $ch)
		{
			if($ch == $task || $ch == $view)
			{
				$return = true;
			}
		}
		return $return;
	}

    /**
     * @return int|mixed
     */
    static function getAgentItemid($agent_id = 0){
        $needs   = [];
        $needs[] = "lagents";
        $needs[] = "agent_layout";
        $needs[] = "agent_listing";
		$needs[] = "agent_details";
		$needs[] = $agent_id;
        $itemid  = self::getItemid($needs);
        return $itemid;
    }

	static function getPropertyItemid($id){
		$needs   = [];
        $needs[] = "property_details";
		$needs[] = $id;
        $itemid  = self::getItemid($needs);
        return $itemid;
	}

    /**
     * @return int|mixed
     */
    static function getCompanyItemid(){
        $needs   = [];
        $needs[] = "lcompanies";
        $needs[] = "company_listing";
        $itemid  = self::getItemid($needs);
        return $itemid;
    }

	static function checkDirectPropertyLink($itemid, $pid)
	{
		$user = Factory::getUser();
		$db = Factory::getDbo();
		
		// Ép kiểu để tránh SQL Injection
		$itemid = (int) $itemid;
		$pid = (int) $pid;

		// Kiểm tra ngôn ngữ nếu cần
		$language_sql = '';
		if (Multilanguage::isEnabled()) {
			$language = Factory::getLanguage();
			$current_lag = $db->quote($language->getTag()); // Dùng quote() để tránh lỗi SQL
			$language_sql = " AND (`language` IN ('*', '', $current_lag))";
		}

		// Câu truy vấn SQL
		$query = "SELECT COUNT(*) FROM #__menu 
				  WHERE id = $itemid 
				  AND published = 1 
				  AND (`link` LIKE '%view=ldetails%' AND `link` LIKE '%id=$pid%') 
				  AND `access` IN (" . implode(',', $user->getAuthorisedViewLevels()) . ") 
				  $language_sql";

		// Thực thi truy vấn
		$db->setQuery($query);
		return (bool) $db->loadResult();
	}

	/**
	 * So sánh menu item với các tham số chỉ định
	 * 
	 * @param int $itemid       ID của menu item cần kiểm tra
	 * @param array $catIds     Danh sách ID danh mục
	 * @param int $type_id      ID loại bất động sản
	 * @param int $country_id   ID quốc gia
	 * @param int $company_id   ID công ty
	 * @param int $state_id     ID tỉnh/bang
	 * @param int $city_id      ID thành phố
	 * @return bool             Trả về true nếu menu item phù hợp, ngược lại false
	 */
	public static function compareMenuItem($itemid, $catIds, $type_id, $country_id, $company_id, $state_id, $city_id)
	{
		// Kiểm tra ID menu hợp lệ
		if (empty($itemid) || $itemid <= 0) {
			return false;
		}
		
		$db = Factory::getDbo();
		
		// Kiểm tra menu item có tồn tại và đang được xuất bản
		$query = $db->getQuery(true)
			->select('COUNT(id)')
			->from('#__menu')
			->where('id = ' . (int)$itemid)
			->where('published = 1');
		
		$db->setQuery($query);
		
		if (!$db->loadResult()) {
			return false;
		}
		
		// Lấy đối tượng menu
		$jmenu = Factory::getApplication()->getMenu();
		$menuObj = $jmenu->getItem($itemid);
		
		if (!is_object($menuObj)) {
			return false;
		}
		
		// Lấy các tham số từ menu item
		$find_pro_type = isset($menuObj->query['type_id']) ? (int)$menuObj->query['type_id'] : 0;
		$find_category_id = isset($menuObj->query['catIds']) ? (array)$menuObj->query['catIds'] : [];
		$find_company_id = isset($menuObj->query['company_id']) ? (int)$menuObj->query['company_id'] : 0;
		$find_country = isset($menuObj->query['country_id']) ? (int)$menuObj->query['country_id'] : 0;
		
		// Lấy tham số từ params
		$params = $menuObj->getParams();
		$find_state_id = (int)$params->get('state_id', 0);
		$find_city_id = (int)$params->get('city_id', 0);
		
		// So sánh tất cả các tham số
		return self::compareArray($catIds, $find_category_id) && 
			   $type_id == $find_pro_type && 
			   $country_id == $find_country && 
			   $company_id == $find_company_id && 
			   $state_id == $find_state_id && 
			   $city_id == $find_city_id;
	}

	public static function compareArray($array1, $array2)
	{
		$array1 = (array) $array1;
		$array2 = (array) $array2;

		return count($array1) === count($array2) && empty(array_diff($array1, $array2));
	}

}
?>
