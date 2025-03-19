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

class AriQuizViewCategory extends AriQuizView 
{
	function display($category, $quizzes, $tpl = null) 
	{
		$this->assignRef('category', $category);
		$this->assignRef('quizzes', $quizzes);
		$this->assign('itemId', AriMenuHelper::getActiveItemId());
		
		$this->_prepareDocument($category);
		
		parent::display($tpl);
	}

	function _prepareDocument($category)
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$params = $app->getParams();
		$title = $category->getMetaParam('title'); 

		$id = (int)@$menu->query['categoryId'];

		// if the menu item does not concern this quiz
		if (empty($title) && $menu && $menu->query['option'] == 'com_ariquiz' && $menu->query['view'] == 'category' && $id == $category->CategoryId)
			$title = $params->get('page_title', '');			

		if (empty($title))
			$title = $category->CategoryName;

		$title = AriQuizHelper::formatPageTitle($title);

		$document->setTitle($title);
		
		$metaDescription = $category->getMetaParam('description');
		if (empty($metaDescription))
			$metaDescription = $params->get('menu-meta_description');
			
		if (empty($metaDescription))
			$metaDescription = strip_tags($category->Description);

		if (!empty($metaDescription))
			$document->setDescription($metaDescription);
			
		$metaKeywords = $category->getMetaParam('keywords');
		if (empty($metaKeywords))
			$metaKeywords = $params->get('menu-meta_keywords');

		if (!empty($metaKeywords))
			$document->setMetadata('keywords', $metaKeywords);
	}
}