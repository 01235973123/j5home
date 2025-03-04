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

class AriQuizControllerQuiz extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}
	
	function add() 
	{
		if (!AriQuizHelper::isAuthorise('quiz.create')) 
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=quizzes');
		}
		
		$model =& $this->getModel();
		$quiz = $model->getTable();

		$this->_display($quiz);
	}

	function edit()
	{
		$quizId = JRequest::getInt('quizId');
		if (!AriQuizHelper::isAuthorise('quiz.edit', 'com_ariquiz.quiz.' . $quizId)) 
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=quizzes');
		}

		$model =& $this->getModel();
		$quiz = $model->getQuiz($quizId);
		if (is_null($quiz))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_QUIZ', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$quizId
				),
				500
			);
			return ;
		}
		
		$this->_display($quiz);
	}
	
	function _display($quiz)
	{
		$data = $this->getRequestData();
		if (!is_null($data))
			$quiz->bind($data);

		$view =& $this->getView();
		$view->displayView($quiz, JRequest::getInt('quizActiveTab'));
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$quizActiveTab = JRequest::getInt('quizActiveTab');
		$quiz = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=quiz&task=edit&quizId=' . $quiz->QuizId . ($quizActiveTab > 0 ? '&quizActiveTab=' . $quizActiveTab : '') . '&__MSG=COM_ARIQUIZ_COMPLETE_QUIZSAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=quizzes&__MSG=COM_ARIQUIZ_COMPLETE_QUIZSAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}
	
	function ajaxIsQuizNameUnique()
	{
		$model =& $this->getModel();

		$quizName = JRequest::getString('quizName');
		$quizId = JRequest::getInt('quizId');

		return $model->isUniqueQuizName($quizName, $quizId);
	}

	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$extraData = JRequest::getVar('extra_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$metaData = JRequest::getVar('metadata_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$quizId = AriUtils::getParam($data, 'QuizId', 0);
		
		if ($quizId != 0)
		{
			if (!AriQuizHelper::isAuthorise('quiz.edit', 'com_ariquiz.quiz.' . $quizId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=quizzes');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('quiz.create')) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=quizzes');
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFormPath('quiz', 'quiz'));
		
		$dataGroups = array('_default', 'security', 'results');
		if (AriQuizHelper::isAuthorise('core.admin'))
			$dataGroups[] = 'rules';

		$form->bind($data, $dataGroups);
		$form->bind($extraData, 'extra');
		$form->bind($metaData, 'metadata');

		$cleanFormsData['params'] = $form->toArray($dataGroups);
		$cleanFormsData['extra_params'] = $form->toArray(array('extra'));
		$cleanFormsData['metadata_params'] = $form->toArray(array('metadata'));

		if (!$form->validate($cleanFormsData['params'], array('_default', 'security', 'results')) ||
			!$form->validate($cleanFormsData['extra_params'], 'extra') ||
			!$form->validate($cleanFormsData['metadata_params'], 'metadata'))
		{
			if ($redirectOnError)
			{
				$quizActiveTab = JRequest::getInt('quizActiveTab');
				$requestData = $data;
				$requestData['ExtraParams'] = $extraData;
				$requestData['Metadata'] = $metaData;
				$this->setRequestData($requestData);
				if ($quizId > 0)
					$this->redirect('index.php?option=com_ariquiz&view=quiz&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&quizId=' . $quizId . ($quizActiveTab > 0 ? '&quizActiveTab=' . $quizActiveTab : ''));
				else
					$this->redirect('index.php?option=com_ariquiz&view=quiz&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE' . ($quizActiveTab > 0 ? '&quizActiveTab=' . $quizActiveTab : ''));
			}

			return null;
		}

		return $model->saveQuiz(
			$cleanFormsData['params'], 
			$cleanFormsData['extra_params'],
			$cleanFormsData['metadata_params']
		);
	}
}