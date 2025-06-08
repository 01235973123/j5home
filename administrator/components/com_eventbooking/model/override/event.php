<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Image\Image;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;

class EventbookingModelOverrideEvent extends EventbookingModelEvent
{
    protected function afterStore($row, $input, $isNew)
    {
        parent::afterStore($row, $input, $isNew);

        $app  = Factory::getApplication();
        $user = Factory::getApplication()->getIdentity();

        if ($app->isClient('administrator') || $user->authorise('core.admin')) {
            $data = $input->getData(RAD_INPUT_ALLOWRAW);
        } else {
            $data = $input->getData();
        }

        if (isset($data['ids_event_published'])) {
            $idsEventPublished = implode(',', $data['ids_event_published']);
            $row->ids_event_published = $idsEventPublished;
        } else {
            $row->ids_event_published = '';
        }

        $row->store();
    }
}
