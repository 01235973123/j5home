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
AriKernel::import('Utils.DateUtility');

class AriQuizControllerQuizresults extends AriController 
{
	var $_resultsStateKey = 'com_ariquiz.dtResults';
	var $_filter;
	
	function display($cachable = false, $urlparams = [])
	{
		$filter = $this->_getFilter(false, true);
		$filterPredicates = $filter->getConfigValue('filter');

		$view =& $this->getView();
		$view->displayView($filterPredicates);
	}
	
	function quizResults()
	{
		$quizId = JRequest::getInt('quizId');
		if ($quizId > 0)
		{
			$filter = $this->_getFilter(false, true);
		
			$filterPredicates = $filter->getConfigValue('filter');
			if (!is_array($filterPredicates))
				$filterPredicates = array();
			
			$filterPredicates['QuizId'] = $quizId;			
			$filter->setConfigValue('filter', $filterPredicates);
			$filter->store();
		}
		
		$this->redirect('index.php?option=com_ariquiz&view=quizresults');
	}
	
	function csvExport()
	{
		$statisticsId = JRequest::getVar('StatisticsInfoId');
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
		$statisticsId = JRequest::getVar('StatisticsInfoId');
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
		$statisticsId = JRequest::getVar('StatisticsInfoId');
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
		$statisticsId = JRequest::getVar('StatisticsInfoId');
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
	
	function ajaxGetResultList()
	{
		$model =& $this->getModel();
		$filter = $this->_getFilter();

		$totalCnt = $model->getResultCount($filter);
		$filter->fixFilter($totalCnt);

		$results = $model->getResultList($filter);
		$results = $this->_modifyResults($results);
		$data = AriMultiPageDataTableControl::createDataInfo($results, $filter, $totalCnt); 

		return $data;
	}
	
	function ajaxDelete()
	{
		if (!AriQuizHelper::isAuthorise('results.delete'))
		{
			return false;
		}
		
		$model =& $this->getModel();
		
		return $model->deleteResults(JRequest::getVar('StatisticsInfoId'));
	}
	
	function ajaxDeleteAll()
	{
		if (!AriQuizHelper::isAuthorise('results.delete'))
		{
			return false;
		}
		
		$model =& $this->getModel();
		
		return $model->deleteAllResults();
	}
	
	function ajaxFilters()
	{
		$filterData = JRequest::getVar('filter', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$filter = $this->_getFilter(false, true);
		
		$filterPredicates = array();
		if (!empty($filterData['QuizId']))
			$filterPredicates['QuizId'] = $filterData['QuizId'];
			
		if (isset($filterData['UserId']))
			$filterPredicates['UserId'] = $filterData['UserId'];
		
		if (!empty($filterData['StartDate']))
		{
			$startDate = intval($filterData['StartDate'], 10);
			$startDate = AriDateUtility::toUnixUTC($startDate);

			$filterData['StartDate'] = $startDate;
			$filterPredicates['StartDate'] = $filterData['StartDate'];
		}
			
		if (!empty($filterData['EndDate']))
		{
			$endDate = intval($filterData['EndDate'], 10) + 23 * 3600 + 59*60 + 58;
			$endDate = AriDateUtility::toUnixUTC($endDate);

			$filterData['EndDate'] = $endDate;//intval($filterData['EndDate'], 10) + 23 * 3600 + 59*60 + 59;
			$filterPredicates['EndDate'] = $filterData['EndDate'];
		}
			
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
				'sortField' => 'StartDate2', 
				'dir' => 'desc'
			), 
			$bindFromRequest, 
			$this->_resultsStateKey);
			
		if ($restore)
			$this->_filter->restore();
			
		return $this->_filter;
	}
	
	function _modifyResults($results)
	{
		if (empty($results))
			return $results;
			
		for ($i = 0; $i < count($results); $i++)
		{
			$result = $results[$i];
			
			$result->StartDate = AriDateUtility::formatDate($result->StartDate);
			$result->EndDate = AriDateUtility::formatDate($result->EndDate);
			$result->StartDate2 = AriDateUtility::formatDate($result->StartDate2);
			$result->EndDate2 = AriDateUtility::formatDate($result->EndDate2);
			
			$results[$i] = $result;
		}

		return $results;
	}
}