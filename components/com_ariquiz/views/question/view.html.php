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

AriKernel::import('Joomla.Menu.MenuHelper');

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

class AriQuizViewQuestion extends AriQuizView 
{
	var $_isFormView = true;
	
	function display($quizStorage, $quizInfo, $questions, $tpl = null) 
	{		
		$this->assign('itemId', AriMenuHelper::getActiveItemId());
		$this->assignRef('quizStorage', $quizStorage);
		$this->assignRef('quizInfo', $quizInfo);

		parent::display($tpl);
		
		$v = ARIQUIZ_VERSION;
		
		$assetsUri = JURI::root(true) . '/components/com_ariquiz/assets/';

		$doc =& JFactory::getDocument();
		
		$doc->addScript($assetsUri . 'js/ari.watermarktext.widget.js?v=' . $v);
		$doc->addScript($assetsUri . 'js/questions.js?v=' . $v);
		
		if ($quizStorage->get('UseCalculator'))
		{
			$doc->addStyleSheet($assetsUri . 'css/calculator.css?v=' . $v);
			$doc->addScript($assetsUri . 'js/ari.calculator.js?v=' . $v);
			
			$doc->addScriptDeclaration(
				sprintf(
					';new YAHOO.ARISoft.widgets.calculator.calc("queCalc", "aCalc_%1$d");',
					$quizStorage->get('QuizId')
				)
			);
		}

		if ($quizStorage->get('ParsePluginTag'))
			$this->_loadPluginsAssets($questions);
			
		$this->_prepareDocument($quizInfo);
	}

	function _loadPluginsAssets($questions)
	{
		AriKernel::import('Joomla.Plugins.PluginProcessHelper');
		AriKernel::import('Document.DocumentIncludesManager');

		$includesManager = new AriDocumentIncludesManager();

		// process
		$content = '';
		foreach ($questions as $question)
		{
			$content .= $question->Question;
			if (!empty($question->QuestionNote))
				$content .= $question->QuestionNote;
		}
		AriPluginProcessHelper::processTags($content);

		$includes = $includesManager->getDifferences(true, array('script'));
		AriDocumentHelper::addCustomTagsToDocument($includes);
	}
	
	function _prepareDocument($quizInfo)
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$title = AriUtils::getParam($quizInfo->Metadata, 'title'); 

		if (empty($title))
			$title = $quizInfo->QuizName;

		$title = AriQuizHelper::formatPageTitle($title);

		$document->setTitle($title);
		
		$metaDescription = AriUtils::getParam($quizInfo->Metadata, 'description');
		if (empty($metaDescription))
			$metaDescription = $params->get('menu-meta_description');
			
		if (empty($metaDescription))
			$metaDescription = strip_tags($quizInfo->Description);

		if (!empty($metaDescription))
			$document->setDescription($metaDescription);
			
		$metaKeywords = AriUtils::getParam($quizInfo->Metadata, 'keywords');
		if (empty($metaKeywords))
			$metaKeywords = $params->get('menu-meta_keywords');

		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
}