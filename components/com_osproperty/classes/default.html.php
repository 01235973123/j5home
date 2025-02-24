<?php

/*------------------------------------------------------------------------
# default.html.php - Ossolution Property
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
use Joomla\CMS\Language\Multilanguage;
class HTML_OspropertyDefault{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property
	 * @param unknown_type $photos
	 * @param unknown_type $info
	 * @param unknown_type $lists	
	 * @param unknown_type $configs
	 */
	static function defaultLayout($option,$property,$lists,$configs){
		global $bootstrapHelper, $mainframe,$configClass,$symbol,$languages;
		$document	= Factory::getDocument();
		$needs		= array();
		$needs[]	= "property_details";
		$needs[]	= $properties[0]->id;
		$itemid		= OSPRoute::getItemid($needs);
		if(($configClass['show_random_feature']==1 && $property->id > 0) && ($configClass['show_frontpage_box']==1 || $configClass['show_quick_search']==1))
		{
			$class1 = $bootstrapHelper->getClassMapping('span8');
			$class2 = $bootstrapHelper->getClassMapping('span4');
		}
		else
		{
			$class1 = $bootstrapHelper->getClassMapping('span12');
			$class2 = "";
		}
		?>
		<?php OSPHelper::generateHeading(2,$configClass['general_bussiness_name']);?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<?php
			if($configClass['show_frontpage_box']==1 || $configClass['show_quick_search']==1)
			{
			?>
			<div class="<?php echo $class1; ?>">
					<?php
					if($configClass['show_frontpage_box']==1){
						echo HTMLHelper::_('bootstrap.startTabSet', 'defaultpage', array('active' => 'homepagetab'));
					}elseif(($configClass['show_frontpage_box']==0) && ($configClass['show_quick_search']==1) ){
						echo HTMLHelper::_('bootstrap.startTabSet', 'defaultpage', array('active' => 'quicksearchtab'));
					}
					?>
					<?php
					if($configClass['show_frontpage_box']==1){
					?>
						<?php
						echo HTMLHelper::_('bootstrap.addTab', 'defaultpage', 'homepagetab', '<i class="osicon-home"></i> ' .Text::_('OS_HOMEPAGE', true));
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<table  width="100%" class="border0 displayblock">
									<tr>
										<td width="100%" class="padding0">
											<BR />
											<?php
											$translatable = Multilanguage::isEnabled() && count($languages);
											if($translatable){
												$default_language = OSPHelper::getDefaultLanguage();
												$language = Factory::getLanguage();
												$current_language = $language->getTag();
												if($current_language == $default_language){
													echo $configClass['introtext'];
												}else{
													$current_language = explode("-",$current_language);
													$current_language = $current_language[0];
													echo $configClass['introtext_'.$current_language];
												}
											}else{
												echo $configClass['introtext'];
											}
											?>
											<BR /><BR />
											<div class="alignleft fontbold fontsmall colorgray">
												<?php
												$user = Factory::getUser();
												if((intval($user->id)==0) and ($configClass['allow_agent_registration'])){
													?>
													<BR />
													<?php echo Text::_('OS_REGISTER_NEW_AGENT_ACCOUNT')?>
													<BR /><BR /><BR />
													<?php
													$needs = array();
													$needs[] = "agent_register";
													$needs[] = "aagentregistration";
													$itemid2  = OSPRoute::getItemid($needs);
													$itemid2  = OSPRoute::confirmItemid($itemid2,'aagentregistration');
													if($itemid2 == 0){
														$itemid2  = OSPRoute::confirmItemid($itemid2,'agent_register');	
													}
													?>
													<a href="<?php echo Route::_('index.php?option=com_osproperty&view=aagentregistration&Itemid='.$itemid2);?>" title="<?php echo Text::_('OS_BECOME_AGENT')?>" class="btn btn-warning"><i class="osicon-user"></i><?php echo Text::_('OS_BECOME_AGENT')?></a>
													<?php
												}elseif($configClass['allow_agent_registration']==1){
													$needs = array();
													$needs[] = "property_new";
													$needs[] = "aaddproperty";
													$itemid  = OSPRoute::getItemid($needs);
													?>
													<BR />
													<?php echo Text::_('OS_PUBLISH_YOUR_PROPERTIES_IN_THE_SITE_BY_CLICK_ON_THE_BELLOW_BUTTON');?>
													<BR /><BR /><BR />
													<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_new&Itemid=".$itemid);?>" title="<?php echo Text::_('OS_ADD_NEW_PROPERTY')?>" class="btn btn-warning"><i class="osicon-edit"></i><?php echo Text::_('OS_ADD_NEW_PROPERTY')?></a>
													<?php
												}
												?>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<?php
						echo HTMLHelper::_('bootstrap.endTab');
						?>
					<?php
					}
					if($configClass['show_quick_search']==1){
					?>
					<?php
					echo HTMLHelper::_('bootstrap.addTab', 'defaultpage', 'quicksearchtab', '<i class="osicon-search"></i> ' .Text::_('OS_QUICK_SEARCH', true));
					?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<?php
								$needs = array();
								$needs[] = "ladvsearch";
								$needs[] = "property_advsearch";
								$itemid1 = OSPRoute::getItemid($needs);
								?>
								<form action="<?php echo Route::_('index.php?option=com_osproperty&view=ladvsearch&Itemid='.$itemid1)?>" name="home_form" method="post">
									<?php if(HelperOspropertyCommon::checkCountry()){?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
											<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COUNTRY')?></label>
											<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
												<?php echo $lists['country'];?>
											</div>
										</div>
									<?php }else{
										echo $lists['country'];
									}
									if(!OSPHelper::userOneState()){
										?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
											<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_STATE')?></label>
											<div id="country_state" class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>"><?php echo $lists['state'];?></div>
										</div>
									<?php }else{ echo $lists['state']; }
									if ($configs['search_show_city']){
									?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
											<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_CITY'); ?></label>
											<div id="city_div" class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
												<?php echo $lists['city']; ?>
											</div>
										</div>
									<?php } ?>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PROPERTY_TYPE')?></label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php echo $lists['type'];?>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PRICE')?></label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php OSPHelper::showPriceFilter('','','',0,'','adv');?>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<input class="btn btn-info" type="submit" name="submitform" value="<?php echo Text::_('OS_SEARCH')?>" />
										</div>
									</div>
									<input type="hidden" name="option" value="<?php echo $option;?>" />
									<input type="hidden" name="task" value="property_advsearch" />
									<input type="hidden" name="default_search" value="1" />
									<input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
									<input type="hidden" name="city_name" id="city_name" value="city" />
								</form>
							</div>
						</div>
						<?php
						echo HTMLHelper::_('bootstrap.endTab');
						?>
					<?php
					}
					echo HTMLHelper::_('bootstrap.endTabSet');
					?>
			</div>
			<?php
			}
			
			if($configClass['show_random_feature']==1 && $property->id > 0){

			?>
			<div class="<?php echo $class2; ?>">
					<div id="div_dom">
						<a id="a_dom"  href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>">
						<?php
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb/'.$photos[0]->image)){
						?>
							<img id="SlideShow" name="SlideShow" alt="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>" title="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>" src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $property->id?>/medium/<?php echo $photos[0]->image?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> <?php echo $bootstrapHelper->getClassMapping('img-rounded'); ?>"/>
						<?php
						}else{
						?>
							<img id="SlideShow" name="SlideShow" alt="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>" title="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> <?php echo $bootstrapHelper->getClassMapping('img-rounded'); ?>"/>
						<?php
						}
						?>
						</a>
					</div>
				<?php
				$photo_str = "";
				$photos = $property->photos;
				if(count($photos) > 0){
					for($i=0;$i<count($photos);$i++){
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/medium/'.$photos[$i]->image)){						
							$photo_str .= '"'.Uri::root().'/images/osproperty/properties/'.$property->id.'/medium/'.$photos[$i]->image.'",';
						}else{
							$photo_str .= '"'.Uri::root().'media/com_osproperty/assets/images/nopropertyphoto.png",';
						}
					}
					$photo_str = substr($photo_str,0,strlen($photo_str)-1);
					?>
					<script type="text/javascript">
					     var slideShowSpeed = 5000; // 10 seconds per slide
					     // Duration of crossfade (seconds)
					     var crossFadeDuration = 3;
					     // Specify the image files
					     var Pic = new Array(<?php echo $photo_str?>);
					     var t;
					     var p = Pic.length;
					     var preLoad = new Array();
					     for (i = 0; i < p; i++) {
					         preLoad[i] = new Image();
					         preLoad[i].src = Pic[i];
					     }
					     var j = 0;
					     function runSlideShow() {
					         if (document.all) {
					            document.images.SlideShow.style.filter = "blendTrans(duration=2)";
					            document.images.SlideShow.style.filter = "blendTrans(duration=crossFadeDuration)";
					            document.images.SlideShow.filters.blendTrans.Apply();
					         }
					        if (j > (p - 1)) j = 0;
					        document.images.SlideShow.src = preLoad[j].src;
					         if (document.all) {
					            document.images.SlideShow.filters.blendTrans.Play();
					         }
					        j = j + 1;
					        if (j > (p - 1)) j = 0;
					         t = setTimeout('runSlideShow()', slideShowSpeed);
					     }
					     runSlideShow();
					</script>
				<?php } ?>
				<div>
					<table id="listings" class="defaultpage_listing border0">
					<tr>
						<td valign="top" class="width30pc">
							<span class="field"><?php echo Text::_('OS_LISTING_FOR')?>:</span>
						</td>
						<td class="width3px"></td>
						<td valign="top" class="width70pc">
							<span class="value"><strong><?php echo $property->type_name?></strong></span>
						</td>
					</tr>
					<tr>
						<td valign="top" class="width30pc">
							<span class="field"><?php echo Text::_('OS_COST')?>:</span>
						</td>
						<td class="width3px"></td>
						<td valign="top" class="width70pc">
							<span class="value"><strong>
							<?php
							if($property->price_text != "")
							{
								echo OSPHelper::showPriceText(Text::_($property->price_text));
							}
							elseif($property->price > 0)
							{
								echo OSPHelper::generatePrice($property->curr,$property->price);
							}
							elseif($property->price_call == 1)
							{
								echo Text::_(OS_CALL_FOR_PRICE);
							}
							?>
							</strong></span>
						</td>
					</tr>
					<?php
					if(($property->ref != "") and ($configClass['show_ref'] == 1)){
						?>
						<tr id="sf_field_1_headline">
							<td valign="top">
								<div class="field"><?php echo Text::_("Ref #")?>:</div>
							</td>
							<td class="width3px"></td>
							<td valign="top">
								<div class="value">
									<?php echo $property->ref;?>
								</div>
							</td>
						</tr>
						<?php
					}
					?>
					<tr id="sf_field_1_headline">
						<td valign="top">
							<div class="field"><?php echo Text::_('OS_HEADLINE')?>:</div>
						</td>
						<td class="width3px"></td>
						<td valign="top">
							<div class="value">
								<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>
							</div>
						</td>
					</tr>
					<?php
					if($property->show_address == 1){
					?>
					<tr id="sf_field_1_address">
						<td valign="top">
							<div class="field"><?php echo Text::_('OS_ADDRESS')?>:</div>
						</td>
						<td class="width3px"></td>
						<td valign="top">
							<div class="value">
								 <?php echo OSPHelper::generateAddress($property)?> 
							</div>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td valign="top">
							<div class="field"><?php echo Text::_('OS_CATEGORY')?>:</div>
						</td>
						<td class="width3px"></td>
						<td valign="top">
							<div class="value">
								
								<?php echo OSPHelper::getCategoryNamesOfPropertyWithLinks($property->id);//echo $property->category_name?>
							</div>
						</td>
					</tr>
					</table>
				</div>
			</div>
<?php
			}
			?>
		</div>
		<!-- show state, category lists -->
		<script type="text/javascript">
		function changeColorTab(id,ctab){
			var temp = document.getElementById(id);
			var active_menu = document.getElementById(ctab);
			active_menu_value = active_menu.value;
			
			var temp1 = document.getElementById(active_menu_value);
			temp1.style.backgroundColor = "<?php echo $configClass['homepage_background2']?>";
			var temp2 = document.getElementById("span_" + active_menu_value);
			temp1.style.color = "black";
			var div = document.getElementById('div_' + active_menu_value);
			if(div != null){
				div.style.display = "none";
			}
			
			active_menu.value = id;
			temp.style.backgroundColor = "<?php echo $configClass['homepage_background1']?>";
			var temp2 = document.getElementById("span_" + id);
			temp.style.color = "white";
			var div = document.getElementById('div_' + id);
			if(div != null){
				div.style.display = "block";
			}
		}
		</script>
		<!-- end show state lists -->
		
		<script type="text/javascript">
			var live_site = '<?php echo Uri::root()?>';
			function change_country_state(country_id){
				var url = 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=default_getstate';
				xmlHttp=GetXmlHttpObject();	
				xmlHttp.onreadystatechange = function() {
					if ( xmlHttp.readyState == 4 ) {
						var response = xmlHttp.responseText;
						document.getElementById("country_state").innerHTML = response;					
					}else{
						document.getElementById("country_state").innerHTML = '<img src="' + live_site + 'media/com_osproperty/assets/images/wait.gif"';
						
					}
				}
				xmlHttp.open( "POST", url, true );
				xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');			
				xmlHttp.send('country_id=' + country_id);
			}
			
			function loadCity(state_id,city_id){
				var live_site = '<?php echo Uri::root()?>';
				loadLocationInfoCity(state_id,city_id,'state',live_site);
			}
		</script>
	
		<?php
	}
}

?>
