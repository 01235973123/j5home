<?php
/*------------------------------------------------------------------------
# results.html.tpl.php - Ossolution Property
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
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/".$themename."/style/font.css");
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
$show_google_map = $params->get('show_map',1);
?>
<div id="listings" class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<?php
		if(count($rows) > 0)
		{
			jimport('joomla.filesystem.file');
			$db = Factory::getDbo();
			$db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where id <> '$row->curr' order by currency_code");
			$currencies   = $db->loadObjectList();
			$currenyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT'));
			$currenyArr   = array_merge($currenyArr,$currencies);
			?>
			<input type="hidden" name="currency_item" id="currency_item" value="" />
			<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
			<div class="clearfix"></div>		
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> toplisting">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span5'); ?> map hidden-phone">
					<?php
					if ($show_google_map == 1)
					{
						if($configClass['map_type'] == 0)
						{
							if(HelperOspropertyGoogleMap::loadMapInListing($rows))
							{
								?>
								<div id="map_canvas" class="map2x relative"></div>
								<?php
							}
						}
						else
						{
							HelperOspropertyOpenStreetMap::loadMapInListing($rows);
						}

					}
					?>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span7'); ?> properties">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<?php
						$j = 0;
						for($i=0;$i<count($rows);$i++) 
						{
							$row = $rows[$i];
							$needs = [];
							$needs[] = "property_details";
							$needs[] = $row->id;
							$itemid = OSPRoute::getItemid($needs);
							$link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);
							if($configClass['load_lazy']){
								$photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
							}else{
								$photourl = $row->photo;
							}
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
								<div class="property_item" data-lat="<?php echo $row->lat_add?>" data-long="<?php echo $row->long_add;?>">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<figure>
												<h6 class="snipe ptype<?php echo $row->pro_type;?> ptype blockdisplay">
													<span><?php echo $row->type_name;?></span>
												</h6>
												<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" class="property_mark_a">
													<img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="ospitem-imgborder oslazy" id="picture_<?php echo $i?>" />
												</a>
												<?php
												if(($configClass['property_save_to_favories'] == 1) && ($user->id > 0)){
												?>
												<span class="save-this">
													<span class="wpfp-span">
														<?php
															if($row->inFav == 0){
																?>
																<span id="fav<?php echo $row->id;?>">
																	<?php
																	$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
																	$msg = str_replace("'","\'",$msg);
																	?>
																	<a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','listing')" class="favLink">
																		<span class="edicon edicon-heart"></span>
																	</a>
																</span>
																<?php
															}else{
																?>
																<span id="fav<?php echo $row->id;?>">
																	<?php
																	$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
																	$msg = str_replace("'","\'",$msg);
																	?>
																	<a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','listing')" href="javascript:void(0)" class="favLinkActive">
																		<span class="edicon edicon-heart"></span>
																	</a>
																</span>
															<?php
															}
														?>
													</span>
												</span>
												<?php
												}
												if($row->isFeatured == 1){
												?>
													<span class="theme2_featuredproperties">
														<?php echo Text::_('OS_FEATURED');?>
													</span>
												<?php }
												if($configClass['active_market_status'] == 1 && $row->isSold > 0){
												?>
													<span class="theme2_marketstatusproperties">
														<?php echo OSPHelper::returnMarketStatus($row->isSold);?>
													</span>
												<?php } 
												?>
											</figure>
											<div class="grid-listing-info">
												<header>
													<h5 class="marB0">
														<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" class="property_mark_a"><?php echo $row->pro_name?></a>
													</h5>
												</header>
												<div class="property-details">
													<span class="price">
														<?php
														if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
														{
															echo " ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
														}
														elseif($row->price_call == 1)
														{
															echo Text::_('OS_CALL_FOR_PRICE');
														}
														else
														{
															echo OSPHelper::generatePrice($row->curr, $row->price);
															if($row->rent_time != ""){
																echo " /".Text::_($row->rent_time);
															}
														}
														?>
													</span>
													<div class="info">
														<?php
														if($configClass['listing_show_nbedrooms'] == 1 && $row->bed_room > 0)
														{
														?>
															<span><i class="fas fa-bed"></i>&nbsp;<?php echo $row->bed_room; ?></span>
															
														<?php } 
														if($configClass['listing_show_nbathrooms'] == 1 && $row->bath_room > 0){
														?>
															<span><i class="fas fa-bath"></i>&nbsp;<?php echo OSPHelper::showBath($row->bath_room); ?></span>
														<?php } ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php
							$j++;
							if($j == 2){
								$j = 0;
								?>
								</div><div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>
			<div>
				<?php
				if((count($rows) > 0) && ($pageNav->total > $pageNav->limit)){
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
		<?php
		}
		?>
	</div>
</div>
<input type="hidden" name="process_element" id="process_element" value="" />
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