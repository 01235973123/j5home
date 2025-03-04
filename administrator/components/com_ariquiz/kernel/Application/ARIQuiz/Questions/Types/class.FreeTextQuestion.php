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

define ('ARIQUIZ_FREETEXTQUESTION_DOC_TAG', 'answers');
define ('ARIQUIZ_FREETEXTQUESTION_ITEM_TAG', 'answer');
define ('ARIQUIZ_FREETEXTQUESTION_CI_ATTR', 'ci');
define ('ARIQUIZ_FREETEXTQUESTION_ID_ATTR', 'id');
define ('ARIQUIZ_FREETEXTQUESTION_SCORE_ATTR', 'score');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionFreeTextQuestion extends AriQuizQuestionBase 
{
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false)
	{
		$data = $this->applyUserData(null, $userXml, $decodeHtmlEntity);
		
		return $data;
	}
	
	function applyUserData($data, $userXml, $decodeHtmlEntity = false)
	{
		if (empty($userXml)) return $data;
		
		$userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
		if (is_array($userData) && count($userData) > 0)
		{
			$tbxAnswer = $userData[0]['tbxAnswer'];
			if (!is_null($tbxAnswer) && strlen($tbxAnswer) > 0)
			{ 
				if (empty($data)) $data = array();
				$data['answer'] = $tbxAnswer;
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_FREETEXTQUESTION_DOC_TAG) 
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
							$xDataMap[$xDataItem['hidQueId']] = $xDataItem['tbxScore'];
						}
					} 
				}
				
				if ($htmlSpecialChars)
					AriKernel::import('String.String');

				$data = array();
				foreach ($childs as $child)
				{
					$answer = AriXmlHelper::getData($child);
					if ($htmlSpecialChars) 
						$answer = AriString::htmlSpecialChars($answer);
					
					$id = AriXmlHelper::getAttribute($child, ARIQUIZ_FREETEXTQUESTION_ID_ATTR);
					$score = AriXmlHelper::getAttribute($child, ARIQUIZ_FREETEXTQUESTION_SCORE_ATTR);
					$score = !is_null($score) ? @intval($score, 10) : 100;
					$dataItem = array(
						'tbxAnswer' => $answer,
						'hidQueId' => $id,
						'cbCI' => AriXmlHelper::getAttribute($child, ARIQUIZ_FREETEXTQUESTION_CI_ATTR),
						'tbxScore' => $score,
						'hidScore' => $score);
					
					if (isset($xDataMap[$id]))
					{
						$dataItem['chkOverride'] = true;
						$dataItem['tbxScore'] = $xDataMap[$id];
					}
					
					$data[] = $dataItem;
				}
			}
		}

		return $data;
	}
	
	function getFrontXml($questionId)
	{
		$tbxAnswer = JRequest::getString('tbxAnswer_' . $questionId, '', 'default', JREQUEST_ALLOWRAW);
		
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_FREETEXTQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 
		$xmlItem =& $xmlDoc->addChild(ARIQUIZ_FREETEXTQUESTION_ITEM_TAG);
		AriXmlHelper::setData($xmlItem, $tbxAnswer);

		return AriXmlHelper::toString($xmlDoc);	
	}

	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		$userScore = $noPenaltyForEmptyAnswer ? 0.00 : -$penalty;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$xData = $this->getDataFromXml($xml);
			$answer = !empty($xData) && count($xData) > 0 ? trim($xData[0]['tbxAnswer']) : '';
			if ($noPenaltyForEmptyAnswer && !empty($answer))
				$userScore = -$penalty;
			
			$xDataMap = array();
			if (!empty($overrideXml))
			{
				$oData = $this->getDataFromXml($overrideXml);
				if ($oData)
				{
					foreach ($oData as $dataItem)
					{
						$xDataMap[$dataItem['hidQueId']] = $dataItem['tbxScore'];
					}
				}
			}

			if (!empty($data) && strlen($answer) > 0)
			{
				foreach ($data as $dataItem)
				{
					$id = $dataItem['hidQueId'];
					$correctAnswer = $dataItem['tbxAnswer'];
					$isCorrect = false;
					if (!empty($dataItem['cbCI']))
					{
                        if (J3_4)
                        {
                            jimport('vendor.joomla.string.src.phputf8.utf8');
                            jimport('vendor.joomla.string.src.phputf8.strcasecmp');
                        }
                        else
                        {
                            jimport('phputf8.utf8');
                            jimport('phputf8.strcasecmp');
                        }

						$isCorrect = (utf8_strcasecmp($answer, $correctAnswer) === 0);
					}
					else
					{
						$isCorrect = strcmp($correctAnswer, $answer) === 0;
					}
					
					if ($isCorrect)
					{
						$scorePercent = isset($xDataMap[$id]) ? $xDataMap[$id] : $dataItem['tbxScore'];
						$scorePercent = $this->correctPercent(@intval($scorePercent, 10));
						$userScore = round(($score * $scorePercent) / 100, 2);
						break;
					}
				}
			}
		}
		
		return $userScore;
	}
	
	function getOverrideXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'hidQueId'), null, true);
		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_FREETEXTQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			foreach ($answers as $answerItem)
			{	
				$id = isset($answerItem['hidQueId'])
					? $answerItem['hidQueId'] 
					: null;
				if (empty($id)) continue;

				if ($answerItem['chkOverride'])
				{  
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_FREETEXTQUESTION_ITEM_TAG);
					$xmlItem->addAttribute(ARIQUIZ_FREETEXTQUESTION_ID_ATTR, $id);
					
					$score = trim($answerItem['tbxScore']);
					if (strlen($score) > 0)
					{
						$score = @intval($score, 10);
						if ($score > -1 && $score < 100)
						{
							$xmlItem->addAttribute(ARIQUIZ_FREETEXTQUESTION_SCORE_ATTR, $score);
						}
					}
				}
			}

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
	
	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'cbCI', 'hidQueId'), null, true);
		
		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_FREETEXTQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;

			foreach ($answers as $answerItem)
			{
				$answer = trim($answerItem['tbxAnswer']);
				if (strlen($answer))
				{
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_FREETEXTQUESTION_ITEM_TAG);
					AriXmlHelper::setData($xmlItem, $answer);

					if ($answerItem['cbCI'])
					{
						$xmlItem->addAttribute(ARIQUIZ_FREETEXTQUESTION_CI_ATTR, 'true');
					}
					
					$id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) 
						? $answerItem['hidQueId'] 
						: uniqid('', TRUE);
					$xmlItem->addAttribute(ARIQUIZ_FREETEXTQUESTION_ID_ATTR, $id);
					
					$score = trim($answerItem['tbxScore']);
					if (strlen($score) > 0)
					{
						$score = @intval($score, 10);
						if ($score > -1 && $score < 100)
						{
							$xmlItem->addAttribute(ARIQUIZ_FREETEXTQUESTION_SCORE_ATTR, $score);
						}
					}
				}
			}

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
	
	function isCorrect($xml, $baseXml, $overrideXml = null)
	{
		$isCorrect = false;
		if (empty($xml) || empty($baseXml))
			return $isCorrect;

		$data = $this->getDataFromXml($baseXml);
		$xData = $this->getDataFromXml($xml);
		$answer = !empty($xData) && count($xData) > 0 ? trim($xData[0]['tbxAnswer']) : '';

		$xDataMap = array();
		if (!empty($overrideXml))
		{
			$oData = $this->getDataFromXml($overrideXml);
			if ($oData)
				foreach ($oData as $dataItem)
					$xDataMap[$dataItem['hidQueId']] = $dataItem['tbxScore'];
		}

		if (!empty($data) && strlen($answer) > 0)
		{
			foreach ($data as $dataItem)
			{
				$id = $dataItem['hidQueId'];
				$correctAnswer = $dataItem['tbxAnswer'];
				if (!empty($dataItem['cbCI']))
				{
                    if (J3_4)
                    {
                        jimport('vendor.joomla.string.src.phputf8.utf8');
                        jimport('vendor.joomla.string.src.phputf8.strcasecmp');
                    }
                    else
                    {
                        jimport('phputf8.utf8');
                        jimport('phputf8.strcasecmp');
                    }

					$isCorrectAnswer = (utf8_strcasecmp($answer, $correctAnswer) === 0);
				}
				else
				{
					$isCorrectAnswer = strcmp($correctAnswer, $answer) === 0;
				}
				
				if ($isCorrectAnswer)
				{
					$scorePercent = isset($xDataMap[$id]) ? $xDataMap[$id] : $dataItem['tbxScore'];
					$scorePercent = $this->correctPercent(@intval($scorePercent, 10));
					$isCorrect = ($scorePercent == 100);
					break;
				}
			}
		}
			
		return $isCorrect;
	}
}