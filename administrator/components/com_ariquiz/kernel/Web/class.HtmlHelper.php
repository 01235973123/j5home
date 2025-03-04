<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

class AriHtmlHelper 
{
	static public function getAttrStr($attrs, $leadSpace = true)
	{
		$str = '';
		
		if (empty($attrs) || !is_array($attrs)) return $str;
		
		$str = array();
		foreach ($attrs as $key => $value)
		{
			if (is_null($value)) continue;
			
			if (is_array($value))
			{
				$subAttrs = array();
				foreach ($value as $subKey => $subValue)
				{
					if (is_null($subValue)) continue;
					
					$subAttrs[] = sprintf('%s:%s',
						$subKey,
						str_replace('"', '\\"', $subValue));
				}
				
				if (count($subAttrs) > 0)
				{
					$str[] = sprintf('%s="%s"',
						$key,
						join(';', $subAttrs));
				}
			}
			else
			{
				$str[] = sprintf('%s="%s"',
					$key,
					str_replace('"', '\\"', $value));
			}
		}
		
		$str = join(' ', $str);
		if (!empty($str) && $leadSpace) $str = ' ' . $str;

		return $str;
	}
	
	static public function extractAttrs($htmlEl)
	{
		$attrs = array();
		if (empty($htmlEl))
			return $attrs;
		
		$matches = array();
		$attrRegExp = '/([a-z\_0-9]+)=("[^"]*"|&quot;.*?&quot;|[^\s]*)/i';
		preg_match_all($attrRegExp, $htmlEl, $matches, PREG_SET_ORDER);
		if (is_array($matches))
		{
			foreach ($matches as $match)
			{
				if (isset($match[1]) && isset($match[2])) 
					$attrs[$match[1]] = trim(html_entity_decode($match[2]), '"');
			}
		}

		return $attrs;
	}
	
	static public function extractInlineStyles($style)
	{
		$styles = array();
		$inlineStyles = explode(';', $style);
		if (empty($inlineStyles))
			return $styles;
		
		foreach ($inlineStyles as $inlineStyle)
		{
			@list($key, $value) = @explode(':', $inlineStyle);
			if (!empty($key)) $key = trim($key);
			if (empty($key))
				continue ;
			
			$styles[$key] = @trim($value);
		}
		
		return $styles;
	}
	
	static public function parseColor($color)
	{
		$rgb = array(0, 0, 0);

		if (empty($color))
			return $rgb;
			
		$color = preg_replace('/[^A-F0-9]/i', '', $color);
		$len = strlen($color);
		if ($len != 3 && $len != 6)
			return $rgb;
			
		if ($len == 3)
			$color = preg_replace('/./', '$0$0', $color);
		
		$rgb[0] = hexdec(substr($color, 0, 2));
		$rgb[1] = hexdec(substr($color, 2, 2));
		$rgb[2] = hexdec(substr($color, 4, 2));
		
		return $rgb;
	}
}