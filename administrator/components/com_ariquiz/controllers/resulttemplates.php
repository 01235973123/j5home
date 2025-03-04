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

class AriQuizControllerResulttemplates extends AriController 
{
	var $_templateStateKey = 'com_ariquiz.dtResultTemplates';
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		if (!AriQuizHelper::isAuthorise('texttemplate.create'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=resulttemplates');
		}

		$this->redirect('index.php?option=com_ariquiz&view=resulttemplate&task=add');
	}
	
	function edit()
	{
		if (!AriQuizHelper::isAuthorise('texttemplate.edit'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=resulttemplates');
		}

		$templateId = JRequest::getVar('TemplateId');
		if (is_array($templateId) && count($templateId) > 0)
			$templateId = $templateId[0];
			
		$templateId = intval($templateId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=resulttemplate&task=edit&templateId=' . $templateId);
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

		$allowEdit = AriQuizHelper::isAuthorise('texttemplate.edit');
		for ($i = 0; $i < count($data); $i++)
		{
			$data[$i]->AllowEdit = $allowEdit;
		}

		return $data;
	}

	function ajaxDelete()
	{
		if (!AriQuizHelper::isAuthorise('texttemplate.delete'))
		{
			return false;
		}

		$model =& $this->getModel();
		
		return $model->deleteTemplate(JRequest::getVar('TemplateId'));
	}
}