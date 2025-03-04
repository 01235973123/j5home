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
AriKernel::import('Web.Controls.Advanced.MultiplierControls');

class AriQuizControllerResultscale extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=resultscales');
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

		$model =& $this->getModel();
		$scale = $model->getTable();

		$this->_display($scale);
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
		
		$scaleId = JRequest::getInt('scaleId');
		$model =& $this->getModel();
		$scale = $model->getScale($scaleId);
		if (is_null($scale))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_RESULTSCALE', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$templateId
				),
				500
			);
			return ;
		}

		$this->_display($scale);
	}
	
	function _display($scale)
	{
		$data = $this->getRequestData();
		if (!is_null($data))
			$scale->bind($data);

		$view =& $this->getView();
		$view->displayView($scale);
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$scale = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=resultscale&task=edit&scaleId=' . $scale->ScaleId . '&__MSG=COM_ARIQUIZ_COMPLETE_RESULTSCALESAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=resultscales&__MSG=COM_ARIQUIZ_COMPLETE_RESULTSCALESAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=resultscales');
	}
	
	function ajaxIsScaleNameUnique()
	{
		$model =& $this->getModel();

		$scaleName = JRequest::getString('scaleName');
		$scaleId = JRequest::getInt('scaleId');

		return $model->isUniqueScaleName($scaleName, $scaleId);
	}

	function _save($redirectOnError = true) 
	{
		AriKernel::import('Joomla.Form.Form');

		$model =& $this->getModel();
		$form = new AriForm('commonSettings');
		$form->load(AriQuizHelper::getFormPath('resultscale', 'resultscale'));
		
		$scaleItemFields = array();
		$scaleItemFields = $form->toArray('scaleitem');
		$scaleItemFields = array_keys($scaleItemFields);
		
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		$scaleItemsData = WebControls_MultiplierControls::getData('tblScaleContainer', $scaleItemFields);
		
		$form->bind($data);
		
		$scaleId = AriUtils::getParam($data, 'ScaleId', 0);
		if ($scaleId > 0)
		{
			if (!AriQuizHelper::isAuthorise('resultscale.edit'))
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=resultscales');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('resultscale.create'))
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=resultscales');
			}
		}

		$cleanData = $form->toArray();

		if (!$form->validate($cleanData))
		{
			if ($redirectOnError)
			{
				$data['ScaleItems'] = $scaleItemsData;
				$this->errorRedirect($data);
			}

			return null;
		}
		
		$cleanData['ScaleItems'] = $scaleItemsData;

		return $model->saveScale($cleanData);
	}
	
	function errorRedirect($data)
	{
		$scaleId = AriUtils::getParam($data, 'ScaleId', 0);
		$this->setRequestData($data);
		if ($scaleId > 0)
			$this->redirect('index.php?option=com_ariquiz&view=resultscale&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&scaleId=' . $scaleId);
		else
			$this->redirect('index.php?option=com_ariquiz&view=resultscale&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
	}
}