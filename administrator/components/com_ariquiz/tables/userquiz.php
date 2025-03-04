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

AriKernel::import('Joomla.Tables.Table');
AriKernel::import('Utils.ArrayHelper');
AriKernel::import('Utils.DateUtility');
AriKernel::import('Xml.XmlHelper');

define('ARIQUIZ_USERQUIZ_STATUS_PREPARE', 'Prepare');
define('ARIQUIZ_USERQUIZ_STATUS_PROCESS', 'Process');
define('ARIQUIZ_USERQUIZ_STATUS_PAUSE', 'Pause');
define('ARIQUIZ_USERQUIZ_STATUS_COMPLETE', 'Finished');

class AriQuizTableUserQuiz extends AriTable
{	
	var $StatisticsInfoId;
	var $QuizId;
	var $UserId = null;
	var $Status = 'Process';
	var $TicketId;
	var $StartDate = null;
	var $EndDate = null;
	var $PassedScore = 0;
	var $UserScore = 0;
	var $MaxScore = 0;
	var $Passed = 0;
	var $CreatedDate;
	var $ResultEmailed = 0;
	var $QuestionCount = 0;
	var $TotalTime = 0;
	var $ExtraData = null;
	var $ModifiedDate = null;
	var $UserScorePercent = 0.00;
	var $ElapsedTime = 0;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquizstatisticsinfo', 'StatisticsInfoId', $db);
	}
	
	function getExtraDataXml($extraData)
	{
		$xml = null;
		if (empty($extraData)) return $xml;
		
		$xmlHandler = AriXmlHelper::getXML('<?xml version="1.0" encoding="utf-8" ?><extraData />', false);
		$xmlDoc = $xmlHandler->document;
		foreach ($extraData as $key => $value)
		{
			$xmlItem =& $xmlDoc->addChild('item');
			$xmlItem->addAttribute('name', $key);
			AriXmlHelper::setData($xmlItem, $value);
		}

		$xml = AriXmlHelper::toString($xmlDoc);
		return $xml;
	}
	
	function parseExtraDataXml($xml)
	{
		$extraData = array();
		
		if (empty($xml)) return $extraData;
		
		$xmlHandler = AriXmlHelper::getXML($xml, false);
		$xmlDoc = $xmlHandler->document;
		$tagName = 'item';
		$childs = $xmlDoc->$tagName;
		if (!empty($childs))
		{
			foreach ($childs as $child)
			{
				$extraData[AriXmlHelper::getAttribute($child, 'name')] = AriXmlHelper::getData($child);
			}
		}

		return $extraData;
	}
}