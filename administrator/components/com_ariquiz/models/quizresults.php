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

class AriQuizModelQuizresults extends AriModel 
{
	function __construct()
	{
		parent::__construct();

		// import constants
		$this->getTable('Quiz');
		$this->getTable('Userquiz');
	}
	
	function deleteResults($idList)
	{
		$idList = AriArrayHelper::toInteger($idList, 1);
		if (count($idList) == 0) 
			return false;
			
		$db =& $this->getDBO();
		
		$query = sprintf(
			'DELETE 
				SI,S,P,SF,SED 
			FROM 
				#__ariquizstatisticsinfo SI LEFT JOIN #__ariquizstatistics S 
					ON SI.StatisticsInfoId = S.StatisticsInfoId
				LEFT JOIN #__ariquizstatistics_pages P
					ON SI.StatisticsInfoId = P.StatisticsInfoId
				LEFT JOIN #__ariquizstatistics_files SF 
					ON S.StatisticsId = SF.StatisticsId
				LEFT JOIN #__ariquiz_statistics_extradata SED 
					ON SED.StatisticsInfoId = SI.StatisticsInfoId
			WHERE 
				SI.StatisticsInfoId IN (%s)',
			join(',', $idList)
		);
		
		$db->setQuery($query);
		if (!$db->execute()) 
		{
			return false;
		}
		
		return true;
	}
	
	function deleteAllResults()
	{
		$db =& $this->getDBO();
		
		$query = sprintf(
			'DELETE 
				SI,S,P,SF,SED 
			FROM 
				#__ariquizstatisticsinfo SI LEFT JOIN #__ariquizstatistics S 
					ON SI.StatisticsInfoId = S.StatisticsInfoId
				LEFT JOIN #__ariquizstatistics_pages P
					ON SI.StatisticsInfoId = P.StatisticsInfoId
				LEFT JOIN #__ariquizstatistics_files SF 
					ON S.StatisticsId = SF.StatisticsId 
				LEFT JOIN #__ariquiz_statistics_extradata SED 
					ON SED.StatisticsInfoId = SI.StatisticsInfoId
			WHERE 
				SI.Status = %s',
			$db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE)
		);
		
		$db->setQuery($query);
		if (!$db->execute())
		{
			return false;
		}
		
		return true;
	}
	
	function getResultCount($filter = null)
	{
		$db =& $this->getDBO();
		
		$query = AriDBUtils::getQuery();
		$query->select('COUNT(*)');
		$query->from('#__ariquizstatisticsinfo SSI');
		$query->innerJoin('#__ariquiz S ON SSI.QuizId = S.QuizId');
		$query->leftJoin('#__users U ON SSI.UserId = U.id');			
		$query->where('SSI.Status = ' . $db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));

		$query = $this->_applyResultsFilter($query, $filter);

		$db->setQuery((string)$query);
		$count = $db->loadResult();
		if (is_null($count))
		{
			return 0;
		}

		return $count;		
	}
	
	function getResultList($filter = null)
	{
		$db =& $this->getDBO();
                        
    	$sortField = null;
		if ($this->_isOnlyGuests($filter))
		{
			$sortField = $filter->getConfigValue('sortField');
			switch ($sortField) 
			{
				case 'Name':
					$filter->setConfigValue(
						'sortField', 
						'SUBSTR(ExtraData, LOCATE(\'<item name="UserName">\', ExtraData) + 22, LOCATE(\'</\', ExtraData) - LOCATE(\'<item name="UserName">\', ExtraData) - 22)'
					);
					break;

				case 'Email':
					$filter->setConfigValue(
						'sortField', 
						'SUBSTR(ExtraData, LOCATE(\'<item name="Email">\', ExtraData) + 19, LOCATE(\'</\', ExtraData, LOCATE(\'<item name="Email">\', ExtraData)) - LOCATE(\'<item name="Email">\', ExtraData) - 19)'
					);
					break;
			}
		}

		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'QC.CategoryName',
				'SSI.TicketId',
				'SSI.StatisticsInfoId',
				'SSI.Passed',
				'SSI.UserId',
				'SSI.ExtraData',
				'SSI.UserScore',
				'SSI.MaxScore',
				'U.Name',
				'U.username AS Login',
				'U.email AS Email',
				'U.Id',
				'S.QuizName',
				'S.QuizId',
				'SSI.StartDate',
				'SSI.EndDate',
				'SSI.StartDate AS StartDate2',
				'SSI.EndDate AS EndDate2',
				'SSI.UserScorePercent AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo SSI');
		$query->innerJoin('#__ariquiz S ON SSI.QuizId = S.QuizId');
		$query->leftJoin('#__ariquizquizcategory QQC ON QQC.QuizId = S.QuizId');
		$query->leftJoin('#__ariquizcategory QC ON QQC.CategoryId = QC.CategoryId');
		$query->leftJoin('#__users U ON SSI.UserId = U.id');			
		$query->where('SSI.Status = ' . $db->Quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));

		$query = $this->_applyResultsFilter($query, $filter);
		if ($filter)
			$query = $filter->applyToQuery($query);
		$query->group('SSI.StatisticsInfoId');

		$db->setQuery((string)$query, $filter->getConfigValue('startOffset'), $filter->getConfigValue('limit'));
		$results = $db->loadObjectList();
    
    	if ($this->_isOnlyGuests($filter))
      		$filter->setConfigValue('sortField', $sortField);

    
		if (is_null($results))
		{
			return 0;
		}
		
		return $this->applyExtraData($results);
	}
	
	function _applyResultsFilter($query, $filter, $ignoreQuizId = false)
	{
		$db =& $this->getDBO();
		
		if (empty($filter))
			return $query;
			
		$filterPredicates = $filter->getConfigValue('filter');
		if (!$ignoreQuizId && !empty($filterPredicates['QuizId'])) 
			$query->where('SSI.QuizId=' . intval($filterPredicates['QuizId'], 10));

		if (isset($filterPredicates['UserId']) && $filterPredicates['UserId'] != -1) 
			$query->where('SSI.UserId=' . intval($filterPredicates['UserId'], 10));

		if (!empty($filterPredicates['StartDate']))
		{
			$startDate = new JDate($filterPredicates['StartDate'], J1_6 ? 'UTC' : 0);
			$startDate = J3_0 ? $startDate->toSql() : $startDate->toMySQL(); 
			$query->where('SSI.EndDate >= ' . $db->Quote($startDate));
		}

		if (!empty($filterPredicates['EndDate']))
		{
			$endDate = new JDate($filterPredicates['EndDate']);
			$endDate = J3_0 ? $endDate->toSql() : $endDate->toMySQL();
			$query->where('SSI.EndDate <= ' . $db->Quote($endDate));
		}

		return $query;
	}
	
	function _isOnlyGuests($filter)
	{
		$isOnlyGuest = false;
		if (empty($filter))
			return $isOnlyGuest;

		$filterPredicates = $filter->getConfigValue('filter');
		if (isset($filterPredicates['UserId']) && $filterPredicates['UserId'] == 0) 
			$isOnlyGuest = true;

		return $isOnlyGuest;
	}
	
	function applyExtraData($results, $needToRemove = true)
	{
		if (!is_array($results) || count($results) == 0)
			return $results;

		reset($results);
		$vars = array_keys(get_object_vars(current($results)));
		$nameVarExists = in_array('Name', $vars);
		$statisticsInfo = $this->getTable('Userquiz');
		foreach ($results as $key => $value)
		{
			$result =& $results[$key];
			$extraData = $result->ExtraData;
			if ($needToRemove)
				unset($result->ExtraData);
			if ($result->UserId || empty($extraData)) 
				continue;

			$extraData = $statisticsInfo->parseExtraDataXml($extraData);
			foreach ($extraData as $key => $value)
			{
				if (in_array($key, $vars))
					$result->$key = $value;
				else if ($key == 'UserName' && $nameVarExists)
					$result->Name = $value;
			}
		}

		return $results;
	}
	
	function getLastResults($count = 5, $ignoreGuest = true, $categoryId = null)
	{		
		if (!is_null($categoryId))
		{
			$categoryId = AriArrayHelper::toInteger($categoryId, 1);
			if (count($categoryId) == 0) 
				return null;
		}

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'IFNULL(U.id, 0) AS UserId',
				'U.name AS UserName',
				'U.username AS LoginName', 
				'Q.QuizName', 
				'Q.QuizId',
				'QSI.UserScore', 
				'QSI.UserScorePercent AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q	ON QSI.QuizId = Q.QuizId');
		if ($ignoreGuest)
			$query->innerJoin('#__users U ON QSI.UserId = U.id');
		else 
			$query->leftJoin('#__users U ON QSI.UserId = U.id');
			
		if (!empty($categoryId))
			$query->leftJoin('#__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId');
			
		$query->where('QSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$query->where('Q.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		if (!empty($categoryId))
			$query->where('IFNULL(QC.CategoryId, 0) IN (' . join(',', $categoryId) . ')');
			
		$query->order('QSI.EndDate DESC');
		$db->setQuery((string)$query, 0, $count);
		$results = $db->loadObjectList();
		if (is_null($results))
		{
			return null;
		}
		
		return $results;
	}

	function getLastUserResults($userId, $count = 5)
	{
		$userId = intval($userId, 10);
		if ($userId < 1)
			return null;
			
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'Q.QuizName',
				'Q.QuizId',
				'QSI.UserScore',
				'QSI.UserScorePercent AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q	ON QSI.QuizId = Q.QuizId');
		$query->where('QSI.UserId = ' . $userId);
		$query->where('QSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$query->where('Q.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		$query->order('QSI.EndDate DESC');
		
		$db->setQuery((string)$query, 0, $count);
		$results = $db->loadObjectList();
		if (is_null($results))
		{
			return null;
		}
		
		return $results;
	}

	function getTopUserResults($userId, $count = 5)
	{	
		$userId = intval($userId, 10);
		if ($userId < 1)
			return null;

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'Q.QuizName',
				'Q.QuizId',
				'MAX(QSI.UserScore) AS UserScore',
				'MAX(QSI.UserScorePercent) AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q	ON QSI.QuizId = Q.QuizId');
		$query->where('QSI.UserId = ' . $userId);
		$query->where('QSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$query->where('Q.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		$query->group('QSI.QuizId');
		$query->order('PercentScore DESC');
		
		$db->setQuery((string)$query, 0, $count);
		$results = $db->loadObjectList();
		if (is_null($results))
		{
			return null;
		}
		
		return $results;
	}
	
	function getTopResults($count = 5, $ignoreGuest = true, $categoryId = null, $aggregateUserResults = false, $startDate = null, $endDate = null)
	{
		if (!is_null($categoryId))
		{
			$categoryId = AriArrayHelper::toInteger($categoryId, 1);
			if (count($categoryId) == 0) 
				return null;
		}

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'IFNULL(U.id, 0) AS UserId',
				'U.name AS UserName',
				'U.username AS LoginName', 
				'Q.QuizName', 
				'Q.QuizId',
				'QSI.UserScore', 
				($aggregateUserResults ? 'MAX' : '') . '(QSI.UserScorePercent) AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q	ON QSI.QuizId = Q.QuizId');
		if ($ignoreGuest)
			$query->innerJoin('#__users U ON QSI.UserId = U.id');
		else 
			$query->leftJoin('#__users U ON QSI.UserId = U.id');
			
		if (!empty($categoryId))
			$query->leftJoin('#__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId');
			
		$query->where('QSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$query->where('Q.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		if (!empty($categoryId))
			$query->where('IFNULL(QC.CategoryId, 0) IN (' . join(',', $categoryId) . ')');
			
		if (!empty($startDate))
			$query->where('QSI.EndDate >= ' . $db->quote(AriDateUtility::toDbUtcDate($startDate)));
			
		if (!empty($endDate))
			$query->where('QSI.EndDate <= ' . $db->quote(AriDateUtility::toDbUtcDate($endDate)));
			
		if ($aggregateUserResults)
			$query->group('U.id');
			
		$query->order('PercentScore DESC,IF(U.id, 1, 0) DESC, QSI.EndDate ASC');
			
		$db->setQuery((string)$query, 0, $count);
		$results = $db->loadObjectList();
		if (is_null($results))
		{
			return null;
		}
		
		return $results;
	}		

	function getAggregateTopResults($count = 5, $ignoreGuest = true, $categoryId = null, $startDate = null, $endDate = null)
	{
		if (!is_null($categoryId))
		{
			$categoryId = AriArrayHelper::toInteger($categoryId, 1);
			if (count($categoryId) == 0) 
				return null;
		}

		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();
		$query->select(
			array(
				'IFNULL(U.id, 0) AS UserId',
				'U.name AS UserName',
				'U.username AS LoginName', 
				'Q.QuizName', 
				'Q.QuizId',
				'QSI.UserScore', 
				'MAX(QSI.UserScorePercent) AS PercentScore'
			)
		);
		$query->from('#__ariquizstatisticsinfo QSI');
		$query->innerJoin('#__ariquiz Q	ON QSI.QuizId = Q.QuizId');
		if ($ignoreGuest)
			$query->innerJoin('#__users U ON QSI.UserId = U.id');
		else 
			$query->leftJoin('#__users U ON QSI.UserId = U.id');
			
		
		$subQuery = AriDBUtils::getQuery();
		$subQuery->select(
			array(
				'MAX(TQSI.UserScorePercent) AS MaxScorePercent',
         		'TQSI.QuizId AS QuizId'
			)
		);
		$subQuery->from('#__ariquizstatisticsinfo TQSI');
		$subQuery->innerJoin('#__ariquiz TQ ON TQSI.QuizId = TQ.QuizId');
		if ($ignoreGuest)
			$subQuery->innerJoin('#__users TU ON TQSI.UserId = TU.id');
		else 
			$subQuery->leftJoin('#__users TU ON TQSI.UserId = TU.id');
		$subQuery->where('TQSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$subQuery->where('TQ.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		if (!empty($startDate))
			$subQuery->where('TQSI.EndDate >= ' . $db->quote(AriDateUtility::toDbUtcDate($startDate)));
			
		if (!empty($endDate))
			$subQuery->where('TQSI.EndDate <= ' . $db->quote(AriDateUtility::toDbUtcDate($endDate)));
		$subQuery->group('TQSI.QuizId');
		$subQuery->order('NULL');
		
		
		$query->innerJoin(
			sprintf(
				'(%1$s) T ON QSI.QuizId = T.QuizId',
				(string)$subQuery
			)
		);
			
		if (!empty($categoryId))
			$query->leftJoin('#__ariquizquizcategory QC ON QSI.QuizId = QC.QuizId');
			
		$query->where('QSI.Status = ' . $db->quote(ARIQUIZ_USERQUIZ_STATUS_COMPLETE));
		$query->where('Q.Status = ' . ARIQUIZ_QUIZ_STATUS_ACTIVE);
		$query->where('QSI.UserScorePercent >= T.MaxScorePercent');
		if (!empty($categoryId))
			$query->where('IFNULL(QC.CategoryId, 0) IN (' . join(',', $categoryId) . ')');
			
		if (!empty($startDate))
			$query->where('QSI.EndDate >= ' . $db->quote(AriDateUtility::toDbUtcDate($startDate)));
			
		if (!empty($endDate))
			$query->where('QSI.EndDate <= ' . $db->quote(AriDateUtility::toDbUtcDate($endDate)));
		
		$query->group('QSI.QuizId');
		$query->order('PercentScore DESC');
		
		$db->setQuery((string)$query, 0, $count);
		$results = $db->loadObjectList();
		if (is_null($results))
		{
			return null;
		}
		
		return $results;
	}	
}