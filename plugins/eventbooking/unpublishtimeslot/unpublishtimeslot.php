<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Codex
 * @copyright          Copyright (C) 2010 - 2024
 * @license            GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgEventbookingUnpublishTimeslot extends CMSPlugin implements SubscriberInterface
{
    protected $app;
    protected $db;

    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterStoreRegistrant'   => 'onAfterStoreRegistrant',
            'onRegistrationCancel'     => 'onChangeRegistrant',
            'onBeforeDeleteRegistrant' => 'onChangeRegistrant',
        ];
    }

    public function onAfterStoreRegistrant(Event $eventObj): void
    {
        [$row] = array_values($eventObj->getArguments());
        $this->unpublishOtherEvents($row->event_id);
    }

    public function onChangeRegistrant(Event $eventObj): void
    {
        [$row] = array_values($eventObj->getArguments());
        $this->publishEventsIfEmpty($row->event_id);
    }

    private function unpublishOtherEvents(int $eventId): void
    {
        $event = EventbookingHelperDatabase::getEvent($eventId);

        if (!$event || $event->auto_unpublish_timeslot) {
            return;
        }

        $excluded = $this->params->get('exclude_category_ids', []);
        if ($excluded && in_array($event->main_category_id, $excluded)) {
            return;
        }

        $db    = $this->db;
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__eb_events')
            ->where('id != ' . (int) $eventId)
            ->where('event_date = ' . $db->quote($event->event_date))
            ->where('event_end_date = ' . $db->quote($event->event_end_date))
            ->where('published = 1');
        $db->setQuery($query);
        $ids = $db->loadColumn();

        if ($ids) {
            $query->clear()
                ->update('#__eb_events')
                ->set('published = 0')
                ->whereIn('id', array_map('intval', $ids));
            $db->setQuery($query);
            $db->execute();
        }
    }

    private function publishEventsIfEmpty(int $eventId): void
    {
        $db    = $this->db;
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__eb_registrants')
            ->where('event_id = ' . (int) $eventId)
            ->where('(published = 0 OR published = 1)');
        $db->setQuery($query);

        if ((int) $db->loadResult() > 1) {
            return;
        }

        $event = EventbookingHelperDatabase::getEvent($eventId);
        if (!$event || $event->auto_unpublish_timeslot) {
            return;
        }

        $excluded = $this->params->get('exclude_category_ids', []);
        if ($excluded && in_array($event->main_category_id, $excluded)) {
            return;
        }

        $query->clear()
            ->select('id')
            ->from('#__eb_events')
            ->where('event_date = ' . $db->quote($event->event_date))
            ->where('event_end_date = ' . $db->quote($event->event_end_date))
            ->where('published = 0');
        $db->setQuery($query);
        $ids = array_diff($db->loadColumn(), [(int) $eventId]);

        if ($ids) {
            $query->clear()
                ->update('#__eb_events')
                ->set('published = 1')
                ->whereIn('id', array_map('intval', $ids));
            $db->setQuery($query);
            $db->execute();
        }
    }
}
