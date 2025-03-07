<?php

/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2013 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class OSMembershipModelMessage extends MPFModel
{
	/**
	 * Store the messages data
	 *
	 * @param   array  $data
	 */
	public function store($data)
	{
		$db  = $this->getDbo();
		$row = $this->getTable('Message');
		$db->truncateTable('#__osmembership_messages');
		foreach ($data as $key => $value)
		{
			$row->id          = 0;
			$row->message_key = $key;
			$row->message     = $value;
			$row->store();
		}
	}
}
