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
AriKernel::import('Application.ARIQuiz.Questions.QuestionFactory');

class AriQuizControllerBankquestion extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
	}

	function add() 
	{
		$model =& $this->getModel();
		$question = $model->getTable();

		$questionTypeId = JRequest::getInt('questionTypeId');
		if ($questionTypeId > 0)
		{
			$question->QuestionTypeId = $questionTypeId;
			$question->QuestionVersion->QuestionTypeId = $questionTypeId;
		}	

		$this->_display($question);
	}

	function edit()
	{
		$questionId = JRequest::getInt('questionId');
		$model =& $this->getModel();
		$question = $model->getQuestion($questionId);
		if (is_null($question))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_QUESTION', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$questionId
				),
				500
			);
			return ;
		}

		$this->_display($question);
	}

	function changeQuestionType()
	{
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$data['QuestionVersion'] = JRequest::getVar('qv_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$model =& $this->getModel();
		$question = $model->getTable();
		$question->bind($data);
		
		$questionType = $question->getQuestionType();
		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$question->QuestionVersion->Data = $specificQuestion->getXml();

		$this->_display($question);
	}
	
	function _display($question)
	{
		$data = $this->getRequestData();
		if (!is_null($data))
			$question->bind($data);
		
		$questionVersion =& $question->QuestionVersion;

		if (empty($questionVersion->QuestionType->QuestionTypeId))
			$questionVersion->QuestionType->load($question->QuestionTypeId);

		$questionType =& $questionVersion->QuestionType;

		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionView =& $this->getSubView($questionType->ClassName, 'question');
		
		$files = $questionVersion->HasFiles
			? $questionVersion->getSimpleFiles()
			: array();

		$questionViewParams = array(
			'specificQuestion' => $specificQuestion,
			'questionData' => $questionVersion->Data,
			'isReadOnly' => false,
			'basedOnBank' => false,
			'files' => $files
		);

		$view =& $this->getView();
		$view->displayView($question, $questionViewParams, $questionView);
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$question = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=bankquestion&task=edit&questionId=' . $question->QuestionId . '&__MSG=COM_ARIQUIZ_COMPLETE_QUESTIONSAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$question = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=bankquestions&__MSG=COM_ARIQUIZ_COMPLETE_QUESTIONSAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
	}
	
	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$qvData = JRequest::getVar('qv_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$questionId = AriUtils::getParam($data, 'QuestionId', 0);
		
		if ($questionId > 0)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.edit', 'com_ariquiz.bankquestion.' . $questionId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);
	
				$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
			}
		}
		else
		{
			$categoryId = intval(AriUtils::getParam($data, 'QuestionCategoryId'), 10);
			if (!AriQuizHelper::isAuthorise('bankquestion.create', $categoryId > 0 ? 'com_ariquiz.bankcategory.' . $categoryId : 'com_ariquiz')) 
			{	
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
			}
		}		

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('question', 'bankquestion'));
		$form->bind($data);
		$form->bind($qvData, 'questionversion');

		$questionData = $form->toArray();
		$questionVersionData = $form->toArray('questionversion');

		if (!$form->validate($questionData) ||
			!$form->validate($questionVersionData, 'questionversion'))
		{
			if ($redirectOnError)
			{
				$data['QuestionVersion'] = $qvData;
				$this->setRequestData($data);
				if ($questionId > 0)
					$this->redirect('index.php?option=com_ariquiz&view=bankquestion&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&questionId=' . $questionId);
				else
					$this->redirect('index.php?option=com_ariquiz&view=bankquestion&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
			}

			return null;
		}

		$questionType = $model->getTable('Questiontype');
		$questionType->load($questionData['QuestionTypeId']);

		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);

		$questionVersionData['Data'] = $specificQuestion->getXml();
		$questionVersionData['Score'] = $specificQuestion->getMaximumQuestionScore(
			floatval($questionVersionData['Score']), 
			$questionVersionData['Data']
		);
		$questionVersionData['Files'] = JRequest::getVar('questionFiles', array(), 'default', 'none');

		$cleanData = $questionData;
		$cleanData['QuestionVersion'] = $questionVersionData;

		return $model->saveQuestion($cleanData);
	}
}