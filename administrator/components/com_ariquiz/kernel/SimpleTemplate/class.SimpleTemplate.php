<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

 (defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Utils.Utils');

class AriSimpleTemplate
{
	public static function &getFilterStore()
	{
		static $filterStore = null;
		
		if ($filterStore == null)
		{
			$filterStore = new AriSimpleTemplateFilterStore();
		}
		
		return $filterStore;
	}
	
	public static function registerFilter($name, $filter)
	{
		$store = self::getFilterStore();
		$store->registerFilter($name, $filter);
	}
	
	public static function applyFilter($name, $value, $params = null)
	{
		$store = self::getFilterStore();
		
		return $store->applyFilter($name, $value, $params);
	}
	
	public static function parse($template, $params, $removeUnrecognized = false)
	{
		if (empty($params)) return $template;
		
		$paramsRegExp = '/\{\$([^}\|]+)((\|[^}\|]+)*)}/si';
		
		$matches = array();
		@preg_match_all($paramsRegExp, $template, $matches, PREG_SET_ORDER);

		if (empty($matches)) return $template;

		$search = array();
		$replace = array();
		foreach ($matches as $match)
		{
			$value = self::getParamValue($match[1], $params);
			if (is_null($value) && !$removeUnrecognized)
					continue;

			$value = self::applyFilters($value, !empty($match[2]) ? $match[2] : '');

			$search[] = $match[0];
			$replace[] = $value;
		}

		return str_replace($search, $replace, $template);
	}
	
	public static function applyFilters($value, $filterStr)
	{
		$filters = preg_split('/(?<!\\\)\|/', $filterStr);
		if (empty($filters))
            return $value;

        $filters = str_replace('\|', '|', $filters);
		
		foreach ($filters as $filter) 
		{
			if (empty($filter)) continue;
			
			$filterInfo = preg_split('/(?<!\\\):/', $filter);
            $filterInfo = str_replace('\:', ':', $filterInfo);
			$filterName = $filterInfo[0];
			array_shift($filterInfo);
			
			$value = self::applyFilter($filterName, $value, $filterInfo);
		}
		
		return $value;
	}
	
	public static function getParamValue($key, $params)
	{
		$value = null;
		
		if (!$key) return $value;
		
		$keys = array();
		if (strpos($key, ':') !== false)
		{
			$keys = explode(':', $key);
		}
		else
		{
			$keys = array($key);
		}
		
		$value = $params;
		foreach ($keys as $cKey)
		{
			$value = AriUtils::getParam($value, $cKey, null);

			if (is_null($value)) break;
		}
		
		if (is_array($value)) $value = null;

		return $value;
	}
}

class AriSimpleTemplateFilterStore
{
	var $_filters;

	function applyFilter($name, $value, $params = null)
	{
		if ($this->filterExists($name))
		{
			$filter = $this->getFilter($name);
			if (!is_array($params)) $params = array();
			array_unshift($params, $value);
			$filterInstance = new $filter();
			$value = call_user_func_array(array($filterInstance, 'parse'), $params); 
		}

		return $value;
	}
	
	function filterExists($name)
	{
		return isset($this->_filters[$name]);
	}
	
	function registerFilter($name, $filter)
	{
		$this->_filters[$name] = $filter;
	}

	function getFilter($name)
	{
		$filter = null;
		if ($this->filterExists($name))
		{
			$filter = $this->_filters[$name];
		}

		return $filter;
	}
}

AriKernel::import('SimpleTemplate.Filters.LoadAll');