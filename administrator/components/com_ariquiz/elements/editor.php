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

use Joomla\CMS\Editor\Editor;

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

AriKernel::import('Xml.XmlHelper');
AriKernel::import('Joomla.Html.HtmlHelper');

if (J3_5 && !J4) JHtml::_('behavior.core');

class JElementEditor extends JElement
{
    var $_node = null;
	var	$_name = 'Editor';

	function fetchElement($name, $value, &$node, $control_name)
	{
        $this->_node = $node;

		$ctrlId = $control_name . $name;
		$ctrlName = $control_name . '[' . $name .']';
		$width = AriXmlHelper::getAttribute($node, 'width', '100%');
		$height = AriXmlHelper::getAttribute($node, 'height', '250');
		$rows = AriXmlHelper::getAttribute($node, 'rows', '20');
		$cols = AriXmlHelper::getAttribute($node, 'cols', '60');

		$needHack = (strpos($ctrlName, '[') !== false);
		$correctedCtrlName = $this->getCorrectedName($ctrlName);

		$editor = $this->getEditor();
		$html = $editor->display(
			$correctedCtrlName, 
			htmlspecialchars($value),
			$width,
			$height,
			$cols,
			$rows);

		if ($needHack)
		{
			AriJoomlaHtmlHelper::loadJQuery();

			$html .= sprintf('<textarea name="%1$s" id="%2$s" style="display: none !important;"></textarea>',
				$ctrlName,
				$ctrlId);
				
			$document =& JFactory::getDocument();
			$document->addScriptDeclaration(sprintf('jQuery(function() {
					var oldSubmitHandler = Joomla.submitform;						 	
					Joomla.submitform = function() {
						var val = %2$s;
						if (typeof(jQuery) !== "undefined")
							jQuery("#%1$s").val(typeof(val) != "undefined" ? val : "");
						else
							$("%1$s").value = typeof(val) != "undefined" ? val : "";
						oldSubmitHandler.apply(this, arguments);
					}
				});',
				$ctrlId,
				$this->getContent($correctedCtrlName))
			);
		}

		return '<div class="el-editor">' . $html . '</div>';
	}
	
	function getEditor()
	{
		return Editor::getInstance();
	}

	function getCorrectedName($name)
	{
		return str_replace(array('[', ']'), array('_', ''), $name);
	}
	
	function getContent($correctedName)
	{
		if (J4) {
			return sprintf(
				'Joomla.editors.instances["%s"].getValue();',
				$correctedName,
			);
		}

		$editor = $this->getEditor();
		$content = $editor->getContent($correctedName);
			
		$content = str_replace('tinyMCE.getContent()', sprintf('tinyMCE.getContent("%s")', $correctedName), $content);
		$content = str_replace('tinyMCE.activeEditor.getContent()', sprintf('tinyMCE.get("%s").getContent()', $correctedName), $content);
		$content = str_replace(
			sprintf('JContentEditor.getContent(\'%s\')', $correctedName), 
			sprintf('(tinyMCE.get("%s") ? tinyMCE.get("%1$s").getContent() : JContentEditor.getContent(\'%1$s\'))', $correctedName),
			$content);
			
		return $content;
	}
}