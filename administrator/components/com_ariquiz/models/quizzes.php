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
AriKernel::import('Data.DataFilter');
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Utils.DateUtility');

class AriQuizModelQuizzes extends AriModel 
{
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('quiz');
	}

	function getQuizCount($filter = null) 
	{
		$db =& $this->getDBO();

		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquiz Q');
		$query->leftJoin('#__ariquizquizcategory QQC ON Q.QuizId = QQC.QuizId');
		$query->leftJoin('#__ariquizcategory QC ON QQC.CategoryId = QC.CategoryId');

		$query = $this->_applyQuizFilter($query, $filter);
		
		$db->setQuery((string)$query);
		$count = $db->loadResult();

		return !!$count ? $count : 0;
	}

	function getQuizList($filter = null) 
	{
		$db =& $this->getDBO();
		if (empty($filter))
			$filter = new AriDataFilter();

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'Q.QuizId',
				'Q.QuizName',
				'Q.Status',
				'Q.Access',
				'QC.CategoryName',
				'QC.CategoryId'
			)
		);
		$query->from('#__ariquiz Q');
		$query->leftJoin('#__ariquizquizcategory QQC ON Q.QuizId = QQC.QuizId');
		$query->leftJoin('#__ariquizcategory QC ON QQC.CategoryId = QC.CategoryId');

		$query = $this->_applyQuizFilter($query, $filter);
		if ($filter)
			$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$quizzes = $db->loadObjectList();

		return $quizzes;
	}
	
	function changeQuizStatus($idList, $status)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$status = intval($status, 10);

		$db =& $this->getDBO();
		$query = sprintf('UPDATE #__ariquiz SET Status = %d WHERE QuizId IN (%s)', 
			$status, 
			join(',', $idList));
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}

		return true;
	}
	
	function activateQuiz($idList)
	{
		return $this->changeQuizStatus(
			$idList, 
			ARIQUIZ_QUIZ_STATUS_ACTIVE);
	}

	function deactivateQuiz($idList)
	{
		return $this->changeQuizStatus(
			$idList, 
			ARIQUIZ_QUIZ_STATUS_INACTIVE);
	}

	function deleteQuiz($idList)
	{		
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$db =& $this->getDBO();
		$query = sprintf('DELETE Q,QQC,QQUEC,QQ,QQV,QQVF,QSI,QS,QSA,QQP FROM' .
			' #__ariquiz Q LEFT JOIN #__ariquiz_quiz_questionpool QQP' .
			'	ON Q.QuizId = QQP.QuizId' .
			' LEFT JOIN #__ariquizquizcategory QQC' .
			'	ON Q.QuizId = QQC.QuizId' .
			' LEFT JOIN #__ariquizquestioncategory QQUEC' .
			'	ON Q.QuizId = QQUEC.QuizId' .
			' LEFT JOIN #__ariquizquestion QQ' .
			'	ON Q.QuizId = QQ.QuizId' .
			' LEFT JOIN #__ariquizquestionversion QQV' .
			'	ON QQ.QuestionId = QQV.QuestionId' .
			' LEFT JOIN #__ariquiz_question_version_files QQVF' .
			'	ON QQV.QuestionVersionId = QQVF.QuestionVersionId' .
			' LEFT JOIN #__ariquizstatisticsinfo QSI' .
			'	ON Q.QuizId = QSI.QuizId' .
			' LEFT JOIN #__ariquizstatistics QS' .
			'	ON QSI.StatisticsInfoId = QS.StatisticsInfoId' .
			' LEFT JOIN #__ariquizstatistics_attempt QSA' .
			'	ON QS.StatisticsId = QSA.StatisticsId' .
			' WHERE Q.QuizId IN (%1$s)',
			join(',', $idList));
		
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}
		
		return true;
	}
	
	function copy($idList, $quizName, $userId)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		$quizModel =& AriModel::getInstance('Quiz', $this->getFullPrefix());
		$db =& $this->getDBO();
		
		foreach ($idList as $quizId)
		{
			$quiz = $quizModel->copy($quizId, $quizName, $userId);
			if (empty($quiz))
				return false;
		}
		
		return true;
	}
	
	function update($idList, $fields, $extraFields, $userId)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;

		if ((empty($fields) && empty($extraFields)) || (is_array($fields) && count($fields) == 0 && is_array($extraFields) && count($extraFields) == 0))
			return true;

		$quizModel =& AriModel::getInstance('Quiz', $this->getFullPrefix());
		foreach ($idList as $quizId)
		{
			if (!$quizModel->update($quizId, $fields, $extraFields, $userId)) {
				return false;				
			}
		}

		return true;
	}

	function _applyQuizFilter($query, $filter)
	{
		if (empty($filter))
		{
			$query->where('Status <> ' . ARIQUIZ_QUIZ_STATUS_DELETE);
			return $query;
		}

		$filterPredicates = $filter->getConfigValue('filter');
		if (!empty($filterPredicates['CategoryId']))
		{
			if (!empty($filterPredicates['IncludeSubcategories']))
			{
				$query->where(
					sprintf(
						'(QQC.CategoryId IN (SELECT CC.CategoryId FROM #__ariquizcategory PC,#__ariquizcategory CC WHERE PC.CategoryId = %d AND CC.lft >= PC.lft AND CC.rgt <= PC.rgt))',
						intval($filterPredicates['CategoryId'], 10)
					)
				);
			}
			else
			{
				$query->where('QQC.CategoryId=' . intval($filterPredicates['CategoryId'], 10));
			}
		} 

		if (!empty($filterPredicates['Status'])) 
			$query->where('Q.Status=' . intval($filterPredicates['Status'], 10));
		else
			$query->where('Q.Status <> ' . ARIQUIZ_QUIZ_STATUS_DELETE);

        if (!empty($filterPredicates['IgnoreQuizId']))
        {
            $ignoreQuizId = $filterPredicates['IgnoreQuizId'];
            if (is_string($ignoreQuizId))
                $ignoreQuizId = explode(',', $ignoreQuizId);

            $ignoreQuizId = AriArrayHelper::toInteger($ignoreQuizId, 1);
            if (count($ignoreQuizId) > 0)
                $query->where('Q.QuizId NOT IN (' . join(',', $ignoreQuizId) . ')');
        }

		return $query;
	}
}