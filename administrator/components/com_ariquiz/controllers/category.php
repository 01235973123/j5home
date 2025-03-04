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

class AriQuizControllerCategory extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=categories');
	}

	function add() 
	{
		if (!AriQuizHelper::isAuthorise('category.create')) 
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=categories');
		}

		$model =& $this->getModel();
		$category = $model->getTable();

		$data = $this->getRequestData();
		if (!is_null($data))
			$category->bind($data);

		$view =& $this->getView();
		$view->displayView($category, JRequest::getInt('categoryActiveTab'));
	}
	
	function edit()
	{
		$categoryId = JRequest::getInt('categoryId');
		if (!AriQuizHelper::isAuthorise('category.edit', 'com_ariquiz.category.' . $categoryId)) 
		{
			throw new Exception(
				JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=categories');
		}

		$model =& $this->getModel();
		$category = $model->getCategory($categoryId);
		if (is_null($category))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_CATEGORY', 
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
		$view->displayView($category, JRequest::getInt('categoryActiveTab'));
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');

		$categoryActiveTab = JRequest::getInt('categoryActiveTab');
		$category = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=category&task=edit&categoryId=' . $category->CategoryId . ($categoryActiveTab > 0 ? '&categoryActiveTab=' . $categoryActiveTab : '') . '&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=categories&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function cancel()
	{
		$this->redirect('index.php?option=com_ariquiz&view=categories');
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
		$metaData = JRequest::getVar('metadata_params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$categoryId = AriUtils::getParam($data, 'CategoryId', 0);
		
		if ($categoryId != 0)
		{
			if (!AriQuizHelper::isAuthorise('category.edit', 'com_ariquiz.category.' . $categoryId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);	
				$this->redirect('index.php?option=com_ariquiz&view=categories');
			}
		}
		else
		{
			$parentId = AriUtils::getParam($data, 'parent_id', 0);
			if (!AriQuizHelper::isAuthorise('category.create', $parentId > 0 ? 'com_ariquiz.category.' . $parentId : 'com_ariquiz')) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);	
				$this->redirect('index.php?option=com_ariquiz&view=categories');
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('category', 'category'));
		
		$dataGroups = array('_default');
		if (AriQuizHelper::isAuthorise('core.admin'))
			$dataGroups[] = 'rules';
		
		$commonSettingsForm->bind($data, $dataGroups);
		$commonSettingsForm->bind($metaData, 'metadata');
		$cleanData = $commonSettingsForm->toArray($dataGroups);
		$cleanMetaData = $commonSettingsForm->toArray(array('metadata'));
		if (!$commonSettingsForm->validate($cleanData) ||
			!$commonSettingsForm->validate($cleanMetaData, array('metadata')))
		{
			if ($redirectOnError)
			{
				$this->setRequestData($data);
				if ($categoryId > 0)
					$this->redirect('index.php?option=com_ariquiz&view=category&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&categoryId=' . $categoryId);
				else
					$this->redirect('index.php?option=com_ariquiz&view=category&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
			}

			return null;
		}

		return $model->saveCategory($cleanData, $cleanMetaData);
	}
}