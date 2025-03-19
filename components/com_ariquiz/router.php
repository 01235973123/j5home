<?php
/*
 * ARI Quiz Router
 *
 * @package		ARI Quiz Router
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/helper.php';

$config = AriQuizHelper::getConfig();
$sefEnabled = (bool)$config->get('EnableSEF');

if ($sefEnabled)
	require_once dirname(__FILE__) . '/router/router_ariquiz.php';
else
	require_once dirname(__FILE__) . '/router/router_empty.php';