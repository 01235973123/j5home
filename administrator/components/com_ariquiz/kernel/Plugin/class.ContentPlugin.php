<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

define('ARI_CONTENT_PLUGIN_ERROR_PARSE', 'Plugin ["{%1$s}"] code can not be parsed.');

class AriContentPlugin  
{
	var $_tag;
	var $_handler = null;
	var $_supportNestedTags = false;

	function __construct($tag = null, $nested = false) 
	{
		if (!is_null($tag))
			$this->_tag = $tag;

		$this->_supportNestedTags = $nested;
	}

	function getTag() 
	{
		return $this->_tag;
	}

	function getParserRegEx() 
	{
		return sprintf(
			'/\{%1$s((?:\s+[a-z\d\_\-]+=(?:"[^"]*"|[^\s\}]*|&quot;.*?&quot;))*)\s*\}(?:(.*?)(\{\/%1$s\}))?/si',
			$this->getTag()
		);
	}
	
	function supportNestedTags() 
	{
		return $this->_supportNestedTags;
	}

	function parse($content, $handler = null) 
	{
		$tag = $this->getTag();
		if (strpos($content, '{' . $tag) === false)
			return $content;

		$supportNestedTags = $this->supportNestedTags();
		if (!$supportNestedTags) 
			return $this->_parse($content, $handler);
			
		if (!$this->isValidCode($content)) 
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(
				sprintf(
					ARI_CONTENT_PLUGIN_ERROR_PARSE,
					$tag
				), 
				'error'
			);
			
			return $content;
		}

		$openTag1 = '{' . $tag . '}';
		$openTag2 = '{' . $tag . ' ';

		while (strpos($content, '{' . $tag) !== false && ($posClosedTag = strpos($content, '{/' . $tag . '}')) !== false) 
		{
			$posOpenTag = -1;
			
			while (true) 
			{
				$curPosOpenTag = -1;
				$posOpenTag1 = strpos($content, $openTag1, $posOpenTag + 1);
				$posOpenTag2 = strpos($content, $openTag2, $posOpenTag + 1);

				if ($posOpenTag1 !== false)
					$curPosOpenTag = $posOpenTag1;

				if ($posOpenTag2 !== false && ($posOpenTag2 < $curPosOpenTag || $curPosOpenTag == -1))
					$curPosOpenTag = $posOpenTag2;

				if ($curPosOpenTag > -1 && $curPosOpenTag > $posOpenTag && $curPosOpenTag < $posClosedTag)
					$posOpenTag = $curPosOpenTag;
				else
					break;
			}

			$matches = array();
			if (!preg_match($this->getParserRegEx(), $content, $matches, 0, $posOpenTag)) 
			{
				$app = JFactory::getApplication();
				$app->enqueueMessage(
					sprintf(
						ARI_CONTENT_PLUGIN_ERROR_PARSE,
						$tag
					), 
					'error'
				);

				return $content;
			}

			if (!empty($matches[2])) 
			{
				$nestedContent = $matches[0];
				$nestedText = $this->_parse($nestedContent, $handler);
				if ($nestedContent !== $matches[0]) 
				{
					$pos = strpos($content, $matches[0]);
					$len = strlen($matches[0]);
					$content = substr_replace($content, $nestedContent, $pos, $len);
				}
			}
		}

		return $content;
	}
	
	function isValidCode($content) 
	{
		$tag = $this->getTag();
		$preparedContent = str_replace('{' . $tag . '}', '{' . $tag . ' }', strtolower($content));
		$openTag = '{' . $tag . ' ';
		$closeTag = '{/' . $tag . '}';

		$closeTagPos = strpos($preparedContent, $closeTag);
		if ($closeTagPos === false)
			return true;

		if (strpos($preparedContent, $openTag) === false)
			return false;

		$counter = 0;
		$openTagPos = -1;
		while ($openTagPos !== false) 
		{
			$openTagPos = strpos($preparedContent, $openTag, $openTagPos + 1);

			if ($openTagPos !== false && $openTagPos < $closeTagPos) 
			{
				++$counter;
			} 
			else if ($closeTagPos !== false) 
			{
				while ($closeTagPos !== false && ($openTagPos === false || $closeTagPos < $openTagPos)) 
				{
					$closeTagPos = strpos($preparedContent, $closeTag, $closeTagPos + 1);
					
					--$counter;
					if ($counter < 0)
						return false;
				}
				
				if ($openTagPos !== false)
					++$counter;
			}
		}

		return ($counter == 0);
	}
	
	function _parse($content, $handler) 
	{
		if (empty($handler))
			$handler = array($this, 'contentHandler');

		$this->_handler = $handler;
		$content = preg_replace_callback(
			$this->getParserRegEx(), 
			array($this, 'parsePlugin'),  
			$content
		);

		$this->_handler = null;

		return $content;
	}

	function parsePlugin($matches) 
	{
		if (empty($matches[0])) 
			return '';

		return call_user_func(
			$this->_handler, 
			$this->parsePluginParams($matches[1]), 
			(!empty($matches[2]) 
				? $matches[2] 
				: ''
			), 
			$matches[0]
		);
	}

	function parsePluginParams($attrs) 
	{
		$params = array();
		if (empty($attrs))
			return $params;

		$matches = null;
		preg_match_all(
			'/([a-z\d\_\-]+)=("[^"]*"|&quot;.*?&quot;|[^\s\}]*)/i', 
			$attrs, 
			$matches, 
			PREG_SET_ORDER
		);
		
		if (!empty($matches))
			foreach ($matches as $match)
				if (!empty($match[2]) && !empty($match[1])) 
					$params[$match[1]] = trim(html_entity_decode($match[2]), '"');

		return $params;
	}

	function contentHandler($params, $content, $sourceContent) 
	{
		return $sourceContent;	
	}
}