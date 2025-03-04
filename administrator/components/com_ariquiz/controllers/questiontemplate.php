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

class AriQuizControllerQuestiontemplate extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
	}

	function add() 
	{
		if (!AriQuizHelper::isAuthorise('questiontemplate.create'))
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
		}

		$model =& $this->getModel();
		$template = $model->getTable();

		$questionTypeId = JRequest::getInt('questionTypeId');
		if ($questionTypeId > 0)
			$template->QuestionTypeId = $questionTypeId;

		$this->_display($template);
	}

	function edit()
	{
		if (!AriQuizHelper::isAuthorise('questiontemplate.edit'))
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
		}
		
		$templateId = JRequest::getInt('templateId');
		$model =& $this->getModel();
		$template = $model->getTemplate($templateId);
		if (is_null($template))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_QUESTIONTEMPLATE', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$templateId
				),
				500
			);
			return ;
		}

		$this->_display($template);
	}
	
	function _display($template)
	{
		$data = $this->getRequestData();
		if (!is_null($data))
			$template->bind($data);

		$questionType =& $template->QuestionType;
		$questionType->load($template->QuestionTypeId);

		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$questionView =& $this->getSubView($questionType->ClassName, 'question');
		
		$questionViewParams = array(
			'specificQuestion' => $specificQuestion,
			'questionData' => $template->Data,
			'isReadOnly' => false,
			'basedOnBank' => false
		);

		$view =& $this->getView();
		$view->displayView($template, $questionViewParams, $questionView);
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$template = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=questiontemplate&task=edit&templateId=' . $template->TemplateId . '&__MSG=COM_ARIQUIZ_COMPLETE_QUESTIONTEMPLATESAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$template = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=questiontemplates&__MSG=COM_ARIQUIZ_COMPLETE_QUESTIONTEMPLATESAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
	}

	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);

		$templateId = AriUtils::getParam($data, 'TemplateId', 0);
		
		if ($templateId > 0)
		{
			if (!AriQuizHelper::isAuthorise('questiontemplate.edit'))
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('questiontemplate.create'))
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
			}
		}
		
		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('question', 'questiontemplate'));
		$commonSettingsForm->bind($data);
		$cleanData = $commonSettingsForm->toArray();
		if (!$commonSettingsForm->validate($cleanData))
		{
			if ($redirectOnError)
			{
				$this->setRequestData($data);
				if ($templateId > 0)
					$this->redirect('index.php?option=com_ariquiz&view=questiontemplate&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&templateId=' . $templateId);
				else
					$this->redirect('index.php?option=com_ariquiz&view=questiontemplate&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
			}

			return null;
		}
		
		$questionType = $model->getTable('QuestionType');
		$questionType->load($cleanData['QuestionTypeId']);
		
		$specificQuestion = AriQuizQuestionFactory::getQuestion($questionType->ClassName);
		$cleanData['Data'] = $specificQuestion->getXml();

		return $model->saveTemplate($cleanData);
	}
	
	function ajaxIsTemplateNameUnique()
	{
		$model =& $this->getModel();
		
		$templateName = JRequest::getString('templateName');
		$templateId = JRequest::getInt('templateId');
		
		return $model->isUniqueTemplateName($templateName, $templateId);
	}
}