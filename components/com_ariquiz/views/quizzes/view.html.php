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

class AriQuizViewQuizzes extends AriQuizView 
{
	function display($quizzes, $categories, $tpl = null) 
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$showDescription = (bool)$params->get('showdescription');
		
		$this->assignRef('quizzes', $quizzes);
		$this->assign('showDescription', $showDescription);
		$this->assign('categories', $categories);
		$this->assign('itemId', AriMenuHelper::getActiveItemId());
		
		$this->_prepareDocument();
		
		parent::display($tpl);
	}
	
	function _prepareDocument()
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$title = $params->get('page_title', ''); 

		if (!empty($title))
		{
			$title = AriQuizHelper::formatPageTitle($title);
			$document->setTitle($title);
		}
		
		$metaDescription = $params->get('menu-meta_description');	
		if (!empty($metaDescription))
			$document->setDescription($metaDescription);

		$metaKeywords = $params->get('menu-meta_keywords');
		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
}