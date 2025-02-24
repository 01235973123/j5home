<?php 
/*------------------------------------------------------------------------
# locator.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

$db = Factory::getDbo();
$rowFluidClass		= $bootstrapHelper->getClassMapping('row-fluid');
$span12Class		= $bootstrapHelper->getClassMapping('span12');
$span6Class			= $bootstrapHelper->getClassMapping('span6');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small'). ' smallSizeBox';
$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini'). ' smallSizeBox';
$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
?>
<div class="<?php echo $rowFluidClass;?>">
	<div class="<?php echo $span12Class;?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_GENERAL')?></legend>
			
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_LOCATION_HAVING_PROPERTIES' );?>::<?php echo Text::_('Do you want to show states/cities that have properties available. If you turn this field on, OS Property will have slower loading'); ?>">
							<label for="configuration[category_layout]">
								<?php echo Text::_( 'OS_SHOW_LOCATION_HAVING_PROPERTIES' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('show_available_states_cities',$configs['show_available_states_cities']);
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_MY_LOCATION' );?>::<?php echo Text::_('OS_SHOW_MY_LOCATION_EXPLAIN'); ?>">
							<label for="configuration[show_my_location]">
								<?php echo Text::_( 'Show My Location' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('show_my_location',$configs['show_my_location']);
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Select property type' );?>::<?php echo Text::_('Please select Property types that will be shown in Search form, if you select specific Property types, those types will be shown as tabs above Search form'); ?>">
							<label for="configuration[category_layout]">
								<?php echo TextOs::_( 'Select property type' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php 
							$type_lists = $configs['adv_type_ids'];
							$type_lists = explode("|",$type_lists);
							
							$type_arr = array();
							$type_arr[] = HTMLHelper::_('select.option',0,Text::_('OS_ALL_TYPES'));
							$db = Factory::getDbo();
							$db->setQuery("Select id as value, type_name as text from #__osrs_types where published = '1' order by ordering");
							$types = $db->loadObjectList();
							$type_arr  = array_merge($type_arr,$types);
							echo HTMLHelper::_('select.genericlist',$type_arr,'adv_type_ids[]','style="height:150px; width:250px;" multiple class="form-control input-large chosen"','value','text',$type_lists);
						?>
					</div>
				</div>
			
		</fieldset>
	</div>
</div>
<div class="<?php echo $rowFluidClass;?>">
	<div class="<?php echo $span12Class;?>">
            <fieldset class="form-horizontal options-form">
                <legend><?php echo Text::_('OS_PRICE_FILTERING')?></legend>
				
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_PRICE_FILTER_TYPE');?>::<?php echo Text::_('Please Select Price Filter Type: Drop-down select list with pre-defined options Or Max-Min Slider Filter'); ?>">
								<label for="configuration[category_layout]">
									<?php echo Text::_( 'OS_PRICE_FILTER_TYPE' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							$type_arr = array();
							$type_arr[] = HTMLHelper::_('select.option','0',Text::_('Drop-down select list with pre-defined options'));
							$type_arr[] = HTMLHelper::_('select.option','1',Text::_('Max-Min Slider Filter'));
							echo HTMLHelper::_('select.genericlist',$type_arr,'configuration[price_filter_type]','class="form-select input-large ilarge"','value','text',$configs['price_filter_type']);
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_MIN_PRICE_ON_SLIDER');?>">
								<label for="configuration[category_layout]">
									<?php echo Text::_( 'OS_MIN_PRICE_ON_SLIDER' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" name="configuration[min_price_slider]" value="<?php echo isset($configs['min_price_slider'])? $configs['min_price_slider']:''; ?>" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_MAX_PRICE_ON_SLIDER');?>">
								<label for="configuration[category_layout]">
									<?php echo Text::_( 'OS_MAX_PRICE_ON_SLIDER' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" name="configuration[max_price_slider]" value="<?php echo isset($configs['max_price_slider'])? $configs['max_price_slider']:''; ?>" />
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_PRICE_SLIDER_STEP_AMOUNT');?>">
								<label for="configuration[category_layout]">
									<?php echo Text::_( 'OS_PRICE_SLIDER_STEP_AMOUNT' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" name="configuration[price_step_amount]" value="<?php echo isset($configs['price_step_amount'])? $configs['price_step_amount']:''; ?>" />
						</div>
					</div>
					<?php
					$db->setQuery("Select * from #__osrs_types order by ordering");
					$property_types = $db->loadObjectList();
					for($i=0;$i<count($property_types);$i++){
						$property_type = $property_types[$i];
						if(($configs['type'.$property_type->id] == 1) or (!isset($configs['type'.$property_type->id]))){
							$checked = "checked";
							$disabled = "disabled";
							$min = "";
							$max = "";
							$step = "";
						}else{
							$checked = "";
							$disabled = "";
							$value = $configs['type'.$property_type->id];
							$valueTemp = explode("|",$value);
							$min = $valueTemp[1];
							$max = $valueTemp[2];
							$step = $valueTemp[3];
						}
						?>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlLabelClass; ?>">
								<span class="editlinktip hasTip hasTooltip">
									<label for="configuration[category_layout]">
										<?php echo Text::_( 'OS_PROPERTY_TYPE' ).':'; ?> [<?php echo $property_type->type_name;?>]
									</label>
								</span>
							</div>
							<div class="<?php echo $controlsClass; ?>">
								<?php echo Text::_('OS_AS_ABOVE');?>&nbsp;<input type="checkbox" value="1" <?php echo $checked; ?> name ="type<?php echo $property_type->id;?>" id="type<?php echo $property_type->id;?>" onClick="javascript:updatePriceSlider('<?php echo $property_type->id;?>')" />
								&nbsp;&nbsp;|&nbsp;&nbsp;
								<?php echo Text::_('OS_MIN_PRICE_ON_SLIDER');?>&nbsp;<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" id="min<?php echo $property_type->id;?>" name="min<?php echo $property_type->id;?>" value="<?php echo $min; ?>" <?php echo $disabled;?> />
								<?php echo Text::_('OS_MAX_PRICE_ON_SLIDER');?>&nbsp;<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" id="max<?php echo $property_type->id;?>" name="max<?php echo $property_type->id;?>" value="<?php echo $max; ?>" <?php echo $disabled;?> />
								<?php echo Text::_('OS_STEP');?>&nbsp;<input type="text" class="text-area-order <?php echo $inputSmallClass; ?>" id ="step<?php echo $property_type->id;?>" name="step<?php echo $property_type->id;?>" value="<?php echo $step; ?>" <?php echo $disabled;?> />
							</div>
						</div>
						<?php
					}
					?>
					<script type="text/javascript">
						function updatePriceSlider(type_id){
							var type_checkbox = jQuery("#type" + type_id);
							if(type_checkbox.prop("checked") == true){
								type_checkbox.val("1");
								jQuery("#min" + type_id).prop("disabled", true);
								jQuery("#max" + type_id).prop("disabled", true);
								jQuery("#step" + type_id).prop("disabled", true);
							}else{
								type_checkbox.val("0");
								jQuery("#min" + type_id).prop("disabled", false);
								jQuery("#max" + type_id).prop("disabled", false);
								jQuery("#step" + type_id).prop("disabled", false);
							}
						}
					</script>
				
			</fieldset>
    </div>
</div>
<div class="<?php echo $rowFluidClass;?>">
	<div class="<?php echo $span6Class;?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo TextOs::_('Locator search setting')?></legend>
			
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip">
							<label for="configuration[bussiness_address]">
								<?php echo Text::_( 'OS_DEFAULT_LOCATION' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<input type="text" class="text-area-order <?php echo $inputLargeClass; ?>" name="configuration[default_location]" value="<?php echo isset($configs['default_location'])? $configs['default_location']:''; ?>" />
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_DEFAULT_RADIUS' );?>">
							<label for="configuration[category_layout]">
								<?php echo Text::_( 'OS_DEFAULT_RADIUS' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						$option_radius_type = array();
						$option_radius_type[] = HTMLHelper::_('select.option',5,5);
						$option_radius_type[] = HTMLHelper::_('select.option',10,10);
						$option_radius_type[] = HTMLHelper::_('select.option',20,20);
						$option_radius_type[] = HTMLHelper::_('select.option',100,100);
						$option_radius_type[] = HTMLHelper::_('select.option',200,200);
						echo HTMLHelper::_('select.genericlist',$option_radius_type,'configuration[default_radius]','class="form-select input-large ilarge"','value','text',isset($configs['default_radius'])? $configs['default_radius']:20);
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Radius type' );?>::<?php echo TextOs::_('Radius type explain'); ?>">
							<label for="configuration[category_layout]">
								<?php echo TextOs::_( 'Radius type' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						$option_radius_type = array();
						$option_radius_type[] = HTMLHelper::_('select.option',0,Text::_('MILES'));
						$option_radius_type[] = HTMLHelper::_('select.option',1,Text::_('KILOMETER'));
						echo HTMLHelper::_('select.genericlist',$option_radius_type,'configuration[locator_radius_type]','class="form-select input-large ilarge"','value','text',isset($configs['locator_radius_type'])? $configs['locator_radius_type']:0);
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip">
							<label for="configuration[bussiness_address]">
								<?php echo Text::_( 'OS_MAX_RESULTS' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<input type="text" class="<?php echo $inputMiniClass; ?>" name="configuration[max_locator_results]" value="<?php echo isset($configs['max_locator_results'])? $configs['max_locator_results']:'100'; ?>" />
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_PROPERTY_TYPE' );?>::<?php echo Text::_('OS_SHOW_PROPERTY_TYPE_EXPLAIN'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo Text::_( 'OS_SHOW_PROPERTY_TYPE' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('locator_show_type',$configs['locator_show_type']);
						?>
					</div>
				</div>
				
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_CATEGORY' );?>::<?php echo Text::_('OS_SHOW_CATEGORY_EXPLAIN'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo Text::_( 'OS_SHOW_CATEGORY' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('locator_show_category',$configs['locator_show_category']);
						?>
					</div>
				</div>
				
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_ADDRESS' );?>::<?php echo Text::_('OS_SHOW_ADDRESS_EXPLAIN'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo Text::_( 'OS_SHOW_ADDRESS' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('locator_show_address',$configs['locator_show_address']);
						?>
					</div>
				</div>
			
		</fieldset>
	</div>
	<div class="<?php echo $span6Class;?>">
		<!-- Advance search -->
		<fieldset class="form-horizontal options-form">
		<legend><?php echo TextOs::_('Advance search setting')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_DEFAULT_SORT_BY' );?>::<?php echo Text::_('OS_DEFAULT_SORT_BY_EXPLAIN'); ?>">
						<label for="configuration[category_layout]">
							<?php echo Text::_( 'OS_DEFAULT_SORT_BY' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php 
						$type_arr = array();
						$type_arr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
						$type_arr[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref'));
						$type_arr[] = HTMLHelper::_('select.option','a.pro_name',Text::_('OS_PROPERTY_TITLE'));
						$type_arr[] = HTMLHelper::_('select.option','a.id',Text::_('OS_LISTDATE'));
						$type_arr[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
						if($configs['use_squarefeet'] == 1){
							if($configs['use_square'] == 0){
								$type_arr[] = HTMLHelper::_('select.option','a.square_feet',Text::_('OS_SQUARE_FEET'));
							}else{
								$type_arr[] = HTMLHelper::_('select.option','a.square_feet',Text::_('OS_SQUARE_METER'));
							}
						}
						echo HTMLHelper::_('select.genericlist',$type_arr,'configuration[adv_sortby]','class="form-select input-large ilarge"','value','text',$configs['adv_sortby']);
					?>
				</div>
			</div>
			
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_DEFAULT_ORDER_BY' );?>::<?php echo Text::_('OS_DEFAULT_ORDER_BY_EXPLAIN'); ?>">
						<label for="configuration[category_layout]">
							<?php echo Text::_( 'OS_DEFAULT_ORDER_BY' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php 
						$type_arr = array();
						$type_arr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESCENDING'));
						$type_arr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASCENDING'));
						echo HTMLHelper::_('select.genericlist',$type_arr,'configuration[adv_orderby]','class="form-select input-large ilarge"','value','text',$configs['adv_orderby']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_MORE_OPTION'); ?>">
						<label for="gallery_type">
							<?php echo Text::_( 'OS_SHOW_MORE_OPTION' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_more',(int)$configs['show_more']);
					?>
				</div>
			</div>
			
		</fieldset>
	</div>
</div>

