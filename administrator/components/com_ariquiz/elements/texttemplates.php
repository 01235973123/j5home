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
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/texttemplates.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');

class JElementTexttemplates extends JElement
{
	var	$_name = 'Texttemplates';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$group = AriXmlHelper::getAttribute($node, 'group');
		$size = intval(AriXmlHelper::getAttribute($node, 'size', 0), 10);
		$multiple = (bool)AriXmlHelper::getAttribute($node, 'multiple');

		$templates = $this->getTemplates($group);

		if (!$multiple)
		{
			$emptyTemplate = new stdClass();
			$emptyTemplate->TemplateId = 0;
			$emptyTemplate->TemplateName = JText::_('COM_ARIQUIZ_LABEL_NONE');
			array_unshift($templates, $emptyTemplate);
		}

		return JHTML::_(
			'select.genericlist', 
			$templates, 
			$control_name . '[' . $name . ']' . ($multiple ? '[]' : ''), 
			'class="inputbox form-select"' . ($multiple ? ' multiple="multiple"' . ($size ? ' size="' . $size . '"' : '') : ''), 
			'TemplateId', 
			'TemplateName', 
			$value,
			$control_name . $name);		
	}
	
	function getTemplates($group)
	{
		static $templates = array();
		
		if (!isset($templates[$group]))
		{
			$templatesModel =& AriModel::getInstance('Texttemplates', 'AriQuizModel');
			
			$filter = new AriDataFilter(
				array(
					'sortField' => 'TemplateName', 
					'dir' => ARI_DATAFILTER_SORT_ASC
				)
			);
	
			$templates[$group] = $templatesModel->getTemplateList($filter, $group);
		}
		
		return $templates[$group];
	}
}