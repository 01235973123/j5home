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
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/questiontemplates.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementQuestiontemplate extends JElement
{
	var	$_name = 'Questiontemplate';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$lazyLoad = (bool)AriXmlHelper::getAttribute($node, 'lazy_load');
		
		$templates = array();
		if ($lazyLoad)
		{
			$initTemplate = new stdClass();
			$initTemplate->TemplateId = 0;
			$initTemplate->TemplateName = JText::_('COM_ARIQUIZ_LABEL_SELECTTEMPLATE');
			
			$templates = array($initTemplate);
			
			$this->registerAssets();
			$this->addScript($control_name . $name);
		}
		else 
		{
			$templates = $this->getTemplateList();
		}

		return JHTML::_(
			'select.genericlist', 
			$templates, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select"', 
			'TemplateId', 
			'TemplateName', 
			$value,
			$control_name . $name);
	}
	
	function addScript($ctrlId)
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration(sprintf(
			'ARIQuestionTemplateElement.initEl("%1$s");',
			$ctrlId
		));
	}
	
	function registerAssets()
	{
		$doc =& JFactory::getDocument();
		
		$uri = JURI::root(true) . '/administrator/components/com_ariquiz/elements/assets/questiontemplate/';
		$doc->addScript($uri . 'questiontemplate.js');
	}

	function getTemplateList()
	{
		$filter = new AriDataFilter(
			array(
				'sortField' => 'TemplateName', 
				'dir' => 'asc'
			), 
			true);
		
		$templateModel =& AriModel::getInstance('Questiontemplates', 'AriQuizModel');
		$templates = $templateModel->getTemplateList($filter);

		$emptyTemplate = new stdClass();
		$emptyTemplate->TemplateId = 0;
		$emptyTemplate->TemplateName = JText::_('COM_ARIQUIZ_LABEL_SELECTTEMPLATE');
		array_unshift($templates, $emptyTemplate);
		
		return $templates;
	}

	function extGetTemplateList()
	{
		$templates = $this->getTemplateList();
		
		return $templates;
	}
}