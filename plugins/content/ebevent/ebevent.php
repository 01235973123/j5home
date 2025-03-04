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

class plgContentEBEvent extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

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
		if (!str_contains($article->text, '{ebevent'))
		{
			return true;
		}

		$regex         = "#{ebevent (\d+)}#s";
		$article->text = preg_replace_callback($regex, [&$this, 'displayEvent'], $article->text);

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
	private function displayEvent($matches)
	{
		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		EventbookingHelper::loadLanguage();

		$id = $matches[1];

		$event = EventbookingHelperDatabase::getEvent($id);

		// Invalid event or the event was unpublished, return to prevent the article from being accessible
		if (!$event || !$event->published)
		{
			return '';
		}

		$request = [
			'option'    => 'com_eventbooking',
			'view'      => 'event',
			'id'        => $id,
			'limit'     => 0,
			'hmvc_call' => 1,
			'Itemid'    => EventbookingHelper::getItemid(),
		];
		$input   = new RADInput($request);
		$config  = require JPATH_ADMINISTRATOR . '/components/com_eventbooking/config.php';
		ob_start();

		//Initialize the controller, execute the task
		RADController::getInstance('com_eventbooking', $input, $config)
			->execute();

		return '<div class="clearfix"></div>' . ob_get_clean();
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
