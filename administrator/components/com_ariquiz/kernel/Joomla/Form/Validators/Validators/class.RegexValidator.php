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

class RegexValidator extends AriValidator
{
	var $_regEx;
	var $_clientRegEx;

	function __construct($xmlElement)
	{
		$this->_regEx = AriXmlHelper::getAttribute($xmlElement, 'regex');
		$this->_clientRegEx = AriXmlHelper::getAttribute($xmlElement, 'client_regex');
		
		parent::__construct($xmlElement);
	}

	function validate($value, $params)
	{
		$isValid = true;
		if (!empty($value))
		{
			$regEx = $this->_regEx;
			$isValid = !(!preg_match($regEx, $value));
		}

		return $isValid;
	}
	
	function getClientRegEx()
	{
		return $this->_clientRegEx ? $this->_clientRegEx : $this->_regEx; 
	}

	function registerScript($prefix, $validationGroups = array())
	{
		$ctrlId = $prefix . $this->_fieldToValidate;
		$config = array(
			'prefix' => $prefix,
			'errorMessage' => $this->getErrorMessage(),
			'validationGroups' => $validationGroups
		);
		$jsConfig = json_encode($config);
		
		$clientRegEx = $this->getClientRegEx();

		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(
			'YAHOO.ARISoft.validators.validatorManager.addValidator(' .
			'	new YAHOO.ARISoft.validators.regexpValidator("' . $ctrlId . '",' . $clientRegEx . ',' . $jsConfig . '))');
		
	}
}