<?php 
/*------------------------------------------------------------------------
# homepage.php - Ossolution Property
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
<div class="<?php echo $rowFluidClass; ?>">
	<div class="<?php echo $span6Class; ?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_DEFAULT_LAYOUT')?></legend>
			<table width="100%" class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_RANDOM_FEATURE' );?>::<?php echo TextOs::_('SHOW_RANDOM_FEATURE_EXPLAIN'); ?>">
							<label for="configuration[show_random_feature]">
								<?php echo TextOs::_( 'SHOW_RANDOM_FEATURE' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_random_feature',$configs['show_random_feature']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_QUICK_SEARCH' );?>::<?php echo TextOs::_('SHOW_QUICK_SEARCH_EXPLAIN'); ?>">
							<label for="configuration[show_quick_search]">
								<?php echo TextOs::_( 'SHOW_QUICK_SEARCH' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_quick_search',$configs['show_quick_search']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'SHOW_HOMEPAGE_BOX' );?>::<?php echo TextOs::_('SHOW_HOMEPAGE_BOX_EXPLAIN'); ?>">
							<label for="configuration[show_frontpage_box]">
								<?php echo TextOs::_( 'SHOW_HOMEPAGE_BOX' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_frontpage_box',$configs['show_frontpage_box']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap" valign="top" colspan="2">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Intro text Homepage' );?>::<?php echo TextOs::_('INTRO_TEXT_EXPLAIN'); ?>">
							<label for="configuration[introtext]">
								<?php echo TextOs::_( 'Intro text Homepage' ).':'; ?>
							</label>
						</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$translatable = JLanguageMultilang::isEnabled() && count($languages);

						$editor = JEditor::getInstance(Factory::getConfig()->get('editor'));
						if (!isset($configs['introtext'])) $configs['introtext'] = '';
						$params = array( 'smilies'=> '0' ,
							'style'  => '1' ,
							'layer'  => '0' ,
							'table'  => '0' ,
							'clear_entities'=>'0'
						);

						if ($translatable)
						{
						?>
						<ul class="nav nav-tabs">
							<li class="active"><a href="#general-page-introtext" data-toggle="tab"><?php echo Text::_('OS_GENERAL'); ?></a></li>
							<li><a href="#translation-page" data-toggle="tab"><?php echo Text::_('OS_TRANSLATION'); ?></a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="general-page-introtext">
								<?php
								}
								echo $editor->display( 'configuration[introtext]',  stripslashes($configs['introtext']) , '400', '200', '20', '20', false, null, null, null, $params );
								?>
								<?php
								if ($translatable)
								{
								?>
							</div>
							<div class="tab-pane" id="translation-page">
								<ul class="nav nav-tabs">
									<?php
									$i = 0;
									foreach ($languages as $language) {
										$sef = $language->sef;
										?>
										<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#translation-page-<?php echo $sef; ?>" data-toggle="tab"><?php echo $language->title; ?>
												<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" /></a></li>
										<?php
										$i++;
									}
									?>
								</ul>
								<div class="tab-content">
									<?php
									$i = 0;
									foreach ($languages as $language)
									{
										$sef = $language->sef;
										?>
										<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">
											<?php
											if (!isset($configs['introtext_'.$sef])) $configs['introtext_'.$sef] = '';
											echo $editor->display( 'configuration[introtext_'.$sef.']',  stripslashes($configs['introtext_'.$sef]) , '400', '200', '20', '20', false, null, null, null, $params );
											?>
										</div>
										<?php
										$i++;
									}
									?>
								</div>
							</div>
							<?php
							}
							?>
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_LIST_PROPERTIES_SETTING')?></legend>
			<table  width="100%" class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show search form' );?>::<?php echo TextOs::_('Show search form explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Show search form' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_searchform',$configs['show_searchform']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show price' );?>::<?php echo TextOs::_('Listing Show price explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show price' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_price',$configs['listing_show_price']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show agent' );?>::<?php echo TextOs::_('Listing Show agent explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show agent' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_agent',$configs['listing_show_agent']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show address' );?>::<?php echo TextOs::_('Listing Show address explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show address' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_address',$configs['listing_show_address']);
						?>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show view' );?>::<?php echo TextOs::_('Listing Show view explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show view' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_view',$configs['listing_show_view']);
						?>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show rating' );?>::<?php echo TextOs::_('Listing Show rating explain'); ?>">
							<label for="checkbox_property_show_rating">
								<?php echo TextOs::_( 'Listing Show rating' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_rating',$configs['listing_show_rating']);
						?>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nrooms' );?>::<?php echo TextOs::_('Listing Show nrooms explain'); ?>">
							<label for="checkbox_property_show_nrooms">
								<?php echo TextOs::_( 'Listing Show nrooms' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nrooms',$configs['listing_show_nrooms']);
						?>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nbedrooms' );?>::<?php echo TextOs::_('Listing Show nbedrooms explain'); ?>">
							<label for="checkbox_property_show_nbedrooms">
								<?php echo TextOs::_( 'Listing Show nbedrooms' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nbedrooms',$configs['listing_show_nbedrooms']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show nbathrooms' );?>::<?php echo TextOs::_('Listing Show nbathrooms explain'); ?>">
							<label for="checkbox_property_show_nbathrooms">
								<?php echo TextOs::_( 'Listing Show nbathrooms' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_nbathrooms',$configs['listing_show_nbathrooms']);
						?>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Listing Show ncomments' );?>::<?php echo TextOs::_('Listing Show ncomments explain'); ?>">
							<label for="checkbox_property_show_ncomments">
								<?php echo TextOs::_( 'Listing Show ncomments' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('listing_show_ncomments',$configs['listing_show_ncomments']);
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="<?php echo $span6Class; ?>">
		<fieldset class="form-horizontal options-form"> 
			<legend><?php echo TextOs::_('Category Settings')?></legend>
			<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Category layout' );?>::<?php echo TextOs::_('Number columns in the frontpage layout'); ?>">
							<label for="configuration[category_layout]">
								<?php echo TextOs::_( 'Category layout' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						$category_layout_arr = array('One Column','Two Columns','Three Columns','Four Columns','Five Columns');
						$option_category_layout = array();
						$number_columns = 100;
						foreach ($category_layout_arr as $value => $text) {
							$option_category_layout[] = HTMLHelper::_('select.option',$value + 1,TextOs::_($text));
						}
						echo HTMLHelper::_('select.genericlist',$option_category_layout,'configuration[category_layout]','class="chosen inputbox"','value','text',isset($configs['category_layout'])? $configs['category_layout']:0);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show description' );?>::<?php echo TextOs::_(''); ?>">
							<label for="checkbox_categories_show_description">
								<?php echo TextOs::_( 'Show description' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('categories_show_description',(int)$configs['categories_show_description']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show sub categories' );?>::<?php echo TextOs::_(''); ?>">
							<label for="checkbox_categories_show_sub_categories">
								<?php echo TextOs::_( 'Show sub categories' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('categories_show_sub_categories',$configs['categories_show_sub_categories']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Active RSS' );?>::<?php echo TextOs::_('Active RSS explain'); ?>">
							<label for="checkbox_categories_show_sub_categories">
								<?php echo TextOs::_( 'Active RSS' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('active_rss',$configs['active_rss']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ORDER_PROPERTIES_BY' );?>::<?php echo Text::_('OS_ORDER_PROPERTIES_BY_EXPLAIN'); ?>">
							<label for="checkbox_categories_show_sub_categories">
								<?php echo Text::_( 'OS_ORDER_PROPERTIES_BY' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						$default_sort_properties_by = $configs['default_sort_properties_by'];
						if($default_sort_properties_by == ""){
							$default_sort_properties_by = "a.id";
						}
						$orderbyArray = array('a.pro_name','a.ref','a.id','a.modified','a.price','a.isFeatured');
						$orderbyArray_labels = array(Text::_('OS_TITLE'),Text::_('Ref'),Text::_('OS_CREATED'),Text::_('OS_MODIFIED'),Text::_('OS_PRICE'),Text::_('OS_FEATURED'));
						?>
						<select name="configuration[default_sort_properties_by]" class="<?php echo $inputLargeClass; ?>">
							<?php
							for($i=0;$i<count($orderbyArray);$i++){
								if($orderbyArray[$i] == $default_sort_properties_by){
									$selected = "selected";
								}else{
									$selected = "";
								}
								?>
								<option value="<?php echo $orderbyArray[$i];?>" <?php echo $selected;?>><?php echo $orderbyArray_labels[$i];?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>


				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ORDER_PROPERTIES_TYPE' );?>::<?php echo Text::_('OS_ORDER_PROPERTIES_TYPE_EXPLAIN'); ?>">
							<label for="checkbox_categories_show_sub_categories">
								<?php echo Text::_( 'OS_ORDER_PROPERTIES_TYPE' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						$default_sort_properties_type = $configs['default_sort_properties_type'];
						if($default_sort_properties_type == ""){
							$default_sort_properties_type = "desc";
						}
						$ordertypeArray = array('desc','asc');
						$ordertypeArray_labels = array(Text::_('OS_DESCENDING'),Text::_('OS_ASCENDING'));
						?>
						<select name="configuration[default_sort_properties_type]" class="<?php echo $inputLargeClass; ?>">
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
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="form-horizontal options-form">
			<legend><?php echo TextOs::_('Property Details Settings')?></legend>
			<table  width="100%" class="admintable">
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show request more details tab' );?>::<?php echo TextOs::_('Show request more details tab explain'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo TextOs::_( 'Show request more details tab' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_request_more_details',$configs['show_request_more_details']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'Allowed subjects' );?>::<?php echo Text::_('Select subjects that will be shown in Request More Details form'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'Allowed subjects' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						$allowed_subjects = $configs['allowed_subjects'];
						$allowed_subjects = explode(",",$allowed_subjects);
						$optionArr = array();
						for($i=1;$i<=7;$i++){
							$optionArr[] = HTMLHelper::_('select.option',$i,Text::_('OS_REQUEST_'.$i));
						}
						echo HTMLHelper::_('select.genericlist',$optionArr,'allowed_subjects[]','multiple class="chosen"','value','text',$allowed_subjects);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_TERM_AND_CONDITION_IN_REQUEST_PAGE_EXPLAIN'); ?>">
							  <label for="checkbox_auto_approval_agent_registration">
								  <?php echo Text::_( 'OS_SHOW_TERM_AND_CONDITION' ).':'; ?>
							  </label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('request_term_condition',intval($configs['request_term_condition']));
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SELECT_TERM_AND_CONDITION_ARTICLE'); ?>">
							  <label for="checkbox_auto_approval_agent_registration">
								  <?php echo Text::_( 'OS_SELECT_ARTICLE' ).':'; ?>
							  </label>
						</span>
					</td>
					<td>
						<?php
						$sql = 'SELECT id, title FROM #__content WHERE `state` = 1 ORDER BY title ';
						$db->setQuery($sql) ;
						$rows = $db->loadObjectList();
						$options = array() ;
						$options[] = HTMLHelper::_('select.option', '' ,'', 'id', 'title') ;
						$options = array_merge($options, $rows) ;		
						echo HTMLHelper::_('select.genericlist', $options, 'configuration[request_article_id]', ' class="input-large chosen form-select" ', 'id', 'title', $configs['request_article_id']) ;
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_COPY_ADMIN' );?>::<?php echo Text::_('OS_COPY_ADMIN_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_COPY_ADMIN' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('copy_admin',(int)$configs['copy_admin']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_LOG_REQUESTS' );?>::<?php echo Text::_('OS_LOG_REQUESTS_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_LOG_REQUESTS' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('log_request',(int)$configs['log_request']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM' );?>::<?php echo Text::_('OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_REQUEST_MORE_DETAILS_FORM' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('captcha_in_request_more_details',(int)$configs['captcha_in_request_more_details']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS' );?>::<?php echo Text::_('OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_USE_GOOGLE_RECAPTCHA_IN_REQUEST_MORE_DETAILS' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('user_recaptcha_in_request_more_details',(int)$configs['user_recaptcha_in_request_more_details']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_TURN_ON_TELL_FRIEND_FORM' );?>::<?php echo TextOs::_('MAIL_TO_FRIEND_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_TURN_ON_TELL_FRIEND_FORM' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('property_mail_to_friends',$configs['property_mail_to_friends']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM' );?>::<?php echo Text::_('OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_SHOW_CAPTCHA_IN_TELL_FRIEND_FORM' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('captcha_in_tell_friend_form',(int)$configs['captcha_in_tell_friend_form']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_PASS_CAPTCHA_WITH_LOGGED_USER' );?>::<?php echo Text::_('OS_PASS_CAPTCHA_WITH_LOGGED_USER_EXPLAIN'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo Text::_( 'OS_PASS_CAPTCHA_WITH_LOGGED_USER' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('pass_captcha_with_logged_user',(int)$configs['pass_captcha_with_logged_user']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show Print' );?>::<?php echo TextOs::_('SHOW_PRINT_EXPLAIN'); ?>">
							<label for="checkbox_property_show_print">
								<?php echo TextOs::_( 'Show Print' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('property_show_print',$configs['property_show_print']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Save to favories' );?>::<?php echo TextOs::_('SAVE_TO_FAVORIES_EXPLAIN'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo TextOs::_( 'Save to favories' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('property_save_to_favories',$configs['property_save_to_favories']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show get direction icon' );?>::<?php echo TextOs::_('Show get direction icon explain'); ?>">
							<label for="checkbox_property_get_direction">
								<?php echo TextOs::_( 'Show get direction icon' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_getdirection',$configs['show_getdirection']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show compare icon' );?>::<?php echo TextOs::_('Show compare icon explain'); ?>">
							<label for="checkbox_property_show_compare_task">
								<?php echo TextOs::_( 'Show compare icon' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_compare_task',$configs['show_compare_task']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show gallery tab' );?>::<?php echo TextOs::_('Show gallery tab explain'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo TextOs::_( 'Show gallery tab' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_gallery_tab',$configs['show_gallery_tab']);
						?>
					</td>
				</tr>
				
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show agent information tab' );?>::<?php echo TextOs::_('Show agent information tab explain'); ?>">
							<label for="checkbox_property_mail_to_friends">
								<?php echo TextOs::_( 'Show agent information tab' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_agent_details',$configs['show_agent_details']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show amenities group'); ?>::<?php echo TextOs::_( 'Show amenities group explain'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo TextOs::_( 'Show amenities group' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_amenity_group',$configs['show_amenity_group']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_UNSELCTED_AMENITIES_EXPLAIN'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo Text::_( 'OS_SHOW_UNSELCTED_AMENITIES' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_unselected_amenities',$configs['show_unselected_amenities']);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_SHOW_AMENITES' );?>">
							<label for="configuration[category_layout]">
								<?php echo Text::_( 'OS_SHOW_AMENITES' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						$category_layout_arr = array('Two Columns','Three Columns');
						$option_category_layout = array();
						foreach ($category_layout_arr as $value => $text) {
							$option_category_layout[] = HTMLHelper::_('select.option',$value + 1,TextOs::_($text));
						}
						echo HTMLHelper::_('select.genericlist',$option_category_layout,'configuration[amenities_layout]','class="chosen inputbox"','value','text',isset($configs['amenities_layout'])? $configs['amenities_layout']:1);
						?>
					</td>
				</tr>
				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show feature fields group'); ?>::<?php echo TextOs::_( 'Show feature fields group explain'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo TextOs::_( 'Show feature fields group' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_feature_group',$configs['show_feature_group']);
						?>
					</td>
				</tr>

				<tr>
					<td class="key" nowrap="nowrap">
						<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show neighborhood fields group'); ?>::<?php echo TextOs::_( 'Show neighborhood fields group explain'); ?>">
							<label for="checkbox_property_save_to_favories">
								<?php echo TextOs::_( 'Show neighborhood fields group' ).':'; ?>
							</label>
						</span>
					</td>
					<td>
						<?php
						OspropertyConfiguration::showCheckboxfield('show_neighborhood_group',$configs['show_neighborhood_group']);
						?>
					</td>
				</tr>
			</table>
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