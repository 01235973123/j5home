<?php 
/*------------------------------------------------------------------------
# payment.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

?>
<table style="width:100%;">
	<td width="50%" valign="top">
		<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/offering_paid.php');?>
	</td>
	<td width="50%" valign="top">
		<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/management.php');?>
	</td>
</table>