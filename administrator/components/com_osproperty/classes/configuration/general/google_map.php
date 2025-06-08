<?php
/*------------------------------------------------------------------------
# google_map.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
$inputSmallClass   = $bootstrapHelper->getClassMapping('input-small');
$inputMiniClass   = $bootstrapHelper->getClassMapping('input-mini') . ' smallSizeBox';
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('OS_MAP') ?></legend>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('map_type', Text::_('OS_SELECT_MAP_TYPE'), TextOs::_('OS_SELECT_MAP_TYPE_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('map_type', (int)$configs['map_type'], 'Google Map', 'OpenStreetMap');
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>"
		data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[map_type]' => '0')); ?>'>
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_aip_key', Text::_('OS_GOOGLE_API_KEY'), ''); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputLargeClass; ?> ilagre" name="configuration[goole_aip_key]"
				value="<?php echo isset($configs['goole_aip_key']) ? $configs['goole_aip_key'] : '' ?>" />
			<BR />
			You can register new Google API key through this <strong><a
					href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true"
					target="_blank"
					title="To get started using the Google Maps JavaScript API, click the button below, which takes you to the Google Developers Console.">link</a></strong>.
			You can read more details <strong><a
					href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">here</a></strong>
		</div>
	</div>

	<div class="<?php echo $controlGroupClass; ?>"
		data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[map_type]' => '0')); ?>'>
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_map_overlay', TextOs::_('Map Overlay'), ''); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			if (!isset($configs['goole_map_overlay'])) $configs['goole_map_overlay'] = 'ROADMAP';
			$option_map_overlay = array();
			$option_map_overlay[] = HTMLHelper::_('select.option', 'ROADMAP', TextOs::_('Normal'));
			$option_map_overlay[] = HTMLHelper::_('select.option', 'SATELLITE', TextOs::_('Satellite'));
			$option_map_overlay[] = HTMLHelper::_('select.option', 'HYBRID', TextOs::_('Hybrid'));
			echo HTMLHelper::_('select.genericlist', $option_map_overlay, 'configuration[goole_map_overlay]', 'class="form-select input-large ilarge"', 'value', 'text', $configs['goole_map_overlay']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>"
		data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[map_type]' => '0')); ?>'>
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_use_map', Text::_('OS_SHOW_STREET_VIEW_IN_DETAILS'), Text::_('OS_SHOW_STREET_VIEW_MAP_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('show_streetview', $configs['show_streetview']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<?php echo HelperOspropertyCommon::showLabel('', TextOs::_('Default coordinates'), TextOs::_('DEFAULT_COORDINATES_EXPLAIN'));
		global $configClass;
		if ($configClass['map_type'] == 0) {
			include(JPATH_ROOT . "/components/com_osproperty/helpers/googlemap.lib.php");
			if (($configClass['goole_default_lat'] == "") && ($configClass['goole_default_long'] == "")) {
				//find the default lat long
				$default_address = $configClass['general_bussiness_address'];
				$defaultGeocode = HelperOspropertyGoogleMap::getLatlongAdd($default_address);
				$configClass['goole_default_lat'] = $defaultGeocode[0]->lat;
				$configClass['goole_default_long'] = $defaultGeocode[0]->long;
			}

			$thedeclat = $configClass['goole_default_lat'];
			$thedeclong = $configClass['goole_default_long'];

			$geoCodeArr = array();
			$tmp		= new \stdClass();
			$tmp->lat = $thedeclat;
			$tmp->long = $thedeclong;
			$geoCodeArr[0]	= $tmp;
			HelperOspropertyGoogleMap::loadGMapinEditProperty($geoCodeArr, 'map', 'er_declat', 'er_declong');
		?>
			<br />

			<body onload="initialize()">
				<div id="map" style="width: 100%; height: 200px"></div>
			</body>
		<?php
		} else {
			include(JPATH_ROOT . "/components/com_osproperty/helpers/openstreetmap.lib.php");
			if (($configClass['goole_default_lat'] == "") && ($configClass['goole_default_long'] == "")) {
				//find the default lat long
				$default_address = $configClass['general_bussiness_address'];
				$defaultGeocode = HelperOspropertyOpenStreetMap::getLatlongAdd($default_address);
				$configClass['goole_default_lat'] = $defaultGeocode[0];
				$configClass['goole_default_long'] = $defaultGeocode[1];
			}

			$thedeclat = $configClass['goole_default_lat'];
			$thedeclong = $configClass['goole_default_long'];

			$geoCodeArr = array();
			$tmp		= new \stdClass();
			$tmp->lat = $thedeclat;
			$tmp->long = $thedeclong;
			$geoCodeArr[0]	= $tmp;
		?>
			<div id="map" style="width: 100%; height: 200px"></div>
		<?php
			HelperOspropertyOpenStreetMap::loadGMapinEditProperty($geoCodeArr, 'map', 'er_declat', 'er_declong');
		}
		?>

		<br />
		<table>
			<tr>
				<td class="key" width="50%" style="border:1px solid #DDD;background-color:#efefef;">
					<?php echo Text::_('Latitude'); ?>
					<input size="5" class="<?php echo $inputMiniClass; ?>" type="text"
						name="configuration[goole_default_lat]" id="er_declat" size="25" maxlength="100"
						value="<?php echo $thedeclat; ?>" />
				</td>
				<td class="key" style="padding-left:10px;border:1px solid #DDD;background-color:#efefef;" width="50%">
					<?php echo Text::_('Longitude'); ?>
					<input size="5" class="<?php echo $inputMiniClass; ?>" type="text"
						name="configuration[goole_default_long]" id="er_declong" size="25" maxlength="100"
						value="<?php echo $thedeclong; ?>" />
				</td>
			</tr>
		</table>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_use_map', Text::_('OS_SHOW_MAP_IN_DETAILS'), TextOs::_('OS_SHOW_MAP_IN_DETAILS_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php
			OspropertyConfiguration::showCheckboxfield('goole_use_map', $configs['goole_use_map']);
			?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_map_resolution', Text::_('OS_DEFAULT_MAP_ZOOM'), Text::_('OS_DEFAULT_MAP_ZOOM_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputMiniClass; ?>" name="configuration[goole_map_resolution]"
				value="<?php echo isset($configs['goole_map_resolution']) ? $configs['goole_map_resolution'] : '' ?>"
				size="2">
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('limit_zoom', Text::_('OS_LIMIT_ZOOM_LEVEL'), Text::_('OS_LIMIT_ZOOM_LEVEL_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="<?php echo $inputMiniClass; ?>" name="configuration[limit_zoom]"
				value="<?php echo isset($configs['limit_zoom']) ? $configs['limit_zoom'] : '15' ?>" size="2">
		</div>
	</div>
	<!--
	<div class
="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_use_map', TextOs::_('Map width'), TextOs::_('MAP_WIDTH_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[property_map_width]" value="<?php echo isset($configs['property_map_width']) ? $configs['property_map_width'] : '' ?>">px
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo HelperOspropertyCommon::showLabel('goole_use_map', TextOs::_('Map height'), TextOs::_('MAP_HEIGHT_EXPLAIN')); ?>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[property_map_height]" value="<?php echo isset($configs['property_map_height']) ? $configs['property_map_height'] : '' ?>">px
		</div>
	</div>
	-->
</fieldset>