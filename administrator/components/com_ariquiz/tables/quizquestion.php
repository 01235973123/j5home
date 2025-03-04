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

require_once dirname(__FILE__) . DS . 'question.php';
require_once dirname(__FILE__) . DS . 'bankquestion.php';

class AriQuizTableQuizquestion extends AriQuizTableQuestion
{
	var $BankQuestion;
	
	function __construct(&$db) 
	{
		parent::__construct($db);

		$this->BankQuestion = new AriQuizTableBankquestion($db);
		
		$this->addRelation('BankQuestionId', 'BankQuestion');
	}
	
	function load($oid = null, $reset = true)
	{
		if (parent::load($oid, $reset) === false)
			return false;

		if ($this->BankQuestionId)
			$this->QuestionTypeId = $this->QuestionVersion->QuestionTypeId = $this->BankQuestion->QuestionTypeId;

		return true;
	}
	
	function &getQuestionType()
	{
		$qv = null;
		if ($this->isBasedOnBankQuestion())
			$qv =& $this->BankQuestion->QuestionVersion;
		else
			$qv =& $this->QuestionVersion;
			
		if (empty($qv->QuestionType->QuestionTypeId))
			$qv->QuestionType->load($this->QuestionTypeId);
			
		return $qv->QuestionType;
	}
	
	function isBasedOnBankQuestion()
	{
		return $this->BankQuestionId > 0;
	}
	
	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.quizquestion.'. (int)$this->$key;        
	}

 	protected function _getAssetTitle()
 	{
 		return $this->QuestionVersion->Question;
 	}

 	protected function _getAssetParentId($table = null, $id = null)
 	{                
 		$assetParent = JTable::getInstance('Asset');

 		$assetParentId = $assetParent->getRootId();                
		if (!empty($this->QuestionCategoryId))
			$assetParent->loadByName('com_ariquiz.questioncategory.' . (int)$this->QuestionCategoryId);                
 		else
 			$assetParent->loadByName('com_ariquiz.quiz.' . (int)$this->QuizId);

		if ($assetParent->id)
			$assetParentId = $assetParent->id;

		return $assetParentId;
	}
}