<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core') ;

EventbookingHelperJquery::validateForm();

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$span7Class        = $bootstrapHelper->getClassMapping('span7');
$span5Class        = $bootstrapHelper->getClassMapping('span5');
$btnPrimary        = $bootstrapHelper->getClassMapping('btn btn-primary');

$config = EventbookingHelper::getConfig();
$mapApiKey = $config->get('map_api_key', '');

$mapProvider = $config->get('map_provider', 'googlemap');

if (trim($config->center_coordinates))
{
	$coordinates = trim($config->center_coordinates);
}
else
{
	if ($mapProvider === 'googlemap')
	{
		$http     = HttpFactory::getHttp();
		$url      = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(' ', '+', $config->default_country) . '&key=' . $mapApiKey;
		$response = $http->get($url);

		if ($response->code == 200)
		{
			$output_deals = json_decode($response->body);
			$latLng       = $output_deals->results[0]->geometry->location;
			$coordinates  = $latLng->lat . ',' . $latLng->lng;
		}
		else
		{
			$coordinates = '37.09024,-95.712891';
		}
	}
	else
	{
		$coordinates = '37.09024,-95.712891';
	}
}

$coordinates = explode(',', $coordinates);
$zoomLevel   = (int) $config->zoom_level ?: 14;

$document = Factory::getApplication()->getDocument();
$rootUri = Uri::root(true);

if ($mapProvider === 'googlemap')
{
	$document->addScript('https://maps.google.com/maps/api/js?key=' . $mapApiKey . '&v=quarterly');
	EventbookingHelperHtml::addOverridableScript('media/com_eventbooking/js/site-location-popup.min.js');
}
else
{
	$document->addScript($rootUri . '/media/com_eventbooking/assets/js/leaflet/leaflet.js')
	->addScript($rootUri . '/media/com_eventbooking/assets/js/autocomplete/jquery.autocomplete.min.js')
	->addStyleSheet($rootUri . '/media/com_eventbooking/assets/js/leaflet/leaflet.css');

	EventbookingHelperHtml::addOverridableScript('media/com_eventbooking/js/site-location-popup-openstreetmap.min.js');
}

$document->addScriptOptions('coordinates', $coordinates)
	->addScriptOptions('zoomLevel', $zoomLevel)
	->addScriptOptions('baseUri', Uri::base(true));

if ($this->params->get('show_page_heading', 1))
{
?>
	<h1 class="eb-page-heading"><?php echo $this->params->get('page_heading') ?: $this->escape(Text::_('EB_ADD_EDIT_LOCATION')); ?></h1>
<?php
}

if (EventbookingHelper::isValidMessage($this->params->get('intro_text')))
{
?>
	<div class="eb-description eb-add-location-intro-text"><?php echo $this->params->get('intro_text');?></div>
<?php
}
?>
<form action="<?php echo Route::_('index.php?option=com_eventbooking&view=location&Itemid=' . $this->Itemid, false); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="row-fluid">
		<div  class="<?php echo $span5Class ?>">
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_('EB_NAME'); ?>
					<span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input class="form-control input-large validate[required]" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->escape($this->item->name); ?>" />
				</div>
			</div>

			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_('EB_ADDRESS'); ?>
					<span class="required">*</span>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					 <input class="form-control input-large validate[required]" type="text" name="address" id="address" size="70" autocomplete="off" maxlength="250" value="<?php echo $this->escape($this->item->address);?>" />
					<ul id="eventmaps_results" style="display:none;"></ul>
				</div>
			</div>
			<?php
			if (EventbookingHelper::isModuleEnabled('mod_eb_cities'))
			{
			?>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo Text::_('EB_CITY'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<input class="form-control" type="text" name="city" id="city" size="30" maxlength="250" value="<?php echo $this->escape($this->item->city);?>" />
					</div>
				</div>
			<?php
			}

			if (EventbookingHelper::isModuleEnabled('mod_eb_states'))
			{
			?>
				<div class="<?php echo $controlGroupClass;  ?>">
					<label class="<?php echo $controlLabelClass; ?>">
						<?php echo Text::_('EB_STATE'); ?>
					</label>
					<div class="<?php echo $controlsClass; ?>">
						<input class="form-control" type="text" name="state" id="state" size="30" maxlength="250" value="<?php echo $this->escape($this->item->state);?>" />
					</div>
				</div>
			<?php
			}
			?>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_('EB_COORDINATES'); ?>
				</label>
				<div class="<?php echo $controlsClass; ?>">
					<input class="form-control input-large validate[required]" type="text" name="coordinates" id="coordinates" size="30" maxlength="250" value="" />
				</div>
			</div>
			<div class="<?php echo $controlGroupClass;  ?>">
				<label class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_('EB_PUBLISHED') ; ?>
				</label>
				<?php echo $this->lists['published']; ?>
			</div>
		 </div>
		 <div class="<?php echo $span7Class ?>">
			<div class="<?php echo $controlGroupClass;  ?>">
				<div id="map-canvas" style="width: 95%; height: 350px"></div><br>
			</div>
		 </div>
		 <div class="row-fluid">
			<button id="save_location" class="<?php echo $btnPrimary; ?>" type="submit"><span class="icon-save"></span><?php echo Text::_('EB_SAVE'); ?></button>
			 <?php
				if ($mapProvider === 'googlemap')
				{
				?>
					<input type="button" id="btn-get-location-from-address" class="btn btn-info" value="<?php echo Text::_('EB_PINPOINT'); ?> &raquo;" />
				<?php
				}
			 ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<input type="hidden" name="published" value="1" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>