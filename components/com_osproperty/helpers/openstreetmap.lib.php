<?php
/*------------------------------------------------------------------------
# googlemap.lib.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Http\HttpFactory;

class HelperOspropertyOpenStreetMap
{
    static private $url = "https://maps.google.com/maps/api/geocode/json";
	public static function loadGoogleScript($suffix = false)
	{
		global $configClass;
		OSPHelper::loadGoogleJS($suffix);
	}

	/**
	 * Get Lat Long address
	 *
	 * @param unknown_type $address
	 * @return unknown
	 */
	static function getLatlongAdd($address)
	{
		$address = urlencode($address);
		$http    = HttpFactory::getHttp();
		$base_url = "https://nominatim.openstreetmap.org/search?q=".$address."&format=json&addressdetails=1";
		
		$fileContents = $http->get($base_url)->body;
		//echo $fileContents;die();
		
		
		$data =  json_decode($fileContents, true);

		if (isset($data[0])) 
		{
			// Lấy latitude và longitude từ kết quả
			$latitude		= $data[0]['lat'];
			$longitude		= $data[0]['lon'];

			return array($latitude,$longitude);
		}
		//die();
        //return array($output[0]['lat'], $output[0]['lon']);
	}

    /**
     * Find Address
     *
     * @param unknown_type $option
     * @param unknown_type $row
     * @return unknown
     */
    public static function findAddress($option,$row,$address,$type)
    {
        if($type == 0)
        {
            $db = Factory::getDbo();
            $address = "";
            if($row->address != "")
            {
                $address .= $row->address;
            }
            if($row->city != "")
            {
                $address .= " ".$row->city;
            }
            if($row->state != "")
            {
                $db->setQuery("Select state_name from #__osrs_states where id = '$row->state'");
                $state_name = $db->loadResult();
                $address .= " ".$state_name;
                $db->setQuery("Select country_id from #__osrs_states where id = '$row->state'");
                $country_id = $db->loadResult();
                $db->setQuery("Select country_name from #__osrs_countries where id = '$country_id'");
                $country_name = $db->loadResult();
                $address .= " ".$country_name;
            }
            $address = trim($address);
        }

        $return = self::getLatlongAdd($address);
        $return[2] = "OK";
        return $return;
    }
	
	/**
	 * Check curl existing
	 *
	 * @return unknown
	 */
	public static function _iscurlinstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Load Google Map
	 *
	 * @param unknown_type $geocode
	 */
	static function loadGoogleMap($geocode,$mapdiv,$maptype){
		global $configClass;
		switch ($maptype){
			case "agent":
			case "company":
				$icon = "workoffice.png";
			break;
			default:
				$icon = "house.png";
			break;
		}
		if(count($geocode) == 1){
			$lat = $geocode[0]->lat;
			$long = $geocode[0]->long;
		}else{
			$lat = $geocode[0]->lat;
			$long = $geocode[0]->long;
		}
		if (!isset($configClass['goole_map_overlay'])) $configClass['goole_map_overlay'] = 'ROADMAP';

        OSPHelper::loadGoogleJS('');
		?>
		<script type="text/javascript">
		function initialize() {
		  var myOptions = {
		  zoom: <?php echo $configClass['goole_map_resolution']?>,
center: new google.maps.LatLng(<?php echo $lat?>,<?php echo $long?>),mapTypeId: google.maps.MapTypeId.<?php echo  $configClass['goole_map_overlay'];?>};
		  var map = new google.maps.Map(document.getElementById("<?php echo $mapdiv?>"),myOptions);
		  setMarkers(map, addressArray);
		}
		
		<?php
		$address = "[";
		for($i=0;$i<count($geocode);$i++){
			$geo = $geocode[$i];
			$address .= '["'.$geo->text.'",'.$geo->lat.','.$geo->long.','.$i.'],';
		}
		$address = substr($address,0,strlen($address)-1);
		$address .= "]";
		?>
		
		var addressArray = <?php echo $address?>;
		
		function setMarkers(map, locations)
        {
             for (var i = 0; i < locations.length; i++) {
                  var j = i + 1;
                  var imagelink = '<?php echo Uri::root()?>media/com_osproperty/assets/images/mapicon/i' + j + '.png';
                  var image = new google.maps.MarkerImage(imagelink,new google.maps.Size(36, 31),new google.maps.Point(0,0),new google.maps.Point(0, 31));
                  var add = locations[i];
                  var myLatLng = new google.maps.LatLng(add[1], add[2]);
                  var marker = new google.maps.Marker({
                       position: myLatLng,
                       map: map,
                       icon: image,
                       title: add[0],
                       zIndex: add[3]
                  });
             }
		}
		</script>
		<?php
	}
	
	/**
	 * load Google Map in Edit Property
	 */
	static function loadGMapinEditProperty($geocode,$div_name,$lat_div,$long_div)
    {
		global $mainframe,$configClass;
        $document           = Factory::getDocument()
            ->addScript(Uri::root() . 'media/com_osproperty/assets/js/leaflet/leaflet.js')
            ->addStyleSheet(Uri::root() . 'media/com_osproperty/assets/js/leaflet/leaflet.css');
		$icon = "house.png";
		?>

		<script type="text/javascript">
        jQuery(document).ready(function(){
            var map = L.map('map').setView([<?php echo $geocode[0]->lat?>, <?php echo $geocode[0]->long;?>], <?php echo $configClass['goole_map_resolution']?>);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox.streets',
                zoom: <?php echo $configClass['goole_map_resolution']?>,
            }).addTo(map);
            var marker = L.marker([<?php echo $geocode[0]->lat?>, <?php echo $geocode[0]->long;?>], {draggable: true}).addTo(map);
            marker.on('dragend', function(event){
                var marker = event.target;
                var location = marker.getLatLng();
                var lat = location.lat;
                var lon = location.lng;
                document.getElementById("<?php echo $lat_div?>").value = lat.toFixed(5);
                document.getElementById("<?php echo $long_div?>").value = lon.toFixed(5);
            });
        });

		</script>
		<?php
	}
	
	/**
	 * Load Locator Map
	 *
	 * @param unknown_type $rows
	 * @param unknown_type $mapdiv
	 */
	static function loadLocatorMap($rows,$mapdiv,$zoomlevel,$search_lat,$search_long)
    {
		global $mainframe,$configClass;
		
		$icon = "house.png";
		if(($search_lat != "") or ($search_long != "")){
			$lat = $search_lat;
			$long = $search_long;
		}else{
			if(count($rows) == 1){
				$lat = $rows[0]->lat;
				$long = $rows[0]->long;
			}else{
				$lat = $rows[0]->lat;
				$long = $rows[0]->long;
			}
		}
		if (!isset($configClass['goole_map_overlay'])) $configClass['goole_map_overlay'] = 'ROADMAP';
        OSPHelper::loadGoogleJS('');
		?>
		<script type="text/javascript">
		  var infowindow;
		  var map;
		  var bounds;
		  var markers = [];
		  var markerIndex=0;
		  var markerArray = [];
		  var bounds = new google.maps.LatLngBounds();
		  function initMap() {
			  var myOptions = {
			  zoom: <?php echo $zoomlevel;?>,
			  center: new google.maps.LatLng(<?php echo $lat?>,<?php echo $long?>),
			  mapTypeId: google.maps.MapTypeId.<?php echo $configClass['goole_map_overlay'];?>,
			  mapTypeControl: true,
              navigationControl: true,
              streetViewControl: true,
              zoomControl: true,
	          zoomControlOptions: {
	            style: google.maps.ZoomControlStyle.SMALL
	          }
			  };
			  var map = new google.maps.Map(document.getElementById("<?php echo $mapdiv?>"),myOptions);
			  //setMarkers(map, addressArray);
			  var infoWindow = new google.maps.InfoWindow();
	 		  var markerBounds = new google.maps.LatLngBounds();
	 
	 		  function makeMarker(options){
				   var pushPin = new google.maps.Marker({map:map});
				   pushPin.setOptions(options);
				   google.maps.event.addListener(pushPin, 'click', function(){
					     infoWindow.setOptions(options);
					     infoWindow.open(map, pushPin);
				   });
				   markerArray.push(pushPin);
				   return pushPin;
			 }

			 google.maps.event.addListener(map, 'click', function(){
			   infoWindow.close();
			 });
			 
			 <?php
			 for($i=0;$i<count($rows);$i++){
			 	$row = $rows[$i];
			 	if($mapdiv == "map_canvas"){
			 	?>
			 	 makeMarker({
				   position: new google.maps.LatLng(<?php echo $row->lat?>,<?php echo $row->long?>),
				   title: "<?php echo htmlspecialchars($row->title);?>",
				   content: "<?php echo htmlspecialchars($row->content)?>",
				   icon:new google.maps.MarkerImage('<?php echo Uri::root()?>media/com_osproperty/assets/images/<?php echo $icon?>')
				 });
			 	<?php
			 	}else{
			 	?>
			 	 makeMarker({
				   position: new google.maps.LatLng(<?php echo $row->lat?>,<?php echo $row->long?>),
				   title: "<?php echo htmlspecialchars($row->title)?>",
				   content: "<?php echo htmlspecialchars($row->content)?>"
				 });
				 bounds.extend(new google.maps.LatLng(<?php echo $row->lat?>,<?php echo $row->long?>));
			 	<?php
			 	}
			 }
			 ?>
			 map.fitBounds(bounds);
			 var listener = google.maps.event.addListener(map, "idle", function() { 
			    if (map.getZoom() > 16) map.setZoom(16); 
			    google.maps.event.removeListener(listener); 
			 });
		  }
		  
		  window.onload=function(){
			if(window.initMap) initMap();
		  }
		  window.onunload=function(){
		    if(typeof(GUnload)!="undefined") GUnload();
		  }
		  
		  function openMarker(i){
			   google.maps.event.trigger(markerArray[i],'click');
		  };
		</script>
		<?php
	}
	

    /**
     * This function is used to load Google Map at top of listing page
     * @param $rows
     * @param int $state_id
     * @param int $city_id
     * @return bool
     */
    static function loadMapInListing($rows,$state_id = 0,$city_id = 0)
    {
        global $mainframe,$configClass,$bootstrapHelper;
        $rootUri            = Uri::root(true);
        $document           = Factory::getDocument()
            ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.js')
            ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.markercluster.js')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.css')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.Default.css')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.Default.ie.css')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/MarkerCluster.css');

        //find default lat/long addresses
        $show_map           = 0;
        $city_name          = "";
        $state_name         = "";
        $db                 = Factory::getDbo();
        if($state_id > 0)
        {
            $db->setQuery("Select state_name from #__osrs_states where id = '$state_id'");
            $state_name     = $db->loadResult();

            $db->setQuery("Select country_id from #__osrs_states where id = '$state_id'");
            $country_id     = $db->loadResult();

            $db->setQuery("Select country_name from #__osrs_countries where id = '$country_id'");
            $country_name   = $db->loadResult();
        }
        if($city_id > 0)
        {
            $db->setQuery("Select city from #__osrs_cities where id = '$city_id'");
            $city_name      = $db->loadResult();
        }

        if(($city_name != "") || ($state_name != ""))
        {
            $address = "";
            if($city_name != "")
            {
                $address = $city_name;
            }
            if($state_name != "")
            {
                $address .= " ".$state_name;
            }
            if($country_name != "")
            {
                $address .= " ".$country_name;
            }
            $return = self::getLatlongAdd($address);
            $default_lat  = $return[0];
            $default_long = $return[1];
        }
        else
        {
            $default_lat  = $configClass['goole_default_lat'];
            $default_long = $configClass['goole_default_long'];
        }

        $duplicate = OSPHelper::findGoogleDuplication($rows);

        //setup Leaflet map
        $zoomLevel        = 16;
        ?>
        <div id="map_canvas" class="map2x relative"></div>
        <script type="text/javascript">
        jQuery(document).ready(function(){
            var markerArray = [];
            var latArr      = [];
            var longArr     = [];
            var mymap       = L.map('map_canvas').setView([<?php echo $default_lat; ?>, <?php echo $default_long; ?>], <?php echo $zoomLevel;?>);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '',
                maxZoom: 18,
                id: 'mapbox.streets',
                zoom: <?php echo $zoomLevel;?>,
            }).addTo(mymap);

            <?php
            if(count($duplicate) > 0)
            {
                $boundItems = array();
                for ($i = 0; $i < count($duplicate); $i++) {
                    $item = $duplicate[$i];
                    $key = OSPHelper::find_key($item->id, $rows);
                    if (($rows[$key]->show_address == 1) && ($rows[$key]->lat_add != "") && ($rows[$key]->long_add != "")) {
                        $boundItems[] = "[" . $rows[$key]->lat_add . "," . $rows[$key]->long_add . "]";
                    }
                }
                if (count($boundItems)) {
                    $boundItems = implode(",", $boundItems);
                }
                ?>
                mymap.fitBounds([<?php echo $boundItems?>]);
                var markers = new L.MarkerClusterGroup();
                <?php

                for($i=0;$i<count($duplicate);$i++)
                {
                    $item = $duplicate[$i];
                    $key  = OSPHelper::find_key($item->id,$rows);
                    if(count((array)$item->value) == 0){ //having no duplication
                        $row = $rows[$key];
                        $row->mapid = $i;
                        $needs = array();
                        $needs[] = "property_details";
                        $needs[] = $row->id;
                        $itemid	 = OSPRoute::getItemid($needs);
                        $title = "";
                        if(($row->ref!="") && ($configClass['show_ref'] == 1)){
                            $title .= $row->ref.",";
                        }
                        $title 		.= $row->pro_name;
                        $title  	 = str_replace("'","",$title);
                        $title 		 = htmlspecialchars($title);
                        $created_on  = $row->created;
                        $modified_on = $row->modified;

                        $addInfo = array();
                        if($row->bed_room > 0){
                            $addInfo[] = $row->bed_room." ".Text::_('OS_BEDROOMS');
                        }
                        if($row->bath_room > 0){
                            $addInfo[] = OSPHelper::showBath($row->bath_room)." ".Text::_('OS_BATHROOMS');
                        }
                        if($row->rooms > 0){
                            $addInfo[] = $row->rooms." ".Text::_('OS_ROOMS');
                        }
                        ?>
                        var contentString<?php echo $row->id?> = '<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">'+
                            '<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">'+
                            '<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>"><img class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> thumbnail" src="<?php echo $row->photo?>" /></a>'+
                            '</div><div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?> ezitem-smallleftpad">'+
                            '<div class="row-fluid"><div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ospitem-maptitle title-blue"><a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid); ?>" title="<?php echo $title;?>"><?php echo $title;?></a></div></div>';
                        <?php
                        if(count($addInfo) > 0)
                        {
                            ?>
                            contentString<?php echo $row->id?> += '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad"><?php echo implode(" | ",$addInfo); ?></span></div>';
                            <?php
                        }
                        ?>
                                contentString<?php echo $row->id?> += '</p>'+
                            '</div>'+
                        '</div>';
                        <?php

                        if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "")
                        {
                            $type_icon  = OSPHelper::getTypeIcon($row->pro_type);
                            ?>
                            var propertyIcon = L.icon({iconUrl: '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>',
                                                                iconSize:     [33, 44] // size of the icon
                            });
                            var popupContent = contentString<?php echo $row->id?>;
                            var marker = L.marker([<?php echo $row->lat_add ?>, <?php echo $row->long_add;?>],{icon: propertyIcon});
                            marker.bindPopup(popupContent);
                            markerArray.push(marker);
                            markers.addLayer(marker);
                            latArr.push(<?php echo $row->lat_add ?>);
                            longArr.push(<?php echo $row->long_add;?>);

                            jQuery("#openmap<?php echo $row->mapid?>").click(function(){
                                var point = [ <?php echo $row->lat_add ?> , <?php echo $row->long_add;?> ];
                                mymap.flyTo(point,16);
                                marker.openPopup();
                            });
                            <?php
                        }
                    }
                    else
                    {
                        //having duplication
                        $row = $rows[$key];
                        $row->mapid = $i;
                        $itemIdArr = array();
                        $titleArr  = array();
                        $descArr   = array();

                        $needs = array();
                        $needs[] = "property_details";
                        $needs[] = $row->id;
                        $itemid	 = OSPRoute::getItemid($needs);
                        $itemIdArr[] = $itemid;

                        $title = "";
                        if(($row->ref!="") && ($configClass['show_ref'] == 1)){
                            $title .= $row->ref.",";
                        }
                        $title 		.= $row->pro_name;
                        $title  	 = str_replace("'","",$title);
                        $title 		 = htmlspecialchars($title);
                        $titleArr[]  = $title;

                        $addInfo = array();
                        if($row->bed_room > 0){
                            $addInfo[] = $row->bed_room." ".Text::_('OS_BEDROOMS');
                        }
                        if($row->bath_room > 0){
                            $addInfo[] = OSPHelper::showBath($row->bath_room)." ".Text::_('OS_BATHROOMS');
                        }
                        if($row->rooms > 0){
                            $addInfo[] = $row->rooms." ".Text::_('OS_ROOMS');
                        }
                        $desc = '<div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span4').'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping('span12').' thumbnail" src="'.$row->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping('span8').' ezitem-smallleftpad"><div class="row-fluid"><div class="'.$bootstrapHelper->getClassMapping('span12').' ospitem-maptitle title-blue"><a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'">'.$title.'</a></div></div>';
                        if(count($addInfo) > 0){
                            $desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
                        }
                        $desc .= '</p></div></div>';
                        $descArr[] = $desc;

                        foreach($item->value as $value){
                            $key  = OSPHelper::find_key($value,$rows);
                            $dupItem = $rows[$key];
                            $dupItem->mapid = $i;
                            $needs = array();
                            $needs[] = "property_details";
                            $needs[] = $dupItem->id;
                            $itemid	 = OSPRoute::getItemid($needs);
                            $itemIdArr[] = $itemid;

                            $title = "";
                            if(($dupItem->ref!="") && ($configClass['show_ref'] == 1)){
                                $title .= $dupItem->ref.",";
                            }
                            $title 		.= $dupItem->pro_name;
                            $title  	 = str_replace("'","",$title);
                            $title 		 = htmlspecialchars($title);
                            $titleArr[]  = $title;

                            $addInfo = array();
                            if($dupItem->bed_room > 0){
                                $addInfo[] = $dupItem->bed_room." ".Text::_('OS_BEDROOMS');
                            }
                            if($dupItem->bath_room > 0){
                                $addInfo[] = OSPHelper::showBath($dupItem->bath_room)." ".Text::_('OS_BATHROOMS');
                            }
                            if($dupItem->rooms > 0){
                                $addInfo[] = $dupItem->rooms." ".Text::_('OS_ROOMS');
                            }
                            $desc = '<div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span4').'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping('span12').' thumbnail" src="'.$dupItem->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping('span8').' ezitem-smallleftpad"><div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span12').' ospitem-maptitle title-blue"><a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'">'.$title.'</a></div></div>';
                            if(count($addInfo) > 0){
                                $desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
                            }
                            $desc .= '</p></div></div>';
                            $descArr[] = $desc;
                        }
                        $desc = implode('<div class="clearfix googleinfordiv"></div>',$descArr);

                        if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "")
                        {
                            $type_icon  = OSPHelper::getTypeIcon($row->pro_type);
                            ?>
                            var contentString<?php echo $row->id?> = '<?php echo $desc;?>';
                            var propertyIcon = L.icon({iconUrl: '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>',
                                                                iconSize:     [33, 44] // size of the icon
                            });
                            var popupContent = '<h5><?php echo Text::_('OS_MULTIPLE_PROPERTIES');?></h5>';
                            popupContent += contentString<?php echo $row->id?>;
                            var marker = L.marker([<?php echo $row->lat_add ?>, <?php echo $row->long_add;?>],{icon: propertyIcon});
                            marker.bindPopup(popupContent);
                            markerArray.push(marker);
                            markers.addLayer(marker);
                            latArr.push(<?php echo $row->lat_add ?>);
                            longArr.push(<?php echo $row->long_add;?>);

                            jQuery("#openmap<?php echo $row->mapid?>").click(function(){
                                var point = [ <?php echo $row->lat_add ?> , <?php echo $row->long_add;?> ];
                                mymap.flyTo(point,16);
                                marker.openPopup();
                            });
                            <?php
                        }
                    }
                }
            ?>
            mymap.addLayer(markers);
            <?php } ?>

			const locationItems = document.querySelectorAll('.property_item');

			// Thêm sự kiện hover vào từng item trong danh sách
			locationItems.forEach(item => {
				item.addEventListener('mouseover', function () {
					// Lấy thông tin latitude, longitude và nội dung popup từ data attributes
					const lat = parseFloat(item.getAttribute('data-lat'));
					const lng = parseFloat(item.getAttribute('data-lng'));
					//const popupContent = item.getAttribute('data-popup');
					
					if(lat != "" && lng != "")
					{
						const marker = findMarkerByLatLng(lat, lng);
						// Tìm marker tương ứng và mở popup
						//alert(marker.getLatLng().lat);
						if (marker) {
							marker.openPopup();
							mymap.panTo([lat, lng]);
						}
					}
				});
			});

			function findMarkerByLatLng(lat, lng) {
				let foundMarker = null;
				mymap.eachLayer(layer => {
					if (layer instanceof L.Marker) {
						const position = layer.getLatLng();
						if (position.lat === lat) {
							foundMarker = layer;
						}
					}
				});
				return foundMarker;
			}
        });
        </script>
        <?php
    }

    /**
     * Load Open Street Map in Details page
     * @param $property
     */
    public static function loadOpenStreetMapDetails($property,$configClass,$style="",$toggleposition = 0)
    {
        $db = Factory::getDbo();
        $db->setQuery("Select type_icon from #__osrs_types where id = '$property->pro_type'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        $rootUri = Uri::root(true);
        $document = Factory::getDocument()
            ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.js')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.css');
        $zoomLevel   = 16;
        $coordinates = $property->lat_add . ',' . $property->long_add;
        $onPopup = false;
        ?>
        <div id="googlemapdiv" style="<?php echo $style; ?>;" class="relative map2x"></div>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                var mymap = L.map('googlemapdiv').setView([<?php echo $property->lat_add; ?>, <?php echo $property->long_add; ?>], <?php echo $zoomLevel;?>);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox.streets',
                    zoom: <?php echo $zoomLevel;?>,
                }).addTo(mymap);

                <?php
                if($property->show_address == 1)
                {
                ?>
                    var propertyIcon = L.icon({iconUrl: '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>',
                                                        iconSize:     [33, 44] // size of the icon
                    });
                    var marker = L.marker([<?php echo $property->lat_add ?>, <?php echo $property->long_add;?>],{icon: propertyIcon}, {draggable: false}).addTo(mymap);
                <?php
                }
                else
                {
                    ?>
                    var circle = L.circle([<?php echo $property->lat_add ?>, <?php echo $property->long_add;?>], {
                        color: '#1D86A0',
                        fillColor: '#1D86A0',
                        fillOpacity: 0.5,
                        radius: 500
                    }).addTo(mymap);
                    <?php
                }
                ?>
                mymap.scrollWheelZoom.disable()
            });
        </script>
        <?php
    }

	/**
     * Load Google Map in Details page
     * @param $property
     */
    public static function loadOpenStreetMapDetailsClone($property,$configClass,$style="",$toggleposition = 0){
        $db = Factory::getDbo();
        $db->setQuery("Select type_icon from #__osrs_types where id = '$property->pro_type'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        $rootUri = Uri::root(true);
        $document = Factory::getDocument()
            ->addScript($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.js')
            ->addStyleSheet($rootUri . '/media/com_osproperty/assets/js/leaflet/leaflet.css');
        $zoomLevel   = 16;
        $coordinates = $property->lat_add . ',' . $property->long_add;
        $onPopup = false;
        ?>
        <div id="googlemapdiv1" style="<?php echo $style; ?>" class="relative map2x"></div>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                var mymap = L.map('googlemapdiv1').setView([<?php echo $property->lat_add; ?>, <?php echo $property->long_add; ?>], <?php echo $zoomLevel;?>);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    maxZoom: 18,
                    id: 'mapbox.streets',
                    zoom: <?php echo $zoomLevel;?>,
                }).addTo(mymap);

                <?php
                if($property->show_address == 1)
                {
                ?>
                var propertyIcon = L.icon({iconUrl: '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>',
                    iconSize:     [33, 44] // size of the icon
                });
                var marker = L.marker([<?php echo $property->lat_add ?>, <?php echo $property->long_add;?>],{icon: propertyIcon}, {draggable: false}).addTo(mymap);
                <?php
                }
                else
                {
                ?>
                var circle = L.circle([<?php echo $property->lat_add ?>, <?php echo $property->long_add;?>], {
                    color: '#1D86A0',
                    fillColor: '#1D86A0',
                    fillOpacity: 0.5,
                    radius: 500
                }).addTo(mymap);
                <?php
                }
                ?>
                mymap.scrollWheelZoom.disable()
            });
        </script>
        <?php
    }

	/**
	 * @param $post_code
	 * @param string $country
	 * @return bool
	 */
	public static function getLocationPostCode($post_code, $country_id = 0)
    {
        global $configClass;
        $db = Factory::getDbo();
		$url_zip = '';
		if($country_id > 0){
            $db->setQuery("Select country_name from #__osrs_countries where id = '$country_id'");
            $country = $db->loadResult();
			$url_zip = '?address='.urlencode($country);
			$url = self::$url.$url_zip."&components=postal_code:".urlencode($post_code);
		}else{
			$url = self::$url."?components=postal_code:".urlencode($post_code);
		}

        $googlekey = $configClass['goole_aip_key'];
		if($googlekey != ""){
			$url .= '&key='.$googlekey;
		}
		$resp_json = self::curl_file_get_contents($url);
		$resp = json_decode($resp_json, true);

		if($resp['status']='OK' && isset($resp['results'][0])){
			return $resp['results'][0]['geometry']['location'];
		}else{
			Factory::getApplication()->enqueueMessage($resp['error_message'],'error');
			return false;
		}
	}

    static private function curl_file_get_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);

        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }
}
?>