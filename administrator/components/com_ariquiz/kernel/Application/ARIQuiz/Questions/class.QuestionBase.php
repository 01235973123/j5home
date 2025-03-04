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

define('ARIQUIZ_QUESTION_TEMPLATE_XML', '<?xml version="1.0" encoding="utf-8" ?><%s />');

AriKernel::import('Xml.XmlHelper');

class AriQuizQuestionBase 
{	
	function applyUserData($data, $userXml)
	{
		return $data;
	}
	
	function getClientDataFromXml($xml, $userXml, $decodeHtmlEntity = false)
	{
		return $this->getDataFromXml($xml, $decodeHtmlEntity);
	}
	
	function getDataFromXml($xml, $decodeHtmlEntity = false, $overrideXml = null, $initData = null)
	{
		return null;
	}
	
	function getFrontXml($questionId)
	{
		return null;
	}
	
	function getXml()
	{
		return null;
	}
	
	function getOverrideXml()
	{
		return null;
	}
	
	function isCorrect($xml, $baseXml, $overrideXml = null)
	{
		return false;
	}
	
	function getScore($xml, $baseXml, $score, $penalty = 0.00, $overrideXml = null, $noPenaltyForEmptyAnswer = false)
	{
		if ($this->isCorrect($xml, $baseXml))
			return $score;

		if ($noPenaltyForEmptyAnswer && $this->isEmptyAnswer($xml))
			return 0.00;

		return -$penalty;
	}
	
	function isEmptyAnswer($xml)
	{
		if (empty($xml))
			return true;
			
		$xData = $this->getDataFromXml($xml);
		if (empty($xData) || (is_array($xData) && count($xData) == 0))
			return true;
		
		return false;
	}

	function correctPercent($percent)
	{
		$percent = @intval($percent, 10);
		
		return $percent > 100 ? 100 : ($percent < 0 ? 0 : $percent);
	}

	function getMaximumQuestionScore($score, $xml)
	{
		return $this->isScoreSpecific()
			? $this->calculateMaximumScore($score, $xml)
			: $score;
	}
	
	function calculateMaximumScore($score, $xml)
	{
		return $score;
	}
	
	function isScoreSpecific()
	{
		return false;
	}

	function hasCorrectAnswer()
	{
		return true;
	}
}