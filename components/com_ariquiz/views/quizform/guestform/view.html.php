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

AriKernel::import('Joomla.Views.SubView');
AriKernel::import('Joomla.Form.Form');
AriKernel::import('Utils.Utils');

class AriQuizSubViewQuizFormGuestForm extends AriSubView 
{
	function display($quiz, $tpl = null)
	{
		$userQuizModel = AriModel::getInstance('Userquiz', 'AriQuizModel');
		$ticketId = $userQuizModel->getGuestTicketId($quiz->QuizId);
		$data = array('UserName' => '', 'Email' => '');
		$readOnly = false;
		if (!empty($ticketId))
		{
			$userQuiz = $userQuizModel->getStatisticsInfoByTicketId($ticketId, 0, array(ARIQUIZ_USERQUIZ_STATUS_PROCESS, ARIQUIZ_USERQUIZ_STATUS_PREPARE), $quiz->QuizId);
			if (!is_null($userQuiz))
			{
				$extraData = $userQuiz->parseExtraDataXml($userQuiz->ExtraData);
				$data['UserName'] = AriUtils::getParam($extraData, 'UserName');
				$data['Email'] = AriUtils::getParam($extraData, 'Email');
				
				foreach ($data as $key => $value)
				{
					if (empty($value))
						$data[$key] = ' ';
				}
				
				foreach ($extraData as $key => $value)
					if (!isset($data[$key]))
						$data[$key] = $value;
						
				$readOnly = true;
			}
		}
		else
		{
			$data['Email'] = AriUtils::getParam($_COOKIE, 'aq_email', '');
			$data['UserName'] = AriUtils::getParam($_COOKIE, 'aq_name', '');			
		}
		
		$form = new AriForm('common');
		$form->load(AriQuizHelper::getFrontendFormPath('quizform', 'guestform'));
		$form->bind($data, $readOnly ? 'readonly' : '_default');
		
		if (!$readOnly)
			if ($quiz->Anonymous == 'No')
				$form->registerValidators('extraData');
			else if ($quiz->Anonymous == 'ByUser')
				$form->registerValidators('extraData', 'simple');

		$this->assign('readOnly', $readOnly);
		$this->assignRef('form', $form);
		
		parent::display($tpl);
	}
}