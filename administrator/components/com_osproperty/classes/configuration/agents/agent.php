<?php 
/*------------------------------------------------------------------------
# agent.php - Ossolution Property
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
use Joomla\CMS\Component\ComponentHelper;

$db = Factory::getDbo();
$rowFluidClass		= $bootstrapHelper->getClassMapping('row-fluid');
$span12Class		= $bootstrapHelper->getClassMapping('span12');
$span6Class			= $bootstrapHelper->getClassMapping('span6');
$controlGroupClass  = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass  = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	    = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass    = $bootstrapHelper->getClassMapping('input-large');
?>
<div class="<?php echo $rowFluidClass;?>">
	<div class="<?php echo $span6Class;?>">
		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_SELECT_USER_TYPES')?></legend>
			<?php echo Text::_('OS_SELECT_USER_TYPES_EXPLAIN'); ?>
			<BR />
			<?php
			$user_types = array();
			$userTypeArr = array(Text::_('OS_AGENT'), Text::_('OS_OWNER'), Text::_('OS_REALTOR'), Text::_('OS_BROKER'), Text::_('OS_BUILDER'), Text::_('OS_LANDLORD'), Text::_('OS_SELLER'));
			for($i=0;$i<count($userTypeArr);$i++)
			{
				$tmp			= new \stdClass();
				$tmp->value		= $i;
				$tmp->text		= $userTypeArr[$i];
				$user_types[$i] = $tmp;
			}


			$checkbox_user_types = array();
			if (isset($configs['user_types']))
			{
				$checkbox_user_types = explode(',',$configs['user_types']);
			}
			if($configs['user_types'] == ""){
				$checkbox_user_types[] = 0;
				$checkbox_user_types[] = 1;
			}
			echo OSPHelper::getChoicesJsSelect(HTMLHelper::_('select.genericlist',$user_types,'configuration[user_types][]','multiple class="inputbox form-select "','value','text',$checkbox_user_types));
			?>
			<BR /><BR />
			<?php echo Text::_('OS_SELECT_DEFAULT_USER_TYPE_REGISTER'); ?>
			<BR />
			<?php
			$usertypelabels = array(Text::_('OS_AGENT'),Text::_('OS_OWNER'),Text::_('OS_REALTOR'),Text::_('OS_BROKER'),Text::_('OS_BUILDER'),Text::_('OS_LANDLORD'),Text::_('OS_SELLER'));
			if($configs['user_types'] == ""){
				$configs['user_types'] = "0,1";
			}
			$user_types_array = $configs['user_types'];
			$user_types_array = explode(",",$user_types_array);
			?>
			<select class="input-medium ilarge form-select" name="configuration[default_user_type]">
				<?php 
				for($i=0;$i<count($user_types_array);$i++){
					if($user_types_array[$i] == $configs['default_user_type']){
						$selected = "selected";
					}else{
						$selected = "";
					}
					?>
					<option value="<?php echo $user_types_array[$i]?>" <?php echo $selected ;?>><?php echo $usertypelabels[$user_types_array[$i]];?></option>
					<?php
				}
				?>
			</select>
		</fieldset>

		<fieldset class="form-horizontal options-form">
			<legend><?php echo Text::_('OS_USER_FIELDS')?></legend>
			<br />
			<table width="100%" class="admintable">
				<?php 
					$Agent_array = array('Show agent image','Show agent address','Show agent email','Show agent fax','Show agent mobile','Show agent phone','Show Agent MSN','Show Agent Skype'
					,'Show Agent Linkin','Show Agent Gplus','Show Agent Facebook','Show Agent Twitter','Show License');
					foreach ($Agent_array as $agent) {
						$name = str_replace(' ','_',strtolower($agent));
						$value = isset($configs[$name])? $configs[$name]:0;
					?>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( $agent );?>">
								<label for="configuration[<?php echo $name; ?>]">
									<?php echo TextOs::_( $agent).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							if (version_compare(JVERSION, '3.0', 'lt')) 
							{
								//echo HTMLHelper::_('select.booleanlist','configuration['.$name.']','',$value);
								$optionArr = array();
								$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
								$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
								echo HTMLHelper::_('select.genericlist',$optionArr,'configuration['.$name.']','class="ishort form-select input-mini"','value','text',$value);
							}
							else
							{
								OspropertyConfiguration::showCheckboxfield($name,$value);
							} 
							?>
						</div>
					</div>
					<?php 	
					}
				?>
			</table>
		</fieldset>

	</div>
	<div class="<?php echo $span6Class;?>">
		
			<fieldset class="form-horizontal options-form">
				<legend><?php echo Text::_('OS_USER_REGISTRATION')?></legend>
				<?php echo Text::_('OS_USER_REGISTRATION_EXPLAIN')?>
				<br />
				<table width="100%" class="admintable">
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Agent registered' );?>::<?php echo TextOs::_('Would you like to allow the registered members can register to become agent members.'); ?>">
								  <label for="checkbox_general_agent_registered">
									  <?php echo TextOs::_( 'Agent registered' ).':'; ?>
								  </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('allow_agent_registration',intval($configs['allow_agent_registration']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_AGENT_USER_GROUP' );?>::<?php echo Text::_('OS_AGENT_USER_GROUP_EXPLAIN'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo Text::_( 'OS_AGENT_USER_GROUP' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							$db 		= Factory::getDbo();
							$params 	= ComponentHelper::getParams('com_users');
							$register_usertype = $params->get('new_usertype');
							$db->setQuery("Select id as value, title as text from #__usergroups where id <> '$register_usertype'");
							$groups 	= $db->loadObjectList();
							$groupArr 	= array();
							$groupArr[] = HTMLHelper::_('select.option','',Text::_("OS_SELECT_ADDITIONAL_GROUP"));
							$groupArr   = array_merge($groupArr,$groups);
							echo HTMLHelper::_('select.genericlist',$groupArr,'configuration[agent_joomla_group_id]','class="input-large ilarge form-select"','value','text',$configs['agent_joomla_group_id']);
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Auto approval agent register request' );?>::<?php echo TextOs::_('Would you like to allow auto approval the agent register request.'); ?>">
								  <label for="checkbox_auto_approval_agent_registration">
									  <?php echo TextOs::_( 'Auto approval agent register request' ).':'; ?>
								  </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('auto_approval_agent_registration',intval($configs['auto_approval_agent_registration']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_LOGIN_BOX_IN_REGISTRATION_FORM_EXPLAIN'); ?>">
								  <label for="checkbox_auto_approval_agent_registration">
									  <?php echo Text::_( 'OS_SHOW_LOGIN_BOX_IN_REGISTRATION_FORM' ).':'; ?>
								  </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('show_agent_login_box',intval($configs['show_agent_login_box']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'Using Joomla Captcha' );?>::In case you want to use reCaptcha, you need to publish the plugin :reCaptcha at Plugins manager. You also need to register Public and Private key">
								<label for="configuration[Captcha_agent_register]">
									<?php echo TextOs::_( 'Captcha_agent_register').':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('captcha_agent_register',intval($configs['captcha_agent_register']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_TERM_AND_CONDITION_IN_REGISTRATION_PAGE_EXPLAIN'); ?>">
								  <label for="checkbox_auto_approval_agent_registration">
									  <?php echo Text::_( 'OS_SHOW_TERM_AND_CONDITION' ).':'; ?>
								  </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('agent_term_condition',intval($configs['agent_term_condition']));
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
							<?php echo OSPHelper::getArticleInput($configs['agent_article_id'], 'configuration[agent_article_id]'); ?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
								<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_USE_EMAIL_AS_USERNAME_EXPLAIN'); ?>">
									<label for="checkbox_property_show_rating">
										<?php echo Text::_( 'OS_USE_EMAIL_AS_USERNAME' ).':'; ?>
									</label>
								</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('use_email_as_agent_username',intval($configs['use_email_as_agent_username']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('Enabling this configur option to force an agent/owner to be a Joomla user'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo Text::_( 'OS_AGENT_IS_ALSO_JOOMLAUSER' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('joomlauser',intval($configs['joomlauser']), Text::_('JYES'),Text::_('OS_OPTIONAL'));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_REGISTRATION_COMPLETED_REDIRECTION' );?>::<?php echo Text::_('Enter the link that user will be redirected after completing Registration progress. Leave it empty then OS Property will redirect user to Home page'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo Text::_( 'OS_REGISTRATION_COMPLETED_REDIRECTION' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" name="configuration[agent_redirect_link]" class="input-large form-control ilarge" value="<?php echo $configs['agent_redirect_link'];?>" />
						</div>
					</div>
				</table>
			</fieldset>
		
			<fieldset class="form-horizontal options-form">
				<legend><?php echo Text::_('OS_FRONTEND_SETTING')?></legend>
				<table width="100%" class="admintable">
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_SEARCH_FORM_IN_LIST_AGENTS_EXPLAIN'); ?>">
								<label for="checkbox_property_show_rating">
									<?php echo Text::_( 'OS_SHOW_SEARCH_FORM_IN_LIST_AGENTS' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('show_agent_search_tab',intval($configs['show_agent_search_tab']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_ONLY_SHOW_AGENTS_HAVE_PROPERTIES_EXPLAIN'); ?>">
								<label for="checkbox_property_show_rating">
									<?php echo Text::_( 'OS_ONLY_SHOW_AGENTS_HAVE_PROPERTIES' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('show_agent_with_properties',intval($configs['show_agent_with_properties']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_('OS_SHOW_ALPHABET_FILTERING_IN_LIST_AGENTS_EXPLAIN'); ?>">
								<label for="checkbox_property_show_rating">
									<?php echo Text::_( 'OS_SHOW_ALPHABET_FILTERING_IN_LIST_AGENTS' ).':'; ?>
								</label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php
							OspropertyConfiguration::showCheckboxfield('show_alphabet',intval($configs['show_alphabet']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Agent listings' );?>::<?php echo TextOs::_('Would you like to allow agent members to list properties for sale via the front-end listings panel?'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo TextOs::_( 'Agent listings' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							OspropertyConfiguration::showCheckboxfield('general_agent_listings',intval($configs['general_agent_listings']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show most rated' );?>::<?php echo TextOs::_('Show most rated explain'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo TextOs::_( 'Show most rated' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							OspropertyConfiguration::showCheckboxfield('agent_mostrated',intval($configs['agent_mostrated']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show most viewed' );?>::<?php echo TextOs::_('Show most viewed explain'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo TextOs::_( 'Show most viewed' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							OspropertyConfiguration::showCheckboxfield('agent_mostviewed',intval($configs['agent_mostviewed']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show agent contact' );?>::<?php echo TextOs::_('Show agent contact explain'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo TextOs::_( 'Show agent contact' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							OspropertyConfiguration::showCheckboxfield('show_agent_contact',intval($configs['show_agent_contact']));
							?>
						</div>
					</div>
					<div class="<?php echo $controlGroupClass; ?>">
						<div class="<?php echo $controlLabelClass; ?>">
							<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show agent properties' );?>::<?php echo TextOs::_('Show agent properties explain'); ?>">
								 <label for="checkbox_general_agent_listings">
									 <?php echo TextOs::_( 'Show agent properties' ).':'; ?>
								 </label>
							</span>
						</div>
						<div class="<?php echo $controlsClass; ?>">
							<?php 
							OspropertyConfiguration::showCheckboxfield('show_agent_properties',intval($configs['show_agent_properties']));
							?>
						</div>
					</div>
					
				</table>
			</fieldset>
		</div>
	</div>