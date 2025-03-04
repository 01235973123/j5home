<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/questioncategories.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementQuestioncategory extends JElement
{
	var	$_name = 'Questioncategory';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$quizId = $this->_parent->get('QuizId');
		$lazyLoad = (bool)AriXmlHelper::getAttribute($node, 'lazy_load');
		
		$categories = array();
		if ($lazyLoad)
		{
			$relatedElement = AriXmlHelper::getAttribute($node, 'related_element');
			
			$initCategory = new stdClass();
			$initCategory->QuestionCategoryId = 0;
			$initCategory->CategoryName = JText::_('COM_ARIQUIZ_LABEL_NONE');
			
			$categories = array($initCategory);
			
			$this->registerAssets();
			$this->addScript($control_name . $name, $control_name, $relatedElement);
		}
		else 
		{
			$categories = $this->getCategoryList($quizId, $control_name);
		}

		return JHTML::_(
			'select.genericlist', 
			$categories, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select"', 
			'QuestionCategoryId', 
			'CategoryName', 
			$value,
			$control_name . $name);
	}
	
	function addScript($ctrlId, $prefix, $relatedElement)
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(sprintf(
			'ARIQuestionCategoryElement.initEl("%1$s", "%2$s", "%3$s");',
			$ctrlId,
			$prefix,
			$relatedElement ? $relatedElement : ''
		));
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/elements/assets/questioncategory/';
		$doc->addScript($uri . 'questioncategory.js');
	}
	
	function getCategoryList($quizId)
	{
		$filter = new AriDataFilter(
			array(
				'sortField' => 'CategoryName', 
				'dir' => 'asc'
			)
		);

		$categories = array();
		if ($quizId > 0)
		{
			$categoryModel =& AriModel::getInstance('Questioncategories', 'AriQuizModel');
			$categories = $categoryModel->getCategoryList($quizId, $filter);
		}
		
		$emptyCat = new stdClass();
		$emptyCat->QuestionCategoryId = 0;
		$emptyCat->CategoryName = JText::_('COM_ARIQUIZ_LABEL_NONE');
		array_unshift($categories, $emptyCat);
		
		return $categories;
	}
	
	function extGetCategoryList()
	{
		$quizId = JRequest::getInt('quizId');
		$categories = $this->getCategoryList($quizId);
		
		return $categories;
	}
}