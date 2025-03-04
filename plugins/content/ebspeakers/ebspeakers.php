<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;

class plgContentEBSpeakers extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * Display event detail in the article
	 *
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param $limitstart
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
	{
		if (!str_contains($article->text, '{ebspeakers'))
		{
			return true;
		}

		$regex         = "#{ebspeakers (\d+)}#s";
		$article->text = preg_replace_callback($regex, [&$this, 'displayEventSpeakers'], $article->text);

		return true;
	}

	/**
	 * Display detail information of the given event
	 *
	 * @param $matches
	 *
	 * @return string
	 * @throws Exception
	 */
	private function displayEventSpeakers($matches)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		$eventId = (int) $matches[1];

		$db = $this->db;

		$query = $db->getQuery(true)
			->select('a.*')
			->from('#__eb_speakers AS a');

		if ($eventId)
		{
			$query->innerJoin('#__eb_event_speakers AS b ON a.id = b.speaker_id')
				->where('b.event_id = ' . $eventId)
				->order('b.id');
		}
		else
		{
			$query->order('a.ordering');
		}

		if ($fieldSuffix = EventbookingHelper::getFieldSuffix())
		{
			EventbookingHelperDatabase::getMultilingualFieldsUseDefaultLanguageData(
				$query,
				['a.name', 'a.title', 'a.url', 'a.description'],
				$fieldSuffix
			);
		}

		$db->setQuery($query);
		$speakers = $db->loadObjectList();

		if (!count($speakers))
		{
			return '';
		}

		$layoutFile = $this->params->get('layout', 'speakers') . '.php';

		return EventbookingHelperHtml::loadCommonLayout('plugins/' . $layoutFile, ['speakers' => $speakers, 'params' => $this->params]);
	}

	/**
	 * Override registerListeners method to only register listeners if needed
	 *
	 * @return void
	 */
	public function registerListeners()
	{
		if (!ComponentHelper::isEnabled('com_eventbooking'))
		{
			return;
		}

		if (!$this->app->isClient('site'))
		{
			return;
		}

		parent::registerListeners();
	}
}
