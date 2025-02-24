<?php
/**
 * @version		1.5.0
 * @package		Joomla
 * @subpackage	OS Property
 * @author      Dang Thuc Dam
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Edocman helper.
 */
class OSPropertyHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
	    JSubMenuHelper::addEntry(
			Text::_('OS_CPANEL'),
			'index.php?option=com_osproperty&task=cpanel_default',
			$vName == 'cpanel_default'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_CONFIGURATION'),
			'index.php?option=com_osproperty&task=configuration_list',
			$vName == 'configuration_list'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_PROPERTIES'),
			'index.php?option=com_osproperty&task=properties_list',
			$vName == 'properties_list'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_CATEGORIES'),
			'index.php?option=com_osproperty&task=categories_list',
			$vName == 'categories_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_PROPERTY_TYPES'),
			'index.php?option=com_osproperty&task=type_list',
			$vName == 'type_list'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_AGENTS'),
			'index.php?option=com_osproperty&task=agent_list',
			$vName == 'agent_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_COMPANIES'),
			'index.php?option=com_osproperty&task=companies_list',
			$vName == 'companies_list'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_CONVENIENCE'),
			'index.php?option=com_osproperty&task=amenities_list',
			$vName == 'amenities_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_PRICELIST'),
			'index.php?option=com_osproperty&task=pricegroup_list',
			$vName == 'pricegroup_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_FIELD_GROUPS'),
			'index.php?option=com_osproperty&task=fieldgroup_list',
			$vName == 'fieldgroup_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGAE_EXTRA_FIELDS'),
			'index.php?option=com_osproperty&task=extrafield_list',
			$vName == 'extrafield_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_STATES'),
			'index.php?option=com_osproperty&task=state_list',
			$vName == 'state_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_CITY'),
			'index.php?option=com_osproperty&task=city_list',
			$vName == 'city_list'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_EMAIL_FORMS'),
			'index.php?option=com_osproperty&task=email_list',
			$vName == 'email_list'
		);	
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_TRANSACTION'),
			'index.php?option=com_osproperty&task=transaction_list',
			$vName == 'transaction_list'
		);			
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_CSVFORMS'),
			'index.php?option=com_osproperty&task=form_default',
			$vName == 'form_default'
		);
		JSubMenuHelper::addEntry(
			Text::_('OS_MANAGE_COMMENTS'),
			'index.php?option=com_osproperty&task=comment_list',
			$vName == 'comment_list'
		);
	}
}
?>