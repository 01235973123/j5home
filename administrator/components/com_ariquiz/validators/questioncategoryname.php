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

class QuestioncategorynameValidator extends AriValidator
{
	function validate($value, $params)
	{
		$categoryId = AriUtils::getParam($params, 'QuestionCategoryId');
		$quizId = AriUtils::getParam($params, 'QuizId');
		$categoryModel =& AriModel::getInstance('Questioncategory', 'AriQuizModel');
		$isValid = $categoryModel->isUniqueCategoryName($value, $quizId, $categoryId);

		return $isValid;
	}

	function registerScript($prefix, $validationGroups = array())
	{
		$this->registerAssets();
		
		$ctrlId = 'params' . $this->_fieldToValidate;
		$config = array(
			'prefix' => $prefix,
			'validationGroups' => $validationGroups,
			'errorMessage' => $this->getErrorMessage()
		);
		$jsConfig = json_encode($config);

		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(
			'YAHOO.ARISoft.validators.validatorManager.addValidator(' .
			'	new YAHOO.ARISoft.Quiz.validators.isQuestionCategoryNameUnique("' . $ctrlId . '",' . $jsConfig . '))');
		
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/validators/assets/';
		$doc->addScript($uri . 'questioncategoryname.js');
	}
}