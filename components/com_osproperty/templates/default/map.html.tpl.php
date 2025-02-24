<?php
/*------------------------------------------------------------------------
# map.html.tpl.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
echo OSPHelper::loadTooltip();
$db = Factory::getDbo();

$show_kml_export = $params->get('show_kml_export',1);
?>
<script type="text/javascript">
function loadStateInListPage(){
	var country_id = document.getElementById('country_id');
	loadStateInListPageAjax(country_id.value,"<?php echo Uri::root()?>");
}
function changeCity(state_id,city_id){
	var live_site = '<?php echo Uri::root()?>';
	loadLocationInfoCity(state_id,city_id,'state_id',live_site);
}
</script>
<div id="notice" class="nodisplay">
	
</div>
<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root();?>"/>
<?php
HelperOspropertyCommon::filterForm($lists);
?>
<div id="listings">
	<?php
	if(count($rows) > 0){
	?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> defaultbar">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> pull-left">
                    <a href="javascript:updateView(3)" title="<?php echo Text::_('OS_CHANGE_TO_GRID_VIEW');?>">
                        <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/gridview.png" class="border1 padding1"/>
                    </a>
                    <a href="javascript:updateView(2)" title="<?php echo Text::_('OS_CHANGE_TO_MAP_VIEW');?>">
                        <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/mapview.png" class="border1 padding1" />
                    </a>
                    <?php
                    if($show_kml_export == 1){
                        ?>
                        <a href="javascript:updateView(4)" title="<?php echo Text::_('OS_CHANGE_TO_GOOGLE_EARTH_KML');?>">
                            <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/kml.png" class="border1 padding1"/>
                        </a>
                    <?php
                    }
                    ?>
                    <input type="hidden" name="listviewtype" id="listviewtype" value="<?php echo $jinput->getInt('listviewtype',$_COOKIE['viewtypecookie']); ?>"/>
                    <script type="text/javascript">
                        function updateView(view){
                            var listviewtype = document.getElementById('listviewtype');
                            listviewtype.value = view;
                            document.ftForm.submit();
                        }
                    </script>
                </div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> pull-right alignright">
                    <?php
                    echo Text::_('OS_RESULTS');
                    echo " ";
                    echo $pageNav->limitstart." - ";
                    if($pageNav->total < $pageNav->limit){
                        echo $pageNav->total." ";
                    }else{
                        echo $pageNav->limitstart + $pageNav->limit." ";
                    }
                    echo Text::_('OS_OF');
                    echo " ".$pageNav->total;
                    ?>
                </div>
            </div>
        </div>
	<table  width="100%">
	<tr>
		<td width="100%">
			<link rel="stylesheet" href="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/css/vendor/slider.css">
	        <link rel="stylesheet" href="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/css/jquery.mCustomScrollbar.css">
	        <link rel="stylesheet" href="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/css/style.css">
	        <div class='location-finder'>
                <div class='left-side'>
                	<?php
                	$address = array();
                	for($i=0;$i<count($rows);$i++){
                		$row = $rows[$i];
                		$needs  = array();
                		$needs[]= "property_details";
                		$needs[]= $row->id;
                		$itemid = OSPRoute::getItemid($needs);
                		?>
                		 <article rel='<?php echo $i + 1;?>'>
	                        <figure>
								<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>">
		                            <img src="<?php echo str_replace("medium/","thumb/",$row->photo);?>" alt="" />
								</a>
	                        </figure>
	                        <div class='text'>
	                            <h3>
	                            <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>">
	                            <?php
	                            if(($row->ref != "")  and ($configClass['show_ref'] == 1)){
	                            	echo $row->ref.", ";
	                            }
	                            echo $row->pro_name;
	                            ?>
	                            </a>
	                            </h3>
	                            
	                            <?php
	                            if($row->show_address == 1){
	                            ?>
	                            <p><?php echo OSPHelper::generateAddress($row);?></p>
	                            <?php
	                            }
	                            ?>
	                            <?php 
								$sold_property_types = $configClass['sold_property_types'];
								$show_sold = 0;
								if($sold_property_types != ""){
									$sold_property_typesArr = explode("|",$sold_property_types);
									if(in_array($row->pro_type, $sold_property_typesArr)){
										$show_sold = 1;
									}
								}
								?>
								<?php if(($configClass['use_sold'] == 1) and ($row->isSold == 1) and ($show_sold == 1)){
									?>
									<span class="badge badge-warning"><strong><?php echo Text::_('OS_SOLD')?></strong></span> <?php echo Text::_('OS_ON');?>: <?php echo $row->soldOn;?>
									<?php
								}
								?> 
	                            <span class='price map_list_price'>
	                            <?php
	                            if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
								{
									echo " ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
								}
								elseif($row->price_call == 0)
								{
									if($row->price > 0)
									{
										?>
										<span id="currency_div<?php echo $i?>">
											<?php
											echo OSPHelper::generatePrice($row->curr,$row->price);
											if($row->rent_time != ""){
												echo " /".Text::_($row->rent_time);
											}
											?>
										</span>
										<?php
									}
								}else{
									echo Text::_('OS_CALL_FOR_PRICE');
								}
								?>
	                            </span>
	                        </div>
	                    </article>
                		<?php
                		
                		//prepare Google map data
                		$allow_to_show = 0;
                		if($row->show_address == 1){
                			$property_address = htmlspecialchars(OSPHelper::generateAddress($row));
                			$allow_to_show = 1;	
                		}else{
                			$property_address = $row->ref.", ".htmlspecialchars($row->pro_name);
                		}
            			if(($row->lat_add != "") and ($row->long_add != "")){
            				$coordinates = $row->lat_add.",".$row->long_add;
            				$allow_to_show = 1;
            			}
            			if($allow_to_show == 1){
	                		$j = $i + 1;
	                		$data = '{address:"'.$coordinates.'", data:"'.$property_address.'", options:{icon: "'.Uri::root().'components/com_osproperty/templates/'.$themename.'/js/img/blue-marker.png"}, tag : "'.$j.'"}';
	                		$address[] = $data;
            			}
                	}
                	?>
                </div>
                <div class='right-side'>
                    <a href="#" class='button-slider expanded'></a>
                    <div id="map_canvas"></div>
                </div>
            </div>
      
	        <script type="text/javascript">
			var jDefaultMap = jQuery.noConflict();
			</script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/bootstrap.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/bootstrap-select.min.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/jquery.flexslider-min.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/bootstrap-slider.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/jquery.mCustomScrollbar.min.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/tinynav.min.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/jquery.placeholder.min.js"></script>
			<?php
			HelperOspropertyGoogleMap::loadGoogleScript();
			?>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/vendor/gmap3.min.js"></script>
	        <script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/main.js"></script>
	        
	        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('#map_canvas').gmap3({
                            map:{
                                options:{
                                    zoom: 10
                                }
                            },
                            marker:{
                                values:[
                                    <?php echo implode(",",$address);?>
                                ],
                                options:{
                                    draggable: false
                                },
                                events:{
                                    mouseover: function(marker, event, context){
                                        var map = jQuery(this).gmap3("get"),
                                                infowindow = jQuery(this).gmap3({get:{name:"infowindow"}});
                                        		marker.setIcon("<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/img/orange-marker.png");
                                        if (infowindow){
                                            infowindow.open(map, marker);
                                            infowindow.setContent(context.data);
                                        } else {
                                            jQuery(this).gmap3({
                                                infowindow:{
                                                    anchor:marker,
                                                    options:{content: context.data}
                                                }
                                            });
                                        }
                                    },
                                    mouseout: function(marker){
                                        var infowindow = jQuery(this).gmap3({get:{name:"infowindow"}});
                                        marker.setIcon("<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/js/img/blue-marker.png");
                                        if (infowindow){
                                            infowindow.close();
                                        }
                                    }
                                }
                            }
                },
                "autofit" );
            });
        	</script>
		</td>
	</tr>
</table>
<input type="hidden" name="goole_default_lat" id="goole_default_lat" value="<?php echo $configClass['goole_default_lat']?>"/>
<input type="hidden" name="goole_default_long" id="goole_default_long" value="<?php echo $configClass['goole_default_long']?>"/>
<?php
	}
?>
<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root(); ?>" />
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js" type="text/javascript"></script>
<script src="<?php echo Uri::root() ?>media/com_osproperty/assets/js/jquery.ui.touch-punch.js" type="text/javascript"></script>
</div>