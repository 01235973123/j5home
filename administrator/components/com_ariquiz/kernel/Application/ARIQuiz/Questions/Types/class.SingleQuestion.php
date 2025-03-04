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

define('ARIQUIZ_SINGLEQUESTION_DOC_TAG', 'answers');
define('ARIQUIZ_SINGLEQUESTION_ITEM_TAG', 'answer');
define('ARIQUIZ_SINGLEQUESTION_RANDOM_ATTR', 'random');
define('ARIQUIZ_SINGLEQUESTION_VIEW_ATTR', 'view');
define('ARIQUIZ_SINGLEQUESTION_ID_ATTR', 'id');
define('ARIQUIZ_SINGLEQUESTION_CORRECT_ATTR', 'correct');
define('ARIQUIZ_SINGLEQUESTION_SCORE_ATTR', 'score');
define('ARIQUIZ_SINGLEQUESTION_VIEWTYPE_RADIO', '0');
define('ARIQUIZ_SINGLEQUESTION_VIEWTYPE_DROPDOWN', '1');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionSingleQuestion extends AriQuizQuestionBase 
{ 
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false, $initData = null)
	{
		$data = $this->getDataFromXml($xml, $decodeHtmlEntity, null, $initData);
		$clientData = array();
		if ($data)
		{
			$extraData = $this->getExtraDataFromXml($xml);
			$ignoreIndex = array('tbxScore', 'hidScore', 'chkOverride', 'hidCorrect');
			$queData = array();
			foreach ($data as $dataItem)
			{
				$item = array();
				foreach ($dataItem as $key => $value)
				{
					if (!in_array($key, $ignoreIndex))
					{
						$item[$key] = $value;
					}
				}
				
				$queData[] = $item;
			}

			if (empty($initData) && $extraData['randomizeOrder']) 
				shuffle($queData);
			
			$clientData['data'] = $queData;
			$clientData['view'] = $extraData['view'];
			$clientData = $this->applyUserData($clientData, $userXml, $decodeHtmlEntity);
		}

		return $clientData;
	}
	
	function applyUserData($data, $userXml, $decodeHtmlEntity = false)
	{
		if (empty($data['data']) || empty($userXml)) return $data;

		$userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
		if (is_array($userData) && count($userData) > 0)
		{
			$queData =& $data['data'];
			$userDataItem = $userData[0];
			$id = $userDataItem['hidQueId'];
					
			for ($i = 0; $i < count($queData); $i++)
			{
				$dataItem =& $queData[$i];
				if ($dataItem['hidQueId'] == $id)
				{
					$dataItem['selected'] = true;
					break;
				}
			}
		}

		return $data;
	}
	
	function getExtraDataFromXml($xml)
	{
		$data = array('randomizeOrder' => false, 'view' => 0);
		if (!empty($xml))
		{
			$xmlHandler = AriXmlHelper::getXML($xml, false);
			$xmlDoc =& $xmlHandler->document;
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_SINGLEQUESTION_DOC_TAG) 
				return $data;
			
			$data['randomizeOrder'] = AriUtils::parseValueBySample(
				AriXmlHelper::getAttribute($xmlDoc, ARIQUIZ_SINGLEQUESTION_RANDOM_ATTR), 
				false
			);
			$data['view'] = AriUtils::parseValueBySample(
				AriXmlHelper::getAttribute($xmlDoc, ARIQUIZ_SINGLEQUESTION_VIEW_ATTR), 
				''
			);
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_SINGLEQUESTION_DOC_TAG) 
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
				
				$data = array();
				
				if ($htmlSpecialChars)
					AriKernel::import('String.String');
				
				foreach ($childs as $child)
				{
					$answer = AriXmlHelper::getData($child);
					if ($htmlSpecialChars) 
						$answer = AriString::htmlSpecialChars($answer);
					
					$id = AriXmlHelper::getAttribute($child, ARIQUIZ_SINGLEQUESTION_ID_ATTR);
					$score = AriXmlHelper::getAttribute($child, ARIQUIZ_SINGLEQUESTION_SCORE_ATTR);
					$dataItem = array(
						'tbxAnswer' => $answer, 
						'hidQueId' => $id,
						'hidCorrect' => AriXmlHelper::getAttribute($child, ARIQUIZ_SINGLEQUESTION_CORRECT_ATTR),
						'tbxScore' => $score,
						'hidScore' => $score);
					
					if (isset($xDataMap[$id]))
					{
						$dataItem['chkOverride'] = true;
						$dataItem['tbxScore'] = $xDataMap[$id];
					}
					
					$data[$id] = $dataItem;
				}

				if (!empty($initData['data']))
				{
					$sortedData = array();
					
					foreach ($initData['data'] as $dataItem)
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
	
	function getFrontXml($questionId)
	{
		$selectedAnswer = JRequest::getString('selectedAnswer_' . $questionId, '', 'none', JREQUEST_ALLOWRAW);
	
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_SINGLEQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 

		if (!empty($selectedAnswer))
		{
			$xmlItem =& $xmlDoc->addChild(ARIQUIZ_SINGLEQUESTION_ITEM_TAG);
			$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_ID_ATTR, $selectedAnswer);
		}
		
		return AriXmlHelper::toString($xmlDoc);
	}

	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		$userScore = $noPenaltyForEmptyAnswer ? 0.00 : -$penalty;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$scoreMap = array();
			$correctId = null;
			if (!empty($data))
			{
				foreach ($data as $dataItem)
				{
					if (!empty($dataItem['hidCorrect']))
					{
						$correctId = $dataItem['hidQueId'];
					}
					
					if (!empty($dataItem['tbxScore']) || (isset($dataItem['tbxScore']) && $dataItem['tbxScore'] === '0'))
					{
						$scoreMap[$dataItem['hidQueId']] = @intval($dataItem['tbxScore']); 
					}
				}
			}
			
			if (!empty($overrideXml))
			{
				$data = $this->getDataFromXml($overrideXml);
				if (!empty($data))
				{
					foreach ($data as $dataItem)
					{
						$id = $dataItem['hidQueId'];
						$scoreMap[$id] = @intval($dataItem['tbxScore']);
					}
				}
			}
			if ($correctId) $scoreMap[$correctId] = 100;

			$xData = $this->getDataFromXml($xml);
			if (!empty($xData) && isset($xData[0]['hidQueId']) && key_exists($xData[0]['hidQueId'], $scoreMap))
			{
				$scorePercent = $this->correctPercent($scoreMap[$xData[0]['hidQueId']]);
				$userScore = round(($score * $scorePercent) / 100, 2);
			}
			else if ($noPenaltyForEmptyAnswer && isset($xData[0]['hidQueId']))
				$userScore = -$penalty;
			
		}
		
		return $userScore;
	}

	function getOverrideXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'hidQueId'), null, true);
		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler = AriXmlHelper::getXml(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_SINGLEQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			
			$randomizeOrder = JRequest::getBool('chkSQRandomizeOrder', false);
			if ($randomizeOrder)
				$xmlDoc->addAttribute(ARIQUIZ_SINGLEQUESTION_RANDOM_ATTR, 'true');
			
			$view = JRequest::getString('ddlSQView');
			if ($view != ARIQUIZ_SINGLEQUESTION_VIEWTYPE_RADIO)
				$xmlDoc->addAttribute(ARIQUIZ_SINGLEQUESTION_VIEW_ATTR, $view);

			foreach ($answers as $answerItem)
			{	
				$id = isset($answerItem['hidQueId'])
					? $answerItem['hidQueId'] 
					: null;
				if (empty($id)) continue;

				if ($answerItem['chkOverride'])
				{  
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_SINGLEQUESTION_ITEM_TAG);
					$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_ID_ATTR, $id);
					
					$score = @intval(trim($answerItem['tbxScore']), 10);
					$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_SCORE_ATTR, $score);
				}
			}

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
	
	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxScore', 'chkOverride', 'cbCorrect', 'hidQueId', 'hidCorrect'), null, true);

		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler = AriXmlHelper::getXml(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_SINGLEQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			
			$randomizeOrder = JRequest::getBool('chkSQRandomizeOrder', false);
			if ($randomizeOrder)
			{
				$xmlDoc->addAttribute(ARIQUIZ_SINGLEQUESTION_RANDOM_ATTR, 'true');
			}
			
			$view = JRequest::getString('ddlSQView');
			if ($view != ARIQUIZ_SINGLEQUESTION_VIEWTYPE_RADIO)
			{
				$xmlDoc->addAttribute(ARIQUIZ_SINGLEQUESTION_VIEW_ATTR, $view);
			}
			
			$isSetCorrect = false;
			foreach ($answers as $answerItem)
			{
				$answer = trim($answerItem['tbxAnswer']);
				if (strlen($answer))
				{
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_SINGLEQUESTION_ITEM_TAG);
					AriXmlHelper::setData($xmlItem, $answer);
					
					$id = isset($answerItem['hidQueId']) && !empty($answerItem['hidQueId']) 
						? $answerItem['hidQueId'] 
						: uniqid('', true);
					$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_ID_ATTR, $id);
					if (!$isSetCorrect && !empty($answerItem['hidCorrect']))
					{
						$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_CORRECT_ATTR, 'true');
						$isSetCorrect = true;
					}
					else
					{
						$score = @intval(trim($answerItem['tbxScore']), 10);
						if ($score > 0 || ($score == 0 && $answerItem['tbxScore'] === '0'))
						{
							$xmlItem->addAttribute(ARIQUIZ_SINGLEQUESTION_SCORE_ATTR, $score);
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
		if (empty($data))
			return $isCorrect;

		$scoreMap = array();
		$correctId = null;
			
		foreach ($data as $dataItem)
		{
			if (!empty($dataItem['hidCorrect']))
				$correctId = $dataItem['hidQueId'];
				
			if (!empty($dataItem['tbxScore']) || (isset($dataItem['tbxScore']) && $dataItem['tbxScore'] === '0'))
				$scoreMap[$dataItem['hidQueId']] = @intval($dataItem['tbxScore']); 
		}

		if (!empty($overrideXml))
		{
			$data = $this->getDataFromXml($overrideXml);
			if (!empty($data))
			{
				foreach ($data as $dataItem)
				{
					$id = $dataItem['hidQueId'];
					$scoreMap[$id] = @intval($dataItem['tbxScore']);
				}
			}
		}

		if ($correctId) 
			$scoreMap[$correctId] = 100;

		$xData = $this->getDataFromXml($xml);
		if (!empty($xData) && isset($xData[0]['hidQueId']) && key_exists($xData[0]['hidQueId'], $scoreMap) && $scoreMap[$xData[0]['hidQueId']] == 100)
			$isCorrect = true;

		return $isCorrect;
	}
}