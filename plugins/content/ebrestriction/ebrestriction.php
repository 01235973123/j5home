<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

class plgContentEBRestriction extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var  \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * Handle content prepare event
	 *
	 * @param $context
	 * @param $row
	 * @param $params
	 * @param $page
	 *
	 * @return bool
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		// Check whether the plugin should process or not
		if (StringHelper::strpos($row->text, '{ebrestriction') === false)
		{
			return true;
		}

		// Search for this tag in the content
		$regex     = '#{ebrestriction ids="(.*?)"}(.*?){/ebrestriction}#s';
		$row->text = preg_replace_callback($regex, [&$this, 'processRestriction'], $row->text);

		return true;
	}

	/**
	 * Process content restriction
	 *
	 * @param   array  $matches
	 *
	 * @return string
	 */
	private function processRestriction($matches)
	{
		$requiredEventIds = $matches[1];
		$protectedText    = $matches[2];
		$registeredEvents = $this->getRegisteredEvents();

		if ($this->isEventOwner($requiredEventIds))
		{
			return $protectedText;
		}

		if (count($registeredEvents) == 0)
		{
			return '';
		}
		elseif ($requiredEventIds == '*')
		{
			return $protectedText;
		}

		if ($requiredEventIds == '0')
		{
			$option = $this->app->getInput()->getCmd('option');
			$view   = $this->app->getInput()->getCmd('view');

			if ($option === 'com_eventbooking' && $view === 'event')
			{
				$requiredEventIds = $this->app->getInput()->getInt('id', 0);
			}
		}

		$requiredEventIds = array_filter(ArrayHelper::toInteger(explode(',', $requiredEventIds)));

		if (count(array_intersect($requiredEventIds, $registeredEvents)))
		{
			return $protectedText;
		}

		return '';
	}

	/**
	 *  Get list of events which the current user has registered
	 *
	 * @return array
	 */
	private function getRegisteredEvents()
	{
		$user = $this->app->getIdentity();

		if ($user->id)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('event_id')
				->from('#__eb_registrants')
				->where('published=1')
				->where('user_id=' . $user->id);
			$db->setQuery($query);

			return $db->loadColumn();
		}

		return [];
	}

	/**
	 * @param   string  $requiredEventIds
	 */
	private function isEventOwner($requiredEventIds)
	{
		$user             = $this->app->getIdentity();
		$requiredEventIds = array_filter(ArrayHelper::toInteger(explode(',', $requiredEventIds)));

		if ($user->id && count($requiredEventIds))
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('created_by')
				->from('#__eb_events')
				->whereIn('id', $requiredEventIds);
			$db->setQuery($query);
			$createdBys = $db->loadColumn();

			if (in_array($user->id, $createdBys))
			{
				return true;
			}
		}

		return false;
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
