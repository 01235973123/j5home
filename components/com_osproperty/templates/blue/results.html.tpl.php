<?php
/*------------------------------------------------------------------------
# results.html.tpl.php - Ossolution Property
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
$show_google_map = $params->get('show_map',1);
$document = Factory::getDocument();
$document->addStyleSheet('//fonts.googleapis.com/css?family=Voltaire');
?>
<div id="notice" style="display:none;">
	
</div>
<div id="listings">
	<?php
	if(count($rows) > 0){
		$db = Factory::getDbo();
		$db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where id <> '$row->curr' order by currency_code");
		$currencies   = $db->loadObjectList();
		$currenyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT'));
		$currenyArr   = array_merge($currenyArr,$currencies);
		?>
		<input type="hidden" name="currency_item" id="currency_item" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
		<div class="clearfix"></div>
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
		
		<div class="latestproperties latestproperties_right">
			<?php
			$k = 0;
			for($i=0;$i<count($rows);$i++){
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
				$lists['curr'] = HTMLHelper::_('select.genericlist',$currenyArr,'curr'.$i,'onChange="javascript:updateCurrency('.$i.','.$row->id.',this.value)" class="input-small"','value','text');
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ospitem-separator">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
								<div id="ospitem-watermark_box">
									<a title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>" href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>" class="ositem-hrefphoto">
										<img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="ospitem-imgborder oslazy" />
										<?php
										if($row->isFeatured == 1){
										?>
											<img alt="<?php echo Text::_('OS_FEATURED');?>" class="spotlight_watermark" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/featured_medium.png">
										 <?php 
										}
										?>
									</a>
									
								</div>
								<div class="ospitem-propertyprice title-blue">
									<span id="currency_div<?php echo $i?>">
										<?php
										if($row->price_text != "")
										{
											echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
										}
										elseif($row->price_call == 0)
										{
											echo OSPHelper::generatePrice($row->curr,$row->price);
											if($row->rent_time != ""){
												echo " /".Text::_($row->rent_time);
											}
											if($configClass['show_convert_currency'] == 1){
											?>
											<BR />
											<span class="fontsmall">
											<?php echo Text::_('OS_CONVERT_CURRENCY')?>: <?php echo $lists['curr']?>
											</span>
											<?php
											}
										}else{
											echo " ".Text::_('OS_CALL_FOR_DETAILS_PRICE');
										}
										?>
									</span>					
								</div>
							</div>
							<?php
							if(!OSPHelper::isJoomla4())
							{
								$extraClass = "ospitem-leftpad";
							}
							else
							{
								$extraClass = "";
							}
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('span9'); ?> <?php echo $extraClass; ?>">
								<div class="ospitem-leftpad">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
									<?php 
									if(($row->commentObject->id > 0) or ($configClass['use_open_house'] == 1)){
										$span = $bootstrapHelper->getClassMapping('span9');
									}else{
										$span = $bootstrapHelper->getClassMapping('span12');
									}
									?>
										<div class="<?php echo $span; ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ospitem-toppad">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<span class="ospitem-propertytitle">
														<a title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>" href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>" class="ositem-hrefphoto nodecoration">
															<div>
															<?php
															if(($row->ref!="") and ($configClass['show_ref'] == 1)){
																?>
																<?php echo $row->ref?>,
																<?php
															}
															?>
														   <?php echo $row->pro_name?>
															
														   <?php
														   if($configClass['show_rating'] == 1){
														   ?>
															   <div class="inlinedisplay" style="width:120px;">
																	<?php echo $row->rating; ?>
															   </div>
														   <?php
														   }
														   ?>
														   </div>
													   </a>
													   <?php
														echo $row->featured_ico;
														echo $row->market_ico;
														echo $row->just_added_ico;
														echo $row->just_updated_ico;
														?>
													</span>
													<div class="clearfix"></div>
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
													<div class="clearfix"></div>
													<div class="street">
														<?php
														if(($row->show_address == 1) and ($configClass['listing_show_address'] == 1)){
															echo OSPHelper::generateAddress($row);
														}
														?>
													</div>
												</div>
											</div>
											<?php
											$addInfo = array();
											if($configClass['listing_show_nbedrooms'] == 1){
												if($row->bed_room > 0){
													$addInfo[] = $row->bed_room." ".Text::_('OS_BEDROOMS');
												}
											}
											if($configClass['listing_show_nbathrooms'] == 1){
												if($row->bath_room > 0){
													$addInfo[] = OSPHelper::showBath($row->bath_room)." ".Text::_('OS_BATHROOMS');
												}
											}
											if($configClass['listing_show_nrooms'] == 1){
												if($row->rooms > 0){
													$addInfo[] = $row->rooms." ".Text::_('OS_ROOMS');
												}
											}
											
											?>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<div class="ospitem-iconbkgr">
														<div class="pspacten">
														   <?php
														   if(($row->show_address == 1) and ($row->lat_add != "") and ($row->long_add != "") and ($show_google_map == 1)){

														   ?>
															<div class="osp-map hidden-phone">
																<a href="#map_canvas" onclick="javascript:openMarker(<?php echo $row->mapid;?>);"><img src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename?>/images/icons/map-blue.png" border="0" title="<?php echo Text::_('OS_CHECK_ON_THE_MAP')?>"></a>
															</div>
															 <?php
														   }
														   ?>
															<div class="overhidden fontsmalli">
																<?php echo Text::_('OS_AREA');?>: <span class="black fontsmallb"><?php echo $row->category_name;?></span> 
																<br><?php echo Text::_('OS_TYPE');?>: <span class="fontsmallb black"><?php echo $row->type_name;?></span>
															</div>
															
														</div>
														<?php
														if(count($addInfo) > 0){	
														?>
														<div class="ospitem-leftpad"> 
															<?php
															echo implode(" | ",$addInfo);
															?>
														</div>
														<?php
														}
														?>
														
														
													</div>
												</div>
											</div>
											<?php //} ?>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ospitem-bopad">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<p>
														<?php 
														echo $row->pro_small_desc;
														?>
													</p>
												</div>
											</div>
											<?php
											$fieldarr = $row->fieldarr;
											if(count((array)$fieldarr) > 0){
												?>
												<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ospitem-bopad">
													<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<?php
													for($f=0;$f<count($fieldarr);$f++){
														$field = $fieldarr[$f];
														?>
														<p><span class="field">
														<?php
														if($field->label != ""){
															echo $field->label;
															?>
															</span> <span>:
															<?php
														}
														?>
														<?php echo $field->fieldvalue;?>
														</span></p> 
														<?php
													}
													?>
													</div>
												</div>
												<?php
											}
											?>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<?php
													$user = Factory::getUser();
													$db   = Factory::getDBO();
														
													if($configClass['show_compare_task'] == 1){
														if(! OSPHelper::isInCompareList($row->id)) {

															$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
															$msg = str_replace("'","\'",$msg);
															?>
															<span id="compare<?php echo $row->id;?>">
																<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','default','listing_grid')" href="javascript:void(0)" class="btn btn-small" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
																	<img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24_gray.png" width="16"/>
																</a>
															</span>
															<?php
														}else{
															$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
															$msg = str_replace("'","\'",$msg);
															?>
															<span id="compare<?php echo $row->id;?>">
																<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','default','listing_grid')" href="javascript:void(0)" class="btn btn-small" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
																	<img title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24.png" width="16"/>
																</a>
															</span>
															<?php
														}
													}
													if(intval($user->id) > 0){
														if($configClass['property_save_to_favories'] == 1){
															//if($task != "property_favorites"){
															$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
															$count = $db->loadResult();
															if($count == 0){
																$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');	
																$msg = str_replace("'","\'",$msg);
																?>
																<span id="fav<?php echo $row->id;?>">
																	<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_grid')" href="javascript:void(0)" class="btn btn-small" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
																		<img title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24_gray.png" width="16"/>
																	</a>
																</span>
																<?php
															}
															//}
															if($count > 0){
																$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');	
																$msg = str_replace("'","\'",$msg);
																?>
																<span id="fav<?php echo $row->id;?>">
																	<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_grid')" href="javascript:void(0)" class="btn btn-small" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>">
																		<img title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24.png" width="16"/>
																	</a>
																</span>
																<?php
															}
														}
														if(HelperOspropertyCommon::isAgent()){
															$my_agent_id = HelperOspropertyCommon::getAgentID();
															
															if($my_agent_id == $row->agent_id){
																$link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
																?>
																<span id="favorite_1">
																	<a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>" class="btn btn-small">
																	<i class="osicon-edit"></i>
																	</a>
																</span>
																<?php
															}
														}
													}
													?>
													<a title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>" class="btn btn-small" href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid)?>">
														<i class="osicon-search"></i></a>
												</div>
											</div>
										</div>
										<?php 
										if(($row->commentObject->id > 0) or ($configClass['use_open_house'] == 1)){
										?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> col_snippet margin0">	
											<?php
											$comment = $row->commentObject;
											$rate = $row->rate;
											$cmd = $row->cmd;
											if($comment->id > 0){
											?>
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> margin0">										
												<a href="#"><?php echo $cmd?>&nbsp;<?php echo $rate?></a>
												<?php
												if($row->number_votes > 0){
												?>
												<p><?php echo Text::_('OS_BASED_ON');?> <strong><?php echo $row->number_votes;?> <?php echo Text::_('OS_REVIEWS');?></strong></p>
												<?php } ?>
												<div class="tex_left snippet_box">
													<div class="quote_open_bull"> </div>
														<p><?php echo $comment->title;?>...</p>
													<div class="quote_close_bull"></div>
												</div>
												<p><i class="osicon-user"></i><strong><?php echo $comment->name;?></strong>, 
												<?php
												if(file_exists(JPATH_ROOT.'/media/com_osproperty/flags/'.$comment->country.'.png')){
												?>
													<img src="<?php echo Uri::root()?>media/com_osproperty/flags/<?php echo $comment->country?>.png"/>
												<?php
												}
												?>
												
												<?php echo date("F j, Y",strtotime($comment->created_on));?></p>		
											</div>
											<?php
											}
											if($configClass['use_open_house'] == 1){
											?>
											<div class="clearfix"></div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> margin0">
												<div class="clearfix"></div>
												<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> <?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> inspectiontimes img-rounded">
												<strong><?php echo Text::_('OS_OPEN_FOR_INSPECTION_TIMES')?></strong>
												<div class="clearfix"></div>
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin fontsmall">
													<?php 
													if(count($row->openInformation) > 0){
														foreach ($row->openInformation as $info){
															?>
															<?php echo Text::_('OS_FROM')?>: <?php echo date($configClass['general_date_format'],strtotime($info->start_from));?>
															-
															<?php echo Text::_('OS_TO')?>: <?php echo date($configClass['general_date_format'],strtotime($info->end_to));?>
															<div class="clearfix"></div>
															<?php
														} 
													}else{
														echo Text::_('OS_NO_INSPECTIONS_ARE_CURRENTLY_SCHEDULED');
													}
													?>
												</div>
												</div>
											</div>
											<?php } ?>
										</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
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
	<?php
	}
	?>
</div>
<input type="hidden" name="process_element" id="process_element" value="" />