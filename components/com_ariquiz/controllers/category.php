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

class AriQuizControllerCategory extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function display() 
	{
		$categoryModel =& $this->getModel('Category');
		$quizzesModel =& $this->getModel('Quizzes');
		$view =& $this->getView();
		
		$categoryId = JRequest::getInt('categoryId');
		$category = $categoryModel->getCategory($categoryId);
		
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$sortField = 'QuizName';
		$sortDir = 'asc';
		if (!empty($params))
		{
			$field = $params->get('sortfield');
			if (in_array($field, array('QuizName', 'Created')))
				$sortField = $field;

			$dir = strtolower($params->get('sortdir'));
			if (in_array($dir, array('asc', 'desc')))
				$sortDir = $dir;
		}

		$filter = new AriDataFilter(
			array(
				'sortField' => 'Q.' . $sortField,
				'sortDirection' => $sortDir,
				'filter' => array(
					'Status' => ARIQUIZ_QUIZ_STATUS_ACTIVE,
					'CategoryId' => $categoryId
				)
			)
		);
		$quizzes = $quizzesModel->getQuizList($filter);

		$view->display($category, $quizzes);
	}
}