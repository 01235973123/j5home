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

define ('ARIQUIZ_MULTIPLEQUESTION_DOC_TAG', 'answers');
define ('ARIQUIZ_MULTIPLEQUESTION_ITEM_TAG', 'answer');
define ('ARIQUIZ_MULTIPLEQUESTION_CORRECT_ATTR', 'correct');
define ('ARIQUIZ_MULTIPLEQUESTION_RANDOM_ATTR', 'random');
define ('ARIQUIZ_MULTIPLEQUESTION_ID_ATTR', 'id');
define ('ARIQUIZ_MULTIPLEQUESTION_SCORES_TAG', 'scores');
define ('ARIQUIZ_MULTIPLEQUESTION_SCORE_TAG', 'score');
define ('ARIQUIZ_MULTIPLEQUESTION_SCORE_ATTR', 'score');
define ('ARIQUIZ_MULTIPLEQUESTION_SCORE_ID_ATTR', 'id');
define ('ARIQUIZ_MULTIPLEQUESTION_SCORE_CORRECT_TAG', 'correct');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionMultipleQuestion extends AriQuizQuestionBase 
{
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
					if ($key != 'cbCorrect')
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLEQUESTION_DOC_TAG) 
				return $data;
			
			if ($htmlSpecialChars)
				AriKernel::import('String.String');
			
			$childs = $xmlDoc->children();
			if (!empty($childs))
			{
				$data = array();
				foreach ($childs as $child)
				{
					if (AriXmlHelper::getTagName($child) != ARIQUIZ_MULTIPLEQUESTION_ITEM_TAG) 
						continue;
					
					$answer = AriXmlHelper::getData($child);
					if ($htmlSpecialChars) $answer = AriString::htmlSpecialChars($answer);
					
					$id = AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLEQUESTION_ID_ATTR);
					
					$data[$id] = array(
						'tbxAnswer' => $answer,
						'hidQueId' => $id,
						'cbCorrect' => AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLEQUESTION_CORRECT_ATTR)
					);
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLEQUESTION_DOC_TAG) 
				return $data;

			$data['randomizeOrder'] = AriUtils::parseValueBySample(
				AriXmlHelper::getAttribute($xmlDoc, ARIQUIZ_MULTIPLEQUESTION_RANDOM_ATTR), 
				false
			);
		}
		
		return $data;
	}
	
	function getScoreDataFromXml($xml, $overrideXml = null)
	{
		$data = null;
		if (!empty($xml))
		{
			$xmlHandler = AriXmlHelper::getXML($xml, false);
			$xmlDoc =& $xmlHandler->document;
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_MULTIPLEQUESTION_DOC_TAG) 
				return $data;
			
			$tagName = ARIQUIZ_MULTIPLEQUESTION_SCORES_TAG;
			if (!isset($xmlDoc->$tagName))
			{ 
				return $data;
			}
			else
			{
				$scoreItems = $xmlDoc->$tagName;
				if (!count($scoreItems)) 
					return $data;
			}

			$scores = $xmlDoc->$tagName;
			$scores = $scores[0];
			$childs = $scores->children();
			if (!empty($childs))
			{
				$xDataMap = array();
				if ($overrideXml)
				{
					$xData = $this->getScoreDataFromXml($overrideXml);
					if ($xData)
					{
						foreach ($xData as $xDataItem)
						{
							$xDataMap[$xDataItem['id']] = $xDataItem['score'];
						}
					}
				}
				
				$data = array();
				foreach ($childs as $child)
				{
					if (AriXmlHelper::getTagName($child) != ARIQUIZ_MULTIPLEQUESTION_SCORE_TAG) 
						continue;
					
					$score = @intval(AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLEQUESTION_SCORE_ATTR), 10);
					$id = AriXmlHelper::getAttribute($child, ARIQUIZ_MULTIPLEQUESTION_SCORE_ID_ATTR);
					if ($score < 0 || $score > 100) 
						continue;

					$dataItem = array('id' => $id, 'score' => $score, 'bankScore' => $score, 'correct' => array());

					$tagName = ARIQUIZ_MULTIPLEQUESTION_SCORE_CORRECT_TAG;
					if (isset($child->$tagName) && count($child->$tagName) > 0)
					{
						$correctNodes = $child->$tagName;
						foreach ($correctNodes as $correctNode)
						{
							$ansId = AriXmlHelper::getAttribute($correctNode, ARIQUIZ_MULTIPLEQUESTION_ID_ATTR);
							if (!empty($id)) 
								$dataItem['correct'][] = $ansId;
						}
					}

					if (isset($xDataMap[$id]))
					{
						$dataItem['override'] = true;
						$dataItem['score'] = $xDataMap[$id];
					}
					
					$data[] = $dataItem;
				}
			}
		}

		return $data;
	}

	function getFrontXml($questionId)
	{
		$selectedAnswers = JRequest::getVar('selectedAnswer_' . $questionId, array(), 'default', 'none', JREQUEST_ALLOWRAW);
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLEQUESTION_DOC_TAG), false);
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
				$xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEQUESTION_ITEM_TAG);
				$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_ID_ATTR, $answerId);
			}
		}
		
		return AriXmlHelper::toString($xmlDoc);
	}

	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		$userScore = $noPenaltyForEmptyAnswer ? 0.00 : -$penalty;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$correctHashMap = array();
			if (!empty($data))
			{
				$correctIdList = array();
				foreach ($data as $dataItem)
				{
					if (!empty($dataItem['cbCorrect']))
					{
						$correctIdList[] = $dataItem['hidQueId'];
					}
				}
				
				$hash = $this->getIdListHash($correctIdList);
				if (!is_null($hash)) $correctHashMap[$hash] = 100; 
			}
			
			$scoreData = $this->getScoreDataFromXml($baseXml, $overrideXml);
			if (!empty($scoreData))
			{
				foreach ($scoreData as $scoreDataItem)
				{
					$percentScore = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
					if ($percentScore < 0) continue ;
					
					$correctList = AriUtils::getParam($scoreDataItem, 'correct', null);
					$hash = $this->getIdListHash($correctList);
					
					if (!isset($correctHashMap[$hash])) $correctHashMap[$hash] = $percentScore; 
				}
			}
			
			$xData = $this->getDataFromXml($xml);
			if ($noPenaltyForEmptyAnswer && is_array($xData) && count($xData) > 0)
				$userScore = -$penalty;

			if (count($correctHashMap) > 0)
			{
				$userHash = null;
				if (is_array($xData))
				{					
					$selIdList = array();
					foreach ($xData as $dataItem)
					{
						$selIdList[] = $dataItem['hidQueId'];
					}

					$userHash = $this->getIdListHash($selIdList);
				}

				if (isset($correctHashMap[$userHash]))
				{
					$scorePercent = $this->correctPercent($correctHashMap[$userHash]);
					$userScore = round(($score * $scorePercent) / 100, 2);
				}
			}
		}

		return $userScore;
	}
	
	function getIdListHash($idList)
	{
		$hash = null;
		if (is_array($idList) && count($idList) > 0)
		{
			sort($idList);
			$hash = md5(join(' ', $idList));
		}
		
		return $hash;
	}
	
	function getOverrideXml()
	{
		$xmlStr = null;
		$scoreData = JRequest::getString('hidPercentScore', '', 'none', JREQUEST_ALLOWRAW);
		if (!empty($scoreData))
		{
			$scoreData = json_decode($scoreData);
			if (is_array($scoreData))
			{
				$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLEQUESTION_DOC_TAG), false);
				$xmlDoc = $xmlHandler->document;

				$scoresXmlItem = null;
				foreach ($scoreData as $scoreDataItem)
				{
					$override = AriUtils::parseValueBySample(AriUtils::getParam($scoreDataItem, 'override', false), false);
					if (!$override) 
						continue;

					$score = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
					if ($score < 0 || $score > 100) continue;
					
					$id = AriUtils::getParam($scoreDataItem, 'id', null);
					if (empty($id)) continue;
					
					if (is_null($scoresXmlItem)) $scoresXmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEQUESTION_SCORES_TAG);
					$xmlItem =& $scoresXmlItem->addChild(ARIQUIZ_MULTIPLEQUESTION_SCORE_TAG);
					$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_SCORE_ATTR, $score);
					$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_SCORE_ID_ATTR, $id);					
				}
			}
			
			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
	
	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'cbCorrect', 'hidQueId'), null, true);
		$xmlStr = null;
		if (!empty($answers))
		{
			$idList = array();
			
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_MULTIPLEQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			
			$randomizeOrder = JRequest::getBool('chkMQRandomizeOrder', false);
			$xmlDoc->addAttribute(ARIQUIZ_MULTIPLEQUESTION_RANDOM_ATTR, $randomizeOrder ? '1' : '0');
			
			foreach ($answers as $answerItem)
			{
				$id = '';
				$answer = trim($answerItem['tbxAnswer']);
				if (strlen($answer))
				{
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEQUESTION_ITEM_TAG);
					AriXmlHelper::setData($xmlItem, $answer);

					$correct = isset($answerItem['cbCorrect']);
					if ($correct)
					{
						$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_CORRECT_ATTR, 'true');
					}
					
					$id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) 
						? $answerItem['hidQueId'] 
						: uniqid('', true);
					$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_ID_ATTR, $id);
				}
				
				$idList[] = $id;
			}
			
			$scoreData = JRequest::getString('hidPercentScore', '', 'none', JREQUEST_ALLOWRAW);
			if (!empty($scoreData))
			{
				$scoreData = json_decode($scoreData);
				if (is_array($scoreData))
				{
					$scoresXmlItem = null;
					foreach ($scoreData as $scoreDataItem)
					{
						$score = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
						if ($score < 0 || $score > 100) continue;
						
						$id = AriUtils::getParam($scoreDataItem, 'id', null);
						if (empty($id)) $id = uniqid('mqs_', true);
						
						if (is_null($scoresXmlItem)) $scoresXmlItem =& $xmlDoc->addChild(ARIQUIZ_MULTIPLEQUESTION_SCORES_TAG);
						$xmlItem =& $scoresXmlItem->addChild(ARIQUIZ_MULTIPLEQUESTION_SCORE_TAG);
						$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_SCORE_ATTR, $score);
						$xmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_SCORE_ID_ATTR, $id);
						
						$correctList = AriUtils::getParam($scoreDataItem, 'correct', null);
						if (!is_array($correctList)) continue ;
						
						for ($i = 0; $i < count($correctList) && $i < count($answers); $i++)
						{
							if ($correctList[$i])
							{
								$id = $idList[$i];
								if (!empty($id))
								{
									$correctXmlItem =& $xmlItem->addChild(ARIQUIZ_MULTIPLEQUESTION_SCORE_CORRECT_TAG);
									$correctXmlItem->addAttribute(ARIQUIZ_MULTIPLEQUESTION_ID_ATTR, $id);
								}
							}
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
		$correctHashMap = array();
		if (!empty($data))
		{
			$correctIdList = array();
			foreach ($data as $dataItem)
				if (!empty($dataItem['cbCorrect']))
					$correctIdList[] = $dataItem['hidQueId'];
				
			$hash = $this->getIdListHash($correctIdList);
			if (!is_null($hash)) 
				$correctHashMap[$hash] = 100; 
		}
			
		$scoreData = $this->getScoreDataFromXml($baseXml, $overrideXml);
		if (!empty($scoreData))
		{
			foreach ($scoreData as $scoreDataItem)
			{
				$percentScore = @intval(AriUtils::getParam($scoreDataItem, 'score', 0), 10);
				if ($percentScore < 0) 
					continue ;
					
				$correctList = AriUtils::getParam($scoreDataItem, 'correct', null);
				$hash = $this->getIdListHash($correctList);
					
				if (!isset($correctHashMap[$hash])) 
					$correctHashMap[$hash] = $percentScore; 
			}
		}

		$xData = $this->getDataFromXml($xml);
		if (count($correctHashMap) > 0)
		{
			$userHash = null;
			if (is_array($xData))
			{					
				$selIdList = array();
				foreach ($xData as $dataItem)
				{
					$selIdList[] = $dataItem['hidQueId'];
				}

				$userHash = $this->getIdListHash($selIdList);
			}

			if (isset($correctHashMap[$userHash]))
			{
				$scorePercent = $this->correctPercent($correctHashMap[$userHash]);
				if ($scorePercent == 100)
					$isCorrect = true;
			}
		}
			
		return $isCorrect;
	}
}