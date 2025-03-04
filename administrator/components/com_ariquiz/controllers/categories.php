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

class AriQuizControllerCategories extends AriController 
{
	var $_categoriesStateKey = 'com_ariquiz.dtCategories';

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
		
		$this->redirect('index.php?option=com_ariquiz&view=category&task=edit&categoryId=' . $categoryId);
	}

	function add()
	{
		$this->redirect('index.php?option=com_ariquiz&view=category&task=add');
	}

	function ajaxGetCategoryList()
	{
		$model =& $this->getModel();

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => ARIQUIZ_GRID_PAGESIZE, 
				'sortField' => 'lft', 
				'dir' => 'asc'
			), 
			true, 
			$this->_categoriesStateKey);

		$totalCnt = $model->getCategoryCount($filter);
		$filter->fixFilter($totalCnt);
		$points = $model->getCategoriesEndPoints();

		$categories = $this->_extendCategoryList(
			$model->getCategoryList($filter),
			$points
		);
		$data = AriMultiPageDataTableControl::createDataInfo($categories, $filter, $totalCnt); 

		return $data;
	}

	function _extendCategoryList($data, $points)
	{
		if (!is_array($data))
			return $data;

		for ($i = 0; $i < count($data); $i++)
		{
			$id = $data[$i]->CategoryId;
			$lft = $data[$i]->lft;
			$parent_id = $data[$i]->parent_id;
			$point = isset($points[$parent_id]) ? $points[$parent_id] : null;
			
			$nodeType = null;
			if (!is_null($point) && $point->StartLft != $point->EndLft)
			{
				if ($lft == $point->StartLft)
					$nodeType = 'first';
				else if ($lft == $point->EndLft)
					$nodeType = 'last';
				else
					$nodeType = 'middle';
			}

			$data[$i]->NodeType = $nodeType;
			$data[$i]->AllowEdit = AriQuizHelper::isAuthorise('category.edit', 'com_ariquiz.category.' . $id);
		}

		return $data;
	}

	function ajaxDelete()
	{
		$idList = AriArrayHelper::toInteger(JRequest::getVar('CategoryId'), 1);
		if (count($idList) == 0) 
			return false;

		$defaultCategoryId = AriQuizHelper::getDefaultCategoryId();
		foreach ($idList as $i => $id)
		{
			if (!AriQuizHelper::isAuthorise('category.delete', 'com_ariquiz.category.' . $id)) 
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

		return $model->deleteCategory($idList, $defaultCategoryId);
	}
	
	function ajaxRebuild()
	{
		if (!AriQuizHelper::isAuthorise('category.edit'))
		{
			return false;
		}

		$model = $this->getModel();
		return $model->rebuild();
	}

	function ajaxOrderUp()
	{
		$categoryId = JRequest::getInt('categoryId');
		if (!AriQuizHelper::isAuthorise('category.edit', 'com_ariquiz.category.' . $categoryId))
		{
			return false;
		}

		$model = $this->getModel();
		return $model->orderUp($categoryId);
	}

	function ajaxOrderDown()
	{
		$categoryId = JRequest::getInt('categoryId');
		if (!AriQuizHelper::isAuthorise('category.edit', 'com_ariquiz.category.' . $categoryId))
		{
			return false;
		}
		
		$model = $this->getModel();
		return $model->orderDown($categoryId);
	}
}