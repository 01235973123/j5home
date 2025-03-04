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

class JElementRichcontentliteral extends JElement
{
	var	$_name = 'Richcontentliteral';

	function fetchElement($name, $value, &$node, $control_name)
	{
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES);
        
        $frameId = $control_name . $name;
        
        $this->addScript($frameId, $value);

		return !empty($value)
			? sprintf(
				'<div class="ari-el-label ari-el-richcontent"><iframe id="%1$s" name="%1$s" frameborder="0" src="index.php?option=com_ariquiz&view=richcontent&tmpl=component"></iframe></div>',
				$frameId)
			: '<div class="ari-el-label ari-el-richcontent">&nbsp;</div>'; 
	}

	function addScript($frameId, $value)
	{
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration(sprintf(
			';YAHOO.util.Event.onDOMReady(function() { YAHOO.util.Event.on(window.frames["%1$s"], "load", function() { var frm = Dom.get("%1$s");frm.contentWindow.YAHOO.ARISoft.page.pageManager.contentManager.setContent(%2$s); }); });',
			$frameId,
			json_encode(html_entity_decode($value, ENT_COMPAT, 'UTF-8'))
		));
	}
}