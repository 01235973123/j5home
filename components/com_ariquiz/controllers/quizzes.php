<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Controllers.Controller');

class AriQuizControllerQuizzes extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function display() 
	{
		$model =& $this->getModel('Quizzes');
		$categoriesModel =& $this->getModel('Categories');
		$view =& $this->getView();

		$app = JFactory::getApplication();
		$params = $app->getParams();

		$sortField = 'QuizName';
		$sortDir = 'asc';
		$parentCategoryId = JRequest::getInt('categoryId');;
		if (!empty($params))
		{
			$field = $params->get('sortfield');
			if (in_array($field, array('QuizName', 'Created')))
				$sortField = $field;

			$dir = strtolower($params->get('sortdir'));
			if (in_array($dir, array('asc', 'desc')))
				$sortDir = $dir;
		}
		
		$filterPredicates = array('Status' => ARIQUIZ_QUIZ_STATUS_ACTIVE);
		if ($parentCategoryId > 0)
		{
			$filterPredicates['CategoryId'] = $parentCategoryId;
			$filterPredicates['IncludeSubcategories'] = true;
		}

		$filter = new AriDataFilter(
			array(
				'sortField' => 'Q.' . $sortField,
				'sortDirection' => $sortDir,
				'filter' => $filterPredicates
			)
		);
		

		$quizzes = $model->getQuizList($filter);
		$quizzes = $this->_normalizeQuizzes($quizzes, $sortField, $sortDir);
		$categories = array_keys($quizzes);
		$categories = $categoriesModel->getCategoriesTree($categories, $parentCategoryId);
		
		$view->display($quizzes, $categories);
	}
	
	function _normalizeQuizzes($quizzes, $sortField, $sortDir)
	{
		if (!is_array($quizzes) || count($quizzes) == 0)
			return array();
			
		if ($sortField == 'QuizName')
			$quizzes = AriUtils::sortAssocArray($quizzes, $sortField, $sortDir, 'natural');

		$nQuizzes = array();		
		foreach ($quizzes as $quiz)
		{
			if (!isset($nQuizzes[$quiz->CategoryId]))
				$nQuizzes[$quiz->CategoryId] = array();
				
			$nQuizzes[$quiz->CategoryId][] = $quiz;
		}

		return $nQuizzes;
	}
}