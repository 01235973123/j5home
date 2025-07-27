<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use OSSolution\EventBooking\Admin\Event\Event\AfterSaveEvent;
use OSSolution\EventBooking\Admin\Event\Events\AfterDeleteEvents;

class EventbookingModelOverrideEvent extends EventbookingModelEvent
{
    protected function beforePublish($cid, $state)
    {
        parent::beforePublish($cid, $state);

        if ($state == 1) {
            $config = EventbookingHelper::getConfig();

            foreach ($cid as $id) {
                /* @var EventbookingTableEvent $row */
                $row = $this->getTable();

                if (!$row->load($id)) {
                    continue;
                }

                if (!$row->created_by) {
                    continue;
                }

                if ($row->published) {
                    continue;
                }

                EventbookingHelper::callOverridableHelperMethod('Mail', 'sendEventApprovedEmail', [$row, $config]);
            }
        }
    }

    public function store($input, $ignore = [])
    {
        parent::store($input, $ignore);
        $config = EventbookingHelper::getConfig();

        /* @var EventbookingTableEvent $row */
        $row       = $this->getTable();
        $published = true;
        $isNew     = true;

        if ($this->state->id) {
            $isNew = false;
            $row->load($this->state->id);
            $published = $row->published;
        }

        if (!$isNew && !$published && $row->published && $row->created_by) {
            EventbookingHelper::callOverridableHelperMethod('Mail', 'sendEventApprovedEmail', [$row, $config]);
        }
    }
}
