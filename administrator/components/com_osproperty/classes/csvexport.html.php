<?php
/*------------------------------------------------------------------------
# cpanel.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;


class HTML_OspropertyCsvExport{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 * @param unknown_type $lists
	 * @param unknown_type $countries
	 */
	static function displayCsvForm($option,$lists,$select_form,$count_csv_forms)
	{
		global $mainframe,$configClass,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		$span3Class    = $bootstrapHelper->getClassMapping('span3');
		$span4Class    = $bootstrapHelper->getClassMapping('span4');
		$span6Class    = $bootstrapHelper->getClassMapping('span6');
		$span1Class    = $bootstrapHelper->getClassMapping('span1');
		$document		= Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']);
		ToolBarHelper::title(Text::_('OS_EXPORT_FORM'),"upload");
		ToolBarHelper::custom('csvexport_proccess','download.png','download.png',Text::_('OS_EXPORTCSV'),false);
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=csvexport_default" name="adminForm" id="adminForm">
			<?php
			if($count_csv_forms == 0)
			{
				?>
				<div class="<?php echo $rowFluidClass; ?>">
					<div class="<?php echo $span12Class; ?>" style="text-align:center;">
						<div class="<?php echo $span3Class; ?>"></div>
						<div class="<?php echo $span6Class; ?>">
							<h3>
								<?php echo Text::_('OS_YOU_MUST_CREATE_CSV_FORM_IF_YOU_WANT_TO_EXPORT_PROPERTIES_TO_CSV');?>
							</h3>
							<div class="clearfix"></div>
							<div class="img-polaroid" style="text-align:left;">
								<?php echo Text::_('OS_YOU_MUST_CREATE_CSV_FORM_IF_YOU_WANT_TO_EXPORT_PROPERTIES_TO_CSV_EXPLAIN');?>
							</div>
							<div class="clearfix"></div>
							<br />
							<a href="index.php?option=com_osproperty&task=form_default" class="btn btn-info"><?php echo Text::_('OS_CREATE_CSV_FORM');?></a>
							<input type="button" onclick="javascript:returnControlPanel();" class="btn btn-warning" value="<?php echo Text::_('OS_I_WILL_CREATE_FORM_LATER');?>" />
						</div>
						<div class="<?php echo $span3Class; ?>"></div>
					</div>
				</div>
				<?php
			}
			else
			{
			?>
			<table width="100%" class="admintable" id="csvExportTable">
				<tr>
					<td class="key">
						<?php echo Text::_("OS_EXPORT_SELECT_FORM");?>
					</td>
					<td>
						<?php echo $lists['select_form']?>
					</td>
				</tr>
                <tr>
                    <td class="key">
                        <?php echo Text::_("OS_INCLUDING_PICTURES");?>
                    </td>
                    <td>
                        <?php echo $lists['include_pictures']?>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo Text::_("OS_INCLUDING_HIT_WITHIN");?>
                    </td>
                    <td>
						<div class="<?php echo $rowFluidClass; ?>">
							<div class="<?php echo $span3Class; ?>">
								<?php
								echo $lists['time_period'];
								?>
							</div>
							<div class="<?php echo $span1Class; ?>">
								<?php
								echo Text::_('OS_OR');
								?>
							</div>
							<div class="<?php echo $span1Class; ?>">
								<?php
								echo Text::_('OS_FROM');
								?>
							</div>
							<div class="<?php echo $span3Class; ?>">
								<?php echo HTMLHelper::_('calendar','','from','from',"%Y-%m-%d",array('class'=>'input-small form-control'));?>
							</div>
							<div class="<?php echo $span1Class; ?>">
								<?php
								echo Text::_('OS_TO');
								?>
							</div>
							<div class="<?php echo $span3Class; ?>">
								<?php echo HTMLHelper::_('calendar','','to','to',"%Y-%m-%d",array('class'=>'input-small form-control'));?>
							</div>
						</div>
                    </td>
                </tr>
				<?php if ($select_form && isset($lists['category_id'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_SELECT_CATEGORY");?>
					</td>
					<td>
						<?php echo $lists['category_id']?>
					</td>
				</tr>
				<?php endif;?>
				<?php if ($select_form && isset($lists['pro_types'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_SELECT_PROPERTY_TYPES");?>
					</td>
					<td>
						<?php echo $lists['pro_types']?>
					</td>
				</tr>
				<?php endif;?>
				<!--
				<?php if ($select_form && isset($lists['agent_id'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_SELECT_AGENT");?>
					</td>
					<td>
						<?php echo $lists['agent_id']?>
					</td>
				</tr>
				<?php endif;?>
				-->
				<?php if ($select_form && isset($lists['country'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_SELECT_COUNTRY");?>
					</td>
					<td>
						<?php echo $lists['country']?>
					</td>
				</tr>
				<?php endif;?>
				<?php if ($select_form && isset($lists['state'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_SELECT_STATE");?>
					</td>
					<td>
						<?php echo $lists['state']?>
					</td>
				</tr>
				<?php endif;?>
				<?php if ($select_form && isset($lists['city'])):?>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_EXPORT_CITY");?>
					</td>
					<td>
						<?php echo $lists['city']?>
					</td>
				</tr>
				<?php endif;?>
			</table>
			<?php
			}
			?>
			<input type="hidden" name="option" value="<?php echo $option?>" />
			<input type="hidden" name="task" value="csvexport_default" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>
	<?php 
	}
	
	static function exportSummary($option,$count_properties,$filecsv, $filename_zip,$include_pictures){
		global $mainframe;
		global $mainframe,$configClass;
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']);
		ToolBarHelper::title(Text::_('OS_EXPORT_FORM'),"logo48.png");
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=csvexport_default" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div class="span12" style="text-align:center;">
				<h3>
					<?php echo Text::_('OS_CSV_EXPORT_SUMMARY');?>
				</h3>
				<BR />
				<center>
				<table width="60%" style="border:1px solid #CCC;width:60%;">
					<tr>
						<td width="30%" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;">
							<?php
							echo Text::_('OS_NUMBER_PROPERTIES_EXPORTED');
							?>
						</td>
						<td width="70%" style="text-align:center;padding:10px;border-left:1px solid #CCC;background-color:#efefef;border-bottom:1px solid #CCC;">
							<?php
							echo $count_properties;
							?>
						</td>
					</tr>
					<tr>
						<td width="30%" style="text-align:left;padding:10px;border-bottom:1px solid #CCC;">
							<?php
							echo Text::_('OS_CSV_FILE_URL');
							?>
						</td>
						<td width="70%" style="text-align:left;padding:10px;border-left:1px solid #CCC;border-bottom:1px solid #CCC;">
							<a href="<?php echo $filecsv?>" title="Download CSV file">
								<?php
								echo $filecsv;
								?>
							</a>
							&nbsp;&nbsp;
							[<a href="<?php echo $filecsv?>" title="Download Picture file">
								<i class="icon-download"></i>
							</a>]
						</td>
					</tr>
                    <?php if($include_pictures == 1){ ?>
					<tr>
						<td width="30%" style="text-align:left;padding:10px;background-color:#efefef;">
							<?php
							echo Text::_('OS_PICTURES_EXPORTED_FILE_URL');
							?>
							
						</td>
						<td width="70%" style="text-align:left;padding:10px;border-left:1px solid #CCC;background-color:#efefef;">
							<a href="<?php echo $filename_zip?>" title="Download Picture file">
								<?php
									echo $filename_zip;
								?>
							</a>
							&nbsp;&nbsp;
							[<a href="<?php echo $filename_zip?>" title="Download Picture file">
								<i class="icon-download"></i>
							</a>]
						</td>
					</tr>
                    <?php } ?>
				</table>
				</center>
			</div>
		</div>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="csvexport_default" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
}
?>