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

AriKernel::import('Joomla.Html.Parameter');
AriKernel::import('Joomla.Form.Validators.ValidatorManager');

class AriForm extends JObject
{
	var $_name;
	var $_formType;
	var $_validatorManagerType;
	var $_form;
	var $_path;
	var $_validatorManager;

	function __construct($name, $formType = 'AriParameter', $validatorManagerType = 'AriValidatorManager')
	{
		$this->_name = $name;
		$this->_formType = $formType;
		$this->_validatorManagerType = $validatorManagerType;
	}
	
	function load($path, $data = null)
	{
		$formType = $this->_formType;
		
		$this->_path = $path;
		$this->_form = new $formType($data, $path);
		$this->_validatorManager = new $this->_validatorManagerType($path);
	}
	
	function bind($data, $groups = array('_default'))
	{
		if (!is_array($groups))
			$groups = array($groups);
		
		foreach ($groups as $group)
			$this->_form->bind($data, $group);
	}
	
	function ignore($name, $group = '_default')
	{
		return $this->_form->ignore($name, $group);
	}
	
	function render($name = 'params', $group = '_default', $registerValidators = true, $emptyCase = false, $options = null)
	{
		if ($registerValidators)
		{
			$validationGroup = array();
			if (!empty($options['validationGroup']))
			{
				$validationGroup = is_array($options['validationGroup'])
					? $options['validationGroup']
					: explode(',', $options['validationGroup']);
			}
			
			$this->registerValidators($name, $group, $validationGroup);
		}
		
		$title = isset($options['title']) ? $options['title'] : '';

		$output = $this->_form->render($title, $name, $group, $options);
		if (empty($name) && $emptyCase)
		{
			$output = preg_replace('/name="\[([^\]]+)]"/i', 'name="$1"', $output);
		}
		
		return $output;
	}
	
	function renderSimple($name = 'params', $options = null)
	{
		return $this->render($name, '_default', true, false, $options);
	}
	
	function getFields($name = 'params', $group = '_default', $recursive = false)
	{
		$fields = $this->_form->getParams($name, $group, $recursive);
		
		return $fields;
	}
	
	function registerValidators($prefix = 'params', $group = '_default', $validationGroup = array())
	{
		$this->_validatorManager->registerValidators($prefix, $group, $validationGroup);
	}
	
	function toArray($groups = array('_default'), $recursive = true)
	{
		$data = array();

		if (!is_array($groups))
			$groups = array($groups);
			
		foreach ($groups as $group)
		{
			$elements = $this->_form->renderToArray('params', $group, $recursive);
			foreach ($elements as $element)
			{
				$data[$element[5]] = $element[4]; 
			}
		}

		return $data;
	}
	
	function validate($data, $groups = array('_default'))
	{
		$isValid = $this->_validatorManager->validate($data, $groups);
		if (!$isValid)
			$this->setError($this->_validatorManager->getError());
			
		return $isValid;
	}
	
	function get($key, $default = '', $group = '_default')
	{
		return $this->_form->get($key, $default, $group);
	}
	
	function setParamAttribute($key, $attribute, $val, $group = '_default')
	{
		return $this->_form->setParamAttribute($key, $attribute, $val, $group);
	}
}