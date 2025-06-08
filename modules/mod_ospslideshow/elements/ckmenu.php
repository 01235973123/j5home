<?php
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * @copyright	Copyright (C) 2016 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('cklist');

// Import the com_menus helper.
require_once realpath(JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php');


class JFormFieldCkmenu extends JFormFieldCklist
{

	public $type = 'ckmenu';

	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), HTMLHelper::_('menu.menus'));

		return $options;
	}
}
