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
AriKernel::import('Joomla.Form.Form');
AriKernel::import('Joomla.Form.MassEditForm');
AriKernel::import('Utils.ArrayHelper');

class AriQuizControllerQuizzes extends AriController 
{
	var $_quizzesStateKey = 'com_ariquiz.dtQuizzes';
	var $_filter = null;

	function display($cachable = false, $urlparams = [])
	{
		$filter = $this->_getFilter(false, true);
		$filterPredicates = $filter->getConfigValue('filter');

		$view =& $this->getView();
		$view->displayView($filterPredicates);
	}
	
	function edit()
	{
		$quizId = JRequest::getVar('QuizId');
		if (is_array($quizId) && count($quizId) > 0)
			$quizId = $quizId[0];
			
		$quizId = intval($quizId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=quiz&task=edit&quizId=' . $quizId);
	}
	
	function add() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quiz&task=add');
	}

	function questions()
	{
		$quizId = JRequest::getVar('QuizId');
		if (is_array($quizId) && count($quizId) > 0)
			$quizId = $quizId[0];
			
		$quizId = intval($quizId, 10);
		
		if ($quizId > 0)
			$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
	}
	
	function ajaxGetQuizList()
	{
		$model =& $this->getModel();

		$filter = $this->_getFilter();

		$totalCnt = $model->getQuizCount($filter);
		$filter->fixFilter($totalCnt);

		$quizzes = $this->_extendQuizList(
			$model->getQuizList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($quizzes, $filter, $totalCnt);

		return $data;
	}
	
	function _extendQuizList($quizzes)
	{
		if (!is_array($quizzes))
			return $quizzes;

		for ($i = 0; $i < count($quizzes); $i++)
		{
			$quizId = $quizzes[$i]->QuizId;
			
			$quizzes[$i]->AllowEdit = AriQuizHelper::isAuthorise('quiz.edit', 'com_ariquiz.quiz.' . $quizId);
			$quizzes[$i]->AllowEditState = AriQuizHelper::isAuthorise('quiz.edit.state', 'com_ariquiz.quiz.' . $quizId);
		}

		return $quizzes;
	}
	
	function ajaxSingleDeactivate()
	{
		$quizId = JRequest::getInt('quizId');
		if ($quizId < 1 || !AriQuizHelper::isAuthorise('quiz.edit.state', 'com_ariquiz.quiz.' . $quizId)) 
		{
			return false;
		}

		$model =& $this->getModel();

		return $model->deactivateQuiz($quizId);
	}
	
	function ajaxSingleActivate()
	{
		$quizId = JRequest::getInt('quizId');
		if ($quizId < 1 || !AriQuizHelper::isAuthorise('quiz.edit.state', 'com_ariquiz.quiz.' . $quizId)) 
		{
			return false;
		}
		
		$model =& $this->getModel();

		return $model->activateQuiz($quizId);
	}

	function ajaxActivate()
	{
		$quizIdList = AriArrayHelper::toInteger(JRequest::getVar('QuizId'), 1);
		if (count($quizIdList) == 0) 
			return false;

		foreach ($quizIdList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('quiz.edit.state', 'com_ariquiz.quiz.' . $id)) 
			{
				// Prune items that you can't change.
				unset($quizIdList[$i]);
			}
		}
		
		$model =& $this->getModel();

		return $model->activateQuiz($quizIdList);
	}
	
	function ajaxDeactivate()
	{
		$quizIdList = AriArrayHelper::toInteger(JRequest::getVar('QuizId'), 1);
		if (count($quizIdList) == 0) 
			return false;

		foreach ($quizIdList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('quiz.edit.state', 'com_ariquiz.quiz.' . $id)) 
			{
				// Prune items that you can't change.
				unset($quizIdList[$i]);
			}
		}

		$model =& $this->getModel();

		return $model->deactivateQuiz($quizIdList);
	}
	
	function ajaxDelete()
	{
		$quizIdList = AriArrayHelper::toInteger(JRequest::getVar('QuizId'), 1);
		if (count($quizIdList) == 0) 
			return false;
			
		foreach ($quizIdList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('quiz.delete', 'com_ariquiz.quiz.' . $id)) 
			{
				// Prune items that you can't change.
				unset($quizIdList[$i]);
			}
		}

		$model =& $this->getModel();

		return $model->deleteQuiz($quizIdList);
	}
	
	function ajaxMassEdit()
	{
		$fields = JRequest::getVar('massParams', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('quiz', 'quiz'));

		if (!$massEditForm->validate($fields))
			return false;
	
		$extraFields = array();
		$extraParams = $massEditForm->getFields('params', 'extra', true);

		foreach ($extraParams as $extraParam)
		{
			$extraParamName = $extraParam[5];
			if (!isset($fields[$extraParamName]))
				continue ;
				
			$extraFields[$extraParamName] = $fields[$extraParamName];
			unset($fields[$extraParamName]);
		}

		$categoryId = intval(AriUtils::getParam($fields, 'CategoryList'), 10);
		if ($categoryId > 0)
		{
			if (!AriQuizHelper::isAuthorise('quiz.create', 'com_ariquiz.category.' . $categoryId))
				return false;
		}

		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuizId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('quiz.edit', 'com_ariquiz.quiz.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}

		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$model =& $this->getModel();

		return $model->update(
			$idList, 
			$fields,
			$extraFields,
			$userId
		);
	}
	
	function ajaxCopy()
	{
		$data = JRequest::getVar('copy', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$copyForm = new AriForm('common');
		$copyForm->load(AriQuizHelper::getFormPath('quiz', 'copy'));
		if (!$copyForm->validate($data))
			return false;
			
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuizId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('quiz.create', 'com_ariquiz.quiz.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}

		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$quizName = $data['QuizName'];
		
		$model =& $this->getModel();
		
		return $model->copy(
			$idList,
			$quizName,
			$userId);
	}
	
	function ajaxFilters()
	{
		$filterData = JRequest::getVar('filter', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$filter = $this->_getFilter(false, true);
		
		$filterPredicates = array();
		if (!empty($filterData['CategoryId']))
			$filterPredicates['CategoryId'] = $filterData['CategoryId'];
		
		if (!empty($filterData['Status']))
			$filterPredicates['Status'] = $filterData['Status'];
			
		$filter->setConfigValue('filter', $filterPredicates);
		$filter->store();
		
		return true;
	}

	function _getFilter($bindFromRequest = true, $restore = false)
	{
		if (!is_null($this->_filter))
			return $this->_filter;
			
		$this->_filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'QuizName', 
				'dir' => 'asc'
			), 
			$bindFromRequest, 
			$this->_quizzesStateKey);
			
		if ($restore)
			$this->_filter->restore();
			
		return $this->_filter;
	}
}