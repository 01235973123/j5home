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

class JElementLabel extends JElement
{
	var	$_name = 'Label';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$ctrlName = $control_name . '[' . $name .']';
		
		$size = AriXmlHelper::getAttribute($node, 'size', '');
		if ($size)
			$size = 'size="' . $size . '"';
			
		$class = AriXmlHelper::getAttribute($node, 'class', '');
		if ($class)
			$class = 'class="' . $class . '"';

        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return sprintf(
			'<div class="ari-el-label">%6$s</div><input type="hidden" name="%1$s" id="%2$s" value="%3$s" %4$s %5$s />',
			$ctrlName,
			$control_name . $name,
			$value ? html_entity_decode($value) : '',
			$class,
			$size,
			($value || $value === '0') ? html_entity_decode($value) : '&nbsp;'); 
	}
}