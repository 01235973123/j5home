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

define('ARIQUIZ_ESSAYQUESTION_DOC_TAG', 'answers');
define('ARIQUIZ_ESSAYQUESTION_ITEM_TAG', 'answer');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionEssayQuestion extends AriQuizQuestionBase 
{
	function isScoreSpecific()
	{
		return true;
	}
	
	function calculateMaximumScore($score, $xml, $overrideXml = null)
	{
		return 0;
	}
	
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false)
	{
		$data = $this->applyUserData(null, $userXml, $decodeHtmlEntity);
		
		return $data;
	}
	
	function applyUserData($data, $userXml, $decodeHtmlEntity = false)
	{
		if (empty($userXml)) 
			return $data;

		$userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
		if (!empty($userData['answer']))
		{
			$tbxAnswer = $userData['answer']; 

			if (empty($data)) 
				$data = array();
				
			$data['answer'] = $tbxAnswer;
		}

		return $data;
	}

	function getDataFromXml($xml, $htmlSpecialChars = true, $overrideXml = null, $initData = null)
	{
		$data = null;
		if (!empty($xml))
		{
			$xmlHandler = AriXmlHelper::getXML($xml, false);
			$xmlDoc =& $xmlHandler->document;
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_ESSAYQUESTION_DOC_TAG) 
				return $data;

			$childs = $xmlDoc->children();
			if (!empty($childs))
			{
				if ($htmlSpecialChars)
					AriKernel::import('String.String');
				
				$data = array();
				foreach ($childs as $child)
				{
					if (AriXmlHelper::getTagName($child) != ARIQUIZ_ESSAYQUESTION_ITEM_TAG)
						continue;
					
					$answer = AriXmlHelper::getData($child);
					if ($htmlSpecialChars) 
						$answer = AriString::htmlSpecialChars($answer);
					
					$data['answer'] = $answer;
					break;
				}
			}
		}

		return $data;
	}

	function getFrontXml($questionId)
	{
		$answer = JRequest::getString('answer_' . $questionId);
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_ESSAYQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 

		$xmlItem =& $xmlDoc->addChild(ARIQUIZ_ESSAYQUESTION_ITEM_TAG);
		AriXmlHelper::setData($xmlItem, $answer);

		return AriXmlHelper::toString($xmlDoc);
	}

	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		return 0;
	}

	function getOverrideXml()
	{
		$xmlStr = null;

		return $xmlStr;
	}
	
	function getXml()
	{
		$xmlStr = null;

		return $xmlStr;
	}

	function hasCorrectAnswer()
	{
		return false;
	}
}