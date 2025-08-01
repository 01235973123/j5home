<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class EventbookingViewLocationHtml extends RADViewHtml
{
	/**
	 * The location data on add/edit location form
	 *
	 * @var stdClass
	 */
	protected $item;

	/**
	 * The list of select lists
	 *
	 * @var array
	 */
	protected $lists;

	/**
	 * Location data
	 *
	 * @var stdClass
	 */
	protected $location;

	/**
	 * List of events from the location
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Pagination object
	 *
	 * @var \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Component config
	 *
	 * @var RADConfig
	 */
	protected $config;

	/**
	 * Twitter bootstrap helper
	 *
	 * @var EventbookingHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * ID of current user
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * The access levels of the current user
	 *
	 * @var array
	 */
	protected $viewLevels;

	/**
	 * The value represent database null date
	 *
	 * @var string
	 */
	protected $nullDate;

	/**
	 * Display events from a location
	 */
	public function display()
	{
		$layout = $this->getLayout();

		if (in_array($this->getLayout(), ['form', 'popup']))
		{
			EventbookingHelperJquery::loadjQuery();

			$this->displayForm();

			return;
		}

		// Use default layout if layout is not one of supported layout
		if (!in_array($this->getLayout(), ['table', 'timeline', 'default']))
		{
			$this->setLayout('default');
		}

		$location = EventbookingHelperDatabase::getLocation($this->input->getInt('location_id'));

		// Make sure this is a valid location
		if (empty($location))
		{
			throw new Exception(Text::_('EB_LOCATION_NOT_FOUND'), 404);
		}

		// Set the layout to display events from this location
		if (($layout == '' || $layout == 'default') && !empty($location->layout))
		{
			$this->setLayout($location->layout);
		}

		$user = Factory::getApplication()->getIdentity();

		$this->location        = $location;
		$this->items           = $this->model->getData();
		$this->pagination      = $this->model->getPagination();
		$this->config          = EventbookingHelper::getConfig();
		$this->bootstrapHelper = EventbookingHelperBootstrap::getInstance();

		$this->nullDate   = Factory::getContainer()->get('db')->getNullDate();
		$this->viewLevels = $user->getAuthorisedViewLevels();
		$this->userId     = $user->id;

		EventbookingHelper::callOverridableHelperMethod('Html', 'antiXSS', [$this->items, ['title', 'price_text']]);

		// Prepare display data
		EventbookingHelper::callOverridableHelperMethod('Data', 'prepareDisplayData', [$this->items, 0, $this->config, $this->Itemid]);

		$this->prepareDocument();

		parent::display();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		// Page title
		if (!$this->params->get('page_title'))
		{
			$this->params->set('page_title', $this->location->name);
		}

		// Page heading
		$this->params->def('page_heading', $this->location->name);

		$this->loadAssets();

		// Build document pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();
	}

	/**
	 * Load assets (javascript/css) for this specific view
	 *
	 * @return void
	 */
	protected function loadAssets()
	{
		// Load requires javascript libraries
		if ($this->config->multiple_booking)
		{
			if ($this->deviceType == 'mobile')
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '100%', '450px', 'false', 'false');
			}
			else
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', 'false', 'false', 'false', 'false');
			}
		}

		if ($this->config->show_list_of_registrants)
		{
			EventbookingHelperModal::iframeModal('a.eb-colorbox-register-lists', 'eb-registrant-lists-modal');
		}

		if ($this->config->show_location_in_category_view || $this->getLayout() == 'timeline')
		{
			EventbookingHelperModal::iframeModal('a.eb-colorbox-map', 'eb-map-modal');
		}
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		Factory::getApplication()->getPathway()->addItem($this->location->name);
	}

	/**
	 * Display Form to allow adding location for event
	 *
	 * @throws Exception
	 */
	protected function displayForm()
	{
		$user = Factory::getApplication()->getIdentity();

		if (!$user->authorise('eventbooking.addlocation', 'com_eventbooking'))
		{
			if (!$user->id)
			{
				$this->requestLogin();
			}
			else
			{
				$app = Factory::getApplication();
				$app->enqueueMessage(Text::_('EB_NO_PERMISSION'), 'error');
				$app->redirect(Uri::root(), 403);
			}
		}

		$document = Factory::getApplication()->getDocument();
		$document->addScriptDeclaration(
			'var siteUrl = "' . EventbookingHelper::getSiteUrl() . '";'
		);

		$config = EventbookingHelper::getConfig();
		$item   = $this->model->getLocationData();

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('EB_SELECT_COUNTRY'), 'value', 'text');
		$countries = EventbookingHelperDatabase::getAllCountries();

		foreach ($countries as $country)
		{
			$options[] = HTMLHelper::_('select.option', $country->name, $country->name);
		}

		$lists['country']   = HTMLHelper::_('select.genericlist', $options, 'country', '', 'value', 'text', $item->country);
		$lists['published'] = HTMLHelper::_('select.booleanlist', 'published', '', $item->id ? $item->published : 1);

		$this->item   = $item;
		$this->lists  = $lists;
		$this->config = $config;

		$this->bootstrapHelper = EventbookingHelperBootstrap::getInstance();

		parent::display();
	}
}
