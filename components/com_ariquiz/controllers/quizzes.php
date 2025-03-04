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
			$config['model_path'] = JPATH_ROOT . '/administrator/components/com_ariquiz/models';

		parent::__construct($config);
	}
	
	function display($cachable = false, $urlparams = []) 
	{
		$model =& $this->getModel('Quizzes');
		$userQuizModel = $this->getModel('Userquiz');
		$categoriesModel =& $this->getModel('Categories');
		$view =& $this->getView();

		$user = JFactory::getUser();
		$userId = $user->get('id');
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$sortField = 'QuizName';
		$sortDir = 'asc';
		$parentCategoryId = $this->input->getInt('categoryId');
        $hideNotPermittedQuizzes = false;
		if (!empty($params))
		{
			$field = $params->get('sortfield');
			if (in_array($field, array('QuizName', 'Created')))
				$sortField = $field;

			$dir = strtolower($params->get('sortdir'));
			if (in_array($dir, array('asc', 'desc')))
				$sortDir = $dir;

            $hideNotPermittedQuizzes = (bool)$params->get('hideNotPermittedQuizzes');
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
        if ($hideNotPermittedQuizzes)
            $quizzes = $this->_filteredQuizzes($quizzes);
		$quizIdList = $this->_getQuizIdList($quizzes);
		$quizzes = $this->_normalizeQuizzes($quizzes, $sortField, $sortDir);
		$statusList = $userId > 0
			? $userQuizModel->getQuizzesStatus($userId, $quizIdList)
			: array();

		$categories = array_keys($quizzes);
		$categories = $categoriesModel->getCategoriesTree($categories, $parentCategoryId);

		$view->displayView($quizzes, $categories, $statusList);
	}

    function _filteredQuizzes($quizzes)
    {
        if (!is_array($quizzes) || count($quizzes) == 0)
            return $quizzes;

        $filteredQuizzes = array();
        $user = JFactory::getUser();
        $userViewLevels = $user->getAuthorisedViewLevels();
        $categoriesModel = $this->getModel('Categories');
        $categoriesAccessLevels = $categoriesModel->getCategoriesAccessLevels();

        foreach ($quizzes as $quiz)
        {
            $quizAccessLevel = $quiz->Access;
            if ($quizAccessLevel == -1)
                $quizAccessLevel = isset($categoriesAccessLevels[$quiz->CategoryId]) ? $categoriesAccessLevels[$quiz->CategoryId]->Access : -1;

            if (in_array($quizAccessLevel, $userViewLevels))
                $filteredQuizzes[] = $quiz;
        }

        return $filteredQuizzes;
    }
	
	function _getQuizIdList($quizzes)
	{
		$quizIdList = array();
		
		if (empty($quizzes))
			return $quizIdList;
			
		foreach ($quizzes as $quiz)
		{
			$quizIdList[] = $quiz->QuizId;
		}
		
		return $quizIdList;
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