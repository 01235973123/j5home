<?php
/*------------------------------------------------------------------------
# locator.html.php - Ossolution Property
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
class HTML_OspropertyLocator
{
	/**
	 * Locator Search Html
	 *
	 * @param unknown_type $option
	 * @param unknown_type $agent
	 */
	static function locatorSearchHtml($option,$rows,$lists,$locator_type,$search_lat,$search_long,$style){
        global $bootstrapHelper, $mainframe, $configClass, $jinput;
        if ($search_lat == "") {
            $search_lat = $configClass['goole_default_lat'];
        }
        if ($search_long == "") {
            $search_long = $configClass['goole_default_long'];
        }
		$db = Factory::getDbo();
		$country_code = "";
		if(!HelperOspropertyCommon::checkCountry()){
			$country_id = HelperOspropertyCommon::getDefaultCountry();
			$db->setQuery("Select country_code from #__osrs_countries where id = '$country_id'");
			$country_code = $db->loadResult();
		}
        
        OSPHelper::loadTooltip();
        $division_col = 0;

        $mapheight = 800;

        ?>
        <script type="text/javascript">
            function checkCats() {
                var cat_elements = document.getElementsByName('categoryArr[]');
                var check_all_cats = document.getElementById('check_all_cats');
                if (check_all_cats.value == 1) {
                    check_all_cats.value = 0;
                    for (var i = 0; i < cat_elements.length; i++) {
                        cat_elements[i].checked = false;
                    }
                } else {
                    check_all_cats.value = 1;
                    for (var i = 0; i < cat_elements.length; i++) {
                        cat_elements[i].checked = true;
                    }
                }
            }
            function submitForm() {
                var radius_search = document.getElementById('radius_search');
                if (radius_search.value != "") {
                    document.profileForm.submit();
                } else {
                    document.profileForm.submit();
                }
            }

            function checkingLocatorForm() {
                var form = document.profileForm;
                var location = form.location;
                if (location.value == "") {
                    alert("<?php echo Text::_('OS_PLEASE_ENTER_ADDRESS');?>");
                    location.focus();
                } else {
                    document.profileForm.submit();
                }
            }
        </script>

		<?php
		OSPHelper::generateHeading(2, Text::_('OS_SEARCH_LOCATOR'));
		?>

		<div id="notice" class="nodisplay">
		</div>
    <div class="clearfix"></div>
	<form method="POST"
      action="<?php echo Route::_('index.php?option=com_osproperty&view=lsearch&Itemid=' . $jinput->getInt('Itemid', 0))?>"
      name="profileForm" id="profileForm" enctype="multipart/form-data">
    <div class="mainframe_search osp-container">
        <?php
		//$style = "style2";
		if($style == "style1"){
			if (($configClass['adv_type_ids'] == "0") or ($configClass['adv_type_ids'] == "")) {
				HelperOspropertyCommon::generateLocatorForm($lists, $locator_type);
			} else {
				$locator_type_ids = $configClass['adv_type_ids'];
				$locator_type_idsArr = explode("|", $locator_type_ids);
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<ul class="nav nav-tabs">
							<?php
							for ($i = 0; $i < count($locator_type_idsArr); $i++) {
								$tid = $locator_type_idsArr[$i];
								$db->setQuery("Select * from #__osrs_types where id = '$tid'");
								$ptype = $db->loadObject();
								$type_name = OSPHelper::getLanguageFieldValue($ptype, 'type_name');
								if ($locator_type > 0) {
									if ($tid == $locator_type) {
										$active = "class='active'";
									} else {
										$active = "";
									}
								} else {
									if ($i == 0) {
										$active = "class='active'";
										$locator_type = $locator_type_idsArr[0];
									} else {
										$active = "";
									}
								}
								?>
								<li <?php echo $active;?> ><a
										href="<?php echo Route::_('index.php?option=com_osproperty&view=lsearch&locator_type=' . $tid . '&Itemid=' . $jinput->getInt('Itemid', 0))?>"><?php echo $type_name;?></a>
								</li>
							<?php
							}
							?>
						</ul>
					</div>
					<div class="tab-content <?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
						<div class="tab-pane active" id="<?php echo strtolower(str_replace(" ", "_", $type_name))?>">
							<?php
							HelperOspropertyCommon::generateLocatorForm($lists, $locator_type);
							?>
						</div>
					</div>
				</div>
			<?php
			}
		}
        ?>
        <?php
        OSPHelper::showPriceTypesConfig();
        ?>
        <script type="text/javascript">
        //filter form with property type and price
        jQuery("#property_type").change(function() {
            updateLocatorPrice(jQuery("#property_type").val(),"<?php echo Uri::root(); ?>");
        });
        function updateLocatorPrice(type_id,live_site){
            xmlHttp=GetXmlHttpObject();
            url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=ajax_updatePrice&type_id=" + type_id + "&option_id=<?php echo $lists['price_value'];?>&min_price=<?php echo $lists['min_price'];?>&max_price=<?php echo $lists['max_price'];?>&module_id=adv";
            xmlHttp.onreadystatechange = ajax_updateLocatorSearch;
            xmlHttp.open("GET",url,true)
            xmlHttp.send(null)
        }

        function ajax_updateLocatorSearch(){
            if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
                var mod_osservice_price = document.getElementById("locator_price");
                if(mod_osservice_price != null) {
                    mod_osservice_price.innerHTML = xmlHttp.responseText;
                    var ptype = jQuery("#property_type").val();
                    jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
                    jQuery(function () {
                        var min_value = jQuery("#min" + ptype).val();
                        min_value = parseFloat(min_value);
                        var step_value = jQuery("#step" + ptype).val();
                        step_value = parseFloat(step_value);
                        var max_value = jQuery("#max" + ptype).val();
                        max_value = parseFloat(max_value);
                        jQuery("#advsliderange").slider({
                            range: true,
                            min: min_value,
                            step: step_value,
                            max: max_value,
                            values: [min_value, max_value],
                            slide: function (event, ui) {
                                var price_from = ui.values[0];
                                var price_to = ui.values[1];
                                jQuery("#advprice_from_input1").val(price_from);
                                jQuery("#advprice_to_input1").val(price_to);

                                price_from = price_from.formatMoney(0, ',', '.');
                                price_to = price_to.formatMoney(0, ',', '.');

                                jQuery("#advprice_from_input").text(price_from);
                                jQuery("#advprice_to_input").text(price_to);
                            }
                        });
                    });
                    Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
                        var n = this,
                            decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
                            decSeparator = decSeparator == undefined ? "." : decSeparator,
                            thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
                            sign = n < 0 ? "-" : "",
                            i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
                            j = (j = i.length) > 3 ? j % 3 : 0;
                        return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
                    };
                }
            }
        }

        </script>
        <div class="clearfix"></div>
        <div class="result_search" >
            <?php //if (count($rows)){
            ?>
            <?php if (($lists['location'] == "") || (count($rows) == 0) || ($style != "style1")) {
                $class = $bootstrapHelper->getClassMapping('span12');
            } else {
                $class = $bootstrapHelper->getClassMapping('span7');
            }
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $class ?>" id="mapDiv">
                    <?php
                    $zoomlevel = 7;
                    if (isset($configClass['goole_map_resolution']) && $configClass['goole_map_resolution'] != '') {
                        $zoomlevel = $configClass['goole_map_resolution'];
                    }
                    $lladd = $rows[0]->lat_add . "," . $rows[0]->long_add;
                    ?>
                    <?php
                    $geocode = array();
                    for ($i = 0; $i < count($rows); $i++) 
					{
						$tmp				= new stdClass();	
                        $row				= $rows[$i];
                        $tmp->id	= $row->id;
                        if (($row->lat_add == "") or ($row->long_add == "")) 
						{
                            //find the address
                            $return = HelperOspropertyGoogleMap::findAddress($option, $row, '', 0);
                            $lat = $return[0];
                            $long = $return[1];
                            $db->setQuery("UPDATE #__osrs_properties SET lat_add = '$lat',long_add='$long' WHERE id = '$row->id'");
                            $db->execute();
                            $row->lat_add = $lat;
                            $row->long_add = $long;
                        }
                        $tmp->show_address = $row->show_address;
                        $tmp->lat	= $row->lat_add;
                        $tmp->long	= $row->long_add;
                        $lladd = "$row->lat_add,$row->long_add";
                        $popup = "<div class='width100pc'><div class='floatleft marginright10'>";

                        // image
                        $db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
                        $photo = $db->loadObjectList();
                        if (count($photo) > 0) {
                            $photo = $photo[0];
                            $popup .= "<img src='" . Uri::root() . "images/osproperty/properties/" . $row->id . "/thumb/" . $photo->image . "' class='".$bootstrapHelper->getClassMapping('img-polaroid')." width60' />";
                        } else {
                            $popup .= "<img src='" . Uri::root() . "media/com_osproperty/assets/images/nopropertyphoto.png'  class='".$bootstrapHelper->getClassMapping('img-polaroid')." width60' />";
                        }
                        $popup .= "</div>";

                        $popup .= "<strong>" . $row->pro_name;
                        if (($row->ref != "") and ($configClass['show_ref'] == 1)) {
                            $popup .= " (" . $row->ref . ")";
                        }
                        $popup .= "</strong>";
                        if ($row->show_address == 1) 
						{
                            $popup .= "<BR />";
                            $popup .= OSPHelper::generateAddress($row);
                        }
                        $popup .= "</div>";
                        $tmp->content	= $popup;
						$tmp->title		= $row->pro_name;
                        $geocode[$i]	= $tmp;
                    }
                    //adjust the same coordinates
					$duplicate = OSPHelper::findGoogleDuplication($rows);
                    ?>
						<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js" defer='defer'></script>
						<?php
						HelperOspropertyGoogleMap::loadGoogleScript('libraries=places');
						?>
						<script type='text/javascript' defer='defer' src='<?php echo Uri::root()?>media/com_osproperty/assets/js/markerclusterer.js'></script>
						<script type='text/javascript' defer='defer' src='<?php echo Uri::root()?>media/com_osproperty/assets/js/markerwithlabel_packed.js'></script>
						
						<link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/css/jquery-ui.css" type="text/css"/>
						<script type="text/javascript">
							jQuery.noConflict();
							(function ($) {
								jQuery(document).ready(function () {
									var markers = [];
									var markerIndex = 0;
									var markerArray = [];
									var infowindow;
									var cityCircle;
									var gmarkers = [];
									var min = .999999;
									var max = 1.000001;
									var myHome = new google.maps.LatLng(<?php echo $search_lat;?>, <?php echo $search_long?>);
									
									<?php
									for($i=0;$i<count($duplicate);$i++){
										  $item = $duplicate[$i];
										  $key  = OSPHelper::find_key($item->id,$rows);
										  if(($rows[$key]->show_address == 1) and ($rows[$key]->lat_add != "") and ($rows[$key]->long_add != "")){
											  ?>
												 var propertyListing<?php echo $rows[$key]->id?> = new google.maps.LatLng(<?php echo $rows[$key]->lat_add; ?>, <?php echo $rows[$key]->long_add; ?>);
											  <?php
										  }
									  }
									?>
									var styles = [
									{
									  stylers: [
										{ hue: "#B1BDD6" },
										{ saturation: -20 }
									  ]
									},
									{
										"featureType": "landscape.natural",
										"elementType": "geometry",
										"stylers": [
											{
												"color": "#d0e3b4"
											}
										]
									},
									{
										"featureType": "poi.park",
										"elementType": "geometry",
										"stylers": [
											{
												"color": "#bde6ab"
											}
										]
									},
									{
										"featureType": "road.highway",
										"elementType": "geometry.fill",
										"stylers": [
											{
												"color": "#ffe15f"
											}
										]
									},
									{
										"featureType": "road.highway",
										"elementType": "geometry.stroke",
										"stylers": [
											{
												"color": "#efd151"
											}
										]
									},
									{
										"featureType": "road.arterial",
										"elementType": "geometry.fill",
										"stylers": [
											{
												"color": "#ffffff"
											}
										]
									},
									{
										"featureType": "road.local",
										"elementType": "geometry.fill",
										"stylers": [
											{
												"color": "black"
											}
										]
									},
									{
										"featureType": "transit.station.airport",
										"elementType": "geometry.fill",
										"stylers": [
											{
												"color": "#cfb2db"
											}
										]
									},
									{
										"featureType": "water",
										"elementType": "geometry",
										"stylers": [
											{
												"color": "#B1BDD6"
											}
										]
									},
									{
									  featureType: "road",
									  elementType: "geometry",
									  stylers: [
										{ lightness: 100 },
										{ visibility: "simplified" }
									  ]
									},{
									  featureType: "road",
									  elementType: "labels",
									  stylers: [
										{ visibility: "off" }
									  ]
									}
									];

									// Create a new StyledMapType object, passing it the array of styles,
									// as well as the name to be displayed on the map type control.
									var styledMap = new google.maps.StyledMapType(styles,{name: "Styled Map"});

									var mapOptions = {
										zoom: 13,
										streetViewControl: true,
										mapTypeControl: true,
										panControl: true,
										center: myHome,
										icon: "<?php echo Uri::root().'media/com_osproperty/assets/images/2-default.png'?>",
										mapTypeControl:false,
										mapTypeControlOptions: {
										  mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
										},
										zoomControl: true,
										zoomControlOptions: {
											position: google.maps.ControlPosition.LEFT_BOTTOM
										},
										scaleControl: true,
										streetViewControl: true,
										streetViewControlOptions: {
											position: google.maps.ControlPosition.BOTTOM_LEFT
										}

									};
									var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
									map.mapTypes.set('map_style', styledMap);
									map.setMapTypeId('map_style');

									<?php
									if($lists['search_my_location'] == 1){	
									?>
									
									var mylocation = new google.maps.Marker({
										position: myHome,
										animation: google.maps.Animation.DROP,
										map: map,
										icon: "<?php echo Uri::root().'media/com_osproperty/assets/images/userpin.png'?>"
									});
									var myCircle = new google.maps.Circle({
										strokeWeight: 0, 
										fillColor: "#008595", 
										fillOpacity: 0.25,  
										radius: 50, 
										map: map,
										center: myHome,
										radius: jQuery('#radius_search').val()*1000
									});
									<?php } ?>


									var infoWindow = new google.maps.InfoWindow();
									var markerBounds = new google.maps.LatLngBounds();
									var tempBound = new google.maps.LatLngBounds();
									jQuery('#togglebtn').click(function () {
										if (jQuery("#mapDiv").hasClass("<?php echo $bootstrapHelper->getClassMapping('span7'); ?>")) {
											jQuery("#mapDiv").removeClass("<?php echo $bootstrapHelper->getClassMapping('span7'); ?>");
											jQuery("#mapDiv").addClass("<?php echo $bootstrapHelper->getClassMapping('span12'); ?>");
											jQuery("#listPropertiesDiv").hide();
											google.maps.event.trigger(map, 'resize');
											map.fitBounds(markerBounds);
											<?php
											if($lists['search_my_location'] == 1){	
											?>
												markerBounds.extend(myHome);
												map.fitBounds(markerBounds);
												//map.setCenter(myHome); 
											<?php } ?>
											jQuery('#togglebtn').empty().append('<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/exit_full_screen.png" class="width25"  alt="<?php Text::_("OS_EXIT_FULL_SCREEN"); ?>"/>');
										} else {
											jQuery("#mapDiv").removeClass("<?php echo $bootstrapHelper->getClassMapping('span12'); ?>");
											jQuery("#mapDiv").addClass("<?php echo $bootstrapHelper->getClassMapping('span7'); ?>");
											jQuery("#listPropertiesDiv").show();
											google.maps.event.trigger(map, 'resize');
											map.fitBounds(markerBounds);
											<?php
											if($lists['search_my_location'] == 1){	
											?>
												markerBounds.extend(myHome);
												map.fitBounds(markerBounds);
											<?php } ?>
											jQuery('#togglebtn').empty().append('<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/full_screen.png" class="width25"  alt="<?php Text::_("OS_FULL_SCREEN"); ?>"/>');
										}
										return false;
									});

									function makeMarker(options) {
										if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
											var pushPin = new google.maps.Marker({map: map});
										}else{
											var pushPin = new MarkerWithLabel({map: map});
										}
										pushPin.setOptions(options);

										google.maps.event.addListener(pushPin, 'click', function () {
											infoWindow.setOptions(options);
											infoWindow.open(map, pushPin);
											map.panTo(pushPin.getPosition());
											map.setZoom(20);
										});
										google.maps.event.addListener(pushPin, 'mouseover', function () {
											pushPin.setAnimation(google.maps.Animation.BOUNCE);
										});
										google.maps.event.addListener(pushPin, 'mouseout', function () {
											pushPin.setAnimation(null);
										});
										markerArray.push(pushPin);
										return pushPin;
									}

									google.maps.event.addListener(map, 'click', function () {
										infoWindow.close();
									});

									<?php
										$showfit = 0;
										for($i=0;$i<count($duplicate);$i++){
		
											$item = $duplicate[$i];
											$key  = OSPHelper::find_key($item->id,$rows);
											if(count((array)$item->value) == 0){ //having no duplication
												$row		= $rows[$key];
												$row->mapid = $i;
												$needs		= array();
												$needs[]	= "property_details";
												$needs[]	= $row->id;
												$itemid		= OSPRoute::getItemid($needs);
												$title		= "";
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
												 var contentString<?php echo $row->id?> = '<div class="<?php echo $bootstrapHelper->getClassMapping("row-fluid"); ?>">'+
															'<div class="<?php echo $bootstrapHelper->getClassMapping("span4"); ?>">'+
															'<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>"><img class="<?php echo $bootstrapHelper->getClassMapping("span12"); ?> thumbnail" src="<?php echo $row->photo?>" /></a>'+
															'</div><div class="<?php echo $bootstrapHelper->getClassMapping("span8"); ?> ezitem-smallleftpad">'+
															'<div class="<?php echo $bootstrapHelper->getClassMapping("row-fluid"); ?>"><div class="<?php echo $bootstrapHelper->getClassMapping("span12"); ?> ospitem-maptitle title-blue"><?php echo $title;?></div></div>';
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
													//if(($row->show_address == 1) and ($row->lat_add != "") and ($row->long_add != "")){
													$show_map = 1;
													$db->setQuery("Select type_icon from #__osrs_types where id = '$row->pro_type'");
													$type_icon = $db->loadResult();
													if($type_icon == ""){
														$type_icon = "1.png";
													}

													if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
													{
														$pricevalue = OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
													}
													elseif($row->price_call == 1)
													{
														$pricevalue = Text::_('OS_CALL_FOR_PRICE');
                                                    }
													elseif ($row->price > 0) 
													{
														$pricevalue = OSPHelper::generatePrice($row->curr, $row->price);
                                                    }
													?>
													makeMarker({
														position: propertyListing<?php echo $row->id?>,
														labelContent: "<?php echo $pricevalue; ?>",
														labelAnchor: new google.maps.Point(40, 0),
														labelClass: "maplabels-a", // the CSS class for the label
														labelInBackground: false,
														title: "<?php echo $title;?>",
														content: contentString<?php echo $row->id?>,
														animation: google.maps.Animation.DROP,
														icon:new google.maps.MarkerImage('<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>')
													});
													<?php
												  // }
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
												$desc = '<div class="'.$bootstrapHelper->getClassMapping("row-fluid").'"><div class="'. $bootstrapHelper->getClassMapping("span4").'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping("span12").' thumbnail" src="'.$row->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping("span8").' ezitem-smallleftpad"><div class="'. $bootstrapHelper->getClassMapping("row-fluid").'"><div class="'.$bootstrapHelper->getClassMapping("span12").' ospitem-maptitle title-blue">'.$title.'</div></div>';
												if(count($addInfo) > 0){
													$desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
												}
												$desc .= htmlspecialchars(str_replace("'","\"",str_replace("\r","",str_replace("\n","",$row->pro_small_desc)))).'<a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid).'">'.Text::_('OS_DETAILS').'</a></p></div></div>';
												$descArr[] = $desc;
													$l = 1;
													foreach($item->value as $value){
														$key  = OSPHelper::find_key($value,$rows);
														$dupItem = $rows[$key];
														$dupItem->mapid = $i;
														$dupItem->subid = $l;
														$l++;
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
														$desc = '<div class="'.$bootstrapHelper->getClassMapping("row-fluid").'"><div class="'.$bootstrapHelper->getClassMapping("span4").'"><a href="'. Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'"><img class="'.$bootstrapHelper->getClassMapping("span12").' thumbnail" src="'.$dupItem->photo.'" /></a></div><div class="'.$bootstrapHelper->getClassMapping("span8").' ezitem-smallleftpad"><div class="'.$bootstrapHelper->getClassMapping("row-fluid").'"><div class="'.$bootstrapHelper->getClassMapping("span12").' ospitem-maptitle title-blue">'.$title.'</div></div>';
														if(count($addInfo) > 0){
															$desc .= '<div class="ospitem-iconbkgr"><span class="ezitem-leftpad">'.implode(" | ",$addInfo).'</span></div>';
														}
														$desc .= htmlspecialchars(str_replace("'","\"",str_replace("\r","",str_replace("\n","",$dupItem->pro_small_desc)))).'<a href="'.Route::_("index.php?option=com_osproperty&task=property_details&id=".$dupItem->id."&Itemid=".$itemid).'">'.Text::_('OS_DETAILS').'</a></p></div></div>';
														$descArr[] = $desc;
													}

													$desc = implode('<div class="clearfix" class="width25" style="border-top:1px dotted #efefef;"></div>',$descArr);
													?>
													
													var contentString<?php echo $row->id?> = '<?php echo $desc;?>';
													<?php
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
														animation: google.maps.Animation.DROP,
														icon:new google.maps.MarkerImage('<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $type_icon;?>')
													});

													<?php
											  }
										?>
										
										jQuery("#item<?php echo $i?>").click(function () {
											google.maps.event.trigger(markerArray[<?php echo $i?>], 'click');
										})
										jQuery("#divitem<?php echo $i?>").mouseover(function () {
											google.maps.event.trigger(markerArray[<?php echo $i?>], 'mouseover');
										})
										jQuery("#divitem<?php echo $i?>").mouseout(function () {
											google.maps.event.trigger(markerArray[<?php echo $i?>], 'mouseout');
										})
										jQuery("#pitem<?php echo $i?>").click(function () {
											google.maps.event.trigger(markerArray[<?php echo $i?>], 'click');
										})
										<?php
										if($l > 0){
											$l--;
											for($l1 = 1;$l1 <= $l;$l1++){
												?>
												jQuery("#item<?php echo $i?>_<?php echo $l1?>").click(function () {
													google.maps.event.trigger(markerArray[<?php echo $i?>], 'click');
												})
												jQuery("#pitem<?php echo $i?>_<?php echo $l1?>").click(function () {
													google.maps.event.trigger(markerArray[<?php echo $i?>], 'click');
												})
												<?php
											}
										}
										?>
										gmarkers.push(markerArray[<?php echo $i?>]);
										markerBounds.extend(propertyListing<?php echo $row->id?>);

										<?php
								}

							$showfit = 1;
							if($showfit == 1){
							?>
								map.fitBounds(markerBounds);
								<?php
								if($lists['search_my_location'] == 1){	
								?>
									markerBounds.extend(myHome);
									map.fitBounds(markerBounds);
								<?php } 
								elseif(count($rows) == 0){
								?>
									markerBounds.extend(myHome);
									map.fitBounds(markerBounds);
								<?php
								}
								?>
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
										textSize: 20
									}
								];
								var mcOptions = {gridSize: 50, maxZoom: 15, styles: clusterStyles};
								var markerCluster = new MarkerClusterer(map, gmarkers, mcOptions);


								var geocoder = new google.maps.Geocoder();
								jQuery(function () {
									jQuery("#location").autocomplete({
										source: function (request, response) {
											if (geocoder == null) {
												geocoder = new google.maps.Geocoder();
											}
											<?php
											if($country_code != ""){
											?>
											geocoder.geocode({'address': request.term,'componentRestrictions':{'country':'<?php echo $country_code;?>'}}, function (results, status) {
											<?php }else{ ?>
											geocoder.geocode({'address': request.term}, function (results, status) {
											<?php } ?>
												if (status == google.maps.GeocoderStatus.OK) {

													var searchLoc = results[0].geometry.location;
													var lat = results[0].geometry.location.lat();
													var lng = results[0].geometry.location.lng();
													var latlng = new google.maps.LatLng(lat, lng);
													var bounds = results[0].geometry.bounds;

													var marker = new google.maps.Marker({
														draggable: false,
														raiseOnDrag: false,
														position: latlng,
														map: map,
														icon: "<?php echo Uri::root().'media/com_osproperty/assets/images/2-default.png'?>"
													});

													var circle = new google.maps.Circle({
														map: map,
														radius: 1609.344 * jQuery('#radius_search').val(), // 1 mile
														strokeColor: '#FFFFFF',
														fillColor: '#FFFFFF',
														fillOpacity: 0,
														strokeWeight: 1,
														editable: false
													});
													circle.bindTo('center', marker, 'position');

													geocoder.geocode({'latLng': latlng}, function (results1, status1) {
														if (status1 == google.maps.GeocoderStatus.OK) {
															if (results1[1]) {
																response($.map(results1, function (loc) {
																	return {
																		label: loc.formatted_address,
																		value: loc.formatted_address,
																		bounds: loc.geometry.bounds
																	}
																}));
															}
														}
													});
												}
											});
										},
										select: function (event, ui) {
											var pos = ui.item.position;
											var lct = ui.item.locType;
											var bounds = ui.item.bounds;
											if (bounds) {
												jQuery('#location').change(function () {
													map.fitBounds(bounds);
													<?php
													if($lists['search_my_location'] == 1){	
													?>
														markerBounds.extend(myHome);
														map.fitBounds(markerBounds);
													<?php } ?>
												});
											}
										}
									});
									function openMarker(i) {
										google.maps.event.trigger(markerArray[i], 'click');
									};
									function makerOver(i) {
										google.maps.event.trigger(markerArray[i], 'mouseover');
									}

									function makerOut(i) {
										google.maps.event.trigger(markerArray[i], 'mouseout');
									}
								});
								});
							})(jQuery);

							function showOption() {
								var more_option_link = document.getElementById('more_option_link');
								var more_option_div = document.getElementById('more_option_div');
								if (more_option_div.style.display == "none") {
									more_option_link.innerHTML = "<?php echo Text::_('OS_LESS_OPTION');?>";
									more_option_div.style.display = "block";
								} else {
									more_option_link.innerHTML = "<?php echo Text::_('OS_MORE_OPTION');?>";
									more_option_div.style.display = "none";
								}
							}

							function updateOrderBy(value) {
								var orderby = document.getElementById('orderby');
								orderby.value = value;
								document.getElementById('profileForm').submit();
							}
							function updateSortBy(value) {
								var orderby = document.getElementById('sortby');
								orderby.value = value;
								document.getElementById('profileForm').submit();
							}
						</script>
						<?php
						if ((count($rows) > 0) && ($style == "style1")){
							?>
							<div id="toggle" class="gmapcontroller" >
								<span id="togglebtn" class="gmapcontroller_fullscreen">
									<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/full_screen.png" style="width:25px !important;" alt="<?php Text::_('OS_FULL_SCREEN'); ?>"/>
								</span>
							</div>
						<?php 
						} 
						if (($lists['location'] != "") and (count($rows) == 0)){
							?>
							<div id="gmap-noresult">
								<?php echo Text::_('OS_WE_DIDNOT_FIND_ANY_RESULTS'); ?>
							</div>
						<?php
						}
						
						if ($lists['location'] == ""){
							?>
							<div id="gmap-noresult">
								<?php echo Text::_('OS_PLEASE_ENTER_LOCATION'); ?>
							</div>
						<?php
						}
						?>
							<!-- Notice -->
							<?php 
							if((count($rows) > 0) and ((int)$configClass['max_locator_results'] > 0)){
							?>
							<div id="locator_map_notice" class="locator_map_notice hidden-phone" >
								<strong><?php echo sprintf(Text::_('OS_ONLY_SHOW_PROPERTIES'),$configClass['max_locator_results']); ?></strong> <?php echo Text::_('OS_USE_FILTER_TO_NARROW_YOUR_SEARCH');?>
							</div>
							<?php } ?>
							<!-- End Notice -->
							<?php
							if($style == "style2"){
							?>
								<div id="locator_showbar" data-original-title="Show the sidebar" style="top: 40px;"></div>
								<div id="locator_sidebar" class="margintop30">
									<div id="locator_sidebar1" style="background-color: transparent; background-image: url('<?php echo Uri::root()?>media/com_osproperty/assets/images/sidebg1.png');">
										<div id="locator_hidebar" data-original-title="Hide the sidebar"></div>
										<?php
										HelperOspropertyCommon::generateLocatorFormVertical($lists,$type_id);
										?>
									</div>
								</div>
								<script type="text/javascript">
									jQuery('#locator_hidebar, #locator_showbar').click(function(){jQuery('#locator_sidebar').toggle('slow');jQuery('#locator_showbar').toggle('slow')});
								</script>
							<?php } ?>
							<div id="map_canvas" class="padding5 map3x"></div>
                        </div>
						
                        <?php if($lists['location'] != "" && count($rows) > 0 && $style == "style1")
						{
				        ?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('span5'); ?> <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" id="listPropertiesDiv">
					        <?php
					        if(count($rows) > 0){
                            ?>
                            <div class="property_listing_left" style="height: 600px;">
                                <div class="clearfix"></div>
                                <div class="header_property_listing"><?php echo Text::_('OS_PROPERTIES_LIST')?>
                                    (<?php echo count($rows);?>)
                                </div>
                                <div class="clearfix"></div>
                                    <div id="listview">
									<?php
									for ($i=0; $i<count($rows);$i++)
										{
                                        $row = $rows[$i];
										if($row->subid != "")
										{
											$subid = "_".$row->subid;
										}
										else
										{
											$subid = "";
										}
                                        $link   = Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$row->itemid);
                                        ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> locatormap_icon" >
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> conten_e_property" id="divitem<?php echo $i?>">
                                                <div class="locator_image_property">
                                                    <?php
                                                    $db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
                                                    $photo = $db->loadObjectList();
                                                    if (count($photo) > 0) 
													{
                                                        $photo = $photo[0];
                                                        OSPHelper::showPropertyPhoto($photo->image, 'thumb', $row->id, 'width:120px;', $bootstrapHelper->getClassMapping('img-polaroid'), '');
                                                    } else {
                                                        ?>
                                                        <img
                                                            src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png"
                                                            width="90"/>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <strong>
                                                    <a href="javascript:void(0);" onClick="javascript:focusonMap();"
                                                       class="locator_title_link" id="item<?php echo $row->mapid.$subid;?>" >
                                                        <?php
                                                        echo $row->pro_name;
                                                        if (($row->ref != "") and ($configClass['show_ref'] == 1)) {
                                                            echo " ($row->ref)";
                                                        }
                                                        ?>
                                                    </a>
                                                    <?php
                                                    if ($configClass['locator_show_type'] == 1) 
													{
                                                        ?>
                                                        &nbsp;|&nbsp;
                                                        <strong><?php echo OSPHelper::getLanguageFieldValue($row, 'type_name');?></strong>
                                                    <?php
                                                    }
													if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
													{
														echo "&nbsp;|&nbsp;<span class='fontbold colorred'>" . OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text')) ."</span>";
														//$pricevalue = $row->price_text;
													}
													elseif($row->price_call == 1)
													{
                                                        echo "&nbsp;|&nbsp;<span class='fontbold colorred'>" . Text::_('OS_CALL_FOR_PRICE') ."</span>";
														//$pricevalue = Text::_('OS_CALL_FOR_PRICE');
                                                    }
													elseif ($row->price > 0) 
													{
                                                        echo "&nbsp;|&nbsp;<span class='fontbold colorred'>" . OSPHelper::generatePrice($row->curr, $row->price) . "</span>";
                                                        if($row->rent_time != ""){
                                                            echo " /".Text::_($row->rent_time);
                                                        }
														//$pricevalue = OSPHelper::generatePrice($row->curr, $row->price);
                                                    }
                                                    $temp_path_img = Uri::root(true) . "/components/com_osproperty/images/assets";
                                                    $user = Factory::getUser();
                                                    ?>
                                                    &nbsp;&nbsp;
                                                    <?php
                                                    if($configClass['show_compare_task'] == 1) 
													{
                                                        ?>
                                                        <span id="compare<?php echo $row->id; ?>">
                                                            <?php
                                                            if (!OSPHelper::isInCompareList($row->id)) {
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
                                                                $msg = str_replace("'","\'",$msg);
                                                                ?>
                                                                <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)">
                                                                    <img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST') ?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST') ?>" src="<?php echo Uri::root(true) ?>/media/com_osproperty/assets/images/compare24_gray.png" border="0" width="16"/></a>
                                                                    </span>
                                                                <?php
                                                            }else{
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                                $msg = str_replace("'","\'",$msg);
                                                                ?>
                                                                <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)">
                                                                    <img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST') ?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST') ?>" src="<?php echo Uri::root(true) ?>/media/com_osproperty/assets/images/compare24.png" border="0" width="16"/></a>
                                                                    </span>
                                                                <?php
                                                            }
                                                            ?>
                                                        </span>
                                                        <?php
                                                    }
                                                    if (intval($user->id) > 0) 
													{
                                                        if ($configClass['property_save_to_favories'] == 1) 
														{
                                                            $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
                                                            $count = $db->loadResult();
                                                            if ($count == 0) {
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                                $msg = str_replace("'","\'",$msg);
                                                                ?>
                                                                <span id="fav<?php echo $row->id; ?>">
                                                                    <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)">
                                                                        <img class="width16" title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root(true);?>/media/com_osproperty/assets/images/save24_gray.png" border="0"/>
                                                                    </a>
                                                                </span>
                                                            <?php
                                                            } 
															else 
															{
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
                                                                $msg = str_replace("'","\'",$msg);
                                                                ?>
                                                                <span id="fav<?php echo $row->id; ?>">
                                                                    <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)">
                                                                        <img class="width16" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root(true);?>/media/com_osproperty/assets/images/save24.png" border="0"/>
                                                                    </a>
                                                                </span>
                                                            <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <span id="fav<?php echo $row->id; ?>">
                                                        <a href="<?php echo $link;?>" title="<?php echo Text::_('OS_VIEW_LISTING_DETAILS')?>">
                                                            <img title="<?php echo Text::_('OS_VIEW_LISTING_DETAILS')?>" alt="<?php echo Text::_('OS_VIEW_LISTING_DETAILS')?>" src="<?php echo Uri::root(true);?>/media/com_osproperty/assets/images/details.png" border="0"/>
                                                        </a>
                                                    </span>
                                                    </strong>
                                                    <BR />
                                                    <?php
                                                    if($configClass['locator_show_address'] == 1){
                                                        if($row->show_address == 1){
                                                        ?>
                                                        <span class="small_text">
                                                        <?php echo OSPHelper::generateAddress($row);?>
                                                        </span>
                                                        <BR />
                                                        <?php
                                                        }
                                                    }
                                                    if($configClass['locator_show_category'] == 1){
                                                    ?>
                                                        <?php echo Text::_('OS_CATEGORY')?>: <strong><?php echo $row->category_name;?></strong>
                                                    <BR />
                                                    <?php
                                                    }
                                                    ?>
                                                    <span class="small_text">
                                                    <?php
                                                    if($configClass['use_rooms'] == 1){
                                                    ?>
                                                    <?php echo $row->rooms;?> <?php echo Text::_('OS_ROOMS')?>
                                                    &nbsp;|&nbsp;
                                                    <?php
                                                    }
                                                    if($configClass['use_bedrooms'] == 1){
                                                    ?>
                                                    <?php echo $row->bed_room;?> <?php echo Text::_('OS_BEDROOMS')?>
                                                    &nbsp;|&nbsp;
                                                    <?php
                                                    }
                                                    if($configClass['use_bathrooms'] == 1){
                                                    ?>
                                                    <?php echo OSPHelper::showBath($row->bath_room);?> <?php echo Text::_('OS_BATHROOMS')?>
                                                    <?php
                                                    }
                                                    if($configClass['use_squarefeet'] == 1){
                                                    ?>
                                                    &nbsp;|&nbsp;
                                                    <?php echo $row->square_feet;?> <?php echo OSPHelper::showSquareSymbol();?>
                                                    <?php
                                                    }
                                                    ?>
                                                    </span>
                                                    <?php
                                                    if(($configClass['use_rooms'] == 1) or ($configClass['use_bedrooms'] == 1) or ($configClass['use_bathrooms'] == 1)){
                                                    ?>
                                                    <BR />
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        <?php
                                        }
                                        ?>
                                        </div>
                                    </div>
                                    <?php
                                }
								elseif($lists['location'] != "")
								{

                                }
                            ?>
                            </div>
						<?php } ?>
					</div>	
				</div>
			</div>
			<script type="text/javascript">
            function focusMarker(i)
            {
                var obj = eval("marker"+i);
                var html=arrBuble[i];
                obj.openInfoWindowHtml(html,{maxWidth:500});
            }
			</script>
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" value="locator_search" />
			<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
			<input type="hidden" name="locator_search" value="1" />
			<input type="hidden" name="locator_type" id="locator_type" value="<?php echo $locator_type?>" />
			<input type="hidden" name="doSearch" id="doSearch" value="1" />
            <input type="hidden" name="process_element" id="process_element" value="" />
            <input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root();?>" />
			<input type="hidden" name="my_lat" id="my_lat" value="" />
			<input type="hidden" name="my_long" id="my_long" value="" />
			<input type="hidden" name="search_my_location" id="search_my_location" value="0" />
		</form>
		<script type="text/javascript">
			var live_site = '<?php echo Uri::root()?>';
			function change_country_company(country_id,state_id,city_id){
				var live_site = '<?php echo Uri::root()?>';
				loadLocationInfoStateCityLocator(country_id,state_id,city_id,'country','state_id',live_site);
			}
			function change_state(state_id,city_id){
				var live_site = '<?php echo Uri::root()?>';
				loadLocationInfoCity(state_id,city_id,'state_id',live_site);
			}
            jQuery( "#gridviewbtn" ).click(function() {
                jQuery("#listview").hide("fast");
                jQuery("#gridview").show("slow");
            });
            jQuery( "#listviewbtn" ).click(function() {
                jQuery("#listview").show("slow");
                jQuery("#gridview").hide("fast");
            });
			function updateMyLocation(){
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function (p) {
						document.getElementById('my_lat').value = p.coords.latitude;
						document.getElementById('my_long').value = p.coords.longitude;
						document.getElementById('search_my_location').value = '1';
						document.profileForm.submit();
					});
				}
			}

            jQuery( "#linkmoreoption" ).click(function() {
                if(jQuery('#locatormoredetails').is(':hidden')) {
                    jQuery('#linkmoreoption').text("<?php echo Text::_('OS_LESS_OPTION');?>");
					jQuery('#locatormoredetails').removeClass('nodisplay');
                    jQuery('#locatormoredetails').slideDown();
                }else{
                    jQuery('#linkmoreoption').text("<?php echo Text::_('OS_MORE_OPTION');?>");
					jQuery('#locatormoredetails').addClass('nodisplay');
                    jQuery('#locatormoredetails').slideUp();
                }
            });

			var width = jQuery("#gridview").width();
			if(width > 500){
				jQuery(".randompropertytype").show();
				jQuery(".randompropertyfeatured").show();
			}else{
				jQuery(".randompropertytype").hide();
				jQuery(".randompropertyfeatured").hide();
			}

			function focusonMap(){
				 jQuery('#map_canvas').goTo();
			}
		</script>
		<?php
	}
	
}

?>