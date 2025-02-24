<?php 
/*------------------------------------------------------------------------
# company.php - Ossolution Property
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

$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass	   = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass   = $bootstrapHelper->getClassMapping('input-large');
?>

<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('Company Setting')?></legend>
	
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Company register' );?>::<?php echo TextOs::_('Company register explain'); ?>">
                      <label for="checkbox_general_agent_registered">
                          <?php echo TextOs::_( 'Company register' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('company_register',intval($configs['company_register']));
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
				OspropertyConfiguration::showCheckboxfield('show_company_login_box',intval($configs['show_company_login_box']));
				?>
			</div>
		</div>
		
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Show captcha on registration form' );?>::<?php echo TextOs::_('Show captcha on registration form explain'); ?>">
                      <label for="checkbox_company_admin_add_agent">
                          <?php echo TextOs::_( 'Show captcha on registration form' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('show_company_captcha',intval($configs['show_company_captcha']));
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
				OspropertyConfiguration::showCheckboxfield('company_term_condition',intval($configs['company_term_condition']));
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
				<?php echo OSPHelper::getArticleInput($configs['company_article_id'], 'configuration[company_article_id]'); ?>
			</div>
		</div>

		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Auto approval company registration request' );?>::<?php echo TextOs::_('Auto approval company registration request explain'); ?>">
                      <label for="checkbox_company_admin_add_agent">
                          <?php echo TextOs::_( 'Auto approval company registration request' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('auto_approval_company_register_request',intval($configs['auto_approval_company_register_request']));
				?>
			</div>
		</div>
		
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'Company admin add new agent from frontend' );?>::<?php echo TextOs::_('Company admin add new agent from frontend explain'); ?>">
                      <label for="checkbox_company_admin_add_agent">
                          <?php echo TextOs::_( 'Company admin add new agent from frontend' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('company_admin_add_agent',intval($configs['company_admin_add_agent']));
				?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ALLOW_COMPANY_ADMINISTRATOR_TO_CHANGE_FEATURED_STATUS' );?>::<?php echo Text::_('Do you allow Company admin to change Featured status of agent'); ?>">
					 <label for="checkbox_general_agent_listings">
						 <?php echo Text::_( 'OS_ALLOW_COMPANY_ADMINISTRATOR_TO_CHANGE_FEATURED_STATUS' ).':'; ?>
					 </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php 
				OspropertyConfiguration::showCheckboxfield('company_changefeaturedstatus',intval($configs['company_changefeaturedstatus']));
				?>
			</div>
		</div>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_ALLOW_COMPANY_ADMIN_TO_ASSIGN_FREE_AGENT' );?>::<?php echo Text::_('OS_ALLOW_COMPANY_ADMIN_TO_ASSIGN_FREE_AGENT_EXPLAIN'); ?>">
                      <label for="checkbox_allow_company_assign_agent">
                          <?php echo Text::_( 'OS_ALLOW_COMPANY_ADMIN_TO_ASSIGN_FREE_AGENT' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('allow_company_assign_agent',intval($configs['allow_company_assign_agent']));
				?>
			</div>
		</div>
		
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_COMPANY_ADMIN_CAN_ADDEDIT_PROPERTIES_EXPLAIN' );?>">
                      <label for="checkbox_company_admin_add_agent">
                          <?php echo Text::_( 'OS_COMPANY_ADMIN_CAN_ADDEDIT_PROPERTIES' ).':'; ?>
                      </label>
				</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php
				OspropertyConfiguration::showCheckboxfield('company_admin_add_properties',intval($configs['company_admin_add_properties']));
				?>
			</div>
		</div>
		
		
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo Text::_( 'OS_COMPANY_USER_GROUP' );?>::<?php echo Text::_('OS_COMPANY_USER_GROUP_EXPLAIN'); ?>">
                     <label for="checkbox_general_agent_listings">
                         <?php echo Text::_( 'OS_COMPANY_USER_GROUP' ).':'; ?>
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
				echo HTMLHelper::_('select.genericlist',$groupArr,'configuration[company_joomla_group_id]','class="form-select input-large ilarge"','value','text',$configs['company_joomla_group_id']);
				?>
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
				OspropertyConfiguration::showCheckboxfield('use_email_as_company_username',intval($configs['use_email_as_company_username']));
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
				<input type="text" name="configuration[company_redirect_link]" class="input-large form-control ilarge" value="<?php echo $configs['company_redirect_link'];?>" />
			</div>
		</div>
</fieldset>