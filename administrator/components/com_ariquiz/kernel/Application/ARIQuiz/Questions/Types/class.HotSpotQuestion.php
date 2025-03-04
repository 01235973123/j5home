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

define('ARIQUIZ_HOTSPOTQUESTION_DOC_TAG', 'answers');
define('ARIQUIZ_HOTSPOTQUESTION_ITEM_TAG', 'answer');
define('ARIQUIZ_HOTSPOTQUESTION_X1', 'x1');
define('ARIQUIZ_HOTSPOTQUESTION_Y1', 'y1');
define('ARIQUIZ_HOTSPOTQUESTION_X2', 'x2');
define('ARIQUIZ_HOTSPOTQUESTION_Y2', 'y2');

AriKernel::import('Application.ARIQuiz.Questions.QuestionBase');
AriKernel::import('Utils.Utils');
AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionHotSpotQuestion extends AriQuizQuestionBase 
{
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false)
	{
		$clientData = array();
		$this->applyUserData($clientData, $userXml, $decodeHtmlEntity);

		return $clientData;
	}
	
	function applyUserData($data, $userXml, $decodeHtmlEntity = false)
	{
		if (empty($userXml)) return $data;
		
		$userData = $this->getDataFromXml($userXml, $decodeHtmlEntity);
		if ($userData)
		{
			if (empty($data))
				$data = array();

			$data['x'] = $userData[ARIQUIZ_HOTSPOTQUESTION_X1];
			$data['y'] = $userData[ARIQUIZ_HOTSPOTQUESTION_Y1];
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
			if (AriXmlHelper::getTagName($xmlDoc) != ARIQUIZ_HOTSPOTQUESTION_DOC_TAG) 
				return $data;

			$childs = $xmlDoc->children();
			if (!empty($childs) && count($childs) > 0)
			{
				$data = array();
				$child = $childs[0];
				$data[ARIQUIZ_HOTSPOTQUESTION_X1] = intval(AriXmlHelper::getAttribute($child, ARIQUIZ_HOTSPOTQUESTION_X1), 10);
				$data[ARIQUIZ_HOTSPOTQUESTION_X2] = intval(AriXmlHelper::getAttribute($child, ARIQUIZ_HOTSPOTQUESTION_X2), 10);
				$data[ARIQUIZ_HOTSPOTQUESTION_Y1] = intval(AriXmlHelper::getAttribute($child, ARIQUIZ_HOTSPOTQUESTION_Y1), 10);
				$data[ARIQUIZ_HOTSPOTQUESTION_Y2] = intval(AriXmlHelper::getAttribute($child, ARIQUIZ_HOTSPOTQUESTION_Y2), 10);
			}
		}

		return $data;
	}
	
	function isCorrect($xml, $baseXml, $overrideXml = null)
	{
		$isCorrect = false;
		if (!empty($xml) && !empty($baseXml))
		{
			$data = $this->getDataFromXml($baseXml);
			$xData = $this->getDataFromXml($xml);
			
			if ($data[ARIQUIZ_HOTSPOTQUESTION_X1] <= $xData[ARIQUIZ_HOTSPOTQUESTION_X1] &&
				$xData[ARIQUIZ_HOTSPOTQUESTION_X1] <= $data[ARIQUIZ_HOTSPOTQUESTION_X2] &&
				$data[ARIQUIZ_HOTSPOTQUESTION_Y1] <= $xData[ARIQUIZ_HOTSPOTQUESTION_Y1] &&
				$xData[ARIQUIZ_HOTSPOTQUESTION_Y1] <= $data[ARIQUIZ_HOTSPOTQUESTION_Y2])
			{
				$isCorrect = true;
			}
		}
		
		return $isCorrect;
	}
	
	function getFrontXml($questionId)
	{
		$x = JRequest::getInt('hidAriHotSpotX_' . $questionId, -1);
		$y = JRequest::getInt('hidAriHotSpotY_' . $questionId, -1);
		
		$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_HOTSPOTQUESTION_DOC_TAG), false);
		$xmlDoc = $xmlHandler->document; 

		if ($x > -1 && $y > -1)
		{
			$xmlItem =& $xmlDoc->addChild(ARIQUIZ_HOTSPOTQUESTION_ITEM_TAG);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_X1, $x);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_Y1, $y);
		}
		
		return AriXmlHelper::toString($xmlDoc);
	}
	
	function getXml()
	{
		$xmlStr = null;
		$coords = JRequest::getString('hotSpotCoords');
		if (!empty($coords))
			$coords = (!empty($coords)) ? json_decode($coords) : null;
		
		$x1 = $x2 = $y1 = $y2 = -1;
		if ($coords)
		{
			$x1 = AriUtils::getParam($coords, 'left', -1);
			$y1 = AriUtils::getParam($coords, 'top', -1);

			$width = AriUtils::getParam($coords, 'width', -1);
			$height = AriUtils::getParam($coords, 'height', -1);

			if ($x1 > -1 && $width > -1)
				$x2 = $x1 + $width;
				
			if ($y1 > -1 && $height > -1)
				$y2 = $y1 + $height;
		}

		if ($x1 > -1 && $x2 > -1 && $y1 > -1 && $y2 > -1)
		{
			$xmlHandler = AriXmlHelper::getXML(sprintf(ARIQUIZ_QUESTION_TEMPLATE_XML, ARIQUIZ_HOTSPOTQUESTION_DOC_TAG), false);
			$xmlDoc = $xmlHandler->document;

			$xmlItem =& $xmlDoc->addChild(ARIQUIZ_HOTSPOTQUESTION_ITEM_TAG);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_X1, $x1);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_X2, $x2);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_Y1, $y1);
			$xmlItem->addAttribute(ARIQUIZ_HOTSPOTQUESTION_Y2, $y2);

			$xmlStr = AriXmlHelper::toString($xmlDoc);
		}

		return $xmlStr;
	}
}