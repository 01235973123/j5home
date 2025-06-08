<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Helper\ModuleHelper;
/**
 * @copyright	Copyright (C) 2016 Ossolution
 * http://www.joomdontion.com
 * Module OSP Slideshow
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die;
error_reporting(E_ERROR | E_PARSE);
// JHtml::_('behavior.modal');
require_once dirname(__FILE__) . '/helper.php';
require_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
require_once JPATH_ROOT.'/components/com_osproperty/helpers/common.php';
require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';

$items = modOspslideshowHelper::getItems($params);

if ($params->get('displayorder', 'normal') == 'shuffle')
	shuffle($items);

$document = Factory::getDocument();
OSPHelper::loadLanguage();
JHTML::_("jquery.framework", true);
if ($params->get('loadjquery', '1')) {
	$document->addScript(JURI::base(true) . '/modules/mod_ospslideshow/assets/jquery.min.js');
}
if ($params->get('loadjqueryeasing', '1')) {
	$document->addScript(JURI::base(true) . '/modules/mod_ospslideshow/assets/jquery.easing.1.3.js');
}
$debug = true;
if ($debug) {
	$document->addScript(JURI::base(true) . '/modules/mod_ospslideshow/assets/camera.js');
} else {
	$document->addScript(JURI::base(true) . '/modules/mod_ospslideshow/assets/camera.min.js');
}

$theme = $params->get('theme', 'default');
$langdirection = $document->getDirection();
if ($langdirection == 'rtl' && File::exists('modules/mod_ospslideshow/themes/' . $theme . '/css/camera_rtl.css')) {
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_ospslideshow/themes/' . $theme . '/css/camera_rtl.css');
} else {
	$document->addStyleSheet(JURI::base(true) . '/modules/mod_ospslideshow/themes/' . $theme . '/css/camera.css');
}

if (File::exists('modules/mod_ospslideshow/themes/' . $theme . '/css/camera_ie.css')) {
	echo '
		<!--[if lte IE 7]>
		<link href="' . JURI::base(true) . '/modules/mod_ospslideshow/themes/' . $theme . '/css/camera_ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->';
}

if (File::exists('modules/mod_ospslideshow/themes/' . $theme . '/css/camera_ie8.css')) {
	echo '
		<!--[if IE 8]>
		<link href="' . JURI::base(true) . '/modules/mod_ospslideshow/themes/' . $theme . '/css/camera_ie8.css" rel="stylesheet" type="text/css" />
		<![endif]-->';
}

// set the navigation variables
if (count($items) == 1) { // for only one slide, no navigation, no button
	$navigation = "navigationHover: false,
			mobileNavHover: false,
			navigation: false,
			playPause: false,";
} else {
	switch ($params->get('navigation', '2')) {
		case 0:
			// aucune
			$navigation = "navigationHover: false,
					mobileNavHover: false,
					navigation: false,
					playPause: false,";
			break;
		case 1:
			// toujours
			$navigation = "navigationHover: false,
					mobileNavHover: false,
					navigation: true,
					playPause: true,";
			break;
		case 2:
		default:
			// on mouseover
			$navigation = "navigationHover: true,
					mobileNavHover: true,
					navigation: true,
					playPause: true,";
			break;
	}
}


// load the slideshow script
$js = "<script type=\"text/javascript\">
       jQuery(function(){
        jQuery('#camera_wrap_" . $module->id . "').camera({
                height: '" . $params->get('height', '400') . "',
                minHeight: '" . $params->get('minheight', '150') . "',
                pauseOnClick: false,
                hover: " . $params->get('hover', '1') . ",
                fx: '" . implode(",", $params->get('effect', array('linear'))) . "',
                loader: '" . $params->get('loader', 'pie') . "',
                pagination: " . $params->get('pagination', '1') . ",
                thumbnails: " . $params->get('thumbnails', '1') . ",
                thumbheight: " . $params->get('thumbnailheight', '100') . ",
                thumbwidth: " . $params->get('thumbnailwidth', '75') . ",
                time: " . $params->get('time', '7000') . ",
                transPeriod: " . $params->get('transperiod', '1500') . ",
                alignment: '" . $params->get('alignment', 'center') . "',
                autoAdvance: " . $params->get('autoAdvance', '1') . ",
                mobileAutoAdvance: " . $params->get('autoAdvance', '1') . ",
                portrait: " . $params->get('portrait', '0') . ",
                barDirection: '" . $params->get('barDirection', 'leftToRight') . "',
                imagePath: '" . JURI::base(true) . "/modules/mod_ospslideshow/images/',
                lightbox: '" . $params->get('lightboxtype', 'mediaboxck') . "',
                fullpage: " . $params->get('fullpage', '0') . ",
				mobileimageresolution: '" . ($params->get('usemobileimage', '0') ? $params->get('mobileimageresolution', '640') : '0') . "',
                " . $navigation . "
                barPosition: '" . $params->get('barPosition', 'bottom') . "',
                responsiveCaption: " . ($params->get('usecaptionresponsive') == '2' ? '1' : '0') . ",
				container: '" . $params->get('container', '') . "'
        });
}); </script>";

echo $js;

$css = '';
// load some css
$css = "#camera_wrap_" . $module->id . " .camera_pag_ul li img, #camera_wrap_" . $module->id . " .camera_thumbs_cont ul li > img {height:" . modOspslideshowHelper::testUnit($params->get('thumbnailheight', '75')) . ";}";

// load the caption styles
$captioncss = modOspslideshowHelper::createCss($params, 'captionstyles');
$fontfamily = ($params->get('captionstylesusefont','0') && $params->get('captionstylestextgfont', '0')) ? "font-family:'" . $params->get('captionstylestextgfont', 'Droid Sans') . "';" : '';
if ($fontfamily) {
	$gfonturl = str_replace(" ", "+", $params->get('captionstylestextgfont', 'Droid Sans'));
	$document->addStylesheet('https://fonts.googleapis.com/css?family=' . $gfonturl);
}

$css .= "
#camera_wrap_" . $module->id . " .camera_caption {
	display: block;
	position: absolute;
}
#camera_wrap_" . $module->id . " .camera_caption > div {
	" . $captioncss['padding'] . $captioncss['margin'] . $captioncss['background'] . $captioncss['gradient'] . $captioncss['borderradius'] . $captioncss['shadow'] . $captioncss['border'] . $fontfamily . "
}
#camera_wrap_" . $module->id . " .camera_caption > div div.camera_caption_title {
	" . $captioncss['fontcolor'] . $captioncss['fontsize'] . "
}
#camera_wrap_" . $module->id . " .camera_caption > div div.camera_caption_desc {
	" . $captioncss['descfontcolor'] . $captioncss['descfontsize'] . "
}
";


if ($params->get('usecaptionresponsive') == '1' || $params->get('usecaptionresponsive') == '2') {
	$css .= "
@media screen and (max-width: " . str_replace("px", "", $params->get('captionresponsiveresolution', '480')) . "px) {
		.camera_caption {
			" . ( $params->get('captionresponsivehidecaption', '0') == '1' ? "display: none !important;" : ($params->get('usecaptionresponsive') == '1' ? "font-size: " . $params->get('captionresponsivefontsize', '0.6em') ." !important;" : "") ) . "
		}
}";
}

if ($params->get('usecaptionresponsive') == '1' || $params->get('usecaptionresponsive') == '2') {
	$css .= "
@media screen and (max-width: " . str_replace("px", "", $params->get('captionresponsiveresolution', '480')) . "px) {
		.camera_caption {
			font-size: " . $params->get('captionresponsivefontsize', '0.6em') ." !important;
		}
		.camera_caption .camera_caption_desc .property-status-tag, .camera_caption .camera_caption_desc .property-desc
		{
			display:none !important;
		}

		.camera_caption .camera_price
		{
			font-size:14px !important;
		}

		.camera_caoption .camera_caption_title
		{
			font-size:14px !important;
		}

		.camera_caption_desc .propertydetailsbtn, .camera_caption_desc .propertydetailsbtn:hover
		{
			padding:2px 10px !important;
			font-size:12px !important;
		}

		.camera_caption > div
		{
			padding:5px !important;
		}

		.camera_caption > div
		{
			width:180px !important;
		}

		.camera_caption > div div.camera_caption_title
		{
			font-size:14px !important;
		}
}";
}
$document->addStyleDeclaration($css);

// display the module
require ModuleHelper::getLayoutPath('mod_ospslideshow', $params->get('layout', 'default'));
