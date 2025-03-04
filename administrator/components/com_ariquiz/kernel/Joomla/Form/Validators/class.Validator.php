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

AriKernel::import('Xml.XmlHelper');

class AriValidator extends JObject
{
	var $_xmlElement;
	var $_errorMessage;
	var $_fieldToValidate;
	var $_groups = array();
	var $_clientValidation = true;

	function __construct(&$xmlElement)
	{
		$this->_xmlElement = $xmlElement;
		$clientValidation = AriXmlHelper::getAttribute($xmlElement, 'client_validation');
		
		$errMsg = AriXmlHelper::getAttribute($xmlElement, 'error_message');
		if ($errMsg)
			$this->setErrorMessage($errMsg);

		$this->_fieldToValidate = AriXmlHelper::getAttribute($xmlElement, 'validate');
		if (!is_null($clientValidation) && empty($clientValidation))
			$this->_clientValidation = false; 
	}
	
	function setErrorMessage($errorMessage, $translate = true)
	{
		if ($translate)
			$errorMessage = JText::_($errorMessage);
			
		$this->_errorMessage = $errorMessage;
	}
	
	function getErrorMessage()
	{
		return $this->_errorMessage;
	}
	
	function getFieldToValidate()
	{
		return $this->_fieldToValidate;
	}
	
	function validate($value, $params)
	{
		return true;
	}

	function registerScripts($prefix = 'params', $validationGroups = array())
	{
		if (!$this->_clientValidation)
			return ;
			
		$this->registerScript($prefix, $validationGroups);
	}
	
	function registerScript($prefix, $validationGroups = array())
	{		
	}
}