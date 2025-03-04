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

class JElementSplitter extends JElement
{
	var	$_name = 'Splitter';

	function fetchElement($name, $value, &$node, $control_name)
	{
		return '<hr class="ari-el-splitter" />';
	}
}