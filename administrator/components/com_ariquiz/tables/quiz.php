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
AriKernel::import('Utils.Utils');
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Utils.DateUtility');

define('ARIQUIZ_QUIZ_STATUS_ACTIVE', 1);
define('ARIQUIZ_QUIZ_STATUS_INACTIVE', 2);
define('ARIQUIZ_QUIZ_STATUS_DELETE', 4);

class AriQuizTableQuiz extends AriTable
{
	var $QuizId = null;
	var $QuizName = '';
	var $Description = '';
	var $CreatedBy = 0;
	var $Created;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $AccessType = null;
	var $Status;
	var $TotalTime = 0;
	var $PassedScore = 0;
	var $QuestionCount = 0;
	var $QuestionTime = 0;
	var $AttemptCount = 0;
	var $AttemptPeriod = null;
	var $LagTime = 0;
	var $StartDate = null;
	var $EndDate = null;
	var $CategoryList = array();
	var $StartImmediately = 0;
	var $Access = -1;

	var $ResultTemplateType;
	var $ResultScaleId = 0;
	var $PassedTemplateId = 0;
	var $FailedTemplateId = 0;
	var $PrintPassedTemplateId = 0;
	var $PrintFailedTemplateId = 0;
	var $MailPassedTemplateId = 0;
	var $MailFailedTemplateId = 0;
	var $CertificatePassedTemplateId = 0;
	var $CertificateFailedTemplateId = 0;
	var $AdminMailTemplateId = 0;
	
	var $AutoMailToUser = 0;
	var $MailGroupList = array();
	var $AdminEmail = '';
	var $Anonymous = 'Yes';
	var $RandomQuestion = 0;
	var $FullStatistics = 'Never';
	var $FullStatisticsOnSuccess = 'All';
	var $FullStatisticsOnFail = 'All';
	var $HideCorrectAnswers = 0;
	var $ExtraParams = null;
	var $Metadata = null;
    var $ShareResults = 0;
    var $PrevQuizId = 0;

	var $asset_id = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz', 'QuizId', $db);
	}
	
	function bind($from, $ignore = array())
	{
		$ignore[] = 'CategoryList';
		$ignore[] = 'MailGroupList';

		if (parent::bind($from, $ignore) === false)
			return false;
			
		$categoryList = AriUtils::getParam($from, 'CategoryList', null);
		$mailGroupList = AriUtils::getParam($from, 'MailGroupList', null);
		$resultScaleId = AriUtils::getParam($from, 'ResultScaleId', null);

		if (!is_null($categoryList))
			$this->CategoryList = $categoryList;
			
		if (!is_null($mailGroupList))
			$this->MailGroupList = $mailGroupList;

		if (empty($resultScaleId)) {
			$this->ResultScaleId = 0;
		}

		return true;
	}

	function update($fields, $extraFields)
	{
		$this->bind($fields);
		
		if (!empty($extraFields))
		{
			if (empty($this->ExtraParams))
				$this->ExtraParams = $extraFields;
			else
			{
				foreach ($extraFields as $fieldName => $fieldVal)
				{
					$this->ExtraParams->$fieldName = $fieldVal;
				}
			}
		}

		$updateCategories = isset($fields['CategoryList']);

		return $this->store($updateCategories, false);		
	}

	function getParam($name, $defValue = null)
	{
		return isset($this->ExtraParams->$name) ? $this->ExtraParams->$name : $defValue;
	}
	
	function getMetaParam($name, $defValue = null)
	{
		return isset($this->Metadata->$name) ? $this->Metadata->$name : $defValue;
	}
	
	function copyFrom($quiz, $quizName, $userId = 0, $created = null)
	{
		if (empty($quiz))
			return false;
		
		if (is_null($created))
			$created = AriDateUtility::getDbUtcDate();
			
		if (!$this->bind($quiz->toArray()))
			return false;

		$this->QuizId = 0;
		$this->QuizName = str_replace('{$QuizName}', $quiz->QuizName, $quizName);
		$this->Created = $created;
		if (!empty($userId))
			$this->CreatedBy = $userId;
		$this->Modified = null;
		$this->ModifiedBy = null;

		$this->MailGroupList = $quiz->MailGroupList;
		$this->CategoryList = $quiz->CategoryList;
		$this->ExtraParams = $quiz->ExtraParams;
		$this->Metadata = $quiz->Metadata;

		return true;
	}

	function store($updateCategories = true, $updateDates = true, $updateNulls = false)
	{
		$this->ExtraParams = $this->ExtraParams ? json_encode($this->ExtraParams) : '';
		$this->Metadata = $this->Metadata ? json_encode($this->Metadata) : '';
		$this->CategoryList = AriArrayHelper::toInteger($this->CategoryList, 1);
		$this->MailGroupList = join(',', AriArrayHelper::toInteger($this->MailGroupList, 1));
		
		if ($updateDates)
		{
			$this->StartDate = AriDateUtility::toDbUtcDate($this->StartDate);
			$this->EndDate = AriDateUtility::toDbUtcDate($this->EndDate);
		}

		if (empty($this->PassedScore)) {
			$this->PassedScore = 0;
		}

		if (empty($this->TotalTime)) {
			$this->TotalTime = 0;
		}

		if (empty($this->QuestionTime)) {
			$this->QuestionTime = 0;
		}

		if (empty($this->QuestionCount)) {
			$this->QuestionCount = 0;
		}
		
		if (empty($this->LagTime)) {
			$this->LagTime = 0;
		}

		if (empty($this->AttemptCount)) {
			$this->AttemptCount = 0;
		}

		$result = parent::store($updateNulls);

		if ($result)
		{
			if ($updateCategories)
				$result &= $this->_updateCategories();				
		}

		return $result;
	}
	
	function load($oid = null, $reset = true)
	{
		$result = parent::load($oid, $reset);

		if (!$result)
			return $result;
			
		if ($this->ExtraParams)
			$this->ExtraParams = json_decode($this->ExtraParams);
			
		if ($this->Metadata)
			$this->Metadata = json_decode($this->Metadata);
			
		$this->CategoryList = $this->_getCategories();
		$this->MailGroupList = $this->_getMailGroupList();

		return $result;
	}
	
	function loadByTicketId($ticketId)
	{
		return $this->customLoad(array(&$this, '_loadByTicketId'), array($ticketId), 0, 1);
	}
	
	function _loadByTicketId($query, $queryParams, $ticketId)
	{
		$db =& $this->getDBO();
		$tblAlias = $queryParams['tblAlias'];

		$query->innerJoin('#__ariquizstatisticsinfo SSI ON SSI.QuizId = ' . $tblAlias . '.QuizId');
		
		$query->where('SSI.TicketId = ' . $db->Quote($ticketId));
			
		return $query;
	}
	
	function _getMailGroupList()
	{
		return is_array($this->MailGroupList) ? $this->MailGroupList : explode(',', $this->MailGroupList);
	}
	
	function _getCategories()
	{
		if ($this->isNew())
			return array();
			
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf('SELECT CategoryId FROM #__ariquizquizcategory WHERE QuizId = %d',
				$this->QuizId));
		$categories = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		if (!$categories)
		{
			$categories = array();
		}
		
		return $categories;
	}

	function _updateCategories()
	{
		if (!$this->isNew())
			if (!$this->_clearCategories())
				return false;

		$categories = AriArrayHelper::toInteger($this->CategoryList, 1);
		if (count($categories) == 0)
			return true;
			
		$sqlData = array();
		foreach ($categories as $category)
		{
			$sqlData[] = sprintf('(%1$d,%2$d)',
				$this->QuizId,
				$category);
		}

		$db =& $this->getDBO();
		$db->setQuery('INSERT INTO #__ariquizquizcategory (QuizId,CategoryId) VALUES' . join(',', $sqlData));
		if (!$db->execute())
		{
			return false;
		}
		
		return true;
	}

	function _clearCategories()
	{
		$db =& $this->getDBO();
		$db->setQuery(
			sprintf('DELETE FROM #__ariquizquizcategory WHERE QuizId = %d',
				$this->QuizId
			)
		);
		if (!$db->execute())
		{
			return false;
		}
		
		return true;
	}
	
	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.quiz.'. (int)$this->$key;        
	}

 	protected function _getAssetTitle()
 	{
 		return $this->QuizName;
 	}

 	protected function _getAssetParentId($table = null, $id = null)
 	{                
 		$assetParent = JTable::getInstance('Asset');

 		$assetParentId = $assetParent->getRootId();                
		if (is_array($this->CategoryList) && count($this->CategoryList) > 0)
		{
			$catId = $this->CategoryList[0];
			$assetParent->loadByName('com_ariquiz.category.' . $catId);                
 		}
 		else
		{                        
 			$assetParent->loadByName('com_ariquiz');
		}                 

		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}
	
	function getAccess()
	{
		if ($this->Access > -1)
			return $this->Access;

		$access = -1;
		if (!is_array($this->CategoryList) || count($this->CategoryList) < 1)
			return $access;
			
		$categoryId = $this->CategoryList[0];

		$db = $this->getDBO();
		$db->setQuery(
			sprintf(
				'SELECT 
					P.access 
				FROM 
					#__ariquizcategory P, 
					(SELECT lft, rgt FROM #__ariquizcategory C WHERE C.CategoryId = %1$d) CT
				WHERE
					P.lft <= CT.lft AND P.rgt >= CT.rgt AND P.access > -1
				ORDER BY
					P.lft DESC
				LIMIT 0,1',
				$categoryId
			)
		);
		$access = $db->loadResult();
		if (is_null($access))
		{
			return -1;
		}
		
		if (!J1_6 && $access == 1)
			$access = 0;

		return $access;
	}
}