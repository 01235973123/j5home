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

AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Database.DBUtils');

class AriQuizModelQuestioncategory extends AriModel 
{
	function getCategory($categoryId, $strictLoad = true) 
	{
		if ($strictLoad && $categoryId < 1)
			return null;

		$category =& $this->getTable();
		$category->load($categoryId);
		
		if ($strictLoad && empty($category->CategoryName))
			$category = null;

		return $category;
	}
	
	function saveCategory($data)
	{
		$category = $this->getTable();
		if (!empty($data['QuestionCategoryId']))
		{
			$category->load($data['QuestionCategoryId']);

			$data['QuizId'] = $category->QuizId;
		}

		$category->bind($data);

		if (!$category->store())
		{
			throw new Exception(
				JText::sprintf(
					'COM_ARIQUIZ_ERROR_SQL_QUERY', 
					__CLASS__ . '::' . __FUNCTION__ . '()', 
					$category->getQuery(), 
					$category->getError()
				),
				500
			);
			return null;
		}
		
		return $category;
	}
	
	function isUniqueCategoryName($name, $quizId, $id = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestioncategory');

		$query->where('CategoryName = ' . $db->Quote($name));
		$query->where('QuizId = ' . intval($quizId, 10));
		if ($id)
			$query->where('QuestionCategoryId <> ' . intval($id, 10));

		$db->setQuery((string)$query);

		$isUnique = $db->loadResult();
		if (is_null($isUnique))
		{
			return null;
		}
		
		return ($isUnique == 0);
	}
	
	function copy($categoryId, $destQuizId, $userId)
	{
		$category = $this->getCategory($categoryId);
		if (empty($category))
			return null;
		
		$copyCategory =& $this->getTable();
		if (!$copyCategory->copyFrom($category, $destQuizId, $userId))
			return null;

		if (!$copyCategory->store())
			return null;
		
		return $copyCategory;
	}
}