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
AriKernel::import('Joomla.Form.Validators.Validator');
AriKernel::import('Utils.Utils');
AriKernel::import('Xml.XmlHelper');

class AriValidatorManager extends JObject
{
	var $_path;
	var $_validators = null;
	var $_registerValidators = array();
	
	function __construct($path)
	{
		$this->_path = $path;
	}
	
	function isAcceptableValidator($node)
	{
		return true;
	}
	
	function getValidators($group = '_default')
	{
		$validators = array();
		
		$xml = AriXmlHelper::getXML($this->_path, true);

		if (empty($xml) || !isset($xml->document->validators))
			return $validators;

		$validatorsNodes = $xml->document->validators;
		$validatorsNode = null;

		foreach ($validatorsNodes as $sectionNode)
		{
			$aGroup = AriXmlHelper::getAttribute($sectionNode, 'group');
			if ($aGroup == $group || 
				($aGroup == null && $group == '_default'))
			{
				$validatorsNode = $sectionNode;
				break;
			}
		}

		if (is_null($validatorsNode) || !isset($validatorsNode->validator))
			return $validators;

		$addPath = AriXmlHelper::getAttribute($validatorsNode, 'addpath');
		foreach ($validatorsNode->validator as $validatorNode)
		{
			if (!$this->isAcceptableValidator($validatorNode))
				continue ;
			
			$validator = $this->createValidator($validatorNode, $addPath);
			if (!empty($validator))
				$validators[] = $validator;
		}

		return $validators;
	}
	
	function createValidator($xmlElement, $addPath)
	{
		$validator = null;
		$type = AriXmlHelper::getAttribute($xmlElement, 'type');
		
		$filter =& JFilterInput::getInstance();
		$type = $filter->clean($type, 'cmd');

		if (empty($type))
			return $validator;

		$validatorClass = ucfirst($type) . 'Validator';
		if (!class_exists($validatorClass))
		{
			$valName = ucfirst($type) . 'Validator';
			AriKernel::import('Joomla.Form.Validators.Validators.' . $valName);
			if ($addPath && !class_exists($validatorClass))
			{
				$valName = strtolower($type);
				$valPath = JPATH_ROOT . $addPath . DS . $valName . '.php';
				if (!file_exists($valPath))
					return $validator;

				require_once $valPath;
			}
		}

		if (!class_exists($validatorClass))
			return $validator;
		
		$validator = new $validatorClass($xmlElement);

		return $validator;
	}
	
	function registerValidators($prefix = 'params', $group = '_default', $validationGroup = array())
	{
		if ($this->isValidatorsRegistered($prefix . '.' . $group))
			return ;
		
		$validators = $this->getValidators($group);
		foreach ($validators as $validator)
		{
			$validator->registerScripts($prefix, $validationGroup);
		}
		
		$this->_registerValidators[$prefix . '.' . $group] = true;
	}
	
	function isValidatorsRegistered($key)
	{
		return !empty($this->_registerValidators[$key]);
	}
	
	function validate($params, $groups = array('_default'))
	{
		$isValid = true;
		
		if (!is_array($groups))
			$groups = array($groups);
			
		foreach ($groups as $group)
		{
			$validators = $this->getValidators($group);
			foreach ($validators as $validator)
			{
				$value = AriUtils::getParam($params, $validator->getFieldToValidate());
				if (!$validator->validate($value, $params))
				{
					$errorMessage = $validator->getErrorMessage();
					if (empty($errorMessage)) {
						$errorMessage = get_class($validator) . '::' . $validator->getFieldToValidate();
					}
					$this->setError($errorMessage);
					$isValid = false;
					break;
				}
			}
			
			if (!$isValid)
				break;
		}

		return $isValid;
	}
}