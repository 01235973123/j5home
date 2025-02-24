<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<h1 class="componentheading">
		<?php
			printf(Text::_("OS_YOUR_PROPERTYS_HAS_BEEN_SAVED"),$property->pro_name);
		?>
		</h1>
		<script type="text/javascript">
		function submitForm(t){
			if(t == "property_edit"){
				document.ftForm1.submit();
			}else if(t == "agent_editprofile"){
				document.ftForm2.submit();
			}
		}
		</script>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_edit&id='.$property->id);?>" name="ftForm1" id="ftForm1" class="form-horizontal">
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" id="task" value="property_edit" />
			<input type="hidden" name="id" value="<?php echo $property->id?>" />
			<input type="hidden" name="cid[]" value="<?php echo $property->id?>" />
		</form>
		<?php
		$needs = array();
		$needs[] = "agent_editprofile";
		$needs[] = "agent_default";
		$needs[] = "aeditdetails";
		$itemid = OSPRoute::getItemid($needs);
		$itemid = OSPRoute::confirmItemidArr($itemid,$need);
		if(!OSPRoute::reCheckItemid($itemid,$needs)){
			$itemid = 9999;
		}
		?>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=agent_editprofile&Itemid='.$itemid);?>" name="ftForm2" id="ftForm2" class="form-horizontal">
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" id="task" value="agent_editprofile" />
		</form>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<div class="btn-toolbar pull-right">
					<div class="btn-group">
						<button type="button" class="btn hasTooltip btn-info" title="<?php echo Text::_('OS_CONTINUE_EDIT_PROPERTY');?>" onclick="javascript:submitForm('property_edit');">
							<i class="osicon-edit"></i> <?php echo Text::_('OS_CONTINUE_EDIT_PROPERTY');?>
						</button>
						<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_GOT0_MY_PROPERTIES');?>" onclick="javascript:submitForm('agent_editprofile');">
							<i class="osicon-home"></i> <?php echo Text::_('OS_GOT0_MY_PROPERTIES');?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		/*
		if(count($msg) > 0 && $msg[] != "")
		{
			?>
			<table width="100%" class="propertyinformationtable" style="margin-bottom:10px;">
				<tr>
					<td align="left" valign="top" class="td1">
						<ul>
						<?php
						foreach($msg as $m)
						{
							echo "<LI>".$m."</LI>";
						}
						?>
						</ul>
					</td>
				</tr>
			</table>
			<?php
		}
		*/
		?>
		<!-- show information -->
		<table width="100%" class="propertyinformationtable">
			<tr>
				<td align="left" valign="top" class="td1">
					<strong>
					<?php echo Text::_('OS_APPROVED')?>: 
					</strong>
					<?php
					echo ($property->approved==1)? Text::_('OS_YES'):Text::_('OS_NO');
					?>
					<BR />
					<strong>
					<?php echo Text::_('OS_PUBLSHED')?>: 
					</strong>
					<?php
					echo ($property->published==1)? Text::_('OS_YES'):Text::_('OS_NO');
					?>
					<?php
					if($property->approved == 1){
						?>
						<BR />
						<strong>
						<?php echo Text::_('OS_START_PUBLISHING')?>: 
						<?php echo $property->publish_up;?>
						</strong>
						<?php
						if($configClass['general_use_expiration_management']==1){
							?>
							<BR />
							<strong>
							<?php echo Text::_('OS_EXPIRED_TIME')?>: 
							<?php
							
							?>
							<?php echo HelperOspropertyCommon::loadTime($expired[0]->expired_time,'2');?>
							</strong>
							<?php
						}
					}
					
					if($property->request_featured == 1 || $property->request_featured == 2)
					{
						?>
						<BR />
						<strong>
							<?php 
							$link = "<a href='".Uri::root()."index.php?option=com_osproperty&task=property_upgrade&cid[]=$property->id&Itemid=".Factory::getApplication()->input->getInt('Itemid',0)."'>".Text::_('here')."</a>";
							printf(Text::_('OS_YOU_HAVE_CHOOSE_PROPERTY_IS_FEATURE'),$link);?>
						</strong>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
				
		<BR />
		<table width="100%" class="border0">
			<tr>
				<td width="100%">
					<?php
					//echo $pane->startPane( 'pane' );
					//echo $pane->startPanel(Text::_('OS_PROPERTY_INFORMATION'),'panel1');
					?>
					<h3><?php echo Text::_('OS_PROPERTY_INFORMATION'); ?></h3>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
							<table  width="100%" class="admintable">
								<?php
								if(($property->ref != "") and ($configClass['show_ref'] == 1)){
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('Ref #')?></td>
										<td class="thankyou_td"><?php echo $property->ref?></td>
									</tr>
									<?php
								}
								if(HelperOspropertyCommon::isCompanyAdmin()){
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('OS_AGENT')?></td>
										<td class="thankyou_td"><?php echo $property->agentname;?></td>
									</tr>
									<?php 
								}
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_PROPERTY_TITLE')?></td>
									<td class="thankyou_td"><?php echo $property->pro_name?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_CATEGORY')?></td>
									<td class="thankyou_td"><?php echo OSPHelper::getCategoryNamesOfProperty($property->id);//OSPHelper::getLanguageFieldValue($property,'category_name'); ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_PROPERTY_TYPE')?></td>
									<td class="thankyou_td"><?php echo OSPHelper::getLanguageFieldValue($property,'type_name'); ?></td>
								</tr>
								<?php
								if($property->isFeatured == 1){
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_FEATURED')?></td>
									<td class="thankyou_td"><?php echo Text::_('OS_YES');?></td>
								</tr>
								<?php
								}
								if($property->rent_time != ""){
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_RENT_TIME_FRAME')?></td>
									<td class="thankyou_td"><?php echo Text::_($property->rent_time);?></td>
								</tr>
								<?php
								}
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_PRICE')?></td>
									<td class="thankyou_td"><?php echo OSPHelper::generatePrice($property->curr,$property->price);
									?> 
									<?php
									if($property->rent_time != ""){
										echo " /".Text::_($property->rent_time);
									}
									?>
									</td>
								</tr>
								<?php
								if($row->price_text != "")
								{
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('OS_PRICE')?></td>
										<td class="thankyou_td"><?php echo OSPHelper::showPriceText(Text::_($row->price_text)); ?></td>
									</tr>
									<?php
								}elseif($property->price_call==1){
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_CALL_FOR_PRICE')?></td>
									<td class="thankyou_td"><?php echo $property->price_call? Text::_('OS_YES'):Text::_('OS_NO'); ?></td>
								</tr>
								<?php
								}
								?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_ADDRESS')?>
									</td>
									<td class="thankyou_td">
										<?php echo $property->address?>
									</td >
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_POSTCODE')?>
									</td>
									<td class="thankyou_td">
										<?php echo $property->postcode?>
									</td>
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_CITY')?>
									</td>
									<td class="thankyou_td">
										<?php //echo $property->city
										echo HelperOspropertyCommon::loadCityName($property->city);
										?>
									</td>
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_COUNTRY')?>
									</td>
									<td class="thankyou_td">
										<?php echo $property->country_name; ?>
									</td>
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_STATE')?>
									</td>
									<td class="thankyou_td">
										<div id="div_states">
										<?php echo $property->state_name; ?>
									</td>
								</tr>
								<?php
								if($property->region != ""){
								?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_REGION')?>
									</td>
									<td class="thankyou_td">
										<?php echo $property->region?>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_DESCRIPTION')?>
									</td>
									<td class="thankyou_td">
										<?php
										  echo OSPHelper::getLanguageFieldValue($property,'pro_small_desc');
										  ?>
										  <BR />
										  <?php
										  $property->pro_full_desc =  HTMLHelper::_('content.prepare', OSPHelper::getLanguageFieldValue($property,'pro_full_desc'));
										  echo stripslashes(OSPHelper::getLanguageFieldValue($property,'pro_full_desc'));
										  ?>
									</td>
								</tr>
							</table>
						</div>
					</div>
					
					<?php
					//echo $pane->endPanel();
					//echo $pane->startPanel(Text::_('OS_OTHER_INFORMATION'),'panel2');
					?>
					<!--
					<h3>
						<?php echo Text::_('OS_OTHER_INFORMATION'); ?>
					</h3>
					<table  width="100%">
						<tr>
							<td width="100%">
								<?php
								if(($property->metakey != "") || ($property->metadesc != "")){
								?>
								<fieldset>
									<legend><strong><?php echo Text :: _('OS_META_INFORMATION')?></strong></legend>
									<table  width="100%" class="admintable">
										<?php
										if($property->metakey != ""){
										?>
										<tr>
											<td class="key" class="width110px" valign="top"> <?php echo Text::_('OS_META_KEYWORDS')?></td>
											<td> <?php echo $property->metakey?> </td>
										</tr>
										<?php
										}
										?>
										<?php
										if($property->metadesc != ""){
										?>
										<tr>
											<td class="key" class="width110px" valign="top"> <?php echo Text::_('OS_META_DESCRIPTION')?></td>
											<td> <?php echo $property->metadesc?> </td>
										</tr>
										<?php
										}
										?>
									</table>
								</fieldset>	
								<?php
								}
								?>
								<fieldset>
									<legend><strong><?php echo Text :: _('OS_PROPERTY_INFORMATION'); ?></strong></legend>
									<div class="thankyou_propertyinformation">
										<?php
										echo OSPHelper::showCoreFields($property);
										?>
									</div>
								</fieldset>
								<?php
								if(count($amenities) > 0){
								?>
								<fieldset>
									<legend><strong><?php echo Text :: _('OS_CONVENIENCE'); ?></strong></legend>
									<table  width="100%" class="border1">
										<tr>
											<?php
											$j = 0;
											for($i=0;$i<count($amenities);$i++){
												if($amenities[$i]->amenities != ""){
												$j++;
												?>
												<td align="left" class="padding5 border1">
													<?php echo OSPHelper::getLanguageFieldValue($amenities[$i],'amenities');?>
												</td>
												<?php
												}
												if($j==3){
													echo "</tr><tr>";
													$j = 0;
												}
											}
											?>
										</tr>
									</table>
								</fieldset>
									<?php
								}
								if(count($groups) > 0){
									for($i=0;$i<count($groups);$i++){
										$group = $groups[$i];
										$fields = HelperOspropertyFields::getFieldsData($property->id, $group->id);
										if(count($fields) > 0){
										?>
											<fieldset>
												<legend><strong><?php echo OSPHelper::getLanguageFieldValue($group,'group_name');?></strong></legend>
												<table  width="100%" class="admintable">
												<?php
												for($j=0;$j<count($fields);$j++){
													$field = $fields[$j];
													//if(HelperOspropertyFieldsPrint::showField($field,$property->id) != ""){
													?>
													<tr>
														<td class="key width110px">
															<?php echo $field->field_label?>
														</td>
														<td>
															<?php
															echo $field->value;
															?>
														</td>
													</tr>
													<?php
													//}
												}
												?>
												</table>
											</fieldset>
											<?php
										}
									}
								}
								?>
								</div>	
							</td>
						</tr>
					</table>
					<?php
					//echo $pane->endPanel();
					//echo $pane->startPanel(Text::_('OS_DOCUMENT_PHOTOS'),'panel3');
					?>
					<h3><?php echo Text::_('OS_DOCUMENT_PHOTOS'); ?></h3>
					<table  width="100%">
						<?php
						if(($property->pro_video != "") or ($property->pro_pdf != "")){
						?>
						<tr>
							<td width="100%" valign="top" class="padding5">
								<strong>
									<?php echo Text::_('OS_DOCUMENT')?>
								</strong>
								<BR />
								<table  width="100%" class="admintable">
									<?php
									if($property->pro_video != ""){
									?>
									<tr>
										<td class="key" valign="top">
											<?php echo Text::_('OS_VIDEO_EMBED_CODE')?>
										</td>
										<td>
											<?php echo stripslashes($property->pro_video);?>
										</td>
									</tr>
									<?php
									}
									if($property->pro_pdf != ""){
									?>
									<tr>
										<td class="key" width="30%">
											<?php echo Text::_('OS_DOCUMENT_LINK')?>
										</td>
										<td>
											<?php echo $property->pro_pdf?>
										</td>
									</tr>
									<?php
									}
									?>
								</table>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td width="100%" valign="top" class="padding5">
								<strong>
									<?php echo Text::_("OS_PHOTOS")?>
								</strong>
								<?php
								if(count($photos) > 0){
									$j = 0;
									?>
									<table width="100%" class="border0">
										<tr>
										<?php
										for($i=0;$i<count($photos);$i++){
											$j++;
											$photo = $photos[$i];
											?>
											<td width="25%" class="border0" valign="top">
												<div class="padding3 border0 displayblock" id="div_<?php echo $i?>">
												<table class="admintable border0">
													<tr>
														<td class="border0">
															<?php
															if($photo->image != ""){
																?>
																<a href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $property->id?>/<?php echo $photo->image?>" class="osmodal">
																<?php
																OSPHelper::showPropertyPhoto($photo->image,'thumb',$property->id,'width:120px;','img-rounded img-polaroid','');
																?>
																</a>
																<?php
															}
															?>
														</td>
													</tr>
													<tr>
														<td class="backgroundlightgray border0">
															<?php echo $photo->image_desc?>
														</td>
													</tr>
												</table>
												</div>
											</td>
											<?php
											if($j == 4){
												echo '</tr><tr>';
												$j = 0;
											}
										}
										?>
										</tr>
									</table>
									<?php
								}
								?>
							</td>
						</tr>
					</table>
					-->
					<?php
					//echo $pane->endPane();
					?>
				</td>
			</tr>
		</table>
	</div>
</div>