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

class AriQuizModelQuizquestions extends AriModel 
{
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('quizquestion');
		$this->getTable('quiz'); 
	}
	
	function getQuestionCount($quizId, $filter = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestion QQ');

		$query->where('QQ.QuizId = ' . intval($quizId, 10));
		$query->where('QQ.Status <> ' . ARIQUIZ_QUESTION_STATUS_DELETE);

        $query = $this->_applyFilter($query, $filter);

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;		
	}
	
	function getQuestionList($quizId, $filter = null)
	{
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		
		$query->select(
			array(
				'Q.QuizName',
				'Q.QuizId',
				'QQ.QuestionId',
				'QQ.BankQuestionId',
				'QQV.Question',
				'QQT.QuestionType',
				'QQT.ClassName AS QuestionTypeClass',
				'QQV.Created',
				'QQC.QuestionCategoryId',
				'QQC.CategoryName',
				'QQ.QuestionIndex AS QuestionIndex2',
				'QQ.Status'
			)
		);
		$query->from('#__ariquiz Q');
		$query->innerJoin('#__ariquizquestion QQ ON Q.QuizId = QQ.QuizId');
		$query->leftJoin('#__ariquizquestion QQ2 ON QQ.BankQuestionId = QQ2.QuestionId');
		$query->innerJoin('#__ariquizquestionversion QQV ON IFNULL(QQ2.QuestionVersionId, QQ.QuestionVersionId) = QQV.QuestionVersionId');
		$query->leftJoin('#__ariquizquestiontype QQT ON IFNULL(QQ2.QuestionTypeId, QQ.QuestionTypeId) = QQT.QuestionTypeId');
		$query->leftJoin('#__ariquizquestioncategory QQC ON QQ.QuestionCategoryId = QQC.QuestionCategoryId');
		
		$query->where('QQ.Status <> ' . ARIQUIZ_QUESTION_STATUS_DELETE);
		$query->where('QQ.QuizId = ' . intval($quizId, 10));

        $query = $this->_applyFilter($query, $filter);

		if ($filter)
			$query = $filter->applyToQuery($query);		

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));

		$questions = $db->loadObjectList();
		if (is_null($questions))
		{
			return null;
		}
	
		return $questions;
	}

    function _applyFilter($query, $filter)
    {
        if (empty($filter))
            return $query;

        $filterPredicates = $filter->getConfigValue('filter');
        if (!empty($filterPredicates['Id']))
        {
            $questionId = intval($filterPredicates['Id'], 10);
            if ($questionId > 0)
                $query->where('QQ.QuestionId = ' . $questionId);
        }

        return $query;
    }

    function copy($idList, $quizId, $questionCategoryId, $userId)
	{
		if ($quizId < 1) 
			return false;

		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$questionModel =& AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		$qIndex = $this->getMaxQuestionIndex($quizId) + 1;
		foreach ($idList as $questionId)
		{
			$question = $questionModel->copy($questionId, $quizId, $questionCategoryId, $qIndex, $userId);
			if (empty($question))
				return false;
			
			++$qIndex;
		}
		
		return true;
	}

	function move($idList, $quizId, $questionCategoryId, $userId)
	{
		if ($quizId < 1 || $questionCategoryId < 0) 
			return false;
			
		$fields = array(
			'QuizId' => $quizId,
			'QuestionCategoryId' => $questionCategoryId
		);

		return $this->update($idList, $fields, $userId);
	}
	
	function deleteQuestions($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf(
				'UPDATE #__ariquizquestion SET Status = %1$d WHERE QuestionId IN (%2$s)',
				ARIQUIZ_QUESTION_STATUS_DELETE,
				join(',', $idList)
			)
		);

		if (!$db->execute())
		{
			return false;
		}

		return true;
	}
	
	function update($idList, $fields, $userId, $ignoreIndex = false)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		if (empty($fields) || (is_array($fields) && count($fields) == 0))
			return true;

		$qIndex = -1;
		if (!$ignoreIndex)
			$qIndex = $this->getMaxQuestionIndex($fields['QuizId']) + 1;

		$questionModel =& AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		foreach ($idList as $questionId)
		{
			if (!$ignoreIndex)
				$fields['QuestionIndex'] = $qIndex;

			if (!$questionModel->update($questionId, $fields, $userId))
				return false;
				
			if (!$ignoreIndex)
				++$qIndex;
		}
		
		return true;
	}
	
	function copyToBank($idList, $fields, $userId)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$categoryId = isset($fields['QuestionCategoryId']) ? intval($fields['QuestionCategoryId'], 10) : 0;
		$basedOnBank = isset($fields['BasedOnBank']) ? (bool)$fields['BasedOnBank'] : false;
		
		$qIndex = $this->getMaxQuestionIndex(0) + 1;
		
		$questionModel =& AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		foreach ($idList as $questionId)
		{
			$question = $questionModel->copyToBank($questionId, $qIndex, $categoryId, $basedOnBank, $userId);
			if (empty($question))
				continue;
						
			++$qIndex;
		}
		
		return true;
	}
	
	function addQuestionsFromBank($idList, $quizId, $categoryId, $score, $userId)
	{
		$quizId = intval($quizId, 10);
		$categoryId = intval($categoryId, 10);
		
		if (is_string($score)) 
			$score = trim($score);
		$score = strlen($score) > 0
			? @intval($score, 10)
			: null;
	
		if ($quizId < 1 || $categoryId < 0)
			return false;

		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		$questionModel =& AriModel::getInstance('Quizquestion', $this->getFullPrefix());
		foreach ($idList as $questionId)
		{
			$question = $questionModel->addQuestionFromBank($questionId, $quizId, $categoryId, $score, $userId);
		}
		
		return true;
	}
	
	function getMaxQuestionIndex($quizId)
	{		
		$quizId = @intval($quizId, 10);
		
		if ($quizId < 0) 
			return -1;

		$db =& $this->getDBO();	
		$db->setQuery(
			sprintf(
				'SELECT QuestionIndex FROM #__ariquizquestion QQ WHERE QQ.QuizId = %d ORDER BY QuestionIndex DESC LIMIT 0,1',
				$quizId
			)
		);
		$index = $db->loadResult();
		if (is_null($index))
		{
			return -1;
		}

		return $index;
	}
	
	function changeQuestionOrder($questionId, $dir)
	{
		$questionId = intval($questionId, 10);
		$dir = intval($dir, 10);
		
		if ($questionId < 1 || $dir == 0)
			return false;
		
		$db =& $this->getDBO();

		$query = sprintf(
			'SELECT 
				QQ1.QuestionId, 
				QQ.QuestionIndex AS OldIndex, 
				QQ1.QuestionIndex AS NewIndex
			FROM #__ariquizquestion QQ LEFT JOIN #__ariquizquestion QQ1
			 	ON QQ.QuizId = QQ1.QuizId
			WHERE
				QQ.QuestionId = %d
				AND 
				QQ1.QuestionIndex %s QQ.QuestionIndex 
				AND 
				QQ1.Status = %d 
			ORDER BY 
				QQ1.QuestionIndex %s 
			LIMIT 0,1',
			$questionId,
			$dir > 0 ? '>' : '<',
			ARIQUIZ_QUESTION_STATUS_ACTIVE,
			$dir > 0 ? 'ASC' : 'DESC');
		
		$db->setQuery($query);
		$obj = $db->loadAssocList();
		if (is_null($obj))
		{
			return false;
		}

		if (is_array($obj) && count($obj) > 0)
		{
			$obj = $obj[0];
			$queryList = array();
			$queryList[] = sprintf('UPDATE #__ariquizquestion SET QuestionIndex = %d WHERE QuestionId = %d',
				$obj['NewIndex'],
				$questionId);
			$queryList[] = sprintf('UPDATE #__ariquizquestion SET QuestionIndex = %d WHERE QuestionId = %d',
				$obj['OldIndex'],
				$obj['QuestionId']);
				
			foreach ($queryList as $queryItem)
			{
				$db->setQuery($queryItem);
				if (!$db->execute())
				{
					return false;
				}
			}				
		}
		
		return true;
	}
	
	function getCategoriesByQuestionId($idList)
	{
		$categories = array();
		
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return $categories;
			
		$db = $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT DISTINCT C.QuestionCategoryId AS CategoryId,C.CategoryName FROM  #__ariquizquestion Q INNER JOIN #__ariquizquestioncategory C ON Q.QuestionCategoryId = C.QuestionCategoryId WHERE Q.QuizId > 0 AND Q.QuestionId IN (%s)',
				join(',', $idList)
			)
		);
		$catList = $db->loadAssocList();
		
		if (is_null($catList))
		{
			return $categories;
		}
		
		if (is_array($catList))
			foreach ($catList as $cat)
			{
				$categories[$cat['CategoryId']] = $cat['CategoryName'];
			}
			
		return $categories;
	}

	function changeQuestionStatus($idList, $status)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$status = intval($status, 10);

		$db = $this->getDBO();
		$query = sprintf('UPDATE #__ariquizquestion SET Status = %d WHERE QuestionId IN (%s) AND QuizId > 0', 
			$status, 
			join(',', $idList));
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}

		return true;
	}
	
	function activateQuestion($idList)
	{
		return $this->changeQuestionStatus(
			$idList, 
			ARIQUIZ_QUESTION_STATUS_ACTIVE);
	}

	function deactivateQuestion($idList)
	{
		return $this->changeQuestionStatus(
			$idList, 
			ARIQUIZ_QUESTION_STATUS_INACTIVE);
	}
}