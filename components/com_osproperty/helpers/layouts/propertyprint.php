<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<style>
fieldset label, fieldset span.faux-label {
    clear: right;
}
table.bathinforTable
{
	font-size:12px;
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

#featurestab h4, #neighbortab h4, #corefieldtab h4{
	font-size:16px;
}

@page {
      size: A4;
      margin: 0;
    }
    @media print {
      table {
        max-height: 100% !important;
        overflow: hidden !important;
        page-break-after: always;
      }
    }
</style>
<?php $db = Factory::getDbo();?>
<div style="page-break-after:always">
<table width="100%" style="border:0px !important;font-family:Arial;font-size:12px;">
	<tr style="border:0px !important;">
		<td width="50%" valign="bottom" style="text-align:left !important;padding-bottom:10px !important;padding-right: 10px;border:0px !important;border-bottom:1px solid #efefef !important;">
			<?php
			if($configClass['logo'] != ""){
				?>
				<img src="<?php echo Uri::root()?><?php echo $configClass['logo']?>" style="height:70px;" />
				<?php
			}
			?>
		</td>
		<td width="50%" valign="bottom" style="text-align:right !important;padding-bottom:10px !important;padding-right: 10px;border:0px !important;border-bottom:1px solid #efefef !important;">
			<?php
			if($configClass['general_bussiness_name'] != ""){
			?>
				<strong><?php echo $configClass['general_bussiness_name'];?></strong>
			<?php
			}
			?>
			<br />
			<?php
			if($configClass['general_bussiness_address'] != ""){
				?>
				<strong><?php echo Text::_('OS_ADDRESS');?>: </strong><?php echo $configClass['general_bussiness_address']; ?>
				<?php
			}
			?>
			&nbsp;
			<?php
			if($configClass['general_bussiness_phone'] != ""){
				?>
				<strong><?php echo Text::_('OS_PHONE');?>: </strong><?php echo $configClass['general_bussiness_phone']; ?>
				<?php
			}
			?>
			<br />
			<?php
			if($configClass['general_bussiness_email'] != ""){
				?>
				<strong><?php echo Text::_('OS_EMAIL');?>: </strong><?php echo $configClass['general_bussiness_email']; ?>
				<?php
			}
			?>
		</td>
	</tr>
	<tr style="border:0px !important;">
		<td width="100%" colspan="2" valign="middle" style="text-align:center !important;padding-bottom:10px !important;padding-top: 10px;border:0px !important;">
            <h1>
                <?php
                if(($row->ref != "") and ($configClass['show_ref'] == 1)){
                    ?>
                    <?php echo $row->ref?>,&nbsp;
                <?php
                }
                ?>
                <?php echo OSPHelper::getLanguageFieldValue($row,'pro_name');?>
            </h1>
			<span style="font-weight:bold;font-size:18px;text-align:center;">
				<?php
				if($row->price_text != "")
				{
					echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
				}
				elseif($row->price_call == 0)
				{
					//echo "<BR />";
					echo OSPHelper::generatePrice($row->curr,$row->price);
					if($row->rent_time != "")
					{
						echo "/".Text::_($row->rent_time);
					}
				}else{
					//echo "<BR />";
					echo Text::_('OS_CALL_FOR_PRICE');
				}
				?>
			</span>
		</td>
	</tr>
	
	<tr style="border:0px !important;">
		<td width="30%" style="text-align:center !important;padding-bottom:10px !important;border:0px !important;border-bottom:1px solid #efefef !important;" valign="top">
			<table>
				<tr>
					<td width="100%" align="center">
					<?php
					if(count($row->photo) > 0){
						$photos = $row->photo;
						$j = 0;
						$photo = $photos[0];
						OSPHelper::showPropertyPhoto($photo->image,'medium',$row->id,'max-width: 1800px;','mx-auto d-block center img-rounded img-polaroid','',0);
					}
					?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding:10px;background-color:#efefef;" align="left">
						<?php
						echo OSPHelper::getLanguageFieldValue($row,'pro_small_desc') ;//$row->pro_small_desc;
						?>
						<BR />
						<?php
						echo OSPHelper::getLanguageFieldValue($row,'pro_full_desc') ; //$row->pro_full_desc;
						?>
					</td>
				</tr>
				<!-- Show convenience !-->
				<?php
				if($configClass['print_convenience'] == 1){
				?>
				<tr>
					<td width="100%" style="padding-top:10px;">
						<strong><?php echo Text::_('OS_CONVENIENCE');?></STRONG>
						<BR />
						<div style="font-size:12px;background-color:#efefef;padding:10px;">
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
						$l = 0;
						ob_start();
						foreach ($optionArr as $amen_cat) 
						{
							$query = "Select a.amenities from #__osrs_amenities as a"
								. " inner join #__osrs_property_amenities as b on b.amen_id = a.id"
								. " where a.published = '1' and b.pro_id = '$property->id' and a.category_id = '$l' order by a.ordering";
							$db->setQuery($query);
							$property_amenities = $db->loadColumn(0);
							$amens_str1 = "";
							if (count($property_amenities) > 0) 
							{
								?>
								<strong><?php echo $amen_cat?>:</strong>
								<BR />
								<?php
								echo implode(", ", $property_amenities);
								?>
								<BR />
								<?php
							}
							$l++;
						}
						?>
						</div>
					</td>
				</tr>
				<?php } ?>
				<!-- Show custom fields information !-->
				<?php
				if($configClass['print_fields'] == 1 && count($extra_field_groups) > 0){
				?>
				<tr>
					<td width="100%" style="padding-top:10px;">
						<strong><?php echo Text::_('OS_MORE_INFORMATION');?></strong>
						<BR />
							<div style="font-size:12px;padding:10px;background-color:#efefef;">
							<?php 
							for($i=0;$i<count($extra_field_groups);$i++){
								$group = $extra_field_groups[$i];
								$group_name = $group->group_name;
								$fields = $group->fields;
								if(count($fields)> 0){
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
										if(($field->displaytitle == 1) or ($field->displaytitle == 2)){
										?>
											<?php echo $field->field_label;?>
										<?php } ?>
										<?php
										if($field->displaytitle == 1){
											?>
											:
										<?php } ?>
										<?php
										if(($field->displaytitle == 1) or ($field->displaytitle == 3)){?>
										<span><?php echo $field->value;?></span> <?php } ?> &nbsp;
										<?php 
										}
									}
									?>
									<BR />
									<?php
								}
								?>
							 </div>
						 <?php 
						}
						?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>
		<td width="70%" valign="top">
			<table width="100%">
				<?php
				if($row->show_address == 1){
				?>
				<tr>
					<td style="background-color:#efefef;padding-bottom:5px;padding-left:10px" colspan="2">
						<span style="font-size:14px;font-weight:bold;">
							<?php
							echo Text::_('OS_ADDRESS').": ";
							?>
							<?php 
							echo OSPHelper::generateAddress($row);
							?>
						</span>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td style="font-size:13px;font-weight:bold;background-color:#FFF;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:10px" width="100%">
						<?php
						echo Text::_('OS_CATEGORIES').": ";
						?>
						<?php echo OSPHelper::getCategoryNamesOfProperty($row->id); ?>
					</td>
				</tr>
				<tr>
					<td style="font-size:13px;font-weight:bold;background-color:#efefef;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:10px;" width="100%">
						<?php
						echo Text::_('OS_TYPE').": ";
						?>
						<?php echo $lists['type']; ?>
					</td>
				</tr>
				<?php
				if(($configClass['active_market_status'] == 1) && ($row->isSold > 0)){
				?>
				<tr>
					<td style="font-size:16px;font-weight:bold;background-color:orange;color:white;padding-top:5px;padding-bottom:5px;text-align:center;" colspan="2">
						<?php
						echo OSPHelper::returnMarketStatus($row->isSold);
						?>
					</td>
				</tr>
				<?php
				}
				
				?>
				<tr>
					<td style="font-size:12px;padding:10px;" colspan="2" id="corefieldtab">
						<?php
						if($configClass['show_feature_group'] == 1){
							?>
							<div style="padding-left:10px;">
								<?php
								echo OSPHelper::showCoreFields($row);
								?>
							</div>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding:10px;background-color:#efefef;">
						<?php
						if(OSPHelper::allowShowingProfile($row->agent->optin)){
							if(($configClass['show_agent_image'] == 1) and ($row->agent->photo != "")){
								?>
								<img style="width: 70px;margin:3px;" src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent->photo?>" />
								<?php
								if(($configClass['show_agent_email'] == 1) and ($row->agent->email != "")){
								?>
									<BR />
									<span style="font-weight:bold;color:gray;"><?php echo $row->agent->email;?></span>
								<?php
								}
								?>
								<?php
								if(($row->agent->phone != "") and ($configClass['show_agent_phone'] == 1)){
								?>
									<BR />
									<span style="font-weight:bold;color:black;font-size:18px;"><?php echo $row->agent->phone;?></span>
								<?php
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
</div>
<?php
$task = Factory::getApplication()->input->getString('task');
if($task == 'property_print'){
?>
<script type="text/javascript">
window.print();
</script>
<?php } ?>