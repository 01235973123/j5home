<?php
/*------------------------------------------------------------------------
# small.details.html.tpl.php - Ossolution Property
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
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/default/style/small.style.css");
?>

<style>
#main ul{
	margin:0px;
}
</style>
<script type="text/javascript">
function showhideDiv(id){
	var temp1 = document.getElementById('fs_' + id);
	var temp2 = document.getElementById('fsb_' + id);
	if(temp1.style.display == "block"){
		temp1.style.display = "none";
		temp2.innerHTML = "[+]";
	}else{
		temp1.style.display = "block";
		temp2.innerHTML = "[-]";
	}
}
</script>
<div id="notice" style="display:none;">
	
</div>
<table class="sTable">
	<tr>
		<td valign="top">
			<h1 style="border:0px;">
			<div style="float:left;">
				<?php
				if($row->ref != ""){
					echo $row->ref.", ";
				}
				?>
				<?php echo $row->pro_name?>
				<?php
				if($row->isFeatured == 1){
					?>
					<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/isfeatured.png" />
					<?php
				}
				$created_on = $row->created;
			    $modified_on = $row->modified;
			    $created_on = strtotime($created_on);
			    $modified_on = strtotime($modified_on);
			    if($created_on > time() - 3*24*3600){ //new
			    	if($configClass['show_just_add_icon'] == 1){
				    	?>
				    	<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/justadd.png" />
				    	<?php
			    	}
			    }elseif($modified_on > time() - 2*24*3600){
			    	if($configClass['show_just_update_icon'] == 1){
				    	?>
				    	<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/justupdate.png" />
				    	<?php
			    	}
			    }
				?>
				<BR />
				<div style="padding-top:10px; display:inline;margin-top:10px;">
					
					<div id="plusone" style="width:75px; float:left;"><g:plusone size="medium" href="<?php echo Uri::root()."index.php?option=com_osproperty&amp;task=property_details&amp;id=".$row->id?>" count="true"></g:plusone></div>
					<!--
					<div id="twitter" style="float:left;"><a style="padding-top:10px;" href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="<?php echo $configClass['general_bussiness_name']?>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
					-->
					<div id="fbsend" style="float:left;">
					<fb:send href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id")?>" font="verdana"></fb:send>
					</div>
					<div id="facebook" style="float:left; padding-left:5px;">
					<?php
					if($configs[88]->fieldvalue == 1){
					?>
					<div id="fb_share_button">
						<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode($socialUrl);?>
							&amp;layout=standard
							&amp;show_faces=true
							&amp;width=150
							&amp;action=like
							&amp;font=arial
							&amp;colorscheme=light
							&amp;locale=en_GB"
							scrolling="no"
							frameborder="0"
							allowTransparency="true"
							style="border:none;
							overflow:hidden;
							width:360px;
							height:28px">
						</iframe>					
					</div>
					<?php
					}
					?>
					
					</div>
					<br />
				</div>

			</div>
			</h1>
		</td>
		<td align="right" style="width: 120px;padding-top:20px;" id="content_nav_icons" valign="top">
		<?php
		if(HelperOspropertyCommon::isAgent()){
			$my_agent_id = HelperOspropertyCommon::getAgentID();
			if($my_agent_id == $row->agent_id){
				$link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
				?>
				<span id="compare_3">
					<a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>">
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/edit16.png" border="0" />
					</a>
				</span>
				<?php
			}
		}
		if(($configClass['show_getdirection'] == 1) and ($row->show_address == 1)){
		?>
		<span id="compare_3">
			<a href="<?php echo Route::_("index.php?option=com_osproperty&task=direction_map&id=".$row->id)?>" title="<?php echo Text::_('OS_GET_DIRECTIONS')?>">
			<img class="png" title="<?php echo Text::_('OS_GET_DIRECTIONS')?>" alt="<?php echo Text::_('OS_GET_DIRECTIONS')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/direction.gif" /></a>
		</span>
		<?php
		}
		if($configClass['show_compare_task'] == 1){
		?>
		<span id="compare_3">
			<a onclick="javascript:osConfirm('<?php echo Text::_(OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_COMPARE_LIST)?>','ajax_addCompare','<?php echo $row->id?>','<?php echo Uri::root()?>')" href="javascript:void(0)">
			<img class="png" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare.png" /></a>
		</span>
		<?php
		}
		$user = Factory::getUser();
		if(($configClass['property_save_to_favories'] == 1) and ($user->id > 0)){
		?>
		<span id="favorite_3">
			<a onclick="javascript:osConfirm('<?php echo Text::_(OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS)?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>')" href="javascript:void(0)">
			<img title="<?php echo Text::_(OS_ADD_TO_FAVORITES)?>" alt="<?php echo Text::_(OS_ADD_TO_FAVORITES)?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/savefavorites.png" /></a>
		</span>
		<?php
		}
		if($configs[57]->fieldvalue == 1){
		?>
			<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_pdf&id=<?php echo $row->id?>" title="<?php echo Text::_('OS_EXPORT_PDF')?>"  rel="nofollow" target="_blank">
			<img alt="<?php echo Text::_(OS_EXPORT_PDF)?>" title="<?php echo Text::_(OS_EXPORT_PDF)?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/tpdf.png" /></a>
		<?php
		}
		if($configs[58]->fieldvalue == 1){
		?>
			<a target="_blank" href="index.php?option=com_osproperty&no_html=1&task=property_print&id=<?php echo $row->id?>"><img alt="<?php echo Text::_(OS_PRINT_THIS_PAGE)?>" title="<?php echo Text::_(OS_PRINT_THIS_PAGE)?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/print.gif" /></a>
		<?php
		}
		?>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="text-align:right;">
			<?php echo $row->price?>
		</td>
	</tr>
</table>
<div class="jwts_tabber" id="jwts_tab">
	<!-- Listing -->
	<div class="jwts_tabbertab" title="<?php echo Text::_(OS_LISTING)?>" >
		<h2><a href="javascript:void(null);" name="advtab" ><?php echo Text::_(OS_LISTING)?></a></h2>
		<!-- Listing details -->
		<div style="padding-top:10px;">
		
		<?php
		if(!OSPHelper::isJoomla4())
		{
			HTMLHelper::_('behavior.modal','a.osmodal');
		}
		else
		{
			OSPHelperJquery::colorbox('a.osmodal');
		}
		$mapwidth = $configClass['property_map_width'];
		if(intval($mapwidth) == 0){
			$mapwidth = 500;
		}
		?>
		<table class="sTable fg" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%">
					<!-- top bar -->
						<div class="property-details-main-div">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tabphoto" id="aphoto" data-toggle="tab"><?php echo Text::_('OS_PHOTO');?></a></li>
								<?php
								if($row->show_address == 1){
								?>
									<li><a href="#tabgoogle" data-toggle="tab"  id="agooglemap"><?php echo Text::_('OS_MAP');?></a></li>
									<?php
									
									if($configClass['show_streetview'] == 1){
									?>
									<li><a href="#tabstreet" data-toggle="tab" id="astreetview"><?php echo Text::_('OS_STREET_VIEW');?></a></li>
									<?php
									}
									?>
								<?php } ?>
							</ul>
							 
							<div class="tab-content">
								<div class="tab-pane active" id="tabphoto">
								  <?php
								  HelperOspropertyCommon::propertyGallery($row->id,$photos);
								  ?>
								</div>
								<?php
								if($row->show_address == 1){
								?>
									<div class="tab-pane" id="tabgoogle">
										<div id="map_canvas" style="height: <?php echo $configClass['property_map_height']?>px; width: <?php echo $mapwidth?>px;"></div>
									</div>
									<?php
									
									if($configClass['show_streetview'] == 1){
									?>
									<div class="tab-pane" id="tabstreet">
										<div id="pano" style="height: <?php echo $configClass['property_map_height']?>px; width: <?php echo $mapwidth?>px;"></div>
									</div>
									<?php
									}
								}
								?>
							</div>
						</div>
					<!-- end top bar -->
				</td>
			</tr>
			</table>
			<?php
			if($row->show_address == 1){
			?>
			<!-- Location -->
			<table class="sTable fg" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%">
					<div class="width-100 fltlft">
						<fieldset class="adminform">
							<legend><?php echo Text::_(OS_LOCATION)?> <a href="javascript:showhideDiv('location');" id="fsb_location">[-]</a></legend>
							<div id="fs_location" style="margin: 0 7px;display:block;">
							<?php echo $row->location?>
						</fieldset>
					</div>
				</td>
			</tr>
			</table>
			
			<!-- End location -->
			<?php
			} //end show address ?
			?>
			
			<!-- Information -->
			<table class="sTable fg" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%">
					<div class="width-100 fltlft">
						<fieldset class="adminform">
							<legend><?php echo Text::_(OS_PROPERTY_INFORMATION)?> <a href="javascript:showhideDiv('information');" id="fsb_information">[-]</a></legend>
							<div id="fs_information" style="margin: 0 7px;display:block;">
							<?php echo $row->info?>
						</fieldset>
					</div>
				</td>
			</tr>
			</table>
			<!-- End infomration -->
			<?php
			if(($configClass['show_amenity_group'] == 1) or ($configClass['show_feature_group'] == 1)){
				
			if(($configClass['show_amenity_group'] == 1) and ($configClass['show_feature_group'] == 1)){
				$width1 = 49;
				$width2 = 2;
				$width3 = 49;
			}elseif(($configClass['show_amenity_group'] == 1) and ($configClass['show_feature_group'] == 0)){
				$width1 = 100;
				$width2 = 0;
				$width3 = 0;
			}elseif(($configClass['show_amenity_group'] == 0) and ($configClass['show_feature_group'] == 1)){
				$width1 = 0;
				$width2 = 0;
				$width3 = 100;
			}
			?>
			<table cellpadding="0" cellspacing="0" width="100%" style="border:0px !important;">
				<tr>
					<?php
					if($configClass['show_feature_group'] == 1){
					?>
					<td width="<?php echo $width1?>%" align="center" valign="top">
						<!-- Propety feature -->						
						<table class="sTable fg" cellpadding="0" cellspacing="0">
						<tr>
							<td width="100%">
								<div class="width-100 fltlft">
									<fieldset class="adminform">
										<legend><?php echo Text::_(OS_PROPERTY_FEATURE)?> <a href="javascript:showhideDiv('feature');" id="fsb_feature">[-]</a></legend>
										<div id="fs_feature" style="margin: 0 7px;display:block;">
										<?php echo $row->featured?>
									</fieldset>
								</div>
							</td>
						</tr>
						</table>
						<!-- End Propety feature -->
					</td>
					<?php
					}
					if(($configClass['show_amenity_group'] == 1) and ($configClass['show_feature_group'] == 1)){
					?>
					<td width="<?php echo $width2?>%">
					
					</td>
					<?php
					}
					if($configClass['show_amenity_group'] == 1){
					?>
					<td width="<?php echo $width3?>%" align="center" valign="top">
						<!-- Propety feature -->
						<table class="sTable fg" cellpadding="0" cellspacing="0">
						<tr>
							<td width="100%">
								<div class="width-100 fltlft">
									<fieldset class="adminform">
										<legend><?php echo Text::_(OS_AMENITIES)?> <a href="javascript:showhideDiv('amenity');" id="fsb_amenity">[-]</a></legend>
										<div id="fs_amenity" style="margin: 0 7px;display:block;">
										<?php echo $row->amens_str?>
									</fieldset>
								</div>
							</td>
						</tr>
						</table>
						<!-- End Propety feature -->
					</td>
					<?php
					}
					?>
				</tr>
			</table>
			<?php
			}
			if((trim($row->neighborhood) != "") and ($configClass['show_neighborhood_group'] == 1)){
			?>
				<table class="sTable fg" cellpadding="0" cellspacing="0">
				<tr>
					<td width="100%">
						<div class="width-100 fltlft">
							<fieldset class="adminform">
								<legend><?php echo Text::_(OS_NEIGHBORHOOD)?> <a href="javascript:showhideDiv('neighborhood');" id="fsb_neighborhood">[-]</a></legend>
								<div id="fs_neighborhood" style="margin: 0 7px;display:block;">
								<?php
									echo $row->neighborhood;
								?>
							</fieldset>
						</div>
					</td>
				</tr>
				</table>
			<?php
			}
			
			if(count($row->extra_field_groups) > 0){
				$extra_field_groups = $row->extra_field_groups;
				for($i=0;$i<count($extra_field_groups);$i++){
				
					$group = $extra_field_groups[$i];
					$group_name = $group->group_name;
					$fields = $group->fields;
					if(count($fields)> 0){
					?>
					<table class="sTable fg" cellpadding="0" cellspacing="0">
						<tr>
							<td width="100%">
								<div class="width-100 fltlft">
									<fieldset class="adminform">
										<legend><?php echo $group_name;?> <a href="javascript:showhideDiv('<?php echo $i?>');" id="fsb_<?php echo $i?>">[-]</a></legend>
										<div id="fs_<?php echo $i?>" style="margin: 0 7px;display:block;">
										<?php
										echo '<table cellpadding="0" cellspacing="0" width="100%">';
										for($j=0;$j<count($fields);$j++){
											$field = $fields[$j];
											
											if($field->displaytitle == 1){
												?>
												<tr>
													<td class="left_details_col">
														<?php
														if($field->field_description != ""){
														?>
															<span class="editlinktip hasTip" title="<?php echo $field->field_label;?>::<?php echo $field->field_description?>">
																<?php echo $field->field_label;?>
															</span>
														<?php
														}else{
														?>
															<?php echo $field->field_label;?>
														<?php
														}
														?>
													</td>
													<td class="right_details_col">
														<?php echo $field->value;?>
													</td>
												</tr>
												<?php
											}else{
												?>
												<tr>
													<td class="right_details_col">
														<?php echo $field->value;?>
													</td>
												</tr>
												<?php
											}
										}
										echo '</table>';
										?>
									</fieldset>
								</div>
							</td>
						</tr>
					</table>
					<?php
					}
				}
			}
			?>
			<!-- Propety other information -->
			
		</div>
		
	</div>
	<!-- end listing -->
	<?php
	if($configClass['show_walkscore'] == 1){
		if($configClass['ws_id'] != ""){
	?>
			<div class="jwts_tabbertab" title="<?php echo Text::_('OS_WALK_SCORE')?>">
				<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_('OS_WALK_SCORE')?></a></h2>
				<div style="margin: 10px 10px 0 10px">
					<?php
					echo $row->ws;
					?>
				</div>
			</div>
		<?php
		}
	}
	
	echo $row->gallery;
	
	if($configClass['show_agent_details'] == 1){
	?>
	<div class="jwts_tabbertab" title="<?php echo Text::_(OS_AGENT_INFO)?>">
		<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_(OS_AGENT_INFO)?></a></h2>
		<div class="block_caption">
			<strong><?php echo Text::_(OS_AGENT_INFORMATION)?></strong>
		</div>
		<?php
		echo $row->agent;
		?>
	</div>
	<?php
	}
	if($configs[56]->fieldvalue == 1){
	?>
	<div class="jwts_tabbertab" title="<?php echo Text::_(OS_SHARING)?>">
		<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_(OS_TELL_A_FRIEND)?></a></h2>
		
		
		<div class="block_caption">
			<strong><?php echo Text::_(OS_TELL_A_FRIEND_FORM)?></strong>
		</div>	
		<div style="margin: 10px 10px 0 10px">
		<div class="blue_middle"><?php echo Text::_(OS_FIELDS_MARKED);?> <span class="red">*</span> <?php echo Text::_(OS_ARE_REQUIRED);?></div>
		<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submittellfriend&Itemid=<?php echo $itemid?>" name="tellfriend_form" id="tellfriend_form">
		<table class="sTable">
		<tr>
			<td style="width: 20%;">
				<span class="grey_small"><?php echo Text::_(OS_FRIEND_NAME);?> <span class="red">*</span></span>
			</td>
			<td>
				<input class="input-large" type="text" id="friend_name" name="friend_name" maxlength="30"  />
			</td>
	
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_FRIEND_EMAIL);?> <span class="red">*</span></span>
			</td>
			<td>
				<input class="input-large" type="text" id="friend_email" name="friend_email" maxlength="30"  />
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_YOUR_NAME);?></span>
			</td>
			<td>
				<input class="input-large" type="text" id="your_name" name="your_name" maxlength="30" />
			</td>
	
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_YOUR_EMAIL);?></span>
			</td>
			<td>
				<input class="input-large" type="text" id="your_email" name="your_email" maxlength="30" />
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_MESSAGE);?></span>
			</td>
			<td>
				<textarea class="inputbox" id="message" name="message" rows="6" cols="40" class="inputbox"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_SECURITY_CODE)?> <span class="red">*</span></span>
			</td>
			<td>
				<table>
					<tr>
						<td valign="bottom">
							<img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>" /> 
							<!--<img src="<?php echo Uri::root()?>index.php?option=com_ospropery&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>"> -->
						</td>
						<td valign="bottom">
							<span class="grey_small" style="line-height:16px;"><?php echo Text::_(OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW)?></span><br />
							<input type="text" class="input-mini" id="sharing_security_code" name="sharing_security_code" maxlength="5" style="width: 50px; margin: 0;" />
						</td>
					</tr>
				</table>
				<div class="clear"></div>		
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-top:10px;">
				<input class="btn btn-primary" type="button" name="finish" value="<?php echo Text::_('OS_SEND');?>" onclick="javascript:submitForm('tellfriend_form');"/>
				<span class="reg_loading" id="tf_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_submittellfriend" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid')?>" />
		<input type="hidden" name="require_field" id="require_field" value="friend_name,friend_email,sharing_security_code" />
		<input type="hidden" name="require_label" id="require_label" value="<?php echo Text::_(OS_FRIEND_NAME);?>,<?php echo Text::_(OS_FRIEND_EMAIL);?>,<?php echo Text::_(OS_SECURITY_CODE)?>" />
		</form>
		</div>
		<BR />
		<?php
		if($configClass['social_sharing']== 1){
		?>
		<div class="block_caption">
			<strong><?php echo Text::_(OS_SHARE_PROPERTY_TO_SOCIAL)?></strong>
		</div>
		<div style="margin: 10px 10px 0 10px">
			<?php
			echo $row->share;
			?>
		</div>
		<div class="grey_line" style="margin-top: 10px;"></div>
		<?php
		}
		?>
	</div>
	<?php
	}
	$user = Factory::getUser();
	if($configClass['comment_active_comment'] == 1){
		?>
		<div class="jwts_tabbertab" title="<?php echo Text::_(OS_COMMENTS)?>">
			<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_(OS_COMMENTS)?></a></h2>
			<?php
			echo $row->comments;
			?>
			<?php
			
			if(($owner == 0) and ($can_add_cmt == 0)){
			?>
			<div class="block_caption" id="comment_form_caption">
				<strong><?php echo Text::_(OS_ADD_COMMENT)?></strong>
			</div>	
			<div class="blue_middle"><?php echo Text::_(OS_FIELDS_MARKED);?> <span class="red">*</span> <?php echo Text::_(OS_ARE_REQUIRED);?></div>
			<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submitcomment&Itemid=<?php echo $itemid;?>" name="commentForm" id="commentForm">
			<table class="sTable">
				<?php
				if($configClass['show_rating'] == 1){
				?>
				<tr>
					<td style="width: 20%;">
						<span class="grey_small"><?php echo Text::_(OS_RATING);?>
					</td>
					<td style="font-weight:bold;font-size:11px;">
						<i>Worst
						&nbsp;
						<?php
						for($i=1;$i<=5;$i++){
							if($i==3){
								$checked = "checked";
							}else{
								$checked = "";
							}
							?>
							<input type="radio" name="rating" id="rating<?php echo $i?>" value="<?php echo $i?>" <?php echo $checked?> />
							<?php
						}
						?>
						&nbsp;&nbsp;Best</i>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td style="width: 20%;">
						<span class="grey_small"><?php echo Text::_(OS_AUTHOR);?> <span class="red">*</span></span>
					</td>
					<td>
						<input class="input-large" type="text" id="comment_author" name="comment_author" maxlength="30"  />
					</td>
				</tr>
				<tr>
					<td>
						<span class="grey_small"><?php echo Text::_(OS_TITLE);?> <span class="red">*</span></span>
					</td>
					<td>
						<input class="input-large" type="text" id="comment_title" name="comment_title" size="40" />
					</td>
				</tr>
				<tr>
					<td>
						<span class="grey_small"><?php echo Text::_(OS_MESSAGE);?> <span class="red">*</span></span>
					</td>
					<td>
						<textarea class="inputbox" id="comment_message" name="comment_message" rows="6" cols="50" class="inputbox"></textarea>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td align="left" class="grey_small">
						<input id="comment_message_counter" class="counter" type="text" readonly="" size="3" maxlength="3"/>
						<span style="float:left;"><?php echo Text::_(OS_CHARACTERS_LEFT)?></span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="grey_small"><?php echo Text::_(OS_SECURITY_CODE)?> <span class="red">*</span></span>
					</td>
					<td>
						<table>
							<tr>
								<td valign="bottom">
									<img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>" /> 
									<!--<img src="<?php echo Uri::root()?>index.php?option=com_ospropery&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>"> -->
								</td>
								<td valign="bottom">
									<span class="grey_small" style="line-height:16px;"><?php echo Text::_(OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW)?></span><br />
									<input type="text" class="input-mini" id="comment_security_code" name="comment_security_code" maxlength="5" style="width: 50px; margin: 0;" />
								</td>
							</tr>
						</table>
						<div class="clear"></div>		
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:10px;">
						<input onclick="javascript:submitForm('commentForm')" style="margin: 0; width: 100px;" class="btn btn-warning" type="button" name="finish" value="<?php echo Text::_(OS_ADD)?>" />
						<span id="comment_loading" class="reg_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
					</td>	
				</tr>
			</table>
			<?php
			}
			?>
			<input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" value="property_submitcomment" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid')?>" />
			<input type="hidden" name="require_field" id="require_field" value="comment_author,comment_title,comment_message,comment_security_code" />
			<input type="hidden" name="require_label" id="require_label" value="<?php echo Text::_(OS_AUTHOR);?>,<?php echo Text::_(OS_TITLE);?>,<?php echo Text::_(OS_MESSAGE);?>,<?php echo Text::_(OS_SECURITY_CODE)?>" />
			<script type="text/javascript">
				var comment_textcounter = new textcounter({
					textarea: 'comment_message',
					min: 0,
					max: <?php echo $configClass['max_character'];?>
				});
				comment_textcounter.init();
			</script>
			<br />
			</form>
		</div>
		<?php
	}
	?>
	<?php
	if($row->pro_video != ""){
	?>
	<div class="jwts_tabbertab" title="<?php echo Text::_(OS_VIDEO)?>">
		<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_(OS_VIDEO)?></a></h2>
		<?php
		echo stripslashes($row->pro_video);
		?>
		<br />

	</div>
	<?php
	}
	
	?>
	<div class="jwts_tabbertab" title="<?php echo Text::_('OS_REQUEST_MORE_INFOR')?>">
		<h2><a href="javascript:void(null);" name="advtab"><?php echo Text::_(OS_REQUEST_MORE_INFOR)?></a></h2>
		<?php
		$user = Factory::getUser();
		?>	
		<div class="block_caption">
			<strong><?php echo Text::_(OS_REQUEST_MORE_INFOR)?></strong>
		</div>	
		<div style="margin: 10px 10px 0 10px">
		<div class="blue_middle"><?php echo Text::_(OS_FIELDS_MARKED);?> <span class="red">*</span> <?php echo Text::_(OS_ARE_REQUIRED);?></div>
		<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_requestmoredetails&Itemid=<?php echo $itemid?>" name="requestdetails_form" id="requestdetails_form">
		<table class="sTable">
		<tr>
			<td style="width: 20%;">
				<span class="grey_small"><?php echo Text::_('OS_SUBJECT');?> <span class="red">*</span></span>
			</td>
			<td>
				<select name='subject' id='subject' class='input-large' onchange="javascript:updateRequestForm(this.value)">
					<option value='1'><?php echo Text::_('OS_REQUEST_1')?></option>
					<option value='2'><?php echo Text::_('OS_REQUEST_2')?></option>
					<option value='3'><?php echo Text::_('OS_REQUEST_3')?></option>
					<option value='4'><?php echo Text::_('OS_REQUEST_4')?></option>
					<option value='5'><?php echo Text::_('OS_REQUEST_5')?></option>
					<option value='6'><?php echo Text::_('OS_REQUEST_6')?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<span class="grey_small"><?php echo Text::_(OS_MESSAGE);?> <span class="red">*</span></span> 
			</td>
			<td>
				<textarea class="input-large" id="requestmessage" name="requestmessage" rows="6" cols="40" ><?php echo Text::_('OS_REQUEST_MSG1')?> <?php echo ($row->ref != "")? $row->ref.", ":""?><?php echo $row->pro_name?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_YOUR_NAME);?> <span class="red">*</span></span>
			</td>
			<td>
				<input class="input-large" type="text" id="requestyour_name" name="requestyour_name" size="30" maxlength="30" value="<?php echo $user->name?>" />
			</td>
	
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_YOUR_EMAIL);?> <span class="red">*</span></span>
			</td>
			<td>
				<input class="input-large" type="text" id="requestyour_email" name="requestyour_email" size="30" maxlength="30" value="<?php echo $user->email;?>"/>
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_('OS_PHONE');?></span>
			</td>
			<td>
				<input class="input-large" type="text" id="your_phone" name="your_phone" maxlength="30"  />
			</td>
		</tr>
		<tr>
			<td>
				<span class="grey_small"><?php echo Text::_(OS_SECURITY_CODE)?> <span class="red">*</span></span>
			</td>
			<td>
				<table>
					<tr>
						<td valign="bottom">
							<img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>" /> 
							<!--<img src="<?php echo Uri::root()?>index.php?option=com_ospropery&task=property_captcha&ResultStr=<?php echo $row->ResultStr?>"> -->
						</td>
						<td valign="bottom">
							<span class="grey_small" style="line-height:16px;"><?php echo Text::_(OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW)?></span><br />
							<input type="text" class="input-large" id="request_security_code" name="request_security_code" maxlength="5" style="width: 50px; margin: 0;"  />
						</td>
					</tr>
				</table>
				<div class="clear"></div>		
			</td>
		</tr>
		<tr>
			<td style="padding-top:10px;" colspan="2">
				<input class="btn btn-info" type="button" id="requestbutton" name="requestbutton" value="<?php echo Text::_("OS_REQUEST_BUTTON1")?>" onclick="javascript:submitForm('requestdetails_form');"/>
				<span class="reg_loading" id="tf_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			</td>
		</tr>
		</table>
		<input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $row->ResultStr?>" />
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_requestmoredetails" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid')?>" />
		<input type="hidden" name="require_field" id="require_field" value="requestmessage,requestyour_name,requestyour_email,request_security_code" />
		<input type="hidden" name="require_label" id="require_label" value="<?php echo Text::_('OS_REQUEST_DETAILS');?>,<?php echo Text::_('OS_YOUR_NAME');?>,<?php echo Text::_('OS_YOUR_EMAIL');?>,<?php echo Text::_(OS_SECURITY_CODE)?>" />
		</form>
		</div>
		<script type="text/javascript">
		function updateRequestForm(subject){
			var message = document.getElementById('requestmessage');
			var requestbutton = document.getElementById('requestbutton');
			if(subject == 1){
				message.value = "<?php echo Text::_('OS_REQUEST_MSG1')?> <?php echo $row->pro_name?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON1')?>";
			}else if(subject == 2){
				message.value = "<?php printf(Text::_('OS_REQUEST_MSG2'),$row->pro_name);?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON2')?>";
			}else if(subject == 3){
				message.value = "<?php echo Text::_('OS_REQUEST_MSG3')?> <?php echo $row->pro_name?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON3')?>";
			}else if(subject == 4){
				message.value = "<?php echo Text::_('OS_REQUEST_MSG4')?> <?php echo $row->pro_name?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON4')?>";
			}else if(subject == 5){
				message.value = "<?php echo Text::_('OS_REQUEST_MSG5')?> <?php echo $row->pro_name?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON5')?>";
			}else if(subject == 6){
				message.value = "<?php echo Text::_('OS_REQUEST_MSG6')?> <?php echo $row->pro_name?>";
				requestbutton.value = "<?php echo Text::_('OS_REQUEST_BUTTON6')?>";
			}
		}
		</script>
		<div class="grey_line" style="margin-top: 10px;"></div>
	</div>
	<?php
	if(file_exists(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."oscalendar.php")){
		if($configClass['integrate_oscalendar'] == 1){
			require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.php");
			require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.html.php");
			$otherlanguage =& Factory::getLanguage();
			$otherlanguage->load( 'com_oscalendar', JPATH_SITE );
			OsCalendarDefault::calendarForm($row->id);
		}
	}
	?>
</div>
<?php
if($configClass['relate_properties'] == 1){
	echo $row->relate;
}

?>
<div class="jwts_clr"></div><br />
