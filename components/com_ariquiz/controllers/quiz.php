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

AriKernel::import('Joomla.Controllers.Controller');
AriKernel::import('Joomla.Event.EventController');

class AriQuizControllerQuiz extends AriController 
{
	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}

	function display() 
	{
		$model =& $this->getModel('Quiz');
		$userQuizModel = $this->getModel('UserQuiz');

		$quizId = JRequest::getInt('quizId');
		$quiz = $model->getQuiz($quizId);
		if (empty($quiz))
		{
			$this->redirect('index.php?option=com_ariquiz&view=message&msg=COM_ARIQUIZ_ACCESSERROR_UNKNOWN');
			exit();
		}
		
		$user =& JFactory::getUser();

		$errorCode = $userQuizModel->canTakeQuiz2($quiz, $user, false);

		if ($errorCode == ARIQUIZ_TAKEQUIZERROR_NONE && $quiz->StartImmediately)
		{
			$this->takeQuiz();
			exit();
		}

		$formView = null;
		if (/*$user->get('id') == 0 && */$quiz->Anonymous != 'Yes')
		{
			$formView = $this->getSubView('guestform', 'quizform');
		}

		$view =& $this->getView();
		$view->display($quiz, $errorCode, $formView);
	}
	
	function takeQuiz()
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		$quizId = JRequest::getInt('quizId');
		$extraData = JRequest::getVar('extraData', array(), 'default', 'none', JREQUEST_ALLOWRAW);

		AriEventController::raiseEvent(
			'onBeforeStartQuiz', 
			array(
				'QuizId' => $quizId, 
				'ExtraData' => $extraData, 
				'UserId' => $userId
			)
		);
		
		$questionCount = -1;
		$ticketId = ($userId > 0)
			? $this->_takeMemberQuiz($userId, $quizId, $extraData, $questionCount)
			: $this->_takeGuestQuiz($quizId, $extraData, $questionCount);

		if (!empty($ticketId))
		{
			$itemId = JRequest::getInt('Itemid');
			$this->redirect(
				JRoute::_('index.php?option=com_ariquiz&view=question&quizId=' . $quizId . '&ticketId=' . $ticketId . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
			);
		}
		else 
		{
			$errorMsg = 'COM_ARIQUIZ_ACCESSERROR_UNKNOWN';
			if ($questionCount == 0)
				$errorMsg = 'COM_ARIQUIZ_ACCESSERROR_NOQUESTIONS';

			$this->redirect('index.php?option=com_ariquiz&view=message&msg=' . $errorMsg);
			exit();
		}
	}

	function _takeGuestQuiz($quizId, $extraData, &$rQuestionCount)
	{
		$userQuizModel = $this->getModel('UserQuiz');

		//check attempt count and lag time
		if (!empty($extraData['Email']))
		{
			$errorCode = $userQuizModel->canTakeQuizByGuest2($quizId, $extraData['Email']);
			if ($errorCode != ARIQUIZ_TAKEQUIZERROR_NONE)
			{
				$accessError = AriQuizHelper::getQuizAccessError($errorCode);
				$this->redirect('index.php?option=com_ariquiz&view=message&msg=' . $accessError);
				exit();
			}
		}

		$ticketId = $userQuizModel->getGuestTicketId($quizId);
		if (is_null($ticketId))
		{
			setcookie('aq_email', '', time() - 24 * 3600, '/');
			setcookie('aq_name', '', time() - 24 * 3600, '/');

			if (!empty($extraData['Email'])) 
				setcookie('aq_email', trim($extraData['Email']), time() + 365 * 24 * 3600, '/');
			if (!empty($extraData['UserName'])) 
				setcookie('aq_name', trim($extraData['UserName']), time() + 365 * 24 * 3600, '/');
			
			$user =& JFactory::getUser();
			$userId = $user->get('id');
			$ticketId = $userQuizModel->composeUserQuiz($quizId, $userId, $extraData, $rQuestionCount);
			if (!is_null($ticketId))
				$userQuizModel->saveGuestTicketId($ticketId, $quizId);
		}

		return $ticketId;
	}

	function _takeMemberQuiz($userId, $quizId, $extraData, &$rQuestionCount)
	{
		$ticketId = null;
		$userQuizModel = $this->getModel('UserQuiz');
		$data = $userQuizModel->getNotFinishedQuizInfo($quizId, $userId);
		if (!is_null($data))
		{
			$ticketId = $data['TicketId'];
			if ($data['Status'] == ARIQUIZ_USERQUIZ_STATUS_PAUSE)
				if (!$userQuizModel->resumeQuizByTicketId($ticketId, $userId))
					$ticketId = null;
		}
		else
		{
			$ticketId = $userQuizModel->composeUserQuiz($quizId, $userId, $extraData, $rQuestionCount);
		}

		return $ticketId;
	}
}