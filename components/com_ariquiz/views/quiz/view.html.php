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

require_once dirname(__FILE__) . DS . '..' . DS . 'view.php';

AriKernel::import('Joomla.Menu.MenuHelper');

class AriQuizViewQuiz extends AriQuizView 
{
	var $_isFormView = true;
	
	function display($quiz, $errorCode, $formView, $tpl = null) 
	{
		$this->setTask('takeQuiz');
		
		if ($errorCode != ARIQUIZ_TAKEQUIZERROR_NONE)
		{
			$this->assign('isError', true);
			$this->assign('errorMessage', AriQuizHelper::getQuizAccessError($errorCode));
		}
		
		$this->assign('itemId', AriMenuHelper::getActiveItemId());
		$this->assignRef('quiz', $quiz);
		$this->assignRef('formView', $formView);
		
		$this->_prepareDocument($quiz);

		parent::display($tpl);
	}
	
	function _prepareDocument($quiz)
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$params = $app->getParams();
		$title = $quiz->getMetaParam('title'); 

		$id = (int)@$menu->query['quizId'];

		// if the menu item does not concern this quiz
		if (empty($title) && $menu && $menu->query['option'] == 'com_ariquiz' && $menu->query['view'] == 'quiz' && $id == $quiz->QuizId)
			$title = $params->get('page_title', '');			

		if (empty($title))
			$title = $quiz->QuizName;

		$title = AriQuizHelper::formatPageTitle($title);

		$document->setTitle($title);
		
		$metaDescription = $quiz->getMetaParam('description');
		if (empty($metaDescription))
			$metaDescription = $params->get('menu-meta_description');
			
		if (empty($metaDescription))
			$metaDescription = strip_tags($quiz->Description);

		if (!empty($metaDescription))
			$document->setDescription($metaDescription);
			
		$metaKeywords = $quiz->getMetaParam('keywords');
		if (empty($metaKeywords))
			$metaKeywords = $params->get('menu-meta_keywords');

		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	} 
}