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

jimport('joomla.plugin.plugin');
AriKernel::import('Plugin.ContentPlugin');

class AriQuizTemplatePluginContent extends JPlugin
{
	var $_pluginTag = '';
	var $_supportNestedTags = false;
	var $_templateKey;
	var $_results;

	function getTemplateKey()
	{
		return $this->_templateKey;
	}

	function getResults()
	{
		return $this->_results;
	}
	
	function getTag()
	{
		return $this->_pluginTag;
	}

	function supportNestedTags() 
	{
		return $this->_supportNestedTags;
	}
	
	function execute($content, $templateKey, $results)
	{
		$this->_templateKey = $templateKey;
		$this->_results = $results;

		$plg = new AriContentPlugin($this->getTag(), $this->supportNestedTags());
		$handler = array($this, 'contentPluginHandler');
		 
		return $plg->parse($content, $handler);
	}
	
	function contentPluginHandler($params, $content, $sourceContent) 
	{
		return $sourceContent;
	}
}