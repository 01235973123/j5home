<?php

/*------------------------------------------------------------------------
# details.html.tpl.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
$db = Factory::getDbo();
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/".$themename."/style/style.css");
$extrafieldncolumns = $params->get('extrafieldncolumns',3);
$show_request = $params->get('show_request_more_details','top');
$show_location = $params->get('show_location','1');
?>
<style>
#main ul{
	margin:0px;
}
</style>
<div id="notice" style="display:none;">
	
</div>
<?php
if(count($topPlugin) > 0){
	for($i=0;$i<count($topPlugin);$i++){
		echo $topPlugin[$i];
	}
}
?>
<div class="row-fluid" id="propertydetails">
	<div class="name-property clearfix center">
        <!--------- title -------->
        <h1>
        <?php
        if(($row->ref != "") and ($configClass['show_ref'] == 1)){
        	?>
        	<span color="orange">
        		<?php echo $row->ref?>
        	</span>
        	-
        	<?php
        }
        ?>
        <?php echo $row->pro_name?>
        <?php
		if($row->isFeatured == 1){
			?>
			<span class="featuredpropertydetails"><?php echo Text::_('OS_FEATURED');?></span>
			<?php
		}
		if(($configClass['active_market_status'] == 1)&&($row->isSold > 0)){
			?>
			<span class="marketstatuspropertydetails"><?php echo OSPHelper::returnMarketStatus($row->isSold);?></span>
			<?php
		}
		$created_on = $row->created;
		$modified_on = $row->modified;
		$created_on = strtotime($created_on);
		$modified_on = strtotime($modified_on);
		if($created_on > time() - 3*24*3600){ //new
			if($configClass['show_just_add_icon'] == 1){
				?>
				<span class="justaddedpropertydetails"><?php echo Text::_('OS_JUSTADDED');?></span>
				<?php
			}
		}elseif($modified_on > time() - 2*24*3600){
			if($configClass['show_just_update_icon'] == 1){
				?>
				<span class="justupdatedpropertydetails"><?php echo Text::_('OS_JUSTUPDATED');?></span>
				<?php
			}
		}
		if($configClass['enable_report'] == 1){
			OSPHelperJquery::colorbox('a.reportmodal');
		?>
			<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&item_type=0&task=property_reportForm&id=<?php echo $row->id?>" class="reportmodal reportlink" rel="{handler: 'iframe', size: {x: 350, y: 600}}" title="<?php echo Text::_('OS_REPORT_LISTING');?>">
				<span class="reportitem">
					<?php echo Text::_('OS_REPORT');?>
				</span>
			</a>
		<?php
		}
		?>
        </h1>
        <!------------- end title --------------->
        <!----- location ----->
        <div class="subtitle">
        	<?php echo $row->subtitle; ?>
        	
        </div>
        <!------ end location ----->
    </div>
</div>

<!--- wrap content -->

<div class="lightGrad detailsView clearfix">
	<div class="row-fluid">
		<!-- content -->
		<?php
		if($show_request == "top"){
			$span = "8";
		}else{
			$span = "12";
		}
		?>
		<div class="span<?php echo $span;?>">
	    	<!-- tab1 -->
	        <div class="row-fluid">    
	            <div class="span6">
	            	<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/jquery.bxslider.js"></script>
	            	<div>
		   				<div id="slides">
		   					<?php
		   					if(count($photos) > 0){
		   					?>
			   				<ul class="bxslider padding0 margin0">
				                <?php
				                for($i=0;$i<count($photos);$i++){
				                	if($photos[$i]->image != ""){
				                		if(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$photos[$i]->image){
				                			?>
				                			<li class="propertyinfoli"><a class="osmodal" href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/<?php echo $photos[$i]->image?>"><img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/medium/<?php echo $photos[$i]->image?>" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>"/></a></li>
				                			<?php
				                		}else{
				                			?>
				                			<li class="propertyinfoli"><img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>"/></li>
				                			<?php
				                		}
				                	}else{
				                		?>
				                		<li class="propertyinfoli"><img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>"/></li>
				                		<?php
				                	}
				                }
				                ?>
			                </ul>
			                <script>
						    jQuery(document).ready(function(){
								jQuery('.bxslider').bxSlider({
								  pagerCustom: '#bx-pager',
								  mode: 'fade',
								  captions: true
								});
								  });
						   </script>
						   <?php
		   					}else{
							   	?>
							   	<ul class="bxslider margin0 padding0">
							   		<li class="propertyinfoli"><img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" alt="" title=""/></li>
							   	</ul>
							   	<?php
						   }
						   ?>
		               </div>
		            </div>
	            </div>
	            <div class="span6">
	            	<div class="descriptionWrap">
	            		<span>
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
								<div class="clearfix"></div>
								<?php
							}
							?>
						</span>
						<div class="detailsView-price"> <?php echo $row->price1;?> </div>
	                    <ul class="attribute-list">
	                    	<?php
	                    	if($configClass['show_feature_group'] == 1){
		                    	if(($row->bed_room > 0) and ($configClass['use_bedrooms'] == 1)){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_BEDS')?>: </strong><span><?php echo $row->bed_room?></span></li>
		                        <?php
		                    	}
		                        ?>
		                        <?php
		                    	if(($row->bath_room > 0) and ($configClass['use_bathrooms'] == 1)){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_BATHS')?>: </strong><span><?php echo OSPHelper::showBath($row->bath_room);?></span></li>
		                        <?php
		                    	}
		                        ?>
		                        <?php
		                    	if(($row->square_feet > 0) and ($configClass['use_squarefeet'] == 1)){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo OSPHelper::showSquareLabels();?>: </strong><span><?php echo OSPHelper::showSquare($row->square_feet);?></span></li>
		                        <?php
		                    	}
		                        ?>
		                        <?php
		                    	if(($row->lot_size > 0) and ($configClass['use_squarefeet'] == 1)){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_LOT_SIZE');?>: </strong><span><?php echo OSPHelper::showLotsize($row->lot_size);?> <?php echo OSPHelper::showSquareSymbol();?></span></li>
		                        <?php
		                    	}
		                        ?>
		                        <?php
		                    	if(($configClass['use_nfloors'] == 1) and ($row->number_of_floors != "")){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_FLOORS')?>: </strong><span><?php echo $row->number_of_floors;?></span></li>
		                        <?php
		                    	}
		                        ?>
								<?php
		                    	if(($configClass['use_rooms'] == 1) and ($row->rooms > 0)){
		                    	?>
		                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_ROOMS')?>: </strong><span><?php echo $row->rooms;?></span></li>
		                        <?php
		                    	}
		                        ?>
		                        <?php
								if($configClass['use_parking'] == 1){
								?>
								<li class="propertyinfoli"><strong><?php echo Text::_('OS_PARKING')?>: </strong><span><?php echo $row->parking;?></span></li>
		                        <?php
								}
	                    	}
	                        ?>
	                        <?php 
							if($configClass['listing_show_view'] == 1){
							?>
	                        <li class="propertyinfoli"><strong><?php echo Text::_('OS_TOTAL_VIEWING')?>: </strong><span><?php echo $row->hits?></span></li>
	                        <?php 
							} 
							if($configClass['show_rating'] == 1){
							?>
	                        <li class="propertyinfoli"><span><?php echo $row->ratingvalue?></span></li>
	                        <?php
							}
	                        ?>
	                    </ul>
	                    <ul class="attribute-list">
	                    	<?php
	                    	if(($row->ref != "")  and ($configClass['show_ref'] == 1)){
	                    	?>
	                        <li><strong>Ref #: </strong><span><?php echo $row->ref;?></span></li>
	                        <?php } ?>
	                        <li><strong><?php echo Text::_('OS_TYPE')?>: </strong><span><?php echo $row->type_name?></span></li>
	                        <li><strong><?php echo Text::_('OS_CATEGORY')?>: </strong><span><?php echo $row->category_name?></span></li>
	                    </ul>
	                    <div class="clearfix"></div>
	                    <?php
			        	if($configClass['show_agent_details'] == 1){
			        	?>
	                    <div class="listing-agent mediaBox">
	                        <div class="photoF media">
	                            <div class="mat">
	                            	<?php echo $row->agentphoto;?>
	                            </div>
	                        </div>
	                        <div class="details">
	                            <ul>
	                                <li class="propertyinfoli">
	                                	<a href="<?php echo Route::_("index.php?option=com_osproperty&task=agent_info&id=".$row->agent_id."&Itemid=".OSPRoute::getAgentItemid());?>"><?php echo $row->agent_name;?></a>
	                            	</li>
	                            	<?php
	                               if(($row->agent_phone != "") and ($configs[46]->fieldvalue == 1)){
									?>
									<li class="propertyinfoli">
										<?php echo $row->agent_phone;?>
									</li>
									<?php
									}    
									if(($row->agent_mobile != "") and ($configs[45]->fieldvalue == 1)){
									?>
									<li class="propertyinfoli">
										<?php echo $row->agent_mobile;?>
									</li>
									<?php
									}
									?>                                              
	                          	</ul>
	                        </div>
	                    </div>
	                    <?php } ?>
	              	</div>
	            </div>                    
	        </div>
	        <div class="row-fluid clearfix">
				<strong><?php echo Text::_('OS_DESCRIPTION')?>: </strong><br />
				<span class="">
					<?php echo stripslashes($row->pro_small_desc);?>
				</span>
			</div>
	    </div>
	    <!-- end content -->
	    <!-- sidebar -->
	    <?php
            if(($configClass['show_request_more_details'] == 1) and ($show_request == "top")){
        ?>
	    <div class="span4 noleftmargin">
	    	<!-- request -->
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<strong>
					<?php echo Text::_('OS_REQUEST_MORE_INFOR')?>
				</strong>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<?php HelperOspropertyCommon::requestMoreDetailsTop($row,$itemid,'input-large'); ?>
			</div>
	        <!-- end request -->
	    </div>
	    <?php
        }
        ?>
    <!-- end sidebar -->
    </div>
</div>

<div class="detailsBar clearfix">
	<div class="row-fluid">
		<div class="span12">
			<ul class="listingActions-list">
				<?php
				$user = Factory::getUser();
				
				if(HelperOspropertyCommon::isAgent()){
					$my_agent_id = HelperOspropertyCommon::getAgentID();
					if($my_agent_id == $row->agent_id){
						$link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
						?>
						 <li class="propertyinfoli">
							<a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>" class="edit has icon s_16">
								<?php echo Text::_('OS_EDIT_PROPERTY')?>
							</a>
						</li>
						<?php
					}
				}
				if(($configClass['show_getdirection'] == 1) and ($row->show_address == 1)){
				?>
				<li class="propertyinfoli">
					<a href="<?php echo Route::_("index.php?option=com_osproperty&task=direction_map&id=".$row->id)?>" title="<?php echo Text::_('OS_GET_DIRECTIONS')?>" class="direction has icon s_16">
					<?php echo Text::_('OS_GET_DIRECTIONS')?>
					</a>
				</li>
				<?php
				}
				if($configClass['show_compare_task'] == 1){
				?>
				<li class="propertyinfoli">
					<span id="compare<?php echo $row->id;?>">
					<?php
					if(! OSPHelper::isInCompareList($row->id)) {
						$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_COMPARE_LIST');
						$msg = str_replace("'","\'",$msg);
						?>
						<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme2','details')" href="javascript:void(0)" class="compare has icon s_16">
							<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>
						</a>
					<?php
					}else{
						$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
						$msg = str_replace("'","\'",$msg);
						?>
						<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme2','details')" href="javascript:void(0)" class="compare has icon s_16">
							<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>
						</a>
					<?php
					}
					?>
					</span>
				</li>
				<?php
				}
				if(($configClass['property_save_to_favories'] == 1) and ($user->id > 0)){
					
					if($inFav == 0){
						?>
		                <li class="propertyinfoli">
							<span id="fav<?php echo $row->id;?>">
								<?php
								$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
								$msg = str_replace("'","\'",$msg);
								?>
								<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','details')" class="_saveListingLink save has icon s_16">
									<?php echo Text::_('OS_ADD_TO_FAVORITES');?>
								</a>
							</span>
		                </li class="propertyinfoli">
		                <?php
					}else{
						?>
						<li class="propertyinfoli">
							<span id="fav<?php echo $row->id;?>">
								<?php
								$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
								$msg = str_replace("'","\'",$msg);
								?>
								<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','details')" href="javascript:void(0)" class="_saveListingLink save has icon s_16">
									<?php echo Text::_('OS_REMOVE_FAVORITES');?>
								</a>
							</span>
		                </li class="propertyinfoli">
						<?php 
					}
				}
				if($configClass['property_pdf_layout'] == 1){
				?>
				<li class="propertyinfoli">
					<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_pdf&id=<?php echo $row->id?>" title="<?php echo Text::_('OS_EXPORT_PDF')?>"  rel="nofollow" target="_blank" class="_saveListingLink pdf has icon s_16">
					PDF
					</a>
				</li>
				<?php
				}
				if($configClass['property_show_print'] == 1){
				?>
				<li class="propertyinfoli">
					<a target="_blank" href="<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&no_html=1&task=property_print&id=<?php echo $row->id?>" class="print has icon s_16">
                    <?php echo Text::_('OS_PRINT_THIS_PAGE')?>
                    </a>
                </li>
                <?php
				}
				if($row->panorama != "") {
					?>
					<li class="propertyinfoli">
						<i class="osicon-picture" style="color:#e4730f;"></i>
						<a href="<?php echo Uri::root(); ?>index.php?option=com_osproperty&task=property_showpano&id=<?php echo $row->id ?>&tmpl=component"
						   class="osmodal" rel="{handler: 'iframe', size: {x: 650, y: 420}}">
							<?php echo Text::_('OS_PANORAMA') ?>
						</a>
					</li>
					<?php
				}
				if($configClass['social_sharing']== 1){
					
				$url = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id");
				$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$url;
				?>
                <li class="propertyinfoli">
                    <a href="http://www.facebook.com/share.php?u=<?php echo $url;?>" target="_blank" class="facebookic has icon s_16" title="<?php echo Text::_('OS_ASK_YOUR_FACEBOOK_FRIENDS');?>" id="link2Listing" rel="canonical"><?php echo Text::_('OS_SHARE')?></a>
                </li>
                 <li class="propertyinfoli">
                    <a href="https://twitter.com/intent/tweet?original_referer=<?php echo $url;?>&tw_p=tweetbutton&url=<?php echo $url;?>" target="_blank" class="twitteric has icon s_16" title="<?php echo Text::_('OS_ASK_YOUR_TWITTER_FRIENDS');?>" id="link2Listing" rel="canonical"><?php echo Text::_('OS_TWEET')?></a>
                </li>
                <?php
				}
                ?>
			</ul> 
		</div>
	</div>
</div>
<!-- end -->

<?php
if($row->pro_full_desc != ""){
?>
<div class="detailsBar clearfix">
	<div class="row-fluid">
		<div class="span12">
			<div class="shell">
		    	<fieldset><legend><span><?php echo Text::_('OS_ABOUT_PROPERTY')?></span></legend></fieldset>
		    	<?php
				if($configClass['use_open_house'] == 1){
		    		?>
		    		<div class="floatright">
		    			<?php echo $row->open_hours;?>
		    		</div>
		    		<?php 
		    	}
		    	$row->pro_full_desc =  HTMLHelper::_('content.prepare', $row->pro_full_desc);
				echo stripslashes($row->pro_full_desc);
		    	?>
	    	</div>
		</div>
	</div>
</div>
<?php
}
?>
<?php
if(count($middlePlugin) > 0){
	for($i=0;$i<count($middlePlugin);$i++){
		echo $middlePlugin[$i];
	}
}
?>
<!-- description list -->
<?php
$fieldok = 0;
$row->extra_field_groups = (array) $row->extra_field_groups;
if(count($row->extra_field_groups) > 0){
	$extra_field_groups = $row->extra_field_groups;
	for($i=0;$i<count($extra_field_groups);$i++){
		$group = $extra_field_groups[$i];
		$group_name = $group->group_name;
		$fields = (array)$group->fields;
		if(count($fields)> 0){
			$fieldok = 1;
		}
	}
}
if(($configClass['show_amenity_group'] == 1) or ($fieldok == 1) or ($configClass['show_neighborhood_group'] == 1) or ($row->pro_pdf != "") or ($row->pro_pdf_file != "") or (count((array)$tagArr) > 0)){
?>
<div class="shell">
    <fieldset><legend><span><?php echo Text::_('OS_FEATURES')?></span></legend></fieldset>
    <div class="listing-features">
       	<div class="row-fluid">
			<div class="span12">
				<?php
				echo $row->core_fields;
                if(($configClass['show_feature_group'] == 1) and ($row->amens_str1 != "")){
        		?>
                <!--<li class="zebra"><strong><?php echo Text::_('OS_AMENITIES')?></strong></li> -->
                <h4>
                	<?php echo Text::_('OS_AMENITIES')?>
                </h4>
                <div class="clearfix"></div>
               	<div class="row-fluid">
               		<?php echo $row->amens_str1;?>
               	</div>
                <?php
        		}
        		?>
        	</div>
        	<div class="clearfix"></div>
        	<?php
        	if(($configClass['show_neighborhood_group'] == 1) and ($row->neighborhood != "")){
        	?>
            <h4>
            	<?php echo Text::_('OS_NEIGHBORHOOD')?>
            </h4>
            <div class="clearfix"></div>
           	<div class="row-fluid">
            <?php 
            echo $row->neighborhood;
            ?>
           	</div>
	        <div class="clearfix"></div>
	        <?php } ?>
    		<?php
            if(count((array)$row->extra_field_groups) > 0){
				if($extrafieldncolumns == 2){
					$span = "span6";
					$jump = 2;
				}else{
					$span = "span4";
					$jump = 3;
				}
				$extra_field_groups = (array)$row->extra_field_groups;
				for($i=0;$i<count($extra_field_groups);$i++){
					$group = $extra_field_groups[$i];
					$group_name = $group->group_name;
					$fields = (array)$group->fields;
					if(count($fields)> 0){
					?>
					<div class="row-fluid">
						<h4>
							<?php echo $group_name;?>
						</h4>
					</div>
					<div class="row-fluid">
						<?php
						$k = 0;
						for($j=0;$j<count($fields);$j++){
							$field = $fields[$j];
							if($field->field_type != "textarea"){
								$k++;
								?>
								<div class="<?php echo $span; ?>">
									<i class="osicon-ok"></i>&nbsp;
									<?php
									if(($field->displaytitle == 1) or ($field->displaytitle == 2)){
										?>
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
									}
									?>
									<?php
									if($field->displaytitle == 1){
										?>
										:&nbsp;
									<?php } ?>
									<?php if(($field->displaytitle == 1) or ($field->displaytitle == 3)){?>
										<?php echo $field->value;?> <?php } ?>
								</div>
								<?php
								if($k == $jump){
									?>
									</div><div class='row-fluid minheight0'>
									<?php
									$k = 0;
								}
							}
						}
						?>
					</div>
					<?php
						for($j=0;$j<count($fields);$j++) {
							$field = $fields[$j];
							if ($field->field_type == "textarea") {
								?>
								<div class="row-fluid">
									<div class="span12">
										<?php
										if (($field->displaytitle == 1) or ($field->displaytitle == 2)) {
											?>
											<i class="osicon-ok"></i>&nbsp;
											<?php
											if ($field->field_description != "") {
												?>
												<span class="editlinktip hasTip"
													  title="<?php echo $field->field_label;?>::<?php echo $field->field_description?>">
													<strong><?php echo $field->field_label;?></strong>
												</span>
												<BR/>
											<?php
											} else {
												?>
												<strong><?php echo $field->field_label;?></strong>
											<?php
											}
										}
										?>
										<?php if (($field->displaytitle == 1) or ($field->displaytitle == 3)) { ?>
											<?php echo $field->value; ?>
										<?php } ?>
									</div>
								</div>
							<?php
							}
						}
					}
				}
			}

			if($row->pro_pdf != ""){
            ?>
            <div class="span12 noleftmargin">
            	<strong><?php echo Text::_('OS_PROPERTY_DOCUMENT')?></strong>: 
            	<a href="<?php echo $row->pro_pdf?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank">
					<?php echo $row->pro_pdf?>
				</a>
            </div>
            <div class="clearfix"></div>
            <?php
			}
			if($row->pro_pdf_file != "")
			{
				if(file_exists(JPATH_ROOT.'/media/com_osproperty/document/'.$row->pro_pdf_file))
				{
					$fileUrl = Uri::root().'media/com_osproperty/document/'.$row->pro_pdf_file;
				}
				else
				{
					$fileUrl = Uri::root().'components/com_osproperty/document/'.$row->pro_pdf_file;
				}
				?>
				<div class="span12 noleftmargin">
					<strong><?php echo Text::_('OS_PROPERTY_DOCUMENT')?></strong>: 
					<a href="<?php echo $fileUrl; ?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank">
						<img src="<?php echo Uri::root()."components/com_osproperty/images/assets"; ?>/pdf.png" />
					</a>
				</div>
				<div class="clearfix"></div>
            <?php
			}
			if(count((array)$tagArr) > 0){
            ?>
            <div class="span12 noleftmargin">
            	<h4><?php echo Text::_('OS_TAGS')?></h4>
            	<?php echo implode(" ",$tagArr);?>
            </div>
            <div class="clearfix"></div>
            <?php
			}
            ?>
        </div>
	</div>
</div>
<?php 
} 
if(($configClass['goole_use_map'] == 1) and ($row->lat_add != "") and ($row->long_add != "")){

$address = OSPHelper::generateAddress($row);
?>
<div class="row-fluid">
	<div class="span12">
		<div class="shell">
	    	<fieldset><legend><span><?php echo Text::_('OS_LOCATION')?></span></legend></fieldset>
	    	<?php
            if($configClass['map_type'] == 1)
            {
                HelperOspropertyOpenStreetMap::loadOpenStreetMapDetails($row, $configClass, '', 1);
            }
            else
            {
                HelperOspropertyGoogleMap::loadGoogleMapDetails($row, $configClass, '', 1);
            }
			?>
		</div>
	</div>
</div>
<?php
}
?>

<!-- tabs bottom -->
<div class="detailsBar clearfix">
<div class="row-fluid">
	<div class="span12">
		<div class="shell">
	    	<fieldset><legend><span><?php echo Text::_('OS_INFORMATION')?></span></legend></fieldset>
	    	<div class="row-fluid">
				<div class="span12">
					<div class="tabs clearfix">
					    <div class="tabbable">
					        <ul class="nav nav-tabs">
					        	<?php
								$agent_div = "";
								$walkscore_div = "";
								$gallery_div = "";
								$comment_div = "";
								$video_div = "";
								$energy_div = "";
								$sharing_div = "";
								$request_div =  "";
								$education_div =  "";
								
								if($configClass['show_agent_details'] == 1){
									$agent_div = "active";
								}elseif($configClass['show_gallery_tab'] == 1){
									$gallery_div = "active";
								}elseif(($configClass['show_walkscore'] == 1) and ($configClass['ws_id'] != "")){
									$walkscore_div = "active";
								}elseif($configClass['comment_active_comment'] == 1){
									$comment_div = "active";
								}elseif($row->pro_video != ""){
									$video_div = "active";
								}elseif(($configClass['energy'] == 1) and (($row->energy > 0) or ($row->climate > 0))){
									$energy_div = "active";
								}elseif($configClass['property_mail_to_friends'] == 1){
									$sharing_div = "active";
								}elseif(($configClass['show_request_more_details'] == 1) and ($show_request == "bottom")){
									$request_div = "active";
								}elseif($configClass['integrate_education'] == 1){
									$education_div = "active";
								}elseif(($configClass['use_property_history'] == 1) and (($row->price_history != "") or ($row->tax != ""))){
									$history_div = "active";
								}


					        	if($configClass['show_agent_details'] == 1){
					        	?>
					            <li class="<?php echo $agent_div?>"><a href="#agenttab" data-toggle="tab"><?php 
								if($row->agenttype == 0){
									echo Text::_('OS_AGENT');
								}else{
									echo Text::_('OS_OWNER');
								}
								?></a></li>
					            <?php
					        	}
								?>
								<?php
								if($configClass['show_gallery_tab'] == 1){
								?>
								<li class="<?php echo $gallery_div?>"><a href="#gallery" data-toggle="tab"><?php echo Text::_('OS_GALLERY');?></a></li>
								<?php
								}
								if($configClass['show_walkscore'] == 1){
									if($configClass['ws_id'] != ""){
								?>
					            	<li class="<?php echo $walkscore_div?>"><a href="#walkscore" data-toggle="tab"><?php echo Text::_('OS_WALK_SCORE');?></a></li>
					            <?php
									}
								}
								$user = Factory::getUser();
								if($configClass['comment_active_comment'] == 1){
								?>
								<li class="<?php echo $comment_div?>"><a href="#comments" data-toggle="tab"><?php echo Text::_('OS_COMMENTS');?></a></li>
								<?php
								}
								?>
								<?php
								if($row->pro_video != ""){
								?>
								<li class="<?php echo $video_div?>"><a href="#tour" data-toggle="tab"><?php echo Text::_('OS_VIRTUAL_TOUR');?></a></li>
								<?php
								}
								?>
								<?php
								if(($configClass['energy'] == 1) and (($row->energy > 0) || ($row->climate > 0) || ($row->e_class != "") || ($row->c_class != ""))){
								?>
								<li class="<?php echo $energy_div?>"><a href="#epc" data-toggle="tab"><?php echo Text::_('OS_EPC');?></a></li>
								<?php
								}
								
								if($configClass['property_mail_to_friends'] == 1){
								?>
								<li class="<?php echo $sharing_div?>"><a href="#tellafriend" data-toggle="tab"><?php echo Text::_('OS_SHARING');?></a></li>
								<?php
								}
								
								if(($configClass['show_request_more_details'] == 1) and ($show_request == "bottom")){
								?>
								<li class="<?php echo $request_div?>"><a href="#requestmoredetailsform" data-toggle="tab"><?php echo Text::_('OS_REQUEST_MORE_INFOR');?></a></li>
								<?php
								}
								if($configClass['integrate_education'] == 1){
									?>
									<li class="<?php echo $education_div?>"><a href="#educationtab" data-toggle="tab"><?php echo Text::_('OS_EDUCATION');?></a></li>
									<?php
								}
								?>
								<?php 
								if(($configClass['use_property_history'] == 1) and (($row->price_history != "") or ($row->tax != ""))){
								?>
									<li class="<?php echo $history_div?>">
										<a href="#historytab" data-toggle="tab">
											<?php echo Text::_('OS_HISTORY_TAX');?>
										</a>
									</li>
								<?php 
								}
								?>
					        </ul>          
					    </div>
					    <div class="tab-content">
					        <!-- tab1 -->
					        <?php
					        $agent_div = "";
					        $walkscore_div = "";
					        $gallery_div = "";
					        $comment_div = "";
					        $video_div = "";
					        $energy_div = "";
					        $sharing_div = "";
					        $request_div =  "";
					        $education_div =  "";
					        $history_div = "";
					        
					        if($configClass['show_agent_details'] == 1){
					        	$agent_div = " active";
					        }elseif($configClass['show_gallery_tab'] == 1){
					        	$gallery_div = " active";
					        }elseif(($configClass['show_walkscore'] == 1) and ($configClass['ws_id'] != "")){
					        	$walkscore_div = " active";
					        }elseif($configClass['comment_active_comment'] == 1){
					        	$comment_div = " active";
					        }elseif($row->pro_video != ""){
					        	$video_div = " active";
					        }elseif(($configClass['energy'] == 1) and (($row->energy > 0) or ($row->climate > 0))){
					        	$energy_div = " active";
					        }elseif($configClass['property_mail_to_friends'] == 1){
					        	$sharing_div = " active";
					        }elseif(($configClass['show_request_more_details'] == 1) and ($show_request == "bottom")){
					        	$request_div = " active";
					        }elseif($configClass['integrate_education'] == 1){
					        	$education_div = " active";
					        }elseif(($configClass['use_property_history'] == 1) and (($row->price_history != "") or ($row->tax != ""))){
					        	$history_div = " active";
					        }
					       // echo $gallery_div;
					        //die();
				        	if($configClass['show_agent_details'] == 1){
				        	?>
						        <div class="tab-pane<?php echo $agent_div?>" id="agenttab">
						        	<?php
									echo $row->agent;
									?>
						        </div>
					        <?php
					        }
							if(($configClass['show_walkscore'] == 1) and ($configClass['ws_id'] != "")){
								if($configClass['ws_id'] != ""){
							?>
					        <div class="tab-pane<?php echo $walkscore_div?>" id="walkscore">
					        	<?php
								echo $row->ws;
								?>
					        </div>
					        <?php
								}
							}
					        ?>
					        <?php
							if($configClass['show_gallery_tab'] == 1){
							?>
					        <div class="tab-pane<?php echo $gallery_div?>" id="gallery">
					        	<?php
								HelperOspropertyCommon::slimboxGallery($row->id,$photos);
								?>
					        </div>
					        <?php
							}
					        if($configClass['comment_active_comment'] == 1){
					        ?>
					        <div class="tab-pane<?php echo $comment_div?>" id="comments">
								<?php
								echo $row->comments;
								
								if(($owner == 0) and ($can_add_cmt == 1)){
									HelperOspropertyCommon::reviewForm($row,$itemid,$configClass);
								}
								?>
							</div>
					        <?php
					        }
					        if($row->pro_video != ""){
					        ?>
					        <div class="tab-pane<?php echo $video_div?>" id="tour">
					        	<?php
								echo stripslashes($row->pro_video);
								?>
					        </div>
					        <?php
					        }
					        ?>
					        <?php
							if(($configClass['energy'] == 1) and (($row->energy > 0) || ($row->climate > 0) || ($row->e_class != "") || ($row->c_class != ""))){
							?>
							<div class="tab-pane<?php echo $energy_div?>" id="epc">
					        	<?php
								echo HelperOspropertyCommon::drawGraph($row->energy, $row->climate,$row->e_class,$row->c_class);
								?>
					        </div>
							<?php
							}
							
							if($configClass['property_mail_to_friends'] == 1){
								?>
								<div class="tab-pane<?php echo $sharing_div?>" id="tellafriend">
						        	<?php HelperOspropertyCommon::sharingForm($row,$itemid); ?>
						        </div>
								<?php
							}
							
							if(($configClass['show_request_more_details'] == 1) and ($show_request == "bottom")){
								?>
								<div class="tab-pane<?php echo $request_div?>" id="requestmoredetailsform">
                                    <?php HelperOspropertyCommon::requestMoreDetails($row,$itemid); ?>
					            </div>
					   		<?php
		            		}
		            		if($configClass['integrate_education'] == 1){
		            		?>
			            		<div class="tab-pane<?php echo $education_div?>" id="educationtab">
						        	<?php
									echo stripslashes($row->education);
									?>
						        </div>
					        <?php
		            		}
		            		if(($configClass['use_property_history'] == 1) and (($row->price_history != "") or ($row->tax != ""))){
		            			?>
		            			<div class="tab-pane<?php echo $history_div?>" id="historytab">
						        	<?php 
									if($row->price_history != ""){
										echo $row->price_history;
										echo "<BR />";
									}
									if($row->tax != ""){
										echo $row->tax;
									}
									?>
						        </div>
		            			<?php 
		            		}
							?>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	if(file_exists(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."oscalendar.php")){
		if(($configClass['integrate_oscalendar'] == 1) and (in_array($row->pro_type,explode("|",$configClass['show_date_search_in'])))){
			?>
			<div class="detailsBar clearfix">
				<div class="row-fluid">
					<div class="span12">
						<div class="shell">
						<fieldset><legend><span><?php echo Text::_('OS_AVAILABILITY')?></span></legend></fieldset>
						<?php
						require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.php");
						require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.html.php");
						$otherlanguage =& Factory::getLanguage();
						$otherlanguage->load( 'com_oscalendar', JPATH_SITE );
						OsCalendarDefault::calendarForm($row->id);
						?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
	?>
<?php
if(($configClass['relate_properties'] == 1) and ($row->relate != "")){
?>
	<div class="detailsBar clearfix">
		<div class="row-fluid">
			<div class="span12">
				<div class="shell">
			    	<fieldset><legend><span><?php echo Text::_('OS_RELATE_PROPERTY')?></span></legend></fieldset>
			    	<?php
			    	echo $row->relate;
			    	?>
		    	</div>
			</div>
		</div>
	</div>
<?php
}
?> 
<?php
if($integrateJComments == 1){
?>
	<div class="detailsBar clearfix">
		<div class="row-fluid">
			<div class="span12">
				<div class="shell">
			    	<fieldset><legend><span><?php echo Text::_('OS_JCOMMENTS')?></span></legend></fieldset>
			    	<?php
			    	$comments = JPATH_SITE . DS .'components' . DS . 'com_jcomments' . DS . 'jcomments.php';
				    if (file_exists($comments)) {
				    	require_once($comments);
				    	echo JComments::showComments($row->id, 'com_osproperty', $row->pro_name);
				    }
			    	?>
		    	</div>
			</div>
		</div>
	</div>
<?php
}
?>
<?php
if(count($bottomPlugin) > 0){
	for($i=0;$i<count($bottomPlugin);$i++){
		echo $bottomPlugin[$i];
	}
}
?>
</div>
<!------------- social ------------------>
<div class="clearfix"></div>
<?php
echo $row->social_sharing;
?>
<!----------------- end social --------------->
<!-- end tabs bottom -->

<!-- end wrap content -->
<!-- end wrap content --><input type="hidden" name="process_element" id="process_element" value="" />