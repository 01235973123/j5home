<?php
use Joomla\CMS\Factory;
/**
 * @package 	mod_featuredagents - Featured agents
 * @version		1.0
 * @created		July 2013

 * @author		Dang Thuc Dam
 * @email		damdt@joomservices.com
 * @website		http://joomdonation.com
 * @copyright	Copyright (C) 2013 Joomdonation. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die;

abstract class modFeaturedAgentsHelper
{
	public static function getList(&$params)
	{
		$number_agents		= $params->get('number_agents',4);
		$user_type			= $params->get('user_type',-1);
		if($user_type > -1)
		{
			$userSql		= " and agent_type = '$user_type'";
		}
		else
		{
			$userSql		= "";
		}
		$sort_by			= $params->get('sort_by','name');
		$show_featured		= $params->get('show_featured',0);
		if($show_featured == 1)
		{
			$userSql1		=  " and featured = '1'";
		}
		else
		{
			$userSql1		= "";
		}
		$user_ids			= $params->get('agent_ids','');
		if($user_ids != "")
		{
			$userIdsSql		= " and id in (".$user_ids.")";
		}
		$db					= Factory::getDbo();
		$db->setQuery("Select * from #__osrs_agents where published = '1' $userSql $userSql1 $userIdsSql order by $sort_by limit $number_agents");
		$items = $db->loadObjectList();
		return $items;
	}
}
