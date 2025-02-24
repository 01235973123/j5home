<?php

/*------------------------------------------------------------------------
# configuration.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Editor\Editor;

class HTML_OspropertyConfiguration
{
	static function configurationHTML($option,$configs,$used_currencies)
    {
		global $mainframe,$_jversion,$configClass,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		$span6Class    = $bootstrapHelper->getClassMapping('span6');
	    HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_CONFIGURATION'),"cog");
		ToolBarHelper::save('configuration_save');
		ToolBarHelper::apply('configuration_apply');
		ToolBarHelper::cancel('configuration_cancel');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		OSPHelper::loadTooltip();
		?>
		<style>
			div.current fieldset {
				border: 1px solid #CCCCCC;
			}
			fieldset label, fieldset span.faux-label {
			    clear: right;
			}
			div.current label, div.current span.faux-label {
			    clear: none;
			    display: block;
			    float: left;
			    margin-top: 1px;
			    min-width: 30px;
			}
		</style>
		<?php
		if (!isset($configs['goole_map_resolution']) || !is_numeric($configs['goole_map_resolution']))
		{
		    $themapres 	    = "10";
		}
		else
		{
		    $themapres 	    = $configs['goole_map_resolution'];
		}
		$thedeclat 		    = $configClass['goole_default_lat'];
		$thedeclong 	    = $configClass['goole_default_long'];
		if (isset($configs['goole_map_latitude']) && is_float($configs['goole_map_latitude']))
		{
			    $thedeclat  = $configs['goole_map_latitude'];
		}
		if (isset($configs['goole_map_longitude']) && is_float($configs['goole_map_longitude']))
		{
			$thedeclong     = $configs['goole_map_longitude'];
		}
		$editorPlugin       = null;
		if (PluginHelper::isEnabled('editors', 'codemirror'))
		{
			$editorPlugin   = 'codemirror';
		}
		elseif(PluginHelper::isEnabled('editor', 'none'))
		{
			$editorPlugin   = 'none';
		}
		if ($editorPlugin)
		{
			$showCustomCss  = 1;
		}
		else
		{
			$showCustomCss  = 0;
		}

		if (OSPHelper::isJoomla4())
		{
			$tabApiPrefix = 'uitab.';

			Factory::getDocument()->getWebAssetManager()->useScript('showon');
		}
		else
		{
			$tabApiPrefix = 'bootstrap.';

			HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
		}
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=configuration_list" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
		<div class="<?php echo $rowFluidClass;?>" id="osp-configuration">
			<div class="tab-content">
				<?php echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'configTab', array('active' => 'general-page')); ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'general-page', TextOs::_('GENERAL_SETTING')); ?>
						<div class="tab-pane active <?php echo $rowFluidClass; ?>" id="general-page">
							<div class="<?php echo $span6Class;?>">
								<!--  Business setting -->
								<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/business.php');?>
								<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/currency.php');?>
								<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/spam.php');?>
								<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/privacy.php');?>
							</div>
							<div class="<?php echo $span6Class;?>">
								<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/layout_of_site.php');?>
								<!--  Top menu -->
								<?php
								if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/general/google_map.php'))
								{
									require_once(JPATH_COMPONENT_ADMINISTRATOR .'/classes/configuration/general/google_map.php');
								}
								?>
							</div>	
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'properties', TextOs::_('PROPERTIES')); ?>
						<div class="tab-pane" id="properties">
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/property.php');?>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'payment', TextOs::_('PAYMENT'). ' & '.TextOs::_('Expiration')); ?>
						<div class="tab-pane" id="payment">
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/payment.php');?>
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/properties/cron_task.php'); ?>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'homepage', Text::_('OS_LAYOUTS')); ?>
						<div class="tab-pane" id="homepage">
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/layouts/layouts.php');?>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'company', TextOs::_('COMPANY')); ?>
						<div class="tab-pane" id="company">
							<!-- 	Fieldset Agent Settings  -->
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/company/company.php');?>
							<!-- end Fieldset Agent Settings  -->
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'agent', TextOs::_('USERTYPE')); ?>
						<div class="tab-pane" id="agent">
							<!-- 	Fieldset Agent Settings  -->
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/agents/agent.php');?>
							<!-- end Fieldset Agent Settings  -->
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'images', TextOs::_('IMAGES')); ?>
						<div class="tab-pane" id="images">
							<!-- 	Fieldset Properties Settings  -->
							<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/images/image.php');?>
							<!-- end Fieldset Agent Settings  -->
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'locator', TextOs::_('SEARCH')); ?>
						<div class="tab-pane" id="locator">
						<?php
						if(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/search/search.php'))
						{
							require_once(JPATH_COMPONENT_ADMINISTRATOR .'/classes/configuration/search/search.php');
						}
						?>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'othersetting', TextOs::_('CURRENCIES')); ?>
						<div class="tab-pane" id="othersetting">
							<fieldset class="form-horizontal options-form">
								<legend><?php echo Text::_('OS_USED_CURRENCIES')?></legend>
							
								<table class="adminlist table table-striped">
									<thead>
										<tr>
											<th >
												<?php
													echo Text::_('OS_CURRENCY_NAME');
												?>
											</th>
											<th class="center">
												<?php
													echo Text::_('OS_CURRENCY_CODE');
												?>
											</th>
											<th class="center">
												<?php
													echo Text::_('OS_CURRENCY_SYMBOL');
												?>
											</th>
											<th class="center">
												<?php
													echo Text::_('OS_PUBLISHED');
												?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$k = 0;
										for ($i = 0, $n = count($used_currencies); $i < $n; $i++)
										{
											$row = $used_currencies[$i];
											?>
											<tr class="<?php echo "row$k"; ?>">
												<td>
													<?php
														echo $row->currency_name;
													?>
												</td>
												<td class="center">
													<?php
														echo $row->currency_code;
													?>
												</td>
												<td class="center">
													<?php
														echo $row->currency_symbol;
													?>
												</td>
												<td class="center">
													<div id="div_<?php echo $row->id;?>">
													<?php
														if($row->published == 1)
														{
															?>
															<a href="javascript:changePublishedStatus(0,<?php echo $row->id?>,'<?php echo Uri::base();?>')" title="<?php echo Text::_('OS_CLICK_HERE_TO_UNPUBLISH_CURRENCY');?>">
																<i class="icon-star colorgreen"></i>
															</a>
															<?php
														}
														else
														{
															?>
															<a href="javascript:changePublishedStatus(1,<?php echo $row->id?>,'<?php echo Uri::base();?>')" title="<?php echo Text::_('OS_CLICK_HERE_TO_PUBLISH_CURRENCY');?>">
																<i class="icon-star colorred" ></i>
															</a>
															<?php
														}
													?>
													</div>
												</td>
											</tr>
											<?php
										}	
										?>
									</tbody>
								</table>
							</fieldset>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php
					if($showCustomCss == 1)
					{
					?>
						<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'customcss', Text::_('Custom CSS')); ?>
							<div class="tab-pane" id="customcss">
								<table  width="100%">
									<tr>
										<td>
											<?php
											$customCss = '';
											if (file_exists(JPATH_ROOT.'/media/com_osproperty/assets/css/custom.css'))
											{
												$customCss = file_get_contents(JPATH_ROOT.'/media/com_osproperty/assets/css/custom.css');
											}
											if (OSPHelper::isJoomla4())
											{
											?>
												<textarea class="form-control" name="configuration[custom_css]" rows="20" style="width:100%;"><?php echo $customCss; ?></textarea>
											<?php
											}
											else
											{
												echo Editor::getInstance($editorPlugin)->display('configuration[custom_css]', $customCss, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'css'));
											}
											?>
										</td>
									</tr>
								</table>
							</div>
						<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
					<?php
					}
					jimport('joomla.filesystem.folder');
					if(Folder::exists(JPATH_ROOT."/components/com_osmembership"))
					{
						?>
						<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'membership', TextOs::_('MEMBERSHIP')); ?>
							<div class="tab-pane" id="membership">
								<table  width="100%">
									<tr>
										<td>
											<!-- 	Fieldset Properties Settings  -->
											<?php 
											require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/membership/membership.php');?>
											<!-- end Fieldset Agent Settings  -->
										</td>
									</tr>
								</table>
							</div>
						<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
						<?php
					}
					else
					{
						$db = Factory::getDBO();
						$db->setQuery("Update #__osrs_configuration set fieldvalue = '0' where fieldname like 'integrate_membership'");
						$db->execute();
					}
		        if(Folder::exists(JPATH_ROOT."/components/com_oscalendar"))
		        {
			       	?>
					<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'oscalendar', TextOs::_('OSCALENDAR')); ?>
						<div class="tab-pane" id="oscalendar">
							<table  width="100%">
								<tr>
									<td>
										<!-- 	Fieldset Properties Settings  -->
										<?php require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/configuration/calendar/calendar.php');?>
										<!-- end Fieldset Agent Settings  -->
									</td>
								</tr>
							</table>
						</div>
					<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
			       	<?php
		        }
				?>
				<?php echo HTMLHelper::_($tabApiPrefix.'addTab', 'configTab', 'downloadid', Text::_('Download ID')); ?>
					<div class="tab-pane" id="downloadid">
						<table  width="100%">
							<tr>
								<td>
									<strong>Download ID: </strong><input type="text" class="input-xlarge form-control ilarge" id="download_id" name="configuration[download_id]" value="<?php echo isset($configClass['download_id'])? $configClass['download_id']:''; ?>"/>
									<BR />
									<span class="colorred">
									Enter your Download ID into this config option to be able to use Joomla Update to update your site to latest version of OS Property whenever there is new version available. To register Download ID, please go to: <a href="http://joomdonation.com" target="_blank">www.joomdonation.com</a> and click on menu <strong><a href="http://joomdonation.com/download-ids.html" target="_blank">Download ID</a></strong>. <strong>Notice:</strong> You should login before you access to this page. 
									</span>
								</td>
							</tr>
						</table>
					</div>
				<?php echo HTMLHelper::_($tabApiPrefix.'endTab') ?>
				<?php echo HTMLHelper::_($tabApiPrefix.'endTabSet'); ?>
			</div>
        </div>
        <input type="hidden" name="option" value="com_osproperty" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="MAX_UPLOAD_SIZE" value="9000000" />
		<input type="hidden" name="currency_id_pos" id="currency_id_pos" value="" />
        </form>
		<script type="text/javascript">
		function changePublishedStatus(status,id,live_site){
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			}
			var currency_id_pos = document.getElementById('currency_id_pos');
			currency_id_pos.value = id;
			url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=configuration_changecurrencystatus&id=" + id + "&status=" + status;
			xmlHttp.onreadystatechange=updateCurrency;
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
		function updateCurrency() { 
			if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
				var currency_id_pos = document.getElementById('currency_id_pos');
				currency_id_pos = currency_id_pos.value;
				document.getElementById("div_" + currency_id_pos).innerHTML = xmlHttp.responseText ;
				
			} 
		}
		</script>
        <?php 
	}
}
?>
