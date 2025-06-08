<?php

/*------------------------------------------------------------------------
# default.php - OS Property Map
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2018 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
if($configClass['map_type'] ==  0)
{
    $map = modOspropertyGoogleMapHelper::createMap($params,$width, $height, $zoom,$module_id,$configClass);
}
else
{
    $rootUri                = Uri::root(true);
    $document               = Factory::getDocument()
        ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.js')
        ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.markercluster.js')
        ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.css')
        ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.Default.css')
        ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.Default.ie.css')
        ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.css');
    $map = modOspropertyGoogleMapHelper::createOpenStreetMap($params,$width, $height, $zoom,$module_id,$configClass);
}
echo "<style type='text/css'>.map{width:100%;float:left;}</style>";
echo "<div class='map'>" . $map . "</div><div class='clearfix'></div>";

?>
