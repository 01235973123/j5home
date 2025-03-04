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

class RequiredValidator extends AriValidator
{
	function validate($value, $params)
	{
		return is_string($value) ? strlen(trim($value)) > 0 : !is_null($value);
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

		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(
			'YAHOO.ARISoft.validators.validatorManager.addValidator(' .
			'	new YAHOO.ARISoft.validators.requiredValidator("' . $ctrlId . '",' . $jsConfig . '))');		
	}
}