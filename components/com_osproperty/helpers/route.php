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
	public static function getItemid($needs)
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
	
	public static function confirmItemid($itemid, $layout){
		global $mainframe;
		$db = Factory::getDbo();
		$language_sql = "";
		if (Multilanguage::isEnabled()){
			$language = Factory::getLanguage();
			$current_lag = $language->getTag();
			$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
		}
		$db->setQuery("Select count(id) from #__menu where published = '1' and `link` like '%$layout%' $language_sql and id = '".$itemid."'");
		$count = $db->loadResult();
		if($count > 0){
			return $itemid;
		}else{
			//return 0;
			$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'default_itemid'");
			$default_itemid = $db->loadResult();
			return intval($default_itemid);
		}
	}
	
	public static function confirmItemidArr($itemid, $layoutArr){
		global $mainframe;
		$db = Factory::getDbo();
		$language_sql = "";
		if (Multilanguage::isEnabled()){
			$language = Factory::getLanguage();
			$current_lag = $language->getTag();
			$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
		}
		$layoutSql = "";
		$layoutArr = (array) $layoutArr;
		if(count($layoutArr) > 0){
			$tempArr = array();
			foreach ($layoutArr as $layout){
				$tempArr[] = "`link` like '%$layout%'";
			}
			$layoutSql = " and (".implode(" or ",$tempArr).")";
		}
		$db->setQuery("Select count(id) from #__menu where published = '1' $layoutSql $language_sql and id = '".$itemid."'");
		$count = $db->loadResult();
		if($count > 0){
			return $itemid;
		}else{
			//return 0;
			$db->setQuery("Select fieldvalue from #__osrs_configuration where fieldname like 'default_itemid'");
			$default_itemid = $db->loadResult();
			return intval($default_itemid);
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

	static function checkDirectPropertyLink($itemid,$pid){
		$user = Factory::getUser();
		$db = Factory::getDbo();
		if (Multilanguage::isEnabled()){
			//if($find_lang != ""){
				//$current_lag = $find_lang;
			//}else{
			$language = Factory::getLanguage();
			$current_lag = $language->getTag();
			//}
			$language_sql = " and (`language` LIKE '$current_lag' or `language` LIKE '*' or `language` = '')";
		}

		//checking details link directly
		$db->setQuery("Select count(id) from #__menu where id = '$itemid' and published = '1' and (`link` like '%view=ldetails%' and `link` like '%id=".$pid."%') $language_sql and `access` IN (". implode(',', $user->getAuthorisedViewLevels()) .")");
		$count = $db->loadResult();
		if($count > 0){
			return true;
		}else{
			return false;
		}
	}

	static function compareMenuItem($itemid,$catIds,$type_id,$country_id,$company_id,$state_id,$city_id)
	{
		$db = Factory::getDbo();
		if($itemid > 0)
		{
			$db->setQuery("Select count(id) from #__menu where id = '$itemid' and published = '1'");
			$count = $db->loadResult();
			if($count > 0)
			{
				$jmenu				= Factory::getApplication()->getMenu();
				$menuObj			= $jmenu->getItem($itemid);
				if(is_object($menuObj))
				{
					$find_pro_type		= $menuObj->query['type_id'];
					$find_category_id	= $menuObj->query['catIds'];
					$find_company_id	= $menuObj->query['company_id'];
					$find_country		= $menuObj->query['country_id'];
					$params				= new Registry() ;
					$params				= $menuObj->getParams();
					//$params			= json_decode($params);
					$find_state_id		= $params->get('state_id');
					$find_city_id		= $params->get('city_id');

					if((self::compareArray($catIds, $find_category_id)) && ($type_id == $find_pro_type) && ($country_id == $find_country) && ($company_id == $find_company_id) && ($state_id == $find_state_id) && ($city_id == $find_city_id))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
			}
		}
		return false;
	}

	public static function compareArray($array1, $array2)
	{
		$array1 = (array) $array1;
		$array2 = (array) $array2;
		if(count($array1) > 0)
		{
			if(count($array1) == count($array2))
			{
				foreach($array1 as $a)
				{
					if(!in_array($a,$array2))
					{
						return false;
					}
				}
			}
		}
		return true;
	}
}
?>
