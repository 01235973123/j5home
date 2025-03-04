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

class TexttemplatenameValidator extends AriValidator
{
	var $_templateGroup = '';
	var $_releatedElement = 'TemplateId';
	
	function __construct(&$xmlElement)
	{
		$this->_templateGroup = AriXmlHelper::getAttribute($xmlElement, 'group');
		$relEl = AriXmlHelper::getAttribute($xmlElement, 'related_element');
		if ($relEl)
			$this->_releatedElement = $relEl;
		
		parent::__construct($xmlElement);
	}
	
	function validate($value, $params)
	{
		$templateId = AriUtils::getParam($params, 'TemplateId');
		$templateModel = AriModel::getInstance('Texttemplate', 'AriQuizModel');
		$isValid = $templateModel->isUniqueTemplateName($value, $this->_templateGroup, $templateId);

		return $isValid;
	}
	
	function registerScript($prefix, $validationGroups = array())
	{
		$this->registerAssets();
		
		$ctrlId = $prefix . $this->_fieldToValidate;
		$config = array(
			'prefix' => $prefix,
			'relatedElement' => $this->_releatedElement,
			'validationGroups' => $validationGroups,
			'errorMessage' => $this->getErrorMessage(),
			'templateGroup' => $this->_templateGroup
		);
		$jsConfig = json_encode($config);

		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(
			'YAHOO.ARISoft.validators.validatorManager.addValidator(' .
			'	new YAHOO.ARISoft.Quiz.validators.isTexttemplateNameUnique("' . $ctrlId . '",' . $jsConfig . '))');
		
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/validators/assets/';
		$doc->addScript($uri . 'texttemplatename.js');
	}
}