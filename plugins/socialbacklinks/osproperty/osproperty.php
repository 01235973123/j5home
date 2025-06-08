<?php
/**
 * SocialBacklinks Redshop content plugin
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.utilities.date' );

/**
 * Redshop SocialBacklinks Synchronizer plugin
 */
class plgSocialbacklinksOsproperty extends JPlugin
{
	/**
	 * Do something onAfterRoute
	 *
	 * @return void
	 */
	public function onSBPluginRegister( )
	{
		JLoader::register( 'PlgSBOspropertyAdapter', JPATH_ROOT . '/plugins/socialbacklinks/osproperty/content/adapter.php' );
		SBPlugin::register( new PlgSBOspropertyAdapter( $this ) );

        // Add component language file
        $lang = JFactory::getLanguage();
        $lang->load('com_socialbacklinks', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks', null, true);
        $lang->load('com_socialbacklinks.override',JPATH_ADMINISTRATOR.'/components/com_socialbacklinks',null,true);
	}

}
