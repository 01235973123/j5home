<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Html.GenericParameter');
AriKernel::import('Web.HtmlHelper');
AriKernel::import('Xml.XmlHelper');

class AriMassEditParameter extends AriGenericParameter
{
	var $_massEditKey = 'massedit';
	
	function isAcceptableParam($node)
	{
		if (empty($node))
			return false;
	
		$isMassEdit = AriXmlHelper::getAttribute($node, $this->_massEditKey);
			
		return !empty($isMassEdit);
	}

	function render($title, $name = 'params', $group = '_default', $options = array('paramsPerRow' => 1))
	{
		$html = parent::render($title, $name, $group, $options);

		if (empty($html))
			return $html;
			
		$chkManageId = uniqid('me', false);
		$html = $this->updateParametersLabels($html);
		$html = sprintf(
			'<fieldset class="ari-settings-group">%1$s',
			!empty($title) 
				? sprintf('<legend><label for="%1$s">%2$s</label> <input type="checkbox" id="%1$s" class="text_area ari-settings-switcher" checked="checked" /></legend>%3$s</fieldset>',
					$chkManageId,
					$title,
					$html)
				: sprintf('%1$s</fieldset>',
					$html)
		);
		
		return $html;
	}
	
	function updateParametersLabels($content)
	{
		$content = preg_replace_callback('/<label[^>]*>/i', array($this, 'updateParametersLabelCallback'), $content);
		
		return $content;
	}
	
	function updateParametersLabelCallback($matches)
	{
		if (empty($matches[0]))
			return '';
			
		$attrs = AriHtmlHelper::extractAttrs($matches[0]);
		if (!empty($attrs['class']))
			$attrs['class'] .= ' ';
		else 
			$attrs['class'] = '';
	
		$attrs['class'] .= 'ari-dashed-line ari-setting-label';
		
		return '<label ' . AriHtmlHelper::getAttrStr($attrs) . '>';
	}
}