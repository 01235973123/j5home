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

class AriQuizControllerResultscales extends AriController 
{
	var $_scaleStateKey = 'com_ariquiz.dtResultScales';
	
	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}

	function add()
	{
		if (!AriQuizHelper::isAuthorise('resultscale.create'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=resultscales');
		}

		$this->redirect('index.php?option=com_ariquiz&view=resultscale&task=add');
	}
	
	function edit()
	{
		if (!AriQuizHelper::isAuthorise('resultscale.edit'))
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=resultscales');
		}
		
		$scaleId = JRequest::getVar('ScaleId');
		if (is_array($scaleId) && count($scaleId) > 0)
			$scaleId = $scaleId[0];
			
		$scaleId = intval($scaleId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=resultscale&task=edit&scaleId=' . $scaleId);
	}

	function ajaxDelete()
	{
		if (!AriQuizHelper::isAuthorise('resultscale.delete'))
		{
			return false;
		}
		
		$model =& $this->getModel();

		return $model->deleteScale(JRequest::getVar('ScaleId'));
	}

	function ajaxGetScaleList()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'ScaleName', 
				'dir' => 'asc'
			), 
			true,
			$this->_scaleStateKey);

		$totalCnt = $model->getScaleCount($filter);
		$filter->fixFilter($totalCnt);

		$scales = $this->_extendScaleList(
			$model->getScaleList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($scales, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendScaleList($data)
	{
		if (!is_array($data))
			return $data;

		$allowEdit = AriQuizHelper::isAuthorise('resultscale.edit');
		for ($i = 0; $i < count($data); $i++)
		{
			$data[$i]->AllowEdit = $allowEdit;
		}

		return $data;
	}
}