<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseQuery;
use Joomla\Utilities\ArrayHelper;

class OSMembershipModelOverrideMembers extends OSMembershipModelMembers
{
    public function __construct($config = [])
    {
        if (!isset($config['search_fields'])) {
            $config['search_fields'] = ['tbl.state', 'tbl.first_name', 'tbl.last_name', 'tbl.membership_id', 'tbl.email', 'b.title', 'c.username', 'c.name'];
        }

        parent::__construct($config);
    }
}
