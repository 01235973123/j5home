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
AriKernel::import('Utils.DateDurationUtility');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Application.ARIQuiz.Questions.QuestionFactory');
AriKernel::import('SimpleTemplate.SimpleTemplate');

class AriQuizModelQuizresult extends AriModel 
{
	var $_timePeriods;
	
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('Userquiz');
	}
	
	function setTimePeriods($periods)
	{
		$this->_timePeriods = $periods;
	}
	
	function getTimePeriods()
	{
		return $this->_timePeriods;
	}

	function getFinishedInfo($statisticsInfoId)
	{
		$db =& $this->getDBO();
		
		$statisticsInfoId = intval($statisticsInfoId);
		$query = sprintf(
		'SELECT ' . 
			'IFNULL(SUM(IF(QQV.Score, QQV.Score, QQV2.Score)), 0) AS MaxScore,' . 
			'SUM(QS.Score) AS UserScore,' .
			'(100 * (SUM(QS.Score) / IFNULL(SUM(IF(QQV.Score, QQV.Score, QQV2.Score)), 0)) >= QSI.PassedScore) AS Passed' .
		' FROM #__ariquizstatisticsinfo QSI INNER JOIN #__ariquizstatistics QS' .
		'	ON QSI.StatisticsInfoId = QS.StatisticsInfoId' .
		' LEFT JOIN #__ariquizquestionversion QQV' .
         	'	ON QS.QuestionVersionId = QQV.QuestionVersionId' .
    	' LEFT JOIN #__ariquizquestionversion QQV2' .
         	'	ON QS.BankVersionId = QQV2.QuestionVersionId' .
		' WHERE QSI.StatisticsInfoId = %d' .
		' GROUP BY QSI.StatisticsInfoId' .
		' LIMIT 0,1',
		$statisticsInfoId);
		$db->setQuery($query);
		$obj = $db->loadAssocList();	
		$obj = (!empty($obj) && count($obj) > 0) ? $obj[0] : null;
		
		return $obj;
	}
	
	function getFormattedFinishedResult($ticketId, $userId, $defaults = array(), $periods = null)
	{
		$result = $this->getFinishedResult($ticketId, $userId, $defaults);
		if (empty($result))
			return null;

		if (empty($result['UserName'])) 
			$result['UserName'] = AriUtils::getParam($defaults, 'UserName', '');
		$result['Passed'] = $result['Passed'] ? $defaults['PassedText'] : $defaults['NotPassedText'];
		$result['StartDate'] = AriDateUtility::formatDate($result['StartDate']); 
		$result['EndDate'] = AriDateUtility::formatDate($result['EndDate']);
		$result['SpentTime'] = AriDateDurationUtility::toString($result['SpentTime'], $periods, ' ', true);
		$result['ResultsLink'] = JURI::root(false) . '/index.php?option=com_ariquiz&view=quizcomplete&ticketId=' . $ticketId;
		$result['AdminResultsLink'] = JURI::root(false) . '/administrator/index.php?option=com_ariquiz&view=quizresult&statisticsInfoId=' . $result['StatisticsInfoId'];

		return $result;
		
	}

	function getQuestionCount($sid, $filter)
	{
		$type = $this->_getResultsType($filter);
		if ($type == 'None')
			return 0;
		
		$db =& $this->getDBO();

		$query = '';
		if ($type == 'All')
		{
			$query = sprintf('SELECT QuestionCount FROM #__ariquizstatisticsinfo WHERE StatisticsInfoId = %d',
				$sid);
		}
		else if ($type == 'OnlyCorrect')
		{
			$query = sprintf('SELECT COUNT(*) AS QuestionCount' .
				' FROM #__ariquizstatistics S LEFT JOIN #__ariquizquestionversion QV' .
				'	 ON S.QuestionVersionId = QV.QuestionVersionId' .
			 	' LEFT JOIN #__ariquizquestionversion BQV' .
				'	 ON S.BankVersionId = BQV.QuestionVersionId' .
				' WHERE S.StatisticsInfoId = %d AND (IF(QV.Score,QV.Score,BQV.Score) = 0 OR S.Score = IF(QV.Score,QV.Score,BQV.Score))',
				$sid
			);
			
		}
		else if ($type == 'OnlyIncorrect')
		{
			$query = sprintf('SELECT COUNT(*) AS QuestionCount' .
				' FROM #__ariquizstatistics S LEFT JOIN #__ariquizquestionversion QV' .
				'	 ON S.QuestionVersionId = QV.QuestionVersionId' .
			 	' LEFT JOIN #__ariquizquestionversion BQV' .
				'	 ON S.BankVersionId = BQV.QuestionVersionId' .
				' WHERE S.StatisticsInfoId = %d AND (IF(QV.Score,QV.Score,BQV.Score) > 0 AND S.Score < IF(QV.Score,QV.Score,BQV.Score))',
				$sid
			);
		}
		
		if (empty($query))
			return 0;
			
		$db->setQuery($query);
		$cnt = $db->loadResult();

		return !empty($cnt) ? $cnt : 0;
	}
	
	function getQuestionList($sid, $filter = null, $getFiles = false, $summaryTemplate = '')
	{
		$type = $this->_getResultsType($filter);
		if ($type == 'None')
			return null;
		
		$sid = intval($sid, 10);
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select(array(
			'IF(S.BankVersionId = 0, S.QuestionVersionId, S.BankVersionId) AS BaseQuestionVersionId',
			'S.Data AS UserData',
			'S.InitData',
			'S.QuestionIndex',
			'S.QuestionId',
			'S.StatisticsId',
			'S.Score AS UserScore',
			'S.AttemptCount',
			'IF(QV2.QuestionId,QV2.AttemptCount,QV.AttemptCount) AS MaxAttemptCount',
			'IF(QV2.QuestionId,QV2.OnlyCorrectAnswer,QV.OnlyCorrectAnswer) AS OnlyCorrectAnswer',
			'IFNULL(IF(QV.Score, QV.Score, QV2.Score), 0.00) AS MaxScore',
			'IF(QV2.QuestionId,QV2.Question,QV.Question) AS Question',
			'IF(QV2.QuestionId,QV2.Note,QV.Note) AS QuestionNote',
			'IF(QV2.QuestionId,QV2.Data,QV.Data) AS QuestionData',
			'IF(QV2.QuestionId, QV2.QuestionTypeId',
			'QV.QuestionTypeId) AS QuestionTypeId',
			'QT.ClassName AS QuestionClassName',
			'QT.QuestionType',
			'QC.CategoryName',
			'S.ElapsedTime AS TotalTime', 
			'SP.PageTime AS QuestionTime', 
			'SI.TotalTime AS QuizTotalTime',
			'SI.TicketId' 
		));
		$query->from('#__ariquizstatisticsinfo SI');
		$query->innerJoin('#__ariquizstatistics S ON SI.StatisticsInfoId = S.StatisticsInfoId');
		$query->innerJoin('#__ariquizstatistics_pages SP ON SP.PageId = S.PageId');
		$query->leftJoin('#__ariquizquestionversion QV ON S.QuestionVersionId = QV.QuestionVersionId');
		$query->leftJoin('#__ariquizquestionversion QV2 ON S.BankVersionId = QV2.QuestionVersionId');
		$query->innerJoin('#__ariquizquestiontype QT ON IFNULL(QV2.QuestionTypeId, QV.QuestionTypeId) = QT.QuestionTypeId');
		$query->leftJoin('#__ariquizquestioncategory QC ON S.QuestionCategoryId = QC.QuestionCategoryId');
		$query->where('SI.StatisticsInfoId = ' . $sid);
		
		if ($type == 'OnlyCorrect')
		{
			$query->where('(IF(QV.Score,QV.Score,QV2.Score) = 0 OR S.Score = IF(QV.Score,QV.Score,QV2.Score))');
		}
		else if ($type == 'OnlyIncorrect')
		{
			$query->where('(IF(QV.Score,QV.Score,QV2.Score) > 0 AND S.Score < IF(QV.Score,QV.Score,QV2.Score))');
		}

		if ($filter)
			$query = $filter->applyToQuery($query);

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$questionList = $db->loadObjectList();
		$cnt = $questionList ? count($questionList) : 0;
		if ($cnt == 0)
			return null;

		$qVersionIdList = array();
		$timePeriods = $this->getTimePeriods();
		for ($i = 0; $i < $cnt; $i++)
		{
			$question =& $questionList[$i];
			$specificQuestion = AriQuizQuestionFactory::getQuestion($question->QuestionClassName); 
			$question->ClientData = $specificQuestion->getClientDataFromXml($question->QuestionData, $question->UserData, false, $question->InitData ? @unserialize($question->InitData) : null);//getClientDataFromXml($question->QuestionData, $question->UserData);
			$question->IsCorrect = $specificQuestion->isCorrect($question->UserData, $question->QuestionData);
			$question->UserData = $specificQuestion->getDataFromXml($question->UserData);
			$question->QuestionData = $specificQuestion->getDataFromXml($question->QuestionData, false, null, $question->InitData ? @unserialize($question->InitData) : null);//$specificQuestion->getDataFromXml($question->QuestionData, false);
			$question->HasCorrectAnswer = $specificQuestion->hasCorrectAnswer();

			$maxAttemptCount = $question->OnlyCorrectAnswer ? ($question->MaxAttemptCount ? $question->MaxAttemptCount : null) : 1;
			
			$question->Summary = $summaryTemplate
				? AriSimpleTemplate::parse(
					$summaryTemplate,
					array(
						'UserScore' => $question->UserScore,
						'MaxScore' => $question->MaxScore,
						'ElapsedTime' => AriDateDurationUtility::toString($question->TotalTime, $timePeriods, ' ', true),
						'QuestionType' => $question->QuestionType,
						'Category' => $question->CategoryName,
						'AttemptCount' => $maxAttemptCount ? min($question->AttemptCount, $maxAttemptCount) : $question->AttemptCount,
						'MaxAttemptCount' => is_null($maxAttemptCount) ? '&infin;' : $maxAttemptCount
					)
				  )
				: '';
			
			$qVersionIdList[] = $question->BaseQuestionVersionId;
		}

		if ($getFiles)
		{
			$userQuizModel =& AriModel::getInstance('Userquiz', $this->getFullPrefix());
			$files = $userQuizModel->getFiles($sid);
			for ($i = 0; $i < $cnt; $i++)
			{
				$question =& $questionList[$i];
				$qId = $question->QuestionId;
				$question->Files = (isset($files[$qId]))
					? $files[$qId]
					: null;
			}
		}

		return $questionList;
	}
	
	function getJsonQuestionList($sid, $filter = null, $parsePluginTags = true, $getFiles = false, $summaryTemplate = '')
	{
		$questionList = $this->getQuestionList($sid, $filter, $getFiles, $summaryTemplate);
		if ($parsePluginTags && $questionList)
		{
			AriKernel::import('Joomla.Plugins.PluginProcessHelper');
			
			for ($i = 0, $cnt = count($questionList); $i < $cnt; $i++)
				$questionList[$i]->Question = AriPluginProcessHelper::processTags($questionList[$i]->Question, true, array('scripts', 'custom'));
		}
		
		$jsonQuestionList = array();
		if ($questionList)
		{
			foreach ($questionList as $item)
			{
				$jsonQuestionList[] = array(
					'QuestionData' => json_encode($item),
					'QuestionIndex' => $item->QuestionIndex
				);
			}
		}
		
		return $jsonQuestionList;
	}
	
	function getFinishedResult($ticketId, $userId, $defaults = array())
	{
		$db =& $this->getDBO();
		
		$userId = intval($userId, 10);
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'Q.AutoMailToUser',
				'Q.MailGroupList',
				'Q.AdminEmail',
				'Q.AttemptCount',
				'Q.FullStatistics',
				'Q.FullStatisticsOnSuccess',
				'Q.FullStatisticsOnFail',
				'Q.AdminMailTemplateId',
				'Q.HideCorrectAnswers',
				'Q.ExtraParams',
				'Q.Metadata',
				'Q.Description',
				'QSI.StatisticsInfoId',
				'QSI.ExtraData',
				'QSI.UserId',
				'QSI.QuestionCount',
				'U.email AS Email',
				'U.Name AS UserName',
				'U.username AS Login',
				'Q.ResultScaleId',
				'Q.ResultTemplateType',
				'Q.PassedTemplateId',
				'Q.FailedTemplateId',
				'Q.PrintPassedTemplateId',
				'Q.PrintFailedTemplateId',
				'Q.MailPassedTemplateId',
				'Q.MailFailedTemplateId',
				'Q.CertificatePassedTemplateId',
				'Q.CertificateFailedTemplateId',
				'Q.QuizName',
				'QSI.PassedScore',
				'QSI.MaxScore',
				'QSI.UserScore', 
				'QSI.UserScorePercent AS PercentScore',
				'QSI.Passed',
				'QSI.Passed AS _Passed',
				'QSI.StartDate',
				'QSI.EndDate',
//				'(UNIX_TIMESTAMP(QSI.EndDate) - UNIX_TIMESTAMP(IFNULL(QSI.ResumeDate,QSI.StartDate))+ QSI.UsedTime) AS SpentTime',
				'QSI.ElapsedTime AS SpentTime',
				'QSI.QuizId',
				'QSI.ResultEmailed',
        'Q.ShareResults'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q ON QSI.QuizId = Q.QuizId');
		$query->leftJoin('#__users U ON QSI.UserId = U.Id');
		$query->where('QSI.TicketId = ' . $db->Quote($ticketId));
		$query->where('QSI.Status = ' . $db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		//if ($userId > 0)
			$query->where('(Q.ShareResults = 1 OR QSI.UserId = ' . $userId . ')');
		$query->group('QSI.StatisticsInfoId');
		$query->order('NULL');
		$db->setQuery((string)$query, 0, 1);
		$obj = $db->loadAssocList();
		if (is_null($obj))
		{
			return null;
		}
		
		$obj = (is_array($obj) && count($obj) > 0) ? $obj[0] : null;
		if ($obj != null)
		{
			if (!empty($defaults))
			{
				foreach ($defaults as $key => $value)
				{
					if (key_exists($key, $obj) && empty($obj[$key]))
					{ 
						$obj[$key] = $value;
					}
				}
			}
			
			$statisticsInfo = $this->getTable('Userquiz');
			$obj['ExtraData'] = $statisticsInfo->parseExtraDataXml($obj['ExtraData']);
			if (!$obj['UserId']) 
				$obj = array_merge($obj, $obj['ExtraData']);
			$obj['PercentScore'] = sprintf('%.2f', $obj['PercentScore']);
			$obj['PassedScore'] = sprintf('%.2f', $obj['PassedScore']);
			$obj['ExtraParams'] = $obj['ExtraParams'] ? json_decode($obj['ExtraParams']) : null;
			$obj['Metadata'] = $obj['Metadata'] ? json_decode($obj['Metadata']) : null;
		}
		
		if ($obj['MailGroupList'])
			$obj['MailGroupList'] = explode(',', $obj['MailGroupList']);

		return $obj;
	}
	
	function getPassedQuizCount($quizId, $userId)
	{
		$quizId = intval($quizId, 10);
		$userId = intval($userId, 10);
		
		if ($quizId < 1 || $userId < 1) 
			return 0;
		
		$db =& $this->getDBO();		
		$query = sprintf('SELECT COUNT(*) FROM #__ariquizstatisticsinfo WHERE UserId = %d AND QuizId = %d AND `Status` = %s LIMIT 0,1',
			$userId,
			$quizId,
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE)
		);
		$db->setQuery($query);
		$count = $db->loadResult();
		if (is_null($count))
		{
			return 0;
		}
		
		return $count;
	}
	
	function markResultSend($ticketId)
	{	
		$ticketId = trim($ticketId);
		if (empty($ticketId)) 
			return ;
			
		$db =& $this->getDBO();
		$query = sprintf(
			'UPDATE #__ariquizstatisticsinfo SET ResultEmailed = 1 WHERE TicketId = %s',
			$db->Quote($ticketId)
		);
		$db->setQuery($query);
		return $db->execute();
	}
	
	function _getResultsType($filter)
	{
		$type = 'All';
		if (empty($filter))
			return $type;
			
		$filterPredicate = $filter->getConfigValue('filter');
		if (empty($filterPredicate) || !isset($filterPredicate['ResultsFilter']))
			return $type;

		return $filterPredicate['ResultsFilter'];
	}

	function getFinishedInfoByCategory($statisticsInfoId)
	{
		$db = $this->getDBO();

		$query = sprintf(
			'SELECT 
				QC.CategoryName,
				IFNULL(SUM(QS.Score), 0) AS UserScore,
				SUM(IF(QV.Score, QV.Score, QV2.Score)) AS MaxScore
			FROM #__ariquizstatistics QS LEFT JOIN #__ariquizquestioncategory QC
				ON QS.QuestionCategoryId = QC.QuestionCategoryId
			INNER JOIN #__ariquizquestionversion QV
				ON QS.QuestionVersionId = QV.QuestionVersionId
			LEFT JOIN #__ariquizquestionversion QV2 
			 	ON QS.BankVersionId = QV2.QuestionVersionId
			WHERE 
				QS.StatisticsInfoId = %1$d 
				AND 
				QS.QuestionCategoryId > 0
			GROUP BY 
				QS.QuestionCategoryId
			ORDER BY 
				QC.CategoryName',
			$statisticsInfoId
		);
		$db->setQuery($query);
		$result = $db->loadAssocList();
		if (is_null($result))
		{
			return false;
		}

		if ($result)
			for ($i = 0, $c = count($result); $i < $c; $i++)
			{
				$resultItem = $result[$i];
				$userScore = intval($resultItem['UserScore'], 10);
				$maxScore = intval($resultItem['MaxScore'], 10);
				$result[$i]['PercentScore'] = $maxScore > 0 ? sprintf('%.2f', 100 * ($userScore / $maxScore)) : 0;
			}

		return $result;
	}
}