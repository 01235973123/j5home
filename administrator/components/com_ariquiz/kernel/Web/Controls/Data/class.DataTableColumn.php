<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class AriDataTableControlColumn extends JObject 
{
	var $_configProps = array(
		'key' => null,
		'field' => null,
		'label' => null,
		'sortable' => false,
		'resizable' => false,
		'formatter' => null,
		'minWidth' => null,
		'hidden' => false,
		'width' => null,
		'sortOptions' => null,
		'className' => ''
	);
	
	var $_ignoredProps = array('headerWidth');
	
	function __construct($config)
	{
		if (is_array($config))
			$this->_configProps = array_merge($this->_configProps, $config);
	}
	
	function getConfigValue($key, $defValue = null)
	{
		return isset($this->_configProps[$key]) ? $this->_configProps[$key] : $defValue; 
	}
	
	function getDef()
	{
		$jsDef = array();
		foreach ($this->_configProps as $key => $value)
		{
			if (in_array($key, $this->_ignoredProps)) continue;
			$isNeedEncode = ($key != 'formatter' || empty($value));
			$jsDef[] = $key . ': ' . ($isNeedEncode ? json_encode($value) : $value);
		}
		
		return '{' . join(',', $jsDef) . '}';
	}
}