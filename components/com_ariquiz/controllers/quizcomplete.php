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
AriKernel::import('Utils.DateUtility');
AriKernel::import('Web.Controls.Data.MultiPageDataTable');
AriKernel::import('Data.DataFilter');
AriKernel::import('Joomla.Mail.Mailer');
AriKernel::import('Web.Response');

class AriQuizControllerQuizcomplete extends AriController 
{
	var $_isQuizFinished = null;
	var $_ticketId = null;
	var $_resultInfo = null;
	var $_templates = null;
	var $_certificateFilePath = null;
	var $_summaryByCategories = null;

	function __construct($config = array()) 
	{
		if (!array_key_exists('model_path', $config))
			$config['model_path'] = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_ariquiz' . DS . 'models';

		parent::__construct($config);
	}
	
	function __destruct()
	{
		if (!is_null($this->_certificateFilePath))
		{
			JFile::delete($this->_certificateFilePath);
			$this->_certificateFilePath = null;
		}
	}

	function display()
	{
		$userQuizModel = $this->getModel('UserQuiz');
		
		$ticketId = $this->_getTicketId();
		$isQuizFinished = $this->_isQuizFinished(true);

		$isQuizMarkAsFinished = $userQuizModel->markQuizAsFinished($ticketId);
		$resultInfo = $this->_getResultInfo();		
		if (empty($resultInfo))
		{
			$this->redirect('index.php?option=com_ariquiz&view=message&msg=COM_ARIQUIZ_ACCESSERROR_UNKNOWN');
			exit();
		}

		$this->sendResultToAdmin($resultInfo);

		if ($isQuizMarkAsFinished)
		{
			AriEventController::raiseEvent('onEndQuiz', $resultInfo);
			
			if ($resultInfo['AutoMailToUser'])
			{
				$isSend = $this->sendEmailToUser(false);
			}
		}
		
		$isPassed = !empty($resultInfo['_Passed']);
		$templateKey = $isPassed 
			? ARIQUIZ_TEXTTEMPLATE_SUCCESSFUL 
			: ARIQUIZ_TEXTTEMPLATE_FAILED;

		$emailVisible = !empty($resultInfo['Email']) && empty($resultInfo['AutoMailToUser']) && $this->_isVisibleCtrl($isPassed 
				? ARIQUIZ_TEXTTEMPLATE_EMAILSUCCESSFUL 
				: ARIQUIZ_TEXTTEMPLATE_EMAILFAILED);
		$printVisible = $this->_isVisibleCtrl($isPassed 
				? ARIQUIZ_TEXTTEMPLATE_PRINTSUCCESSFUL 
				: ARIQUIZ_TEXTTEMPLATE_PRINTFAILED);
		$certificateVisible = $this->_isVisibleCtrl($isPassed 
				? ARIQUIZ_TEXTTEMPLATE_CERTIFICATESUCCESSFUL 
				: ARIQUIZ_TEXTTEMPLATE_CERTIFICATEFAILED);

		$questions = null;
		$quizParams = $resultInfo['ExtraParams'];
		if (!empty($quizParams->ParsePluginTag))
			$questions = $userQuizModel->getQuizQuestions($resultInfo['StatisticsInfoId']);

		$view =& $this->getView();
		$view->display(
			array(
				'isDetailedResultsAvailable' => $this->isDetailedResultsAvailable(),
				'ticketId' => $ticketId,
				'resultInfo' => $resultInfo,
				'resultText' => $this->_getResultText($templateKey),
				'btnEmailVisible' => $emailVisible,
				'btnPrintVisible' => $printVisible,
				'btnCertificateVisible' => $certificateVisible,
				'questions' => $questions
			)
		);
	}

	function ajaxSendEmail()
	{
		if (!$this->_isQuizFinished())
			return false;

		$isSend = $this->sendEmailToUser();
		
		return $isSend;
	}

	function isDetailedResultsAvailable()
	{
		$resultInfo = $this->_getResultInfo();
		$fullStatistics = $resultInfo['FullStatistics'];
		$fullStatisticsOnSuccess = $resultInfo['FullStatisticsOnSuccess'];
		$fullStatisticsOnFail = $resultInfo['FullStatisticsOnFail'];

		if ($fullStatistics == 'Never' || 
			($fullStatistics == 'OnSuccess' && $fullStatisticsOnSuccess == 'None') ||
			($fullStatistics == 'OnFail' && $fullStatisticsOnFail == 'None')
			)
		{
			return false;
		}

		$isPassed = (bool)$resultInfo['_Passed'];
		if ($fullStatistics == 'Always' || 
			($fullStatistics == 'OnSuccess' && $isPassed) || 
			($fullStatistics == 'OnFail' && !$isPassed)
			)
		{
				return true;
		}

		if ($fullStatistics == 'OnLastAttempt')
		{
			$user =& JFactory::getUser();
			$userId = $user->get('id');
			if (!$userId)
				return true;
				
			$attemptCount = $resultInfo['AttemptCount'];
			if ($attemptCount < 1)
				return true;

			$quizId = $resultInfo['QuizId'];
			$resultModel = $this->getModel('Quizresult');
			$passedQuizCount = $resultModel->getPassedQuizCount($quizId, $userId);
			if ($passedQuizCount >= $attemptCount)
				return true;
		}		

		return false;
	}
	
	function getDetailedResultsType($resultInfo = null)
	{
		if (is_null($resultInfo))
			$resultInfo = $this->_getResultInfo();

		$fullStatisticsOnSuccess = $resultInfo['FullStatisticsOnSuccess'];
		$fullStatisticsOnFail = $resultInfo['FullStatisticsOnFail'];

		$isPassed = (bool)$resultInfo['_Passed'];

		return $isPassed ? $fullStatisticsOnSuccess : $fullStatisticsOnFail;
	}

	function printResults()
	{
		if (!$this->_isQuizFinished(true))
			return ;

		$resultInfo = $this->_getResultInfo();
		$isPassed = !empty($resultInfo['_Passed']);
		$templateKey = $isPassed 
			? ARIQUIZ_TEXTTEMPLATE_PRINTSUCCESSFUL 
			: ARIQUIZ_TEXTTEMPLATE_PRINTFAILED;
			
		echo $this->_getResultText($templateKey);
	}
	
	function certificate()
	{
		$certificatePath = $this->_generateCertificate();
		
		if (!is_null($certificatePath))
			AriResponse::sendContentAsAttach(file_get_contents($certificatePath), 'certificate.pdf');
	}
	
	function getAttachmentsForMail(&$content)
	{
		$attachments = array();
		
		$matches = array();
		
		@preg_match_all('/\{\$Attachment\:([^}]+)}/si', $content, $matches, PREG_SET_ORDER);
		
		if (empty($matches))
			return $attachments;
			
		$content = preg_replace('/\{\$Attachment\:([^}]+)}/si', '', $content);
			
		foreach ($matches as $match)
		{
			if (empty($match[1]))
				continue ;
				
			$attachment = $match[1];
			if (isset($attachments[$attachment]))
				continue ;

			$attachmentFilePath = null;
			switch ($attachment)
			{
				case 'Certificate':
					$attachmentFilePath = $this->_generateCertificate();
					break;
			}
			
			if (!empty($attachmentFilePath))
				$attachments[$attachment] = $attachmentFilePath;
		}
		
		$attachments = array_unique(array_values($attachments));

		return $attachments;
	} 
	
	function sendResultToAdmin($result)
	{
		if (!empty($result['ResultEmailed']) || (empty($result['AdminEmail']) && empty($result['MailGroupList']))) 
			return false;

		$templateKey = ARIQUIZ_TEXTTEMPLATE_ADMINEMAIL;
		$email = $result['AdminEmail'] ? trim($result['AdminEmail']) : '';
		if (!$this->_isVisibleCtrl($templateKey)) 
			return false;

		$resultText = $this->_getResultText($templateKey);
		if (empty($resultText))
			return false;

		$attachments = $this->getAttachmentsForMail($resultText);
		$mailTemplate = $this->_getMailTemplate($templateKey);
		$subject = AriUtils::getParam($mailTemplate, 'Subject', '');
		if (empty($subject)) 
			$subject = JText::_('COM_ARIQUIZ_LABEL_QUIZRESULTS');

		if (!empty($result['MailGroupList']))
		{
			$usersModel = $this->getModel('Users');
			$users = $usersModel->getUserList($result['MailGroupList']);
			
			if ($users)
			{
				foreach ($users as $user)
					$email .= ';' . $user->Email;
				
				$email = trim($email, ';');
			}
		}

		$isSend = AriMailer::send(
			AriUtils::getParam($mailTemplate, 'From', ''),
			AriUtils::getParam($mailTemplate, 'FromName', ''), 
			$email,
			$subject,
			$resultText, 
			true,
			null,
			null,
			$attachments);

		if ($isSend)
		{
			$resultModel = $this->getModel('Quizresult');
			$resultModel->markResultSend($this->_getTicketId());
		}
	
		return $isSend;
	}
	
	function sendEmailToUser()
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		
		$ticketId = $this->_getTicketId();
		$resultInfo = $this->_getResultInfo();
		if (empty($resultInfo['Email']))
			return false;

		$email = $resultInfo['Email'];
		$isPassed = !empty($resultInfo['_Passed']);
		
		$templateKey = $isPassed 
			? ARIQUIZ_TEXTTEMPLATE_EMAILSUCCESSFUL 
			: ARIQUIZ_TEXTTEMPLATE_EMAILFAILED;
		if (!$this->_isVisibleCtrl($templateKey)) 
			return false;
		
		$resultText = $this->_getResultText($templateKey);
		if (empty($resultText))
			return false;

		$attachments = $this->getAttachmentsForMail($resultText);
		$mailTemplate = $this->_getMailTemplate($templateKey);
		$subject = AriUtils::getParam($mailTemplate, 'Subject', '');
		if (empty($subject)) 
			$subject = JText::_('COM_ARIQUIZ_LABEL_EMAILQUIZRESULT');

		$isSend = AriMailer::send(
			AriUtils::getParam($mailTemplate, 'From', ''),
			AriUtils::getParam($mailTemplate, 'FromName', ''), 
			$email,
			$subject,
			$resultText, 
			true,
			null,
			null,
			$attachments);

		return $isSend;
	}
	
	function _getTemplates()
	{
		if (!is_null($this->_templates))
			return $this->_templates;

		$user =& JFactory::getUser();
		$userId = $user->get('id');
		$ticketId = $this->_getTicketId();
		$resultInfo = $this->_getResultInfo();
		$templates = array();
		
		if ($resultInfo['ResultScaleId'] && $resultInfo['ResultTemplateType'] == 'scale')
		{
			$resultScaleModel = $this->getModel('Resultscale');
			$scaleItem = $resultScaleModel->getScaleItemByScore($resultInfo['ResultScaleId'], $resultInfo['PercentScore']);
			if ($scaleItem)
			{
				if ($scaleItem->TextTemplateId)
					$templates[ARIQUIZ_TEXTTEMPLATE_SUCCESSFUL] = $templates[ARIQUIZ_TEXTTEMPLATE_FAILED] = $scaleItem->TextTemplateId;
					
				if ($scaleItem->MailTemplateId)
					$templates[ARIQUIZ_TEXTTEMPLATE_EMAILSUCCESSFUL] = $templates[ARIQUIZ_TEXTTEMPLATE_EMAILFAILED] = $scaleItem->MailTemplateId;
					
				if ($scaleItem->PrintTemplateId)
					$templates[ARIQUIZ_TEXTTEMPLATE_PRINTSUCCESSFUL] = $templates[ARIQUIZ_TEXTTEMPLATE_PRINTFAILED] = $scaleItem->PrintTemplateId;
					
				if ($scaleItem->CertificateTemplateId)
					$templates[ARIQUIZ_TEXTTEMPLATE_CERTIFICATESUCCESSFUL] = $templates[ARIQUIZ_TEXTTEMPLATE_CERTIFICATEFAILED] = $scaleItem->CertificateTemplateId;	
			}
			
			$templates[ARIQUIZ_TEXTTEMPLATE_ADMINEMAIL] = $resultInfo['AdminMailTemplateId'];
		}
		else
		{
			$templates[ARIQUIZ_TEXTTEMPLATE_ADMINEMAIL] = $resultInfo['AdminMailTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_SUCCESSFUL] = $resultInfo['PassedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_FAILED] = $resultInfo['FailedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_EMAILSUCCESSFUL] = $resultInfo['MailPassedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_EMAILFAILED] = $resultInfo['MailFailedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_PRINTSUCCESSFUL] = $resultInfo['PrintPassedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_PRINTFAILED] = $resultInfo['PrintFailedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_CERTIFICATESUCCESSFUL] = $resultInfo['CertificatePassedTemplateId'];
			$templates[ARIQUIZ_TEXTTEMPLATE_CERTIFICATEFAILED] = $resultInfo['CertificateFailedTemplateId'];

			if (!is_array($templates)) 
				$templates = array();
		}

		$this->_templates = $templates;
			
		return $this->_templates;
	}
	
	function _getMailTemplate($templateKey)
	{
		$mailTemplate = null;
		$templates = $this->_getTemplates();
		$resultText = '';
		if (!isset($templates[$templateKey]))
			return $mailTemplate;

		$templateId = $templates[$templateKey];
		$mailTemplateModel = $this->getModel('Mailtemplate');
		$mailTemplate = $mailTemplateModel->getTemplateByTextTemplateId($templateId);

		return $mailTemplate;
	}

	function _getResultText($templateKey)
	{
		$resultInfo = $this->_getResultInfo();
		$templates = $this->_getTemplates();
		$resultText = '';
		if (!isset($templates[$templateKey]))
			return $resultText;

		$templateId = $templates[$templateKey];
		$textTemplateModel = $this->getModel('Texttemplate');
		$template = $textTemplateModel->getTemplate($templateId);
		if ($template)
		{
			if (strpos($template->Value, '{$SummaryByCategories') !== false)
				$resultInfo['SummaryByCategories'] = $this->_getSummaryByCategories();
			
			$resultText = $template->parse($resultInfo);
		}
		
		return $resultText;
	}
	
	function _getSummaryByCategories()
	{
		if (!is_null($this->_summaryByCategories))
			return $this->_summaryByCategories;

		$resultInfo = $this->_getResultInfo();
		$quizResultModel = $this->getModel('QuizResult');
		$summaryByCategories = $quizResultModel->getFinishedInfoByCategory($this->_resultInfo['StatisticsInfoId']);
		
		AriKernel::import('Web.Controls.Repeater');
		$repeater = new AriRepeaterWebControl(ARIQUIZ_TEXTTEMPLATE_SUMMARYBYCATEGORIES_TEMPLATE, $summaryByCategories);
		
		$this->_summaryByCategories = $repeater->getContent();

		return $this->_summaryByCategories;
	}

	function _getResultInfo()
	{
		if (is_null($this->_resultInfo))
		{
			$user =& JFactory::getUser();
			$userId = $user->get('id');

			$quizResultModel = $this->getModel('QuizResult');
			$this->_resultInfo = $quizResultModel->getFormattedFinishedResult(
				$this->_getTicketId(),
				$userId,
				array(
					'UserName' => JText::_('COM_ARIQUIZ_LABEL_GUEST'),
					'PassedText' => JText::_('COM_ARIQUIZ_LABEL_PASSED'),
					'NotPassedText' => JText::_('COM_ARIQUIZ_LABEL_NOTPASSED'),
				),
				AriQuizHelper::getShortPeriods()
			);
			
			if (empty($this->_resultInfo))
				return $this->_resultInfo;

			$detailedResultsCount = $this->_resultInfo['QuestionCount'];
			$resultsType = $this->getDetailedResultsType($this->_resultInfo);
			
			if ($resultsType != 'All')
			{
				$filter = new AriDataFilter(
					array(
						'startOffset' => 0, 
						'limit' => 10,
						'sortField' => 'QuestionIndex', 
						'dir' => 'asc',
						'filter' => array(
							'ResultsFilter' => $resultsType
						)
					)
				);

				$detailedResultsCount = $quizResultModel->getQuestionCount($this->_resultInfo['StatisticsInfoId'], $filter);;
			}

			$this->_resultInfo['DetailedResultsCount'] = $detailedResultsCount;
		}

		return $this->_resultInfo;
	}

	function _isVisibleCtrl($templateKey)
	{
		$templates = $this->_getTemplates();
		return !empty($templates[$templateKey]); 
	}

	function _isQuizFinished($redirect = false)
	{
		$userQuizModel = $this->getModel('UserQuiz');
		if (is_null($this->_isQuizFinished))
		{
			$this->_isQuizFinished = $userQuizModel->isQuizFinishedByTicketId($this->_getTicketId());
		}
		
		if (!$this->_isQuizFinished && $redirect)
		{
			$ticketId = $this->_getTicketId();
			$quizModel = $this->getModel('Quiz');
			$quiz = $quizModel->getQuizByTicketId($ticketId);
			$itemId = JRequest::getInt('Itemid');
			$this->redirect(
				JRoute::_('index.php?option=com_ariquiz&view=question&ticketId=' . $ticketId . '&quizId=' . $quiz->QuizId . ($itemId > 0 ? '&Itemid=' . $itemId : ''), false)
			);
		}
		
		return $this->_isQuizFinished;
	}
	
	function _getTicketId()
	{
		if (is_null($this->_ticketId))
			$this->_ticketId = JRequest::getString('ticketId');
		
		return $this->_ticketId;
	}
	
	function ajaxGetResultList()
	{
		$type = $this->getDetailedResultsType();
		$model =& $this->getModel('QuizResult');
		$model->setTimePeriods(AriQuizHelper::getShortPeriods());

		$filter = new AriDataFilter(
			array(
				'startOffset' => 0, 
				'limit' => 10,
				'sortField' => 'QuestionIndex', 
				'dir' => 'asc',
				'filter' => array(
					'ResultsFilter' => $type
				)
			),
			true,
			null,
			array('QuestionIndex')
		);
		$filter->setConfigValue('sortField', 'QuestionIndex');
		$filter->setConfigValue('dir', 'asc');
		
		if (!$this->isDetailedResultsAvailable())
		{
			$filter->setConfigValue('limit', 0);
			
			return AriMultiPageDataTableControl::createDataInfo(null, $filter, $totalCnt);;
		}

		$sid = JRequest::getInt('sid');

		$totalCnt = $model->getQuestionCount($sid, $filter);
		if ($totalCnt < $filter->getConfigValue('limit'))
			$filter->setConfigValue('limit', $totalCnt);
		
		$filter->fixFilter($totalCnt);
		$parseTag = JRequest::getBool('parseTag');

		$results = $model->getJsonQuestionList($sid, $filter, $parseTag, false, JText::_('COM_ARIQUIZ_QUESTIONSUMMARY'));
		
		$sortField = $filter->getConfigValue('sortField');
		$filter->setConfigValue('sortField', null);
		
		$data = AriMultiPageDataTableControl::createDataInfo($results, $filter, $totalCnt);

		$filter->setConfigValue('sortField', $sortField);

		return $data;
	}

	function _generateCertificate()
	{
		if (!is_null($this->_certificateFilePath))
			return $this->_certificateFilePath;
		
		AriKernel::import('PDF.DOMPDF.DOMPDF');
		
		$resultInfo = $this->_getResultInfo();
		$isPassed = !empty($resultInfo['_Passed']);
		$templateKey = $isPassed 
			? ARIQUIZ_TEXTTEMPLATE_CERTIFICATESUCCESSFUL 
			: ARIQUIZ_TEXTTEMPLATE_CERTIFICATEFAILED;
		$content = $this->_getResultText($templateKey);
		if (empty($content))
			return null;
	
		$html = sprintf('<html><head></head><body>%1$s</body></html>',
			$content
		);

		$domPdf = new DOMPDF();
  		$domPdf->load_html($html);
  		$domPdf->render();

		$pdf = $domPdf->output();

		$certificateFilePath = JPATH_ROOT . '/tmp/' . uniqid('certificate_', false) . '.pdf';

		if (!JFile::write($certificateFilePath, $pdf))
			$certificateFilePath = null;

		$this->_certificateFilePath = $certificateFilePath;
			
		return $this->_certificateFilePath;
	}
}