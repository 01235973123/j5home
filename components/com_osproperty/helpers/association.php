<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2023 joomdonation.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       3.0
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Uri\Uri;

JLoader::register('OSPHelper', JPATH_ROOT . '/components/com_osproperty/helpers/helper.php');
JLoader::register('OSPRoute', JPATH_ROOT . '/components/com_osproperty/helpers/route.php');

class OspropertyHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{
		$languages			= LanguageHelper::getLanguages('lang_code');
		unset($languages[Factory::getLanguage()->getTag()]);
		$current_language	= Factory::getLanguage();
		$current_language	= $current_language->getTag();
		$default_language	= OSPHelper::getDefaultLanguage();
		//jimport('helper.route', JPATH_COMPONENT_SITE);

		$app				= Factory::getApplication();
		$jinput				= $app->input;
		$task				= is_null($task) ? $jinput->getCmd('task') : $task;
		$id					= empty($id) ? $jinput->getInt('id') : $id;
		$return				= [];
		if ($task === 'property_details')
		{
			if ($id)
			{
				foreach ($languages as $tag => $language)
				{

					$prefix = explode("-",$tag);
					$lang = $prefix[0];
					$prefix = '_'.$prefix[0];

					$needs = array();
					$needs[0] = "property_details";
					$needs[1] = $id;
					$needs[2] = $tag;
					$itemid   = OSPRoute::getItemid($needs);
					$return[$tag] = 'index.php?option=com_osproperty&task=property_details&lang='.$lang.'&id='.$id.'&l='.$prefix.'&Itemid='.$itemid;
				}
				return $return;
			}
		}
		return array();

	}
}
