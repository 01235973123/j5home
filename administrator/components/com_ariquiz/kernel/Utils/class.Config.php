<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

jimport('joomla.filesystem.file');

AriKernel::import('Utils.Utils');

class AriConfig extends JObject
{
	var $_config;
	var $_defaultConfig;
	var $_groups = array('_default');

	var $_table;
	var $_modelPath;
	var $_cachePath;
	
	function getGroups()
	{
		return $this->_groups;
	}

	function get($name, $def = null)
	{
		$config = $this->getConfig();
		if (isset($config[$name]))
			return $config[$name];
			
		$defConfig = $this->getDefaultConfig();

		return AriUtils::getParam($defConfig, $name, $def);
	}
	
	function set($name, $val = null)
	{
		$this->bind(array($name => $val));
	}

	function getCacheNS()
	{
		return '_Cache_' . __CLASS__;
	}
	
	function getConfig($reload = false)
	{
		if (!is_null($this->_config) && !$reload)
			return $this->_config;
			
		if ($this->_cachePath && JFile::exists($this->_cachePath))
		{
			require_once $this->_cachePath;
			
			$cacheNS = $this->getCacheNS();
			if (isset(${$cacheNS}))
				$this->_config = ${$cacheNS};

			if (!is_null($this->_config))
				return $this->_config;
		}

		$db =& JFactory::getDBO();
		$db->setQuery(
			sprintf(
				'SELECT ParamName,ParamValue FROM `%1$s`',	
				$this->_table
			)
		);
		
		$config = $db->loadAssocList('ParamName');
			
		$this->_config = array();
		if (is_array($config))
			foreach ($config as $key => $val)
				$this->_config[$key] = $val['ParamValue'];
				
		$this->cache($this->_config);
			
		return $this->_config;
	}

	function reload()
	{
		$this->getConfig(true);
	}

	function bind($data, $merge = true)
	{	
		$config = array();
		if ($merge)
		{
			$config = $this->getConfig();
			$config = array_merge($config, $data);
		}
		else 
		{
			$config = $data;
		}

		$this->_config = $config;
	}
	
	function save($strict = true)
	{
		$config = $this->getConfig();

		$db = JFactory::getDBO();
		$db->setQuery(
			sprintf(
				'TRUNCATE TABLE `%1$s`',	
				$this->_table
			)
		);
		$db->execute();
		
		if ($strict)
		{
			$defaultConfig = $this->getDefaultConfig();
			if (!is_null($defaultConfig))
			{
				$newConfig = array();
				foreach ($defaultConfig as $key => $val)
					$newConfig[$key] = isset($config[$key]) ? $config[$key] : $val;
					
				$config = $newConfig;
			}
		}
		
		if (count($config) == 0)
			return ;

		$query = array();
 		foreach ($config as $key => $val)
			$query[] = sprintf('(%1$s,%2$s)',
				$db->Quote($key),
				$db->Quote($val));
				
		$query = sprintf('INSERT INTO `%1$s` (ParamName,ParamValue) VALUES %2$s',
			$this->_table,
			join(',', $query));
		$db->setQuery($query);
		$db->execute();
			
		$this->_config = $config;
		
		$this->cache($this->_config);
	}
	
	function getDefaultConfig()
	{
		if (empty($this->_modelPath))
			return null;
			
		if (!is_null($this->_defaultConfig))
			return $this->_defaultConfig;
			
		AriKernel::import('Joomla.Form.Form');
		$form = new AriForm('common');
		$form->load($this->_modelPath);
		
		$this->_defaultConfig = $form->toArray($this->getGroups());
		
		return $this->_defaultConfig;
	}
	
	function cache($config)
	{
		if (empty($this->_cachePath))
			return ;

		$content = sprintf("<?php\r\n(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');\r\n\$%1\$s=%2\$s;",
			$this->getCacheNS(),
			var_export($config, true)
		);

		JFile::write($this->_cachePath, $content);
	}
	
	function removeCache()
	{
		if ($this->_cachePath && @file_exists($this->_cachePath))
			JFile::delete($this->_cachePath);
	}
}

class AriConfigFactory
{
	static public function getInstance($type, $path = null)
	{
		static $configList = array();
		
		if (empty($type))
			throw new Exception(
				'AriConfigFactory::getInstance() : "Type" is not specified.',
				500
			);
		
		if (!isset($configList[$type]))
		{
			if (!empty($path))
				AriKernel::import($path);
			
			$config = new $type();
			$configList[$type] = $config;
		}

		return $configList[$type];
	}
}