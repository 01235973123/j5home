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

jimport('joomla.filter.filterinput');

class AriControllersResolver extends JObject 
{
	var $_path;
	var $_controllerPrefix;
	
	function __construct($config = array()) 
	{
		if (array_key_exists('path', $config)) 
			$this->_path = $config['path'];
		else
			$this->_path = JPATH_COMPONENT . DS . 'controllers' . DS;

		if (array_key_exists('controllerPrefix', $config))
			$this->_controllerPrefix = $config['controllerPrefix'];
		else
			$this->_controllerPrefix = 'AriController';
	}

	function &getController($controllerName, $config = array()) 
	{
		$controller = null;
		$filter = JFilterInput::getInstance();
		$controllerName = $filter->clean($controllerName, 'WORD');
		if (empty($controllerName)) 
			return $controller;

		$path = $this->_path . strtolower($controllerName) . '.php';
		if (@!file_exists($path))
			return $controller;

		require_once $path;

		$name = ucfirst($controllerName);
		$className = $this->_controllerPrefix . 'Controller' . $name;
		if (class_exists($className))
			$controller = new $className(
				array_merge(
					array(
						'name' => $name,
						'prefix' => $this->_controllerPrefix,
					),
					$config));

		return $controller;
	}

	function execute($controller, $task) 
	{
		$controller = $this->getController($controller);
		if ($controller) 
		{
			$ret = $controller->execute($task);
			$controller->redirect();
			
			return $ret;
		}
			
		return false;
	}
}