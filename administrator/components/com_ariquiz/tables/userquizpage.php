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

AriKernel::import('Joomla.Tables.Table');

require_once dirname(__FILE__) . '/userquizquestion.php';

class AriQuizTableUserQuizPage extends AriTable
{	
	var $PageId;
	var $StatisticsInfoId;
	var $PageNumber;
	var $PageIndex;
	var $QuestionCount = 0;
	var $StartDate = null;
	var $EndDate = null;
	var $SkipDate = null;
	var $SkipCount = 0;
	var $PageTime = null;
	var $UsedTime = 0;
	var $IpAddress = null;
	var $Description = '';
	var $Questions = array();

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizstatistics_pages', 'PageId', $db);
		
		//$this->addRelation('PageId', 'Questions', ARI_TABLE_RELATION_ONETOMANY, 'AriQuizTableUserQuizQuestion', 'PageId');
	}

	function loadWithQuestions($pageId)
	{
		$result = parent::load($pageId);

		if (!$result)
			return $result;
			
		$question = new AriQuizTableUserQuizQuestion($this->getDBO());
		$this->Questions = $question->getPageQuestions($this->PageId);
		
		return true;
	}
	
	function loadNextPage($sid, $userId = null)
	{
		$result = $this->customLoad(array(&$this, '_loadNextPage'), array($sid, $userId), 0, 1);
		if (!$result)
			return $result;
			
		$question = new AriQuizTableUserQuizQuestion($this->getDBO());
		$this->Questions = $question->getPageQuestions($this->PageId);
		
		return true;
	}

	function _loadNextPage($query, $queryParams, $sid, $userId)
	{
		$tblAlias = $queryParams['tblAlias'];
		
		$query->innerJoin('#__ariquizstatisticsinfo SSI ON SSI.StatisticsInfoId = ' . $tblAlias . '.StatisticsInfoId');
		
		$query->where('SSI.StatisticsInfoId = ' . intval($sid, 10));
		if (!is_null($userId))
			$query->where('SSI.UserId = ' . intval($userId, 10));

		$query->where($tblAlias . '.EndDate IS NULL');
		$query->where(sprintf(
			'(
				%1$s.StartDate IS NULL 
				OR 
				%1$s.PageTime = 0
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(%1$s.StartDate), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(%1$s.StartDate), 
						0
					) 
					+ 
					%1$s.UsedTime
				) < %1$s.PageTime
			)',
			$tblAlias)
		);
		$query->where(
			'(
				SSI.TotalTime = 0 
				OR 
				SSI.StartDate IS NULL
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)),
						0
					) 
					+ 
					SSI.UsedTime
				) < SSI.TotalTime
			)'
		);
		$query->order(
			sprintf('%1$s.PageIndex ASC',
				$tblAlias)
		);
		
		return $query;
	}
	
	function loadCurrentPage($sid, $userId = 0)
	{
		$result = $this->customLoad(array(&$this, '_loadCurrentPage'), array($sid, $userId), 0, 1);
		if (!$result)
			return $result;

		$question = new AriQuizTableUserQuizQuestion($this->getDBO());
		$this->Questions = $question->getPageQuestions($this->PageId);

		return true;
	}
	
	function _loadCurrentPage($query, $queryParams, $sid, $userId)
	{
		$tblAlias = $queryParams['tblAlias'];
		
		$query->innerJoin('#__ariquizstatisticsinfo SSI ON SSI.StatisticsInfoId = ' . $tblAlias . '.StatisticsInfoId');
		
		$query->where('SSI.StatisticsInfoId = ' . intval($sid, 10));
		if ($userId > 0)
			$query->where('SSI.UserId = ' . intval($userId, 10));

		$query->where($tblAlias . '.StartDate IS NOT NULL');
		$query->where($tblAlias . '.EndDate IS NULL');
		$query->where(sprintf(
			'(
				%1$s.PageTime = 0 
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(%1$s.StartDate), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(%1$s.StartDate), 
						0
					) 
					+ 
					%1$s.UsedTime
				) < %1$s.PageTime
			)',
			$tblAlias)
		);
		$query->where(
			'(
				SSI.TotalTime = 0 
				OR 
				SSI.StartDate IS NULL
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)), 
						0
					) 
					+ 
					SSI.UsedTime
				) < SSI.TotalTime
			)'
		);
		$query->order(
			sprintf('%1$s.PageIndex ASC',
				$tblAlias)
		);

		return $query;
	}
	
	function loadCurrentPageByTicketId($ticketId, $userId = 0)
	{
		$result = $this->customLoad(array(&$this, '_loadCurrentPageByTicketId'), array($ticketId, $userId), 0, 1);
		if (!$result)
			return $result;

		$question = new AriQuizTableUserQuizQuestion($this->getDBO());
		$this->Questions = $question->getPageQuestions($this->PageId);

		return true;
	}
	
	function _loadCurrentPageByTicketId($query, $queryParams, $ticketId, $userId)
	{
		$db = $this->getDBO();
		$tblAlias = $queryParams['tblAlias'];
		
		$query->innerJoin('#__ariquizstatisticsinfo SSI ON SSI.StatisticsInfoId = ' . $tblAlias . '.StatisticsInfoId');
		
		$query->where('SSI.TicketId = ' . $db->Quote($ticketId));
		if ($userId > 0)
			$query->where('SSI.UserId = ' . intval($userId, 10));

		$query->where($tblAlias . '.StartDate IS NOT NULL');
		$query->where($tblAlias . '.EndDate IS NULL');
		/*
		$query->where(sprintf(
			'(
				%1$s.PageTime = 0 
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(%1$s.StartDate), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(%1$s.StartDate), 
						0
					) 
					+ 
					%1$s.UsedTime
				) < %1$s.PageTime
			)',
			$tblAlias)
		);
		$query->where(
			'(
				SSI.TotalTime = 0 
				OR 
				SSI.StartDate IS NULL
				OR 
				(
					IF(
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) > UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)), 
						UNIX_TIMESTAMP(UTC_TIMESTAMP()) - UNIX_TIMESTAMP(IFNULL(SSI.ResumeDate, SSI.StartDate)), 
						0
					) 
					+ 
					SSI.UsedTime
				) < SSI.TotalTime
			)'
		);
		*/
		$query->order(
			sprintf('%1$s.PageIndex ASC',
				$tblAlias)
		);

		return $query;
	}
	
	function containsQuestion($questionId)
	{
		if (empty($this->Questions))
			return false;
			
		foreach ($this->Questions as $question)
			if ($question->QuestionId == $questionId)
				return true;
				
		return false;
	}
	
	function getQuestion($questionId)
	{
		if (empty($this->Questions))
			return null;
			
		foreach ($this->Questions as $question)
			if ($question->QuestionId == $questionId)
				return $question;
				
		return null;
	}
}