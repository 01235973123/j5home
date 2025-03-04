<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Joomla.Models.Model');

class AriQuizStorage extends JObject
{
	var $TicketId;
	var $_user;
	var $_storage;
	var $_model;
	var $_lastAccessError = ARIQUIZ_TAKEQUIZERROR_NONE; 
	
	function __construct(&$model, $quizId, $ticketId, $user, $init = true)
	{
		$this->_model = $model;
		$this->_user = $user;
		$this->TicketId = $ticketId;
		
		if ($init) $this->init($quizId);
	}
	
	function &getModel()
	{
		return $this->_model;
	}

	function init($quizId, $reload = false)
	{
		static $isLoaded;

		if (!$reload && $isLoaded) return ;

		$key = $this->getKey();
		if ($reload || empty($_SESSION[$key]))
		{
			$storage = $this->createStorage($quizId);
			
			$_SESSION[$key] =& $storage;
		}

		$this->_storage =& $_SESSION[$key];
		
		if (!is_null($this->_storage))
			$this->_storage->LoadedTime = time();

		$isLoaded = true;
	}

	function createStorage($quizId)
	{
		$storage = null;
		$model =& $this->getModel();

		$sessionInfo = $model->getStatisticsInfoByTicketId( 
			$this->TicketId,
			$this->_user->get('id'),
			null,
			$quizId);

		if (empty($sessionInfo))
			return $storage;

		$quizModel =& AriModel::getInstance('Quiz', $model->getFullPrefix());

		$sid = $sessionInfo->StatisticsInfoId;
		$quiz = $quizModel->getQuiz($quizId);

		$storage = new stdClass();
		$storage->SessionUserId = $sessionInfo->UserId;
		$storage->CreatedTime = time();
		$storage->LoadedTime = null;
		$storage->QuizId = $quiz->QuizId;
		$storage->CanBack = (bool)$quiz->getParam('CanBack');
		$storage->CanSkip = (bool)$quiz->getParam('CanSkip');
		$storage->CanStop = (bool)$quiz->getParam('CanStop');
		$storage->CanTerminate = (bool)$quiz->getParam('CanTerminate');
		$storage->UseCalculator = (bool)$quiz->getParam('UseCalculator');
		$storage->ShowCorrectAnswer = (bool)$quiz->getParam('ShowCorrectAnswer');
		$storage->ShowExplanation = (bool)$quiz->getParam('ShowExplanation');
		$storage->ParsePluginTag = (bool)$quiz->getParam('ParsePluginTag');
		$storage->NoPenaltyForEmptyAnswer = (bool)$quiz->getParam('NoPenaltyForEmptyAnswer');
        $storage->ShowPaging = (bool)$quiz->getParam('ShowPaging');
		$storage->IsAvailable = null;
		$storage->IsStartDateSet = false;
		$storage->StatisticsInfoId = $sid;
		$storage->Files = $model->getFiles($sid);

		return $storage;
	}

	function clear()
	{
		$key = $this->getKey();
		if (isset($_SESSION[$key]))
		{
			$this->_storage = null;
			$_SESSION[$key] = null;
			unset($_SESSION[$key]);
		}
	}

	function isAvailable($checkPaused = true)
	{
		$this->setLastAccessError(ARIQUIZ_TAKEQUIZERROR_NONE);
		$isAvailable = $this->get('IsAvailable');
		
		$currentUserId = $this->_user->get('id');
		$originalUserId = $this->get('SessionUserId');

		if ($currentUserId != $originalUserId)
		{
			$this->setLastAccessError(ARIQUIZ_TAKEQUIZERROR_ANOTHERUSER);
			return false;
		}

		if (is_null($isAvailable))
		{
			$sid = $this->get('StatisticsInfoId');
			if (empty($sid))
			{
				$isAvailable = false;
				$this->setLastAccessError(ARIQUIZ_TAKEQUIZERROR_UNKNOWNERROR);
			}
			else
			{
				$model =& $this->getModel();
				$result = $model->canTakeQuiz2(
					$this->get('QuizId'), 
					$this->_user, 
					$checkPaused);
				
				$this->setLastAccessError($result);
				$isAvailable = ($result == ARIQUIZ_TAKEQUIZERROR_NONE);

				// If it is not possible to get a quiz now, it can be possible to get it later. 
				// For example if a lag time is expired and etc. so we cache IsAvailable value only when it is true.
				if ($isAvailable)
					$this->set('IsAvailable', $isAvailable);
			}
		}

		return $isAvailable;
	}

	function getTicketId()
	{
		return $this->TicketId;
	}

	function get($propName, $defValue = null)
	{
		return (isset($this->_storage->$propName))
			? $this->_storage->$propName
			: $defValue;
	}
	
	function set($propName, $value = null)
	{
		if (!is_null($this->_storage))
			$this->_storage->$propName = $value;
	}
	
	function getKey()
	{
		return 'aq_' . $this->TicketId;
	}
	
	function getFile($questionId, $alias)
	{
		$file = null;
		$files = $this->get('Files');
		if (isset($files[$questionId][$alias]))
			$file = $files[$questionId][$alias];
			
		return $file;
	}
	
	function setLastAccessError($error)
	{
		$this->_lastAccessError = $error;
	}
	
	function getLastAccessError()
	{
		return $this->_lastAccessError;
	}
}