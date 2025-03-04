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
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Joomla.Database.DBUtils');

require_once dirname(__FILE__) . DS . 'exportresults' . DS . 'class.ResultTemplates.php';

class AriQuizModelQuizexportresults extends AriModel 
{
	function getBaseView($idList)
	{
		$db =& $this->getDBO();

		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return null;
		
		$query = sprintf('SELECT IF(QQV.Score, QQV.Score, QQV2.Score) AS MaxScore,SSI.StartDate AS QuizStartDate, SSI.EndDate AS QuizEndDate, SSI.UserId, SSI.ExtraData, SS.QuestionIndex, SP.IpAddress, SSI.UserScore AS QuizUserScore,SSI.QuestionCount,SSI.MaxScore AS QuizMaxScore,SSI.Passed,SSI.PassedScore AS QuizPassedScore, U.name AS UserName, U.email AS Email, Q.QuizName, Q.QuizId, SS.Score, SS.StatisticsInfoId, SS.StatisticsId, IF(QQV2.QuestionId,QQV2.Question,QQV.Question) AS Question, IF(QQV2.QuestionId,QQV2.Data,QQV.Data) AS BaseData, SS.Data, QQC.CategoryName, QQT.QuestionType, QQT.ClassName, SS.QuestionVersionId, SP.StartDate, SP.EndDate,  (UNIX_TIMESTAMP(SP.EndDate) - UNIX_TIMESTAMP(SP.StartDate) + SP.UsedTime) AS TotalTime' .
			' FROM #__ariquizstatisticsinfo SSI INNER JOIN #__ariquizstatistics SS ON SSI.StatisticsInfoId = SS.StatisticsInfoId' .
			' INNER JOIN #__ariquizstatistics_pages SP ON SP.PageId = SS.PageId' .
			' INNER JOIN #__ariquiz Q ON SSI.QuizId = Q.QuizId' .
			' LEFT JOIN #__ariquizquestionversion QQV' .
         	'	ON SS.QuestionVersionId = QQV.QuestionVersionId' .
    		' LEFT JOIN #__ariquizquestionversion QQV2' .
         	'	ON SS.BankVersionId = QQV2.QuestionVersionId' .
			' INNER JOIN #__ariquizquestiontype QQT ON IFNULL(QQV2.QuestionTypeId,QQV.QuestionTypeId) = QQT.QuestionTypeId' .
			' LEFT JOIN #__ariquizquestioncategory QQC ON QQV.QuestionCategoryId = QQC.QuestionCategoryId' .
			' LEFT JOIN #__users U ON SSI.UserId = U.id' .
			' WHERE SSI.Status = "Finished" AND SS.StatisticsInfoId IN (%s)' .
			' ORDER BY SS.StatisticsInfoId, SS.QuestionIndex',
			join(',', $idList)
		);
		$db->setQuery($query);
		$results = $db->loadObjectList('StatisticsId');
		if (is_null($results))
		{
			return null;
		}

		$quizResultsModel = & AriModel::getInstance('Quizresults', $this->getFullPrefix());
		
		return $quizResultsModel->applyExtraData($results);
	}
	
	function getSimpleBaseView($idList)
	{
		$db =& $this->getDBO();

		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return null;
		
		$query = sprintf('SELECT SSI.StatisticsInfoId,SSI.UserScore AS QuizUserScore,SSI.QuestionCount,SSI.ExtraData,SSI.UserId,SSI.MaxScore AS QuizMaxScore,SSI.Passed,SSI.PassedScore AS QuizPassedScore, U.name AS UserName, U.email AS Email, Q.QuizName, Q.QuizId, SSI.StartDate, SSI.EndDate, SSI.ElapsedTime AS TotalTime' .
			' FROM #__ariquizstatisticsinfo SSI INNER JOIN #__ariquiz Q ON SSI.QuizId = Q.QuizId' .
			' LEFT JOIN #__users U ON SSI.UserId = U.id' .
			' WHERE SSI.Status = "Finished" AND SSI.StatisticsInfoId IN (%s)' .
			' ORDER BY SSI.StatisticsInfoId',
			join(',', $idList)
		);
		$db->setQuery($query);
		$results = $db->loadObjectList('StatisticsInfoId');
		if (is_null($results))
		{
			return null;
		}
		
		$quizResultsModel = & AriModel::getInstance('Quizresults', $this->getFullPrefix());
		
		return $quizResultsModel->applyExtraData($results);
	}
	
	function getCSVView($idList, $params = array(), $periods = null)
	{
		$fields = array(
			'#', 
			'Quiz Name', 
			'User', 
			'Email', 
			'Question Count', 
			'Passed', 
			'Start Date', 
			'End Date', 
			'Spent Time', 
			'User Score', 
			'User Score Percent', 
			'Max Score', 
			'Passing Score'
		);
		$fields = array_map(function ($v) { return '"' . $v . '"'; }, $fields);
		$csv = join("\t", $fields);

		$results = $this->getSimpleBaseView($idList);
		
		if (!empty($results))
		{
			$anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
			$passed = isset($params['Passed']) ? $params['Passed'] : '';
			$noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
			$i = 1;
			foreach ($results as $result)
			{
				$csv .= "\r\n";
				$userScorePercent = boolval(+$result->QuizMaxScore) 
					? round(100 * $result->QuizUserScore / $result->QuizMaxScore, 2)
					: 100.00;
				$rowData = array(
					$i, 
					$result->QuizName,
					!empty($result->UserName) ? $result->UserName : $anonymous,
					$result->Email,
					$result->QuestionCount,
					$result->Passed ? $passed : $noPassed,
					AriDateUtility::formatDate($result->StartDate),
					AriDateUtility::formatDate($result->EndDate),
					AriDateDurationUtility::toString($result->TotalTime, $periods, ' ', true),
					$result->QuizUserScore,
					$userScorePercent . '%',
					$result->QuizMaxScore,
					$result->QuizPassedScore . '%'); 
				foreach ($rowData as $key => $dataItem)
				{
					$rowData[$key] = str_replace("\t", ' ', $dataItem);
				}

				$csv .= join("\t", $rowData);
				
				++$i;
			}
		}
		
		if (function_exists('iconv'))
		{
			$csv = chr(255) . chr(254) . @iconv('UTF-8', 'UTF-16LE', $csv);
		}
		else if (function_exists('mb_convert_encoding'))
		{
			$csv = chr(255) . chr(254) . @mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
		}
		
		return $csv;
	}
	
	function getHtmlView($idList, $params = array(), $periods = null)
	{
		$htmlView = ARI_RESULT_HTML_TEMPLATE;

		$htmlPageBreak = '<br clear="all" style="page-break-before:always" />';
		
		$htmlQuizDataHeader = ARI_RESULT_HTML_QUIZ_DATA_HEADER;

		$htmlQuizHeader = ARI_RESULT_HTML_QUIZ_HEADER;
		$results = $this->getBaseView($idList);
		$simpleResults = $this->getSimpleBaseView($idList);
		if (empty($results) || empty($simpleResults))
		{
			return '';
		}

		$data = '';
		if (!empty($results))
		{
			$prevId = 0;
			$subData = '';
			$anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
			$passed = isset($params['Passed']) ? $params['Passed'] : '';
			$noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
			
			foreach ($results as $result)
			{
				if ($prevId != $result->StatisticsInfoId)
				{
					if (!empty($prevId))
					{
						$data .= sprintf($htmlQuizDataHeader, $subData);
						$data .= $htmlPageBreak;
					}
					
					$quizTotalTime = isset($simpleResults[$result->StatisticsInfoId]) ? $simpleResults[$result->StatisticsInfoId]->TotalTime : 0;
					$header = sprintf($htmlQuizHeader,
						$result->QuizName,
						AriDateUtility::formatDate($result->QuizStartDate),
						AriDateUtility::formatDate($result->QuizEndDate),
						AriDateDurationUtility::toString($quizTotalTime, $periods, ' ', true),
						sprintf('%.2f', $result->QuizUserScore),
						sprintf('%.2f', $result->QuizMaxScore),
						sprintf('%.2f', $result->QuizPassedScore) . '%',
						$result->Passed ? $passed : $noPassed,
						!empty($result->UserName) ? $result->UserName : $anonymous,
						$result->Email);

					$data .= $header;
					$subData = '';
				}
				
				$index = $result->QuestionIndex + 1;
				$ip = long2ip($result->IpAddress);
				$subData .= sprintf(ARI_RESULT_HTML_DATA_ROW,
					$index,
					$result->Question,
					sprintf('%.2f', $result->Score),
					sprintf('%.2f', $result->MaxScore)); 

				$prevId = $result->StatisticsInfoId;
			}
			
			$data .= sprintf($htmlQuizDataHeader, $subData);
		}
		$htmlView = sprintf($htmlView, $data);
		
		return $htmlView;
	}
	
	function getExcelView($idList, $params = array(), $periods = null)
	{
		$results = $this->getSimpleBaseView($idList);
		if (empty($results))
			return '';
		
		$data = '';
		if (!empty($results))
		{
			$i = 1;
			$anonymous = isset($params['Anonymous']) ? $params['Anonymous'] : '';
			$passed = isset($params['Passed']) ? $params['Passed'] : '';
			$noPassed = isset($params['NoPassed']) ? $params['NoPassed'] : '';
			foreach ($results as $result)
			{
				$userScorePercent = boolval(+$result->QuizMaxScore) 
					? round(100 * $result->QuizUserScore / $result->QuizMaxScore, 2)
					: 100.00;
				$data .= sprintf(ARI_RESULT_HTML_EXCEL_ROW,
					$i, 
					$result->QuizName,
					!empty($result->UserName) ? $result->UserName : $anonymous,
					$result->Email,
					$result->QuestionCount,
					$result->Passed ? $passed : $noPassed,
					AriDateUtility::formatDate($result->StartDate),
					AriDateUtility::formatDate($result->EndDate),
					AriDateDurationUtility::toString($result->TotalTime, $periods, ' ', true),
					$result->QuizUserScore,
					$userScorePercent . '%',
					$result->QuizMaxScore,
					$result->QuizPassedScore . '%'); 				
				++$i;
			}
		}

		$excel = sprintf(ARI_RESULT_HTML_EXCEL_TEMPLATE, $data);

		return $excel;
	}
	
	function getWordView($statisticsInfoId, $params = array(), $periods = null)
	{
		return $this->getHtmlView($statisticsInfoId, $params, $periods);
	}
}