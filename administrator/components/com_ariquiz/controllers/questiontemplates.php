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

class AriQuizControllerQuestiontemplates extends AriController 
{
	var $_templateStateKey = 'com_ariquiz.dtQuestionTemplates';
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		if (!AriQuizHelper::isAuthorise('questiontemplate.create'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
		}
		
		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
		$form->bind(JRequest::getVar('type'));
		$questionTypeId = intval($form->get('QuestionTypeId'), 10);

		$this->redirect('index.php?option=com_ariquiz&view=questiontemplate&task=add' . ($questionTypeId > 0 ? '&questionTypeId=' . $questionTypeId : ''));
	}
	
	function edit()
	{
		if (!AriQuizHelper::isAuthorise('questiontemplate.edit'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questiontemplates');
		}
		
		$templateId = JRequest::getVar('TemplateId');
		if (is_array($templateId) && count($templateId) > 0)
			$templateId = $templateId[0];
			
		$templateId = intval($templateId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=questiontemplate&task=edit&templateId=' . $templateId);
	}
	
	function display($cachable = false, $urlparams = []) 
	{
		$qtModel =& $this->getModel('QuestionTypes');
		$questionTypes = $qtModel->getQuestionTypeList();

		$view =& $this->getView();
		$view->displayView($questionTypes);
	}

	function ajaxGetTemplateList()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'TemplateName', 
				'dir' => 'asc'
			), 
			true,
			$this->_templateStateKey);

		$totalCnt = $model->getTemplateCount($filter);
		$filter->fixFilter($totalCnt);

		$templates = $this->_extendTemplateList(
			$model->getTemplateList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($templates, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendTemplateList($data)
	{
		if (!is_array($data))
			return $data;

		$allowEdit = AriQuizHelper::isAuthorise('questiontemplate.edit');
		for ($i = 0; $i < count($data); $i++)
		{
			$data[$i]->AllowEdit = $allowEdit;
		}

		return $data;
	}

	function ajaxDelete()
	{
		if (!AriQuizHelper::isAuthorise('questiontemplate.delete'))
		{
			return false;
		}
		
		$model =& $this->getModel();
		
		return $model->deleteTemplate(JRequest::getVar('TemplateId'));
	}
}