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
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Data.DataFilter');

class AriQuizModelQuestioncategories extends AriModel 
{
	function __construct()
	{
		parent::__construct();
		
		// import constants
		$this->getTable('quiz');
		$this->getTable('quizquestion');
		$this->getTable('questioncategory');
	}
	
	function getCategoryCount($quizId, $filter = null)
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestioncategory QQC');
		$query->innerJoin('#__ariquiz Q ON QQC.QuizId = Q.QuizId');

		$quizId = intval($quizId, 10);
		if ($quizId > 0)
			$query->where('QQC.QuizId = ' . $quizId);
			
		$query->where('Q.Status <> ' . ARIQUIZ_QUIZ_STATUS_DELETE);
		$query->where('QQC.Status <> ' . ARIQUIZ_QUESTIONCATEGORY_STATUS_DELETE);

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;
	}
	
	function getCategoryList($quizId, $filter = null, $loadQuestionPool = false)
	{
		$db =& $this->getDBO();
		if (empty($filter))
			$filter = new AriDataFilter();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'QQC.QuestionCategoryId',
				'QQC.CategoryName',
				'QQC.Description',
				'QQC.QuestionCount',
				'QQC.QuestionTime',
				'QQC.QuizId',
				'Q.QuizName'
			)
		);
		$query->from('#__ariquizquestioncategory QQC');
		$query->innerJoin('#__ariquiz Q ON QQC.QuizId = Q.QuizId');

		$quizId = intval($quizId, 10);
		if ($quizId > 0)
			$query->where('QQC.QuizId = ' . $quizId);
			
		$query->where('Q.Status <> ' . ARIQUIZ_QUIZ_STATUS_DELETE);
		$query->where('QQC.Status <> ' . ARIQUIZ_QUESTIONCATEGORY_STATUS_DELETE);
		
		$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$categories = $db->loadObjectList();

		if (is_null($categories))
		{
			return null;
		}
		
		if ($loadQuestionPool && is_array($categories) && count($categories) > 0)
		{
			$query = AriDBUtils::getQuery();
			$query->select('QuestionCategoryId,BankCategoryId,QuestionCount');
			$query->from('#__ariquiz_quiz_questionpool');
			$query->where('QuizId = ' . $quizId);
			$query->where('QuestionCategoryId > 0');
			
			$db->setQuery((string)$query);
			$questionPool = $db->loadObjectList();
			if (is_null($questionPool))
			{
				return $categories;
			}

			$catQuestionPool = array();
			if (is_array($questionPool))
			{
				foreach ($questionPool as $questionPoolItem)
				{
					$catId = $questionPoolItem->QuestionCategoryId;
					if (!isset($catQuestionPool[$catId]))
						$catQuestionPool[$catId] = array();
						
					$catQuestionPool[$catId][] = $questionPoolItem;
				}
			}

			for ($i = 0; $i < count($categories); $i++)
			{
				$catId = $categories[$i]->QuestionCategoryId;
				$categories[$i]->QuestionPool = isset($catQuestionPool[$catId])
					? $catQuestionPool[$catId]
					: array();
			}
		}
		
		return $categories;
	}
	
	function deleteCategory($idList, $deleteQuestions = false)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		
		$catStr = join(',', $idList);
		$query = array();
		$query[] = sprintf('DELETE FROM #__ariquizquestioncategory WHERE QuestionCategoryId IN (%1$s)', 
			$catStr);

		if ($deleteQuestions)
		{
			$query[] = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV' .
				'	 ON QQ.QuestionVersionId = QQV.QuestionVersionId' .
				' SET QQ.Status = %d,QQ.QuestionCategoryId=0,QQV.QuestionCategoryId=0 WHERE QQ.QuestionCategoryId IN (%s)', 
				ARIQUIZ_QUESTION_STATUS_DELETE, 
				$catStr);
		}
		else 
		{
			$query[] = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV' .
				'	 ON QQ.QuestionVersionId = QQV.QuestionVersionId' .
				' SET QQ.QuestionCategoryId = 0, QQV.QuestionCategoryId = 0 WHERE QQ.QuestionCategoryId IN (%s)',  
				$catStr);
		}
		
		foreach ($query as $queryItem)
		{
			$db->setQuery($queryItem);
			if (!$db->execute())
			{
				return false;
			}
		}

		return true;
	}

	function copy($sourceQuizId, $destQuizId, $userId)
	{
		$catMapping = array();

		$db =& $this->getDBO();
		
		$categories = $this->getCategoryList($sourceQuizId);
		if (empty($categories))
			return $catMapping;

		$now = AriDateUtility::getDbUtcDate();
		$questionCategoryModel =& AriModel::getInstance('Questioncategory', $this->getFullPrefix());

		foreach ($categories as $category)
		{
			$copyCategory = $questionCategoryModel->copy($category->QuestionCategoryId, $destQuizId, $userId);
			if (empty($copyCategory))
				return null;

			$catMapping[$category->QuestionCategoryId] = $copyCategory->QuestionCategoryId;
		}

		return $catMapping;
	}

	function update($dataConfigFile, $idList, $fields, $userId)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		if (empty($fields)) 
			return true;
		
		AriKernel::import('Data.DDLManager');

		$ddlManager = new AriDDLManager($dataConfigFile);
		$db =& $this->getDBO();
		
		$entity = 'questioncategory';
		$queryParts = array();
		$idListStr = join(',', $idList);
		$fields['Modified'] = AriDateUtility::getDbUtcDate();
		$fields['ModifiedBy'] = $userId;

		// update main quizzes parameters
		foreach ($fields as $key => $value)
		{
			$fieldInfo = $ddlManager->getFieldInfo($entity, $key);
			if (is_null($fieldInfo)) 
				continue ;

			if ($ddlManager->isBool($entity, $key))
			{
				$value = @intval($value, 10);
				if ($value) 
					$value = 1;
			}
			else if ($ddlManager->isNumberField($entity, $key))
			{
				$value = @intval($value, 10);
			}
			
			$queryParts[] = sprintf('`%s`=%s',
				$key,
				$db->Quote($value));
		}
		
		if (count($queryParts) == 0)
			return false;
		
		$queryParts = join(',', $queryParts);

		$query = sprintf('UPDATE #__ariquizquestioncategory SET %s WHERE QuestionCategoryId IN (%s)',
			$queryParts, 
			$idListStr);

		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}
		
		return true;
	}
	
	function getCategoryMapping($categoryNames, $quizId)
	{		
		$categoryMapping = array();
		if (!is_array($categoryNames) || count($categoryNames) == 0)
			return $categoryMapping;

		$db =& $this->getDBO();
		$query = sprintf('SELECT QuestionCategoryId AS CategoryId,CategoryName FROM #__ariquizquestioncategory WHERE QuizId = %d AND CategoryName IN (%s)',
			$quizId,
			join(',', AriDBUtils::quote($categoryNames))
		);
		$db->setQuery($query);
		$categories = $db->loadAssocList();
		if (is_null($categories))
		{
			return $categoryMapping;
		}

		foreach ($categories as $category)
		{
			$categoryMapping[$category['CategoryName']] = $category['CategoryId'];
		}

		return $categoryMapping;
	}
}