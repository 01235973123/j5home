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
AriKernel::import('Web.Response');

class AriQuizControllerQuizresult extends AriController 
{
	function quizresults() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizresults');
	}
	
	function display($cachable = false, $urlparams = [])
	{
		$sid = JRequest::getInt('statisticsInfoId');
		
		$view =& $this->getView();
		$view->displayView($sid);
	}
	
	function csvExport()
	{
		$statisticsId = JRequest::getInt('StatisticsInfoId');
		$exportModel =& $this->getModel('Quizexportresults');

		$result = $exportModel->getCSVView(
			$statisticsId,
			array(
				'Anonymous' => JText::_('COM_ARIQUIZ_LABEL_GUEST'),
				'Passed' => JText::_('COM_ARIQUIZ_LABEL_PASSSED'),
				'NoPassed' => JText::_('COM_ARIQUIZ_LABEL_NOTPASSSED')
			),
			AriQuizHelper::getShortPeriods()
		);

		AriResponse::sendContentAsAttach($result, 'results.csv');
		exit();
	}
	
	function htmlExport()
	{
		$statisticsId = JRequest::getInt('StatisticsInfoId');
		$exportModel =& $this->getModel('Quizexportresults');

		$result = $exportModel->getHtmlView(
			$statisticsId,
			array(
				'Anonymous' => JText::_('COM_ARIQUIZ_LABEL_GUEST'),
				'Passed' => JText::_('COM_ARIQUIZ_LABEL_PASSSED'),
				'NoPassed' => JText::_('COM_ARIQUIZ_LABEL_NOTPASSSED')
			),
			AriQuizHelper::getShortPeriods()
		);

		AriResponse::sendContentAsAttach($result, 'results.html');
		exit();
	}
	
	function wordExport()
	{
		$statisticsId = JRequest::getInt('StatisticsInfoId');
		$exportModel =& $this->getModel('Quizexportresults');

		$result = $exportModel->getWordView( 
			$statisticsId,
			array(
				'Anonymous' => JText::_('COM_ARIQUIZ_LABEL_GUEST'),
				'Passed' => JText::_('COM_ARIQUIZ_LABEL_PASSSED'),
				'NoPassed' => JText::_('COM_ARIQUIZ_LABEL_NOTPASSSED')
			),
			AriQuizHelper::getShortPeriods()
		);

		AriResponse::sendContentAsAttach($result, 'results.doc');
		exit();
	}
	
	function excelExport()
	{
		$statisticsId = JRequest::getInt('StatisticsInfoId');
		$exportModel =& $this->getModel('Quizexportresults');

		$result = $exportModel->getExcelView( 
			$statisticsId,
			array(
				'Anonymous' => JText::_('COM_ARIQUIZ_LABEL_GUEST'),
				'Passed' => JText::_('COM_ARIQUIZ_LABEL_PASSSED'),
				'NoPassed' => JText::_('COM_ARIQUIZ_LABEL_NOTPASSSED')
			),
			AriQuizHelper::getShortPeriods()
		);

		AriResponse::sendContentAsAttach($result, 'results.xls');
		exit();
	}

	function ajaxGetResult()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'QuestionIndex', 
				'dir' => 'asc'
			),
			true,
			null,
			array('QuestionIndex')
		);
		$filter->setConfigValue('sortField', 'QuestionIndex');
		$filter->setConfigValue('dir', 'asc');

		$sid = JRequest::getInt('statisticsInfoId');

		$totalCnt = $model->getQuestionCount($sid, $filter);
		$filter->fixFilter($totalCnt);

		$results = $model->getJsonQuestionList($sid, $filter, false, false, JText::_('COM_ARIQUIZ_QUESTIONSUMMARY'));
		
		$sortField = $filter->getConfigValue('sortField');
		$filter->setConfigValue('sortField', null);
		
		$data = AriMultiPageDataTableControl::createDataInfo($results, $filter, $totalCnt);

		$filter->setConfigValue('sortField', $sortField);

		return $data;
	}
}