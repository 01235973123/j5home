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

class AriQuizModelBankquestions extends AriModel 
{
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('quizquestion');
		$this->getTable('quiz'); 
	}
	
	function getQuestionCount($filter = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizquestion QQ');

		$query->where('QQ.QuizId = 0');
		$query->where('QQ.Status <> ' . ARIQUIZ_QUESTION_STATUS_DELETE);
		
		$query = $this->_applyBankFilter($query, $filter);

		$db->setQuery((string)$query);
		$count = $db->loadResult();

		if (is_null($count))
		{
			return 0;
		}

		return $count;		
	}
	
	function getQuestionList($filter = null)
	{
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		
		$query->select(
			array(
				'QQ.QuestionId',
				'QQ.QuestionId AS QuestionId2',
				'QQV.Question',
				'QQT.QuestionType',
				'QQT.ClassName AS QuestionTypeClass',
				'QQV.Created',
				'QBC.CategoryId',
				'QBC.CategoryName'
			)
		);
		$query->from('#__ariquizquestion QQ');
		$query->innerJoin('#__ariquizquestionversion QQV ON QQ.QuestionVersionId = QQV.QuestionVersionId');
		$query->leftJoin('#__ariquizquestiontype QQT ON QQ.QuestionTypeId = QQT.QuestionTypeId');
		$query->leftJoin('#__ariquizbankcategory QBC ON QQ.QuestionCategoryId = QBC.CategoryId');

		$query->where('QQ.Status <> ' . ARIQUIZ_QUESTION_STATUS_DELETE);
		$query->where('QQ.QuizId = 0');

		$query = $this->_applyBankFilter($query, $filter);
		if ($filter)
			$query = $filter->applyToQuery($query);		

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));

		return $db->loadObjectList();
	}

	function update($idList, $fields, $userId)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		if (empty($fields) || (is_array($fields) && count($fields) == 0))
			return true;

		$questionModel =& AriModel::getInstance('Bankquestion', $this->getFullPrefix());
		foreach ($idList as $questionId)
		{
			if (!$questionModel->update($questionId, $fields, $userId))
				return false;
		}
		
		return true;
	}
	
	function deleteQuestions($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
		
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf(
				'UPDATE 
					#__ariquizquestion Q,
					(
						SELECT
							BQ.QuestionId AS QuestionId
						FROM
							#__ariquizquestion BQ LEFT JOIN #__ariquizquestion QQ
								ON QQ.BankQuestionId = BQ.QuestionId AND QQ.Status <> %1$d
						WHERE
							BQ.QuizId = 0
							AND
							BQ.QuestionId IN (%2$s)
							AND
							QQ.QuestionId IS NULL
						GROUP BY 
							BQ.QuestionId
					) T
				SET 
					Q.Status = %1$d 
				WHERE 
					Q.QuestionId = T.QuestionId',
				ARIQUIZ_QUESTION_STATUS_DELETE,
				join(',', $idList)
			)
		);

		return $db->execute();
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
				'SELECT DISTINCT C.CategoryId,C.CategoryName FROM  #__ariquizquestion Q INNER JOIN #__ariquizbankcategory C ON Q.QuestionCategoryId = C.CategoryId WHERE Q.QuizId = 0 AND Q.QuestionId IN (%s)',
				join(',', $idList)
			)
		);
		$catList = $db->loadAssocList();
		
		if (is_array($catList))
			foreach ($catList as $cat)
			{
				$categories[$cat['CategoryId']] = $cat['CategoryName'];
			}
			
		return $categories;
	}
	
	function _applyBankFilter($query, $filter)
	{
		if (empty($filter))
			return $query;

		$filterPredicates = $filter->getConfigValue('filter');
		if (!empty($filterPredicates['CategoryId']))
			$query->where('QQ.QuestionCategoryId=' . intval($filterPredicates['CategoryId'], 10));

        if (!empty($filterPredicates['Id']))
        {
            $questionId = intval($filterPredicates['Id'], 10);
            if ($questionId > 0)
                $query->where('QQ.QuestionId = ' . $questionId);
        }

		$notLoadUsedQuestions = !empty($filterPredicates['QuizId']) && !empty($filterPredicates['NotLoadUsedQuestions']);
		if ($notLoadUsedQuestions)
		{
			$query->leftJoin(sprintf('
				(SELECT QQ1.QuestionId,COUNT(QQ1.QuestionId) AS QuestionCount
				 	FROM
			 			#__ariquizquestion QQ1 INNER JOIN #__ariquizquestion BQ 
  							ON QQ1.QuestionId = BQ.BankQuestionId
					WHERE
  			   			BQ.`Status` = %1$d AND BQ.QuizId = %2$d
					GROUP BY 
						QQ1.QuestionId
				) T
				ON QQ.QuestionId = T.QuestionId',
				ARIQUIZ_QUESTION_STATUS_ACTIVE,
				$filterPredicates['QuizId'])
			);
			
			$query->where('IFNULL(T.QuestionCount, 0) = 0');
		}
		
		return $query;
	}
}