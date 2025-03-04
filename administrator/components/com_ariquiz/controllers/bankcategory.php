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

class AriQuizControllerBankcategory extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
	}

	function add() 
	{
		if (!AriQuizHelper::isAuthorise('bankcategory.create')) 
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 403);
			$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
		}

		$model =& $this->getModel();
		$category = $model->getTable();

		$data = $this->getRequestData();
		if (!is_null($data))
			$category->bind($data);

		$view =& $this->getView();
		$view->displayView($category);
	}
	
	function edit()
	{
		$categoryId = JRequest::getInt('categoryId');
		if (!AriQuizHelper::isAuthorise('bankcategory.edit', 'com_ariquiz.bankcategory.' . $categoryId)) 
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 403);
			$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
		}

		$model =& $this->getModel();
		$category = $model->getCategory($categoryId);
		if (is_null($category))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_BANKCATEGORY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$categoryId
				),
				500
			);
			return ;
		}

		$data = $this->getRequestData();
		if (!is_null($data))
			$category->bind($data);

		$view =& $this->getView();
		$view->displayView($category);
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$category = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=bankcategory&task=edit&categoryId=' . $category->CategoryId . '&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=bankcategories&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
	}
	
	function ajaxIsCategoryNameUnique()
	{
		$model =& $this->getModel();
		
		$categoryName = JRequest::getString('categoryName');
		$categoryId = JRequest::getInt('categoryId');
		
		return $model->isUniqueCategoryName($categoryName, $categoryId);
	}
	
	function _save($redirectOnError = true) 
	{
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$categoryId = AriUtils::getParam($data, 'CategoryId', 0);
		if ($categoryId != 0)
		{
			if (!AriQuizHelper::isAuthorise('bankcategory.edit', 'com_ariquiz.bankcategory.' . $categoryId)) 
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 403);
				$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('bankcategory.create')) 
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'), 403);
				$this->redirect('index.php?option=com_ariquiz&view=bankcategories');
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('bankcategory', 'category'));
		
		$dataGroups = array('_default');
		if (AriQuizHelper::isAuthorise('core.admin'))
			$dataGroups[] = 'rules';
		
		$commonSettingsForm->bind($data, $dataGroups);
		$cleanData = $commonSettingsForm->toArray($dataGroups);
		if (!$commonSettingsForm->validate($cleanData))
		{
			if ($redirectOnError)
			{
				$this->setRequestData($data);
				if ($categoryId > 0)
					$this->redirect('index.php?option=com_ariquiz&view=bankcategory&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&categoryId=' . $categoryId);
				else
					$this->redirect('index.php?option=com_ariquiz&view=bankcategory&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
			}

			return null;
		}

		return $model->saveCategory($cleanData);
	}
}