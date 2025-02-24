<?php 
/*------------------------------------------------------------------------
# property.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowFluidClass		= $bootstrapHelper->getClassMapping('row-fluid');
$span12Class		= $bootstrapHelper->getClassMapping('span12');
$span6Class			= $bootstrapHelper->getClassMapping('span6');
$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
$controlGroupClass	= $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass	= $bootstrapHelper->getClassMapping('control-label');
$controlsClass		= $bootstrapHelper->getClassMapping('controls');
$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini'). ' smallSizeBox';
$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small'). ' smallSizeBox';
?>
<div class="<?php echo $rowFluidClass;?>">
	<div class="<?php echo $span6Class;?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_FEATURED_FIELDS')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('general_approval', TextOs::_( 'Auto approval' ), Text::_("Select Yes if you don't want new properties require admin approval before publishing?")); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('general_approval',$configs['general_approval']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('default_access_level', Text::_( 'OS_DEFAULT_ACCESS_LEVEL' ), Text::_('OS_DEFAULT_ACCESS_LEVEL_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if($configs['default_access_level'] == "")
					{
						$configs['default_access_level'] = 1;
					}
					echo OSPHelper::getChoicesJsSelect(OSPHelper::accessDropdown('configuration[default_access_level]',$configs['default_access_level']));
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('property_not_avaiable', Text::_( 'OS_UNAVAILABLE_LINK' ), TextOs::_('Not available link explain.')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class = "<?php echo $inputLargeClass;?> ilarge" name="configuration[property_not_avaiable]" value="<?php echo isset($configs['property_not_avaiable'])? $configs['property_not_avaiable']:''; ?>" />
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('address_format', TextOs::_( 'Address format' ), TextOs::_('Address format explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$addressArr = array();
					$addressArr[0] =  Text::_('OS_ADDRESS');
					$addressArr[1] =  Text::_('OS_CITY');
					$addressArr[2] =  Text::_('OS_STATE');
					$addressArr[3] =  Text::_('OS_REGION');
					$addressArr[4] =  Text::_('OS_POSTCODE');

					$optionArr = array();
					$optionArr[0] = "0,1,2,3,4";
					$optionArr[1] = "0,1,4,2,3";
					$optionArr[2] = "0,1,4,3,2";
					$optionArr[3] = "0,1,3,4,2";
					$optionArr[4] = "0,1,3,2,4";
					$optionArr[5] = "0,1,2,4,3";

					$nColArr = array();
					for($i=0;$i<count($optionArr);$i++){
						$item = $optionArr[$i];
						$itemArr = explode(",",$item);
						$value = "";
						if(count($itemArr) > 0){
							for($j=0;$j<count($itemArr);$j++){
								$value .= $addressArr[$itemArr[$j]].", ";
							}
							$value = substr($value,0,strlen($value)-2);
						}
						$nColArr[$i] = HTMLHelper::_('select.option',$item,$value);
					}
					if (!isset($configs['address_format'])) $configs['address_format'] = '1';
					echo HTMLHelper::_('select.genericlist',$nColArr,'configuration[address_format]','class="form-select input-large ilarge"','value','text',$configs['address_format']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('limit_upload_photos', TextOs::_( 'Max photos can uploaded' ), TextOs::_('Max photos can uploaded explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="<?php echo $inputMiniClass; ?>" size="5" name="configuration[limit_upload_photos]" value="<?php echo isset($configs['limit_upload_photos'])? $configs['limit_upload_photos']:''; ?>"> <?php echo Text::_("OS_PHOTOS")?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('limit_upload_pdfs', Text::_( 'OS_MAX_PDF_FILES_CAN_BE_UPLOADED' ), Text::_('OS_MAX_PDF_FILES_CAN_BE_UPLOADED_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$max_pdf_files = isset($configs['limit_upload_pdfs'])? $configs['limit_upload_pdfs']:'5';
					$optionArr = [];
					for($p=1;$p <= 10;$p++)
					{
						$optionArr[] = HTMLHelper::_('select.option',$p,$p);
					}
					echo HTMLHelper::_('select.genericlist', $optionArr, 'configuration[limit_upload_pdfs]','class="form-select imedium input-medium"','value','text', $max_pdf_files);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('limit_upload_photos', Text::_( 'OS_REF_FIELD' ), TextOs::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$option_ref_field = array();
					$option_ref_field[] = HTMLHelper::_('select.option',0,Text::_('OS_MANUAL_ENTER'));
					$option_ref_field[] = HTMLHelper::_('select.option',1,Text::_('OS_AUTO_GENERATE'));
					echo HTMLHelper::_('select.genericlist',$option_ref_field,'configuration[ref_field]','class="form-select input-large ilarge"','value','text',isset($configs['ref_field'])? $configs['ref_field']:0);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ref_prefix', Text::_( 'OS_REF_PREFIX' ), Text::_('OS_REF_PREFIX_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="<?php echo $inputSmallClass; ?>" name="configuration[ref_prefix]" value="<?php echo isset($configs['ref_prefix'])? $configs['ref_prefix']:'PREFIX'; ?>">
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_ref', Text::_( 'OS_SHOW_REF' ), Text::_('OS_SHOW_REF_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_ref',$configs['show_ref']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_metatag', Text::_( 'Show meta tag' ), TextOs::_('Show meta tag explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_metatag',$configs['show_metatag']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_just_add_icon', TextOs::_( 'Show just added icon' ), TextOs::_('Show just added icon explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_just_add_icon',$configs['show_just_add_icon']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_just_update_icon', TextOs::_( 'Show just updated icon' ), TextOs::_('Show just updated icon explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_just_update_icon',$configs['show_just_update_icon']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_just_update_icon', TextOs::_( 'Use energy and elimate' ), TextOs::_('Use energy and elimate explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('energy',$configs['energy']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('energy_value', TextOs::_( 'Energy Measurement steps' ), TextOs::_('Energy Measurement steps explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if($configs['energy_class'] == ""){
						$configs['energy_class'] = "A,B,C,D,E,F,G";
					}
					if($configs['energy_value'] == ""){
						$configs['energy_value'] = "50,90,150,230,330,450";
					}
					?>
					<strong>Class:</strong>
					<small>Please enter class name of Energy graph, separated by comma</small>
					<input type="text" class="<?php echo $inputLargeClass; ?>" name="configuration[energy_class]" value="<?php echo $configs['energy_class'];?>" />
					<BR />
					<strong>Value:</strong>
					<small>Please enter value of Energy graph, separated by comma</small>
					<input type="text" class="<?php echo $inputLargeClass; ?>" name="configuration[energy_value]" value="<?php echo $configs['energy_value'];?>" />
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('climate_value', TextOs::_( 'Climate Measurement steps' ), TextOs::_('Climate Measurement steps explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if($configs['climate_class'] == ""){
						$configs['climate_class'] = "A,B,C,D,E,F,G";
					}
					if($configs['climate_value'] == ""){
						$configs['climate_value'] = "5,10,20,35,55,80";
					}
					?>
					<strong>Class:</strong>
					<small>Please enter class name of Co2 graph, separated by comma</small>
					<input type="text" class="<?php echo $inputLargeClass; ?>" name="configuration[climate_class]" value="<?php echo $configs['climate_class'];?>" />
					<BR />
					<strong>Value:</strong>
					<small>Please enter value of Co2 graph, separated by comma</small>
					<input type="text" class="<?php echo $inputLargeClass; ?>" name="configuration[climate_value]" value="<?php echo $configs['climate_value'];?>" />
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'OS_USE_BASE_PROPERTY_FIELDS' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_rooms', TextOs::_( 'Use number rooms field' ), TextOs::_('Use number rooms field explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_rooms',$configs['use_rooms']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_bedrooms', TextOs::_( 'Use number bedrooms field' ), TextOs::_('Use number bedrooms field explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_bedrooms',$configs['use_bedrooms']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_bathrooms', TextOs::_( 'Use number bathrooms field' ), TextOs::_('Use number bathrooms field explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_bathrooms',$configs['use_bathrooms']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('fractional_bath', Text::_( 'OS_FRACTIONAL_BATHS' ), Text::_('OS_FRACTIONAL_BATHS_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('fractional_bath',(int)$configs['fractional_bath']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('more_bath_infor', TextOs::_( 'More Bathroom info' ), TextOs::_('Do you want to have more bathroom information')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('more_bath_infor',(int)$configs['more_bath_infor']);
					?>
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'OS_BUILDING_INFORMATION' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_nfloors', Text::_( 'OS_BUILDING_INFORMATION' ), Text::_('OS_BUILDING_INFORMATION_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_nfloors',$configs['use_nfloors']);
					?>
				</div>
			</div>
			<strong>
				<?php echo TextOs::_( 'Use parking field' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_parking', TextOs::_( 'Use parking field' ), TextOs::_('Use parking field explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_parking',$configs['use_parking']);
					?>
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'OS_BASEMENT_FOUNDATION' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('basement_foundation', Text::_( 'OS_BASEMENT_FOUNDATION' ), Text::_('OS_BASEMENT_FOUNDATION_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('basement_foundation',$configs['basement_foundation']);
					?>
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'OS_LAND_INFORMATION' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_squarefeet', Text::_( 'OS_LAND_INFORMATION' ), Text::_('OS_LAND_INFORMATION_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_squarefeet',$configs['use_squarefeet']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_square', Text::_( 'OS_LAND_AREA_UNIT_OF_MEASUREMENT' ), Text::_('OS_LAND_AREA_UNIT_OF_MEASUREMENT_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_square',$configs['use_square'],Text::_('OS_FEET'), Text::_('OS_METER'));
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('acreage', Text::_( 'OS_ACREAGE_UNIT_OF_MEASUREMENT' ), Text::_('OS_ACREAGE_UNIT_OF_MEASUREMENT_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('acreage',(int)$configs['acreage'],Text::_('OS_ACRES'),Text::_('OS_HECTARES'));
					?>
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'Show Property History & Tax' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_property_history', Text::_( 'Show Property History & Tax' ), Text::_('Do you want to show Property Sold History & Tax')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_property_history',$configs['use_property_history']);
					?>
				</div>
			</div>
			<strong>
				<?php echo Text::_( 'OS_BUSINESS_INFORMATION' ); ?>
			</strong>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_business',Text::_( 'OS_BUSINESS_INFORMATION' ), Text::_('OS_BUSINESS_INFORMATION_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_business',$configs['use_business']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_rural',Text::_( 'OS_RURAL_INFORMATION' ), Text::_('OS_RURAL_INFORMATION_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_rural',$configs['use_rural']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('use_open_house',Text::_( 'Show Open House' ), Text::_('Do you want to show Open House information')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('use_open_house',$configs['use_open_house']);
					?>
				</div>
			</div>
			<div class="headerlabel">
				<?php echo HelperOspropertyCommon::showLabel('',Text::_( 'OS_FIELDS_REQUIRED' ), Text::_('OS_FIELDS_REQUIRED_EXPLAIN')); ?>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('adddress_required',Text::_( 'OS_ADDRESS' ), Text::_('OS_ADDRESS_REQUIRED')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('adddress_required',$configs['adddress_required']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('short_desc_required',Text::_( 'OS_SHORT_DESCRIPTION' ), Text::_('OS_SHORT_DESC_REQUIRED')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('short_desc_required',$configs['short_desc_required']);
					?>
				</div>
			</div>
			<!--
			<div class="headerlabel">
				<?php echo HelperOspropertyCommon::showLabel('',Text::_( 'OS_GRAB_IMAGES' ), Text::_('OS_GRAB_IMAGES_EXPLAIN')); ?>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('grabimages_backend',Text::_( 'OS_USING_AT_BACKEND' ), Text::_('OS_USING_AT_BACKEND_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('grabimages_backend',$configs['grabimages_backend']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('grabimages_frontend',Text::_( 'OS_USING_AT_FRONTEND' ), Text::_('OS_USING_AT_FRONTEND_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('grabimages_frontend',$configs['grabimages_frontend']);
					?>
				</div>
			</div>
			-->
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_SOCIAL_SHARING')?></legend>
			<table  width="100%" class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ALLOW_SOCIAL_SHARING' );?>::<?php echo TextOs::_('ALLOW_SOCIAL_SHARING_EXPLAIN'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo Text::_( 'OS_ALLOW_SOCIAL_SHARING' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('social_sharing',(int)$configs['social_sharing']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHARING_TYPE' );?>::<?php echo Text::_('OS_SHARING_TYPE_EXPLAIN'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo Text::_( 'OS_SHARING_TYPE' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('social_sharing_type',$configs['social_sharing_type'],'Addthis sharing','Native sharing');
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_PUBLISHED_ID' );?>::<?php echo Text::_('In case you select Addthis sharing, please enter Published ID'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo Text::_( 'OS_PUBLISHED_ID' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<input type="text" size="20" name="configuration[publisher_id]" value="<?php echo isset($configs['publisher_id'])? $configs['publisher_id']:''; ?>" class="input-small form-control" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_SEF')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('sef_configure', Text::_( 'OS_SEF_LINK_CONTAIN' ), Text::_('OS_SEF_LINK_CONTAIN_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$sefArr[] = HTMLHelper::_('select.option','0',Text::_('OS_ALIAS_ONLY'));
					$sefArr[] = HTMLHelper::_('select.option','1',Text::_('OS_REF_ALIAS'));
					$sefArr[] = HTMLHelper::_('select.option','2',Text::_('OS_REF_ALIAS_ID'));
					echo HTMLHelper::_('select.genericlist',$sefArr,'configuration[sef_configure]','class="form-select input-large ilarge"','value','text',$configs['sef_configure']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_BREADCRUMBS')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('include_categories', Text::_( 'OS_INCLUDE_CATEGORIES' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('include_categories',(int)$configs['include_categories']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('include_type', Text::_( 'OS_INCLUDE_PROPERTY_TYPE' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('include_type',(int)$configs['include_type']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_CONVENIENCE')?></legend>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<strong>
					<?php
					echo Text::_('OS_AMENTITY_CATEGORY_ICONS');
					?>
					</strong>
				</div>
			</div>
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

			$amenityCategoryIcons = array('edicon-cog','edicon-lifebuoy','edicon-power-cord','edicon-bullhorn','edicon-fire','edicon-paint-format','edicon-podcast','edicon-image','edicon-lock');

			for($k=0;$k<count($optionArr);$k++)
			{
				?>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<?php
						echo $optionArr[$k];
						?>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						if($configs['category_'.$k]	== "")
						{
							$configs['category_'.$k] = "edicon ".$amenityCategoryIcons[$k];
						}
						?>
						<input type="text" class="<?php echo $inputMediumClass; ?> imedium" value="<?php echo $configs['category_'.$k];?>" name="configuration[category_<?php echo $k?>]" />
					</div>
				</div>
				<?php
			}
			?>
		</fieldset>
	</div>
	<div class="<?php echo $span6Class; ?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_ALERT_EMAIL_SETTING')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_( 'OS_ACTIVATE_ALERT_EMAIL_FEATURE' ).':'; ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('active_alertemail',(int)$configs['active_alertemail']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo Text::_( 'OS_SEND_ALERT_WHEN_NEW_PROPERTY_CREATED' ).':'; ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if(!isset($configs['new_property_alert']))
					{
						$configs['new_property_alert'] = 1;
					}
					OspropertyConfiguration::showCheckboxfield('new_property_alert',$configs['new_property_alert']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SEND_ALERT_WHEN_NEW_PROPERTY_UPDATED_EXPLAIN'); ?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo Text::_( 'OS_SEND_ALERT_WHEN_NEW_PROPERTY_UPDATED' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					if(!isset($configs['update_property_alert']))
					{
						$configs['update_property_alert'] = 1;
					}
					OspropertyConfiguration::showCheckboxfield('update_property_alert',$configs['update_property_alert']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_CRONJOB_FILE' );?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo Text::_( 'OS_CRONJOB_FILE' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					Live URL: <strong class="colorgreen"><?php echo Uri::root(); ?>components/com_osproperty/cron.php</strong>
					<BR />
					Real Path: <strong class="colorred"><?php echo JPATH_ROOT; ?>/components/com_osproperty/cron.php</strong>
					<BR />
					<span style="font-size:13px;">You need to set up a cron job using your hosting account control panel which should execute every hours. Depending on your web server you should use either the live url or real path.</span>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'NUMBER_LISTING_TO_CHECK_PER_CRONTASK_RUNNING' );?>
						<label for="checkbox_number_email_by_hour">
							<?php echo TextOs::_( 'NUMBER_LISTING_TO_CHECK_PER_CRONTASK_RUNNING' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
						<input type="text" class="text-area-order input-mini form-control imini" size="5" name="configuration[max_properties_per_time]" value="<?php echo isset($configs['max_properties_per_time'])?$configs['max_properties_per_time']:'100' ?>" />
						<BR />
						<span style="font-size:13px;"><?php echo TextOs::_( 'NUMBER_LISTING_TO_CHECK_PER_CRONTASK_RUNNING_EXPLAIN' ); ?></span>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'NUMBER_SAVED_LIST_TO_CHECK_PER_CRONTASK_RUNNING' );?>
						<label for="checkbox_number_email_by_hour">
						<?php echo TextOs::_( 'NUMBER_SAVED_LIST_TO_CHECK_PER_CRONTASK_RUNNING' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="text-area-order input-mini form-control imini" size="5" name="configuration[max_lists_per_time]" value="<?php echo isset($configs['max_lists_per_time'])?$configs['max_lists_per_time']:'50' ?>" />
					<BR />
					<span style="font-size:13px;"><?php echo TextOs::_( 'NUMBER_SAVED_LIST_TO_CHECK_PER_CRONTASK_RUNNING_EXPLAIN' ); ?></span>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'NUMBER_EMAIL_CRONTASK_RUNNING' );?>
						<label for="checkbox_number_email_by_hour">
					<?php echo TextOs::_( 'NUMBER_EMAIL_CRONTASK_RUNNING' ).':'; ?>
					</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="text-area-order input-mini form-control imini" size="5" name="configuration[max_email_per_time]" value="<?php echo isset($configs['max_email_per_time'])?$configs['max_email_per_time']:'50' ?>" />
					<BR />
					<span style="font-size:13px;"><?php echo TextOs::_( 'NUMBER_EMAIL_CRONTASK_RUNNING_EXPLAIN' ); ?></span>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo TextOs::_('Comment Settings')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('comment_active_comment', Text::_( 'Active Comment & Rating' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('comment_active_comment',(int)$configs['comment_active_comment']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[comment_active_comment]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('comment_auto_approved',TextOs::_( 'Auto approved Comment' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('comment_auto_approved',(int)$configs['comment_auto_approved']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[comment_active_comment]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_rating',TextOs::_( 'Show rating icon' ), Text::_('Show rating icon explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_rating',(int)$configs['show_rating']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[comment_active_comment]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_rating',Text::_( 'OS_LOGGED_USER_CAN_SUBMIT_REVIEW' ), Text::_('OS_ONLY_REGISTERED_USER_CAN_SUBMIT_REVIEW_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('registered_user_write_comment',(int)$configs['registered_user_write_comment']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[comment_active_comment]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('only_one_review',Text::_( 'OS_ONE_USER_CAN_WRITE_ONE_REVIEW' ), Text::_('OS_ONE_USER_CAN_WRITE_ONE_REVIEW_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('only_one_review',(int)$configs['only_one_review']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[comment_active_comment]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('only_one_review',Text::_( 'OS_ALLOW_USER_TO_EDIT_COMMENT' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('allow_edit_comment',(int)$configs['allow_edit_comment']);
					?>
				</div>
			</div>
		</fieldset>
		<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/csv.php');?>
		<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/sold.php');?>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo TextOs::_('Walking score setting')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_walkscore', TextOs::_( 'Show walked score tab' ), TextOs::_('Show walked score tab explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_walkscore',$configs['show_walkscore']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[show_walkscore]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ws_id',TextOs::_( 'Walked score ID' ), TextOs::_('Walked score ID explain.')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" size="50" class="<?php echo $inputLargeClass; ?>" name="configuration[ws_id]" value="<?php echo isset($configs['ws_id'])? $configs['ws_id']:''; ?>">
					<div class="clr"></div>
					<?php echo Text::_('Click here to request new API Walked Score key');?>
					<a href="http://www.walkscore.com/professional/api-sign-up.php" target="_blank">http://www.walkscore.com/professional/api-sign-up.php</a>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[show_walkscore]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ws_width',Text::_( 'Width size of Walked score' ), TextOs::_('Width size of Walked score div explain.')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="<?php echo $inputMiniClass; ?>" size="5" name="configuration[ws_width]" value="<?php echo isset($configs['ws_width'])? $configs['ws_width']:''; ?>"> px
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[show_walkscore]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ws_height',Text::_( 'Height size of Walked score' ), TextOs::_('Height size of Walked score div explain.')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="<?php echo $inputMiniClass; ?>" size="5" name="configuration[ws_height]" value="<?php echo isset($configs['ws_height'])? $configs['ws_height']:''; ?>"> px
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[show_walkscore]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ws_height',TextOs::_( 'Unit' ), Text::_('')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$nColArr = array();
					$nColArr[] = HTMLHelper::_('select.option','mi','Miles');
					$nColArr[] = HTMLHelper::_('select.option','km','Kilometers');
					echo HTMLHelper::_('select.genericlist',$nColArr,'configuration[ws_unit]','class="form-select input-large ilarge"','value','text',$configs['ws_unit']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_PROPERTY_PRINT')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('property_show_print', Text::_( 'OS_ACTIVATE_PRINT' ), Text::_('OS_ACTIVATE_PRINT_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('property_show_print',(int)$configs['property_show_print']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[property_show_print]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('print_convenience',Text::_( 'OS_SHOW_AMENITIES_IN_PRINT' ), ''); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('print_convenience',(int)$configs['print_convenience']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[property_show_print]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('print_fields',Text::_( 'OS_SHOW_CUSTOM_FIELDS_IN_PRINT' ), ''); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('print_fields',(int)$configs['print_fields']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_PDF_EXPORT')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('property_pdf_layout', Text::_( 'OS_PDF_EXPORT_FEATURE' ), Text::_('PDF_LAYOUT_EXPLAIN')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('property_pdf_layout',$configs['property_pdf_layout']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[property_pdf_layout]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('ws_id',Text::_( 'OS_SELECT_PDF_FONT' ), ''); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option','','Select font');
					$fontArr = array('courier','helvetica','times','freeserif','dejavu','dejavuserifi');
					foreach($fontArr as $font){
						if(file_exists(JPATH_ROOT.'/components/com_osproperty/helpers/tcpdf/fonts/'.$font.'.php')){
							$optionArr[] = HTMLHelper::_('select.option',$font,$font);
						}
					}
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[pdf_font]','class="form-select input-large ilarge"','value','text',isset($configs['pdf_font'])? $configs['pdf_font']:'times');
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[property_pdf_layout]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('show_googlemap_pdf',Text::_( 'OS_SHOW_MAP_IMAGE_IN_PDF' ), ''); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_googlemap_pdf',(int)$configs['show_googlemap_pdf']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[property_pdf_layout]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('pdf_fields',Text::_( 'OS_SHOW_CUSTOM_FIELDS_IN_PDF_PAGE' ), ''); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('pdf_fields',(int)$configs['pdf_fields']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_RELATED_PROPERTIES')?></legend>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('relate_properties', Text::_( 'OS_SHOW_RELATED_PROPERTIES' ), Text::_('OS_SHOW_RELATED_PROPERTIES')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('relate_properties',$configs['relate_properties']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('max_relate',TextOs::_( 'Max relate properties' ), TextOs::_('Max relate properties explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" class="<?php echo $inputMiniClass; ?>" size="5" name="configuration[max_relate]" value="<?php echo isset($configs['max_relate'])? $configs['max_relate']:''; ?>"> <?php echo Text::_('OS_PROPERTIES');?>
				</div>
			</div>

			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('relate_columns',Text::_( 'OS_NUMBER_COLUMNS' ), Text::_('OS_RELATED_PROPERTIES_IN_COLUMNS')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$columns = array();
					$columns[] = HTMLHelper::_('select.option','2','2');
					$columns[] = HTMLHelper::_('select.option','3','3');
					$columns[] = HTMLHelper::_('select.option','4','4');
					if (!isset($configs['relate_columns'])) $configs['relate_columns'] = '2';
					echo HTMLHelper::_('select.genericlist',$columns,'configuration[relate_columns]','class="form-select input-small ilarge"','value','text',$configs['relate_columns']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<strong>
					How do you want to show related properties
				</strong>
				<BR />
				<span style="font-style:italic;">OS Property has 2 related properties parts. 1 - By distances, 2 - By property type</span>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('relate_city',Text::_( 'OS_SHOW_RELATED_PROPERTIES_BY_DISTANCES' ), Text::_('Do you want to show related properties by distances')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('relate_city',$configs['relate_city']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('relate_distance',Text::_( 'OS_DISTANCE' ), TextOs::_('Relate properties distance explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$distanceArr[] = HTMLHelper::_('select.option','5','5 Miles');
					$distanceArr[] = HTMLHelper::_('select.option','10','10 Miles');
					$distanceArr[] = HTMLHelper::_('select.option','20','20 Miles');
					$distanceArr[] = HTMLHelper::_('select.option','50','50 Miles');
					$distanceArr[] = HTMLHelper::_('select.option','100','100 Miles');
					if (!isset($configs['relate_distance'])) $configs['relate_distance'] = '0';
					echo HTMLHelper::_('select.genericlist',$distanceArr,'configuration[relate_distance]','class="form-select input-small ilarge"','value','text',$configs['relate_distance']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('relate_property_type',Text::_( 'OS_SHOW_RELATED_PROPERTIES_IN_SAME_TYPE' ), TextOs::_('Select relate properties in the same property type explain')); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('relate_property_type',$configs['relate_property_type']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
				<strong>
					Filter related properties by price and categories
				</strong>
				</br>
				<span style="font-style:italic;">Leave price empty if you don't want to filter related property by category and price</span>
			</div>
			<?php
			$db = Factory::getDbo();
			$db->setQuery("Select id, category_name from #__osrs_categories where published = '1' order by ordering");
			$categories = $db->loadObjectList();
			if(count($categories)> 0){
				foreach ($categories as $category){
					?>
					<div class="control-group related_properties_category_price" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[relate_properties]' => '1')); ?>'>
						<div class="<?php echo $controlLabelClass; ?>">
							<?php echo $category->category_name; ?>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" class="<?php echo $inputSmallClass; ?>" name="configuration[price_from_<?php echo $category->id; ?>]" value="<?php echo $configs['price_from_'.$category->id];?>" placeholder="From"/>
							-
							<input type="text" class="<?php echo $inputSmallClass; ?>" name="configuration[price_to_<?php echo $category->id; ?>]" value="<?php echo $configs['price_to_'.$category->id];?>" placeholder="To"/>
						</div>
					</div>
					<?php
				}
			}
			?>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_TWEET_AUTO_POSTING')?></legend>
			This feature is used to post property details into Twitter.com.
			<BR />
			<strong>Note: </strong>
			<BR />
			1. You need to enter Consumer Key, Consumer Secret, Access Token and Access Token Secret
			<BR />
			2. This feature will update Published and Approved properties
			<BR /><BR />
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('tweet_autoposting', Text::_( 'OS_ENABLE_TWEET_AUTO_POSTING' ), Text::_( 'OS_ENABLE_TWEET_AUTO_POSTING' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('tweet_autoposting',(int)$configs['tweet_autoposting']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[tweet_autoposting]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('tw_posting_properties',Text::_( 'OS_POSTING_PROPERTIES' ), Text::_( 'OS_POSTING_PROPERTIES' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$optionArr = array();
					$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_BOTH_NEW_AND_UPDATED_PROPERTIES'));
					$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_ONLY_NEW_PROPERTIES'));
					$optionArr[] = HTMLHelper::_('select.option',2,Text::_('OS_ONLY_UPDATED_PROPERTIES'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[tw_posting_properties]','class="form-select input-large ilarge"','value','text',$configs['tw_posting_properties']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[tweet_autoposting]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('consumer_key',Text::_( 'OS_CONSUMER_KEY' ), Text::_( 'OS_CONSUMER_KEY' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="consumer_key"  class="<?php echo $inputLargeClass; ?>" name="configuration[consumer_key]" value="<?php echo isset($configs['consumer_key'])? $configs['consumer_key']:''; ?>">
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[tweet_autoposting]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('consumer_key',Text::_( 'OS_CONSUMER_SECRET' ), Text::_( 'OS_CONSUMER_SECRET' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="consumer_secret" class="<?php echo $inputLargeClass; ?>" name="configuration[consumer_secret]" value="<?php echo isset($configs['consumer_secret'])? $configs['consumer_secret']:''; ?>" />
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[tweet_autoposting]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('tw_access_token',Text::_( 'OS_ACCESS_TOKEN' ), Text::_( 'OS_ACCESS_TOKEN' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="tw_access_token" class="<?php echo $inputLargeClass; ?>" name="configuration[tw_access_token]" value="<?php echo isset($configs['tw_access_token'])? $configs['tw_access_token']:''; ?>" />
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[tweet_autoposting]' => '1')); ?>'>
				<div class="<?php echo $controlLabelClass; ?>">
					<?php echo HelperOspropertyCommon::showLabel('tw_access_token_secret',Text::_( 'OS_ACCESS_TOKEN_SECRET' ), Text::_( 'OS_ACCESS_TOKEN_SECRET' )); ?>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" id="tw_access_token_secret" class="<?php echo $inputLargeClass; ?>" name="configuration[tw_access_token_secret]" value="<?php echo isset($configs['tw_access_token_secret'])? $configs['tw_access_token_secret']:''; ?>" />
				</div>
			</div>
		</fieldset>
	</div>
</div>
