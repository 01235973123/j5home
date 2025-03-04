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

define('ARIQUIZ_MULTIPLESUMQUESTION_DOC_TAG', 'answers');
define('ARIQUIZ_MULTIPLESUMQUESTION_ITEM_TAG', 'answer');
define('ARIQUIZ_MULTIPLESUMQUESTION_RANDOM_ATTR', 'random');
define('ARIQUIZ_MULTIPLESUMQUESTION_ID_ATTR', 'id');
define('ARIQUIZ_MULTIPLESUMQUESTION_SCORE_ATTR', 'score');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionMultipleSummingQuestion extends AriQuizQuestionBase 
{
	function isScoreSpecific()
	{
		return true;
	}
	
	function calculateMaximumScore($score, $xml, $overrideXml = null)
	{
		$score = 0;
		$data = $this->getDataFromXml($xml, $overrideXml);
		
		if (is_array($data))
		{
			foreach ($data as $dataItem)
			{
				if (isset($dataItem['tbxMSQScore']))
				{
					$answerScore = @floatval($dataItem['tbxMSQScore']);
					if ($answerScore > 0)
						$score += $answerScore;
				}
			}
		}

		return $score;
	}
	
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false, $initData = null)
	{
		$data = $this->getDataFromXml($xml, $decodeHtmlEntity, null, $initData);
		$clientData = array();
		if ($data)
		{
			$extraData = $this->getExtraDataFromXml($xml);
			foreach ($data as $dataItem)
			{
				$item = array();
				foreach ($dataItem as $key => $value)
				{
					if ($key != 'tbxMSQScore')
					{
						$item[$key] = $value;
					}
				}
				
				$clientData[] = $item;
			}
			
			if (empty($initData) && $extraData['randomizeOrder']) 
				shuffle($clientData);
			
			$clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
		}

		return $clientData;
	}
	
	function applyUserData($data, $userXml, $decodeHtmlEntity = false)
	{
		if (empty($data) || empty($userXml)) return $data;
		
		$userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
		if (is_array($userData) && count($userData) > 0)
		{
			$userAns = array();
			foreach ($userData as $userDataItem)
			{
				$userAns[] = $userDataItem['hidQueId'];
			}

			if (count($userAns) > 0)
			{
				for ($i = 0; $i < count($data); $i++)
				{
					$dataItem =& $data[$i];
					if (in_array($dataItem['hidQueId'], $userAns))
					{
						$dataItem['selected'] = true;
					}
				}
			}
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLESUMQUESTION_DOC_TAG) 
				return $data;
			
			$childs = $xmlDoc->children();
			if (!empty($childs))
			{
				$xDataMap = array();
				if (!empty($overrideXml))
				{
					$xData = $this->getDataFromXml($overrideXml, $htmlSpecialChars);
					if (!empty($xData))
					{
						foreach ($xData as $xDataItem)
						{
							$xDataMap[$xDataItem['hidQueId']] = $xDataItem['tbxMSQScore'];
						}
					} 
				}
				
				if ($htmlSpecialChars)
					AriKernel::import('String.String');
				
				$data = array();
				foreach ($childs as $child)
				{
					if (AriXmlHelper::getTagName($child) != ARIQUIZ_MULTIPLESUMQUESTION_ITEM_TAG) 
						continue;
					
					$answer = AriXmlHelper::getData($child);
					if ($htmlSpecialChars) 
						$answer = AriString::htmlSpecialChars($answer);
					$id = AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLESUMQUESTION_ID_ATTR);
					$score = @floatval(AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLESUMQUESTION_SCORE_ATTR));
					$data[$id] = array(
						'tbxAnswer' => $answer,
						'hidQueId' => $id,
						'tbxMSQScore' => $score,
						'hidScore' => $score);
					
					if (isset($xDataMap[$id]))
					{
						$dataItem['chkOverride'] = true;
						$dataItem['tbxMSQScore'] = $xDataMap[$id];
					}
				}
				
				if (!empty($initData))
				{
					$sortedData = array();
					
					foreach ($initData as $dataItem)
					{
						$id = $dataItem['hidQueId'];
						
						$sortedData[] = $data[$id];
					}
					
					$data = $sortedData;
				}
				else
				{
					$data = array_values($data);
				}
			}
		}

		return $data;
	}

	function getExtraDataFromXml($xml)
	{
		$data = array('randomizeOrder' => false);
		if (!empty($xml))
		{
			$xmlHandler = AriXmlHelper::getXML($xml, false);
			$xmlDoc =& $xmlHandler->document;
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLESUMQUESTION_DOC_TAG) 
				return $data;

			$data['randomizeOrder'] = AriUtils::parseValueBySample(
				AriXmlHelper::getAttribute($xmlDoc, ARIQUIZ_MULTIPLESUMQUESTION_RANDOM_ATTR), 
				false
			);
		}

		return $data;
	}

	function getFrontXml($questionId)
	{
		$selectedAnswers = JRequest::getVar('selectedAnswer_' . $questionId, array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLESUMQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 
		if (!is_array($selectedAnswers))
		{
			$selectedAnswers = array($selectedAnswers);
		}

		foreach ($selectedAnswers as $answerId)
		{
			$answerId = trim($answerId);
			if (!empty($answerId))
			{
				$xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLESUMQUESTION_ITEM_TAG);
				$xmlItem->addAttribute(ARIQUIZ_MULTIPLESUMQUESTION_ID_ATTR, $answerId);
			}
		}
		
		return AriXmlHelper::toString($xmlDoc);
	}

	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		$userScore = 0;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$scoreMapping = array();
			if (!empty($data))
			{
				foreach ($data as $dataItem)
				{
					if (!empty($dataItem['tbxMSQScore']))
					{
						$scoreMapping[$dataItem['hidQueId']] = @floatval($dataItem['tbxMSQScore']);
					}
				}
			}

			if (count($scoreMapping) > 0)
			{
				$xData = $this->getDataFromXml($xml);
				if ($xData)
				{
					foreach ($xData as $dataItem)
					{
						$selId = $dataItem['hidQueId'];
						if (key_exists($selId, $scoreMapping))
						{
							$userScore += $scoreMapping[$selId];
						}
					}
				}

				if ($userScore < 0) $userScore = 0;
				else if ($userScore > $score) $userScore = $score;
			}
		}
		
		return $userScore;
	}

	function getOverrideXml()
	{
		$xmlStr = null;

		return $xmlStr;
	}
	
	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxMSQScore', 'hidQueId'), null, true);
		$xmlStr = null;
		if (!empty($answers))
		{
			$idList = array();
			
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLESUMQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			
			$randomizeOrder =JRequest::getBool('chkMSQRandomizeOrder', false);
			if ($randomizeOrder)
			{
				$xmlDoc->addAttribute(ARIQUIZ_MULTIPLESUMQUESTION_RANDOM_ATTR, 'true');
			}
			
			foreach ($answers as $answerItem)
			{
				$answer = trim($answerItem['tbxAnswer']);
				if (!strlen($answer)) continue ;

				$xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLESUMQUESTION_ITEM_TAG);
				AriXmlHelper::setData($xmlItem, $answer);

				$score = AriUtils::parseValueBySample(
					AriUtils::getParam($answerItem, 'tbxMSQScore', 0),
					0.0);
				if ($score != 0)
					$xmlItem->addAttribute(ARIQUIZ_MULTIPLESUMQUESTION_SCORE_ATTR, $score);

				$id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) 
					? $answerItem['hidQueId'] 
					: uniqid('', true);
				$xmlItem->addAttribute(ARIQUIZ_MULTIPLESUMQUESTION_ID_ATTR, $id);
			}

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}

	function hasCorrectAnswer()
	{
		return false;
	}
}