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
AriKernel::import('Web.Response');

class AriQuizControllerBankquestions extends AriController 
{
	var $_bankStateKey = 'com_ariquiz.dtBank';
	var $_filter;
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFormPath('question', 'question_questiontype'));
		$form->bind(JRequest::getVar('type'));
		$questionTypeId = intval($form->get('QuestionTypeId'), 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=bankquestion&task=add' . ($questionTypeId > 0 ? '&questionTypeId=' . $questionTypeId : ''));
	}
	
	function edit()
	{
		$questionId = JRequest::getVar('QuestionId');
		if (is_array($questionId) && count($questionId) > 0)
			$questionId = $questionId[0];
			
		$questionId = intval($questionId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=bankquestion&task=edit&questionId=' . $questionId);
	}
	
	function display($cachable = false, $urlparams = []) 
	{
		$qtModel =& $this->getModel('QuestionTypes');
		$questionTypes = $qtModel->getQuestionTypeList();
		
		$filter = $this->_getFilter(false, true);
		$filterPredicates = $filter->getConfigValue('filter');

		$view =& $this->getView();
		$view->displayView($questionTypes, $filterPredicates);
	}
	
	function exportCSV()
	{
		$questionId = JRequest::getVar('QuestionId');
		$csvExporter = $this->getModel('QuestionsCSVExport');
		$result = $csvExporter->exportBankQuestions(
			$questionId
		);

		if (empty($result))
		{
			$this->redirect('index.php?option=com_ariquiz&view=bankquestions&__MSG=COM_ARIQUIZ_ERROR_CSVEXPORT');
			exit();
		}

		AriResponse::sendContentAsAttach($result, 'bank_questions.csv');
		exit();
	}
	
	function uploadCSVImport()
	{
		if (!AriQuizHelper::isAuthorise('bankquestion.create'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
		}
		
		$file = JRequest::getVar('importDataCSVFile', '', 'files', 'array');
		$fileName = null;
		if (!empty($file) && $file['size'] > 0)
			$fileName = $file['tmp_name'];

		$result = $this->_CSVImport($fileName);
		
		if ($result)
		{
			$filter = $this->_getFilter(false, true);
			$filter->setConfigValue('sortField', 'QuestionId2');
			$filter->setConfigValue('sortDirection', 'desc');
			$filter->store();
		}
		
		$this->redirect('index.php?option=com_ariquiz&view=bankquestions&__MSG=' . ($result ? 'COM_ARIQUIZ_COMPLETE_DATAIMPORT' : 'COM_ARIQUIZ_COMPLETE_DATAIMPORTFAILED'));
	}
	
	function importCSVFromDir()
	{
		if (!AriQuizHelper::isAuthorise('bankquestion.create'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=bankquestions');
		}

		$file = JRequest::getString('importDataCSVDir');
		$result = false;
		if (!empty($file) && @file_exists($file) && @is_file($file))
			$result = $this->_CSVImport($file);
			
		if ($result)
		{
			$filter = $this->_getFilter(false, true);
			$filter->setConfigValue('sortField', 'QuestionId2');
			$filter->setConfigValue('sortDirection', 'desc');
			$filter->store();
		}
		
		$this->redirect('index.php?option=com_ariquiz&view=bankquestions&__MSG=' . ($result ? 'COM_ARIQUIZ_COMPLETE_DATAIMPORT' : 'COM_ARIQUIZ_COMPLETE_DATAIMPORTFAILED'));
	}

	function ajaxGetQuestionList()
	{
		$model =& $this->getModel();

		$filter = $this->_getFilter();

		$totalCnt = $model->getQuestionCount($filter);
		$filter->fixFilter($totalCnt);

		$questions = $this->_extendQuestionList(
			$model->getQuestionList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($questions, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendQuestionList($data)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->QuestionId;
			
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('bankquestion.edit', 'com_ariquiz.bankquestion.' . $id);
		}

		return $data;
	}

	function ajaxMassEdit()
	{
		$fields = JRequest::getVar('massParams', array(), 'default', 'none', JREQUEST_ALLOWRAW);
		
		$massEditForm = new AriMassEditForm('common');
		$massEditForm->load(AriQuizHelper::getFormPath('question', 'bankquestion_massedit'));

		if (!$massEditForm->validate($fields))
			return false;
			
		$categoryId = intval(AriUtils::getParam($fields, 'QuestionCategoryId'), 10);
		if ($categoryId > 0)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.create', 'com_ariquiz.bankcategory.' . $categoryId))
				return false;
		}	

		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.edit', 'com_ariquiz.bankquestion.' . $id)) 
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
			$userId
		);
	}

	function ajaxDelete()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('QuestionId'), 1);
		if (count($idList) == 0) 
			return false;

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.delete', 'com_ariquiz.bankquestion.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
		}
		
		$model =& $this->getModel();

		return $model->deleteQuestions($idList);
	}

	function ajaxFilters()
	{
		$filterData = JRequest::getVar('filter', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$filter = $this->_getFilter(false, true);
		
		$filterPredicates = array();
		if (!empty($filterData['CategoryId']))
			$filterPredicates['CategoryId'] = $filterData['CategoryId'];

        if (!empty($filterData['Id']))
            $filterPredicates['Id'] = intval($filterData['Id'], 10);
			
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
				'sortField' => 'Question', 
				'dir' => 'asc'
			), 
			$bindFromRequest,
			$this->_bankStateKey);
			
		if ($restore)
			$this->_filter->restore();
			
		return $this->_filter;
	}
	
	function _CSVImport($csvFile)
	{
		$user =& JFactory::getUser();
		$csvImporter = $this->getModel('QuestionsCSVImport');
		$result = $csvImporter->importBankQuestions(
			$csvFile,
			$user->get('id'),
			AriQuizHelper::getDefaultBankCategoryId()
		);
		
		return $result;
	}
}