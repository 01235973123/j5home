<?php

/*------------------------------------------------------------------------
# helper.php - mod_ospropertysearch
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
class modOspropertyGoogleMapHelper
{
	private $params;
	/**
	 * Enter description here...
	 *
	 */
	public static function loadLanguage(){
		Factory::getLanguage()->load('com_osproperty', JPATH_SITE, Factory::getLanguage()->getTag(), true);
	}
	
	public static function loadAllProperty($osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,$configClass){
		global $mainframe,$lang_suffix,$bootstrapHelper;
		$languages = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			//generate the suffix
			$lang_suffix = OSPHelper::getFieldSuffix();
		}

		$user = Factory::getUser();
		$db = Factory::getDbo();
		$query = "Select a.id,a.pro_name,a.pro_name$lang_suffix,a.address,a.state,a.city,a.country,a.show_address,a.price,a.price_text,a.price_call,a.postcode,a.region,a.lat_add,a.long_add,a.pro_type,c.name as agent_name,c.photo as agent_photo,c.email as agent_email,d.id as typeid,d.type_name$lang_suffix as type_name,d.type_icon,e.country_name from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." WHERE 1=1";
		$query .= ' and a.`access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		$query .= ' and a.`lat_add` <> "" and a.`long_add` <> "" ';
		if(count((array)$osp_countries) > 0  && $osp_countries[0] > 0)
		{
			$query .= " and a.country in (".implode(",",$osp_countries).")";
		}
		if($state_ids != ""){
			$query .= " and a.state in ($state_ids)";
		}
		if($city_ids != ""){
			$query .= " and a.city in ($city_ids)";
		}
		if(count((array)$osp_category) > 0 && $osp_category[0] > 0)
		{
			$query .= " and a.id in (Select pid from #__osrs_property_categories where category_id in (".implode(",",$osp_category)."))";
		}
		if((count((array)$osp_type) > 0) and ($osp_type[0] > 0)){
			$query .= " and a.pro_type in (".implode(",",$osp_type).")";
		}
		$query .= " and a.show_address = '1' and a.published = '1' and a.approved = '1'";
		if($maxitem > 0){
			$query .= " limit $maxitem";
		}	
	
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];

				$db->setQuery("Select category_id from #__osrs_property_categories where pid = '$row->id'");
				$row->category_id = $db->loadResult();

				$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
				$image = $db->loadResult();
				$address = OSPHelper::generateAddress($row);
				$address = str_replace("'","",$address);
				$row->address = $address;
				$needs = array();
				$needs[] = "property_details";
				$needs[] = $row->id;
				$itemid = OSPRoute::getItemid($needs);
				$link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);
				ob_start();
				?><div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>"><div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>"><?php if($image != ""){ if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$image)){ ?><a href="<?php echo $link?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><img src="<?php echo JURI::root()?>images/osproperty/properties/<?php echo $row->id?>/medium/<?php echo $image?>" class="span12 thumbnail" width="150" alt="<?php echo str_replace("'","",OSPHelper::getLanguageFieldValue($row,'pro_name'));?>"></a><?php } else { ?><a href="<?php echo $link?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><img src="<?php echo JURI::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" class="span12 thumbnail" alt="<?php echo str_replace("'","",OSPHelper::getLanguageFieldValue($row,'pro_name'));?>"></a><?php } }else{ ?><a href="<?php echo $link?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><img src="<?php echo JURI::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" class="span12 thumbnail" alt="<?php echo str_replace("'","",OSPHelper::getLanguageFieldValue($row,'pro_name'));?>"></a><?php } ?></div><div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?>"><span class="mod_ospropertymap_title"><a href="<?php echo $link?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><?php
				if(($row->ref != "") && ($configClass['show_ref'] == 1)){
					echo $row->ref.", ";
				} echo str_replace("'","",OSPHelper::getLanguageFieldValue($row,'pro_name')); ?></a></span><div class="clearfix"></div><span class="mod_ospropertymap_price"><?php if($row->price_text != ""){ echo Text::_($row->price_text); }else if($row->price_call == 0){ echo OSPHelper::generatePrice($row->curr,$row->price); }
				?></span>&nbsp;&nbsp;<span class="mod_ospropertymap_type"><?php echo $row->type_name?></span><div class="clearfix"></div><?php echo $row->address;?></div></div><?php
				$row->infowindow = ob_get_contents();
				ob_end_clean();
			}
		}
		return $rows;
	}

	static function createOpenStreetMap ($params, $width = null, $height = null, $zoom = null,$module_id,$configClass)
    {
        $width					= $params->get('width','100%');
        $height					= $params->get('height',300);
        $zoom					= $params->get('zoom',10);
        $map_type				= $params->get('map_type','google');
        $google_maptype			= $params->get('google_maptype','ROADMAP');
        $osp_category			= $params->get('osp_category');
        $osp_type				= $params->get('osp_type');
        $maxitem				= $params->get('maxitem',100);
        $bootstrap				= $params->get('bootstrap','bootstrap2');

        $osp_countries          = $params->get('osp_countries');
        $state_ids              = $params->get('state_ids');
        $city_ids               = $params->get('city_ids');
        $disable_mouse_wheel	= $params->get('disable_mouse_wheel',0);

        if($params->get('useCache') == 1){
            $data               = self::Cache( 'ospropertymap.json', $params->get('cacheTime'),'',$osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,$module_id,$configClass );
            $items              = json_decode($data);
        }else{
            $items              = self::loadAllProperty($osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,'',$configClass);
        }

        $latlng                 = $items[0]->lat_add . "," . $items[0]->long_add;
        $map                    = $map_type;
        $str                    = null;
        $result                 = "";
        $address                = null;
        $s                      = null;
        $cluster                = 1;
        $loc                    = null;
        $q                      = 0;
        $v                      = rand();
        $boundItems             = array();

        if (empty($width)) {
            $width              = self::formatField(Factory::getApplication()->input->getString('viewwidth', ($params->get('width', '100%'))));
        } else {
            $width              = self::formatField($width);
        }
        if (empty($height)) {
            $height             = self::formatField(Factory::getApplication()->input->getString('viewheight', ($params->get('height', 400))));
        } else {
            $height             = self::formatField($height);
        }

        $map                    = 0;
        //$items[0]->google_maptype = $google_maptype;
        $result.= "  <script type='text/javascript'> var icons_" . $v . "=[];";

        foreach ($items as $item)
        {
            $address = "";
            $s = $item->id . "_" . $v;
            $data = array();
            if($item->type_icon == "")
            {
                $item->type_icon = "1.png";
            }

            $address = $item->infowindow;
            $tempArr[] ="['" . preg_replace("/[\n\r\f]+/m","<br />",$address) . "', " . $item->lat_add . ", " . $item->long_add . ", 6]";
            if(file_exists(JPATH_ROOT.'/media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon))
            {
                $result.="icons_" . $v . "[" . $q . "] ='" . JURI::root().'media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon . "';";
            }
            else
            {
                $result.="icons_" . $v . "[" . $q . "] ='https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=" . ($q + 1) . "|FF776B|000000';";
            }
            $boundItems[] = "[".$item->lat_add.",".$item->long_add."]";
            $q++;
        }
        if($disable_mouse_wheel == 1)
        {
            $disableScript = "mymap". $s .".scrollWheelZoom.disable();";
        }
        else
        {
            $disableScript = "mymap". $s .".scrollWheelZoom.enable();";
        }
        if(count($boundItems))
        {
            $boundItems = implode(",",$boundItems);
        }
        $loc = implode(",",$tempArr);
        //$result.="</script>";

        $result.= "
            jQuery(document).ready(function(){
                var marker" . $s . ";
			    var map" . $s . ";
				var locations" . $s . " = [" . $loc . "];
				var panorama" . $s . ";
				var markerArray". $s ." = [];
				var mymap". $s ."       = L.map('map_canvas_". $s ."').setView([".$latlng."], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '',
                    maxZoom: 18,
                    id: 'mapbox.streets',
                    zoom: 5,
                }).addTo(mymap". $s .");
                var markers". $s ." = new L.MarkerClusterGroup();
                for (i = 0; i < locations" . $s . ".length; i++) 
                {
                    var propertyIcon = L.icon({iconUrl: icons_" . $v . "[i],
                        iconSize:     [33, 44] // size of the icon
                    });
                    var marker = L.marker([locations" . $s . "[i][1], locations" . $s . "[i][2]],{icon: propertyIcon});
                    marker.bindPopup(locations" . $s . "[i][0]);
                    markerArray". $s .".push(marker);
                    markers". $s .".addLayer(marker);
                }
                mymap". $s .".fitBounds([".$boundItems."]);
                mymap". $s .".addLayer(markers". $s .");
                ". $disableScript ."
            });
        </script>
        "; //end result
        $result.="<div class='map_canvas' id='map_canvas_" . $s . "'  style='width:" . $width . ";height:" . $height . ";float:left;position:relative;'></div>";
        return $result;
    }
	
	static function createMap($params, $width = null, $height = null, $zoom = null,$module_id,$configClass) {
        $doc = Factory::getDocument();
		
		if($configClass['goole_aip_key'] != ""){
			$key = "&key=".$configClass['goole_aip_key'];
		}else{
			$key = "";
		}

        $db						= Factory::getDbo();
        $width					= $params->get('width','100%');
		$height					= $params->get('height',300);
		$zoom					= $params->get('zoom',10);
		$map_type				= $params->get('map_type','google');
		$google_maptype			= $params->get('google_maptype','ROADMAP');
		$osp_category			= $params->get('osp_category');
		$osp_type				= $params->get('osp_type');
		$maxitem				= $params->get('maxitem',100);
		$bootstrap				= $params->get('bootstrap','bootstrap2');
		$disable_mouse_wheel	= $params->get('disable_mouse_wheel',0);
		$google_mapstyle		= $params->get('google_mapstyle',0);
		if($disable_mouse_wheel == 1){
			$disable_scroll = "scrollwheel: false";
		}else{
			$disable_scroll = "scrollwheel: true";
		}


		$osp_countries = $params->get('osp_countries');
		$state_ids = $params->get('state_ids');
		$city_ids = $params->get('city_ids');
        
		if($params->get('useCache') == 1)
		{
			$data  = self::Cache( 'ospropertymap.json', $params->get('cacheTime'),'',$osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,$module_id,$configClass );
			$items = json_decode($data);
		}
		else
		{
			$items = self::loadAllProperty($osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,'',$configClass);
		}

		if(count($items) > 0)
		{
        
			$latlng = $items[0]->lat_add . "," . $items[0]->long_add;
			$map = $map_type;
			$str = null;
			$result = "";
			$address = null;
			$s = null;
			$cluster = 1;
			$loc = null;
			$q = 0;
			$v = rand();
		   
			if (empty($width)) {
				$width = self::formatField(Factory::getApplication()->input->getString('viewwidth', ($params->get('width', '100%'))));
			} else {
				$width = self::formatField($width);
			}
			if (empty($height)) {
				$height = self::formatField(Factory::getApplication()->input->getString('viewheight', ($params->get('height', 400))));
			} else {
				$height = self::formatField($height);
			}
			
			$map = 0;
				
			//if (empty($items[0]->google_maptype)) {
				//$items[0]->google_maptype = $google_maptype;
			//}
			
			$result.= "  <script type='text/javascript'> var icons_" . $v . "=[];";
			$tempArr = array();
			foreach ($items as $item) {
				$address = "";
				$s = $item->id . "_" . $v;
				$data = array();
				if($item->type_icon == ""){
					$item->type_icon = "1.png";
				}
				if(file_exists(JPATH_ROOT.'/media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon)){
					$icons = JURI::root().'media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon;
				}else{
					$icons = "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=1|FF776B|000000";
				}
				$address = $item->infowindow;
				$tempArr[] ="['" . preg_replace("/[\n\r\f]+/m","<br />",$address) . "', " . $item->lat_add . ", " . $item->long_add . ", 6]";
				if(file_exists(JPATH_ROOT.'/media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon)){
					$result.="icons_" . $v . "[" . $q . "]='" . JURI::root().'media/com_osproperty/assets/images/googlemapicons/'.$item->type_icon . "';";
				} else {
					$result.="icons_" . $v . "[" . $q . "]='https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=" . ($q + 1) . "|FF776B|000000';";
				}
				$q++;
			}
			$loc = implode(",",$tempArr);
			$result.="</script>";
			?>
			<script type="text/javascript">
			var style0 = [];
			var style1 = [
				{
					"featureType": "water",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#e9e9e9"
						},
						{
							"lightness": 17
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#f5f5f5"
						},
						{
							"lightness": 20
						}
					]
				},
				{
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#ffffff"
						},
						{
							"lightness": 17
						}
					]
				},
				{
					"featureType": "road.highway",
					"elementType": "geometry.stroke",
					"stylers": [
						{
							"color": "#ffffff"
						},
						{
							"lightness": 29
						},
						{
							"weight": 0.2
						}
					]
				},
				{
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#ffffff"
						},
						{
							"lightness": 18
						}
					]
				},
				{
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#ffffff"
						},
						{
							"lightness": 16
						}
					]
				},
				{
					"featureType": "poi",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#f5f5f5"
						},
						{
							"lightness": 21
						}
					]
				},
				{
					"featureType": "poi.park",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#dedede"
						},
						{
							"lightness": 21
						}
					]
				},
				{
					"elementType": "labels.text.stroke",
					"stylers": [
						{
							"visibility": "on"
						},
						{
							"color": "#ffffff"
						},
						{
							"lightness": 16
						}
					]
				},
				{
					"elementType": "labels.text.fill",
					"stylers": [
						{
							"saturation": 36
						},
						{
							"color": "#333333"
						},
						{
							"lightness": 40
						}
					]
				},
				{
					"elementType": "labels.icon",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "transit",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#f2f2f2"
						},
						{
							"lightness": 19
						}
					]
				},
				{
					"featureType": "administrative",
					"elementType": "geometry.fill",
					"stylers": [
						{
							"color": "#fefefe"
						},
						{
							"lightness": 20
						}
					]
				},
				{
					"featureType": "administrative",
					"elementType": "geometry.stroke",
					"stylers": [
						{
							"color": "#fefefe"
						},
						{
							"lightness": 17
						},
						{
							"weight": 1.2
						}
					]
				}
			];

			var style2 = [
				{
					"featureType": "administrative",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "on"
						},
						{
							"lightness": 33
						}
					]
				},
				{
					"featureType": "landscape",
					"elementType": "all",
					"stylers": [
						{
							"color": "#f2e5d4"
						}
					]
				},
				{
					"featureType": "poi.park",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#c5dac6"
						}
					]
				},
				{
					"featureType": "poi.park",
					"elementType": "labels",
					"stylers": [
						{
							"visibility": "on"
						},
						{
							"lightness": 20
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "all",
					"stylers": [
						{
							"lightness": 20
						}
					]
				},
				{
					"featureType": "road.highway",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#c5c6c6"
						}
					]
				},
				{
					"featureType": "road.arterial",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#e4d7c6"
						}
					]
				},
				{
					"featureType": "road.local",
					"elementType": "geometry",
					"stylers": [
						{
							"color": "#fbfaf7"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "all",
					"stylers": [
						{
							"visibility": "on"
						},
						{
							"color": "#acbcc9"
						}
					]
				}
			];

			var style3 = [
				{
					"featureType": "landscape",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 65
						},
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "poi",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 51
						},
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "road.highway",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "road.arterial",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 30
						},
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "road.local",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"lightness": 40
						},
						{
							"visibility": "on"
						}
					]
				},
				{
					"featureType": "transit",
					"stylers": [
						{
							"saturation": -100
						},
						{
							"visibility": "simplified"
						}
					]
				},
				{
					"featureType": "administrative.province",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "labels",
					"stylers": [
						{
							"visibility": "on"
						},
						{
							"lightness": -25
						},
						{
							"saturation": -100
						}
					]
				},
				{
					"featureType": "water",
					"elementType": "geometry",
					"stylers": [
						{
							"hue": "#ffff00"
						},
						{
							"lightness": -25
						},
						{
							"saturation": -97
						}
					]
				}
			];

			var style4 = [
					{
						"featureType": "all",
						"elementType": "labels.text.fill",
						"stylers": [
							{
								"saturation": 36
							},
							{
								"color": "#000000"
							},
							{
								"lightness": 40
							}
						]
					},
					{
						"featureType": "all",
						"elementType": "labels.text.stroke",
						"stylers": [
							{
								"visibility": "on"
							},
							{
								"color": "#000000"
							},
							{
								"lightness": 16
							}
						]
					},
					{
						"featureType": "all",
						"elementType": "labels.icon",
						"stylers": [
							{
								"visibility": "off"
							}
						]
					},
					{
						"featureType": "administrative",
						"elementType": "geometry.fill",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 20
							}
						]
					},
					{
						"featureType": "administrative",
						"elementType": "geometry.stroke",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 17
							},
							{
								"weight": 1.2
							}
						]
					},
					{
						"featureType": "landscape",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 20
							}
						]
					},
					{
						"featureType": "poi",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 21
							}
						]
					},
					{
						"featureType": "road.highway",
						"elementType": "geometry.fill",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 17
							}
						]
					},
					{
						"featureType": "road.highway",
						"elementType": "geometry.stroke",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 29
							},
							{
								"weight": 0.2
							}
						]
					},
					{
						"featureType": "road.arterial",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 18
							}
						]
					},
					{
						"featureType": "road.local",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 16
							}
						]
					},
					{
						"featureType": "transit",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 19
							}
						]
					},
					{
						"featureType": "water",
						"elementType": "geometry",
						"stylers": [
							{
								"color": "#000000"
							},
							{
								"lightness": 17
							}
						]
					}
				];

			var style5 = [
				{
					"stylers": [
						{
							"hue": "#dd0d0d"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "labels",
					"stylers": [
						{
							"visibility": "off"
						}
					]
				},
				{
					"featureType": "road",
					"elementType": "geometry",
					"stylers": [
						{
							"lightness": 100
						},
						{
							"visibility": "simplified"
						}
					]
				}
			];
			</script>
			<?php
				$dist = 'unitSystem: google.maps.UnitSystem.METRIC';
				$script1 = "";
				$script2 = "";
				$script3 = "";
				if ($cluster == 1) {
					$script1 = "<script type='text/javascript' src='".Uri::root()."media/com_osproperty/assets/js/markerclusterer.js'></script>";
					$script2 = "gmarkers.push(marker);";
					$script3 = "
					clusterStyles = [
					  {
						  textColor: '#ffffff',
						  opt_textColor: '#ffffff',
						  url: '".Juri::root()."media/com_osproperty/assets/images/cloud.png',
						  height: 72,
						  width: 72,
						  textSize: 15,
					  }
					];

					var mcOptions = {gridSize: 50, maxZoom: 14, styles: clusterStyles};
					var markerCluster = new MarkerClusterer(map" . $s . ",gmarkers,mcOptions);";
				}
				$task = Factory::getApplication()->input->getString('task');
				if($task != "property_details"){
					$result.="<script src='https://maps.googleapis.com/maps/api/js?sensor=false&v=quarterly" . self::addLocalization() . $key ."'></script>";
				}
				$result.="<style type='text/css'>.map_canvas img, #map img
							{
								max-width: none !important;
							}
							.map-route
							{
								margin-right:3px;
								margin-bottom:3px;
							}
							.map-route .input-medium
							{
								border-radius:2px;
								margin-right:3px;
								margin-bottom:3px;
							}
							.map-route .btn-info
							{
								padding:4px;
								margin-bottom:3px;
							}
							.map-route .btn-inverse
							{
								background:#888 !important;
								border-color:#888 !important;
								
							}
						</style>" . $script1 . "
						<script type='text/javascript'>
						google.maps.event.addDomListener(window, 'load', initialize" . $s . ");
						  var marker" . $s . ";
						  var map" . $s . ";
							var locations" . $s . " = [" . $loc . "];
								var panorama" . $s . ";
						var mapOptions" . $s . ";
						  function initialize" . $s . "() {
						   var total=locations" . $s . ".length;
						   if(total>1)
						{
						var bounds" . $s . " = new google.maps.LatLngBounds();
						}
						var location" . $s . " = new google.maps.LatLng(" . $latlng . ");
							mapOptions" . $s . " = {
							  zoom: " . $zoom . ",
							  mapTypeId: google.maps.MapTypeId." . $google_maptype . ",
							  center: location" . $s . ",
							  ".$disable_scroll."
							};
							var styledMap = new google.maps.StyledMapType(style".$google_mapstyle.",{name: 'Styled Map'});
							map" . $s . " = new google.maps.Map(document.getElementById('map_canvas_" . $s . "'),mapOptions" . $s . ");
							map" . $s . ".mapTypes.set('map_style', styledMap);
							map" . $s . ".setMapTypeId('map_style');
							infowindow" . $s . " = new google.maps.InfoWindow({ maxWidth: 450 });
						
							var i;
						var gmarkers = [];
							for (i = 0; i < locations" . $s . ".length; i++) {  
								marker = new google.maps.Marker({
								position: new google.maps.LatLng(locations" . $s . "[i][1], locations" . $s . "[i][2]),
								map: map" . $s . ",
								icon:icons_" . $v . "[i],
								center: new google.maps.LatLng(" . $items[0]->lat_add . "," . $items[0]->long_add . "),
								});
							  " . $script2 . "
							  google.maps.event.addListener(marker, 'click', (function(marker" . $s . ", i) {
								return function() {
								  infowindow" . $s . ".setContent(locations" . $s . "[i][0]);
								  infowindow" . $s . ".open(map" . $s . ", marker" . $s . ");
								  map" . $s . ".fitBounds(bounds" . $s . ");
								  map" . $s . ".panTo(marker" . $s . ".getPosition());
								  map" . $s . ".setZoom(18);
								}
							  })(marker, i));
							  if(total>1)
								{
								  bounds" . $s . ".extend(marker.getPosition());
								  }
								}
								  panorama" . $s . " = map" . $s . ".getStreetView();
								  panorama" . $s . ".setPosition(location" . $s . ");
								  panorama" . $s . ".setPov(/** @type {google.maps.StreetViewPov} */({
									heading: 265,
									pitch: 0
								  }));
									if(total>1)
										{
										 map" . $s . ".fitBounds(bounds" . $s . ");
										 map" . $s . ".panToBounds(bounds" . $s . ");
										}
										" . $script3 . "
									  }
										function launchInfoWindow(x) {
										google.maps.event.trigger(marker" . $s . "[x], 'click');
										var listener = google.maps.event.addListener(map, 'idle', function() { 
											if (map.getZoom() > 15) map.setZoom(15);
											google.maps.event.removeListener(listener); 
										 });
										}
										
											function toggleStreetView" . $s . "(i) {
										var cent= new google.maps.LatLng(locations" . $s . "[i][1], locations" . $s . "[i][2]);
										panorama" . $s . ".setPosition(cent);
								  var toggle = panorama" . $s . ".getVisible();
									if (toggle == false) {
								   panorama" . $s . ".setVisible(true);
									 } else {
									panorama" . $s . ".setVisible(false);
									 }
									}
								function backToMap" . $s . "()
								{
									initialize" . $s . "();
									document.getElementById('infobox_" . $s . "').style.display='none';
									document.getElementById('backtomap_" . $s . "').style.display='none';

								}
								function reinitialize()
								{
									var script = document.createElement('script');
									script.type = 'text/javascript';
									script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +'callback=initialize" . $s . $key. "';
									document.body.appendChild(script);

								}
				</script>";
				$result.="<div class='map_canvas' id='map_canvas_" . $s . "'  style='width:" . $width . ";height:" . $height . ";float:left;position:relative;'></div><div class='infobox' id='infobox_" . $s . "' style='float:left;display:none'></div><input type=\"button\" class=\"btn btn-danger\" id='backtomap_" . $s . "' style='display:none;float:left;' value=\"" . Text::_("COM_MYMAPLOCATIONS_BACK") . "\" onclick=\"backToMap" . $s . "();\"></input>";
				 return $result;
		}
		else
		{
			return '';
		}
    }


    public static function formatField($field) {
        if (is_numeric($field)) {
            $field = $field . "px";
        }
        return $field;
    }

    public static function addLocalization() {
        $language = Factory::getLanguage();
        $tag = $language->getTag();
        $lang = "";
        if (strpos($tag, 'ar') !== false) {
            $lang = "&language=ar";
        } else if (strpos($tag, 'eu') !== false) {
            $lang = "&language=eu";
        } else if (strpos($tag, 'bg') !== false) {
            $lang = "&language=bg";
        } else if (strpos($tag, 'bn') !== false) {
            $lang = "&language=bn";
        } else if (strpos($tag, 'ca') !== false) {
            $lang = "&language=ca";
        } else if (strpos($tag, 'cs') !== false) {
            $lang = "&language=cs";
        } else if (strpos($tag, 'da') !== false) {
            $lang = "&language=da";
        } else if (strpos($tag, 'de') !== false) {
            $lang = "&language=de";
        } else if (strpos($tag, 'el') !== false) {
            $lang = "&language=el";
        } else if (strpos($tag, 'es') !== false) {
            $lang = "&language=es";
        } else if (strpos($tag, 'fr') !== false) {
            $lang = "&language=fr";
        } else if (strpos($tag, 'nl') !== false) {
            $lang = "&language=nl";
        } else if (strpos($tag, 'it') !== false) {
            $lang = "&language=it";
        }
        return $lang;
    }
	public static function formatJS($string) {
        $str = str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string) ($string)), "\0..\37'\\")));
        return $str;
    }
    
    /**
    * Simple caching function
    * @version  1.3
    * @param string $file
    * @param string | array $datafn                  e.g:  functionname |  array( object, function) ,
    * @param array  $datafnarg    default is array  e.g:   array( arg1, arg2, ...) ,       
    * @param mixed $time         default is 900  = 15 min
    * @param mixed $onerror      string function or array(object, method )
    * @return string
    */
    public static function Cache( $file, $time=900, $onerror='',$osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,$module_id)
    {
    	jimport('joomla.filesystem.file');
    	jimport('joomla.filesystem.folder');
    	$moduledir = basename(dirname(__FILE__));
    	
        if (is_writable(JPATH_CACHE))
        {
            // check cache dir or create cache dir
            if (!Folder::exists(JPATH_CACHE.'/'.$moduledir.$module_id))
            {

                Folder::create(JPATH_CACHE.'/'.$moduledir.$module_id.'/'); 
            }

            $cache_file = JPATH_CACHE.'/'.$moduledir.$module_id.'/'.$file;

            // check cache file, if not then write cache file
            if ( !File::exists($cache_file) )
            {
                //$data =  call_user_func_array($datafn, $datafnarg);
                $data = self::loadAllProperty($osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,'',$configClass);
                $data = json_encode($data);
                File::write($cache_file, $data);
            }  
            // if cache file expires, then write cache
            elseif ( filesize($cache_file) == 0 || ((filemtime($cache_file) + (int) $time ) < time()) )
            {
                //$data =  call_user_func_array($datafn, $datafnarg);
                $data = self::loadAllProperty($osp_category,$osp_type,$osp_countries,$state_ids,$city_ids,$maxitem,$bootstrap,$params,'',$configClass);
                $data = json_encode($data);
                File::write($cache_file, $data);
            }
            // read cache file
            $data =  file_get_contents($cache_file);
            //$params['file'] = $cache_file;
            //$params['data'] = $data;
            
            //if( !empty($onerror) ) call_user_func($onerror, $params);
            return $data;
        }
    }
}
?>
