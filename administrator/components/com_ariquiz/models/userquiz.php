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
AriKernel::import('Utils.DateUtility');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Application.ARIQuiz.Utils.QuizStorage');
AriKernel::import('Utils.ArrayHelper');

define('ARIQUIZ_TAKEQUIZERROR_NONE', 0);
define('ARIQUIZ_TAKEQUIZERROR_LAGTIME', 1);
define('ARIQUIZ_TAKEQUIZERROR_ATTEMPTCOUNT', 2);
define('ARIQUIZ_TAKEQUIZERROR_NOTREGISTERED', 3);
define('ARIQUIZ_TAKEQUIZERROR_NOTHAVEPERMISSIONS', 4);
define('ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR', 5);
define('ARIQUIZ_TAKEQUIZERROR_HASPAUSEDQUIZ', 6);
define('ARIQUIZ_TAKEQUIZERROR_DATEACCESS', 7);
define('ARIQUIZ_TAKEQUIZERROR_ANOTHERUSER', 8);
define('ARIQUIZ_TAKEQUIZERROR_PREVQUIZ', 9);

define('ARIQUIZ_USERGROUP_REGISTERED', 18);

class AriQuizModelUserQuiz extends AriModel 
{
	var $QUESTION_PART_COUNT = 80;
	var $GUEST_TICKET_KEY = 'ariQuizTicketId';
	
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('Question');
		$this->getTable('Userquiz');
	}
	
	function getQuizStorage($quizId, $ticketId, $user, $init = true)
	{
		$storage = new AriQuizStorage($this, $quizId, $ticketId, $user, $init);
		
		return $storage;
	}

	function getGuestTicketKey()
	{
		return $this->GUEST_TICKET_KEY;
	}

	function getGuestTicketId($quizId)
	{
		$key = $this->getGuestTicketKey();
		$ticketId = null;
		if (!empty($_COOKIE[$key]))
		{
			// Data is stored in cookie in the next way: ticketId:quizId , where ':' is separator
			@list($cookieTicketId, $cookieQuizId) = explode(':', $_COOKIE[$key]);
			if ($cookieQuizId == $quizId)
			{
				if ($this->isValidTicketId($cookieTicketId, $quizId, 0))
				{
					$ticketId = $cookieTicketId;
				}
				else 
				{
					$this->clearGuestTicketId();
				}
			}
		}

		return $ticketId;
	}
	
	function clearGuestTicketId()
	{
		$key = $this->getGuestTicketKey();
		setcookie($key, '', time() - 3 * 24 * 3600, '/');
		if (isset($_COOKIE[$key]))
			$_COOKIE[$key] = null;
	}
	
	function saveGuestTicketId($ticketId, $quizId)
	{
		$key = $this->getGuestTicketKey();
		setcookie($key, $ticketId . ':' . $quizId, time() + 3 * 24 * 3600, '/');
	}

	function createTicketId($quizId, $userId = 0, $extraData = null)
	{
		$db =& $this->getDBO();
		
		$quizId = intval($quizId, 10);
		$userId = intval($userId, 10);
		$ticketId = $this->generateTicketId();

		$createdDate = AriDateUtility::getDbUtcDate();

		$statisticsInfo =& $this->getTable('Userquiz');
		$extraDataXml = $statisticsInfo->getExtraDataXml($extraData);
		$query = sprintf('INSERT INTO #__ariquizstatisticsinfo (QuizId,UserId,Status,TicketId,CreatedDate,ExtraData,PageCount,UserScorePercent)' . 
			' VALUES(%1$d,%2$d,%6$s,%3$s,%4$s,%5$s,0,0)', 
			$quizId, 
			$userId,
			$db->Quote($ticketId),
			$db->Quote($createdDate),
			empty($extraDataXml) ? 'NULL' : $db->Quote($extraDataXml),
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PREPARE)
		);
			
		$db->setQuery($query);
		if (!$db->execute()) 
		{
			return null;
		}
		
		return $ticketId;
	}
	
	function isValidTicketId($ticketId, $quizId = null, $userId = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizstatisticsinfo');
		$query->where('TicketId = ' . $db->Quote($ticketId));
		
		if (!is_null($quizId))
			$query->where('QuizId = ' . intval($quizId, 10));
			
		if (!is_null($userId))
			$query->where('UserId = ' . intval($userId, 10));
			
		$query->where('Status <> ' . $db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
			
		$db->setQuery((string)$query);
		$res = $db->loadResult();

		return !empty($res);
	}
	
	function generateTicketId()
	{
		mt_srand((float)microtime() * 1000000);
		$key = mt_rand();
		
		return md5(uniqid($key, true));
	}
	
	function composeUserQuiz($quizId, $userId, $extraData, &$rQuestionCount)
	{
		$rQuestionCount = -1;
		
		$quizId = intval($quizId, 10);
		$userId = intval($userId, 10);

		$quizModel =& AriModel::getInstance('Quiz', $this->getFullPrefix());
		$quiz = $quizModel->getQuiz($quizId);		
		if (empty($quiz) || empty($quiz->QuizId) || $quiz->Status != ARIQUIZ_QUIZ_STATUS_ACTIVE)
			return null;

		$questionCatModel =& AriModel::getInstance('Questioncategories', $this->getFullPrefix());
		$qCategoryList = $questionCatModel->getCategoryList($quizId, null, true);		
		if (!is_array($qCategoryList))
			$qCategoryList = array();

		// add uncategory questions
		$uncategory = new stdClass();
		$uncategory->QuestionCategoryId = 0;
		$uncategory->QuestionCount = 0;//$quiz->QuestionCount;
		$uncategory->QuestionTime = $quiz->QuestionTime;
		$uncategory->Description = '';
		$uncategory->QuestionPool = array();
		$qCategoryList[] = $uncategory;

		$defaultQuestionTime = $quiz->QuestionTime;
		$questions = $this->composeQuizQuestions($quizId, $quiz->RandomQuestion, $qCategoryList, $defaultQuestionTime);
		if (empty($questions))
		{
			$rQuestionCount = 0;
			return null;
		}

		$qCategoryList = AriArrayHelper::toAssoc($qCategoryList, 'QuestionCategoryId');
		$ticketId = null;
		$questionCount = !empty($questions) ? count($questions) : 0;		
		if ($questionCount > 0)
		{
			if ($quiz->QuestionCount > 0 && $questionCount > $quiz->QuestionCount)
			{
				$queKeys = array_rand($questions, $quiz->QuestionCount);
				if (!is_array($queKeys)) $queKeys = array($queKeys);
				$tempQuestions = array();
				foreach ($queKeys as $key)
					$tempQuestions[] = $questions[$key];

				$questions = $tempQuestions;	
				$questionCount = $quiz->QuestionCount;
			}
			$queryList = array();
			$index = 0;
			
			$ticketId = $this->createTicketId($quizId, $userId, $extraData);
			if (empty($ticketId))
				return null;

			$statisticsId = $this->getStatisticsInfoIdByTicketId($ticketId, 0, ARIQUIZ_USERQUIZ_STATUS_PREPARE);
			if (empty($statisticsId))
				return null;			
				
			
			$queryList[] = sprintf('DELETE S,SF FROM #__ariquizstatistics S LEFT JOIN #__ariquizstatistics_files SF ON S.StatisticsId = SF.StatisticsId WHERE S.StatisticsInfoId = %d', $statisticsId);
			$queryList[] = sprintf('DELETE FROM #__ariquizstatistics_pages WHERE StatisticsInfoId = %d', $statisticsId);

			$db =& $this->getDBO();
			$queryValues = array();
			$paging = $quiz->getParam('Paging', 'OnePerPage');
			$pageCount = 0;
			if ($paging == 'SeparateByCat')
			{	
				$catPagesMapping = array();
				foreach ($questions as $key => $question)
				{
					$catId = $question->QuestionCategoryId;
					if (!isset($catPagesMapping[$catId]))
						$catPagesMapping[$catId] = array(
							'PageNumber' => count($catPagesMapping),
							'QuestionCount' => 0,
							'Questions' => array()
						);
					
					$catPagesMapping[$catId]['Questions'][] = $question;
					++$catPagesMapping[$catId]['QuestionCount'];
					$questions[$key]->PageNumber = $catPagesMapping[$catId]['PageNumber'];
				}

				$sortedQuestions = array();
				foreach ($catPagesMapping as $catId => $pageInfo)
				{
					$pageTime = isset($qCategoryList[$catId]) ? $qCategoryList[$catId]->QuestionTime : 0;
					
					$sortedQuestions = array_merge($sortedQuestions, $pageInfo['Questions']);
					
					$queryValues[] = sprintf('(%d,%d,%d,%d,%d,%s,0,0)',
						$statisticsId,
						$pageInfo['PageNumber'],
						$pageInfo['PageNumber'],
						$pageInfo['QuestionCount'],
						$pageTime,
						$db->Quote(isset($qCategoryList[$catId]) ? $qCategoryList[$catId]->Description : '')
					);
				}

				$questions = $sortedQuestions;
			}
			else 
			{
				$questionsPerPage = 1;
				if ($paging == 'AllOnPage')
					$questionsPerPage = $questionCount;
				else if ($paging == 'CustomNumber')
				{
					$questionsPerPage = intval($quiz->getParam('Paging_CustomNumber_PageSize'), 10);
					if ($questionsPerPage < 1)
						$questionsPerPage = 1;
				}

				$idx = 0;
				foreach ($questions as $key => $question)
				{
					$questions[$key]->PageNumber = floor($idx / $questionsPerPage);
					++$idx;
				}

				$maxPageNum = ceil($questionCount / $questionsPerPage) - 1;
				for ($pageNum = 0; $pageNum <= $maxPageNum; $pageNum++)
				{
					$pageTime = $quiz->QuestionTime;
					if ($questionsPerPage == 1)
					{
						$question = $questions[$pageNum];
						$catId = $question->QuestionCategoryId;
						if (isset($qCategoryList[$catId]))
							$pageTime = $qCategoryList[$catId]->QuestionTime;
					}

					$queryValues[] = sprintf('(%d,%d,%d,%d,%d,"",0,0)',
						$statisticsId,
						$pageNum,
						$pageNum,
						$pageNum < $maxPageNum ? $questionsPerPage : $questionCount - ($questionsPerPage * $pageNum),
						$pageTime
					);
				}
			}
			$pageCount = count($queryValues);

			while ($index < count($queryValues))
			{
				$queryList[] = 'INSERT INTO #__ariquizstatistics_pages (StatisticsInfoId,PageNumber,PageIndex,QuestionCount,PageTime,Description,SkipCount,UsedTime) VALUES' . join(',', array_slice($queryValues, $index, $this->QUESTION_PART_COUNT)); 

				$index += $this->QUESTION_PART_COUNT;
			}

			$index = 0;
			$queryValues = array(); 
			foreach ($questions as $question)
			{
				$queryValues[] = sprintf('(%d,%d,%d,%d,%d,%d,%d,%f,%d)',
					$question->QuestionId, 
					$question->QuestionVersionId, 
					$statisticsId, 
					$index, 
					$question->QuestionCategoryId,
					!empty($question->BankQuestionId) ? $question->BankQuestionId : 0,
					!empty($question->BankVersionId) ? $question->BankVersionId : 0,
					-$question->Penalty,
					$question->PageNumber
				);
				++$index;

				if ($index % $this->QUESTION_PART_COUNT == 0)
				{
					$queryList[] = 'INSERT INTO #__ariquizstatistics (QuestionId,QuestionVersionId,StatisticsInfoId,QuestionIndex,QuestionCategoryId,BankQuestionId,BankVersionId,Score,PageNumber) VALUES' . join(',', $queryValues);
					$queryValues = array();
				}
			}

			if ($index % $this->QUESTION_PART_COUNT != 0 && count($queryValues) > 0)
			{
				$queryList[] = 'INSERT INTO #__ariquizstatistics (QuestionId,QuestionVersionId,StatisticsInfoId,QuestionIndex,QuestionCategoryId,BankQuestionId,BankVersionId,Score,PageNumber) VALUES' . join(',', $queryValues);
			}
			
			$queryList[] = sprintf('UPDATE #__ariquizstatistics S, #__ariquizstatistics_pages SP SET S.PageId = SP.PageId WHERE S.StatisticsInfoId = %d AND S.StatisticsInfoId = SP.StatisticsInfoId AND S.PageNumber = SP.PageNumber',
				$statisticsId);
			
			$queryList[] = sprintf('UPDATE #__ariquizstatisticsinfo' . 
				' SET Status = %6$s, StartDate = NULL, PassedScore = %1$f, QuestionCount = %2$d, TotalTime = %3$s, PageCount = %7$d' .
				' WHERE StatisticsInfoId = %4$d AND Status = %5$s', 
				$quiz->PassedScore,
				$questionCount,
				is_null($quiz->TotalTime) ? 'NULL' : $quiz->TotalTime, 
				$statisticsId,
				$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PREPARE),
				$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PROCESS),
				$pageCount
			);
			$queryList[] = sprintf('INSERT INTO #__ariquizstatistics_files (StatisticsInfoId,StatisticsId,QuestionId,FileVersionId,Alias)
 				SELECT
 					S.StatisticsInfoId,
 					S.StatisticsId,
   					S.QuestionId,
   					F.FileVersionId,
   					QVF.Alias
 				FROM
   					#__ariquizstatistics S INNER JOIN #__ariquiz_question_version_files QVF
     					ON S.QuestionVersionId = QVF.QuestionVersionId OR S.BankVersionId = QVF.QuestionVersionId
   					INNER JOIN #__ariquiz_file F
     					ON QVF.FileId = F.FileId
     				INNER JOIN #__ariquiz_file_versions FV
     					ON F.FileVersionId = FV.FileVersionId
 				WHERE
   					S.StatisticsInfoId = %d
			',
			$statisticsId);
			
			if (is_array($extraData) && count($extraData) > 0)
			{
				$query = 'INSERT INTO #__ariquiz_statistics_extradata (StatisticsInfoId,Name,Value) VALUES ';
				$values = array();
				foreach ($extraData as $itemName => $itemValue)
				{
					$values[] = sprintf('(%d,%s,%s)',
						$statisticsId,
						$db->Quote($itemName),
						$db->Quote($itemValue)
					);
				}
				
				$queryList[] = $query . join(',', $values);
			}

			foreach ($queryList as $queryItem)
			{
				$db->setQuery($queryItem);
				if (!$db->execute())
				{
					return false;
				}
			}				
		}
		
		$rQuestionCount = $questionCount;

		return $ticketId;
	}
	
	function composeQuizQuestions($quizId, $randomQuestion, $qCategoryList, $defaultQuestionTime)
	{
		$questions = array();
		if (empty($qCategoryList))
			return $questions;

		$poolQuestions = $this->getQuestionPool($qCategoryList);
		foreach ($qCategoryList as $qCategory)
		{
			$curQuestionTime = !empty($qCategory->QuestionTime) ? $qCategory->QuestionTime : $defaultQuestionTime;
			$questionCount = $qCategory->QuestionCount;
			$categoryId = $qCategory->QuestionCategoryId;
			$catQuestions = $randomQuestion 
				? $this->getRandomQuestions($quizId, $questionCount, $categoryId)
				: $this->getOrderedQuestions($quizId, $questionCount, $categoryId);

			$count = is_array($catQuestions) ? count($catQuestions) : 0;
			if (!empty($poolQuestions[$categoryId]) && ($questionCount == 0 || $questionCount > $count))
			{
				$catPoolQuestions = $poolQuestions[$categoryId];
				if ($questionCount > 0)
				{
					shuffle($catPoolQuestions);
					$catPoolQuestions = array_slice($catPoolQuestions, 0, $questionCount - $count);
				}
				
				for ($i = 0; $i < count($catPoolQuestions); $i++)
					$catPoolQuestions[$i]->QuestionCategoryId = $categoryId;

				$catQuestions = array_merge($catQuestions, $catPoolQuestions);
				$count = is_array($catQuestions) ? count($catQuestions) : 0;
			}

			if ($count > 0)
			{
				if (!empty($curQuestionTime))
				{
					for ($i = 0; $i < $count; $i++)
					{
						$question =& $catQuestions[$i];
						if (empty($question->QuestionTime))
							$question->QuestionTime = $curQuestionTime;
					}
				}

				$questions = array_merge($questions, $catQuestions);
			}
		}

		$questions = $randomQuestion
			? $this->normalizeRandomQuestions($questions)
			: $this->normalizeOrderedQuestions($questions);

		return $questions;
	}
	
	function getQuestionPool($categoryList)
	{
		$questions = array();
		if (!is_array($categoryList) || count($categoryList) == 0)
			return $questions;

		$db =& $this->getDBO();

		$poolData = array();
		$unlimitedCategories = array();
		foreach ($categoryList as $category)
		{
			$questionPool = AriUtils::getParam($category, 'QuestionPool');

			if (!is_array($questionPool) || count($questionPool) == 0)
				continue ;

			foreach ($questionPool as $catQuestionPool)
			{
				$bankCategoryId = $catQuestionPool->BankCategoryId;
				$questionCount = $catQuestionPool->QuestionCount;
				
				if ($questionCount == 0)
					$unlimitedCategories[] = $bankCategoryId;

				if (!isset($poolData[$bankCategoryId]))
					$poolData[$bankCategoryId] = $catQuestionPool->QuestionCount;
				else if ($questionCount > 0 && $poolData[$bankCategoryId] > 0)
					$poolData[$bankCategoryId] += $questionCount;
				else 
					$poolData[$bankCategoryId] = 0;
			}
		}

		$query = array();
		foreach ($poolData as $bankCategoryId => $questionCount)
		{						
			$query[] = sprintf(
				'(SELECT SQ.QuestionId,SQ.QuestionVersionId,0 AS QuestionIndex,SQ.QuestionCategoryId,SQ.QuestionId AS BankQuestionId, SQ.QuestionVersionId AS BankVersionId, SQV.Penalty ' .
				'FROM #__ariquizquestion SQ ' .
				'	INNER JOIN #__ariquizquestionversion SQV ON SQ.QuestionVersionId = SQV.QuestionVersionId ' .
				'WHERE SQ.QuizId = 0 AND SQ.QuestionCategoryId = %1$d AND SQ.Status = ' . ARIQUIZ_QUESTION_STATUS_ACTIVE . ' ' .
				'ORDER BY RAND() ' .
				($questionCount > 0 ? 'LIMIT 0,' . $questionCount : '') . 
				')',
				$bankCategoryId
			);
		}

		if (count($query) == 0)
			return $questions;
			
		$query = join(' UNION ', $query);
		$db->setQuery($query);
		$catQuestionsPool = $db->loadObjectList();
		if (is_null($catQuestionsPool))
		{
			return $questions;
		}
		
		$questionsByCategories = array();
		foreach ($catQuestionsPool as $catQuestion)
		{
			$bankCategoryId = $catQuestion->QuestionCategoryId;
			if (!isset($questionsByCategories[$bankCategoryId]))
				$questionsByCategories[$bankCategoryId] = array();
				
			$questionsByCategories[$bankCategoryId][] = $catQuestion;
		}

		foreach ($categoryList as $category)
		{
			$questionPool = AriUtils::getParam($category, 'QuestionPool');
			if (!is_array($questionPool) || count($questionPool) == 0)
				continue ;

			$questionCategoryId = $category->QuestionCategoryId;
			$questions[$questionCategoryId] = array();
			foreach ($questionPool as $catQuestionPool)
			{
				$bankCategoryId = $catQuestionPool->BankCategoryId;
				$questionCount = $catQuestionPool->QuestionCount;
				
				if (empty($questionsByCategories[$bankCategoryId]))
					continue ;

				if ($questionCount == 0)
				{
					$questions[$questionCategoryId] = array_merge($questions[$questionCategoryId], $questionsByCategories[$bankCategoryId]);
					$questionsByCategories[$bankCategoryId] = null;
				}
				else if (!in_array($bankCategoryId, $unlimitedCategories))
				{
					$questions[$questionCategoryId] = array_merge($questions[$questionCategoryId], array_splice($questionsByCategories[$bankCategoryId], 0, $questionCount));
				}
			}
		}

		return $questions;
	}
	
	function normalizeOrderedQuestions($questions)
	{
		$newQuestions = array();
		if (empty($questions))
			return $newQuestions;

		$unorderedQuestions = array();
		foreach ($questions as $question)
		{
			if (!isset($newQuestions[$question->QuestionIndex]))
				$newQuestions[$question->QuestionIndex] = $question;
			else 
				$unorderedQuestions[] = $question;
		}

		ksort($newQuestions);
		
		$newQuestions = array_values($newQuestions);
		if (count($unorderedQuestions))
			$newQuestions = array_merge($newQuestions, $unorderedQuestions); 

		return $newQuestions;
	}
	
	function normalizeRandomQuestions($questions)
	{
		if (empty($questions))
			return $questions;

		srand((float) microtime() * 10000000);
		shuffle($questions);

		return $questions;
	}
	
	function getOrderedQuestions($quizId, $questionCount = null, $qCategoryId = 0)
	{
		$questions = $this->getQuestionsForUserQuiz($quizId, ' QuestionIndex', $questionCount, $qCategoryId);

		return $questions;
	}
	
	function getRandomQuestions($quizId, $questionCount = null, $qCategoryId = 0)
	{
		$questions = $this->getQuestionsForUserQuiz($quizId, ' RAND()', $questionCount, $qCategoryId);
		
		return $questions;
	}

	function getQuestionsForUserQuiz($quizId, $orderStr, $questionCount = 0, $qCategoryId = 0)
	{
		$catPredicate = '';
		if (!is_null($qCategoryId))
		{
			if (empty($qCategoryId)) $qCategoryId = 0;
			$catPredicate = sprintf(' AND SQ.QuestionCategoryId = %d', $qCategoryId);
		}

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select('SQ.QuestionId,SQ.QuestionVersionId,SQ.QuestionIndex,SQ.QuestionCategoryId,SQ2.QuestionId AS BankQuestionId, SQ2.QuestionVersionId AS BankVersionId, (IF(SQ.BankQuestionId > 0, IF(SQV.Penalty <> 0, SQV.Penalty, SQV2.Penalty), SQV.Penalty)) AS Penalty');
		$query->from('#__ariquizquestion SQ');
		$query->leftJoin('#__ariquizquestionversion SQV ON SQ.QuestionVersionId = SQV.QuestionVersionId');
		$query->leftJoin('#__ariquizquestion SQ2 ON SQ.BankQuestionId = SQ2.QuestionId');
		$query->leftJoin('#__ariquizquestionversion SQV2 ON SQ2.QuestionVersionId = SQV2.QuestionVersionId');
		$query->where('SQ.Status = ' . ARIQUIZ_QUESTION_STATUS_ACTIVE);
		$query->where('SQ.QuizId = ' . intval($quizId, 10));
		if (!is_null($qCategoryId))
		{
			if (empty($qCategoryId)) 
				$qCategoryId = 0;
			$query->where('SQ.QuestionCategoryId = ' . intval($qCategoryId, 10));
		}
		
		$query->order($orderStr);
		$db->setQuery((string)$query, 0, $questionCount);

		return $db->loadObjectList();
	}

	function hasPausedQuiz($quizId, $userId)
	{
		$db =& $this->getDBO();

		$quizId = @intval($quizId, 10);
		if ($quizId < 1) 
			return false;

		$userId = @intval($userId, 10);
		if ($userId < 1) 
			return false;

		$query = sprintf('SELECT COUNT(*)' .
			' FROM #__ariquizstatisticsinfo' .
			' WHERE UserId = %d AND QuizId = %d AND `Status` = %s',
			$userId,
			$quizId,
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PAUSE));
		$db->setQuery($query);
		$result = $db->loadResult();
		if (is_null($result))
		{
			return false;
		}
		
		return ($result > 0);
	}

	function getPausedQuizTicketId($quizId, $userId)
	{
		$quizId = @intval($quizId, 10);
		if ($quizId < 1) 
			return null;
		
		$userId = @intval($userId, 10);
		if ($userId < 1) 
			return null;
		
		$db =& $this->getDBO();
		$query = sprintf('SELECT TicketId FROM #__ariquizstatisticsinfo WHERE QuizId = %d AND UserId = %d AND `Status` = %s LIMIT 0,1',
			$quizId,
			$userId,
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PAUSE));
		$db->setQuery($query);
		$ticketId = $db->loadResult();
		if (is_null($ticketId))
		{
			return false;
		}
		
		return $ticketId;
	}

	function resumeQuizByTicketId($ticketId, $userId)
	{
		$userId = @intval($userId, 10);
		if (empty($ticketId) || $userId < 1) 
			return false;
		
		$page = $this->getCurrentPageByTicketId($ticketId, $userId);
		if (empty($page))
			return false;

		$now = AriDateUtility::getDbUtcDate();
		$db =& $this->getDBO();
		$query = sprintf('UPDATE #__ariquizstatisticsinfo QSI INNER JOIN #__ariquizstatistics_pages QP' .
			' ON QSI.StatisticsInfoId = QP.StatisticsInfoId' .
			' SET QSI.Status = %4$s, QSI.UsedTime = QSI.UsedTime + (UNIX_TIMESTAMP(QSI.ModifiedDate) - UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate, QSI.StartDate))), QP.UsedTime = QP.UsedTime + (UNIX_TIMESTAMP(QSI.ModifiedDate) - UNIX_TIMESTAMP(QP.StartDate)), QP.StartDate = %1$s, QSI.ResumeDate = %1$s' .
			' WHERE QSI.TicketId = %2$s AND QSI.Status = %3$s AND QP.PageId = %5$d',
			$db->Quote($now),
			$db->Quote($ticketId),
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PAUSE),
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PROCESS),
			$page->PageId
		);
		$db->setQuery($query);
		return $db->execute();
	}

	function getNotFinishedQuizInfo($quizId, $userId)
	{
		$data = null;
		
		$quizId = @intval($quizId, 10);
		if ($quizId < 1) 
			return $data;

		$userId = @intval($userId, 10);
		if ($userId < 1) 
			return $data;

		$db =& $this->getDBO();
		$query = sprintf('SELECT TicketId,`Status`' .
			' FROM #__ariquizstatisticsinfo' .
			' WHERE (Status IN (%3$s)) AND UserId = %1$d AND QuizId = %2$d AND QuestionCount > 0 ORDER BY StatisticsInfoId DESC LIMIT 0,1',
			$userId,
			$quizId,
			join(',', AriDBUtils::quote(array(ARIQUIZ_USERQUIZ_STATUS_PREPARE, ARIQUIZ_USERQUIZ_STATUS_PROCESS, ARIQUIZ_USERQUIZ_STATUS_PAUSE)))
		);
		$db->setQuery($query);
		$res = $db->loadAssoc();
		if (is_array($res) && count($res) > 0)
		{
			$data = array(
				'TicketId' => $res['TicketId'],
				'Status' => $res['Status']
			);
		}

		return $data;
	}
	
	function getQuizInfo($sid)
	{
		$db =& $this->getDBO();
		
		$query = sprintf('SELECT 
					QSI.QuizId,
					QSI.TotalTime,
					QSI.UsedTime, 
					IFNULL(QSI.ResumeDate, QSI.StartDate) AS RealStartDate, 
					UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate, QSI.StartDate)) AS StartDate, 
					UNIX_TIMESTAMP(UTC_TIMESTAMP()) AS Now, 
					Q.QuizName,
					Q.Description,
					Q.Metadata,
					Q.ExtraParams,
					QSI.QuestionCount,
					QSI.PageCount' .
				' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquiz Q' .
				'	ON QSI.QuizId = Q.QuizId' .
				' WHERE QSI.StatisticsInfoId = %d AND QSI.Status = %s LIMIT 0,1',
				$sid,
				$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PROCESS));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$result = $result && count($result) > 0 ? $result[0] : null;
		if (!is_null($result))
		{
			$result->ExtraParams = !empty($result->ExtraParams) 
				? json_decode($result->ExtraParams)
				: null;
	
			$result->Metadata = !empty($result->Metadata) 
				? json_decode($result->Metadata)
				: null;
		}
		
		return $result;
	}
	
	function getUserCompletedPages($sid)
	{
		$db =& $this->getDBO();

		$query = sprintf('SELECT COUNT(*) FROM #__ariquizstatistics_pages SP' . 
			' WHERE SP.StatisticsInfoId = %d AND' . 
			' (SP.EndDate IS NOT NULL OR' . 
			' (SP.StartDate IS NOT NULL AND (SP.PageTime IS NOT NULL AND SP.PageTime > 0) AND (IF(UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(SP.StartDate), UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(SP.StartDate), 0) + SP.UsedTime) >= SP.PageTime))', 
			$sid);
		$db->setQuery($query);
		$result = $db->loadResult();
		if (is_null($result))
		{
			return 0;
		}

		return $result;
	}
	
	function getStatisticsInfoIdByTicketId($ticketId, $userId = 0, $status = null, $quizId = null)
	{
		$sid = 0;
		if (empty($ticketId) || (!is_null($quizId) && $quizId < 1))
			return $sid;

		$userId = intval($userId, 10);
		if (!is_null($status) && !is_array($status)) 
			$status = array($status);

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select('StatisticsInfoId');
		$query->from('#__ariquizstatisticsinfo');
		$query->where('TicketId = ' . $db->Quote($ticketId));
		if ($userId > 0)
			$query->where('UserId = ' . $userId);
			
		if (!is_null($status))
			$query->where('Status IN (' . join(',', AriDBUtils::quote($status)) . ')');
			
		if (!is_null($quizId))
			$query->where('QuizId = ' . intval($quizId, 10));
		
		$db->setQuery((string)$query);
		$sid = $db->loadResult();
		if (is_null($sid))
		{
			$sid = 0;
		}

		return $sid;		
	}

	function getStatisticsInfoByTicketId($ticketId, $userId = 0, $status = null, $quizId = null)
	{
		$userId = intval($userId, 10);
		if ($status != null && !is_array($status)) 
			$status = array($status);

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select('*');
		$query->from('#__ariquizstatisticsinfo');
		$query->where('TicketId = ' . $db->Quote($ticketId));
		
		if ($userId > 0)
			$query->where('UserId = ' . $userId);
			
		if (!is_null($status))
			$query->where('Status IN (' . join(',', AriDBUtils::quote($status)) . ')');
			
		if (!is_null($quizId))
			$query->where('QuizId = ' . intval($quizId, 10));
		
		$db->setQuery((string)$query);
		$result = $db->loadAssocList();

		if (!is_array($result) || count($result) < 1) 
			return null;
	
		$statistics = $this->getTable('Userquiz');
		if (!$statistics->bind($result[0]))
			return null;

		return $statistics;
	}

	function canTakeQuiz($quiz, $user, $checkPaused = true)
	{
		$result = $this->canTakeQuiz2($quiz, $user, $checkPaused);

		return ($result == ARIQUIZ_TAKEQUIZERROR_NONE);
	}
	
	function canTakeQuiz2($quiz, $user, $checkPaused = true)
	{
		$db =& $this->getDBO();
		$dbNullDate = $db->getNullDate();

		$quizModel = & AriModel::getInstance('Quiz', $this->getFullPrefix());
		if (!is_object($quiz)) 
			$quiz = $quizModel->getQuiz($quiz);
		$quizId = $quiz->QuizId;

		if (empty($quiz) || empty($quiz->QuizId) && $quiz->Status != ARIQUIZ_QUIZ_STATUS_ACTIVE)
			return ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR;
			
		$userId = $user->get('id');
		// check quiz count and lag time
		if (!empty($userId) && (!empty($quiz->LagTime) || !empty($quiz->AttemptCount)))
		{
			$minEndDate = null;
			if (!empty($quiz->AttemptPeriod))
			{
				$attemptPeriod = json_decode($quiz->AttemptPeriod);
				$count = 0;
				if (isset($attemptPeriod->count))
					$count = intval($attemptPeriod->count, 10);
					
				if ($count > 0)
				{
					$ts = time();
					$ts -= date('H') * 60 * 60 + intval(date('i'), 10) * 60 + max(intval(date('s')) - 1, 0);
					$subDays = 0;
					switch ($attemptPeriod->type)
					{
						case 'week':
							$subDays = date('w');
							break;
						
						case 'month':
							$subDays = date('j') - 1;
							break;
							
						case 'year':
							$subDays = date('z');
							break;
					}

					if ($subDays > 0)
						$ts = strtotime('-' . $subDays . ' day', $ts);

					$minEndDate = AriDateUtility::toDbUtcDate(strtotime('-' . ($count - 1) . ' ' . $attemptPeriod->type, $ts));
				}
			}
			
			$query = sprintf('SELECT IFNULL(COUNT(QuizId), 0) AS QuizCount, IFNULL((UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(MAX(EndDate))), 0) AS LagTime' .
				' FROM #__ariquizstatisticsinfo' .
				' WHERE Status = "Finished" AND UserId = %d AND QuizId = %d' . ($minEndDate ? ' AND EndDate >= ' . $db->Quote($minEndDate) : '') .
				' GROUP BY QuizId' .
				' LIMIT 0,1',
				$userId,
				$quizId);
			$db->setQuery($query);
			$result = $db->loadAssocList();
			if (is_null($result))
			{
				return ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR;
			}
					
			$result = count($result) > 0
				? $result[0]
				: array('QuizCount' => 0, 'LagTime' => 0);

			if ($quiz->AttemptCount > 0 && $result['QuizCount'] >= $quiz->AttemptCount)
			{
				return ARIQUIZ_TAKEQUIZERROR_ATTEMPTCOUNT;
			}
			else if ($quiz->LagTime > 0 && $result['QuizCount'] > 0 && $result['LagTime'] < $quiz->LagTime)
			{
				return ARIQUIZ_TAKEQUIZERROR_LAGTIME;
			}
		}

		$quizStartDate = ($quiz->StartDate && $quiz->StartDate != $dbNullDate) ? $quiz->StartDate : null;
		$quizEndDate = ($quiz->EndDate && $quiz->EndDate != $dbNullDate) ? $quiz->EndDate : null;
		if ($quizStartDate || $quizEndDate)
		{
			$currentTs = strtotime(gmdate("M d Y H:i:s", time()) . ' UTC');
			if ($quizStartDate)
			{
				$startDate = strtotime($quizStartDate . ' UTC');
				if ($startDate > $currentTs)
					return ARIQUIZ_TAKEQUIZERROR_DATEACCESS;
			}

			if ($quizEndDate)
			{
				$endDate = strtotime($quizEndDate . ' UTC');

				if ($endDate < $currentTs)
					return ARIQUIZ_TAKEQUIZERROR_DATEACCESS;
			}
		}

		$accessItem = $quiz->getAccess();
		if ($accessItem < 0)
			return ARIQUIZ_TAKEQUIZERROR_NOTHAVEPERMISSIONS;

		$user = JFactory::getUser();
		$viewLevels = $user->getAuthorisedViewLevels();
		$errorCode = ARIQUIZ_TAKEQUIZERROR_NOTHAVEPERMISSIONS;

		if (in_array($accessItem, $viewLevels))
			$errorCode = ARIQUIZ_TAKEQUIZERROR_NONE;

		if ($errorCode != ARIQUIZ_TAKEQUIZERROR_NONE) 
			return $errorCode;

        if ($userId > 0 && $quiz->PrevQuizId > 0 && $quiz->PrevQuizId != $quizId)
        {
            $query = sprintf('SELECT COUNT(*)' .
                ' FROM #__ariquizstatisticsinfo' .
                ' WHERE Status = "Finished" AND UserId = %1$d AND QuizId = %2$d AND Passed = 1',
                $userId,
                $quiz->PrevQuizId
            );
            $db->setQuery($query);
            $result = $db->loadResult();
            if (is_null($result))
            {
                return ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR;
            }

            if (empty($result))
                return ARIQUIZ_TAKEQUIZERROR_PREVQUIZ;
        }
				
		if ($checkPaused && !empty($userId))
		{
			if ($this->hasPausedQuiz($quiz->QuizId, $userId))
			{
				return ARIQUIZ_TAKEQUIZERROR_HASPAUSEDQUIZ;
			}
		}

		return ARIQUIZ_TAKEQUIZERROR_NONE;
	}
	
	/* */
	function canTakeQuizByGuest($quiz, $email)
	{
		$result = $this->canTakeQuizByGuest2($quiz, $email);

		return ($result == ARIQUIZ_TAKEQUIZERROR_NONE);
	}
	
	function canTakeQuizByGuest2($quiz, $email)
	{
		$db =& $this->getDBO();
		$dbNullDate = $db->getNullDate();

		$quizModel = & AriModel::getInstance('Quiz', $this->getFullPrefix());
		if (!is_object($quiz)) 
			$quiz = $quizModel->getQuiz($quiz);
		$quizId = $quiz->QuizId;

		if (empty($quiz) || empty($quiz->QuizId) && $quiz->Status != ARIQUIZ_QUIZ_STATUS_ACTIVE)
			return ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR;

		// check quiz count and lag time
		if (!empty($email) && (!empty($quiz->LagTime) || !empty($quiz->AttemptCount)))
		{
			$minEndDate = null;
			if (!empty($quiz->AttemptPeriod))
			{
				$attemptPeriod = json_decode($quiz->AttemptPeriod);
				$count = 0;
				if (isset($attemptPeriod->count))
					$count = intval($attemptPeriod->count, 10);

				if ($count > 0)
				{
					$ts = time();
					$ts -= date('H') * 60 * 60 + intval(date('i'), 10) * 60 + max(intval(date('s')) - 1, 0);
					$subDays = 0;
					switch ($attemptPeriod->type)
					{
						case 'week':
							$subDays = date('w');
							break;
						
						case 'month':
							$subDays = date('j') - 1;
							break;
							
						case 'year':
							$subDays = date('z');
							break;
					}

					if ($subDays > 0)
						$ts = strtotime('-' . $subDays . ' day', $ts);

					$minEndDate = AriDateUtility::toDbUtcDate(strtotime('-' . ($count - 1) . ' ' . $attemptPeriod->type, $ts));
				}
			}

			$query = sprintf('SELECT IFNULL(COUNT(S.QuizId), 0) AS QuizCount, IFNULL((UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(MAX(S.EndDate))), 0) AS LagTime' .
				' FROM #__ariquizstatisticsinfo S INNER JOIN #__ariquiz_statistics_extradata SED 
					ON SED.StatisticsInfoId = S.StatisticsInfoId' .
				' WHERE S.Status = "Finished" AND S.QuizId = %2$d' . ($minEndDate ? ' AND S.EndDate >= ' . $db->Quote($minEndDate) : '') . ' AND SED.Name = "Email" AND SED.Value = %1$s' .
				' GROUP BY S.QuizId' .
				' LIMIT 0,1',
				$db->Quote($email),
				$quizId);

			$db->setQuery($query);
			$result = $db->loadAssocList();
			if (is_null($result))
			{
				return ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR;
			}
					
			$result = count($result) > 0
				? $result[0]
				: array('QuizCount' => 0, 'LagTime' => 0);

			if ($quiz->AttemptCount > 0 && $result['QuizCount'] >= $quiz->AttemptCount)
			{
				return ARIQUIZ_TAKEQUIZERROR_ATTEMPTCOUNT;
			}
			else if ($quiz->LagTime > 0 && $result['QuizCount'] > 0 && $result['LagTime'] < $quiz->LagTime)
			{
				return ARIQUIZ_TAKEQUIZERROR_LAGTIME;
			}
		}

		$quizStartDate = ($quiz->StartDate && $quiz->StartDate != $dbNullDate) ? $quiz->StartDate : null;
		$quizEndDate = ($quiz->EndDate && $quiz->EndDate != $dbNullDate) ? $quiz->EndDate : null;
		if ($quizStartDate || $quizEndDate)
		{
			$currentTs = strtotime(gmdate("M d Y H:i:s", time()) . ' UTC');
			if ($quizStartDate)
			{
				$startDate = strtotime($quizStartDate . ' UTC');
				if ($startDate > $currentTs)
					return ARIQUIZ_TAKEQUIZERROR_DATEACCESS;
			}

			if ($quizEndDate)
			{
				$endDate = strtotime($quizEndDate . ' UTC');

				if ($endDate < $currentTs)
					return ARIQUIZ_TAKEQUIZERROR_DATEACCESS;
			}
		}

		return ARIQUIZ_TAKEQUIZERROR_NONE;
	}
	
	function isQuizFinishedByTicketId($ticketId)
	{
		$db = $this->getDBO();

		$query = sprintf(
			'SELECT 
				COUNT(*) 
			FROM 
				#__ariquizstatisticsinfo QSI INNER JOIN #__ariquizstatistics_pages QSP 
					ON QSP.StatisticsInfoId = QSI.StatisticsInfoId 
			WHERE 
				QSI.TicketId = %s 
				AND
				(
					QSP.StartDate IS NULL 
					OR
					(
						QSP.EndDate IS NULL 
						AND 
						(
							QSP.PageTime = 0 
							OR
							( 
								IF(
									UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(QSP.StartDate), 
									UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(QSP.StartDate), 
									0
								) 
								+ 
								QSP.UsedTime
							) < QSP.PageTime
						)
					)
				)
				AND
				(
					QSI.TotalTime = 0 
					OR 
					QSI.StartDate IS NULL 
					OR 
					(
						IF(
							UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate, QSI.StartDate)), 
							UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate, QSI.StartDate)), 
							0
						) 
						+ 
						QSI.UsedTime
					) < QSI.TotalTime
				)
			ORDER BY NULL
			LIMIT 0,1', 
			$db->Quote($ticketId)
		);
		$db->setQuery($query);
		$count = $db->loadResult();
		
		return ($count === 0 || $count === '0');
	}
	
	function markQuizAsFinished($ticketId, $userId = 0)
	{
		$db =& $this->getDBO();

		$statisticsInfoId = $this->getStatisticsInfoIdByTicketId($ticketId, $userId, ARIQUIZ_USERQUIZ_STATUS_PROCESS);
		if (empty($statisticsInfoId))
		{
			return false;
		}
		
		$quizResultModel = & AriModel::getInstance('Quizresult', $this->getFullPrefix());
		$finishedInfo = $quizResultModel->getFinishedInfo($statisticsInfoId);
		if (empty($finishedInfo))
		{
			return false;
		}

		$finishedDate = $this->getFinishedQuizDate($statisticsInfoId);
		if (!$finishedDate)
		{
			return false;
		}

		$userScore = $finishedInfo['UserScore'] > 0 ? $finishedInfo['UserScore'] : 0;
		$query = sprintf('UPDATE #__ariquizstatisticsinfo SET Status = %1$s,EndDate = %2$s,MaxScore = %3$f,UserScore = %4$f,UserScorePercent = %7$f, Passed = %5$d, ElapsedTime = UNIX_TIMESTAMP(%2$s) - UNIX_TIMESTAMP(StartDate) + UsedTime WHERE StatisticsInfoId = %6$d',
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE),
			$db->Quote($finishedDate),
			$finishedInfo['MaxScore'],
			$userScore,
			$finishedInfo['Passed'],
			$statisticsInfoId,
			$finishedInfo['MaxScore'] > 0 ? min(round(100 * $userScore / $finishedInfo['MaxScore'], 2), 100.00) : 0.00
		);
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}
		
		$query = sprintf(
			'UPDATE 
				#__ariquizstatistics S INNER JOIN #__ariquizstatistics_pages SP 
					ON S.PageId = SP.PageId
				INNER JOIN #__ariquizstatisticsinfo SI
					ON S.StatisticsInfoId = SI.StatisticsInfoId 
			SET 
				S.ElapsedTime =
				IF(
					SP.PageTime > 0,
					LEAST(
						SP.PageTime,
						IF(
							SP.StartDate IS NOT NULL,
							UNIX_TIMESTAMP(SI.EndDate) - UNIX_TIMESTAMP(SP.StartDate) + SP.UsedTime,
							SP.UsedTime 
						)
					),
					IF(
						SP.StartDate IS NOT NULL,
						UNIX_TIMESTAMP(SI.EndDate) - UNIX_TIMESTAMP(SP.StartDate) + SP.UsedTime,
						SP.UsedTime 
					)
				),
				S.EndDate = IF(SP.StartDate IS NOT NULL, SI.EndDate, NULL)
			WHERE 
				S.StatisticsInfoId = %1$d 
				AND 
				S.Completed = 0 
				AND 
				(
					SP.EndDate IS NULL 
					AND  
					(
						SP.StartDate IS NOT NULL
						OR
						SP.SkipDate IS NOT NULL
					)
				)',
			$statisticsInfoId
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function getFinishedQuizDate($statisticsInfoId)
	{
		$db = $this->getDBO();
		$query = sprintf(
			'SELECT CAST(
				IF( 
					( 
						SELECT 
							IF
							(
								COUNT(EP.PageId) = COUNT(EP.StartDate), 
								1, 
								0
							) 
						FROM 
							#__ariquizstatistics_pages EP 
						WHERE 
							EP.StatisticsInfoId = %1$d
						GROUP BY 
							EP.StatisticsInfoId 
					), 
					IF(
						@TotalQuizEndDate IS NULL,
						@TotalQuestionEndDate,
						IF(
							@TotalQuestionEndDate IS NULL OR @TotalQuizEndDate < @TotalQuestionEndDate,
							@TotalQuizEndDate,
							@TotalQuestionEndDate
						)
					), 
					@TotalQuizEndDate
				) 
				AS DATETIME) AS EndDate 
			FROM (
				SELECT
					@TotalQuizEndDate := 
						IF( 
							QSI.TotalTime > 0, 
							FROM_UNIXTIME( 
								UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate,QSI.StartDate)) 
								+ 
								QSI.TotalTime 
								- 
								QSI.UsedTime 
							), 
							NULL
						),
					@TotalQuestionEndDate :=
						IF
						( 
							P.PageTime > 0 AND P.EndDate IS NULL, 
							FROM_UNIXTIME( 
								UNIX_TIMESTAMP(P.StartDate) + P.PageTime - P.UsedTime 
							),
							P.EndDate
						) 
				FROM
					#__ariquizstatisticsinfo QSI INNER JOIN #__ariquizstatistics_pages P 
						ON P.StatisticsInfoId = QSI.StatisticsInfoId 
				WHERE 
					P.StatisticsInfoId = %1$d
				ORDER BY 
					P.StartDate DESC 
				LIMIT 0,1
			) T',
			$statisticsInfoId
		);
		$db->setQuery($query);
		return $db->loadResult();
	}

	function setSafeQuizStartDate($sid, $startDate = null)
	{
		$db =& $this->getDBO();
		
		if (empty($startDate)) 
			$startDate = AriDateUtility::getDbUtcDate();
		
		$query = sprintf('UPDATE #__ariquizstatisticsinfo SET StartDate = %s WHERE StatisticsInfoId = %d AND StartDate IS NULL', 
			$db->Quote($startDate),
			$sid);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function stopQuiz($sid, $userId)
	{
		$userId = intval($userId, 10);
		$sid = intval($sid, 10);
		if ($userId < 1 || $sid < 1) 
			return false;

		$stopDate = AriDateUtility::getDbUtcDate();

		$db =& $this->getDBO();
		$query = sprintf('UPDATE #__ariquizstatisticsinfo SET Status = %s,ModifiedDate=%s WHERE StatisticsInfoId = %d AND `Status` = %s',
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PAUSE),
			$db->Quote($stopDate),
			$sid,
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PROCESS)
		);
		$db->setQuery($query);
		return $db->execute();
	}

	function terminateQuiz($sid, $userId)
	{
		$userId = intval($userId, 10);
		$sid = intval($sid, 10);
		if ($sid < 1) 
			return false;

		$db = $this->getDBO();
		$query = sprintf(
			'DELETE 
				SI,S,SF
			FROM 
				#__ariquizstatisticsinfo SI LEFT JOIN #__ariquizstatistics S 
					ON SI.StatisticsInfoId = S.StatisticsInfoId 
				LEFT JOIN #__ariquizstatistics_files SF 
					ON S.StatisticsId = SF.StatisticsId 
			WHERE 
				SI.StatisticsInfoId = %1$d AND SI.`Status` = %2$s AND SI.UserId = %3$d',
			$sid,
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_PROCESS),
			$userId
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function completePage($page, $attempts)
	{
		$queryList = array();
		$db = $this->getDBO();

		if ($page->EndDate)
			$queryList[] = sprintf(
				'UPDATE #__ariquizstatistics_pages SET EndDate = %1$s WHERE PageId = %2$d',
				$db->Quote($page->EndDate),
				$page->PageId
			);
		
		if (is_array($attempts) && count($attempts) > 0)
		{
			$date = AriDateUtility::getDbUtcDate();
			$quotedDate = $db->Quote($date);
			$attemptValues = array();
			foreach ($attempts as $id => $data)
			{
				$attemptValues[] = sprintf(
					'(%d,%s,%s)',
					$id,
					$db->Quote($data),
					$quotedDate
				);
			}

			$queryList[] = 'INSERT INTO #__ariquizstatistics_attempt (StatisticsId, Data, CreatedDate) VALUES' . join(',', $attemptValues);
		}
		
		foreach ($page->Questions as $question)
		{
			if ($attempts[$question->StatisticsId])
			{
				$queryList[] = sprintf(
					'UPDATE #__ariquizstatistics SET AttemptCount = %1$d WHERE StatisticsId = %2$d',
					$question->AttemptCount,
					$question->StatisticsId
				);

				continue ;
			}
			else if ($question->Completed)
				continue ;

			$elapsedTime = sprintf('UNIX_TIMESTAMP(%1$s) - UNIX_TIMESTAMP(%2$s) + %3$d',
				$db->Quote($question->EndDate),
				$db->Quote($page->StartDate),
				$page->UsedTime
			); 
			$queryList[] = sprintf(
				'UPDATE #__ariquizstatistics SET AttemptCount = %1$d, Score = %2$f, Data = %3$s, ElapsedTime = %5$s, EndDate = %6$s, Completed = 1 WHERE StatisticsId = %4$d',
				$question->AttemptCount,
				$question->Score,
				$db->Quote($question->Data),
				$question->StatisticsId,
				!empty($question->EndDate)
					? 
						($page->PageTime > 0
							? sprintf('LEAST(%1$d,%2$s)', $page->PageTime, $elapsedTime)
							: $elapsedTime
						) 
					: 'NULL',
				!empty($question->EndDate) ? $db->Quote($question->EndDate) : 'NULL'
			);
		}

		if (is_array($queryList))
		{
			foreach ($queryList  as $query)
			{
				$db->setQuery($query);
				if (!$db->execute())
				{
					return false;
				}
			}
		}

		return true;
			
	}

	function updateNewQuestionPage($page)
	{
		if ($page && !$page->store())
			return false;

		$db = $this->getDBO();
		$idList = array();
		$query = 'UPDATE #__ariquizstatistics SET `InitData` = CASE';
		foreach ($page->Questions as $question)
		{
			$query .= sprintf(' WHEN StatisticsId = %d THEN %s',
				$question->StatisticsId,
				!empty($question->InitData) ? $db->Quote($question->InitData) : 'NULL'
			);
			$idList[] = $question->StatisticsId;
		}
		$query .= 'ELSE `InitData` END WHERE StatisticsId IN (' . join(',', $idList) . ')';
		$db->setQuery($query);
		return $db->execute();
	}

	function getNextPage($sid, $userId = null)
	{
		$page = $this->getTable('Userquizpage');
		if (!$page->loadNextPage($sid, $userId))
			$page = null;

		return $page;
	}

	function getCurrentPage($sid, $userId = 0)
	{
		$page = $this->getTable('Userquizpage');
		if (!$page->loadCurrentPage($sid, $userId))
			$page = null;

		return $page;
	}
	
	function getCurrentPageByTicketId($ticketId, $userId = 0)
	{
		$page = $this->getTable('Userquizpage');
		if (!$page->loadCurrentPageByTicketId($ticketId, $userId))
			$page = null;

		return $page;
	}
	
	function getPage($pageId, $sid = 0)
	{
		$page = $this->getTable('Userquizpage');
		if (!$page->loadWithQuestions($pageId))
			$page = null;
			
		if (!is_null($page) && $sid > 0 && $page->StatisticsInfoId != $sid)
			$page = null;
			
		return $page;
	}
	
	function nextPage($page, $skipDate = null)
	{
		$db = $this->getDBO();
		
		if (empty($skipDate)) 
			$skipDate = AriDateUtility::getDbUtcDate();
		$skipDate = $db->Quote($skipDate);

		$query = sprintf('UPDATE #__ariquizstatistics_pages' .
			' SET UsedTime = UsedTime + (UNIX_TIMESTAMP(%1$s) - UNIX_TIMESTAMP(StartDate)),' .
			' SkipCount = SkipCount + 1,SkipDate = %1$s,StartDate = NULL' .
			' WHERE PageId = %2$d', 
			$skipDate,
			$page->PageId);
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}

		$idList = array();
		$query = 'UPDATE #__ariquizstatistics SET `Data` = CASE';
		foreach ($page->Questions as $question)
		{
			if ($question->Completed)
				continue ;

			$query .= sprintf(' WHEN StatisticsId = %d THEN %s',
				$question->StatisticsId,
				!empty($question->Data) ? $db->Quote($question->Data) : 'NULL'
			);
			$idList[] = $question->StatisticsId;
		}
		$query .= 'ELSE `Data` END WHERE StatisticsId IN (' . join(',', $idList) . ')';
		
		if (count($idList) > 0)
		{
			$db->setQuery($query);
			if (!$db->execute())
			{
				return false;
			}
		}
		
		if (!$this->updatePageIndexes($page->StatisticsInfoId, $page->PageNumber + 1))
			return false;

		return true;
	}
	
	function prevPage($page, $skipDate = null)
	{
		if ($page->PageNumber == 0)
			return false;
		
		$db = $this->getDBO();
		
		if (empty($skipDate)) 
			$skipDate = AriDateUtility::getDbUtcDate();
		$skipDate = $db->Quote($skipDate);

		$query = sprintf('UPDATE #__ariquizstatistics_pages' .
			' SET UsedTime = UsedTime + (UNIX_TIMESTAMP(%1$s) - UNIX_TIMESTAMP(StartDate)),' .
			' SkipCount = SkipCount + 1,SkipDate = %1$s,StartDate = NULL' .
			' WHERE PageId = %2$d', 
			$skipDate,
			$page->PageId);
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}

		$idList = array();
		$query = 'UPDATE #__ariquizstatistics SET `Data` = CASE';
		foreach ($page->Questions as $question)
		{
			if ($question->Completed)
				continue ;

			$query .= sprintf(' WHEN StatisticsId = %d THEN %s',
				$question->StatisticsId,
				!empty($question->Data) ? $db->Quote($question->Data) : 'NULL'
			);
			$idList[] = $question->StatisticsId;
		}
		$query .= 'ELSE `Data` END WHERE StatisticsId IN (' . join(',', $idList) . ')';
		
		if (count($idList) > 0)
		{
			$db->setQuery($query);
			if (!$db->execute())
			{
				return false;
			}
		}
		
		if (!$this->updatePageIndexes($page->StatisticsInfoId, $page->PageNumber - 1))
			return false;

		return true;
	}
	
	function updatePageQuestions($page)
	{
		$db = $this->getDBO();
		
		$idList = array();
		$query = 'UPDATE #__ariquizstatistics SET `Data` = CASE';
		foreach ($page->Questions as $question)
		{
			if ($question->Completed)
				continue ;

			$query .= sprintf(' WHEN StatisticsId = %d THEN %s',
				$question->StatisticsId,
				!empty($question->Data) ? $db->Quote($question->Data) : 'NULL'
			);
			$idList[] = $question->StatisticsId;
		}
		$query .= 'ELSE `Data` END WHERE StatisticsId IN (' . join(',', $idList) . ')';

		if (count($idList) > 0)
		{			
			$db->setQuery($query);
			if (!$db->execute())
			{
				return false;
			}
		}
		
		return true;
	}
	
	function updatePageIndexes($sid, $currentPageNumber)
	{
		$db = $this->getDBO();
		
		if ($currentPageNumber < 0)
			$currentPageNumber = 0;

		$query = sprintf('UPDATE #__ariquizstatistics_pages P INNER JOIN #__ariquizstatisticsinfo SI ON P.StatisticsInfoId = SI.StatisticsInfoId' .
			' SET P.PageIndex = CASE WHEN P.PageNumber >= %2$d THEN P.PageNumber - %2$d ELSE GREATEST(0, SI.PageCount - %2$d) + P.PageNumber END' .
			' WHERE P.StatisticsInfoId = %1$d', 
			$sid,
			$currentPageNumber
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function getFiles($sid)
	{
		$db =& $this->getDBO();

		$query = sprintf(
			'SELECT 
				SF.StatisticsId,
				SF.QuestionId,
				FV.FileName,
				SF.Alias,
				F.Group,
				F.FolderId,
				F.MimeType
			FROM
				#__ariquizstatistics_files SF INNER JOIN #__ariquiz_file_versions FV
					ON SF.FileVersionId = FV.FileVersionId
				INNER JOIN #__ariquiz_file F
					ON FV.FileId = F.FileId 
			WHERE 
				SF.StatisticsInfoId = %d',
			$sid);
		$db->setQuery($query);
		$sessionFiles = $db->loadAssocList();
		if (is_null($sessionFiles))
		{
			return null;
		}
		
		$files = array();
		if (!is_array($sessionFiles))
			return $files;

		foreach ($sessionFiles as $file)
		{
			$questionId = $file['QuestionId'];
			if (!isset($files[$questionId]))
				$files[$questionId] = array();

			$files[$questionId][$file['Alias']] = array(
				'FileName' => $file['FileName'],
				'Group' => $file['Group'],
				'Folder' => $file['FolderId'],
				'MimeType' => $file['MimeType']
			);
		}
		
		return $files;
	}
	
	function getFile($ticketId, $questionId, $alias)
	{
		$db =& $this->getDBO();

		$query = sprintf(
			'SELECT 
				SF.StatisticsId,
				SF.QuestionId,
				FV.FileName,
				SF.Alias,
				F.Group,
				F.FolderId AS Folder,
				F.MimeType
			FROM
				#__ariquizstatisticsinfo SI LEFT JOIN #__ariquizstatistics_files SF
					ON SI.StatisticsInfoId = SF.StatisticsInfoId
				INNER JOIN #__ariquiz_file_versions FV
					ON SF.FileVersionId = FV.FileVersionId
				INNER JOIN #__ariquiz_file F
					ON FV.FileId = F.FileId 
			WHERE 
				SI.TicketId = %s AND SF.QuestionId = %d AND SF.Alias = %s
			LIMIT 0,1',
			$db->Quote($ticketId),
			intval($questionId, 10),
			$db->Quote($alias)
		);
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	function getQuizQuestions($sid)
	{
		$db =& $this->getDBO();
		
		$query = sprintf('SELECT IF(QV2.QuestionId,QV2.Question,QV.Question) AS Question,IF(QV2.QuestionId,QV2.Note,QV.Note) AS QuestionNote' .
			' FROM #__ariquizstatisticsinfo SI INNER JOIN #__ariquizstatistics S' .
			' 	ON SI.StatisticsInfoId = S.StatisticsInfoId' . 
			' LEFT JOIN #__ariquizquestionversion QV' .
			'	ON S.QuestionVersionId = QV.QuestionVersionId' .
			' LEFT JOIN #__ariquizquestionversion QV2' . 
			' 	ON S.BankVersionId = QV2.QuestionVersionId' .
			' WHERE SI.StatisticsInfoId = %d',
			$sid);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getQuizzesStatus($userId, $quizIdList)
	{
		if (!is_array($quizIdList) || count($quizIdList) == 0)
			return array();

		$db = $this->getDBO();
		
		$query = sprintf(
			'SELECT
				Q.QuizId,
				COUNT(SI.QuizId) AS CompletedCount,
				MAX(SI.Passed) AS Passed
			FROM
				#__ariquiz Q LEFT JOIN #__ariquizstatisticsinfo SI
					ON Q.QuizId = SI.QuizId AND SI.UserId = %1$d
			WHERE
				Q.QuizId IN (%2$s)
			GROUP BY 
				Q.QuizId
			',
			$userId,
			join(',', $quizIdList)
		);
		$db->setQuery($query);
		$statusList = $db->loadObjectList('QuizId');
		
		return $statusList;
	}

    function goToPage($newPageNum, $page, $skipDate = null)
    {
        $db = $this->getDBO();

        if (empty($skipDate))
            $skipDate = AriDateUtility::getDbUtcDate();
        $skipDate = $db->Quote($skipDate);

        $query = sprintf('UPDATE #__ariquizstatistics_pages' .
            ' SET UsedTime = UsedTime + (UNIX_TIMESTAMP(%1$s) - UNIX_TIMESTAMP(StartDate)),' .
            ' SkipCount = SkipCount + 1,SkipDate = %1$s,StartDate = NULL' .
            ' WHERE PageId = %2$d',
            $skipDate,
            $page->PageId);
        $db->setQuery($query);
        if (!$db->execute())
        {
            return false;
        }

        $idList = array();
        $query = 'UPDATE #__ariquizstatistics SET `Data` = CASE';
        foreach ($page->Questions as $question)
        {
            if ($question->Completed)
                continue ;

            $query .= sprintf(' WHEN StatisticsId = %d THEN %s',
                $question->StatisticsId,
                !empty($question->Data) ? $db->Quote($question->Data) : 'NULL'
            );
            $idList[] = $question->StatisticsId;
        }
        $query .= 'ELSE `Data` END WHERE StatisticsId IN (' . join(',', $idList) . ')';

        if (count($idList) > 0)
        {
            $db->setQuery($query);
            if (!$db->execute())
            {
                return false;
            }
        }

        if (!$this->updatePageIndexes($page->StatisticsInfoId, $newPageNum))
            return false;

        return true;
    }

    function getPagesStatus($sid)
    {
        $db = $this->getDBO();

        $db->setQuery(
            sprintf(
                'SELECT PageId,PageNumber,IF(
                  (EndDate IS NULL) AND (PageTime = 0
				OR
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(StartDate),
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(StartDate),
						0
					)
					+
					UsedTime
				) < PageTime)
                 , 0, 1) AS Completed FROM #__ariquizstatistics_pages WHERE StatisticsInfoId = %1$d',
                $sid
            )
        );
        return $db->loadObjectList('PageNumber');
    }
}