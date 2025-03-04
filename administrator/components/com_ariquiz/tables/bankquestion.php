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

class AriQuizTableBankquestion extends AriQuizTableQuestion
{
	function bind($from, $ignore=array())
	{
		$res = parent::bind($from, $ignore);

		$this->QuizId = 0;
		$this->BankQuestionId = 0;

		return $res;
	}
	
	function store($updateNulls = null)
	{
		$this->QuizId = 0;
		$this->BankQuestionId = 0;

		return parent::store($updateNulls);
	}

	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.bankquestion.'. (int)$this->$key;        
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
			$assetParent->loadByName('com_ariquiz.bankcategory.' . (int)$this->QuestionCategoryId);                
 		else
 			$assetParent->loadByName('com_ariquiz');

		if ($assetParent->id)
			$assetParentId = $assetParent->id;

		return $assetParentId;
	}
}