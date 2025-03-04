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

define('ARIQUIZ_QUESTION_STATUS_ACTIVE', 1);
define('ARIQUIZ_QUESTION_STATUS_INACTIVE', 4);
define('ARIQUIZ_QUESTION_STATUS_DELETE', 2);

require_once dirname(__FILE__) . DS . 'questionversion.php';

class AriQuizTableQuestion extends AriTable 
{
	var $QuestionId;
	var $QuizId;
	var $QuestionVersionId = null;
	var $QuestionCategoryId = 0;
	var $QuestionTypeId = 0;
	var $CreatedBy;
	var $Created;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $QuestionVersion;
	var $Status = ARIQUIZ_QUESTION_STATUS_ACTIVE;
	var $QuestionIndex = 0;
	var $BankQuestionId = 0;

	var $asset_id = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizquestion', 'QuestionId', $db);

		$this->QuestionVersion = new AriQuizTableQuestionVersion($db);
		
		$this->addRelation('QuestionVersionId', 'QuestionVersion');
	}
	
	function copyFrom($question, $quizId, $questionCategoryId, $questionIndex = 0, $userId = 0, $created = null)
	{
		if (empty($question))
			return false;

		if (is_null($created))
			$created = AriDateUtility::getDbUtcDate();

		$fields = $question->toArray();
		$fields['QuestionId'] = 0;
		$fields['QuizId'] = $quizId;
		$fields['QuestionVersionId'] = 0;
		$fields['QuestionCategoryId'] = $questionCategoryId;
		$fields['QuestionIndex'] = $questionIndex;
		$fields['QuestionVersion'] = $question->QuestionVersion->toArray();
		$fields['QuestionVersion']['QuestionId'] = 0;
		$fields['QuestionVersion']['QuestionVersionId'] = 0;
		$fields['QuestionVersion']['Files'] = $question->QuestionVersion->getSimpleFiles();
		if (!$this->bind($fields))
			return false;

		return true;
	}

	function bind($from, $ignore = array())
	{
		$ignore[] = 'QuestionVersion';

		if (parent::bind($from, $ignore) === false)
			return false;

		if ($this->QuestionVersion->bind(AriUtils::getParam($from, 'QuestionVersion', array()), $ignore) === false)
			return false;

		$this->QuestionVersion->QuestionCategoryId = $this->QuestionCategoryId;
		$this->QuestionVersion->QuestionTypeId = $this->QuestionTypeId;
		$this->QuestionVersion->QuestionId = $this->QuestionId;
		$this->QuestionVersion->BankQuestionId = $this->BankQuestionId;

		return true;
	}

	function store($updateNulls = false)
	{
		if ($this->isNew() && $this->QuestionIndex < 1)
		{
			$maxIndex = $this->getMaxQuestionIndex();
			$this->QuestionIndex = $maxIndex + 1;
		}

		if ($this->isNew() && parent::store($updateNulls) === false)
			return false;

		$questionVersion =& $this->QuestionVersion;
		$questionVersion->QuestionVersionId = null;
		$questionVersion->QuestionId = $this->QuestionId;
		if ($questionVersion->store($updateNulls) === false)
			return false;

		$this->QuestionVersionId = $questionVersion->QuestionVersionId;
		$this->QuestionCategoryId = $questionVersion->QuestionCategoryId;
		$this->QuestionTypeId = $questionVersion->QuestionTypeId;

		return parent::store($updateNulls);
	}
	
	function update($fields)
	{
		$this->bind($fields);
		
		if (isset($fields['Score']))
			$this->QuestionVersion->Score = floatval($fields['Score']);
			
		if (isset($fields['Penalty']))
			$this->QuestionVersion->Penalty = floatval($fields['Penalty']);

		//if (isset($fields['QuestionCategoryId']))
		//	$this->QuestionCategoryId = $this->QuestionVersion->QuestionCategoryId = intval($fields['QuestionCategoryId'], 10);

		$this->QuestionVersion->Files = $this->QuestionVersion->getSimpleFiles();

		return $this->store();		
	}
	
	function &getQuestionType()
	{
		$qv =& $this->QuestionVersion;

		if (empty($qv->QuestionType->QuestionTypeId))
			$qv->QuestionType->load($this->QuestionTypeId);

		return $qv->QuestionType;
	}
	
	function getMaxQuestionIndex()
	{		
		$db =& $this->getDBO();	
		$db->setQuery(
			sprintf(
				'SELECT QuestionIndex FROM #__ariquizquestion QQ WHERE QQ.QuizId = %d ORDER BY QQ.QuestionIndex DESC LIMIT 0,1',
				$this->QuizId
			)
		);
		$index = $db->loadResult();
		if (is_null($index))
		{
			return -1;
		}
		
		if (is_null($index)) 
			$index = -1;
		
		return $index;
	}
}