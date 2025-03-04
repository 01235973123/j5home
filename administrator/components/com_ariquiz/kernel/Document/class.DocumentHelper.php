<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Joomla.Compat.Application');

class AriDocumentHelper
{
	static public function includeJsFile($fileUrl)
	{
		$document =& JFactory::getDocument();
		$document->addScript($fileUrl);
	}
	
	static public function includeCssFile($cssUrl, $type = 'text/css', $media = null, $attrs = array())
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet($cssUrl, $type, $media, $attrs);
	}
	
	static public function includeCustomHeadTag($tag)
	{
		$document =& JFactory::getDocument();
		if ($document->getType() != 'html')
			return ;

		$document->addCustomTag($tag);
	}
	
	static public function addCustomTagsToDocument($tags)
	{
		if (empty($tags)) 
			return ;

		$app = AriApplication::getApplication();
		
		$content = $app->getBody();
		$content = preg_replace('/(<\/head\s*>)/i', join('', $tags) . '$1', $content);
		
		$app->setBody($content); 
	}
}