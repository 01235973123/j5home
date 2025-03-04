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

class AriQuizControllerQuestioncategory extends AriController 
{
	function display($cachable = false, $urlparams = []) 
	{
		$this->redirect('index.php?option=com_ariquiz&view=questioncategories');
	}

	function add() 
	{
		$model =& $this->getModel();
		$category = $model->getTable();
		
		$quizId = JRequest::getInt('quizId');
		if ($quizId > 0)
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.create', 'com_ariquiz.quiz.' . $quizId)) 
			{
				throw new Exception(
					JText::_(
						'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'
					),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=questioncategories&quizId=' . $quizId);
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.create')) 
			{
				throw new Exception(
					JText::_(
						'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'
					),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=questioncategories');
			}
		}
		
		if ($quizId > 0)
			$category->QuizId = $quizId;

		$data = $this->getRequestData();
		if (!is_null($data))
			$category->bind($data);

		$view =& $this->getView();
		$view->displayView($category, JRequest::getInt('categoryActiveTab'));
	}
	
	function edit()
	{
		$categoryId = JRequest::getInt('categoryId');
		
		if (!AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.questioncategory.' . $categoryId)) 
		{
			throw new Exception(
				JText::_(
					'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'
				),
				403
			);
			$this->redirect('index.php?option=com_ariquiz&view=questioncategories');
		}
		
		$model =& $this->getModel();
		$category = $model->getCategory($categoryId);
		if (is_null($category))
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_LOAD_QUESTIONCATEGORY', 
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
		$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=edit&categoryId=' . $category->QuestionCategoryId  . ($categoryActiveTab > 0 ? '&categoryActiveTab=' . $categoryActiveTab : '') . '&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		
		$category = $this->_save();
		$this->redirect('index.php?option=com_ariquiz&view=questioncategories&quizId=' . $category->QuizId . '&__MSG=COM_ARIQUIZ_COMPLETE_CATEGORYSAVE');
	}

	function cancel()
	{
		$quizId = JRequest::getInt('quizId');
		
		$this->redirect('index.php?option=com_ariquiz&view=questioncategories' . ($quizId > 0 ? '&quizId=' . $quizId : ''));
	}
	
	function ajaxIsCategoryNameUnique()
	{
		$model =& $this->getModel();
		
		$categoryName = JRequest::getString('categoryName');
		$categoryId = JRequest::getInt('categoryId');
		$quizId = JRequest::getInt('quizId');
		
		return $model->isUniqueCategoryName($categoryName, $quizId, $categoryId);
	}
	
	function _save($redirectOnError = true) 
	{		
		$model =& $this->getModel(); 
		$data = JRequest::getVar('params', null, 'default', 'none', JREQUEST_ALLOWRAW);
		
		$categoryId = AriUtils::getParam($data, 'CategoryId', 0);
		$quizId = AriUtils::getParam($data, 'QuizId', 0);
		
		if ($categoryId > 0)
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.edit', 'com_ariquiz.questioncategory.' . $categoryId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'),
					403
				);	
				$this->redirect('index.php?option=com_ariquiz&view=questioncategories');
			}
		}
		else
		{
			if (!AriQuizHelper::isAuthorise('questioncategory.create', 'com_ariquiz.quiz.' . $quizId)) 
			{
				throw new Exception(
					JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'),
					403
				);
				$this->redirect('index.php?option=com_ariquiz&view=questioncategories&quizId=' . $quizId);
			}
		}

		AriKernel::import('Joomla.Form.Form');

		$commonSettingsForm = new AriForm('commonSettings');
		$commonSettingsForm->load(AriQuizHelper::getFormPath('questioncategory', 'category'));
		
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
					$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=edit&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE&categoryId=' . $categoryId);
				else
					$this->redirect('index.php?option=com_ariquiz&view=questioncategory&task=add&__MSG=COM_ARIQUIZ_ERROR_ENTITYSAVE');
			}

			return null;
		}

		$poolData = $this->_getQuestionPoolData();
		$cleanData['QuestionPool'] = $poolData;

		return $model->saveCategory($cleanData);
	}
	
	function _getQuestionPoolData()
	{
		AriKernel::import('Joomla.Form.Form');
		
		$poolForm = new AriForm('questionPool');
		$poolForm->load(AriQuizHelper::getFormPath('questioncategory', 'questionpool'));

		$questionPoolFields = array();
		$questionPoolFields = $poolForm->toArray();
		$questionPoolFields = array_keys($questionPoolFields);
		for ($i = 0; $i < count($questionPoolFields); $i++)
			$questionPoolFields[$i] = 'poolParams' . $questionPoolFields[$i];

		$poolData = WebControls_MultiplierControls::getData('tblPoolContainer', $questionPoolFields);
		if (!is_array($poolData))
			return array();

		$normalizedPoolData = array();
		foreach ($poolData as $poolDataItem)
		{
			$bankCategoryId = intval(AriUtils::getParam($poolDataItem, 'poolParamsBankCategoryId'), 10);
			if ($bankCategoryId < 1)
				continue ;
				
			$questionCount = intval(AriUtils::getParam($poolDataItem, 'poolParamsQuestionCount'), 10);
			if ($questionCount < 0)
				$questionCount = 0;
				
			$normalizedPoolData[$bankCategoryId] = array(
				'BankCategoryId' => $bankCategoryId,
				'QuestionCount' => $questionCount
			);
		}

		return array_values($normalizedPoolData);
	}
}