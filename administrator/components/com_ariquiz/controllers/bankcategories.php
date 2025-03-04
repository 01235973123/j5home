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
AriKernel::import('Utils.ArrayHelper');

class AriQuizControllerBankcategories extends AriController 
{
	var $_categoriesStateKey = 'com_ariquiz.dtBankCategories';

	function quizzes() 
	{
		$this->redirect('index.php?option=com_ariquiz&view=quizzes');
	}
	
	function edit()
	{
		$categoryId = JRequest::getVar('CategoryId');
		if (is_array($categoryId) && count($categoryId) > 0)
			$categoryId = $categoryId[0];
			
		$categoryId = intval($categoryId, 10);
		
		$this->redirect('index.php?option=com_ariquiz&view=bankcategory&task=edit&categoryId=' . $categoryId);
	}

	function add()
	{
		$this->redirect('index.php?option=com_ariquiz&view=bankcategory&task=add');
	}

	function ajaxGetCategoryList()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'CategoryName', 
				'dir' => 'asc'
			), 
			true, 
			$this->_categoriesStateKey);

		$totalCnt = $model->getCategoryCount($filter);
		$filter->fixFilter($totalCnt);

		$categories = $this->_extendCategoryList(
			$model->getCategoryList($filter)
		);
		$data = AriMultiPageDataTableControl::createDataInfo($categories, $filter, $totalCnt); 

		return $data;
	}
	
	function _extendCategoryList($data)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->CategoryId;
			
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('bankcategory.edit', 'com_ariquiz.bankcategory.' . $id);
		}

		return $data;
	}

	function ajaxDelete()
	{
		$deleteQuestions = JRequest::getBool('deleteQuestions');
		$idList = AriArrayHelper::toInteger(JRequest::getVar('CategoryId'), 1);
		if (count($idList) == 0) 
			return false;

		$defaultCategoryId = AriQuizHelper::getDefaultBankCategoryId();
		if (!$deleteQuestions)
		{
			if (!AriQuizHelper::isAuthorise('bankquestion.create', 'com_ariquiz.bankcategory.' . $defaultCategoryId)) 
			{
				return false;
			}
		}

		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('bankcategory.delete', 'com_ariquiz.category.' . $id)) 
			{
				// Prune items that you can't change.
				unset($idList[$i]);
			}
			else if ($defaultCategoryId == $id)
			{
				// it is not possible to delete default category
				unset($idList[$i]);
			}
		}
 
		$model =& $this->getModel();

		return $model->deleteCategory($idList, $deleteQuestions, $defaultCategoryId);
	}
}