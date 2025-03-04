<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

define('ARI_DATAFILTER_SORT_ASC', 'asc');
define('ARI_DATAFILTER_SORT_DESC', 'desc');
define('ARI_DATAFILTER_REQUEST_OFFSET', 'adtStart');
define('ARI_DATAFILTER_REQUEST_LIMIT', 'adtLimit');
define('ARI_DATAFILTER_REQUEST_SORT', 'adtSort');
define('ARI_DATAFILTER_REQUEST_DIR', 'adtDir');
define('ARI_DATAFILTER_REQUEST_INIT', 'adtInit');

class AriDataFilter extends JObject
{
	var $_config = array(
		'sortField' => null,
		'sortDirection' => ARI_DATAFILTER_SORT_ASC,
		'secondarySorting' => null,
		'startOffset' => 0,
		'limit' => null,
		'filter' => null);
	var $_allowSortFields = null;
	var $_persistanceKey;

	function __construct(
		$config = null, 
		$bindFromRequest = false, 
		$persistanceKey = null, 
		$allowSortFields = null
	)
	{
		$this->_persistanceKey = $persistanceKey;
		$this->_allowSortFields = $allowSortFields;

		if (is_array($config))
			$this->_config = array_merge($this->_config, $config);

		if ($bindFromRequest)
		{ 
			$this->restore();

			if (!JRequest::getVar(ARI_DATAFILTER_REQUEST_INIT))
			{
				$this->bindFromRequest();
				$this->store();
			}
		}
	}
	
	function bindFromRequest()
	{
		$startOffset = JRequest::getInt(ARI_DATAFILTER_REQUEST_OFFSET);
		if ($startOffset < 0) 
			$startOffset = 0;
		
		$limit = JRequest::getInt(ARI_DATAFILTER_REQUEST_LIMIT, 10);
		if ($limit < 0) 
			$limit = 10;
		
		$sortField = JRequest::getString(ARI_DATAFILTER_REQUEST_SORT);
		
		$sortDirection = JRequest::getCmd(ARI_DATAFILTER_REQUEST_DIR);
		if (empty($sortDirection))
			$sortDirection = ARI_DATAFILTER_SORT_ASC;
			
		$this->setConfigValue('startOffset', $startOffset);
		$this->setConfigValue('limit', $limit);
		$this->setConfigValue('sortField', $sortField);
		$this->setConfigValue('sortDirection', $sortDirection);

		$this->_fix();
	}

	function store($persistanceKey = null)
	{
		if (empty($persistanceKey)) 
			$persistanceKey = $this->_persistanceKey;
		if (empty($persistanceKey)) 
			return ;

		$filter = $this->_config['filter'];
		if (is_array($filter) || is_object($filter))
			$this->_config['filter'] = serialize($filter);

		$mainframe =& JFactory::getApplication();
		$mainframe->setUserState($persistanceKey, $this->_config);
		
		$this->_config['filter'] = $filter;
	}
	
	function restore($persistanceKey = null)
	{
		if (empty($persistanceKey)) 
			$persistanceKey = $this->_persistanceKey;
		if (empty($persistanceKey)) 
			return ;

		$mainframe =& JFactory::getApplication();
		$props = $mainframe->getUserState($persistanceKey);
		if (!empty($props['filter'])) $props['filter'] = @unserialize($props['filter']);
		if (empty($props['sortField'])) $props['sortField'] = $this->getConfigValue('sortField');
		if (empty($props['sortDirection'])) $props['sortDirection'] = $this->getConfigValue('sortDirection');

		if (is_array($props))
			$this->_config = array_merge($this->_config, $props);

		$this->_fix();
	}
	
	function _fix()
	{
		$sortField = $this->getConfigValue('sortField');
		$allowSortFields = $this->_allowSortFields;
		if ($sortField && is_array($allowSortFields) && !in_array($sortField, $allowSortFields))
			$this->setConfigValue('sortField', null);
	}

	function fixFilter($cnt)
	{
		$limit = intval($this->getConfigValue('limit'), 10);
		if (empty($cnt) || $limit == 0)
		{
			$this->setConfigValue('startOffset', 0);
		}
		else
		{
			$startOffset = intval($this->getConfigValue('startOffset'), 10);	
			if ($cnt <= $startOffset)
			{
				$startOffset = $limit * (ceil($cnt / $limit) - 1); 
			}
			else
			{
				$startOffset = $limit * floor($startOffset / $limit);
			}
			$this->setConfigValue('startOffset', $startOffset);
/*
			if ($limit > $cnt)
				$this->setConfigValue('limit', $cnt);*/
		}
	}
	
	function setConfigValue($key, $value = null)
	{
		$this->_config[$key] = $value;
	}

	function getConfigValue($key, $default = null)
	{
		return isset($this->_config[$key]) ? $this->_config[$key] : $default;
	}
	
	function applyToQuery($query)
	{
		$sortField = $this->getConfigValue('sortField');
		if (!empty($sortField))
		{
			$query->order($sortField . ' ' . $this->getConfigValue('sortDirection'));
				
			$secondarySorting = $this->getConfigValue('secondarySorting');
			if ($secondarySorting)
			{
				foreach ($secondarySorting as $sortingItem)
				{
					$sortField = AriUtils::getParam($sortingItem, 'sortField', null);
					if (empty($sortField)) 
						continue;
					$sortDir = strtolower(AriUtils::getParam($sortingItem, 'sortDirection', ''));
					if ($sortDir && $sortDir != ARI_DATAFILTER_SORT_ASC && $sortDir != ARI_DATAFILTER_SORT_DESC) 
						$sortDir = '';
					
					$query->order($sortField . ' ' . $sortDir);
				}
			}
		}

		return $query;
	}
}