<?php
/*------------------------------------------------------------------------
# direction.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class HTML_OspropertyDirection{
	/**
	 * Get Direction
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property
	 */
	static function getDirectionForm($option,$property,$lists,$address,$pro_address)
	{
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		?>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=direction_map&id='.$property->id.'&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
		
		<h1 class="componentheading">
			<?php
				echo Text::_('OS_GET_DIRECTIONS');
				echo " ";
				echo Text::_('OS_TO');
				echo " ";
				echo OSPHelper::getLanguageFieldValue($property,'pro_name');
			?>
		</h1>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
				<strong>
					<?php
						echo Text::_('OS_ENTER_YOUR_ADDRESS');
					?>
				</strong>
				<BR />
				<input type="text" class="input-large form-control" name="address" id="address_direction" size="30" value="<?php echo $address;?>" style="display:inline;"/>
				&nbsp;&nbsp;&nbsp;
				<strong>
					<?php
						echo Text::_('OS_ROUTE_STYLE');
					?>
				</strong>
				
				<?php echo $lists['routeStyle'];?>
				<input type="button" class="btn  btn-info" value="<?php echo Text::_('OS_GET_DIRECTIONS')?>" onclick="javascript:submitForm();" />
				<?php
				$needs = array();
				$needs[] = "property_details";
				$needs[] = $property->id;
				$itemid  = OSPRoute::getItemid($needs);
				?>
				<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid);?>" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>"><?php echo Text::_('OS_BACK');?></a>
			</div>
		</div>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php
				if($address != "")
				{
					if($configClass['map_type'] == 0)
					{
						$row   = new stdClass();
						$param = new stdClass;
						$param->api_key = $configClass['goole_aip_key'];
						$param->width =  400;
						$param->height =  400;
						$param->zoom =  15;
						$param->dir_width = 320;
						$param->header_map = '';
						$param->header_dir = '';
						$param->map_on_right = 1;
						$row->text = '{googleDir width='.$param->width.' height='.$param->height.' '.$mode.'dir_width=275 from="'.$address.'" to="'.$pro_address.'"}' ;
						$plugin = new Plugin_googleDirections($row, $param, $is_mod);
						echo $row->text;
					}
					else
					{
						$return			= HelperOspropertyOpenStreetMap::findAddress($option,'',$address,1);
						$search_lat		= $return[0];
						$search_long	= $return[1];
						$status			= $return[2];
						?>
						<div id="direction_map" style="width:100%;height:600px;"></div>
					
						<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
						<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
						<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

					
						<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

						
						<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

						  <script>
							var map = L.map('direction_map').setView([10.762622, 106.660172], 13);


							L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
							  maxZoom: 19,
							  attribution: '&copy; OpenStreetMap contributors'
							}).addTo(map);


							var start = { lat: <?php echo $search_lat; ?>, lon: <?php echo $search_long; ?> };
							var destination = { lat: <?php echo $property->lat_add; ?>, lon: <?php echo $property->long_add; ?> };


							L.marker([start.lat, start.lon]).addTo(map)
							  .bindPopup("<?php echo Text::_('OS_START');?>").openPopup();
							L.marker([destination.lat, destination.lon]).addTo(map)
							  .bindPopup("<?php echo $property->pro_name;?>");

							//alert("<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&format=row&nohtml=1&task=direction_getresponse&start_lat=" + start.lat + "&start_lon=" + start.lon + "&dest_lat=" + destination.lat + "&dest_lon=" + destination.lon);

							fetch("<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&format=row&nohtml=1&task=direction_getresponse&start_lat=" + start.lat + "&start_lon=" + start.lon + "&dest_lat=" + destination.lat + "&dest_lon=" + destination.lon)
							  .then(response => response.json())
							  .then(data => {
								if (data.routes && data.routes.length > 0) {
								  var route = data.routes[0];

								  var routeLine = L.geoJSON(route.geometry, {
									style: { color: 'blue', weight: 5 }
								  }).addTo(map);
								  map.fitBounds(routeLine.getBounds());
								} else {
								  
								}
							  })
							  .catch(error => console.error('Error:', error));
						  </script>
						<?php
					}
				}
				?>
			</div>
		</div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="direction_map" />
		<input type="hidden" name="id" id="id" value="<?php echo $property->id?>" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		</form>
		<script type="text/javascript">
		function submitForm(){
			var address = document.getElementById('address_direction');
			if(address.value == "")
			{
				alert("<?php echo Text::_('OS_PLEASE_ENTER_ADDRESS')?>");
			}
			else
			{
				document.ftForm.submit();
			}
		}
		</script>
		<?php
	}
}
?>