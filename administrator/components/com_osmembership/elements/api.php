<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldApi extends FormField
{
	protected $type = 'api';

	public function getInput()
	{
		return '<button class="btn" onclick="window.open(\'https://www.paypal.com/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true\', \'\', \'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=540\');">GET API Access</button>';
	}
}
