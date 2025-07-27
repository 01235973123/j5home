<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
?>
<page style="font-family: freeserif;">
	<table width="660" cellpadding="10" border="0" background="#f4f2ed">
		<tr>
			<td width="400" height="35" align="left" style="margin-top:10px;text-align:left;background-color:#7eaeb3;color:white;">
				<div>
					<strong><font size="14" style='font-size:25pt;font-weight:bold;'><?php if(($row->ref != "") && ($configClass['show_ref'] == 1)){ echo "# ".$row->ref .", "; } echo strtoupper(OSPHelper::getLanguageFieldValue($row,'pro_name'));?></font></strong>
				</div>
			</td>
			
			<td width="260" align="right" valign="bottom" style="padding-top:10px;background-color:#efefef;">
				<strong>
					<font size="14" color="#7eaeb3" style='font-size:18pt;font-weight:bold;color:#7eaeb3;'><?php echo $lists['type']; ?>.</font>
					<?php
					if(($configClass['active_market_status'] == 1) && ($row->isSold > 0)){
					?>
						<font size="14" color="#dd0c0c" style='font-size:18pt;font-weight:bold;color:#dd0c0c;'><?php echo OSPHelper::returnMarketStatus($row->isSold); ?>.</font>
					<?php } ?>
					<font size="14" style='font-size:18pt;font-weight:bold;' color="#686868">
					<?php
					if($row->price_text != "")
					{
						echo " ".OSPHelper::showPriceText(OSPHelper::showPriceText(Text::_($row->price_text)));
					}
					elseif($row->price_call == 1)
					{
						echo Text::_('OS_CALL_FOR_PRICE');
					}
					else
					{
						echo OSPHelper::generatePrice($row->curr,$row->price);
						if($row->rent_time != "")
						{
							echo " /".Text::_($row->rent_time);
						}
					}
					?>
					</font>
				</strong>
			</td>
			
		</tr>
	</table>
	<?php 
	if(count($row->photo) > 0){
		$photos = $row->photo;
		$j = 0;
		?>
		<table width="660" cellpadding="5" cellspacing="10" bgcolor="#f2f7fe">
			<tr>
				<td width="420" valign="top">
					<table width="100%">
						<tr>
							<td align="center" width="450" colspan="4" cellpadding="10" style="padding:5px;">
								<!-- <img src="<?php echo Uri::root(true)?>/images/osproperty/properties/<?php echo $row->id;?>/<?php echo $photos[0]->image?>" width="450" style="width:450px; !important;"/> -->
								<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/<?php echo $photos[0]->image?>" width="450" style="width:450px; !important;"/>
							</td>
						</tr>
					</table>
				</td>
				<?php
				if(count($photos) > 1){
				?>
				<td width="240" valign="top">
					<table width="100%">
						<?php
						if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "" && $configClass['goole_aip_key'] != "" && $configClass['show_googlemap_pdf'] == 1)
						{
							$mphoto = 3;
						}
						else
						{
							$mphoto = 4;
						}
						if(count($photos) > $mphoto)
						{
							$maxphoto = $mphoto;
						}
						else
						{
							$maxphoto = count($photos);
						}
						for($i=1;$i<$maxphoto;$i++)
						{
							$j++;
							$photo = $photos[$i];
							$image = explode(".",$photo->image);
							$extension = $image[count($image)-1];
							$extension = strtolower($extension);
							if(in_array($extension,array('jpg','png','gif','jpeg'))){
								?>
								<tr>
									<td width="230" style="margin:10px;" ALIGN="CENTER">
								
										<?php
										if($photo->image != "" && file_exists(JPATH_ROOT.DS."images/osproperty/properties/".$row->id."/thumb/".$photo->image))
										{
											?>
											<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/thumb/<?php echo $photo->image?>" width="170" />
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
						}
						if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "" && $configClass['show_googlemap_pdf'] == 1)
						{

							if($configClass['map_type'] == 0 && $configClass['goole_aip_key'] != "")
							{
								$picturelink = "https://maps.googleapis.com/maps/api/staticmap?center=".$row->lat_add.",".$row->long_add."&zoom=10&scale=1&size=170x100"."&maptype=roadmap&format=jpg&visual_refresh=true&markers=size:mid%7Ccolor:0xff0000%7Clabel:1%7C".$row->lat_add.",".$row->long_add."&key=".$configClass['goole_aip_key'];
								
								$time		= time();
								$desFolder	= JPATH_ROOT.'/tmp/';
								$imageName	= 'google-map_'.$time.'.JPG';
								$imagePath	= $desFolder.$imageName;

								$imgData	= file_get_contents($picturelink);

								if ($imgData !== false) 
								{
									file_put_contents($imagePath,$imgData);
									?>
									<tr>
										<td width="230" style="margin:10px;" ALIGN="CENTER">
											<img src="<?php echo Uri::root().'tmp/'.$imageName; ?>" width="170" />
										</td>
									</tr>
									<?php
								}
							}
							else
							{
								$latitude = $row->lat_add;
								$longitude = $row->long_add;
								$zoom = 14;           

								
								$tileSize = 256;
								$worldSize = pow(2, $zoom) * $tileSize;
								$xTile = floor(($longitude + 180) / 360 * pow(2, $zoom));
								$yTile = floor((1 - log(tan(deg2rad($latitude)) + 1 / cos(deg2rad($latitude))) / pi()) / 2 * pow(2, $zoom));

								$mapUrl = "https://tile.openstreetmap.org/$zoom/$xTile/$yTile.png";
								$imgData = file_get_contents($mapUrl);

								if ($imgData !== false) 
								{
									file_put_contents($imagePath,$imgData);
									?>
									<tr>
										<td width="230" style="margin:10px;" ALIGN="CENTER">
											<img src="<?php echo Uri::root().'tmp/'.$imageName; ?>" width="170" />
										</td>
									</tr>
									<?php
								}

							}
						}
						?>
					</table>
				</td>
				<?php } ?>
			</tr>
		</table>
		<?php
	}

	?>

	<table cellpadding="5" cellspacing="5" width="660" border="0" bgcolor="#cde3fe">
		
		<tr>
			<td valign = "top" width="450" align="left">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<?php
						if($row->show_address == 1){
						?>
						<tr>
							<td width="100%" align="left">
								<font size="13"><strong><?php echo OSPHelper::generateAddress($row);?></strong></font>
							</td>
						</tr>
						<?php
						}
					?>
					
					<tr>
						<td width="100%" align="left">
							<?php
							$small_desc = OSPHelper::getLanguageFieldValue($row,'pro_small_desc');
							if($small_desc != ""){
								echo $small_desc;
								echo "<BR />";
								echo strip_tags(OSPHelper::getLanguageFieldValue($row,'pro_full_desc')); 
							}
							?>
						</td>
					</tr>
					<?php
					if($configClass['pdf_fields'] == 1 && count($groups) > 0)
					{
					?>
					<tr>
						<td width="100%" align="left">
							<BR /><BR />
							<table border="0" bgcolor="#FFF">
								<tr>
									<td>
										<strong><?php echo strtoupper(Text::_('OS_MORE_INFORMATION')); ?></strong>
										<BR />
										<?php
										for($i=0;$i<count($groups);$i++)
										{
											$group = $groups[$i];
											$group_name = $group->group_name;
											$fields = $group->fields;
											if(count($fields)> 0)
											{
												?>  
												<strong><?php echo $group_name;?></strong>
												<BR />
												<?php 
												$k = 0;
												for($j=0;$j<count($fields);$j++){
													$field = $fields[$j];
													if($field->value != ""){
													?> 
													<?php
													if(($field->displaytitle == 1) || ($field->displaytitle == 2)){
													?>
														<?php echo $field->field_label;?>
													<?php } ?>
													<?php
													if($field->displaytitle == 1){
														?>
														:
													<?php } ?>
													<?php
													if(($field->displaytitle == 1) || ($field->displaytitle == 3)){?>
													<span><?php echo $field->value;?></span> <?php } ?> &nbsp;
													<?php 
													}
												}
												?>
												<BR />
												<?php
											}
										}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					}	
					?>
					<tr>
						<td width="100%">
							<?php
							$query = $db->getQuery(true);
							$query->select("*")->from("#__osrs_property_price_history")->where("pid = '$row->id'")->order("`date` desc");
							$db->setQuery($query);
							$prices = $db->loadObjectList();
							if(($configClass['use_property_history'] == 1) && (count($prices) > 0)){ ?>
							<!-- History -->
							<BR /><BR />
							<table width="100%" border="0">
								<tr>
									<td width="100%" BGCOLOR="#7eaeb3" colspan="4">
										<strong><font color="#FFF"><?php echo Text::_('OS_PROPERTY_HISTORY');?></font></strong>
									</td>
								</tr>
								<tr>
									<th width="25%" bgcolor="#CCC">
										<?php echo Text::_('OS_DATE');?>
									</th>
									<th width="25%" bgcolor="#CCC">
										<?php echo Text::_('OS_EVENT');?>
									</th>
									<th width="25%" bgcolor="#CCC">
										<?php echo Text::_('OS_PRICE');?>
									</th>
									<th width="25%" bgcolor="#CCC">
										<?php echo Text::_('OS_SOURCE');?>
									</th>
								</tr>

								<?php
								$i = 0;
								foreach ($prices as $price){
									$i++;
									if($i % 2 == 0){
										$bgcolor = '#FFF';
									}else{
										$bgcolor = '#EFEFEF';
									}
									?>
									<tr>
										<td width="25%" BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo $price->date;?>
										</td>
										<td width="25%" BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo $price->event;?>
										</td>
										<td width="25%" BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo OSPHelper::generatePrice('',$price->price);?>
										</td>
										<td width="25%" BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo $price->source;?>
										</td>
									</tr>
									<?php 
								}
								?>
							</table>
							<?php 
							}
									
							$query = $db->getQuery(true);
							$query->select("*")->from("#__osrs_property_history_tax")->where("pid = '$row->id'")->order("`tax_year` desc");
							$db->setQuery($query);
							$taxes = $db->loadObjectList();
							if(($configClass['use_property_history'] == 1) && (count($taxes) > 0)){ ?>
							<!-- tax -->
							<BR />
							<table width="100%" border="0">
								<tr>
									<td width="100%" BGCOLOR="#7eaeb3" colspan="5">
										<strong><font color="#FFF"><?php echo Text::_('OS_PROPERTY_TAX');?></font></strong>
									</td>
								</tr>
								
								<tr>
									<th width="10%" bgcolor="#CCC">
										<?php echo Text::_('OS_YEAR');?>
									</th>
									<th width="15%" bgcolor="#CCC">
										<?php echo Text::_('OS_TAX');?>
									</th>
									<th width="15%" bgcolor="#CCC">
										<?php echo Text::_('OS_CHANGE');?>
									</th>
									<th width="30%" bgcolor="#CCC">
										<?php echo Text::_('OS_TAX_ASSESSMENT');?>
									</th>
									<th width="30%" bgcolor="#CCC">
										<?php echo Text::_('OS_TAX_ASSESSMENT_CHANGE');?>
									</th>
								</tr>

								<?php
								$i = 0;
								foreach ($taxes as $tax){
									$i++;
									if($i % 2 == 0){
										$bgcolor = '#FFF';
									}else{
										$bgcolor = '#EFEFEF';
									}
									?>
									<tr>
										<td BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo $tax->tax_year;?>
										</td>
										<td BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo OSPHelper::generatePrice('',$tax->property_tax);?>
										</td>
										<td BGCOLOR="<?php echo $bgcolor;?>">
											<?php 
											if($tax->tax_change != ""){
											?>
												<?php echo $tax->tax_change;?> %
											<?php }else { ?>
												--
											<?php } ?>
										</td>
										<td BGCOLOR="<?php echo $bgcolor;?>">
											<?php echo OSPHelper::generatePrice('',$tax->tax_assessment);?>
										</td>
										<td BGCOLOR="<?php echo $bgcolor;?>">
											<?php 
											if($tax->tax_assessment_change != ""){
											?>
												<?php echo $tax->tax_assessment_change;?> %
											<?php }else { ?>
												--
											<?php } ?>
										</td>
									</tr>
									<?php 
								}
								?>
							</table>
							<?php 
							} 
							?>
						</td>
					</tr>
					<?php

					//end field groups
					//photos
					if(OSPHelper::allowShowingProfile($row->agent->optin)){
					?>
					<tr>
						<td width="100%">
							<BR /><Br />
							<table border="0" BGCOLOR="#fff">
								<tr>
									<?php
									if($configClass['show_agent_image']== 1){
									?>
										<td width="100" VALIGN="TOP">
											<?php
											$agent_photo = $row->agent->photo;
											$agent_photo_arr = explode(".",$agent_photo);
											$ext = $agent_photo_arr[count($agent_photo_arr)-1];
											$ext = strtolower($ext);
											if ($row->agent->photo != "" && file_exists(JPATH_ROOT.'/images/osproperty/agent/'.$row->agent->photo)){
												?>
												<img src="<?php echo Uri::root()?>images/osproperty/agent/<?php echo $row->agent->photo?>" width="100"/>
												<?php
											}else{
												?>
												<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/user.jpg" style="border:1px solid #CCC;" width="100" />
												<?php
											}
											?>
										</td>
									<?php
									}
									?>
									<td width="350" VALIGN="TOP">
										<table width="100%" border="0">
											<tr>
												<td width="15%" BGCOLOR='#FFF'>
													<?php echo Text::_('OS_NAME')?>
												</td>
												<td BGCOLOR='#FFF'>
													<strong><?php echo $row->agent->name;?></strong>
												</td>
											</tr>
											<?php
											if($configClass['show_agent_address'] == 1){
											?>
											<tr>
												<td width="15%" BGCOLOR="#f3fafb" >
													<?php echo Text::_('OS_ADDRESS');?>
												</td>
												<td width="85%" BGCOLOR="#f3fafb">
													<?php 
													if($row->agent->address != ""){
														echo $row->agent->address;
														if($row->agent->city > 0){
															echo ", ";
														}
													}
													if($row->agent->city > 0){
														echo HelperOspropertyCommon::loadCityName($row->agent->city);
														if(OSPHelper::loadSateName($row->agent->state_name) != ""){
															echo ", ";
														}
													}
													if(OSPHelper::loadSateName($row->agent->state_name) != ""){
														echo OSPHelper::loadSateName($row->agent->state_name);
														if(HelperOspropertyCommon::checkCountry()){
															echo ", ";
														}
													}
													if(HelperOspropertyCommon::checkCountry()){
														echo OSPHelper::getCountryName($row->agent->country);
													}
													?>
												</td>
											</tr>
											<?php
											}
											
											if(($row->agent->phone != "") && ($configClass['show_agent_phone'] == 1)){
											?>
											<tr>
												<td width="15%" BGCOLOR='#FFF'>
													<?php echo Text::_('OS_PHONE');?>
												</td>
												<td BGCOLOR='#FFF'>
													<?php echo $row->agent->phone;?>
												</td>
											</tr>
											<?php
											}
											if(($row->agent->mobile != "")and ($configClass['show_agent_mobile'] == 1)){
											?>
											<tr>
												<td width="15%" BGCOLOR="#f3fafb">
													<?php echo Text::_('OS_MOBILE');?>
												</td>
												<td BGCOLOR="#f3fafb">
													<?php echo $row->agent->mobile;?>
												</td>
											</tr>
											<?php
											}
											?>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<?php
					}
					?>
				</table>
			</td>
			<td valign = "top" width="200">
				<table cellpadding="0" cellspacing="5" width="100%" border="0">
					<?php
					if((($configClass['use_rooms'] == 1) && ($row->rooms > 0)) || (($configClass['use_bedrooms'] == 1) && ($row->bed_room > 0)) || (($configClass['use_bathrooms'] == 1) && ($row->bath_room > 0)) || ($row->living_areas != "")){
						?>
						<tr>
							<td width="100%">
								<table border="0" width="100%">
									<tr>
										<td width="100%" BGCOLOR="#F9D192" colspan="3">
											<strong><?php echo strtoupper(Text :: _('OS_BASE_INFORMATION')); ?>:</strong>
										</td>
									</tr>
									<?php
									if(($configClass['use_rooms'] == 1) && ($row->rooms > 0)){ ?>
									<tr>
										<td width="30%" BGCOLOR="#FFFFFF">
											<?php echo Text::_('OS_ROOMS');?>
										</td>
										<td width="70%" BGCOLOR="#FFFFFF">:&nbsp;
											<?php echo $row->rooms ;?>
										</td>
									</tr>
									<?php
									}
									?>
									<?php
									if(($configClass['use_bedrooms'] == 1) && ($row->bed_room > 0)){?>
									<tr>
										<td width="30%" BGCOLOR="#EFEFEF">
											<?php echo Text::_('OS_BED');?>
										</td>
										<td width="70%" BGCOLOR="#EFEFEF">:&nbsp;
											<?php echo $row->bed_room ;?>
										</td>
									</tr>
									<?php
									}
									?>
									<?php
									if(($configClass['use_bathrooms'] == 1) && ($row->bath_room > 0)){ ?>
									<tr>
										<td width="30%" BGCOLOR="#FFFFFF">
											<?php echo Text::_('OS_BATH');?>
										</td>
										<td width="70%" BGCOLOR="#FFFFFF">:&nbsp;
											<?php echo OSPHelper::showBath($row->bath_room) ;?>
										</td>
									</tr>
									<?php
									}
									?>
									<?php
									if($row->living_areas != ""){ ?>
									<tr>
										<td width="30%" BGCOLOR="#EFEFEF">
											<?php echo Text::_('OS_LIVING_AREAS');?>
										</td>
										<td width="70%" BGCOLOR="#EFEFEF">:&nbsp;
											<?php echo $row->living_areas ;?>
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

					if($configClass['use_squarefeet'] == 1){
						$textFieldsArr = array('subdivision','land_holding_type','lot_dimensions','frontpage','depth');
						$numberFieldArr = array('total_acres','square_feet','lot_size');
						$show = 0;
						foreach($textFieldsArr as $textfield){
							if($row->{$textfield} != ""){
								$show = 1;
							}
						}
						foreach($numberFieldArr as $numfield){
							if($row->{$numfield} > 0){
								$show = 1;
							}
						}
						if($show == 1) {
							?>
							<tr>
								<td width="100%">
									<table  width="100%" border="0">
										<tr>
											<td width="100%" BGCOLOR="#F9D192" colspan="3">
												<strong><?php echo strtoupper(Text :: _('OS_LAND_INFORMATION')); ?>:</strong>
											</td>
										</tr>
										<?php
										$i = 0;
										foreach($textFieldsArr as $textfield){ 
											if($row->{$textfield} != ""){
												$i++;
												if($i % 2 == 0){
													$bgcolor = "#FFFFFF";
												}else{
													$bgcolor = "#EFEFEF";
												}
												?>
												<tr>
													<td width="30%" BGCOLOR="<?php echo $bgcolor;?>">
														<?php echo Text::_('OS_'.strtoupper($textfield));?>
													</td>
													<td width="70%" BGCOLOR="<?php echo $bgcolor;?>">:&nbsp;
														<?php echo $row->{$textfield};?>
													</td>
												</tr>
												<?php
											}
										}
										$i = 0;
										foreach($numberFieldArr as $numfield){
											if((float)$row->{$numfield} > 0){
												$i++;
												if($i % 2 == 0){
													$bgcolor = "#FFFFFF";
												}else{
													$bgcolor = "#EFEFEF";
												}
												?>
												<tr>
													<td width="30%" BGCOLOR="<?php echo $bgcolor;?>">
														<?php echo Text::_('OS_'.strtoupper($numfield));?>
													</td>
													<td width="70%" BGCOLOR="<?php echo $bgcolor;?>">:&nbsp;
														<?php echo OSPHelper::showSquare($row->{$numfield});?>
													</td>
												</tr>
												<?php
											}
										}
										?>
									</table>
								</td>
							</tr>
							<?php
						}
					}

					if($configClass['use_nfloors'] == 1){
						$textFieldsArr = array('house_style','house_construction','exterior_finish','roof','flooring');
						$numberFieldArr = array('built_on','remodeled_on','number_of_floors','floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total');
						$show = 0;
						foreach($textFieldsArr as $textfield){
							if($row->{$textfield} != ""){
								$show = 1;
							}
						}
						foreach($numberFieldArr as $numfield){
							if($row->{$numfield}  > 0){
								$show = 1;
							}
						}
						if($show == 1) {
							?>
							<tr>
								<td width="100%">
									<table  width="100%" border="0">
										<tr>
											<td width="100%" BGCOLOR="#F9D192" colspan="3">
												<strong><?php echo strtoupper(Text :: _('OS_BUILDING_INFORMATION')); ?>:</strong>
											</td>
										</tr>
										<?php
										$i = 0;
										foreach($textFieldsArr as $textfield){ 
											if($row->{$textfield} != ""){
												$i++;
												if($i % 2 == 0){
													$bgcolor = "#FFFFFF";
												}else{
													$bgcolor = "#EFEFEF";
												}
												?>
												<tr>
													<td width="50%" BGCOLOR="<?php echo $bgcolor;?>">
														<?php echo Text::_('OS_'.strtoupper($textfield));?>
													</td>
													<td width="50%" BGCOLOR="<?php echo $bgcolor;?>">:&nbsp;
														<?php echo $row->{$textfield};?>
													</td>
												</tr>
												<?php
											}
										}
										$i = 0;
										foreach($numberFieldArr as $numfield){
											if($row->{$numfield} > 0){
												$i++;
												if($i % 2 == 0){
													$bgcolor = "#FFFFFF";
												}else{
													$bgcolor = "#EFEFEF";
												}
												?>
												<tr>
													<td width="50%" BGCOLOR="<?php echo $bgcolor;?>">
														<?php echo Text::_('OS_'.strtoupper($numfield));?>
													</td>
													<td width="50%" BGCOLOR="<?php echo $bgcolor;?>">:&nbsp;
														<?php echo OSPHelper::showSquare($row->{$numfield});?>
													</td>
												</tr>
												<?php
											}
										}
										?>
									</table>
								</TD>
							</TR>
							<?php
						}
					}

					if(($configClass['show_amenity_group'] == 1) && (count($amenities) > 0)){
						?>
						<tr>
							<td width="100%">
								<table width="100%" border="0" bgcolor="#FFF">
									<tr>
										<td width="100%" BGCOLOR="#F9D192">
											<strong><?php echo strtoupper(Text :: _('OS_CONVENIENCE')); ?>:</strong>
										</td>
									</tr>
									<?php
									$optionArr = array();
									$optionArr[] = Text::_('OS_GENERAL_AMENITIES');
									$optionArr[] = Text::_('OS_ACCESSIBILITY_AMENITIES');
									$optionArr[] = Text::_('OS_APPLIANCE_AMENITIES');
									$optionArr[] = Text::_('OS_COMMUNITY_AMENITIES');
									$optionArr[] = Text::_('OS_ENERGY_SAVINGS_AMENITIES');
									$optionArr[] = Text::_('OS_EXTERIOR_AMENITIES');
									$optionArr[] = Text::_('OS_INTERIOR_AMENITIES');
									$optionArr[] = Text::_('OS_LANDSCAPE_AMENITIES');
									$optionArr[] = Text::_('OS_SECURITY_AMENITIES');
									
									for($l=0;$l<count($optionArr);$l++){
										$j = 0;
										$db->setQuery("Select a.* from #__osrs_amenities as a left join #__osrs_property_amenities as b on a.id = b.amen_id where a.category_id = '".$l."' and b.pro_id = '$row->id'");
										$amenities = $db->loadObjectList();
										if(count($amenities) > 0){
											
											echo "<tr><td BGCOLOR='#FBE1B7'>";
											?>
											<strong><?php echo $optionArr[$l];?>:</strong>
											<?php 
											for($i=0;$i<count($amenities);$i++){
												if(count($amenitylists) > 0){
													if(in_array($amenities[$i]->id,$amenitylists)){
														echo OSPHelper::getLanguageFieldValue($amenities[$i],'amenities').",";
													}
												}
											}
											echo "</td></tr>";
										}
									}
									?>
								</table>
							</td>
						</tr>
						<?php
					}

					if($configClass['show_neighborhood_group'] == 1){
						$db->setQuery("Select count(id) from #__osrs_neighborhood where pid = '$row->id'");
						$count = $db->loadResult();
						if($count > 0){
							$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
								." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
								." where a.pid = '$row->id'";
							$db->setQuery($query);
							$neighbors = $db->loadObjectList();
							?>
							<tr>
								<td width="100%">
									<table  width="100%" border="0" BGCOLOR="#FFF">
										<tr>
											<td width="100%" BGCOLOR="#F9D192" colspan="2">
												<strong><?php echo strtoupper(Text :: _('OS_NEIGHBORHOOD')); ?>:</strong>
											</td>
										</tr>
										<?php
										for($i=0;$i<count($neighbors);$i++){
											$neighbor = $neighbors[$i];
											
											if($i % 2==0){
												$bgcolor = "#EFEFEF";
											}else{
												$bgcolor = "#FFF";
											}
											?>
											<tr>
												<td width="35%" BGCOLOR="<?php echo $bgcolor;?>">
													<?php
													echo Text::_($neighbor->neighborhood);
													?>
												</td>
												<td width="65%" BGCOLOR="<?php echo $bgcolor;?>">:&nbsp;
													<?php echo $neighbor->mins?> <?php echo Text::_('OS_MINS')?> <?php echo Text::_('OS_BY');?>
													<?php
													switch ($neighbor->traffic_type){
														case "1":
															echo Text::_('OS_WALK');
														break;
														case "2":
															echo Text::_('OS_CAR');
														break;
														case "3":
															echo Text::_('OS_TRAIN');
														break;
													}
													?>
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
					}
					?>
				</table>
			</td>
		</tr>
	</table>
</page>