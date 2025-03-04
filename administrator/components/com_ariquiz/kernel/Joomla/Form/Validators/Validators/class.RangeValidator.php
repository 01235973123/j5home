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

define('ARI_RANGEVALIDATOR_TYPE_INT', 1);
define('ARI_RANGEVALIDATOR_TYPE_FLOAT', 2);

class RangeValidator extends AriValidator
{
	var $_minValue = null;
	var $_maxValue = null;
	var $_type = ARI_RANGEVALIDATOR_TYPE_INT;

	function __construct($xmlElement)
	{
		$min = AriXmlHelper::getAttribute($xmlElement, 'min');
		$max = AriXmlHelper::getAttribute($xmlElement, 'max');
		$dataType = AriXmlHelper::getAttribute($xmlElement, 'datatype');

		$this->_minValue = $min != null ? intval($min, 10) : null;
		$this->_maxValue = $max != null ? intval($max, 10) : null;

		$type = $dataType != null ? 'ARI_RANGEVALIDATOR_TYPE_' . strtoupper($dataType) : null;
		if ($type && defined($type))
			$this->_type = constant($type);

		parent::__construct($xmlElement);
	}

	function validate($value, $params)
	{
		$isValid = !empty($value) ? is_numeric($value) : true;
		if (!$isValid || is_null($value) || (is_string($value) && $value === ''))
			return $isValid;

		// check data type
		$dataType = $this->_type;
		switch ($dataType)
		{
			case ARI_RANGEVALIDATOR_TYPE_INT:
				$isValid = (intval($value) == $value);
				break;
					
			case ARI_RANGEVALIDATOR_TYPE_FLOAT:
				$isValid = (floatval($value) == $value);
				break;
		}
		
		$len = strlen($value);
		$minValue = $this->_minValue;
		$maxValue = $this->_maxValue;
		if ($len > 0 &&
			((!is_null($minValue) && $value < $minValue) ||
			(!is_null($maxValue) && $value > $maxValue)))
		{
			$isValid = false;
		}

		return $isValid;
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
		$doc->addScriptDeclaration(sprintf(
			'YAHOO.ARISoft.validators.validatorManager.addValidator(' .
			'	new YAHOO.ARISoft.validators.rangeValidator("%1$s",%2$s,%3$s,%4$s,%5$s))',
			$ctrlId,
			json_encode($this->_minValue),
			json_encode($this->_maxValue),
			json_encode($this->_type),
			$jsConfig
		));
	}
}