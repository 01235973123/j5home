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

AriKernel::import('Application.ARIQuiz.Questions.QuestionFactory');
AriKernel::import('Joomla.Controllers.Controller');
AriKernel::import('Joomla.Event.EventController');
AriKernel::import('Utils.DateUtility');

class AriQuizControllerQuestion extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function reload()
	{
		$this->display();
	}

	function display()
	{
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable())
		{
			$accessErrorCode = $quizStorage->getLastAccessError();
			$accessError = AriQuizHelper::getQuizAccessError($accessErrorCode);
			$quizStorage->clear();
			$this->redirect(
				JRoute::_('index.php?option=com_ariquiz&view=message&msg=' . $accessError, false)
			);
			exit();
		}
		
		$userQuizModel = $this->getModel('UserQuiz');
		
		$sid = $quizStorage->get('StatisticsInfoId');
		$ticketId = $quizStorage->getTicketId();
		$statistics = $userQuizModel->getNextPage($sid);
		if (empty($statistics) || empty($statistics->PageId) || empty($statistics->Questions))
		{
			$quizStorage->clear();
			if ($userQuizModel->isQuizFinishedByTicketId($ticketId))
			{
				$itemId = JRequest::getInt('Itemid');
				$this->redirect(
					JRoute::_('index.php?option=com_ariquiz&view=quizcomplete&ticketId=' . $ticketId . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
				);
				exit();
			}
			else 
			{
				$this->redirect(
					JRoute::_('index.php?option=com_ariquiz&view=message&msg=COM_ARIQUIZ_ACCESSERROR_UNKNOWN', false)
				);
				exit();
			}
		}

		$quizInfo = $userQuizModel->getQuizInfo($sid);
		$quizInfo->CompletedPageCount = $userQuizModel->getUserCompletedPages($sid);
		if ($quizInfo->TotalTime)
		{
			$totalTime = $quizInfo->TotalTime;
			if ($quizInfo->StartDate)
			{
				$quizInfo->TotalTime = 
					$quizInfo->TotalTime 
					- 
					$quizInfo->Now 
					- 
					$quizInfo->UsedTime 
					+ 
					$quizInfo->StartDate;
			}

			--$quizInfo->TotalTime;
		}
		else
			$quizInfo->TotalTime = null;
			
		if (empty($quizInfo->ExtraParams))
			$quizInfo->ExtraParams = new stdClass();
			
		$showFileUrl = JURI::root(true) . '/index.php?option=com_ariquiz&view=question&task=showFile&questionId=#{questionId}&alias=#{alias}&quizId=' . $quizInfo->QuizId . '&ticketId=' . $ticketId;
		
		if (is_array($quizInfo->ExtraParams))
			$quizInfo->ExtraParams['ShowFileUrl'] = $showFileUrl;
		else
		{
			$quizInfo->ExtraParams->ShowFileUrl = $showFileUrl;
		}

		$questions = null;
		if ($quizStorage->get('ParsePluginTag'))
			$questions = $userQuizModel->getQuizQuestions($sid);

		$view =& $this->getView();
		$view->display($quizStorage, $quizInfo, $questions);
	}
	
	function _getQuizStorage()
	{
		$userQuizModel = $this->getModel('UserQuiz');

		$ticketId = JRequest::getString('ticketId');
		$quizId = JRequest::getInt('quizId');
		$user =& JFactory::getUser();

		return $userQuizModel->getQuizStorage($quizId, $ticketId, $user);
	}

	function _stopQuiz()
	{
		$result = false;
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('CanStop') || $userId < 1)
			return $result;
		
		$ticketId = $quizStorage->getTicketId();		
		$userQuizModel = $this->getModel('UserQuiz');
		$result = $userQuizModel->stopQuiz($quizStorage->get('StatisticsInfoId'), $userId);
		if ($result)
		{
			$quizStorage->clear();
			AriEventController::raiseEvent(
				'onStopQuiz', 
				array(
					'QuizId' => $quizStorage->get('QuizId'), 
					'TicketId' => $ticketId, 
					'UserId' => $userId
				)
			);
		}

		return $result;
	}
	
	function stopExit()
	{
		$result = $this->_stopQuiz();
		$msg = $result ? 'COM_ARIQUIZ_LABEL_QUIZSTOPPED' : 'COM_ARIQUIZ_LABEL_UNKNOWERROR';
		$itemId = JRequest::getInt('Itemid');

		$this->redirect(
			JRoute::_('index.php?option=com_ariquiz&view=message&msg=' . $msg . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
		);
		exit();
	}
	
	function _terminateQuizSession()
	{
		$result = false;
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('CanTerminate'))
			return $result;

		$ticketId = $quizStorage->getTicketId();		
		$userQuizModel = $this->getModel('UserQuiz');
		$result = $userQuizModel->terminateQuiz($quizStorage->get('StatisticsInfoId'), $userId);
		if ($result)
		{
			$quizStorage->clear();
			AriEventController::raiseEvent(
				'onTerminateQuiz', 
				array(
					'QuizId' => $quizStorage->get('QuizId'), 
					'TicketId' => $ticketId, 
					'UserId' => $userId
				)
			);
		}

		return $result;
	}
	
	function terminate()
	{
		$result = $this->_terminateQuizSession();
		$msg = $result ? 'COM_ARIQUIZ_LABEL_QUIZTERMINATED' : 'COM_ARIQUIZ_LABEL_UNKNOWERROR';
		$itemId = JRequest::getInt('Itemid');

		$this->redirect(
			JRoute::_('index.php?option=com_ariquiz&view=message&msg=' . $msg . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
		);
		exit();
	}

	function ajaxGetPage()
	{
		$ret = null;
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable())
			return $ret;

		$sid = $quizStorage->get('StatisticsInfoId');
		$ticketId = $quizStorage->getTicketId();
		$parseTag = $quizStorage->get('ParsePluginTag');
		$user = JFactory::getUser();
		$userId = $user->get('id');

		$userQuizModel = $this->getModel('UserQuiz');
		$page = $userQuizModel->getNextPage($sid, $userId);
		if (empty($page->PageId))
			return $ret;

		$isNewPage = empty($page->StartDate);
			
		$ret = new stdClass();
		$ret->pageId = $page->PageId;
		$ret->pageNumber = $page->PageNumber;
		$ret->description = $page->Description;
		$ret->questions = array();
		
		$pageTime = null; 
		if (!empty($page->PageTime))
		{
			$pageTime = !empty($page->StartDate)
				? strtotime($page->StartDate) + $page->PageTime - strtotime(AriDateUtility::getDbUtcDate()) - $page->UsedTime 
				: $page->PageTime - $page->UsedTime;
			--$pageTime;
		}
		$ret->pageTime = $pageTime;

		foreach ($page->Questions as $question)
		{
			$questionVersion = $question->getBaseQuestionVersion();
			$questionEntity = AriQuizQuestionFactory::getQuestion($questionVersion->QuestionType->ClassName);
			$questionData = null;
			
			if ($question->InitData)
			{
				$questionData = $questionEntity->applyUserData(@unserialize($question->InitData), $question->Data);
			}
			else
			{
				$questionData = $questionEntity->getClientDataFromXml($questionVersion->Data, $question->Data);
				
				if ($isNewPage)
					$question->InitData = @serialize($questionData);
			}

			AriKernel::import('Joomla.Plugins.PluginProcessHelper');

			$retQuestion = new stdClass();
			$retQuestion->hasCorrectAnswer = $questionEntity->hasCorrectAnswer();
			$retQuestion->questionData = $questionData;
			$retQuestion->questionId = $question->QuestionId;
			$retQuestion->questionText = $parseTag 
				? AriPluginProcessHelper::processTags($questionVersion->Question, true, array('scripts', 'custom'))
				: $questionVersion->Question;
			$retQuestion->questionType = $questionVersion->QuestionType->ClassName;
			$retQuestion->questionIndex = $question->QuestionIndex;
			$retQuestion->completed = (bool)$question->Completed;
			$ret->questions[] = $retQuestion;
		}

		$quizId = $quizStorage->get('QuizId');
		if ($isNewPage)
		{
			AriKernel::import('Web.Request');

			$startDate = AriDateUtility::getDbUtcDate();
			$page->IpAddress = AriRequest::getIP();
			$page->StartDate = $startDate;

			$userQuizModel->updateNewQuestionPage($page);					
			if (!$quizStorage->get('IsStartDateSet'))
			{
				$userQuizModel->setSafeQuizStartDate($sid, $startDate);
				$quizStorage->set('IsStartDateSet', true);
			}
		}

		AriEventController::raiseEvent(
			'onLoadPage', 
			array(
				'Page' => $ret, 
				'QuizId' => $quizId, 
				'TicketId' => $ticketId, 
				'UserId' => $userId
			)
		);

		return $ret;
	}
	
	function ajaxNextPage()
	{
		$retResult = false;
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('CanSkip'))
			return $retResult;

		$pageId = JRequest::getInt('pageId');
		$sid = $quizStorage->get('StatisticsInfoId');
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		$userQuizModel = $this->getModel('UserQuiz');
		$page = $userQuizModel->getCurrentPage($sid, $userId);
		if (empty($page->PageId) || $page->PageId != $pageId)
			return $retResult;

		foreach ($page->Questions as $question)
		{
			$questionVersion = $question->getBaseQuestionVersion();
			$questionEntity = AriQuizQuestionFactory::getQuestion($questionVersion->QuestionType->ClassName);
			$data = $questionEntity->getFrontXml($question->QuestionId);
			$question->Data = $data;
		}

		$skipDate = AriDateUtility::getDbUtcDate();
		if ($userQuizModel->nextPage($page, $skipDate))
		{	
			AriEventController::raiseEvent(
				'onNextPage', 
				array(
					'QuizId' => $quizStorage->get('QuizId'), 
					'TicketId' => $quizStorage->getTicketId(), 
					'UserId' => $userId,
					'Page' => $page 
				)
			);

			$retResult = true;				
		}

		return $retResult;			
	}
	
	function ajaxPrevPage()
	{
		$retResult = false;
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('CanBack'))
			return $retResult;

		$pageId = JRequest::getInt('pageId');
		$sid = $quizStorage->get('StatisticsInfoId');
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		$userQuizModel = $this->getModel('UserQuiz');
		$page = $userQuizModel->getCurrentPage($sid, $userId);
		if (empty($page->PageId) || $page->PageId != $pageId)
			return $retResult;
			
		if ($page->PageNumber == 0)
			return true;

		foreach ($page->Questions as $question)
		{
			$questionVersion = $question->getBaseQuestionVersion();
			$questionEntity = AriQuizQuestionFactory::getQuestion($questionVersion->QuestionType->ClassName);
			$data = $questionEntity->getFrontXml($question->QuestionId);
			$question->Data = $data;
		}

		$skipDate = AriDateUtility::getDbUtcDate();
		if ($userQuizModel->prevPage($page, $skipDate))
		{	
			AriEventController::raiseEvent(
				'onPrevPage', 
				array(
					'QuizId' => $quizStorage->get('QuizId'), 
					'TicketId' => $quizStorage->getTicketId(), 
					'UserId' => $userId,
					'Page' => $page 
				)
			);

			$retResult = true;				
		}

		return $retResult;
	}

	function ajaxSavePage()
	{
		$retResult = array(
			'result' => false, 
			'moveToNext' => false, 
			'showExplanation' => false, 
			'tryAgain' => false,
			'questionId' => 0
		);
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable())
			return $retResult;

		$pageId = JRequest::getInt('pageId');
		$sid = $quizStorage->get('StatisticsInfoId');
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$timeOver = JRequest::getBool('timeOver');
		$userQuizModel = $this->getModel('UserQuiz');
		$statistics = $timeOver
			? $userQuizModel->getPage($pageId, $sid)
			: $userQuizModel->getCurrentPage($sid, $userId);

		if (empty($statistics) || $statistics->PageId != $pageId || ($timeOver && $statistics->EndDate))
				return $retResult;

		$endDate = AriDateUtility::getDbUtcDate();
		$showExplanation = false;
		$attempts = array();
		foreach ($statistics->Questions as $question)
		{
			$questionVersion = $question->Question->QuestionVersion;
			$questionEntity = AriQuizQuestionFactory::getQuestion($questionVersion->QuestionType->ClassName);
			
			if ($questionEntity->hasCorrectAnswer())
				$showExplanation = true;

			if ($question->Completed)
				continue ;

			$baseQuestionVersion = $question->getBaseQuestionVersion();
			$maxScore = $questionVersion->Score != 0 ? $questionVersion->Score : $baseQuestionVersion->Score;
			$penalty = $questionVersion->Penalty != 0 ? $questionVersion->Penalty : $baseQuestionVersion->Penalty;
			$data = !$timeOver 
				? $questionEntity->getFrontXml($question->QuestionId)
				: null;
			$score = $questionEntity->getScore(
				$data, 
				$baseQuestionVersion->Data, 
				$maxScore,
				$penalty, 
				$question->getOverrideData(),
				$quizStorage->get('NoPenaltyForEmptyAnswer')
			);

			$updateQuestion = true;
			++$question->AttemptCount;
			if (!$timeOver && $baseQuestionVersion->OnlyCorrectAnswer)
			{
				$isCorrect = $questionEntity->isCorrect($data, $baseQuestionVersion->Data, $question->getOverrideData());
				if ($score != $maxScore && ($baseQuestionVersion->AttemptCount == 0 || $question->AttemptCount < $baseQuestionVersion->AttemptCount))
				{
					$attempts[$question->StatisticsId] = $data; 

					if (!$retResult['tryAgain'])
					{
						$retResult['questionId'] = $question->QuestionId;
						$retResult['tryAgain'] = true;
					}

					$retResult['result'] = true;
					
					$updateQuestion = false;
				}
			}

			if ($updateQuestion)
			{
				$question->EndDate = $endDate;
				$question->Data = $data;
				$question->Score = $score;
			}
		}

		if (!$retResult['tryAgain'])
			$statistics->EndDate = $endDate;

		$userQuizModel->completePage($statistics, $attempts);
		$retResult['result'] = true;

		if (!$retResult['tryAgain'])
		{
			$retResult['moveToNext'] = true;
			$retResult['showExplanation'] = $showExplanation && $quizStorage->get('ShowExplanation');
		}

		return $retResult;
	}
	
	function ajaxGetCorrectAnswer()
	{
		$retResult = null;
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('ShowCorrectAnswer'))
			return $retResult;

		$userQuizModel = $this->getModel('UserQuiz');
		$sid = $quizStorage->get('StatisticsInfoId');
		$questionId = JRequest::getInt('qid');
		$user = JFactory::getUser();
		$userId = $user->get('id');

		$statistics = $userQuizModel->getCurrentPage($sid, $userId);
		if (empty($statistics) || !$statistics->containsQuestion($questionId))
			return $retResult;
			
		$question = $statistics->getQuestion($questionId);

		$questionVersion = $question->Question->QuestionVersion;
		$baseQuestionVersion = $question->getBaseQuestionVersion();
		$maxScore = $questionVersion->Score != 0 ? $questionVersion->Score : $baseQuestionVersion->Score;
		$penalty = $questionVersion->Penalty != 0 ? $questionVersion->Penalty : $baseQuestionVersion->Penalty;

		$className = $baseQuestionVersion->QuestionType->ClassName;
		$questionEntity = AriQuizQuestionFactory::getQuestion($className);
		
		if (!$questionEntity->hasCorrectAnswer())
			return $retResult;
		
		$frontXml = $questionEntity->getFrontXml($question->QuestionId);
				
		$retResult = new stdClass();
		$retResult->QuestionId = $question->QuestionId;
		$retResult->TicketId = $quizStorage->getTicketId();
		$retResult->QuestionClassName = $className;
		$retResult->QuestionData = $questionEntity->getDataFromXml($baseQuestionVersion->Data, false);
		$retResult->UserData = $questionEntity->getDataFromXml($frontXml);
		$retResult->MaxScore = $maxScore;
		$retResult->UserScore = $questionEntity->getScore($frontXml, $baseQuestionVersion->Data, $maxScore, $penalty, null, $quizStorage->get('NoPenaltyForEmptyAnswer'));
		$retResult->IsCorrect = $questionEntity->isCorrect($frontXml, $baseQuestionVersion->Data);
		$retResult->Note = $baseQuestionVersion->Note;
			
		return $retResult;
	}
	
	function ajaxGetExplanation()
	{
		$retResult = array();
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable() || !$quizStorage->get('ShowExplanation'))
			return $retResult;
			
		$userQuizModel = $this->getModel('UserQuiz');
		$sid = $quizStorage->get('StatisticsInfoId');
		$pageId = JRequest::getInt('pageId');
		$user =& JFactory::getUser();
		$userId = $user->get('id');

		$page = $userQuizModel->getPage($pageId, $sid);
		if (empty($page))
			return $retResult;

		foreach ($page->Questions as $pageQuestion)
		{
			if (!$pageQuestion->Completed)
				continue ;

			$questionVersion = $pageQuestion->getBaseQuestionVersion();
			$className = $questionVersion->QuestionType->ClassName;
			$questionEntity = AriQuizQuestionFactory::getQuestion($className);

			$question = new stdClass();
			$question->QuestionId = $pageQuestion->QuestionId;
			$question->TicketId = $quizStorage->getTicketId();
			$question->QuestionClassName = $className;
			$question->QuestionData = $questionEntity->getDataFromXml($questionVersion->Data, false);
			$question->UserData = $questionEntity->getDataFromXml($pageQuestion->Data);
			$question->ClientData = $questionEntity->getClientDataFromXml($questionVersion->Data, $pageQuestion->Data);
			$question->MaxScore = $questionVersion->Score;
			$question->UserScore = $pageQuestion->Score;
			$question->IsCorrect = $questionEntity->isCorrect($pageQuestion->Data, $questionVersion->Data);
			$question->Note = $questionVersion->Note;
			$question->HasCorrectAnswer = $questionEntity->hasCorrectAnswer();

			$retResult[$question->QuestionId] = $question;			
		}

		return $retResult;
	}
	
	function ajaxStopQuiz()
	{
		return $this->_stopQuiz();
	}
	
	function ajaxResumeQuiz()
	{
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable())
			return false;

		$user =& JFactory::getUser();
		$userQuizModel = $this->getModel('UserQuiz');

		return $userQuizModel->resumeQuizByTicketId($quizStorage->getTicketId(), $user->get('id'));
	}
	
	function showFile()
	{
		$quizStorage = $this->_getQuizStorage();
		if (!$quizStorage->isAvailable())
			exit();

		$questionId = JRequest::getInt('questionId');
		$alias = JRequest::getString('alias');	
		$file = $quizStorage->getFile($questionId, $alias);
		if (empty($file))
		{
			header("HTTP/1.0 404 Not Found");
			exit();
		}

		$dir = AriQuizHelper::getFilesDir($file['Group']);
		$foldersModel = $this->getModel(
			'Folders', 
			'',
			array(
				'rootDir' => $dir, 
				'group' => $file['Group']
			)
		);
		$path = $foldersModel->getSimplePath($foldersModel->getPath($file['Folder']));
		$filePath = $dir . DS . join(DS, $path) . DS . $file['FileName'];

		$handle = fopen($filePath, "rb");			 
		$content = fread($handle, filesize($filePath));
		fclose($handle);

		if (!empty($file['MimeType']))
			header('Content-type: ' . $file['MimeType']);

		while (@ob_end_clean());
		echo $content;
		exit();
	}
}