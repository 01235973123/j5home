<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

AriKernel::import('Xml.XmlHelper');

class JElementQuizuser extends JElement
{
	var	$_name = 'Quizuser';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$cssClass = AriXmlHelper::getAttribute($node, 'css_class');

		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT
				id AS UserId,
				name AS UserName
			FROM
				#__users U INNER JOIN #__ariquizstatisticsinfo SSI  
					ON SSI.UserId = U.Id
			WHERE 
				SSI.Status = "Finished"
			GROUP BY U.Id
			ORDER BY U.name ASC'
		);
		$users = $db->loadObjectList();
		
		$guestLabel = AriXmlHelper::getAttribute($node, 'guest_label');
		$allUsersLabel = AriXmlHelper::getAttribute($node, 'allusers_label');
		
		if ($guestLabel)
		{
			$guestUser = new stdClass();
			$guestUser->UserId = 0;
			$guestUser->UserName = JText::_($guestLabel);
			array_unshift($users, $guestUser);
		}

		if ($allUsersLabel)
		{
			$allUsers = new stdClass();
			$allUsers->UserId = -1;
			$allUsers->UserName = JText::_($allUsersLabel);
			array_unshift($users, $allUsers);
		}

		return JHTML::_(
			'select.genericlist', 
			$users, 
			$control_name . '[' . $name . ']', 
			'class="inputbox form-select' . ($cssClass ? ' ' . $cssClass : '') . '"', 
			'UserId', 
			'UserName', 
			$value,
			$control_name . $name);
	}
}