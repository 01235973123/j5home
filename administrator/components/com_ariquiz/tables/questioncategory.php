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

define('ARIQUIZ_QUESTIONCATEGORY_STATUS_ACTIVE', 1);
define('ARIQUIZ_QUESTIONCATEGORY_STATUS_INACTIVE', 2);
define('ARIQUIZ_QUESTIONCATEGORY_STATUS_DELETE', 4);

require_once dirname(__FILE__) . DS . 'quizquestionpool.php';

class AriQuizTableQuestionCategory extends AriTable
{
	var $QuestionCategoryId;
	var $CategoryName;
	var $Description = '';
	var $CreatedBy;
	var $Created;
	var $ModifiedBy = 0;
	var $Modified = null;
	var $QuizId;
	var $QuestionCount = 0;
	var $QuestionTime = 0;
	var $RandomQuestion = 0;
	var $Status;
	var $Quiz;
	var $QuestionPool = array();

	var $asset_id = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizquestioncategory', 'QuestionCategoryId', $db);
		
		$this->Quiz = new stdClass();
		$this->addRelation('QuestionCategoryId', 'QuestionPool', ARI_TABLE_RELATION_ONETOMANY, 'AriQuizTableQuizquestionpool', 'QuestionCategoryId');
	}
	
	function copyFrom($category, $destQuizId, $userId = 0, $created = null)
	{
		if (empty($category))
			return false;
		
		if (is_null($created))
			$created = AriDateUtility::getDbUtcDate();

		$questionPool = array();
		if (is_array($category->QuestionPool))
		{
			foreach ($category->QuestionPool as $questionPoolItem)
				if ($questionPoolItem->BankCategoryId > 0)
					$questionPool[] = array(
						'BankCategoryId' => $questionPoolItem->BankCategoryId,
						'QuestionCount' => $questionPoolItem->QuestionCount
					);
		}
			
		$catFields = $category->toArray();
		if (!is_array($catFields))
			$catFields = array();
			
		$catFields['QuestionPool'] = $questionPool;
			
		if (!$this->bind($catFields))
			return false;

		$this->QuestionCategoryId = 0;
		$this->QuizId = $destQuizId;
		$this->Created = $created;
		if (!empty($userId))
			$this->CreatedBy = $userId;
		$this->Modified = null;
		$this->ModifiedBy = null;

		return true;
	}

	function bind($from, $ignore = array())
	{
		$ignore[] = 'QuestionPool';

		if (parent::bind($from, $ignore) === false)
			return false;

		$questionPool = AriUtils::getParam($from, 'QuestionPool', array());
		if (!is_array($questionPool))
			$questionPool = array($questionPool);

		$this->QuestionPool = array();
		foreach ($questionPool as $questionPoolItem)
		{
			$item = new AriQuizTableQuizquestionpool($this->getDBO());
			if ($item->bind($questionPoolItem) !== false)
			{
				$item->QuizId = $this->QuizId;
				$item->QuestionCategoryId = $this->QuestionCategoryId;
			}
			
			$this->QuestionPool[] = $item;
		}

		return true;
	}

	function store($updateNulls = null)
	{	
		if (!$this->isNew())
		{
			$db =& $this->getDBO();
			$db->setQuery(
				sprintf('DELETE FROM #__ariquiz_quiz_questionpool WHERE QuestionCategoryId = %d',
					$this->QuestionCategoryId)
			);
			if (!$db->execute())
				return false;
		}

		if (empty($this->QuestionTime)) {
			$this->QuestionTime = 0;
		}
		
		if (empty($this->QuestionCount)) {
			$this->QuestionCount = 0;
		}
		
		if (parent::store($updateNulls) === false)
			return false;

		$questionPool =& $this->QuestionPool;
		foreach ($questionPool as $questionPoolItem)
		{
			$questionPoolItem->QuizId = $this->QuizId;
			$questionPoolItem->QuestionCategoryId = $this->QuestionCategoryId;
			$questionPoolItem->store($updateNulls);
		}

		return true;
	}

	protected function _getAssetName()
	{
		$key = $this->_tbl_key;
		
		return 'com_ariquiz.questioncategory.'. (int)$this->$key;        
	}

 	protected function _getAssetTitle()
 	{
 		return $this->CategoryName;
 	}

 	protected function _getAssetParentId($table = null, $id = null)
 	{                
 		$assetParent = JTable::getInstance('Asset');

 		$assetParentId = $assetParent->getRootId();                
		if (!empty($this->QuizId))
			$assetParent->loadByName('com_ariquiz.quiz.' . (int)$this->QuizId);                
 		else
 			$assetParent->loadByName('com_ariquiz');

		if ($assetParent->id)
			$assetParentId = $assetParent->id;

		return $assetParentId;
	}
}