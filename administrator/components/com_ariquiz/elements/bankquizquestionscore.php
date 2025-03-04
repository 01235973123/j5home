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

AriKernel::import('Xml.XmlHelper');

class JElementBankquizquestionscore extends JElement
{
	var	$_name = 'Bankquizquestionscore';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$bankGroup = AriXmlHelper::getAttribute($node, 'bank_group', '');
		$scoreField = AriXmlHelper::getAttribute($node, 'score_field', 'Score');

		$score = $value;
		$overridenScore = floatval($this->_parent->get($scoreField, '', $bankGroup));
		$isScoreOverriden = !empty($overridenScore);

		$size = AriXmlHelper::getAttribute($node, 'size', '');
		if ($size)
			$size = 'size="' . $size . '"';
			
		$class = AriXmlHelper::getAttribute($node, 'class', '');
		if ($class)
			$class = 'class="' . $class . '"';

		$ctrlName = $control_name . '[' . $name . ']';
		$ctrlId = $control_name . $name;
		$chkId = 'chk_' . $control_name . $name;
		
		$this->includeScript();
		
		return sprintf(
			'<input type="text" name="%1$s" id="%2$s" value="%3$s" ' . $class . ' ' . $size . '%4$s _initValue="%5$s" />&nbsp;&nbsp;' .
			'<input type="checkbox" id="%6$s"%8$s onclick="YAHOO.ARISoft.page.changeScoreOverride(\'%2$s\', this.checked)" />&nbsp;&nbsp;<label for="%6$s" style="clear:none;">%7$s</label>',
			$ctrlName,
			$ctrlId,
			(!$isScoreOverriden ? $value : $overridenScore),
			(!$isScoreOverriden ? ' disabled="disabled" ' : ''),
			$value,
			$chkId,
			JText::_('COM_ARIQUIZ_LABEL_OVERRIDE'),
			$isScoreOverriden ? ' checked="checked"' : ''
		); 
	}
	
	function includeScript()
	{
		static $isLoaded;
		
		if ($isLoaded)
			return ;
		
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('
		YAHOO.ARISoft.page.changeScoreOverride = function(tbxScore, isOverride) {
			tbxScore = YAHOO.util.Dom.get(tbxScore);
			tbxScore.disabled = !isOverride;
			if (!isOverride)
				tbxScore.value = tbxScore.getAttribute("_initValue"); 
		};');
		
		$isLoaded = true;
	}
}