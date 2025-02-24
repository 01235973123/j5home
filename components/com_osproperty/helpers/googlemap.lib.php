<?php
/*------------------------------------------------------------------------
# googlemap.lib.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
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

class HelperOspropertyGoogleMap{
    static private $url = "https://maps.google.com/maps/api/geocode/json";
	public static function loadGoogleScript($suffix = false){
		global $configClass;
		OSPHelper::loadGoogleJS($suffix);
	}

	/**
	 * Get Lat Long address
	 *
	 * @param unknown_type $address
	 * @return unknown
	 */
	static function getLatlongAdd($address){
		$configClass = OSPHelper::loadConfig();
		if($configClass['goole_aip_key'] != ""){
			$key = "&key=".$configClass['goole_aip_key'];
		}else{
			$key = "";
		}
		$address = urlencode($address);
		$base_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false".$key;
		if(HelperOspropertyGoogleMap::_iscurlinstalled()){
			$ch = curl_init();
		    curl_setopt ($ch, CURLOPT_URL, $base_url);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		    $fileContents = curl_exec($ch);
		    curl_close($ch);
			if(trim($fileContents) == "")
			{
				$return_data = file_get_contents($base_url);
			}
		}else{
			$fileContents = file_get_contents($base_url);
		}
		$output =  json_decode($fileContents);
		$lat    =  $output->results[0]->geometry->location->lat;
		$long   =  $output->results[0]->geometry->location->lng;
		
		$return = array();
		$tmp = new stdClass();
		$tmp->lat = $lat;
		$tmp->long = $long;
		$return[0] = $tmp;
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
		
		function setMarkers(map, locations) {
		  
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
	 *
	 */
	static function loadGMapinEditProperty($geocode,$div_name,$lat_div,$long_div)
	{
		global $mainframe,$configClass;
		if (!isset($configClass['goole_map_overlay'])) $configClass['goole_map_overlay'] = 'ROADMAP';
		$icon = "house.png";
		self::loadGoogleScript();
		$document = Factory::getDocument();
		$document->addScriptDeclaration("
		  var propertyPoint = new google.maps.LatLng(".$geocode[0]->lat.",".$geocode[0]->long.");
		  var marker;
		  var map;
		  var geocoder;
		  var centerChangedLast;
		  var reverseGeocodedLast;
		  var currentReverseGeocodeResponse;
		  var markersArray = [];
		  function initialize() {
		    var mapOptions = {
		      zoom: ".$configClass['goole_map_resolution'].",
		      mapTypeId: google.maps.MapTypeId.ROADMAP,
		      mapTypeControl: true,
              navigationControl: true,
		      center: propertyPoint
		    };
			geocoder = new google.maps.Geocoder();
		    map = new google.maps.Map(document.getElementById('".$div_name."'),mapOptions);
		    marker = new google.maps.Marker({
		      map:map,
		      draggable:true,
		      animation: google.maps.Animation.DROP,
		      position: propertyPoint
		    });
		    markersArray.push(marker);
		    google.maps.event.addListener(marker, 'dragend', toggleBounce);
		  }

		  
		
		  function toggleBounce() {
		     if (marker.getAnimation() != null) {
		       marker.setAnimation(null);
		     } else {
		       marker.setAnimation(google.maps.Animation.BOUNCE);
		     }
		     var point = marker.getPosition();
		     map.panTo(point);
		     document.getElementById('".$lat_div."').value = point.lat().toFixed(5);
		     document.getElementById('".$long_div."').value = point.lng().toFixed(5);
		  }
		  
		  function showAddress(address){
			  geocoder.geocode({'address': address,'partialmatch': true}, geocodeResult);
		  }
		  function geocodeResult(results, status) {
		    if (status == 'OK' && results.length > 0) {
		      map.fitBounds(results[0].geometry.viewport);
		      
		      marker.setPosition(map.getCenter());
		      marker.setMap(map);
			  //markersArray.push(marker);
			  google.maps.event.addListener(marker, 'dragend', toggleBounce);
			  document.getElementById('".$lat_div."').value = map.getCenter().lat().toFixed(5);
		      document.getElementById('".$long_div."').value = map.getCenter().lng().toFixed(5);
		    } else {
		      alert('Geocode was not successful for the following reason: ' + status);
		    }
		  }
		 	
	      function addMarkerAtCenter() {
		    var marker = new google.maps.Marker({
		        position: map.getCenter(),
		        map: map
		    });
	      }
	      
	      function addMarker(location) {
			  marker = new google.maps.Marker({
			    position: location,
			    map: map
			  });
			  markersArray.push(marker);
			}
			
			// Removes the overlays from the map, but keeps them in the array
			function clearOverlays() {
			  if (markersArray) {
			    for (i in markersArray) {
			      markersArray[i].setMap(null);
			    }
			  }
			}

			window.onload=function(){
                if(window.initialize) initialize();
            }
		");
	}
	
	/**
	 * Load Locator Map
	 *
	 * @param unknown_type $rows
	 * @param unknown_type $mapdiv
	 */
	static function loadLocatorMap($rows,$mapdiv,$zoomlevel,$search_lat,$search_long){
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
	 * Find Address
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @return unknown
	 */
	public static function findAddress($option,$row,$address,$type){
		global $configClass;
		if($configClass['goole_aip_key'] != ""){
			$key = "&key=".$configClass['goole_aip_key'];
		}else{
			$key = "";
		}
		if($type == 0){
			$db = Factory::getDbo();
			$address = "";
			if($row->address != ""){
				$address .= $row->address;
			}
			if($row->city != ""){
				$address .= " ".$row->city;
			}
			if($row->state != ""){
				$db->setQuery("Select state_name from #__osrs_states where id = '$state'");
				$state_name = $db->loadResult();
				$address .= " ".$state_name;
				$db->setQuery("Select country_id from #__osrs_states where id = '$state'");
				$country_id = $db->loadResult();
				$db->setQuery("Select country_name from #__osrs_countries where id = '$country_id'");
				$country_name = $db->loadResult();
				$address .= " ".$country_name;
			}
			$address = trim($address);
		}
		
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false".$key;
		if(HelperOspropertyGoogleMap::_iscurlinstalled())
		{
			$ch = curl_init();
		    curl_setopt ($ch, CURLOPT_URL, $url);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		    $return_data = curl_exec($ch);
			//echo $return_data;die();
		    curl_close($ch);
			if(trim($return_data) == "")
			{
				$return_data = file_get_contents($url);
			}
		}
		else
		{
			$return_data = file_get_contents($url);
		}
		
		$return_data = json_decode($return_data);
		$return_location = $return_data->results;
		$return = array();
		if($return_data->status == "OK"){
			$return[0] = $return_location[0]->geometry->location->lat;
			$return[1] = $return_location[0]->geometry->location->lng;
			$return[2] = $return_data->status;
		}
		return $return;
	}
	
	/**
	 * Load Street View Map
	 *
	 * @param unknown_type $geocode
	 * @param unknown_type $divname
	 */
	public static function loadStreetViewMap($geocode,$divname){
		global $mainframe;
		$lat = $geocode[0]->lat;
		$long = $geocode[0]->long;
        OSPHelper::loadGoogleJS('');
		?>
		<script type="text/javascript">
		function initialize() {
		  var fenway = new google.maps.LatLng(<?php echo $lat?>,<?php echo $long?>);
		  // Note: constructed panorama objects have visible: true
		  // set by default.
		  var panoOptions = {
		    position: fenway,
		    addressControlOptions: {
		      position: google.maps.ControlPosition.BOTTOM
		    },
		    linksControl: false,
		    panControl: false,
		    zoomControlOptions: {
		      style: google.maps.ZoomControlStyle.SMALL
		    },
		    enableCloseButton: false
		  };
		
		  var panorama = new google.maps.StreetViewPanorama(
		      document.getElementById("pano"), panoOptions);
		}
		</script>
		</head>
		<body onload="initialize()">
		  <div id="pano"></div>
		</body>
		</html>
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
		if($configClass['goole_aip_key'] != "")
		{
			$key = "&key=".$configClass['goole_aip_key'];
		}
		else
		{
			$key = "";
		}
		//find default lat/long addresses
        $show_map = 0;
		$city_name = "";
		$state_name = "";
		$db = Factory::getDbo();
		if($state_id > 0)
		{
			$db->setQuery("Select state_name from #__osrs_states where id = '$state_id'");
			$state_name = $db->loadResult();
			
			$db->setQuery("Select country_id from #__osrs_states where id = '$state_id'");
			$country_id = $db->loadResult();
			
			$db->setQuery("Select country_name from #__osrs_countries where id = '$country_id'");
			$country_name = $db->loadResult();
		}
		if($city_id > 0)
		{
			$db->setQuery("Select city from #__osrs_cities where id = '$city_id'");
			$city_name = $db->loadResult();
		}
		
		if(($city_name != "") or ($state_name != ""))
		{
			$address = "";
			if($city_name != ""){
				$address = $city_name;
			}
			if($state_name != ""){
				$address .= " ".$state_name;
			}
			if($country_name != ""){
				$address .= " ".$country_name;
			}
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false".$key;
			if(HelperOspropertyGoogleMap::_iscurlinstalled())
			{
				$ch = curl_init();
			    curl_setopt ($ch, CURLOPT_URL, $url);
			    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
			    $return_data = curl_exec($ch);
			    curl_close($ch);
				if(trim($return_data) == "")
				{
					$return_data = file_get_contents($url) or die("url not loading");
				}
			}
			else
			{
				$return_data = file_get_contents($url) or die("url not loading");
			}
			$return_data = json_decode($return_data);
			$return_location = $return_data->results;
			$return = array();
			if($return_data->status == "OK"){
				$default_lat  = $return_location[0]->geometry->location->lat;
				$default_long = $return_location[0]->geometry->location->lng;
			}
		}else{
			$default_lat  = $configClass['goole_default_lat'];
			$default_long = $configClass['goole_default_long'];
		}
		
		$duplicate = OSPHelper::findGoogleDuplication($rows);

        OSPHelper::loadGoogleJS('');
		?>

		<script type='text/javascript' src='<?php echo Uri::root()?>media/com_osproperty/assets/js/markerclusterer.js' defer="defer"></script>
		<script type="text/javascript">
		  var map;
		  var panorama;
		  var centerPlace = new google.maps.LatLng(<?php echo $default_lat; ?>, <?php echo $default_long; ?>);
		  var bounds = new google.maps.LatLngBounds();
		  var gmarkers = [];
		  var markerArray = [];
		  <?php
			$min = 0.9999999;
			$max = 1.00000000;
			$start_point = 1;
			//for($i=0; $i<count($rows)-1; $i++){
		  		//$obj1 = $rows[$i];
		  		//for($j = 1;$j<count($rows);$j++){
		  			//$obj2 = $rows[$j];
		  			//if(($obj1->lat_add == $obj2->lat_add) and ($obj1->long_add == $obj2->long_add)){
		  				//$obj2->lat_add  =  $obj2->lat_add * ($start_point * ($max - $min) + $min);
		  				//$obj2->long_add =  $obj2->long_add * ($start_point * ($max - $min) + $min);
		  				//$start_point++;
		  			//}
		  		//}
		    //}
		    
		  for($i=0;$i<count($duplicate);$i++){
			  $item = $duplicate[$i];
			  $key  = OSPHelper::find_key($item->id,$rows);
			  if(($rows[$key]->show_address == 1) and ($rows[$key]->lat_add != "") and ($rows[$key]->long_add != "")){
				  ?>
					 var propertyListing<?php echo $rows[$key]->id?> = new google.maps.LatLng(<?php echo $rows[$key]->lat_add; ?>, <?php echo $rows[$key]->long_add; ?>);
					 bounds.extend(propertyListing<?php echo $rows[$key]->id?>);
				  <?php
		  	  }
		  }

		  $google_map_overlay = $configClass['goole_map_overlay'];
	      if($google_map_overlay == ""){
			 $google_map_overlay = "ROADMAP";
		  }
		  ?>
		  var infowindow;
		  var map;
		  //var bounds;
		  var markers = [];
		  var markerIndex=0;
		  
		  function initMap() {
			// Set up the map
			var mapOptions = {
				center: centerPlace,
				scrollwheel: false,
				zoom: 12,
				streetViewControl: true,
				mapTypeId: google.maps.MapTypeId.<?php echo $google_map_overlay;?>
			};
			map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
			//map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));
			map.fitBounds(bounds);
			var listener = google.maps.event.addListener(map, "idle", function() { 
			if (map.getZoom() > 18) map.setZoom(18); 
			  google.maps.event.removeListener(listener); 
			});
			var infoWindow = new google.maps.InfoWindow();
	 		var markerBounds = new google.maps.LatLngBounds();

	 		function makeMarker(options){
				  var pushPin = new google.maps.Marker({map:map});
				  pushPin.setOptions(options);
				  google.maps.event.addListener(pushPin, 'click', function(){
					    infoWindow.setOptions(options);
					    infoWindow.open(map, pushPin);
					    map.panTo(pushPin.getPosition());
						map.setZoom(18);
				  });
				  markerArray.push(pushPin);
				  return pushPin;
			}
			
			google.maps.event.addListener(map, 'click', function(){
			   infoWindow.close();
			});
			 
			 <?php
			  for($i=0;$i<count($duplicate);$i++){
		
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
					if(($row->ref!="") and ($configClass['show_ref'] == 1)){
						$title .= $row->ref.",";
					}
					$title 		.= $row->pro_name;
					$title  	 = str_replace("'","",$title);
					$title 		 = htmlspecialchars($title);
					$created_on  = $row->created;
					$modified_on = $row->modified;
					$created_on  = strtotime($created_on);
					$modified_on = strtotime($modified_on);
					
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
						'<div class="row-fluid"><div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ospitem-maptitle title-blue"><?php echo $title;?></div></div>';
					 <?php 
					 if(count($addInfo) > 0){
					 ?>
						 contentString<?php echo $row->id?> += '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad"><?php echo implode(" | ",$addInfo); ?></span></div>';
					 <?php
					 }
					 ?>
					 contentString<?php echo $row->id?> += '<?php echo htmlspecialchars(str_replace("'","\"",str_replace("\r","",str_replace("\n","",$row->pro_small_desc))));?> <a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>"><?php echo Text::_('OS_DETAILS');?></a></p>'+
						'</div>'+
						'</div>';
						<?php
						if(($row->show_address == 1) and ($row->lat_add != "") and ($row->long_add != "")){
							$show_map = 1;
							$db->setQuery("Select type_icon from #__osrs_types where id = '$row->pro_type'");
							$type_icon = $db->loadResult();
							if($type_icon == ""){
								$type_icon = "1.png";
							}
							?>
							makeMarker({
								position: propertyListing<?php echo $row->id?>,
								title: "<?php echo $title;?>",
								content: contentString<?php echo $row->id?>,
								icon:new google.maps.MarkerImage('<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>')
							});
							<?php
					   }
				  }else{ //having duplication
					
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
					if(($row->ref!="") and ($configClass['show_ref'] == 1)){
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
					$desc = '<div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span4').'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping('span12').' thumbnail" src="'.$row->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping('span8').' ezitem-smallleftpad"><div class="row-fluid"><div class="'.$bootstrapHelper->getClassMapping('span12').' ospitem-maptitle title-blue">'.$title.'</div></div>';
					if(count($addInfo) > 0){
						$desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
					}
					$desc .= htmlspecialchars(str_replace("'","\"",str_replace("\r","",str_replace("\n","",$row->pro_small_desc)))).'<a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'">'.Text::_('OS_DETAILS').'</a></p></div></div>';
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
						if(($dupItem->ref!="") and ($configClass['show_ref'] == 1)){
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
						$desc = '<div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span4').'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping('span12').' thumbnail" src="'.$dupItem->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping('span8').' ezitem-smallleftpad"><div class="'.$bootstrapHelper->getClassMapping('row-fluid').'"><div class="'.$bootstrapHelper->getClassMapping('span12').' ospitem-maptitle title-blue">'.$title.'</div></div>';
						if(count($addInfo) > 0){
							$desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
						}
						$desc .= htmlspecialchars(str_replace("'","\"",str_replace("\r","",str_replace("\n","",$dupItem->pro_small_desc)))).'<a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'">'.Text::_('OS_DETAILS').'</a></p></div></div>';
						$descArr[] = $desc;
					}

					$desc = implode('<div class="clearfix googleinfordiv"></div>',$descArr);
					?>
						
					 var contentString<?php echo $row->id?> = '<?php echo $desc;?>';
						<?php
						if(($row->show_address == 1) and ($row->lat_add != "") and ($row->long_add != "")){
							$show_map = 1;
							$db->setQuery("Select type_icon from #__osrs_types where id = '$row->pro_type'");
							$type_icon = $db->loadResult();
							if($type_icon == ""){
								$type_icon = "1.png";
							}
							?>
							makeMarker({
								position: propertyListing<?php echo $row->id?>,
								title: "<?php echo Text::_('OS_MULTIPLE_PROPERTIES');?>",
								content: contentString<?php echo $row->id?>,
								icon:new google.maps.MarkerImage('<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>')
							});
							<?php
					   }
						
				  }
			  }
			  for($i=0;$i<count($duplicate);$i++){
			  	  ?>
			  	  gmarkers.push(markerArray[<?php echo $i?>]);
			  	  <?php 
			  }
			  
			  ?>
			  clusterStyles = [
                  {
                      textColor: '#ffffff',
                      opt_textColor: '#ffffff',
                      url: '<?php echo Uri::root()?>media/com_osproperty/assets/images/cloud.png',
                      height: 72,
                      width: 72,
                      textSize: 15,
                  }
              ];
              var mcOptions = {gridSize: 50, maxZoom: 15, styles: clusterStyles};
			  var markerCluster = new MarkerClusterer(map,gmarkers,mcOptions);
		  }

		  window.onload=function(){
			if(window.initMap) initMap();
		  }
		  window.onunload=function(){
		    if(GUnload) GUnload();
		  }
		  function openMarker(i){
		  	   //location.hash = "#map_canvas";
			   jQuery("html, body").scrollTop(jQuery("#map_canvas").offset().top);
			   google.maps.event.trigger(markerArray[i],'click');
		  };
		</script>
		<?php
        if($show_map == 0){
            return false;
        }else{
            return true;
        }
	}

    /**
     * Load Google Map in Details page
     * @param $property
     */
    public static function loadGoogleMapDetails($property,$configClass,$style="",$toggleposition = 0){
        $db = Factory::getDbo();
        if($property->lat_add == '' && $property->long_add == ''){
            return;
        }
        $google_map_overlay = $configClass['goole_map_overlay'];
        if($google_map_overlay == ""){
            $google_map_overlay = "ROADMAP";
        }
        $google_map_resolution = $configClass['goole_map_resolution'];
        if($google_map_resolution == 0){
            $google_map_resolution = 15;
            $population = 150;
        }elseif(($google_map_resolution > 0) and ($google_map_resolution <= 5)){
            $population = 400000;
        }elseif(($google_map_resolution > 5) and ($google_map_resolution <= 10)){
            $population = 2000;
        }elseif(($google_map_resolution > 10) and ($google_map_resolution <= 15)){
            $population = 150;
        }else{
            $population = 100;
        }

        $db->setQuery("Select type_icon from #__osrs_types where id = '$property->pro_type'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        ?>
	
        <script type="text/javascript">
			var style = [
				{
					"featureType": "all",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"weight": "2.00"
						}
					]
				},
				{
					"featureType": "all",
					"elementType": "geometry.stroke",
					"stylers": [
						{
							"color": "#9c9c9c"
						}
					]
				},
				{
					"featureType": "all",
					"elementType": "labels.text",
					"stylers": [
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "all",
					"stylers": [
						{
							"color": "#f2f2f2"
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "landscape.man_made",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "poi",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "all",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 45
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#eeeeee"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"color": "#7b7b7b"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "labels.text.stroke",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "road.highway",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "road.arterial",
					"elementType": "labels.icon",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "transit",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "all",
					"stylers": [
						{
							"color": "#46bcec"
						},
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#c8d7d4"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"color": "#070707"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "labels.text.stroke",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				}
			]
            // <![CDATA[
            var map;
            var panorama;
            var centerPlace = new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>);
            var propertyListing = new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>);
            var citymap = {};
            citymap['chicago'] = {
                center: new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>),population: <?php echo $population;?>
            };

            function initMapPropertyDetails() {
                // Set up the map
				<?php
				$limit_zoom = $configClass['limit_zoom'];
				if((int)$limit_zoom == 0){
					$limit_zoom = 18;
				}
				?>
                var streetview = new google.maps.StreetViewService();
                var mapOptions = {
                    center: centerPlace,
                    scrollwheel: false,
                    zoom: <?php echo $google_map_resolution;?>,
                    mapTypeId: google.maps.MapTypeId.<?php echo $google_map_overlay?>,
					maxZoom:<?php echo $limit_zoom?>,
                    streetViewControl: false
                };
                map = new google.maps.Map(document.getElementById('googlemapdiv'), mapOptions);
				<?php
                if($google_map_overlay == "ROADMAP"){
                ?>
                var styledMap = new google.maps.StyledMapType(style,{name: 'Styled Map'});
				map.mapTypes.set('map_style', styledMap);
                map.setMapTypeId('map_style');
				<?php } ?>
				map.addListener('click', function() {
				    if(map.scrollwheel === false){
						map.setOptions({'scrollwheel': true});
						jQuery('#googlemapdiv').addClass('scrollon');
					}else{
						map.setOptions({'scrollwheel': false});
						jQuery('#googlemapdiv').removeClass('scrollon');
					}
				});

                // Setup the markers on the map
                <?php if($property->show_address == 1){?>
                var propertyListingMarkerImage =
                    new google.maps.MarkerImage(
                        '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>');
                var propertyListingMarker = new google.maps.Marker({
                    position: propertyListing,
                    map: map,
                    icon: propertyListingMarkerImage,
                    title: 'Property ID <?php echo $property->id?>'
                });
                <?php }else {?>
                for (var city in citymap) {
                    var populationOptions = {
                        strokeColor: '#1D86A0',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#1D86A0',
                        fillOpacity: 0.35,
                        map: map,
                        center: citymap[city].center,
                        radius: Math.sqrt(citymap[city].population) * 100
                    };
                    // Add the circle for this city to the map.
                    cityCircle = new google.maps.Circle(populationOptions);
                }
                <?php } ?>

                // We get the map's default panorama and set up some defaults.
                // Note that we don't yet set it visible.
                panorama = map.getStreetView();
                streetview.getPanoramaByLocation(centerPlace, 25, function(data, status){
                    switch(status){
                        case google.maps.StreetViewStatus.OK:
                            panorama.setPosition(centerPlace);
                            panorama.setPov({
                                    heading: 265,
                                    zoom:1,
                                    pitch:0}
                            );
                            break;
                        case google.maps.StreetViewStatus.ZERO_RESULTS:
                            document.getElementById('togglebtn').style.display = "none";
                            break;
                        default:
                            document.getElementById('togglebtn').style.display = "none";
                    }
                });
            }
            function toggleStreetView() {
                var toggle = panorama.getVisible();
                if (toggle == false) {
                    panorama.setVisible(true);
                } else {
                    panorama.setVisible(false);
                }
            }
            window.onload=function(){
                if(window.initMapPropertyDetails) initMapPropertyDetails();
            }
            window.onunload=function(){
                if(typeof(GUnload)!="undefined") GUnload();
            }
            // ]]>
            </script>
        <?php
        if(($configClass['show_streetview'] == 1) and ($property->show_address == 1) and ($toggleposition == 0)){
            ?>
            <div id="toggle">
                <input type="button" id="togglebtn" class="btn btn-info btn-small" value="<?php echo Text::_('OS_TOGGLE_STREET_VIEW');?>" onclick="toggleStreetView();" />
            </div>
        <?php } ?>
        <div id="googlemapdiv" style="<?php echo $style; ?>" class="relative map2x"></div>
        <?php
        if(($configClass['show_streetview'] == 1) and ($property->show_address == 1) and ($toggleposition == 1)){
            ?>
            <div id="toggle">
                <input type="button" id="togglebtn" class="btn btn-info btn-small" value="<?php echo Text::_('OS_TOGGLE_STREET_VIEW');?>" onclick="toggleStreetView();" />
            </div>
        <?php } ?>
        <?php
    }

	/**
     * Load Google Map in Details page
     * @param $property
     */
    public static function loadGoogleMapDetailsClone($property,$configClass,$style="",$toggleposition = 0){
        $db = Factory::getDbo();
        $google_map_overlay = $configClass['goole_map_overlay'];
        if($google_map_overlay == ""){
            $google_map_overlay = "ROADMAP";
        }
        $google_map_resolution = $configClass['goole_map_resolution'];
        if($google_map_resolution == 0){
            $google_map_resolution = 15;
            $population = 150;
        }elseif(($google_map_resolution > 0) and ($google_map_resolution <= 5)){
            $population = 400000;
        }elseif(($google_map_resolution > 5) and ($google_map_resolution <= 10)){
            $population = 2000;
        }elseif(($google_map_resolution > 10) and ($google_map_resolution <= 15)){
            $population = 150;
        }else{
            $population = 100;
        }

        $db->setQuery("Select type_icon from #__osrs_types where id = '$property->pro_type'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        ?>
		<?php
        if(($configClass['show_streetview'] == 1) and ($property->show_address == 1) and ($toggleposition == 0)){
            ?>
            <div id="toggle">
                <input type="button" id="togglebtn" class="btn btn-info btn-small" value="<?php echo Text::_('OS_TOGGLE_STREET_VIEW');?>" onclick="toggleStreetView1();" />
            </div>
        <?php } ?>
        <div id="googlemapdiv1" style="<?php echo $style; ?>" class="relative map2x"></div>
        <?php
        if(($configClass['show_streetview'] == 1) and ($property->show_address == 1) and ($toggleposition == 1)){
            ?>
            <div id="toggle">
                <input type="button" id="togglebtn" class="btn btn-info btn-small" value="<?php echo Text::_('OS_TOGGLE_STREET_VIEW');?>" onclick="toggleStreetView1();" />
            </div>
        <?php } ?>
        <script type="text/javascript">
			var style = [
				{
					"featureType": "all",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"weight": "2.00"
						}
					]
				},
				{
					"featureType": "all",
					"elementType": "geometry.stroke",
					"stylers": [
						{
							"color": "#9c9c9c"
						}
					]
				},
				{
					"featureType": "all",
					"elementType": "labels.text",
					"stylers": [
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "all",
					"stylers": [
						{
							"color": "#f2f2f2"
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "landscape.man_made",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "poi",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "all",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 45
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#eeeeee"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"color": "#7b7b7b"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "labels.text.stroke",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				},
				{
					"featureType": "road.highway",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "road.arterial",
					"elementType": "labels.icon",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "transit",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "all",
					"stylers": [
						{
							"color": "#46bcec"
						},
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#c8d7d4"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"color": "#070707"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "labels.text.stroke",
					"stylers": [
						{
							"color": "#ffffff"
						}
					]
				}
			]
            // <![CDATA[
            var map1;
            var panorama1;
            var centerPlace = new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>);
            var propertyListing = new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>);
            var citymap = {};
            citymap['chicago'] = {
                center: new google.maps.LatLng(<?php echo $property->lat_add?>, <?php echo $property->long_add?>),population: <?php echo $population;?>
            };

            function initMapPropertyDetails1() {
                // Set up the map
				<?php
				$limit_zoom = $configClass['limit_zoom'];
				if((int)$limit_zoom == 0){
					$limit_zoom = 18;
				}
				?>
                var streetview = new google.maps.StreetViewService();
                var mapOptions = {
                    center: centerPlace,
                    scrollwheel: false,
                    zoom: <?php echo $google_map_resolution;?>,
                    mapTypeId: google.maps.MapTypeId.<?php echo $google_map_overlay?>,
					maxZoom:<?php echo $limit_zoom?>,
                    streetViewControl: false
                };
                map1 = new google.maps.Map(document.getElementById('googlemapdiv1'), mapOptions);
                <?php
                if($google_map_overlay == "ROADMAP"){
                ?>
                var styledMap = new google.maps.StyledMapType(style,{name: 'Styled Map'});
                map1.mapTypes.set('map_style', styledMap);
                map1.setMapTypeId('map_style');
                <?php } ?>
				map1.addListener('click', function() {
				    if(map1.scrollwheel === false){
						map1.setOptions({'scrollwheel': true});
						jQuery('#googlemapdiv1').addClass('scrollon');
					}else{
						map1.setOptions({'scrollwheel': false});
						jQuery('#googlemapdiv1').removeClass('scrollon');
					}
				});
				
                // Setup the markers on the map
                <?php if($property->show_address == 1){?>
                var propertyListingMarkerImage =
                    new google.maps.MarkerImage(
                        '<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>');
                var propertyListingMarker = new google.maps.Marker({
                    position: propertyListing,
                    map: map1,
                    icon: propertyListingMarkerImage,
                    title: 'Property ID <?php echo $property->id?>'
                });
                <?php }else {?>
                for (var city in citymap) {
                    var populationOptions = {
                        strokeColor: '#1D86A0',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#1D86A0',
                        fillOpacity: 0.35,
                        map: map1,
                        center: citymap[city].center,
                        radius: Math.sqrt(citymap[city].population) * 100
                    };
                    // Add the circle for this city to the map.
                    cityCircle = new google.maps.Circle(populationOptions);
                }
                <?php } ?>

                // We get the map's default panorama and set up some defaults.
                // Note that we don't yet set it visible.
                panorama1 = map1.getStreetView();
                streetview.getPanoramaByLocation(centerPlace, 25, function(data, status){
                    switch(status){
                        case google.maps.StreetViewStatus.OK:
                            panorama1.setPosition(centerPlace);
                            panorama1.setPov({
                                    heading: 265,
                                    zoom:1,
                                    pitch:0}
                            );
                            break;
                        case google.maps.StreetViewStatus.ZERO_RESULTS:
                            document.getElementById('togglebtn').style.display = "none";
                            break;
                        default:
                            document.getElementById('togglebtn').style.display = "none";
                    }
                });
            }

            function toggleStreetView1() {
                var toggle = panorama1.getVisible();
                if (toggle == false) {
                    panorama1.setVisible(true);
                } else {
                    panorama1.setVisible(false);
                }
            }

            //window.onload=function(){
                initMapPropertyDetails1();
            //}

            window.onunload=function(){
                if(typeof(GUnload)!="undefined") GUnload();
            }
            // ]]>
            </script>
        
        <?php
    }


    /**
     * Load Google Map in Details page
     * @param $property
     */
    public static function loadYandexDetails($property,$configClass,$style="position:relative;width: 100%; height: 300px")
    {
        $db = Factory::getDbo();
        $db->setQuery("Select type_icon from #__osrs_types where id = '$property->pro_type'");
        $type_icon = $db->loadResult();
        if($type_icon == ""){
            $type_icon = "1.png";
        }
        $google_map_overlay = $configClass['goole_map_overlay'];
        if($google_map_overlay == ""){
            $google_map_overlay = "ROADMAP";
        }
        $google_map_overlay = strtolower($google_map_overlay);
        $google_map_resolution = $configClass['goole_map_resolution'];
        if($google_map_overlay == "roadmap"){
            $google_map_overlay = "map";
        }
        if($google_map_resolution == 0){
            $google_map_resolution = 15;
            $population = 150;
        }elseif(($google_map_resolution > 0) and ($google_map_resolution <= 5)){
            $population = 400000;
        }elseif(($google_map_resolution > 5) and ($google_map_resolution <= 10)){
            $population = 2000;
        }elseif(($google_map_resolution > 10) and ($google_map_resolution <= 15)){
            $population = 150;
        }else{
            $population = 100;
        }

        $language = Factory::getLanguage();
        $activate_lang = $language->getTag();
        $document = Factory::getDocument();
        $document->addScript("http://api-maps.yandex.ru/2.0/?coordorder=longlat&amp;load=package.full&amp;lang=".$activate_lang);
        ?>
        <div id="YMapsID" style="<?php echo $style;?>"></div><div id="YMapsCredit" class="zhym-credit"></div><div id="YMapsMainRoutePanel"><div id="YMapsMainRoutePanel_Total"></div></div><div id="YMapsRoutePanel"><div id="YMapsRoutePanel_Description"></div><div id="YMapsRoutePanel_Total"></div><div id="YMapsRoutePanel_Steps"></div></div>
        <script type="text/javascript" >/*<![CDATA[*/
        var map, mapcenter, geoResult, geoRoute;
        var searchControl, searchControlPMAP;
        var clustermarkers0;
        var markerCluster0;
        ymaps.ready(initialize);
        function initialize () {
            mapcenter = [<?php echo $property->lat_add;?>, <?php echo $property->long_add;?>];
            map = new ymaps.Map("YMapsID", {
                center: mapcenter,
                zoom: <?php echo $google_map_resolution;?>
            });
            geoResult = new ymaps.GeoObjectCollection();
            clustermarkers0 = [];
            markerCluster0 = new ymaps.Clusterer({ maxZoom: 14
                , clusterDisableClickZoom: false
                , synchAdd: false
                , showInAlphabeticalOrder: false
                , gridSize: 64
                , minClusterSize: 2
            });
            map.geoObjects.add(markerCluster0);
            if (map.behaviors.isEnabled('dblClickZoom'))
                map.behaviors.disable('dblClickZoom');
            map.behaviors.enable('scrollZoom');
            map.controls.add(new ymaps.control.ZoomControl());
            map.setType("yandex#<?php echo $google_map_overlay;?>");
            map.setZoom(9);
            map.options.set("minZoom",1);
            map.events.add("typechange", function (e) {
                map.zoomRange.get(map.getCenter()).then(function (range) {
                    if (map.getZoom() > range[1] )
                    {
                        map.setZoom(range[1]);
                    }
                });
            });
            minimap = new ymaps.control.MiniMap();
            minimap.expand();
            //map.controls.add(minimap);
            var toolbar = new ymaps.control.MapTools();
            map.controls.add(toolbar);
            document.getElementById("YMapsCredit").innerHTML = '';
            if (map.behaviors.isEnabled('rightMouseButtonMagnifier'))
                map.behaviors.disable('rightMouseButtonMagnifier');
            map.behaviors.enable('drag');
            //map.balloon.open([<?php echo $property->lat_add;?>, <?php echo $property->long_add;?>]);
            var latlng1= [<?php echo $property->lat_add;?>, <?php echo $property->long_add;?>];
            var placemark1= new ymaps.Placemark(latlng1);
            placemark1.options.set("hasBalloon", true);
           // placemark1.properties.set("hintContent", '');
            placemark1.options.set("iconImageHref", "<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>");
            placemark1.options.set("iconImageSize", [34,44]);
            //placemark1.options.set("iconImageOffset", [-7,-29]);
            var contentStringHead1 = '<div id="placemarkContent1">' +
                '<h1 id="headContent1" class="placemarkHead"><?php echo $property->pro_name; ?></h1>'+
                '</div>';
            var contentStringHeadCluster1 = '<div id="placemarkContentCluster1">' +
                '<span id="headContentCluster1" class="placemarkHeadCluster"><?php echo $property->pro_name; ?></span>'+
                '</div>';
            //var contentStringBody1 = '<div id="bodyContent1"  class="placemarkBody"></div>';
            //placemark1.properties.set("balloonContentHeader", contentStringHead1);
            //placemark1.properties.set("balloonContentBody", contentStringBody1);
            //placemark1.events.add("click", function (e) {
            //});
            clustermarkers0.push(placemark1);
            //placemark1.properties.set("clusterCaption", contentStringHeadCluster1);
            markerCluster0.add(clustermarkers0);
        }
        function PlacemarkByIDShow(p_id, p_action, p_zoom) {
            //alert("This feature is supported only when you enable it in map menu item property!");
        }
        /*]]>*/</script>
        <?php
    }

	/**
	 * @param $post_code
	 * @param string $country
	 * @return bool
	 */
	public static function getLocationPostCode($post_code, $country_id = 0){
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

    static private function curl_file_get_contents($URL){
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