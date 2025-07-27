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

<div class="print-page">
	<div class="print-main-wrap">
		<div class="print-wrap">
			<header class="print-header">
				<div class="print-logo-wrap">
					<div class="logo">
						<?php
						if($configClass['logo'] != ""){
							?>
							<img src="<?php echo Uri::root()?><?php echo $configClass['logo']?>" style="height:70px;" />
							<?php
						}
						?>
					</div>
					<div class="primary-text">
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
					</div>
				</div>
				<div class="print-title-wrap">
					<div class="d-flex align-items-center">
						<div class="flex-grow-1">
							<div class="page-title">
								<h1>
									<?php
									if($row->ref != "" && $configClass['show_ref'] == 1)
									{
										?>
										<?php echo $row->ref?>,&nbsp;
									<?php
									}
									?>
									<?php echo OSPHelper::getLanguageFieldValue($row,'pro_name');?>
								</h1>
							</div>
							<?php
							if($row->show_address == 1)
							{
							?>
								<div class="item-address">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
</svg>
									<?php 
									echo OSPHelper::generateAddress($row);
									?>
								</div>
							<?php } ?>
						</div>
						<ul class="item-price-wrap hide-on-list">
							<li class="item-price">
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
							</li>
						</ul>
					</div>
				</div>
				<div class="print-banner-wrap">
					<?php
					if(count($row->photo) > 0){
						$photos = $row->photo;
						$j = 0;
						$photo = $photos[0];
						OSPHelper::showPropertyPhoto($photo->image,'',$row->id,'max-width: 100%;','mx-auto d-block center img-polaroid','',0);
					}
					?>
				</div>
				<div class="print-agent-info-wrap">
					<h2 class="print-title"><?php echo Text::_('OS_CONTACT_INFORMATION');?></h2>
					<div class="agent-details">
						<div class="d-flex align-items-center">
							<?php
							if($configClass['show_agent_image'] == 1 && $row->agent->photo != "")
							{
								?>
								<div class="agent-image">
									<img style="width: 80px;margin:3px;" src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent->photo?>" />
								</div>
								<?php
							}
							?>
							<ul class="list-unstyled m-0 ml-3 mr-3">
								<li class="agent-name">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
</svg>
									<?php echo $row->agent->name; ?>
								</li>
								<li class="agent-phone-wrap clearfix">
									<?php
									if($row->agent->phone != "" && $configClass['show_agent_phone'] == 1){
									?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
  <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
</svg>&nbsp;<?php echo $row->agent->phone;?>
									<?php
									}
									?>
									&nbsp;
									<?php
									if($row->agent->mobile != "" && $configClass['show_agent_mobile'] == 1){
									?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone" viewBox="0 0 16 16">
  <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
  <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
</svg>
</svg>&nbsp;<?php echo $row->agent->mobile;?>
									<?php
									}
									?>
								</li>
								<li>
									<?php
									if($configClass['show_agent_email'] == 1  && $row->agent->email != ""){
									?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
</svg>&nbsp;<?php echo $row->agent->email;?>
									<?php
									}
									?>
								</li>
							</ul>
						</div>
					</div>
					<BR />
				</div>
			</header>
			<section class="print-content">
				<div class="print-section">
					<h2 class="print-title">
						<?php echo Text::_('OS_DESCRIPTION');?>
					</h2>
					<?php
					echo OSPHelper::getLanguageFieldValue($row,'pro_small_desc') ;//$row->pro_small_desc;
					?>
					<BR />
					<?php
					echo OSPHelper::getLanguageFieldValue($row,'pro_full_desc') ; //$row->pro_full_desc;
					?>
				</div>
				<div class="print-section">
					<h2 class="print-title">
						<?php echo Text::_('OS_DETAILS');?>
					</h2>
					<div class="block-content-wrap">
						<div class="detail-wrap">
							<ul class="list-2-cols list-unstyled">
								<li class="detail-id">
									<strong><?php echo Text::_('OS_PROPERTY_ID');?></strong>
									<span><?php echo $row->ref; ?></span>
								</li>
								<li class="detail-price">
									<strong><?php echo Text::_('OS_PRICE');?></strong>
									<span><?php echo $row->price?></span>
								</li>
								<?php
								if($configClass['use_squarefeet'] == 1 && $row->square_feet > 0){
								?>
								<li class="detail-city">
									<strong><?php echo Text::_('OS_PROPERTY_SIZE');?></strong>
									<span><?php echo OSPHelper::showSquare($row->square_feet)." ".OSPHelper::showSquareSymbol(); ?></span>
								</li>
								<?php
								}	
								?>
								<?php
								if($configClass['use_bathrooms'] == 1 && $row->bath_room > 0){
								?>
								<li class="detail-baths">
									<strong><?php echo Text::_('OS_BATHS');?></strong>
									<span><?php echo OSPHelper::showBath($row->bath_room); ?></span>
								</li>
								<?php
								}	
								?>
								<?php
								if($configClass['use_bedrooms'] == 1 && $row->bed_room > 0){
								?>
								<li class="detail-beds">
									<strong><?php echo Text::_('OS_BEDS');?></strong>
									<span><?php echo $row->bed_room; ?></span>
								</li>
								<?php
								}	
								?>
								<?php
								if($configClass['use_parking'] == 1){
								?>
									<li class="detail-parking">
										<strong><?php echo Text::_('OS_PARKING');?></strong>
										<span><?php echo $row->parking; ?></span>
									</li>
									<?php
									if($row->garage_description != "")
									{
										?>
										<li class="detail-parking-description">
											<strong><?php echo Text::_('OS_GARAGE_DESCRIPTION');?></strong>
											<span><?php echo $row->garage_description; ?></span>
										</li>
										<?php
									}	
									?>
								<?php } ?>
								<?php
								if($row->built_on > 0){
								?>
								<li class="detail-parking">
									<strong><?php echo Text::_('OS_BUILT_ON');?></strong>
									<span><?php echo $row->built_on; ?></span>
								</li>
								<?php } ?>
								<li class="detail-type">
									<strong><?php echo Text::_('OS_PROPERTY_TYPE');?></strong>
									<span><?php echo $lists['type']; ?></span>
								</li>
								<li class="detail-category">
									<strong><?php echo Text::_('OS_CATEGORIES');?></strong>
									<span><?php echo OSPHelper::getCategoryNamesOfProperty($row->id); ?></span>
								</li>
								<?php
								if($configClass['active_market_status'] == 1 && $row->isSold > 0)
								{	
								?>
								<li class="detail-status">
									<strong><?php echo Text::_('OS_MARKET_STATUS');?></strong>
									<span><?php echo OSPHelper::returnMarketStatus($row->isSold);?></span>
								</li>
								<?php
								}	
								?>
							</ul>
						</div>
						<div class="block-title-wrap">
							<h2 class="print-title">
								<?php echo Text::_('OS_ADDITIONAL_INFORMATION');?>
							</h2>
						</div>
						<ul class="list-2-cols list-unstyled">
							<?php
							$tmpArray                   = [];
							?>
							<!-- building information -->
							<?php
							if($configClass['use_nfloors'] == 1)
							{
								$textFieldsArr = array('house_style','house_construction','exterior_finish','roof','flooring');
								$numberFieldArr = array('floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total');
								$intFieldArr = array('number_of_floors','built_on','remodeled_on');
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
								foreach($intFieldArr as $numfield){
									if($row->{$numfield}  > 0){
										$show = 1;
									}
								}
								if($show == 1) 
								{
									foreach($textFieldsArr as $textfield)
									{
										if($row->{$textfield} != "")
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($textfield));?></strong>
												<span><?php echo $row->{$textfield}; ?></span>
											</li>
											<?php
										}
									}
									foreach($intFieldArr as $numfield)
									{
										if($row->{$numfield}  > 0)
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($numfield));?></strong>
												<span><?php echo $row->{$numfield}; ?></span>
											</li>
											<?php
										}
									}
									foreach($numberFieldArr as $numfield)
									{
										if($row->{$numfield}  > 0)
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($numfield));?></strong>
												<span><?php echo $row->{$numfield}; 
													if($numfield != "number_of_floors")
													{
														echo " ".OSPHelper::showSquareSymbol();
													}
												?>
												
												</span>
											</li>
											<?php
										}
									}
								}
							}
							if($configClass['basement_foundation'] == 1 && ($row->basement_size > 0 || $row->basement_foundation != "" || $row->percent_finished != ""))
							{
								if($row->basement_foundation != "")
								{
									?>
									<li>
										<strong><?php echo Text::_('OS_BASEMENT_FOUNDATION');?></strong>
										<span><?php echo $row->basement_foundation; ?></span>
									</li>
									<?php
								}
								if($row->basement_size > 0)
								{
									?>
									<li>
										<strong><?php echo Text::_('OS_BASEMENT_SIZE');?></strong>
										<span><?php echo $row->basement_size; ?></span>
									</li>
									<?php
								}
								if($row->percent_finished != "")
								{
									?>
									<li>
										<strong><?php echo Text::_('OS_PERCENT_FINISH');?></strong>
										<span><?php echo $row->percent_finished; ?></span>
									</li>
									<?php
								}
							}
							if($configClass['use_squarefeet'] == 1)
							{
								$textFieldsArr = array('subdivision','land_holding_type','lot_dimensions','frontpage','depth');
								$numberFieldArr = array('total_acres','lot_size','living_areas');
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
									foreach($textFieldsArr as $textfield)
									{
										if($row->{$textfield} != "")
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($textfield));?></strong>
												<span><?php echo $row->{$textfield}; ?></span>
											</li>
											<?php
										}
									}
									foreach($numberFieldArr as $numfield)
									{
										if($row->{$numfield}  > 0)
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($numfield));?></strong>
												<span><?php echo OSPHelper::showBath($row->{$numfield}); 
												switch($numfield)
												{
													case "square_feet":
													case "lot_size":
														echo " ".OSPHelper::showSquareSymbol();
														break;
													default:
														echo " ".OSPHelper::showAcresSymbol();
														break;
												}
												?></span>
											</li>
											<?php
										}
									}
								}
							}
							if($configClass['use_business'] == 1){
								$textFieldsArr = array('takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities');
								$show = 0;
								foreach($textFieldsArr as $textfield){
									if($row->{$textfield} != ""){
										$show = 1;
									}
								}

								if($show == 1) {
									foreach($textFieldsArr as $textfield)
									{
										if($row->{$textfield} != "")
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($textfield));?></strong>
												<span><?php echo $row->{$textfield}; ?></span>
											</li>
											<?php
										}
									}
								}
							}
							if($configClass['use_rural'] == 1){
								$textFieldsArr = array('fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
								$show = 0;
								foreach($textFieldsArr as $textfield){
									if($row->{$textfield} != ""){
										$show = 1;
									}
								}

								if($show == 1) {
									foreach($textFieldsArr as $textfield)
									{
										if($row->{$textfield} != "")
										{
											?>
											<li>
												<strong><?php echo Text::_('OS_'.strtoupper($textfield));?></strong>
												<span><?php echo $row->{$textfield}; ?></span>
											</li>
											<?php
										}
									}
								}
							}
							?>
						</ul>
					</div>
				</div>
				<div class="print-section">
					<h2 class="print-title"><?php echo Text::_('OS_AMENITIES')?></h2>
					<div class="block-content-wrap">
						<div class="list-3-cols list-unstyled">
							<?php
							$optionArr = [];
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
							
							foreach ($optionArr as $amen_cat)
							{
								$query = "Select a.* from #__osrs_amenities as a"
									." inner join #__osrs_property_amenities as b on b.amen_id = a.id"
									." where a.published = '1' and b.pro_id = '$row->id' and a.category_id = '$l' order by a.ordering";
								$db->setQuery($query);
								$amens = $db->loadObjectList();
								$amens_str1 = "";
								if(count($amens) > 0)
								{
									$amens_str = "";
									$j = 0;
									$k = 0;
									if($configClass['amenities_layout'] == 1)
									{
										$span = $bootstrapHelper->getClassMapping('span6'); //"span6";
										$jump = 2;
									}
									else
									{
										$span = $bootstrapHelper->getClassMapping('span4');
										$jump = 3;
									}
									
									for($i=0;$i<count($amens);$i++)
									{
										$k++;
										$amen = $amens[$i];
										?>
										<li>
											<?php
											if($amen->icon != "")
											{
												?>
												<i class="<?php echo $amen->icon;?>"></i>&nbsp;
												<?php
											}
											else
											{
												?>
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
			  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
			</svg>
												<?php
											}
											echo OSPHelper::getLanguageFieldValue($amen,'amenities');
										?>
										</li>
										<?php
									}
								}
								$l++;
							}
							?>
						</div>
					</div>
				</div>
				<?php
				if($configClass['energy'] == 1 && ($row->energy > 0 || $row->climate > 0 || $row->e_class != "" || $row->c_class != ""))
				{
				?>
					<div class="print-section">
						<h2 class="print-title"><?php echo Text::_('OS_ENERGY_CLASS')?></h2>
						<div class="block-content-wrap">
							<?php
							echo HelperOspropertyCommon::drawGraph($row->energy, $row->climate,$row->e_class,$row->c_class);
							?>
						</div>
					</div>
				<?php
				}

				if(count((array)$photos) > 1)
				{
					?>
					<div class="print-section">
						<h2><?php echo Text::_('OS_PHOTOS')?></h2>
						<?php
						for($i=1;$i<count($photos);$i++)
						{
							if($photos[$i]->image != "")
							{
								if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/'.$photos[$i]->image))
								{
									?>
									<div class="print-gallery-image">
										<img class="img-fluid mb-3" src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/<?php echo $photos[$i]->image?>" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>"/>
									</div>
									<?php
								}
							}
						}
						?>
					</div>
					<?php
				}
				?>
			</section>
		</div>
	</div>
</div>
<?php
$task = Factory::getApplication()->input->getString('task');
if($task == 'property_print'){
?>
<script type="text/javascript">
window.print();
</script>
<?php } ?>