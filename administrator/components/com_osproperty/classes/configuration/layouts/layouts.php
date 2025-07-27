<?php 
/*------------------------------------------------------------------------
# homepage.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Editor\Editor;

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
<div class="<?php echo $rowFluidClass; ?>">
	<div class="<?php echo $span6Class; ?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_DEFAULT_LAYOUT')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_RANDOM_FEATURE' );?>::<?php echo TextOs::_('SHOW_RANDOM_FEATURE_EXPLAIN'); ?>">
						<label for="configuration[show_random_feature]">
							<?php echo TextOs::_( 'SHOW_RANDOM_FEATURE' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_random_feature',$configs['show_random_feature']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_QUICK_SEARCH' );?>::<?php echo TextOs::_('SHOW_QUICK_SEARCH_EXPLAIN'); ?>">
						<label for="configuration[show_quick_search]">
							<?php echo TextOs::_( 'SHOW_QUICK_SEARCH' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_quick_search',$configs['show_quick_search']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_HOMEPAGE_BOX' );?>::<?php echo TextOs::_('SHOW_HOMEPAGE_BOX_EXPLAIN'); ?>">
						<label for="configuration[show_frontpage_box]">
							<?php echo TextOs::_( 'SHOW_HOMEPAGE_BOX' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_frontpage_box',$configs['show_frontpage_box']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Intro text Homepage' );?>::<?php echo TextOs::_('INTRO_TEXT_EXPLAIN'); ?>">
					<label for="configuration[introtext]">
						<?php echo TextOs::_( 'Intro text Homepage' ).':'; ?>
					</label>
				</span>
				<?php
				$translatable = Multilanguage::isEnabled() && count($languages);

				$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
				if (!isset($configs['introtext'])) $configs['introtext'] = '';
				$params = array( 'smilies'=> '0' ,
					'style'  => '1' ,
					'layer'  => '0' ,
					'table'  => '0' ,
					'clear_entities'=>'0'
				);

				if ($translatable)
				{
					echo HTMLHelper::_('bootstrap.startTabSet', 'homeintrotext', array('active' => 'general-page-introtext'));
						echo HTMLHelper::_('bootstrap.addTab', 'homeintrotext', 'general-page-introtext', Text::_('OS_GENERAL', true));
				}
				?>
			
				<div class="tab-pane active">
					<?php
					echo $editor->display( 'configuration[introtext]',  stripslashes($configs['introtext']) , '400', '200', '20', '20', false, null, null, null, $params );
					?>
				</div>
				<?php 
				if ($translatable)
				{
				?>
					<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
					<?php echo HTMLHelper::_('bootstrap.addTab', 'homeintrotext', 'translation-page-introtext', Text::_('OS_TRANSLATION', true)); ?>
					
					<div class="tab-content">			
						<?php	
							$i = 0;
							$activate_sef = $languages[0]->sef;
							echo HTMLHelper::_('bootstrap.startTabSet', 'languagetranslation', array('active' => 'translation-page-'.$activate_sef));
							foreach ($languages as $language)
							{												
								$sef = $language->sef;
								echo HTMLHelper::_('bootstrap.addTab', 'languagetranslation',  'translation-page-'.$sef, '<img src="'.Uri::root().'media/com_osproperty/flags/'.$sef.'.png" />');
								?>
								<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">	
									<?php
									if (!isset($configs['introtext_'.$sef])) $configs['introtext_'.$sef] = '';
									echo $editor->display( 'configuration[introtext_'.$sef.']',  stripslashes($configs['introtext_'.$sef]) , '400', '200', '20', '20', false, null, null, null, $params );
									?>
								</div>
								<?php
								echo HTMLHelper::_('bootstrap.endTabSet');
								$i++;		
							}
							echo HTMLHelper::_('bootstrap.endTabSet');
						?>
					</div>	
				</div>
				<?php
				}
				?>
			</div>
		</fieldset>

		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_LIST_PROPERTIES_SETTING')?></legend>
			
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show search form' );?>::<?php echo TextOs::_('Show search form explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Show search form' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('show_searchform',$configs['show_searchform']);
						?>
					</div>
				</div>
				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show price' );?>::<?php echo TextOs::_('Listing Show price explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show price' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_price',$configs['listing_show_price']);
						?>
					</div>
				</div>

				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show agent' );?>::<?php echo TextOs::_('Listing Show agent explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show agent' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_agent',$configs['listing_show_agent']);
						?>
					</div>
				</div>

				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show address' );?>::<?php echo TextOs::_('Listing Show address explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show address' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_address',$configs['listing_show_address']);
						?>
					</div>
				</div>


				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show view' );?>::<?php echo TextOs::_('Listing Show view explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show view' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_view',$configs['listing_show_view']);
						?>
					</div>
				</div>


				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show rating' );?>::<?php echo TextOs::_('Listing Show rating explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show rating' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_rating',$configs['listing_show_rating']);
						?>
					</div>
				</div>


				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nrooms' );?>::<?php echo TextOs::_('Listing Show nrooms explain'); ?>">
							<label for="checkbox_property_show_nrooms">
								<?php echo TextOs::_( 'Listing Show nrooms' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nrooms',$configs['listing_show_nrooms']);
						?>
					</div>
				</div>


				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nbedrooms' );?>::<?php echo TextOs::_('Listing Show nbedrooms explain'); ?>">
							<label for="checkbox_property_show_nbedrooms">
								<?php echo TextOs::_( 'Listing Show nbedrooms' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nbedrooms',$configs['listing_show_nbedrooms']);
						?>
					</div>
				</div>

				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nbathrooms' );?>::<?php echo TextOs::_('Listing Show nbathrooms explain'); ?>">
							<label for="checkbox_property_show_nbathrooms">
								<?php echo TextOs::_( 'Listing Show nbathrooms' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nbathrooms',$configs['listing_show_nbathrooms']);
						?>
					</div>
				</div>


				<div class="<?php echo $controlGroupClass; ?>">
					<div class="<?php echo $controlLabelClass; ?>">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show ncomments' );?>::<?php echo TextOs::_('Listing Show ncomments explain'); ?>">
							<label for="checkbox_property_show_ncomments">
								<?php echo TextOs::_( 'Listing Show ncomments' ).':'; ?>
							</label>
						</span>
					</div>
					<div class="<?php echo $controlsClass; ?>">
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_ncomments',$configs['listing_show_ncomments']);
						?>
					</div>
				</div>
		</fieldset>
	</div>
	<div class="<?php echo $span6Class; ?>">
		<fieldset class="form-horizontal options-form"> 
			<legend><?php echo TextOs::_('Category Settings')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Category layout' );?>::<?php echo TextOs::_('Number columns in the frontpage layout'); ?>">
						<label for="configuration[category_layout]">
							<?php echo TextOs::_( 'Category layout' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$category_layout_arr = array('One Column','Two Columns','Three Columns','Four Columns','Five Columns');
					$option_category_layout = [];
					$number_columns = 100;
					foreach ($category_layout_arr as $value => $text) {
						$option_category_layout[] = HTMLHelper::_('select.option',$value + 1,TextOs::_($text));
					}
					echo HTMLHelper::_('select.genericlist',$option_category_layout,'configuration[category_layout]','class="form-select input-large ilarge"','value','text',isset($configs['category_layout'])? $configs['category_layout']:0);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show description' );?>::<?php echo TextOs::_(''); ?>">
						<label for="checkbox_categories_show_description">
							<?php echo TextOs::_( 'Show description' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('categories_show_description',(int)$configs['categories_show_description']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show sub categories' );?>::<?php echo TextOs::_(''); ?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo TextOs::_( 'Show sub categories' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('categories_show_sub_categories',$configs['categories_show_sub_categories']);
					?>
				</div>
			</div>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Active RSS' );?>::<?php echo TextOs::_('Active RSS explain'); ?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo TextOs::_( 'Active RSS' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('active_rss',$configs['active_rss']);
					?>
				</div>
			</div>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ORDER_PROPERTIES_BY' );?>::<?php echo Text::_('OS_ORDER_PROPERTIES_BY_EXPLAIN'); ?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo Text::_( 'OS_ORDER_PROPERTIES_BY' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$default_sort_properties_by = $configs['default_sort_properties_by'];
					if($default_sort_properties_by == ""){
						$default_sort_properties_by = "a.id";
					}
					$orderbyArray = array('a.pro_name','a.ref','a.id','a.modified','a.price','a.isFeatured','a.ordering');
					$orderbyArray_labels = array(Text::_('OS_TITLE'),Text::_('Ref'),Text::_('OS_CREATED'),Text::_('OS_MODIFIED'),Text::_('OS_PRICE'),Text::_('OS_FEATURED'),Text::_('OS_ORDERING'));
					?>
					<select name="configuration[default_sort_properties_by]" class="form-select input-large ilarge">
						<?php
						for($i=0;$i<count($orderbyArray);$i++)
						{
							if($orderbyArray[$i] == $default_sort_properties_by)
							{
								$selected = "selected";
							}
							else
							{
								$selected = "";
							}
							?>
							<option value="<?php echo $orderbyArray[$i];?>" <?php echo $selected;?>><?php echo $orderbyArray_labels[$i];?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>


			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ORDER_PROPERTIES_TYPE' );?>::<?php echo Text::_('OS_ORDER_PROPERTIES_TYPE_EXPLAIN'); ?>">
						<label for="checkbox_categories_show_sub_categories">
							<?php echo Text::_( 'OS_ORDER_PROPERTIES_TYPE' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$default_sort_properties_type = $configs['default_sort_properties_type'];
					if($default_sort_properties_type == ""){
						$default_sort_properties_type = "desc";
					}
					$ordertypeArray = array('desc','asc');
					$ordertypeArray_labels = array(Text::_('OS_DESCENDING'),Text::_('OS_ASCENDING'));
					?>
					<select name="configuration[default_sort_properties_type]" class="form-select input-large ilarge">
						<?php
						for($i=0;$i<count($ordertypeArray);$i++){
							if($ordertypeArray[$i] == $default_sort_properties_type){
								$selected = "selected";
							}else{
								$selected = "";
							}
							?>
							<option value="<?php echo $ordertypeArray[$i];?>" <?php echo $selected;?>><?php echo $ordertypeArray_labels[$i];?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo TextOs::_('Property Details Settings')?></legend>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SELECT_THEME_FOR_PROPERTY_DETAILS_PAGE' );?>::<?php echo Text::_('OS_SELECT_THEME_FOR_PROPERTY_DETAILS_PAGE_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_SELECT_THEME_FOR_PROPERTY_DETAILS_PAGE' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$db = Factory::getDbo();
					$db->setQuery("Select name as value, title as text from #__osrs_themes");
					$themes			= $db->loadObjectList();
					$themeArr		= [];
					$themeArr[]		= HTMLHelper::_('select.option','',Text::_('OS_INHERIT_FROM_GLOBAL_CONFIGURATION'));
					$themeArr		= array_merge($themeArr, $themes);
					echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$themeArr,'configuration[property_details_theme]','class=" imedium form-select"','value','text',$configs['property_details_theme']));
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_REQUEST_MORE_DETAILS_FORM' );?>::<?php echo TextOs::_('Show request more details tab explain'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_SHOW_REQUEST_MORE_DETAILS_FORM' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_request_more_details',$configs['show_request_more_details']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'Allowed subjects' );?>::<?php echo Text::_('Select subjects that will be shown in Request More Details form'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'Allowed subjects' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$allowed_subjects = $configs['allowed_subjects'];
					$allowed_subjects = explode(",",$allowed_subjects);
					$optionArr = [];
					for($i=1;$i<=7;$i++){
						$optionArr[] = HTMLHelper::_('select.option',$i,Text::_('OS_REQUEST_'.$i));
					}
					echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$optionArr,'allowed_subjects[]','multiple class=" imedium"','value','text',$allowed_subjects));
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_TERM_AND_CONDITION_IN_REQUEST_PAGE_EXPLAIN'); ?>">
						  <label for="checkbox_auto_approval_agent_registration">
							  <?php echo Text::_( 'OS_SHOW_TERM_AND_CONDITION' ).':'; ?>
						  </label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('request_term_condition',intval($configs['request_term_condition']));
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SELECT_TERM_AND_CONDITION_ARTICLE'); ?>">
						  <label for="checkbox_auto_approval_agent_registration">
							  <?php echo Text::_( 'OS_SELECT_ARTICLE' ).':'; ?>
						  </label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php echo OSPHelper::getArticleInput($configs['request_article_id'], 'configuration[request_article_id]'); ?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_COPY_ADMIN' );?>::<?php echo Text::_('OS_COPY_ADMIN_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_COPY_ADMIN' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('copy_admin',(int)$configs['copy_admin']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_LOG_REQUESTS' );?>::<?php echo Text::_('OS_LOG_REQUESTS_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_LOG_REQUESTS' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('log_request',(int)$configs['log_request']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM' );?>::<?php echo Text::_('OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('captcha_in_request_more_details',(int)$configs['captcha_in_request_more_details']);
					?>
				</div>
			</div>
			<!--
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS' );?>::<?php echo Text::_('OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('user_recaptcha_in_request_more_details',(int)$configs['user_recaptcha_in_request_more_details']);
					?>
				</div>
			</div>
			-->
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_TURN_ON_TELL_FRIEND_FORM' );?>::<?php echo TextOs::_('MAIL_TO_FRIEND_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_TURN_ON_TELL_FRIEND_FORM' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('property_mail_to_friends',$configs['property_mail_to_friends']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM' );?>::<?php echo Text::_('OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('captcha_in_tell_friend_form',(int)$configs['captcha_in_tell_friend_form']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_PASS_CAPTCHA_WITH_LOGGED_USER' );?>::<?php echo Text::_('OS_PASS_CAPTCHA_WITH_LOGGED_USER_EXPLAIN'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo Text::_( 'OS_PASS_CAPTCHA_WITH_LOGGED_USER' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('pass_captcha_with_logged_user',(int)$configs['pass_captcha_with_logged_user']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show Print' );?>::<?php echo TextOs::_('SHOW_PRINT_EXPLAIN'); ?>">
						<label for="checkbox_property_show_print">
							<?php echo TextOs::_( 'Show Print' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('property_show_print',$configs['property_show_print']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Save to favories' );?>::<?php echo TextOs::_('SAVE_TO_FAVORIES_EXPLAIN'); ?>">
						<label for="checkbox_property_save_to_favories">
							<?php echo TextOs::_( 'Save to favories' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('property_save_to_favories',$configs['property_save_to_favories']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show get direction icon' );?>::<?php echo TextOs::_('Show get direction icon explain'); ?>">
						<label for="checkbox_property_get_direction">
							<?php echo TextOs::_( 'Show get direction icon' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_getdirection',$configs['show_getdirection']);
					?>
				</div>
			</div>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show compare icon' );?>::<?php echo TextOs::_('Show compare icon explain'); ?>">
						<label for="checkbox_property_show_compare_task">
							<?php echo TextOs::_( 'Show compare icon' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_compare_task',$configs['show_compare_task']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show gallery tab' );?>::<?php echo TextOs::_('Show gallery tab explain'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo TextOs::_( 'Show gallery tab' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_gallery_tab',$configs['show_gallery_tab']);
					?>
				</div>
			</div>
			
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show agent information tab' );?>::<?php echo TextOs::_('Show agent information tab explain'); ?>">
						<label for="checkbox_property_mail_to_friends">
							<?php echo TextOs::_( 'Show agent information tab' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_agent_details',$configs['show_agent_details']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show amenities group'); ?>::<?php echo TextOs::_( 'Show amenities group explain'); ?>">
						<label for="checkbox_property_save_to_favories">
							<?php echo TextOs::_( 'Show amenities group' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_amenity_group',$configs['show_amenity_group']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_UNSELCTED_AMENITIES_EXPLAIN'); ?>">
						<label for="checkbox_property_save_to_favories">
							<?php echo Text::_( 'OS_SHOW_UNSELCTED_AMENITIES' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_unselected_amenities',$configs['show_unselected_amenities']);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_AMENITES' );?>">
						<label for="configuration[category_layout]">
							<?php echo Text::_( 'OS_SHOW_AMENITES' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					$category_layout_arr = array('Two Columns','Three Columns');
					$option_category_layout = [];
					foreach ($category_layout_arr as $value => $text) {
						$option_category_layout[] = HTMLHelper::_('select.option',$value + 1,TextOs::_($text));
					}
					echo HTMLHelper::_('select.genericlist',$option_category_layout,'configuration[amenities_layout]','class="form-select input-large ilarge"','value','text',isset($configs['amenities_layout'])? $configs['amenities_layout']:1);
					?>
				</div>
			</div>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show feature fields group'); ?>::<?php echo TextOs::_( 'Show feature fields group explain'); ?>">
						<label for="checkbox_property_save_to_favories">
							<?php echo TextOs::_( 'Show feature fields group' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_feature_group',$configs['show_feature_group']);
					?>
				</div>
			</div>

			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlLabelClass; ?>">
					<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show neighborhood fields group'); ?>::<?php echo TextOs::_( 'Show neighborhood fields group explain'); ?>">
						<label for="checkbox_property_save_to_favories">
							<?php echo TextOs::_( 'Show neighborhood fields group' ).':'; ?>
						</label>
					</span>
				</div>
				<div class="<?php echo $controlsClass; ?>">
					<?php
					OspropertyConfiguration::showCheckboxfield('show_neighborhood_group',$configs['show_neighborhood_group']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('Page Navigation')?></legend>
			<div class="control-group">
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('overrides_pagination',Text::_( 'OS_OVERRIDES_JOOMLA_PAGINATION' ), Text::_('OS_OVERRIDES_JOOMLA_PAGINATION_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					OspropertyConfiguration::showCheckboxfield('overrides_pagination',$configs['overrides_pagination']);
					?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('general_number_properties_per_page', Text::_( 'Number items per page' ), Text::_('Number of items to show per page at the front-end')); ?>
				</div>
				<div class="controls">
					<input type="text" class="<?php echo $inputMiniClass; ?>" size="10" name="configuration[general_number_properties_per_page]" value="<?php echo isset($configs['general_number_properties_per_page'])? $configs['general_number_properties_per_page']:''; ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('fix_page_navigation_issue',Text::_( 'OS_FIX_PAGINATION_ISSUE_IN_LISTING_PAGE' ), Text::_('OS_FIX_PAGINATION_ISSUE_IN_LISTING_PAGE_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					OspropertyConfiguration::showCheckboxfield('fix_page_navigation_issue',$configs['fix_page_navigation_issue']);
					?>
				</div>
			</div>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_FRONTEND_PROPERTY_MODIFICATION')?></legend>
			<div class="control-group">
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('frontend_upload_type',Text::_( 'OS_FRONTEND_UPLOAD' ), Text::_('OS_FRONTEND_UPLOAD_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					OspropertyConfiguration::showCheckboxfield('frontend_upload_type',$configs['frontend_upload_type'],'Ajax Upload','Standard Upload');
					?>
				</div>
			</div>
		</fieldset>
	</div>
</div>