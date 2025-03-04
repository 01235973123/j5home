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

require_once dirname(__FILE__) . DS . 'question.php';

class AriQuizTableUserQuizQuestion extends AriTable
{	
	var $StatisticsId;
	var $QuestionId;
	var $QuestionVersionId;
	var $StatisticsInfoId;
	var $Data;
	var $QuestionIndex = 0;
	var $Question;
	var $Score = null;
	var $QuestionCategoryId;
	var $BankQuestionId = 0;
	var $BankVersionId = 0;
	var $InitData = null;
	var $AttemptCount = 0;
	var $BankQuestionVersion;
	var $PageNumber = 0;
	var $PageId = 0;
	var $Completed = 0;
	var $ElapsedTime = null;
	var $EndDate = null;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizstatistics', 'StatisticsId', $db);
		
		$this->Question = new AriQuizTableQuestion($db);
		$this->BankQuestionVersion = new AriQuizTableQuestionVersion($db);

		$this->addRelation('QuestionId', 'Question');
		$this->addRelation('BankVersionId', 'BankQuestionVersion');
	}
	
	function getBaseQuestionVersion()
	{
		return $this->BankQuestionId > 0 ? $this->BankQuestionVersion : $this->Question->QuestionVersion;
	}
	
	function getOverrideData()
	{
		return $this->BankQuestionId > 0 ? $this->Question->QuestionVersion->Data : null;
	}
	
	function getPageQuestions($pageId)
	{
		return $this->customLoadList(array(&$this, '_getPageQuestions'), array($pageId));
	}
	
	function _getPageQuestions($query, $queryParams, $pageId)
	{
		$tblAlias = $queryParams['tblAlias'];
		$query->where(
			sprintf('%1$s.PageId = %2$d',
				$tblAlias,
				$pageId
			)
		);
		$query->order(
			sprintf('%1$s.QuestionIndex ASC',
				$tblAlias
			)
		);
		
		return $query;
	}	
}