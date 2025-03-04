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

class AriQuizControllerMailtemplate extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
	}

	function add() 
	{
		if (!AriQuizHelper::isAuthorise('mailtemplate.create'))
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
		}
		
		$model =& $this->getModel();
		$template = $model->getTable();

		$this->_display($template);
	}
	
	function edit()
	{
		if (!AriQuizHelper::isAuthorise('mailtemplate.edit'))
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
		}

		$templateId = JRequest::getInt('templateId');
		$model =& $this->getModel();
		$template = $model->getTemplate($templateId);
		if (is_null($template))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_MAILTEMPLATE', 
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

		$view =& $this->getView();
		$view->displayView($template);
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$template = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=mailtemplate&task=edit&templateId=' . $template->MailTemplateId . '&__MSG=COM_ARIQUIZ_COMPLETE_TEMPLATESAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=mailtemplates&__MSG=COM_ARIQUIZ_COMPLETE_TEMPLATESAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
	}
	
	function ajaxIsTemplateNameUnique()
	{
		$model =& $this->getModel();

		$templateName = JRequest::getString('templateName');
		$templateId = JRequest::getInt('templateId');

		return $model->isUniqueTemplateName($templateName, $templateId);
	}

	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$textData = JRequest::getVar('text_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$templateId = AriUtils::getParam($data, 'MailTemplateId', 0);
		if ($templateId != 0)
		{
			if (!AriQuizHelper::isAuthorise('mailtemplate.edit'))
			{
				throw new Exception(
					JText::_(
						'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'
					),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('mailtemplate.create'))
			{
				throw new Exception(
					JText::_(
						'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'
					),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=mailtemplates');
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('mailtemplate', 'mailtemplate'));
		$form->bind($data);
		$form->bind($textData, 'texttemplate');

		$cleanData = $form->toArray();
		$cleanTextData = $form->toArray('texttemplate');

		if (!$form->validate($cleanData) ||
			!$form->validate($cleanTextData, 'texttemplate'))
		{
			if ($redirectOnError)
			{
				$data['TextTemplate'] = $textData;
				$this->errorRedirect($data);
			}

			return null;
		}
		
		$cleanData['TextTemplate'] = $cleanTextData;

		return $model->saveTemplate($cleanData);
	}
	
	function errorRedirect($data)
	{
		$templateId = AriUtils::getParam($data, 'MailTemplateId', 0);
		$this->setRequestData($data);
		if ($templateId > 0)
			$this->redirect('index.php?option=com_ariquiz&view=mailtemplate&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&templateId=' . $templateId);
		else
			$this->redirect('index.php?option=com_ariquiz&view=mailtemplate&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
	}
}