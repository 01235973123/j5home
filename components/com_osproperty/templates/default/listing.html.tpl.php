<?php
/*------------------------------------------------------------------------
# listing.html.tpl.php - Ossolution Property
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
$user               = Factory::getUser();
$db                 = Factory::getDBO();
$show_kml_export    = $params->get('show_kml_export',1);
$document           = Factory::getDocument();
$document->addStyleSheet(Uri::root(true).'/components/com_osproperty/templates/default/style/font.css');
$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$span10Class        = $bootstrapHelper->getClassMapping('span10');
$span2Class         = $bootstrapHelper->getClassMapping('span2');
$span6Class         = $bootstrapHelper->getClassMapping('span6');
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
<div id="notice" style="display:none;">
	
</div>
<?php
HelperOspropertyCommon::filterForm($lists);
?>
<div id="listings">
	<?php
	if(count($rows) > 0)
	{
	?>
        <div class="<?php echo $rowFluidClass; ?> defaultbar">
            <div class="<?php echo $span6Class; ?> pull-left">
                <a href="javascript:updateView(3)" title="<?php echo Text::_('OS_CHANGE_TO_GRID_VIEW');?>">
                    <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/gridview.png" class="border1 padding1" />
                </a>
                <a href="javascript:updateView(2)" title="<?php echo Text::_('OS_CHANGE_TO_MAP_VIEW');?>">
                    <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/mapview.png" class="border1 padding1" />
                </a>
                <?php
                if($show_kml_export == 1){
                    ?>
                    <a href="javascript:updateView(4)" title="<?php echo Text::_('OS_CHANGE_TO_GOOGLE_EARTH_KML');?>">
                        <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/kml.png" class="border1 padding1" />
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
            <div class="<?php echo $span6Class; ?> pull-right alignright">
                <?php
                echo Text::_('OS_RESULTS');
                echo " ";
                $start = $pageNav->limitstart + 1;
                echo $start." - ";
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
		<input type="hidden" name="currency_item" id="currency_item" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
		<div class="latestproperties latestproperties_right">
			<ul class="display padding0">
			<?php
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				$needs = array();
				$needs[] = "property_details";
				$needs[] = $row->id;
				$itemid = OSPRoute::getItemid($needs);
				if($configClass['load_lazy']){
					$photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
				}else{
					$photourl = $row->photo;
				}
				$lists['curr'] = HTMLHelper::_('select.genericlist',$currenyArr,'curr'.$i,'onChange="javascript:convertCurrencyDefault('.$row->id.',this.value,0)" class="input-small"','value','text');
				?>
				<li class="featured">
				<?php
				if($row->isFeatured == 1){
				?>
		       	 	<div class="featured_strip"><?php echo Text::_('OS_FEATURED')?></div>
		        <?php 
				}
				?>	
				
				<?php
				$width = $configClass['listing_photo_width_size'];
				if(intval($width) == 0){
					$width = 120;
				}
				?>
				<style>
				.photos_count{
					width:<?php echo $width?>px !important;
				}
				</style>
	       		<div class="<?php echo $rowFluidClass; ?> content_block">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?> noleftmargin">
						<div class="item-photo">
							<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid);?>">
								<img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="oslazy" id="picture_<?php echo $row->id;?>" />
							</a>
							<?php
							if($row->count_photo > 1)
							{
							?>
								<i class="property_details_photo_prev" id="property_details_photo_prev_<?php echo $row->id?>"></i>
								<i class="property_details_photo_next" id="property_details_photo_next_<?php echo $row->id?>"></i>
								<div class="property_details_photo_count">
									<span class="current_number" id="current_number_<?php echo $row->id?>">1</span>/<span class="total_number"><?php echo $row->count_photo;?></span>
								</div>
								<input type="hidden" name="current_picture_<?php echo $row->id?>" id="current_picture_<?php echo $row->id?>" value="1" />
							<?php
							}	
							?>
							<span class="type_name">
								<?php echo $row->type_name; ?>
							</span>
							<?php
							if($configClass['listing_show_price'] == 1)
							{
								?>
								<span class="price_value">
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
										<div id="currency_div<?php echo $row->id;?>">
												<?php
												echo OSPHelper::generatePrice($row->curr,$row->price);
												if($row->rent_time != ""){
													echo "/".Text::_($row->rent_time);
												}
												?>
											</div>
										<?php
									}
								}
								else
								{
									echo Text::_('OS_CALL_FOR_PRICE');
								}
								?>
								</span>
								<?php
							}
							?>

							<div class="propertyoptions">
								<?php
								if(($configClass['show_getdirection'] == 1) and ($row->show_address== 1)){
									?>
									<a href="<?php echo Route::_("index.php?option=com_osproperty&task=direction_map&id=".$row->id)?>" title="<?php echo Text::_('OS_GET_DIRECTIONS')?>" class="getdirectiondefault">
										<i class="osicon-move"></i>
									</a>

									<?php
								}
								if($configClass['show_compare_task'] == 1)
								{
									if(! OSPHelper::isInCompareList($row->id))
									{
										$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
										$msg = str_replace("'","\'",$msg);
										?>
										<span id="compare<?php echo $row->id?>">
										<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','default','listing_list')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" class="compareLink">
											<span class="edicon edicon-copy"></span>
										</a>
									</span>
										<?php
									}else{
										$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
										$msg = str_replace("'", "\'", $msg);
										?>
										<span id="compare<?php echo $row->id?>">
										<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','default','listing_list')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" class="compareLink activated">
											<span class="edicon edicon-copy"></span>
										</a>
									</span>
										<?php
									}
								}
								if(intval($user->id) > 0)
								{
									if($configClass['property_save_to_favories'] == 1)
									{
										if($task != "property_favorites")
										{
											$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
											$count = $db->loadResult();
											if($count == 0){
												$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
												$msg = str_replace("'","\'",$msg);
												?>
												<span id="fav<?php echo $row->id?>">
												<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" class="favLink">
													<span class="edicon edicon-heart"></span>
												</a>
											</span>
												<?php
											}
										}
										if($count > 0)
										{
											$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
											$msg = str_replace("'","\'",$msg);
											?>
											<span id="fav<?php echo $row->id?>">
											<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" class="favLink activated">
												<span class="edicon edicon-heart"></span>
											</a>
										</span>
											<?php
										}
									}
									if(HelperOspropertyCommon::isAgent())
									{
										$my_agent_id = HelperOspropertyCommon::getAgentID();

										if($my_agent_id == $row->agent_id)
										{
											$link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
											?>
											<a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>" class="editLink">
												<span class="edicon edicon-pencil"></span>
											</a>

											<?php
										}
									}
								}
								?>
							</div>
								
							<?php //} ?>
						</div>
					</div>
                    <?php
                    if(OSPHelper::allowShowingProfileInListing($row->optin))
                    {
                        $span1 = $span10Class;
                        $span2 = $span2Class;
                    }
                    else
                    {
                        $span1 = $span12Class;
                    }
                    ?>
					<div class="content <?php echo $bootstrapHelper->getClassMapping('span8'); ?> noleftmargin">
                        <div class="<?php echo $rowFluidClass?>">
                            <div class="<?php echo $span1;?>">
                                <h3 class="clearfix">
                                    <div class="<?php echo $rowFluidClass; ?>">
                                        <div class="<?php echo $span12Class; ?>">
                                            <a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>" class="property_title" title="<?php echo $row->pro_name?>">
                                                <?php
                                                if(($row->ref!="")  and ($configClass['show_ref'] == 1)){
                                                    ?>
                                                    <?php echo $row->ref?>,
                                                    <?php
                                                }
                                                ?>
                                               <?php echo $row->pro_name?>
                                            </a>
                                            <?php
                                            echo $row->featured_ico;
                                            echo $row->market_ico;
                                            echo $row->just_added_ico;
                                            echo $row->just_updated_ico;
                                           ?>
                                        </div>
                                    </div>
                                </h3>
                                <?php
                                if(($row->show_address == 1) && ($configClass['listing_show_address'] == 1)){
                                    ?>
                                    <p class="address">
                                        <i class="fa fa-map-marker"></i>
                                        <?php
                                        echo OSPHelper::generateAddress($row);
                                        ?>
                                    </p>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if(OSPHelper::allowShowingProfileInListing($row->optin))
                            {
                                ?>
                                <div class="<?php echo $span2; ?>">
                                    <a title="<?php echo $row->agent_name?>" href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->agent_id.'&Itemid='.$jinput->getInt('Itemid',0));?>">
                                        <?php
                                        if($row->agent_photo != "")
                                        {
                                            if(file_exists(JPATH_ROOT.DS."images".DS."osproperty".DS."agent".DS."thumbnail".DS.$row->agent_photo))
                                            {
                                                ?>
                                                <img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent_photo?>" class="border1 padding3 height60px"/>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" class="border1 padding3 height60px" />
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" class="border1 padding3 height60px"/>
                                            <?php
                                        }
                                        ?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <p class="bottompropertytitle"></p>
						<div class="property_detail <?php echo $rowFluidClass;?>">
							<!-- show custom field data -->
							<?php
							$addCssClass = "noborder";
                            if(($configClass['use_squarefeet'] == 1 && $row->square_feet > 0) || (($configClass['listing_show_nbedrooms'] == 1) & ($row->bed_room > 0)) || (($configClass['listing_show_nbathrooms'] == 1) & ($row->bath_room > 0)) || $row->parking != "")
							{
								$addCssClass = "";
                            ?>
                            <div class="property-info-agent noleftmargin">
                                <ul class="base-information">
                                    <?php
                                    if(($configClass['use_squarefeet'] == 1) && ($row->square_feet > 0))
                                    {
                                        ?><li class="property-icon-square meta-block">
                                        <i class="ospico-square"></i>
                                        <span>
                                            <?php
                                            echo OSPHelper::showSquare($row->square_feet);
                                            echo "&nbsp;";
                                            echo OSPHelper::showSquareSymbol();
                                            ?>
                                        </span></li>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if(($configClass['listing_show_nbedrooms'] == 1) & ($row->bed_room > 0))
                                    {
                                        ?>
                                        <li class="property-icon-bed meta-block">
                                            <i class="ospico-bed"></i>
                                            <span><?php echo $row->bed_room;?> <?php echo Text::_('OS_BEDS');?></span></li>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if(($configClass['listing_show_nbathrooms'] == 1) & ($row->bath_room > 0))
                                    {
                                        ?>
                                        <li class="property-icon-bath meta-block">
                                            <i class="ospico-bath"></i>
                                            <span> <?php echo OSPHelper::showBath($row->bath_room);?> <?php echo Text::_('OS_BATHS');?></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if($row->parking != ""){
                                        ?>
                                        <li class="property-icon-parking meta-block">
                                            <i class="ospico-parking"></i>
                                            <span><?php echo $row->parking;?></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>

	                    <p class="propertylistinglinks <?php echo $addCssClass; ?>">
	                    	<?php
							echo $row->other_information;
							?>
	                    </p>
						</div>
					</div>
				</li>
				<?php
			}
			?>
			</ul>
		</div>
		<?php
	}
	?>
	
</div>
<div>
    <?php
    if((count($rows) > 0) and ($pageNav->total > $pageNav->limit)){
        ?>
        <div class="pageNavdiv">
			<?php
				echo $pageNav->getListFooter();
			?>
		</div>
		<?php
	}
	?>
</div>
<script type="text/javascript">
<?php
foreach ($rows as $row){
	if($row->count_photo > 1){
		?>
		var pictureArr<?php echo $row->id?> = new Array();
		<?php
		$photoArr = $row->photoArr;
		$j = 0;
		foreach ($photoArr as $photo){
			?>
			pictureArr<?php echo $row->id?>[<?php echo $j?>] = "<?php echo $photo;?>";
			<?php
			$j++;
		}
		?>
		jQuery( "#property_details_photo_prev_" + <?php echo $row->id?> ).click(function() {
			var current_item = document.getElementById("current_picture_" + <?php echo $row->id?>).value;
			if(current_item <= 1){
				current_item = <?php echo $row->count_photo;?>;
			}else{
				current_item--;
			}
		
			jQuery( "#picture_" + <?php echo $row->id?> ).attr("src","<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/medium/" + pictureArr<?php echo $row->id?>[current_item - 1]);
			document.getElementById("current_picture_" +  <?php echo $row->id?>).value    = current_item;
			document.getElementById("current_number_" +  <?php echo $row->id?>).innerHTML = current_item;
		});
		
		jQuery( "#property_details_photo_next_" + <?php echo $row->id?> ).click(function() {
			var current_item = document.getElementById("current_picture_" + <?php echo $row->id?>).value;
			if(current_item >= <?php echo $row->count_photo;?>){
				current_item = 1;
			}else{
				current_item++;
			}
		
			jQuery( "#picture_" + <?php echo $row->id?> ).attr("src","<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/medium/" + pictureArr<?php echo $row->id?>[current_item - 1]);
			document.getElementById("current_picture_" +  <?php echo $row->id?>).value    = current_item;
			document.getElementById("current_number_" +  <?php echo $row->id?>).innerHTML = current_item;
		});
		<?php
	}
}
?>
</script>
<input type="hidden" name="process_element" id="process_element" value="" />
