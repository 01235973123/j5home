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

class EventbookingViewCategoryHtml extends RADViewList
{
	/**
	 * Id of the active category
	 *
	 * @var int
	 */
	protected $categoryId;

	/**
	 * The active category
	 *
	 * @var stdClass
	 */
	protected $category = null;

	/**
	 * List of children categories
	 *
	 * @var array
	 */
	protected $categories = [];

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
	 * Intro text
	 *
	 * @var string
	 */
	protected $introText;

	/**
	 * Flag to mark of this is direct menu item to link to the view
	 *
	 * @var bool
	 */
	protected $isDirectMenuLink = false;

	/**
	 * Contain name of views which could be used to get menu item parameters for the current view
	 *
	 * @var array
	 */
	protected $paramsViews = ['categories', 'category'];

	/**
	 * Tell this view to inherit hide_children_events parameter from the parent categies view
	 *
	 * @var array[]
	 */
	protected $inheritParams = ['categories' => ['hide_children_events'], 'category' => ['hide_children_events']];

	/**
	 * Prepare the view data before it is rendered
	 *
	 * @return  void
	 * @throws  Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		// Use override menu item
		if ($this->params->get('menu_item_id') > 0)
		{
			$this->Itemid = $this->params->get('menu_item_id');
		}

		EventbookingHelper::callOverridableHelperMethod('Html', 'antiXSS', [$this->items, ['title', 'price_text']]);

		$app    = Factory::getApplication();
		$user   = Factory::getApplication()->getIdentity();
		$config = EventbookingHelper::getConfig();

		// If category id is passed, make sure it is valid and the user is allowed to access
		if ($categoryId = (int) $this->state->get('id'))
		{
			$this->category = $this->model->getCategory();

			if (empty($this->category))
			{
				throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
			}

			if (!$this->category->published)
			{
				throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
			}

			if (!in_array($this->category->access, $user->getAuthorisedViewLevels()))
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			if (!$this->params->get('hide_children_categories'))
			{
				$model = RADModel::getTempInstance('Categories', 'EventbookingModel', ['table_prefix' => '#__eb_']);

				$this->categories = $model->setState('limitstart', 0)
					->setState('limit', 0)
					->setState('filter_order', 'tbl.ordering')
					->setState('id', $categoryId)
					->getData();

				// Remove empty categories
				if (!$config->show_empty_cat)
				{
					$this->categories = $this->filterEmptyCategories($this->categories);
				}
			}
		}

		$active = $app->getMenu()->getActive();

		if (
			$active
			&& isset($active->query['option'], $active->query['view'])
			&& $active->query['option'] == 'com_eventbooking'
			&& $active->query['view'] == $this->getName())
		{
			// This is direct menu link to category view, so use the layout from menu item setup
			$this->isDirectMenuLink = true;
		}
		elseif ($this->input->getInt('hmvc_call') && $this->input->getCmd('layout'))
		{
			// Use layout from the HMVC call, in this case, it's from EB view module
		}
		elseif (!empty($this->category->layout))
		{
			$this->setLayout($this->category->layout);
		}
		else
		{
			$this->setLayout('default');
		}

		// Calculate page intro text
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$message     = EventbookingHelper::getMessages();

		if ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'intro_text' . $fieldSuffix}))
		{
			$introText = $message->{'intro_text' . $fieldSuffix};
		}
		elseif (EventbookingHelper::isValidMessage($message->intro_text))
		{
			$introText = $message->intro_text;
		}
		else
		{
			$introText = '';
		}

		if ($introText)
		{
			$introText = HTMLHelper::_('content.prepare', $introText);
		}

		if ($config->multiple_booking)
		{
			// Store last access category for routing back from shopping cart
			$app->getSession()->set('last_category_id', $categoryId);
		}

		$this->config          = $config;
		$this->categoryId      = $categoryId;
		$this->bootstrapHelper = EventbookingHelperBootstrap::getInstance();
		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->userId          = $user->id;
		$this->nullDate        = Factory::getContainer()->get('db')->getNullDate();
		$this->introText       = $introText;

		// Prepare display data
		EventbookingHelper::callOverridableHelperMethod('Data', 'prepareDisplayData', [
				$this->items,
				$this->categoryId,
				$this->config,
				$this->Itemid,
			]
		);

		// Prepare events and locations alias for routing
		$eventsAlias    = [];
		$locationsAlias = [];

		foreach ($this->items as $item)
		{
			if ($config->insert_event_id)
			{
				$eventsAlias[$item->id] = $item->id . '-' . $item->alias;
			}
			else
			{
				$eventsAlias[$item->id] = $item->alias;
			}

			$locationsAlias[$item->location_id] = $item->location_alias;
		}

		EventbookingHelperRoute::$eventsAlias    = array_filter($eventsAlias);
		EventbookingHelperRoute::$locationsAlias = array_filter($locationsAlias);

		// Prepare document meta data before it is rendered
		$this->prepareDocument();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$active = Factory::getApplication()->getMenu()->getActive();

		if ($active && $this->isDirectMenuLink($active))
		{
		}
		elseif ($this->category)
		{
			// Not direct menu item, use meta_keywords and menu-meta_description from the event if set
			if ($this->category->meta_keywords)
			{
				$this->params->set('menu-meta_keywords', $this->category->meta_keywords);
			}

			if ($this->category->meta_description)
			{
				$this->params->set('menu-meta_description', $this->category->meta_description);
			}

			if ($this->category->page_title)
			{
				$this->params->set('page_title', $this->category->page_title);
			}
		}

		// Page title
		if (!$this->params->get('page_title') && $this->category)
		{
			// Page title
			if ($this->category->page_title)
			{
				$pageTitle = $this->category->page_title;
			}
			else
			{
				$pageTitle = Text::_('EB_SUB_CATEGORIES_PAGE_TITLE');
				$pageTitle = str_replace('[CATEGORY_NAME]', $this->category->name, $pageTitle);
			}

			$this->params->set('page_title', $pageTitle);
		}

		// Page heading
		if (!$this->params->get('page_heading'))
		{
			if ($this->params->get('display_events_type') == 3)
			{
				$pageHeading = Text::_('EB_EVENTS_ARCHIVE');
			}
			elseif ($this->category)
			{
				$pageHeading = $this->category->page_heading ?: $this->category->name;
			}
			else
			{
				$pageHeading = Text::_('EB_EVENT_LIST');
			}

			$this->params->set('page_heading', $pageHeading);
		}

		// Meta keywords and description
		$this->params->def('menu-meta_keywords', $this->category ? $this->category->meta_keywords : '');
		$this->params->def('menu-meta_description', $this->category ? $this->category->meta_description : '');

		// Load required assets for the view
		$this->loadAssets();

		// Build pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();

		// Add Feed links to document
		if ($this->config->get('show_feed_link', 1))
		{
			$this->addFeedLinks();
		}

		// Intro text
		if (EventbookingHelper::isValidMessage($this->params->get('intro_text')))
		{
			$this->introText = HTMLHelper::_('content.prepare', $this->params->get('intro_text'));
		}

		// Add filter variables to pagination links if configured
		if ($this->config->get('show_search_bar', 0))
		{
			$this->setPaginationAdditionalParams();
		}
	}

	/**
	 * Load assets (javascript/css) for this specific view
	 *
	 * @return void
	 */
	protected function loadAssets()
	{
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

		if ($this->config->show_location_in_category_view || in_array($this->getLayout(), ['timeline', 'columns', 'grid']))
		{
			EventbookingHelperModal::iframeModal('a.eb-colorbox-map', 'eb-map-modal');
		}

		Factory::getApplication()->getDocument()->addScriptDeclaration(
			'
	        function cancelRegistration(registrantId)
	        {
	            var form = document.adminForm ;
	    
	            if (confirm("' . Text::_('EB_CANCEL_REGISTRATION_CONFIRM') . '"))
	            {
	                form.task.value = "registrant.cancel" ;
	                form.id.value = registrantId ;
	                form.submit() ;
	            }
	        }
	    '
		);
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		if ($this->input->getInt('hmvc_call'))
		{
			return;
		}

		$app    = Factory::getApplication();
		$active = $app->getMenu()->getActive();

		if (isset($active->query['view']) && in_array($active->query['view'], ['categories', 'category']))
		{
			$parentId = (int) $active->query['id'];

			if ($categoryId = $this->state->get('id'))
			{
				$pathway = $app->getPathway();
				$paths   = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);

				for ($i = count($paths) - 1; $i >= 0; $i--)
				{
					$path    = $paths[$i];
					$pathUrl = EventbookingHelperRoute::getCategoryRoute($path->id, $this->Itemid);
					$pathway->addItem($path->name, $pathUrl);
				}
			}
		}
	}

	/**
	 * Set meta data for the document
	 *
	 * @return void
	 */
	protected function setDocumentMetadata()
	{
		parent::setDocumentMetadata();

		if (!empty($this->category->image) && file_exists(JPATH_ROOT . '/' . $this->category->image))
		{
			Factory::getApplication()->getDocument()->setMetaData('og:image', Uri::root() . $this->category->image, 'property');
		}
	}

	/**
	 * Remove categories with no events from the list
	 *
	 * @param   array  $categories
	 *
	 * @return array
	 */
	protected function filterEmptyCategories($categories)
	{
		return array_values(
			array_filter($categories, function ($category) {
				return $category->total_events > 0;
			})
		);
	}

	/**
	 * @param   \Joomla\CMS\Menu\MenuItem  $active
	 *
	 * @return bool
	 */
	protected function isDirectMenuLink($active)
	{
		$result = parent::isDirectMenuLink($active);

		if (!$result)
		{
			return false;
		}

		$id         = $active->query['id'] ?? 0;
		$id         = (int) $id;
		$categoryId = (int) $this->input->getInt('id', 0);

		return $id === $categoryId;
	}

	/**
	 * Add additional params to pagination in case some filters are selected on search bar
	 *
	 * @return void
	 */
	protected function setPaginationAdditionalParams(): void
	{
		if ($this->state->search)
		{
			$this->pagination->setAdditionalUrlParam('search', $this->state->search);
		}

		if ($this->state->location_id)
		{
			$this->pagination->setAdditionalUrlParam('location_id', $this->state->location_id);
		}

		if ($this->state->filter_duration)
		{
			$this->pagination->setAdditionalUrlParam('filter_duration', $this->state->filter_duration);
		}

		if ($this->state->category_id)
		{
			$this->pagination->setAdditionalUrlParam('category_id', $this->state->category_id);
		}

		if ($this->state->filter_city)
		{
			$this->pagination->setAdditionalUrlParam('filter_city', $this->state->filter_city);
		}

		if ($this->state->filter_state)
		{
			$this->pagination->setAdditionalUrlParam('filter_state', $this->state->filter_state);
		}
	}
}
