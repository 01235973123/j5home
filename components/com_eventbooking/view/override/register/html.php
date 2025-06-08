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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class EventbookingViewOverrideRegisterHtml extends EventbookingViewRegisterHtml
{
	use EventbookingViewCaptcha;

	protected function displayGroupForm($event, $input)
	{
		if (isset($_GET['individual'])) {
			$event->max_group_number = 1;
			$event->min_group_number = 1;
		}

		parent::displayGroupForm($event, $input);
	}

	protected function setPageTitle($event, $layout)
	{
		parent::setPageTitle($event, $layout);
		if (isset($_GET['individual'])) {
			$pageTitle = '';
			$active    = Factory::getApplication()->getMenu()->getActive();

			// Try to get page title from menu item settings
			if (
				$active
				&& isset($active->query['view'], $active->query['event_id'])
				&& $active->query['view'] == 'register'
				&& $active->query['event_id'] == $event->id
			) {
				$params = $active->getParams();

				$pageTitle = $params->get('page_title');
			}

			// If page title not set from menu item parameter, use language item
			if (!$pageTitle) {
				$config   = EventbookingHelper::getConfig();
				$language = Factory::getApplication()->getLanguage();

				$pageTitle = Text::_('EB_INDIVIDUAL_REGISTRATION_PAGE_TITLE');

				$pageTitle = str_replace('[EVENT_TITLE]', $event->title, $pageTitle);
				$pageTitle = str_replace('[EVENT_DATE]', HTMLHelper::_('date', $event->event_date, $config->event_date_format, null), $pageTitle);
				Factory::getApplication()->getDocument()->setTitle($pageTitle);
			}
		}
	}

	protected function setRegistrationFormHeadingAndMessage($layout)
	{
		parent::setRegistrationFormHeadingAndMessage($layout);
		if (!$this->waitingList && isset($_GET['individual'])) {
			$pageHeading = Text::_('EB_INDIVIDUAL_REGISTRATION');
			$messageKey  = 'registration_form_message' . $this->fieldSuffix;

			if ($this->fieldSuffix && EventbookingHelper::isValidMessage($this->event->{$messageKey})) {
				$msg = $this->event->{$messageKey};
			} elseif ($this->fieldSuffix && EventbookingHelper::isValidMessage($this->message->{$messageKey})) {
				$msg = $this->message->{$messageKey};
			} elseif (EventbookingHelper::isValidMessage($this->event->{$messageKey})) {
				$msg = $this->event->{$messageKey};
			} else {
				$msg = $this->message->{$messageKey};
			}

			$msg         = EventbookingHelper::replaceUpperCaseTags($msg, $replaces);
			$pageHeading = EventbookingHelper::replaceUpperCaseTags($pageHeading, $replaces);

			$this->formMessage = $msg;
			$this->pageHeading = $pageHeading;
		}
	}
}
