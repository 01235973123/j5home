<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

if (version_compare(JVERSION, '4.4.99', '>'))
{
	JLoader::registerAlias('JFormFieldList', '\\Joomla\\CMS\\Form\\Field\\ListField');
}
else
{
	FormHelper::loadFieldClass('list');
}

class JFormFieldEBLocation extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'eblocation';

	protected function getOptions()
	{
		$user      = Factory::getApplication()->getIdentity();
		$config    = EventbookingHelper::getConfig();

		/* @var \Joomla\Database\DatabaseDriver $db */
		$db        = Factory::getContainer()->get('db');
		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_SELECT_LOCATION'));
		$query     = $db->getQuery(true)
			->select('id, name')
			->from('#__eb_locations')
			->where('published = 1')
			->order('name');

		if (!$user->authorise('core.admin', 'com_eventbooking') && !$config->show_all_locations_in_event_submission_form)
		{
			$query->where('user_id = ' . (int) $user->id);
		}

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $location)
		{
			$options[] = HTMLHelper::_('select.option', $location->id, $location->name);
		}

		return $options;
	}
}
