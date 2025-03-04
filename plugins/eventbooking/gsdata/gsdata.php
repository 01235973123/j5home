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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgEventBookingGsdata extends CMSPlugin implements SubscriberInterface
{
	/**
	 * @var \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onDisplayEvents' => 'onDisplayEvents',
		];
	}

	/**
	 * Add google structured data for the rendered events
	 *
	 * @param   Event  $eventObj
	 *
	 * @return void
	 */
	public function onDisplayEvents(Event $eventObj): void
	{
		[$events] = array_values($eventObj->getArguments());

		$config = EventbookingHelper::getConfig();

		$output  = [];
		$rootUrl = EventbookingHelper::getRootUrl();

		foreach ($events as $event)
		{
			if ($event->location_name
				&& str_starts_with($event->location_name, 'http')
				&& filter_var($event->location_name, FILTER_VALIDATE_URL) !== false)
			{
				$onlineLocation = true;
			}
			else
			{
				$onlineLocation = false;
			}

			if (empty($event->location_address) && !$onlineLocation)
			{
				continue;
			}

			// Online location
			if ($onlineLocation)
			{
				$location = [
					'@type' => 'VirtualLocation',
					'url'   => $event->location_name,
				];
			}
			else
			{
				$location = [
					'@type'   => 'Place',
					'name'    => $event->location_name,
					'address' => $event->location_address,
				];
			}

			$data = [
				'@context'  => 'https://schema.org',
				'@type'     => 'Event',
				'name'      => $event->title,
				'startDate' => Factory::getDate($event->event_date)->format("Y-m-d\TH:i"),
				'url'       => $rootUrl . $event->url,
				'location'  => $location,
			];

			if (!empty($event->meta_description))
			{
				$data['description'] = $event->meta_description;
			}
			else
			{
				$data['description'] = $event->short_description;
			}

			if ((int) $event->event_end_date)
			{
				$data['endDate'] = Factory::getDate($event->event_end_date)->format("Y-m-d\TH:i");
			}

			if (!empty($event->image_url))
			{
				$data['image'] = $rootUrl . $event->image_url;
			}

			if ($event->individual_price > 0)
			{
				$offers = ['@type' => 'Offer'];

				if ($event->individual_price > 0)
				{
					$offers['price']         = EventbookingHelper::formatPrice($event->individual_price, $config);
					$offers['priceCurrency'] = $event->currency_code ?: $config->currency_code;
				}

				if ($event->registration_open)
				{
					if (!$event->event_capacity || $event->event_capacity > $event->total_registrants)
					{
						$offers['availability'] = 'https://schema.org/InStock';
					}
					elseif ($event->event_capacity <= $event->total_registrants)
					{
						$offers['availability'] = 'https://schema.org/SoldOut';
					}
				}

				$data['offers'] = $offers;
			}

			$output[] = $data;
		}

		// Add structure data to script
		if (count($output))
		{
			$this->app
				->getDocument()
				->getWebAssetManager()
				->addInlineScript(json_encode($output, JSON_UNESCAPED_UNICODE), [], ['type' => 'application/ld+json']);
		}
	}
}
