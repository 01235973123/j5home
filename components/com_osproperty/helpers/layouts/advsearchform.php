<?php
/*------------------------------------------------------------------------
# advSearchForm.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2017 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$span10Class        = $bootstrapHelper->getClassMapping('span10');
$span4Class         = $bootstrapHelper->getClassMapping('span4');
$span3Class         = $bootstrapHelper->getClassMapping('span3');
$span2Class         = $bootstrapHelper->getClassMapping('span2');
$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
if (OSPHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
	Factory::getDocument()->getWebAssetManager()->useScript('showon');
}
else
{
	$tabApiPrefix = 'bootstrap.';
	HTMLHelper::_('script', 'jui/cms.js', ['version' => 'auto', 'relative' => true]);
}
?>
<div class="<?php echo $rowFluidClass; ?>" id="ospropertyadvsearch">
	<div class="<?php echo $span12Class; ?>">
		<div class="tab-content margintop10">
			<?php
			echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'advsearch', array('active' => 'general-information'));
			?>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'addTab', 'advsearch', 'general-information', Text::_('OS_GENERAL_INFORMATION', true));
			?>
			<div class="tab-pane active" id="general-information" >
				<fieldset>
					<div class="<?php echo $rowFluidClass; ?>">
						<?php $increase_div = 0; ?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_CATEGORY')?>
							</strong>
							<div class="clearfix"></div>
							<?php $parentArr = OSPHelper::loadCategoryBoxes($lists['category_ids'],'category_ids'); ?>
							<div class="custom-multi-select" id="category-select">
								<span class="selected-items"><?php echo Text::_('OS_SELECT_CATEGORIES')?></span>
								<div class="dropdown-content">
									<?php
									foreach($parentArr as $cat)
									{
										?>
										<label><?php echo $cat; ?></label>
										<?php
									}
									?>
								</div>
							</div>
							<?php $increase_div++;?>
						</div>
						<?php
						if($configClass['adv_type_ids'] == "0" || $configClass['adv_type_ids'] == "")
						{
						$increase_div++;
						?>
                            <div class="<?php echo $span4Class; ?> searchfields">
                                <strong>
                                    <?php echo Text::_('OS_PROPERTY_TYPE')?>
                                </strong>
                                <div class="clearfix"></div>
                                <div class="custom-multi-select" id="category-select">
									<span class="selected-items"><?php echo Text::_('OS_SELECT_PROPERTY_TYPES')?></span>
									<div class="dropdown-content">
										<?php
										foreach($lists['protypes'] as $type)
										{
											?>
											<label><input type="checkbox" name="property_types[]" value="<?php echo $type->value;?>" data-value="<?php echo $type->text; ?>"><?php echo $type->text; ?></label>
											<?php
										}
										?>
									</div>
								</div>
                            </div>
						<?php
						}
						else
						{
							?>
							<input type="hidden" name="property_type" id="property_type" value="<?php echo $type_id_search?>" />
							<?php
						}
						if(OSPHelper::checkOwnerExisting())
						{
                            $increase_div++;
                            ?>
                            <div class="<?php echo $span4Class; ?> searchfields">
                                <strong>
                                    <?php echo Text::_('OS_PROPERTIES_POSTED_BY')?>:
                                </strong>
                                <div class="clearfix"></div>
                                <?php echo $lists['agenttype']; ?>
                            </div>
						    <?php
						}
						?>
						<?php
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>
						<div class="<?php echo $span4Class; ?> ">
							<strong><?php echo Text::_('OS_PRICE_RANGE')?></strong><BR />
							<div class="price-range">
								<input type="range" id="minPriceRange" name="min_price" min="0" max="<?php echo $configClass['max_price_slider']?>" step="<?php echo $configClass['price_step_amount']?>" value="<?php echo $lists['min_price'];?>" />
								<input type="range" id="maxPriceRange" name="max_price" min="0" max="<?php echo $configClass['max_price_slider']?>" step="<?php echo $configClass['price_step_amount']?>" value="<?php echo $lists['max_price'];?>" />
							</div>
							<div class="price-values">
								<span><?php echo Text::_('OS_MIN') ?>: <?php echo HelperOspropertyCommon::loadCurrency(0); ?><span id="minPriceValue"><?php echo $lists['min_price'];?></span></span>
								<span><?php echo Text::_('OS_MAX') ?>: <?php echo HelperOspropertyCommon::loadCurrency(0); ?><span id="maxPriceValue"><?php echo $lists['max_price'];?></span></span>
							</div>
						</div>
						<?php $increase_div++;?>
						<?php
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>							
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_KEYWORD');?>
							</strong>
							<div class="clearfix"></div>
							<input type="text" class="<?php echo $inputLargeClass; ?>" value="<?php echo htmlspecialchars($lists['keyword_value'])?>" name="keyword"/>
						</div>
						<?php $increase_div++;?>
						<?php
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_SORTBY')?>
							</strong>
							<div class="clearfix"></div>
							<?php echo $lists['sortby'];?>
						</div>
						<?php $increase_div++;?>
						<?php
						if($increase_div == 3){
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>


						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_ORDERBY')?>
							</strong>
							<div class="clearfix"></div>
							<?php echo $lists['orderby'];?>
						</div>
						<?php $increase_div++;?>
						<?php
						if($increase_div == 3){
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>
					</div>
				</fieldset>
			</div>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'endTab');
			?>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'addTab', 'advsearch', 'location-tab', Text::_('OS_LOCATION', true));
			?>
			<div class="tab-pane" id="location-tab" >
				<fieldset>
					<?php $increase_div = 0; ?>
					<div class="<?php echo $rowFluidClass; ?>">
						<?php $increase_div++;?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_ADDRESS');?>
							</strong>
							<div class="clearfix"></div>
							<input type="text" class="<?php echo $inputMediumClass; ?>" value="<?php echo htmlspecialchars($lists['address_value']);?>" name="address" />
						</div>
						<?php
						if(HelperOspropertyCommon::checkCountry()){
							$increase_div++;
						?>
							<div class="<?php echo $span4Class; ?> searchfields">
								<strong>
									<?php echo Text::_('OS_COUNTRY')?>
								</strong>
								<div class="clearfix"></div>
								<?php echo $lists['country']?>
							</div>
						<?php
						}

                        if(OSPHelper::userOneState())
                        {
							echo $lists['state'];
						}
						else
						{
							$increase_div++; //state
						?>
							<div class="<?php echo $span4Class; ?> searchfields">
								<strong>
									<?php echo Text::_('OS_STATE')?>
								</strong>
								<div class="clearfix"></div>
								<div id="country_state">
									<?php echo $lists['state']?>
								</div>
							</div>
						<?php
						}
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						$increase_div++; //city
						?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_CITY')?>
							</strong>
							<div class="clearfix"></div>
							<div id="city_div">
							<?php echo $lists['city']?>
							</div>
						</div>
						<?php
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						$increase_div++;
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?>">
							<strong>
								<?php echo Text::_('OS_POSTCODE')?>
							</strong>
							<div class="clearfix"></div>
							<input type="text" class="<?php echo $inputSmallClass; ?> ishort" value="<?php echo $lists['postcode'];?>" id="postcode" name="postcode" placeholder="<?php echo Text::_('OS_POSTCODE');?>" />
							<?php 
							if($configClass['show_my_location'] == 1)
							{
							?>
							<?php echo Text::_('OS_OR');?>
							<span class="adv_geoloc_icon button" onclick="javascript:updateMyLocation();" id="se_geoloc_icon" title="<?php echo Text::_('OS_SEARCH_GEOLOC_TOOLTIP_INFO'); ?>" ></span>
							<?php } ?>
							<input type="hidden" name="se_geoloc" id="se_geoloc" value="<?php echo $lists['se_geoloc'];?>" />
							&nbsp;
							<?php echo $lists['radius']; ?>
						</div>
						<?php
						if($increase_div == 3)
						{
							$increase_div = 0;
							?>
							</div>
							<div class="<?php echo $rowFluidClass; ?>">
							<?php
						}
						?>
					</div>
				</fieldset>
			</div>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'endTab');
			?>
			<?php
            if(count($amenities) > 0)
			{
                echo HTMLHelper::_($tabApiPrefix.'addTab', 'advsearch', 'amenities-tab', Text::_('OS_AMENITIES', true));
                ?>
                <div class="tab-pane" id="amenities-tab">
                    <fieldset>
                        <div class="<?php echo $rowFluidClass; ?>">
                            <div class="<?php echo $span12Class; ?>">
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

                                $amenities_post = $jinput->get('amenities', array(), 'ARRAY');
                                $j = 0;
                                for ($k = 0; $k < count($optionArr); $k++) {
                                    $j++;
                                    $db->setQuery("Select * from #__osrs_amenities where category_id = '" . $k . "' and published = '1'");
                                    $amenities = $db->loadObjectList();
                                    if (count($amenities) > 0) {
                                        ?>
                                        <div class="<?php echo $rowFluidClass; ?>">
                                            <div class="<?php echo $span12Class; ?>">
                                                <strong>
                                                    <?php echo $optionArr[$k]; ?>
                                                </strong>
                                            </div>
                                        </div>
                                    <div class="<?php echo $rowFluidClass; ?>">
                                        <?php
                                        $j = 0;
                                        for ($i = 0; $i < count($amenities); $i++)
                                        {
                                            $j++;
                                            if (isset($amenities_post))
                                            {
                                                if (in_array($amenities[$i]->id, $amenities_post))
                                                {
                                                    $checked = "checked";
                                                }
                                                else
                                                {
                                                    $checked = "";
                                                }
                                            }
                                            else
                                            {
                                                $checked = "";
                                            }
                                            ?>
                                            <div class="<?php echo $span3Class; ?>">
                                                <label for="amenities<?php echo $amenities[$i]->id; ?>">
                                                    <input type="checkbox" name="amenities[]"
                                                           id="amenities<?php echo $amenities[$i]->id; ?>" <?php echo $checked ?>
                                                           value="<?php echo $amenities[$i]->id; ?>"/> <?php echo OSPHelper::getLanguageFieldValue($amenities[$i], 'amenities'); ?>
                                                </label>
                                            </div>
                                            <?php
                                            if ($j == 4)
                                            {
                                                $j = 0;
                                                ?>
                                                </div><div class="<?php echo $rowFluidClass; ?>">
                                                <?php
                                            }
                                        }
                                        ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <?php
                echo HTMLHelper::_($tabApiPrefix.'endTab');
            }
			echo HTMLHelper::_($tabApiPrefix.'addTab', 'advsearch', 'other-tab', Text::_('OS_OTHER', true));
			?>
			<div class="tab-pane" id="other-tab" >
				<div class="<?php echo $rowFluidClass; ?>">
					<?php $increase_div = 0; ?>
					<div class="<?php echo $span4Class; ?>">
						<?php
						$isFeatured = $jinput->getInt('isFeatured',0);
						if($isFeatured == 1)
						{
							$checked = "checked";
						}
						else
						{
							$checked = "";
						}
						?>
						<input type="checkbox" name="isFeatured" id="isFeatured" value="<?php echo $isFeatured;?>" <?php echo $checked;?> onclick="javascript:changeValue('isFeatured')" />
						&nbsp;
						<strong>
							<?php echo Text::_('OS_SHOW_ONLY_FEATURED_PROPERTIES');?> 
						</strong>
					</div>
					<?php $increase_div++;?>
					<?php
					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}

                    if($configClass['active_market_status'] == 1)
					{
					?>
					<div class="<?php echo $span4Class; ?> searchfields">
						<strong>
							<?php echo Text::_('OS_MARKET_STATUS');?> 
						</strong>
						<?php
						echo $lists['marketstatus'];
						?>
					</div>
					<?php $increase_div++;?>
					<?php } ?>
					<?php
					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}
					?>
					<?php
					if($configClass['use_bathrooms'] == 1)
					{
						$increase_div++;
					    ?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_BATHROOMS')?>
							</strong>
							<div class="clearfix"></div>
							<?php echo $lists['nbath'];?>
						</div>
					    <?php
					}
					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}
					if($configClass['use_bedrooms'] == 1)
					{
						$increase_div++;
					    ?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_BEDROOMS')?>
							</strong>
							<div class="clearfix"></div>
							<?php echo $lists['nbed'];?>
						</div>
					    <?php
					}
					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}
					if($configClass['use_nfloors'] == 1)
					{
						$increase_div++;
					    ?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_FLOORS')?>
							</strong>
							<div class="clearfix"></div>
							<?php echo $lists['nfloor']; ?>
						</div>
					    <?php
					}
					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}
					if($configClass['use_rooms'] == 1)
					{
						$increase_div++;
					    ?>
						<div class="<?php echo $span4Class; ?> searchfields">
							<strong>
								<?php echo Text::_('OS_ROOMS')?>
							</strong>
						<div class="clearfix"></div>
						<?php echo $lists['nroom']; ?>
						</div>
					<?php
					}

					if($increase_div == 3)
					{
						$increase_div = 0;
						?>
						</div>
						<div class="<?php echo $rowFluidClass; ?>">
						<?php
					}
                    if($configClass['use_squarefeet'] == 1)
                    {
                        $increase_div++;
                        ?>
                        <div class="<?php echo $span4Class; ?> searchfields squaresearch">
                            <strong>
                                <?php
                                if($configClass['use_square'] == 0)
								{
                                    echo Text::_('OS_SQUARE_FEET');
                                }
								else
								{
                                    echo Text::_('OS_SQUARE_METER');
                                }
                                ?>
                                <?php
                                echo "(";
                                echo OSPHelper::showSquareSymbol();
                                echo ")";
                                ?>
                            </strong>
                            <div class="clearfix"></div>
                            <input type="text" class="input-mini form-control ishort" name="sqft_min" id="sqft_min" placeholder="<?php echo Text::_('OS_MIN')?>" value="<?php echo isset($lists['sqft_min']) ? $lists['sqft_min']:"";?>" />
                            &nbsp;-&nbsp;
                            <input type="text" class="input-mini form-control ishort" name="sqft_max" id="sqft_max" placeholder="<?php echo Text::_('OS_MAX')?>" value="<?php echo isset($lists['sqft_max']) ? $lists['sqft_max']:"";?>"/>
                        </div>
                        <?php
                        if($increase_div == 3)
						{
                            $increase_div = 0;
                            ?>
                            </div>
                            <div class="<?php echo $rowFluidClass; ?>">
                            <?php
                        }
                        ?>
                        <div class="<?php echo $span4Class; ?> searchfields squaresearch">
                            <strong>
                                <?php
                                    echo Text::_('OS_LOT_SIZE');
                                ?>
                                (<?php echo OSPHelper::showSquareSymbol();?>)
                            </strong>
                            <div class="clearfix"></div>
                            <input type="text" class="input-mini form-control ishort" name="lotsize_min" id="lotsize_min" placeholder="<?php echo Text::_('OS_MIN')?>" value="<?php echo isset($lists['lotsize_min']) ? $lists['lotsize_min']:"";?>" />
                            &nbsp;-&nbsp;
                            <input type="text" class="input-mini form-control ishort" name="lotsize_max" id="lotsize_max" placeholder="<?php echo Text::_('OS_MAX')?>" value="<?php echo isset($lists['lotsize_max']) ? $lists['lotsize_max']:"";?>"/>
                        </div>
						<?php
                        if($increase_div == 3){
                            $increase_div = 0;
                            ?>
                            </div>
                            <div class="<?php echo $rowFluidClass; ?>">
                            <?php
                        }
                        ?>
						<!--
						<div class="<?php echo $span4Class; ?> searchfields posteddatesearch">
                            <strong>
                                <?php
                                    echo Text::_('OS_CREATED_ON');
                                ?>
                            </strong>
                            <div class="clearfix"></div>
                            <?php echo HTMLHelper::calendar($lists['created_from'],'created_from','created_from',"%Y-%m-%d", array('placeholder' => Text::_('OS_FROM'), 'class' => $bootstrapHelper->getClassMapping('input-medium')));?>
                            <?php echo HTMLHelper::calendar($lists['created_to'],'created_to','created_to',"%Y-%m-%d", array('placeholder' => Text::_('OS_TO') , 'class' => $bootstrapHelper->getClassMapping('input-medium') ));?>
                        </div>
						-->
                    <?php
                    }
					?>
				</div>
			</div>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'endTab');
			?>
			<?php
			echo HTMLHelper::_($tabApiPrefix.'endTabSet');
			?>
		</div>
		<div class="clearfix"></div>
		<?php
		$db->setQuery("Select count(id) from #__osrs_extra_fields where published = '1' and searchable = '1'");
		$countfields = $db->loadResult();
		if(($countfields > 0) and ($configClass['show_more'])){
		?>
		<span class="more_option" id="more_option_span"><?php echo Text::_('OS_MORE_OPTION')?>&nbsp; <i class="osicon-chevron-down"></i></span>
		<div id="more_option_div" class="nodisplay">
			<?php
			$fieldLists = [];
			for($i=0;$i<count($groups);$i++)
			{
				$group = $groups[$i];
				if(count($group->fields) > 0)
				{
					?>
					<div class="<?php echo $span12Class; ?> noleftmargin">
						<div class="block_caption">
							<?php echo OSPHelper::getLanguageFieldValue($group,'group_name');?>
						</div>
						<?php
						$fields = $group->fields;
						for($j=0;$j<count($fields);$j++){
							$field = $fields[$j];
							$fieldLists[] = $field->id;
							?>
							<div class="<?php echo $rowFluidClass; ?>" id="advextrafield_<?php echo $field->id;?>">
							<?php 
							HelperOspropertyFields::showFieldinAdvSearch($field,1);
							?>
							</div>
							<div class="clearfix"></div>
							<?php
						}
						?>
					</div>		
					<div class="clearfix"></div>
					<?php
				}
			}
			?>
		</div>
		<?php } ?>
	</div>
	<input type="hidden" name="advfieldLists" id="advfieldLists" value="<?php echo implode(",",(array)$fieldLists)?>" />
</div>
<div class="<?php echo $rowFluidClass; ?>">
	<div class="<?php echo $span12Class; ?> alignright noleftmargin">
		<input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_SEARCH')?>" id="btnSubmit"/>
		<?php
		$needs = [];
		$needs[] = "ladvsearch";
		$needs[] = "property_advsearch";
		$itemid = OSPRoute::getItemid($needs);	
		?>
		<a href="<?php echo Route::_('index.php?option=com_osproperty&view=ladvsearch&Itemid='.$itemid);?>" class="btn btn-secondary"><?php echo Text::_('OS_RESET')?></a>
		<?php
		if(!$ismobile){
			$user = Factory::getUser();
			if(intval($user->id) > 0){
				?>
				<input type="button" class="btn btn-warning" value="<?php echo Text::_('OS_SAVE_TO_SEARCH_LIST_ADDNEW')?>" onclick="javascript:saveSearchList();"  id="btnSaveSearchList"/>
				<?php
			}

			if($jinput->getInt('list_id',0) > 0){
				?>
				<input type="button" class="btn btn-success" value="<?php echo Text::_('OS_SAVE_TO_SEARCH_LIST_UPDATE')?>" onclick="javascript:updateSearchList();" id="btnUpdateSearchList"/>
				<?php
			}
		}
		//}
		?>
	</div>
</div>
<script type="text/javascript">
function updateMyLocation(){
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(advSearchShowPosition,
			function(error){
				alert("<?php echo str_ireplace('"', "'",Text::_(''));?>");

			}, {
				timeout: 30000, enableHighAccuracy: true, maximumAge: 90000
			});
	}
}

function advSearchShowPosition(position){
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + 1);
	var ll = position.coords.latitude+'_'+position.coords.longitude;
	document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString()+";path=/";
	document.getElementById('se_geoloc').value = '1';
	document.getElementById('ftForm').submit();
}
jQuery("#property_types").change(function(){
	var fields = jQuery("#advfieldLists").val();
	var fieldArr = fields.split(",");
	if(fieldArr.length > 0){
		for(i=0;i<fieldArr.length;i++){
			jQuery("#advextrafield_" + fieldArr[i]).hide("fast");
		}
	}
	//var selected_value = jQuery("#propserty_types").val();
	var selected_value = []; 
	var property_types = document.getElementById('property_types');
	var j = 0;
	for(i=0;i<property_types.length;i++){
		if(property_types.options[i].selected == true){
			selected_value[j] =  property_types.options[i].value;
			j++;
		}
	}
	if(selected_value.length > 0){
		
		for(j=0;j < selected_value.length;j++){
			var selected_fields = jQuery("#advtype_id_" + selected_value[j]).val();
			//alert(selected_fields);
			var fieldArr = selected_fields.split(",");
			if(fieldArr.length > 0){
				for(i=0;i<fieldArr.length;i++){
					jQuery("#advextrafield_" + fieldArr[i]).show("slow");
				}
			}
		}
	}
});

document.addEventListener('DOMContentLoaded', function() {
    // Multiple Select Handler
    document.querySelectorAll('.custom-multi-select').forEach(select => {
        select.addEventListener('click', function(event) {
            this.classList.toggle('open');
            event.stopPropagation();
        });

        this.querySelectorAll('.dropdown-content input').forEach(input => {
            input.addEventListener('change', function() {
                updateSelectedOptions(select);
            });
        });
    });

    function updateSelectedOptions(select) {
        const selectedItems = select.querySelector('.selected-items');
        const selectedOptions = Array.from(select.querySelectorAll('.dropdown-content input:checked'))
            .map(input => input.dataset.value)
            .join(', ') || '<?php echo Text::_("OS_SELECT")?>';
        selectedItems.textContent = selectedOptions;
    }

    document.addEventListener('click', function(event) {
        document.querySelectorAll('.custom-multi-select').forEach(select => {
            if (!select.contains(event.target)) {
                select.classList.remove('open');
            }
        });
    });

});

const minPriceRange = document.getElementById('minPriceRange');
const maxPriceRange = document.getElementById('maxPriceRange');
const minPriceValue = document.getElementById('minPriceValue');
const maxPriceValue = document.getElementById('maxPriceValue');

minPriceRange.addEventListener('input', function() {
    minPriceValue.textContent = minPriceRange.value;
});

maxPriceRange.addEventListener('input', function() {
    maxPriceValue.textContent = maxPriceRange.value;
});
</script>