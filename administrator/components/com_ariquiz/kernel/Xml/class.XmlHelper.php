<?php 
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class AriXmlHelperBase
{ 
	static public function getXML($data, $isFile = true)
	{
		return null;
	}
	
	static public function &getNode(&$rootNode, $tagName)
	{
		$node = null;
		if (isset($rootNode->$tagName)) 
			$node =& $rootNode->$tagName;

		return $node;
	}
	
	static public function &getSingleNode(&$rootNode, $tagName)
	{
		$node =& static::getNode($rootNode, $tagName);
		if ($node != null && is_array($node))
			$node =& $node[0];
		
		return $node;
	}
	
	static public function getData($rootNode, $tagName = null, $default = null)
	{
		$node = $tagName
			? static::getSingleNode($rootNode, $tagName)
			: $rootNode;

		if (empty($node))
			return $default;
			
		return $node->data();
	}
	
	static public function setData($node, $data)
	{
		$node->setData($data);
	}
	
	static public function getAttribute($node, $attrName, $default = null)
	{
		$val = $node->attributes($attrName);

		if (is_null($val))
			$val = $default;
		else
		{
			if (empty($val) && is_a($val, 'SimpleXMLElement'))
			{				
				$attrs = (array)$node->attributes();				
				
				if (!isset($attrs['@attributes'][$attrName]))
					$val = $default;
				else
					$val = $attrs['@attributes'][$attrName];
			}
		}
			
		return $val;
	}
	
	static public function getTagName($node)
	{
		return $node->name();
	}

	static public function toString($doc)
	{
		if (is_null($doc))
			return '';
			
		return $doc->toString();
	}
}

require_once dirname(__FILE__) . DS . 'j30' . DS . 'class.XmlHelper.php';