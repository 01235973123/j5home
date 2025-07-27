<?php

/*------------------------------------------------------------------------
# mod_ospropertyrandom.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

include_once(JPATH_ROOT."/components/com_osproperty/helpers/common.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/route.php");
include_once(JPATH_ROOT.'/components/com_osproperty/helpers/bootstrap.php');
if(!OSPHelper::isJoomla4())
{
	HTMLHelper::_('behavior.tooltip');
}
require_once( dirname(__FILE__).'/helper.php' );
global $lang_suffix;
$lang_suffix = OSPHelper::getFieldSuffix();
OSPHelper::loadMedia();
OSPHelper::loadBootstrap();
OSPHelper::loadLanguage();

$configClass = OSPHelper::loadConfig();
if($configClass['load_lazy']){
	?>
	<script src="<?php echo Uri::root(); ?>media/com_osproperty/assets/js/lazy.js" type="text/javascript" defer="defer"></script>
	<?php
}
// Grab the products
$helper					= new modOSpropertyramdomHelper( $params ); 
//list($properties,$check_search) = $helper->getProperty();
$properties = $helper->getProperty();

$modulelayout			= $params->get('modulelayout',0);
$width					= $params->get('width',50);
$show_small_desc		= $params->get('show_small_desc',0);
$show_address			= $params->get('show_address',0);
$show_price				= $params->get('show_price',0);
$show_marketstatus		= $params->get('show_marketstatus',0);
$limit_word				= $params->get('limit_word',50);
$limit_title_word		= $params->get('limit_title_word',0);
$show_photo				= $params->get('show_photo',1);
$featured				= $params->get('featured',0);
$category				= $params->get('category','');
$agent_id				= $params->get('agent_id',0);
$company_id				= $params->get('company_id',0);
$type					= $params->get('type','');
$style					= $params->get('mstyle',0);
$enable_nav				= $params->get('enable_nav',0);
$nproperties			= $params->get('nproperties',0);
$country_ids			= $params->get('country_ids','');
$state_ids				= $params->get('state_ids','');
$city_ids				= $params->get('city_ids','');
$property_ids			= $params->get('property_ids','');
$open_from				= $params->get('open_from', '');
$open_to				= $params->get('open_to', '');
$only_open				= $params->get('only_open',0);

if($style == 1)
{
    $properties_per_row = $params->get('properties_per_row',4);
    $divstyle = 12/$properties_per_row;
	switch ($properties_per_row){
		case "1":
			$font_height = "os-2x";
		break;
		case "2":
			$font_height = "os-2x";
		break;
		case "3":
			$font_height = "os-1x";
		break;
		case "4":
			$font_height = "os-1x";
		break;
		case "6":
			$font_height = "os-1x";
		break;
	}
}
else
{
	$divstyle = 12;
	$properties_per_row = 1;
	$font_height = "os-2x";
}
$element_width			= $params->get('element_width',180);
$element_height			= $params->get('element_height',200);
$show_bathrooms			= $params->get('show_bathrooms',0);
$show_bedrooms			= $params->get('show_bedrooms',0);
$show_parking			= $params->get('show_parking',0);
$show_square			= $params->get('show_square',0);
$show_category			= $params->get('show_catgoryname',0);
$show_type				= $params->get('show_typename',0);
$bstyle					= $params->get('bstyle','white');
$sold					= $params->get('sold',0);
$max_properties			= $params->get('max_properties','');


$document = Factory::getDocument();
$document->getWebAssetManager()->registerAndUseStyle('mod_ospropertyrandom.style',Uri::root().'modules/mod_ospropertyrandom/asset/style.css');
include_once(JPATH_ROOT."/components/com_osproperty/helpers/common.php");

if($modulelayout == 0){
	$layout = "default";
}else{
	$layout = "showcase";
}
global $bootstrapHelper;
OSPHelper::generateBoostrapVariables();
require( ModuleHelper::getLayoutPath( 'mod_ospropertyrandom',$layout ) );
if($configClass['load_lazy']){
	?>
	<script type="text/javascript">
	jQuery(function() {
		jQuery("img.oslazy").lazyload();
	});
	</script>
	<?php
}
