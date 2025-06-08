<?php
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
/*------------------------------------------------------------------------
# helper.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class modOSpropertyramdomHelper extends CMSObject
{
	/**
     * Sets the modules params as a property of the object
     * @param unknown_type $params
     * @return unknown_type
     */
	function __construct( $params )
	{
		$this->params = $params;
	}

	/**
     * Sample use of the products model for getting products with certain properties
     * See admin/models/products.php for all the filters currently built into the model 
     * 
     * @param $parameters
     * @return unknown_type
     */
	function getProperty()
	{
		global $languages;

		define('DS',DIRECTORY_SEPARATOR);
		//$languages = OSPHelper::getLanguages();
		$lang_suffix = OSPHelper::getFieldSuffix();
		
		$db                 = Factory::getDBO();
		$check_search       = true;

		// filter property for lastest
		$filter_latest      = $this->params->get('latest',0);

		// filter property for category
		$filter_category    = $this->params->get('category',0);

		// filter property for type
		$filter_type        = $this->params->get('type',0);

		$style              = $this->params->get('mstyle',0);

		$featured           = $this->params->get('featured',0);
		
		$sold	            = $this->params->get('sold',0);
		
		$open_house         = $this->params->get('open_house',0);
		
		$orderby            = $this->params->get('orderby','created');
		
		$recent_properties  = $this->params->get('recent_properties','0');

		$propertyids        = $this->params->get('propertyids','');

		$agent_id			= $this->params->get('agent_id',0);

		$company_id			= $this->params->get('company_id',0);

		$country_ids		= $this->params->get('country_ids','');

		$state_ids			= $this->params->get('state_ids','');

		$city_ids			= $this->params->get('city_ids','');

		$property_ids		= $this->params->get('property_ids','');

		$open_from			= $this->params->get('open_from', '');

		$open_to			= $this->params->get('open_to', '');

		$only_open			= $this->params->get('only_open', '0');

		$where				= [];

		$temp				= [];

		if($propertyids != "")
		{
			$propertyids    = explode(",",$propertyids);
			$propertyids    = ArrayHelper::toInteger($propertyids);
		}
		else
		{
			$propertyids    = array();
		}

		if($country_ids != "")
		{
			$countryArr		= explode(",",$country_ids);
			$countryArr     = ArrayHelper::toInteger($countryArr);
			$country_ids    = implode(",", $countryArr);
			$where[]        = "p.country in (".$country_ids.")";
		}

		if($state_ids != "")
		{
			$stateArr		= explode(",",$state_ids);
			$stateArr		= ArrayHelper::toInteger($stateArr);
			$state_ids		= implode(",", $stateArr);
			$where[]        = "p.state in (".$state_ids.")";
		}

		if($city_ids != "")
		{
			$cityArr		= explode(",",$city_ids);
			$cityArr		= ArrayHelper::toInteger($cityArr);
			$city_ids		= implode(",", $cityArr);
			$where[]        = "p.city in (".$city_ids.")";
		}

		if($property_ids != "")
        {
            $property_ids   = explode(",",$property_ids);
            $property_ids   = ArrayHelper::toInteger($property_ids);
			$where[]        = "p.id in (".implode(", ",$property_ids).")";
        }

		$exclude_property_ids           = $this->params->get('exclude_property_ids','');
        if($exclude_property_ids != "")
        {
            $exclude_property_ids       = explode(",",$exclude_property_ids);
            $exclude_property_ids       = ArrayHelper::toInteger($exclude_property_ids);
        }
        else
        {
            $exclude_property_ids       = array();
        }

		if($only_open == 1)
		{
			$where[]		= " p.id in (Select pid from #__osrs_property_open)";
		}

		if($open_from != "")
        {
            $where[]		= "p.id in (select pid from #__osrs_property_open where start_from >= '$open_from')";
        }

		if($open_to != "")
        {
            $where[]		= "p.id in (select pid from #__osrs_property_open where end_to <= '$open_to')";
        }

        $max_properties = $this->params->get('max_properties','');
        if(intval($max_properties) > 0)
        {
            $limitsql       = " limit $max_properties";
        }
        else
        {
            $limitsql       = "";
        }
		
		$ordertype          = $this->params->get('ordertype','desc');

		// condition

		if ($filter_type > 0)
		{
			$where[]        = "p.pro_type = $filter_type";
			//$check_search = false;
		}

		if($featured == 1)
		{
			$where[]        = "p.isFeatured = '1'";
		}
		
		if($sold > 0){
			$where[] = "p.isSold = '$sold'";
		}
		
		if($open_house == 1)
		{
			$where[] = "p.id in (Select pid from #__osrs_property_open where end_to >= '".date("Y-m-d H:i:s",time())."')";
		}
		
		if($recent_properties == 1)
		{
			$recent_properties_viewed = array();
			$recent_properties_viewed_str = $_COOKIE['recent_properties_viewed'];
			if($recent_properties_viewed_str != "")
			{
				$recent_properties_viewed = explode(",",$recent_properties_viewed_str);
				for($i=count($recent_properties_viewed)-1;$i >= 0;$i--)
				{
					$temp[] = $recent_properties_viewed[$i];
				}
				$where[] = "p.id in (".implode(",",$temp).")";
			}
		}

		// get property have been filter
		$select = "	SELECT p.*, s.state_name, c.country_name,ty.id as typeid, ty.type_name$lang_suffix as type_name FROM #__osrs_properties AS p
    				INNER JOIN #__osrs_types AS ty ON ty.id = p.pro_type
    				LEFT JOIN #__osrs_states AS s ON s.id = p.state
    				INNER JOIN #__osrs_countries AS c ON c.id = p.country
    				INNER JOIN #__osrs_agents as ag ON p.agent_id = ag.id
    				";
    	$where_sql = "";
		if(count($propertyids) > 0)
		{
			$where_sql = " AND p.id in (".implode(",",$propertyids).")";
		}
		elseif(count($where) > 0)
        {
			$where_sql = " AND ".implode(" AND ",$where);
		}

		if(count($exclude_property_ids) > 0)
        {
            $where_sql .= " AND p.id not in (".implode(",", $exclude_property_ids).")";
        }
    	
		$where = " WHERE p.published = '1' AND p.approved = '1' ".OSPHelper::publishDateSql('p');
		$where .= $where_sql;
		$where .= ' and p.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		
		if($filter_category > 0)
		{
			$categoryArr = array();
			$categoryArr = HelperOspropertyCommon::getSubCategories($filter_category,$categoryArr);
			$catids      = implode(",",$categoryArr);
			$where      .= " AND p.id in(Select pid from #__osrs_property_categories where category_id IN ($catids))";
		}
		
		if($agent_id > 0)
		{
			$where .= " AND p.agent_id = '$agent_id'";
		}

		if($company_id > 0)
		{
			$where .= " AND p.company_id = '$company_id'";
		}

		if($recent_properties == 0)
		{
			$order_by = " ORDER BY $orderby $ordertype";
		}
		elseif(count($temp) > 0)
		{
			$order_by = " ORDER BY FIND_IN_SET(p.id,'".implode(",",$temp)."') ";
		}

		$limit = " limit $limitsql";
		//echo $select.$where.$order_by.$limitsql;die();
		$db->setQuery($select.$where.$order_by.$limitsql);
		//echo $db->getQuery();
		$properties = $db->loadObjectList();

		foreach (@$properties as $property) {
			$pro_name = OSPHelper::getLanguageFieldValueBackend($property,'pro_name',$lang_suffix);
			$property->pro_name = $pro_name;
			$pro_small_desc = OSPHelper::getLanguageFieldValue($property,'pro_small_desc',$lang_suffix);
			$property->pro_small_desc = $pro_small_desc;
			$db->setQuery("SELECT image FROM #__osrs_photos WHERE pro_id = $property->id order by ordering");
			$property->photo = $db->loadResult();
			
			//show price information
			ob_start();
			?>
			<span class="property_price">
			<?php
			if(OSPHelper::getLanguageFieldValue($property,'price_text') != "")
			{
				echo OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($property,'price_text'));
			}
			elseif($property->price_call == 1)
			{
				echo Text::_('OSPROPERTY_CALL_FOR_PRICE');
			}
			else
			{
				if($property->price > 0)
				{
					echo OSPHelper::generatePrice($property->curr,$property->price);
				}
				if($property->rent_time != "")
				{
					echo " /".Text::_($property->rent_time);
				}
			}
			echo "</span>";
			$property->price_information = ob_get_contents();
			ob_end_clean();
			
			$property->category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($property->id);
		}

		//return array($properties,$check_search);
		return $properties;
	}
	
	/**
	 * Get Itemid
	 *
	 * @param unknown_type $pid
	 */
	static function getItemid($pid){
		$db = Factory::getDbo();
		$needs = array();
		$needs[] = "property_details";
		$needs[] = $pid;
		$itemid  = OSPRoute::getItemid($needs);
		//echo $itemid;
		return $itemid;
	}
}
?>
