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

require_once dirname(__FILE__) . '/quizeditor.php';

AriKernel::import('Xml.XmlHelper');

class JElementTexttemplate extends JElementQuizeditor
{
	var	$_name = 'Texttemplate';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$this->_includeAssets();
		
		$output = parent::fetchElement($name, $value, $node, $control_name);
		$params = $this->getParams($node);
		
		$output = sprintf('<div style="position:relative;" class="element"><div style="float:right;width:150px;text-align:left;"><h3>%3$s</h3><div>%2$s</div></div><div style="padding-right:160px;">%1$s</div></div>',
			$output,
			$this->getFormattedParams($params, $control_name . '_' . $name),
			JText::_('COM_ARIQUIZ_LABEL_PARAMETERS')
		);
		
		return $output;
	}
	
	function getFormattedParams($params, $editorId)
	{
		$fmtParams = array();
		
		foreach ($params as $id => $param)
		{
			$fmtParams[] = sprintf('<a href="javascript:void(0);" onclick="aqInsertTextToEditor(\'%1$s\', \'%2$s\');return false;" class="hasTip" title="%1$s::%3$s">%1$s</a>',
				$param['label'],
				$editorId,
				$param['description']);
		}
		
		return join('<br/>', $fmtParams);
	}
	
	function getParams(&$node)
	{
		$params = array();
		
		if (empty($node->template_params))
			return $params;

		$paramsNode = AriXmlHelper::getSingleNode($node, 'template_params');
		if (!isset($paramsNode->template_param))
			return $params;

		foreach ($paramsNode->template_param as $paramNode)
		{
			$paramId = AriXmlHelper::getAttribute($paramNode, 'id');
			$paramLabel = AriXmlHelper::getAttribute($paramNode, 'label');
			$paramDescr = AriXmlHelper::getAttribute($paramNode, 'description');
			if ($paramDescr)
				$paramDescr = JText::_($paramDescr);
			
			if (empty($paramLabel))
				$paramLabel = '{$' . $paramId . '}';
				
			$params[$paramId] = array(
				'id' => $paramId,
				'label' => $paramLabel,
				'description' => $paramDescr
			);
		}

		return $params;
	}

	function _includeAssets()
	{
		static $loaded;
		
		if ($loaded)
			return ;

		$filePath = str_replace(DS == '\\' ? '/' : '\\', DS, dirname(__FILE__));
		if (strlen(JPATH_ROOT) > 1)
			$filePath = str_replace(JPATH_ROOT, '', $filePath);
			
		$uri = JURI::root(true) . str_replace(DS, '/', $filePath) . '/';
			
		$document = JFactory::getDocument();
		$document->addScript($uri . 'assets/texttemplate/texttemplate.js');
			
		$loaded = true;
	}
}