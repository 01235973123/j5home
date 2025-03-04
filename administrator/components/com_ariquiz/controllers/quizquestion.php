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

class AriQuizControllerQuizquestion extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizquestions');
	}
	
	function reload()
	{
		$data = $this->getRequestData();
		$model =& $this->getModel();
		$question = $model->getTable();
		if (!is_null($data))
			$question->bind($data);
		
		if ($question->BankQuestionId > 0)
			$question = $this->populateQuestionByBankQuestion($question, $question->BankQuestionId);

		$this->_display($question);	
	}

	function add() 
	{
		$model =& $this->getModel();
		$question = $model->getTable();
		
		$quizId = JRequest::getInt('quizId');
		if ($quizId > 0)
			$question->QuizId = $quizId;
			
		if ($quizId > 0)
		{
			if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('question.create')) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=quizzes');
			}
		}

		$questionType = JRequest::getString('newQuestionType');
		switch ($questionType)
		{
			case 'newQuestionQuestionType':
				$question = $this->populateQuestionByQuestionType($question, JRequest::getInt('questionTypeId'));
				break;

			case 'newQuestionQuestionTemplate':
				$question = $this->populateQuestionByQuestionTemplate($question, JRequest::getInt('questionTemplateId'));
				break;

			default:
				$config = AriQuizHelper::getConfig();
				$question = $this->populateQuestionByQuestionType($question, $config->get('DefaultQuestionType'));
		}
		
		if (is_null($question))
			throw new Exception(
				__CLASS__ . '::' . __FUNCTION__ . '(): Can\'t create a new question. Input parameters are incorrect.',
				500
			);

		$this->_display($question);
	}
	
	function edit()
	{
		$questionId = JRequest::getInt('questionId');
		if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId)) 
		{
			$quizId = JRequest::getInt('quizId');
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
		}
		
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
	
	function populateQuestionByQuestionType($question, $questionTypeId)
	{
		if ($questionTypeId < 1)
			return null;

		$question->QuestionTypeId = $questionTypeId;
		
		return $question;
	}
	
	function populateQuestionByQuestionTemplate($question, $templateId)
	{
		$templateModel =& $this->getModel('questiontemplate');
		$template = $templateModel->getTemplate($templateId);
		if (is_null($template))
			return null;

		$questionVersion =& $question->QuestionVersion; 
		$question->QuestionTypeId = $questionVersion->QuestionTypeId = $template->QuestionTypeId;
		$questionVersion->Data = $template->Data;		
		
		return $question;
	}
	
	function populateQuestionByBankQuestion($question, $bankQuestionId)
	{
		$bankModel =& $this->getModel('Bankquestion');
		$bankQuestion = $bankModel->getQuestion($bankQuestionId);
		if (is_null($bankQuestion))
			return null;

		$questionVersion =& $question->QuestionVersion;
		$bankQuestionVersion =& $bankQuestion->QuestionVersion;

		$question->BankQuestionId = $bankQuestion->QuestionId;
		$question->BankQuestion = $bankQuestion;
		$questionVersion->BankQuestionId = $bankQuestion->QuestionId;

		$questionVersion->QuestionTypeId = $bankQuestionVersion->QuestionTypeId;
		$questionVersion->QuestionType = $bankQuestionVersion->QuestionType;

		return $question;
	}

	function _display($question)
	{
		$data = $this->getRequestData();
		if (!is_null($data))
			$question->bind($data);

		$bankQuestion =& $question->BankQuestion;
		$bankQuestionVersion =& $bankQuestion->QuestionVersion;
		$questionVersion =& $question->QuestionVersion;
		$questionType =& $question->getQuestionType();
		
		if (empty($questionType->ClassName))
			throw new Exception(
				__CLASS__ . '::' . __FUNCTION__ . '(): Can\'t create a new question. Input parameters are incorrect.',
				E_USER_ERROR
			);

		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionView =& $this->getSubView($questionType->ClassName, 'question');

		$basedOnBank = $question->isBasedOnBankQuestion();
		$baseQuestionVersion = $basedOnBank
			? $bankQuestionVersion
			: $questionVersion;
		$files = $baseQuestionVersion->HasFiles
			? $baseQuestionVersion->getSimpleFiles()
			: array();

		$questionViewParams = array(
			'specificQuestion' => $specificQuestion,
			'questionData' => $basedOnBank ? $bankQuestionVersion->Data : $questionVersion->Data,
			'questionOverridenData' => $basedOnBank ? $questionVersion->Data : null,
			'basedOnBank' => $basedOnBank,
			'files' => $files
		);

		$view =& $this->getView();
		$view->displayView($question, $questionViewParams, $questionView);
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

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$question = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=quizquestion&task=edit&questionId=' . $question->QuestionId);
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$question = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $question->QuizId);
	}

	function cancel()
	{
		$quizId = JRequest::getInt('quizId');
		
		$this->redirect('index.php?option=com_ariquiz&view=quizquestions' . ($quizId > 0 ? '&quizId=' . $quizId : ''));
	}
	
	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$qvData = JRequest::getVar('qv_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$bankQuestionId = intval($data['BankQuestionId'], 10);
		$isBankBased = $bankQuestionId > 0;
		
		$questionId = intval(AriUtils::getParam($data, 'QuestionId', 0), 10);
		$quizId = intval(AriUtils::getParam($data, 'QuizId'), 10);
		
		if ($questionId > 0)
		{
			if (!AriQuizHelper::isAuthorise('question.edit', 'com_ariquiz.quizquestion.' . $questionId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
			}
		}
		else
		{
			$categoryId = intval(AriUtils::getParam($data, 'QuestionCategoryId'), 10);
			
			if ($categoryId > 0)
			{
				if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.questioncategory.' . $categoryId)) 
				{	
					throw new Exception(
						JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
						403
					);
					$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
				}
			}
			else
			{
				if (!AriQuizHelper::isAuthorise('question.create', 'com_ariquiz.quiz.' . $quizId))
				{
					throw new Exception(
						JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
						403
					);
					$this->redirect('index.php?option=com_ariquiz&view=quizquestions&quizId=' . $quizId);
				}
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('question', $isBankBased ? 'bankquizquestion' : 'question'));
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
				$this->errorRedirect($data);
			}

			return null;
		}
		
		if ($isBankBased)
		{
			$bankModel =& $this->getModel('bankquestion');
			$bankQuestion = $bankModel->getQuestion($bankQuestionId);
			if (is_null($bankQuestion))
			{
				if ($redirectOnError)
					$this->errorRedirect($data);

				return null;
			}

			$questionType =& $bankQuestion->QuestionVersion->QuestionType;
			$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);

			$questionVersionData['Data'] = $specificQuestion->getOverrideXml();
			if ($specificQuestion->isScoreSpecific())
				$questionVersionData['Score'] = 0;
			else
			{
				if (!isset($qvData['Score']))
					$questionVersionData['Score'] = 0;
					
				if (!isset($qvData['Penalty']))
					$questionVersionData['Penalty'] = 0;
			}
		}
		else
		{
			$questionType = $model->getTable('Questiontype');
			$questionType->load($questionData['QuestionTypeId']);
			
			$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
			$questionVersionData['Data'] = $specificQuestion->getXml();
			$questionVersionData['Score'] = $specificQuestion->getMaximumQuestionScore(
				floatval($questionVersionData['Score']), 
				$questionVersionData['Data']
			);
			
			$questionVersionData['Files'] = JRequest::getVar('questionFiles', array(), 'default', 'none');
		}
		
		$cleanData = $questionData;
		$cleanData['QuestionVersion'] = $questionVersionData;

		return $model->saveQuestion($cleanData);
	}
	
	function errorRedirect($data)
	{
		$questionId = AriUtils::getParam($data, 'QuestionId', 0);
		$this->setRequestData($data);
		if ($questionId > 0)
			$this->redirect('index.php?option=com_ariquiz&view=quizquestion&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&questionId=' . $questionId);
		else
			$this->redirect('index.php?option=com_ariquiz&view=quizquestion&task=reload&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
	}
}