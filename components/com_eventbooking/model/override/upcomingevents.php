<?php

/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\Database\DatabaseQuery;

class EventbookingModelOverrideUpcomingevents extends EventbookingModelUpcomingevents
{
    /**
     * Instantiate the model.
     *
     * @param   array  $config  configuration data for the model
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->state->insert('speaker_id', 'int', 0);
    }

    protected function buildQueryWhere(DatabaseQuery $query)
    {
        parent::buildQueryWhere($query);

        $state  = $this->getState();

        if ($state->speaker_id) {
            $query->where('tbl.id IN (SELECT event_id FROM #__eb_event_speakers WHERE speaker_id = ' . (int) $state->speaker_id . ')');
        }

        return $this;
    }
}
