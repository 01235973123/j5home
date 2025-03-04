<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

AriKernel::import('Xml.XmlHelper');

class JElementHeader extends JElement
{
	var	$_name = 'Header';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$options = array(JText::_($value));
		foreach ($node->children() as $option)
		{
			$options[] = AriXmlHelper::getData($option);
		}
		
		return sprintf('<div style="font-weight: bold; font-size: 120%%; color: #FFF; background-color: #7A7A7A; padding: 2px 0; text-align: center;">%s</div>', call_user_func_array('sprintf', $options));
	}
}
?>