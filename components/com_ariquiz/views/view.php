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

AriKernel::import('Joomla.Views.View');

function AriQuizFixBaseUrlHandler()
{
	$body = JResponse::getBody();
	
	if (preg_match('/<base[^>]+>/i', $body))
		JResponse::setBody(preg_replace('/<base[^>]+>/i', '<base href="' . JURI::base() . '" />', $body));
	else
		JResponse::setBody(
			preg_replace('/(<\/head\s*>)/i', '<base href="' . JURI::base() . '" />' . '$1', $body, 1)
		);
}

class AriQuizView extends AriView 
{
	var $_isFormView = false;
	var $_task = '';
	var $_theme;
	
	function setTheme($theme)
	{
		$this->_theme;
	}
	
	function getTheme()
	{
		return $this->_theme;
	}
	
	function setTask($task)
	{
		$this->_task = $task;
	}
	
	function getTask()
	{
		return $this->_task;
	}
	
	function fixBaseUrl()
	{
		$mainframe =& JFactory::getApplication();

		if ($mainframe->isAdmin())
			return;
			
		$mainframe->registerEvent('onAfterRender', 'AriQuizFixBaseUrlHandler');
	}
	
	function loadTemplate($tpl = null) 
	{
		$config = AriQuizHelper::getConfig();
		$cfgFixBaseUrl = $config->get('FixBaseUrl');
		if ($cfgFixBaseUrl == 'auto')
		{
			$mainframe =& JFactory::getApplication();
			$cfgFixBaseUrl = (bool)$mainframe->getCfg('sef');
		}
		else 
			$cfgFixBaseUrl = (bool)$cfgFixBaseUrl;
		
		if ($cfgFixBaseUrl)
			$this->fixBaseUrl();

		if (!J1_5)
			JHtml::_('behavior.framework');
			
		$v = ARIQUIZ_VERSION;

		$assetsUri = JURI::root(true) . '/components/com_ariquiz/assets/';
		$doc =& JFactory::getDocument();

		$doc->addStyleSheet($assetsUri . 'css/yui.combo.css?v=' . $v);
		$doc->addStyleSheet($assetsUri . 'css/styles.css?v=' . $v);
		
		$doc->addScript($assetsUri . 'js/yui.combo.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.all.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/templates.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.quiz.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.pageController.js?v=' . $v);
		$doc->addScript(JURI::root(true) . '/index.php?option=com_ariquiz&view=jsmessages&task=getMessages&v=' . $v);
		$doc->addScriptDeclaration(
			sprintf(';YAHOO.util.Event.onDOMReady(function(){ YAHOO.util.Dom.addClass(document.body, "yui-skin-sam"); });initPageController("%1$s", "quizForm", "com_ariquiz", %2$s, false);',
				JURI::root(true),
				J1_5 ? 'true' : 'false'
			)
		);

		$this->_loadTheme();

		$tpl = parent::loadTemplate($tpl);
		if (JError::isError($tpl)) 
			return $tpl;
			
		if ($this->_isFormView)
			$tpl = sprintf('<div class="ari-quiz-container" id="ariQuizContainer">
				<div id="ariInfoMessage" class="message" style="display:none;"></div>
				<form action="index.php" enctype="multipart/form-data" method="post" name="quizForm" id="quizForm">
				%1$s
				<input type="hidden" name="option" value="com_ariquiz" />
				<input type="hidden" name="view" value="%2$s" />
				<input type="hidden" name="task" value="%4$s" />
				%3$s
				</form>
				</div>',
				$tpl,
				$this->getName(),
				JHTML::_('form.token'),
				$this->getTask());

		return $tpl;
	}
	
	function _loadTheme()
	{
		$themesPriority = array();
		$theme = $this->getTheme();
		
		if (!empty($theme))
			$themesPriority[] = $theme;
			
		$config = AriQuizHelper::getConfig();
		$cfgTheme = $config->get('Theme');
		if (!empty($cfgTheme))
			$themesPriority[] = $cfgTheme;	 

		$themesPriority[] = 'default';
		
		reset($themesPriority);
		
		jimport('joomla.filter.filterinput');
		
		$filter = JFilterInput::getInstance();
		$themesFolder = JPATH_ROOT . '/components/com_ariquiz/themes/';
		foreach ($themesPriority as $theme)
		{
			$theme = $filter->clean($theme, 'ALNUM');
			if (empty($theme))
				continue ;
				
			$themePath = $themesFolder . $theme . '/loader.php';
			if (!@file_exists($themePath))
				continue ;
				
			require_once $themePath;
			
			$themeClass = 'AriQuizThemeLoader_' . ucfirst($theme);
			if (!class_exists($themeClass))
				continue ;
				
			$themeInstance = new $themeClass();
			$themeInstance->load();
			
			break;
		}
	}
}