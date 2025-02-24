<style>
fieldset label, fieldset span.faux-label {
    clear: right;
}
table.admintable td.key, table.admintable td.paramlist_key {
    background-color: #F6F6F6;
    border-bottom: 1px solid #E9E9E9;
    border-right: 1px solid #E9E9E9;
    color: #666666;
    font-weight: bold;
    text-align: right;
    width: 140px;
    font-size:12px;
    padding-right:10px;
}

table.admintable th, table.admintable td {
    font-size: 12px;
}

table.admintable td {
    padding: 3px;
    font-size:12px;
    
}

legend {
    color: #146295;
    font-size: 12px;
    font-weight: bold;
}

div.width-20 fieldset, div.width-30 fieldset, div.width-35 fieldset, div.width-40 fieldset, div.width-45 fieldset, div.width-50 fieldset, div.width-55 fieldset, div.width-60 fieldset, div.width-65 fieldset, div.width-70 fieldset, div.width-80 fieldset, div.width-100 fieldset {
    background-color: #FFFFFF;
    padding: 5px 17px 17px;
}
fieldset {
    border: 1px solid #CCCCCC;
    margin-bottom: 10px;
    padding: 5px;
    text-align: left;
}
</style>

<table width="100%" style="border:0px !important;font-family:Arial;font-size:12px;">
	<tr style="border:0px !important;">
		<td width="100%" colspan="2" valign="middle" style="text-align:left !important;padding-bottom:5px !important;padding-top: 5px;border:0px !important;border-bottom:1px solid #efefef !important;">
			<font style="font-weight:bold;font-size:16px;">
				<?php
				if($row->ref != ""){
					?>
					<?php echo $row->ref?>,&nbsp;
					<?php
				}
				?>
				<?php echo OSPHelper::getLanguageFieldValue($row,'pro_name');?>
				<?php
				if($row->price_call == 0){
					echo "&nbsp;&nbsp;/";
					echo OSPHelper::generatePrice($row->curr,$row->price);
					if($row->rent_time != ""){
						echo "/".Text::_($row->rent_time);
					}
				}else{
					echo "&nbsp;&nbsp;/";
					echo Text::_('OS_CALL_FOR_PRICE');
				}
				?>
			</font>
		</td>
	</tr>
	
	<tr style="border:0px !important;">
		<td width="100%" colspan="2" valign="middle" style="text-align:left !important;padding-bottom:5px !important;padding-top: 5px;border:0px !important;">
			<table width="100%">
				<tr>
					<td width="60%" valign="top">
						<table width="100%">
							<?php
							if($row->show_address == 1){
							?>
							<tr>
								<td style="border:1px solid #CCC !important;background-color:#A1F9F2;padding-top:5px;padding-bottom:5px;" colspan="2">
									<font style="font-size:14px;font-weight:bold;">
										<?php 
										echo OSPHelper::generateAddress($row);
										?>
									</font>
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td style="border-right:1px solid #CCC !important;border-left:1px solid #CCC !important;border-top:1px solid #CCC !important;border-bottom:1px solid #CCC !important;font-size:14px;font-weight:bold;color:white;background-color:#098B0F;padding-top:5px;padding-bottom:5px;text-align:center;" width="50%">
									<?php echo $lists['category']; ?>
								</td>
								<td style="border-right:1px solid #CCC !important;border-top:1px solid #CCC !important;border-bottom:1px solid #CCC !important;font-size:14px;font-weight:bold;background-color:#1E55D3;color:white;padding-top:5px;padding-bottom:5px;text-align:center;" width="50%">
									<?php echo $lists['type']; ?>
								</td>
							</tr>
							<?php
							if($row->isFeatured == 1){
							?>
							<tr>
								<td style="border-right:1px solid #CCC !important;font-size:16px;font-weight:bold;background-color:#F60219;color:white;padding-top:5px;padding-bottom:5px;text-align:center;" colspan="2">
									<?php
									echo Text::_('OS_FEATURED');
									?>
								</td>
							</tr>
							<?php
							}
							?>
							<tr>

	<tr style="border:0px !important;">
		<td width="100%" colspan="2" valign="middle" style="text-align:center !important;border:0px !important;border-bottom:1px solid #efefef !important;">
			<?php
			if(count($row->photo) > 0){
				$photos = $row->photo;
				$j = 0;
				?>
				<table  width="100%" style="border:0px !important;">
					<tr style="border:0px !important;">
					<?php
					for($i=0;$i<2;$i++){
						$j++;
						$photo = $photos[$i];
						?>
						<td width="25%" style="border:0px !important;text-align:center !important;" valign="top">
							<?php
							if($photo->image != ""){
								OSPHelper::showPropertyPhoto($photo->image,'medium',$row->id,'width: 260px;','img-rounded img-polaroid','');
							}
							?>					
						</td>
						<?php
						if($j == 2){
							echo '</tr><tr style="border:0px !important;">';
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



							<td style="font-size:12px;padding:5px;text-align:justify;" colspan="2">
						<center>
							<font style="font-size:14px;font-weight:bold;">
								<?php
								echo Text::_('OS_FEATURES');
								?>
							</font>
						</center>
						<center>
						<?php

						if($configClass['show_feature_group'] == 1){
							if($configClass['use_rooms']){
								if($row->rooms > 0){?>
									<br />
									<?php echo Text::_('OS_NUMBER_ROOMS')?>: <?php echo $row->rooms;?>
								<?php 
								} 
							}
							
							if($configClass['use_bedrooms']){
							?>
								<?php if($row->bed_room > 0){?>
									<BR />
									<?php echo Text::_('OS_NUMBER_BEDROOMS')?>: <?php echo $row->bed_room;?>
								<?php 
								} 
							}
								
							if($configClass['use_bathrooms']){
								?>
								<?php if($row->bath_room > 0){?>
									<BR />
									<?php echo Text::_('OS_NUMBER_BATHROOMS')?>: <?php echo $row->bath_room; ?>
								<?php
								}
							} 

								if($configClass['use_parking']){
								if($row->parking != ""){?>
									<BR />		
									<?php echo Text::_('OS_PARKING')?>: <?php echo $row->parking?>
								<?php 
								} 
							}
								
							if($configClass['use_squarefeet']){
								if($row->square_feet != ""){?>
								<br />
								<?php echo OSPHelper::showSquareLabels();?>: <?php echo $row->square_feet?>
								<?php 
								} 
							}
								
							if($configClass['use_nfloors']){
								if($row->number_of_floors > 0){?>
								<br />
								<?php echo Text::_('OS_NUMBER_OF_FLOORS')?>: <?php echo $row->number_of_floors?>
								<?php 
								} 
							}
						}
?>
<hr></center>
									<?php
									echo $row->pro_small_desc;
									?>
									<BR />
									<?php
									echo $row->pro_full_desc;

									if($configClass['show_amenity_group'] == 1){
										if(count($amenitylists) > 0){
											?>
											<BR />
											<strong><?php echo Text::_('OS_CONVENIENCE')?>: </strong><BR />
											<?php
											$j = 0;
											for($i=0;$i<count($amenities);$i++){
												if(count($amenitylists) > 0){
													if(in_array($amenities[$i]->id,$amenitylists)){
														$j++;
														echo OSPHelper::getLanguageFieldValue($amenities[$i],'amenities').", ";
													}
												}
											}
										}
									}
									
									if($configClass['show_neighborhood_group'] == 1){
										$db = Factory::getDbo();
										$query = "Select count(a.id) from #__osrs_neighborhood as a"
												." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
												." where a.pid = '$row->id'";
										$db->setQuery($query);
										$count = $db->loadResult();
										if($count > 0){
											?>
											<BR />
											<strong><?php echo Text::_('OS_NEIGHBORHOOD')?>: </strong><BR />
											<?php
											$query = "Select a.*,b.neighborhood from #__osrs_neighborhood as a"
													." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
													." where a.pid = '$row->id'";
											$db->setQuery($query);
											$neighbodhoods = $db->loadObjectList();
											if(count($neighbodhoods) > 0){
												for($i=0;$i<count($neighbodhoods);$i++){
													$neighborhood = $neighbodhoods[$i];
													?>
													<?php echo Text::_($neighborhood->neighborhood)?>:
													<?php echo $neighborhood->mins?> <?php echo Text::_('OS_MINS')?> <?php echo Text::_('OS_BY')?> &nbsp;
													<?php
													switch ($neighborhood->traffic_type){
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
													echo ", ";
												}
											}
										}
									}
?>



									<!-- //if($configClass['show_agent_details'] == 1){
									//	if(($configClass['show_agent_image'] == 1) and ($row->agent->photo != "")){
											?> -->
									<!--		<BR /><BR /> -->
									<!--		<img style="width: 70px;border:1px solid #CCC;margin:3px;" src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent->photo?>" /> -->
											<?php
									//		if(($configClass['show_agent_email'] == 1) and ($row->agent->email != "")){
											?>
									<!--			<BR /> -->
									<!--			<font style="font-weight:bold;color:gray;"><?php echo $row->agent->email;?></font> -->
											<?php
									//		}
											?>
											<?php
									//		if(($row->agent->phone != "") and ($configClass['show_agent_phone'] == 1)){
											?>
									<!--			<BR />
												<font style="font-weight:bold;color:black;font-size:18px;"><?php echo $row->agent->phone;?></font> -->
											<?php
									//		}
									//	}
									//}
									 ?>
								</td>
							</tr>
						</table>
					</td>
					<td width="40%" valign="top" style="background-color:#64164A;color:white;padding:5px;">
								<?php 
					//			} 
					//		}
					//	}
						
						if(count($groups) > 0){
							for($i=0;$i<count($groups);$i++){
								$group = $groups[$i];
								$fields = HelperOspropertyFields::getFieldsData($row->id, $group->id);
								if(count($fields) > 0){?>
								<br />
									<center>
										<font style="color:#C8A065;font-size:16px;font-weight:bold;">
											<?php echo OSPHelper::getLanguageFieldValue($group,'group_name');?>
										</font>
									</center>
									<br />
									<?php
									for($j=0;$j<count($fields);$j++){
										$field = $fields[$j];
										echo $field->field_label.": ".$field->value."<br />";
									}
								}
							}
						}
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script type="text/javascript">
window.print();
</script>