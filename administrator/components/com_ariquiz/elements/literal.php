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

class JElementLiteral extends JElement
{
	var	$_name = 'Literal';

	function fetchElement($name, $value, &$node, $control_name)
	{
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		return sprintf(
			'<div class="ari-el-label">%1$s</div>',
			html_entity_decode($value)
		); 
	}
}