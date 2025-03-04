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

AriKernel::import('Utils.Utils');
AriKernel::import('Joomla.Controllers.ControllerBase');

define('ARI_CONTROLLER_AJAX_PREFIX', 'ajax');
define('ARI_CONTROLLER_REQUEST_DATA_KEY', 'rdKey');

class AriController extends AriControllerBase2
{
	var $_prefix = '';
	var $_requestKey = null;

	function __construct($config = array()) 
	{
		if (array_key_exists('prefix', $config))
			$this->_prefix = $config['prefix'];
		else
			$this->_prefix = $this->getPrefix();

		parent::__construct($config);
	}
	
	function getView($name = '', $type = 'html', $prefix = '', $config = array()) 
	{
		if (empty($prefix))
			$prefix = $this->getPrefix() . 'View';

		if (!array_key_exists('base_path', $config))
			$config['base_path'] = $this->basePath;
			
		return parent::getView($name, $type, $prefix, $config);
	}
	
	function getSubView($subName, $name = '', $type = 'html', $prefix = '', $config = array(), $path = null) 
	{
		if (empty($prefix))
			$prefix = $this->getPrefix() . 'SubView';

		if (!array_key_exists('base_path', $config))
			$config['base_path'] = $this->basePath;

		$result = null;

		// Clean the view name
		$subViewName = preg_replace('/[^A-Z0-9_]/i', '', $subName);
		$viewName	 = preg_replace('/[^A-Z0-9_]/i', '', $name);
		$classPrefix = preg_replace('/[^A-Z0-9_]/i', '', $prefix);
		$viewType	 = preg_replace('/[^A-Z0-9_]/i', '', $type);
		
		if (!array_key_exists('parent_prefix', $config))
			$config['parent_prefix'] = $viewName;

		// Build the view class name
		$viewClass = $classPrefix . $viewName . $subViewName;
		if (!class_exists($viewClass))
		{
			jimport('joomla.filesystem.path');
			
			if (empty($path))
			{
				$path = $this->paths;
				$path = $path['view'];
			}

			$viewFileName = $this->createFileName('view', array('name' => $viewName . '/' . $subViewName, 'type' => $viewType));

			$path = JPath::find(
				$path,
				$viewFileName
			);

			if ($path) {
				require_once $path;

				if (!class_exists($viewClass)) {
					throw new Exception(
						JText::_('Sub View class not found [class, file]:') . ' ' . $viewClass . ', ' . $path,
						500
					);
					return $result;
				}
			} else {
				return $result;
			}
		}

		$result = new $viewClass($config);
		return $result;
			
	}

	function &getModel($name = '', $prefix = '', $config = array()) 
	{
		if (empty($prefix))
			$prefix = $this->getPrefix() . 'Model';

		$ret = parent::getModel($name, $prefix, $config);
		return $ret;
	}
	
	function execute($task) 
	{
		$retValue = parent::execute($task);

		if ($this->isAjaxTask($task))
			$this->sendAjaxResponse($retValue);

		return $retValue;
	}

	function getPrefix() 
	{
		$prefix = $this->_prefix;

		if (empty($prefix)) 
		{
			$r = null;
			if (!preg_match( '/(.*)Controller/i', get_class($this), $r))
				throw new Exception('AriController::getPrefix() : Cannot get or parse class prefix.', 500);

			$prefix = $r[1];
		}

		return $prefix;		
	}
	
	function redirect($url = null, $msg = null, $type = 'message', $handleTmpl = true)
	{
		$requestKey = $this->getRequestKey(false);
		if ($requestKey)
		{
			$url = new JURI($url);
			$url->setVar(ARI_CONTROLLER_REQUEST_DATA_KEY, $requestKey);
			$url = $url->toString();
		}
		
		if ($url && $handleTmpl && JRequest::getString('tmpl'))
		{
			$url = new JURI($url);
			if (!$url->getVar('tmpl'))
				$url->setVar('tmpl', JRequest::getString('tmpl'));
			$url = $url->toString();
		}

		$this->setRedirect($url, $msg, $type);
		return parent::redirect();
	}
	
	function isAjaxTask($task)
	{
		return ($task && strpos($task, ARI_CONTROLLER_AJAX_PREFIX) === 0);
	}
	
	function sendAjaxResponse($data)
	{
		$data = json_encode($data);

		while (@ob_end_clean());
		// safari 6.0.x fix
		header('Cache-Control: no-cache');

		header('Content-type: text/html; charset=UTF-8');

		echo $data;
		exit();
	}

	function getRequestKey($generate = true)
	{
		if (empty($this->_requestKey) && $generate)
			$this->_requestKey = AriUtils::generateUniqueId();

		return $this->_requestKey;
	}

	function setRequestData($data)
	{
		$key = $this->getRequestKey();

		$mainframe =& JFactory::getApplication();
		$mainframe->setUserState($key, $data);
	}
	
	function getRequestData($clear = true)
	{
		$data = null;
		$key = JRequest::getString(ARI_CONTROLLER_REQUEST_DATA_KEY);
		if (empty($key))
			return $data;

		$mainframe =& JFactory::getApplication();
		$data = $mainframe->getUserState($key);

		if ($clear)
			$this->clearRequestData($key);

		return $data;
	}
	
	function clearRequestData($key = null)
	{
		if (is_null($key))
			$key = $this->getRequestKey(false);
		
		if (empty($key))
			return ;
			
		$mainframe =& JFactory::getApplication();
		$mainframe->setUserState($key, null);
	}
}