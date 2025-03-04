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

define ('ARIQUIZ_CORRELATIONQUESTION_DOC_TAG', 'items');
define ('ARIQUIZ_CORRELATIONQUESTION_ITEM_TAG', 'item');
define ('ARIQUIZ_CORRELATIONQUESTION_RANDOM_ATTR', 'random');
define ('ARIQUIZ_CORRELATIONQUESTION_LBLITEM_TAG', 'label');
define ('ARIQUIZ_CORRELATIONQUESTION_ANSITEM_TAG', 'answer');
define ('ARIQUIZ_CORRELATIONQUESTION_ID_ATTR', 'id');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Web.Controls.Advanced.MultiplierControls');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionCorrelationQuestion extends AriQuizQuestionBase 
{
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false, $initData = null)
	{
		$data = $this->getDataFromXml($xml, $decodeHtmlEntity, null, $initData);
		$clientData = array('labels' => array(), 'answers' => array());
		if ($data)
		{
			$extraData = $this->getExtraDataFromXml($xml);
			foreach ($data as $dataItem)
			{
				$clientData['labels'][] = array('id' => $dataItem['hidLabelId'], 'label' => $dataItem['tbxLabel']);
				$clientData['answers'][] = array('id' => $dataItem['hidAnswerId'], 'answer' => $dataItem['tbxAnswer']);
			}
			
			if (empty($initData) && $extraData['randomizeOrder']) 
				shuffle($clientData['labels']);

			shuffle($clientData['answers']);
			
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
			$correlations = array();
			foreach ($userData as $userDataItem)
			{
				$correlations[$userDataItem['hidLabelId']] = $userDataItem['hidAnswerId']; 
			}
			
			$data['correlations'] = $correlations;
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_CORRELATIONQUESTION_DOC_TAG) 
				return $data;
			
			$childs = $xmlDoc->children();
			if (!empty($childs))
			{
				if ($htmlSpecialChars)
					AriKernel::import('String.String');
				
				$data = array();
				foreach ($childs as $child)
				{
					$answerTag = ARIQUIZ_CORRELATIONQUESTION_ANSITEM_TAG;
					$labelTag = ARIQUIZ_CORRELATIONQUESTION_LBLITEM_TAG;
					
					$answer = $child->$answerTag;
					$answer = $answer ? $answer[0] : null; 
					
					$label = $child->$labelTag;
					$label = $label ? $label[0] : null;
					
					$answerStr = AriXmlHelper::getData($answer);
					$labelStr = AriXmlHelper::getData($label);
					if ($htmlSpecialChars)
					{ 
						$answerStr = AriString::htmlSpecialChars($answerStr);
						$labelStr = AriString::htmlSpecialChars($labelStr);
					}
					
					$lblId = AriXmlHelper::getAttribute($label, ARIQUIZ_CORRELATIONQUESTION_ID_ATTR);
					
					$data[$lblId] = array(
						'tbxLabel' => $labelStr,
						'tbxAnswer' => $answerStr,
						'hidLabelId' => $lblId,
						'hidAnswerId' => AriXmlHelper::getAttribute($answer, ARIQUIZ_CORRELATIONQUESTION_ID_ATTR)
					);
				}
				
				if (!empty($initData['labels']))
				{
					$sortedData = array();
					
					foreach ($initData['labels'] as $dataItem)
					{
						$id = $dataItem['id'];
						
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_CORRELATIONQUESTION_DOC_TAG) 
				return $data;
			
			$data['randomizeOrder'] = AriUtils::parseValueBySample(
				AriXmlHelper::getAttribute($xmlDoc, ARIQUIZ_CORRELATIONQUESTION_RANDOM_ATTR), 
				false
			);
		}
		
		return $data;
	}
	
	function getFrontXml($questionId)
	{
		$ddlVariant = JRequest::getVar('ddlVariant_' . $questionId, array(), 'default', 'none', JREQUEST_ALLOWRAW);
		
		return $this->_createFrontXml($ddlVariant);
	}
	
	function _createFrontXml($correlation)
	{
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_CORRELATIONQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 
		
		if (is_array($correlation))
		{
			foreach ($correlation as $key => $value)
			{
				$xmlItem =& $xmlDoc->addChild(ARIQUIZ_CORRELATIONQUESTION_ITEM_TAG);
				$subXmlItem =& $xmlItem->addChild(ARIQUIZ_CORRELATIONQUESTION_ANSITEM_TAG);
				$subXmlItem->addAttribute(ARIQUIZ_CORRELATIONQUESTION_ID_ATTR, $value);
					
				$subXmlItem =& $xmlItem->addChild(ARIQUIZ_CORRELATIONQUESTION_LBLITEM_TAG);
				$subXmlItem->addAttribute(ARIQUIZ_CORRELATIONQUESTION_ID_ATTR, $key);
			}
		}

		return AriXmlHelper::toString($xmlDoc);
	}
	
	function isCorrect($xml, $baseXml, $overrideXml = null)
	{
		$isCorrect = false;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$xData = $this->getDataFromXml($xml);
			
			if (is_array($data) && is_array($xData))
			{
				$prepareXData = array();
				foreach ($xData as $dataItem)
				{
					$prepareXData[$dataItem['hidLabelId']] = $dataItem['hidAnswerId']; 
				}
				
				$isCorrect = true;
				foreach ($data as $dataItem)
				{
					$lblId = $dataItem['hidLabelId'];
					$ansId = $dataItem['hidAnswerId'];
					if (!key_exists($lblId, $prepareXData) || $prepareXData[$lblId] != $ansId)
					{
						$isCorrect = false;
						break;
					}
				}
			}
		}

		return $isCorrect;
	}
	
	function getXml()
	{
		$answers = WebControls_MultiplierControls::getData('tblQueContainer', array('tbxAnswer', 'tbxLabel', 'hidQueId'), null, true);
		
		$xmlStr = null;
		if (!empty($answers))
		{
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_CORRELATIONQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;
			
			$randomizeOrder = JRequest::getBool('chkCQRandomizeOrder', false);
			if ($randomizeOrder)
			{
				$xmlDoc->addAttribute(ARIQUIZ_CORRELATIONQUESTION_RANDOM_ATTR, 'true');
			}

			foreach ($answers as $answerItem)
			{
				$answer = trim($answerItem['tbxAnswer']);
				$label = trim($answerItem['tbxLabel']);
				if (strlen($answer) && strlen($label))
				{
					$xmlItem =& $xmlDoc->addChild(ARIQUIZ_CORRELATIONQUESTION_ITEM_TAG);
					$subXmlItem =& $xmlItem->addChild(ARIQUIZ_CORRELATIONQUESTION_ANSITEM_TAG);
					AriXmlHelper::setData($subXmlItem, $answer);
					$id = isset($answerItem['hidAnswerId']) && !empty($answerItem['hidAnswerId']) 
						? $answerItem['hidAnswerId'] 
						: uniqid('', true);
					$subXmlItem->addAttribute(ARIQUIZ_CORRELATIONQUESTION_ID_ATTR, $id);
					
					$subXmlItem =& $xmlItem->addChild(ARIQUIZ_CORRELATIONQUESTION_LBLITEM_TAG);
					AriXmlHelper::setData($subXmlItem, $label);
					$id = isset($answerItem['hidLabelId']) && !empty($answerItem['hidLabelId']) 
						? $answerItem['hidLabelId'] 
						: uniqid('', true);
					$subXmlItem->addAttribute(ARIQUIZ_CORRELATIONQUESTION_ID_ATTR, $id);
				}
			}

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
	
	function isEmptyAnswer($xml)
	{
		$isEmpty = parent::isEmptyAnswer($xml);
		if ($isEmpty)
			return true;
			
		$xData = $this->getDataFromXml($xml);
		foreach ($xData as $dataItem)
		{
			if (!empty($dataItem['hidAnswerId']))
				return false;
		}
		
		return true;
	}	
}