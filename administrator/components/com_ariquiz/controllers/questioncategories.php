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
AriKernel::import('Web.Controls.Data.MultiPageDataTable');
AriKernel::import('Data.DataFilter');
AriKernel::import('Utils.ArrayHelper');

class AriQuizControllerQuestioncategories extends AriController 
{
	var $_questionsStateKey = 'com_ariquiz.dtQuestionCategories';
	
	function display($cachable = false, $urlparams = []) 
	{
		$quizId = JRequest::getInt('quizId');
		$quiz = null;
		if ($quizId > 0)
		{
			$quizModel = $this->getModel('Quiz');
			$quiz = $quizModel->getQuiz($quizId);
		}

		$view =& $this->getView();
		$view->displayView($quiz);
	}
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		$quizId = JRequest::getInt('quizId');		
		
		$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=add' . ($quizId > 0 ? '&quizId=' . $quizId : ''));
	}
	
	function edit()
	{
		$quizId = JRequest::getInt('quizId');
		$categoryId = JRequest::getVar('QuestionCategoryId');
		if (is_array($categoryId) && count($categoryId) > 0)
			$categoryId = $categoryId[0];
			
		$categoryId = intval($categoryId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=edit&categoryId=' . $categoryId . ($quizId > 0 ? '&quizId=' . $quizId : ''));
	}
	
	function categoryedit()
	{
		$quizId = JRequest::getInt('quizId');
		$categoryId = JRequest::getInt('categoryId');
		
		if (!AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.questioncategory.' . $categoryId)) 
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questioncategories');
		}
		
		$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=edit&categoryId=' . $categoryId . ($quizId > 0 ? '&quizId=' . $quizId : ''));
	}

	function ajaxGetCategoryList()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'CategoryName', 
				'dir' => 'asc'
			), 
			true,
			$this->_questionsStateKey);
			
		$quizId = JRequest::getInt('quizId');

		$totalCnt = $model->getCategoryCount($quizId, $filter);
		$filter->fixFilter($totalCnt);

		$categories = $this->_extendCategoryList(
			$model->getCategoryList($quizId, $filter)
		);	
		$data = AriMultiPageDataTableControl::createDataInfo($categories, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendCategoryList($data)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->QuestionCategoryId;
			
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.questioncategory.' . $id);
		}

		return $data;
	}

	function ajaxDelete()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionCategoryId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.delete', 'com_ariquiz.questioncategory.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}
		
		$deleteQuestions = JRequest::getBool('deleteQuestions');
		$model =& $this->getModel();

		return $model->deleteCategory($idList, $deleteQuestions);
	}
	
	function ajaxMassEdit()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionCategoryId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.questioncategory.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}
		
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$fields = JRequest::getVar('massParams', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$model =& $this->getModel();

		return $model->update(
			AriQuizHelper::getDataConfigPath(), 
			$idList, 
			$fields,
			$userId
		);
	}
}