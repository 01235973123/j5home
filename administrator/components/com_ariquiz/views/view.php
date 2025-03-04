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
AriKernel::import('Application.ARIQuiz.Html.Toolbar.Toolbar');

class AriQuizAdminView extends AriView 
{
	var $_INFO_MESSAGE_KEY = '__MSG';
	var $_hideMainMenu = false;
	var $_infoMessage;
	var $_ctrlName;

	function __construct($config = array())
	{
		$infoMessage = JRequest::getString($this->_INFO_MESSAGE_KEY);
		if (!empty($infoMessage))
			$this->setMessage(JText::_($infoMessage));
			
		if (array_key_exists('ctrlName', $config))
			$this->_ctrlName = $config['ctrlName'];
		
		parent::__construct($config);
	}
	
	function display($tpl = null)
	{
		AriQuizHelper::addSubmenu($this->getName());
		
		parent::display($tpl);
	}
	
	function setMessage($message)
	{
		$this->_infoMessage = $message;
	}
	
	function getCtrlName()
	{
		return empty($this->_ctrlName) ? $this->getName() : $this->_ctrlName;
	}
	
	function loadTemplate($tpl = null) 
	{
		$v = ARIQUIZ_VERSION;
		$assetsUri = JURI::root(true) . '/components/com_ariquiz/assets/';
		$adminAssetsUri = JURI::root(true) . '/administrator/components/com_ariquiz/assets/';
		$doc =& JFactory::getDocument();

		$doc->addStyleSheet($assetsUri . 'css/yui.combo.css?v=' . $v);
		$doc->addStyleSheet($adminAssetsUri . 'css/styles.css?v=' . $v);

		$doc->addScript($assetsUri . 'js/yui.combo.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.all.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/templates.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.quiz.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.watermarktext.widget.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/questions.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/ari.pageController.js?v=' . $v);
		$doc->addScript(JURI::root(true) . '/administrator/index.php?option=com_ariquiz&view=jsmessages&task=getMessages&v=' . $v);
		$doc->addScriptDeclaration(
			sprintf('YAHOO.util.Event.onDOMReady(function(){YAHOO.util.Dom.addClass(document.body,"yui-skin-sam");});initPageController("%1$s", "adminForm", "com_ariquiz", %2$s, true)',
				JURI::root(true),
				'false'
			)
		);
		
		if (!J4) {
			JHTML::_('behavior.tooltip');
		} else {
			Joomla\CMS\Factory::getApplication()
            	->getDocument()
            	->getWebAssetManager()
            	->useScript('bootstrap.popover');

			$doc->addScript($adminAssetsUri . 'js/tooltip.js');
		}

		$tpl = parent::loadTemplate($tpl);
		if (class_exists('JError') && JError::isError($tpl)) 
			return $tpl;

		$tpl = sprintf('<div class="ari-quiz-container">
			<div id="ariInfoMessage" class="message"%7$s>%6$s</div>
			<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
			%1$s
			<input type="hidden" name="hidemainmenu" value="%5$s" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="option" value="%2$s" />
			<input type="hidden" name="view" value="%3$s" />
			<input type="hidden" id="task" name="task" value="" />
			%4$s
			</form>
			</div>',
			$tpl,
			JRequest::getCmd('option'),
			$this->getCtrlName(),
			JHTML::_('form.token'),
			$this->_hideMainMenu ? '1' : '0',
			$this->_infoMessage,
			empty($this->_infoMessage) ? ' style="display: none;"' : '');

		return $tpl;
	}	
}