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

class JFormFieldEBEventsType extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'ebeventstype';

	protected $layout = 'joomla.form.field.list-fancy-select';

	protected function getOptions()
	{
		/* @var \Joomla\Database\DatabaseDriver $db */
		$db    = Factory::getContainer()->get('db');
		$query = $db->getQuery(true)
			->select('config_value')
			->from('#__eb_configs')
			->where('config_key = "hide_past_events"');
		$db->setQuery($query);
		$hidePastEvents = (int) $db->loadResult();

		$options = [];

		if ($hidePastEvents)
		{
			$options[] = HTMLHelper::_('select.option', 0, Text::_('Use Global (Upcoming Events)'));
		}
		else
		{
			$options[] = HTMLHelper::_('select.option', 0, Text::_('Use Global (All Events)'));
		}

		$options[] = HTMLHelper::_('select.option', 1, Text::_('All Events'));

		$options[] = HTMLHelper::_('select.option', 2, Text::_('Upcoming Events'));

		$options[] = HTMLHelper::_('select.option', 3, Text::_('Past Events'));

		return $options;
	}
}
