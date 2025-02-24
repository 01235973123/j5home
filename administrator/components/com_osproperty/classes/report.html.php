<?php
/*------------------------------------------------------------------------
# report.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;


class HTML_OspropertyReport{
	/**
	 * List all user report
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function listReports($option,$rows,$pageNav,$lists){
		global $mainframe;
		ToolBarHelper::title(Text::_('OS_USER_REPORT'));
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'report_remove');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%">
						<?php echo Text::_("OS_FILTER_ITEM_TYPE")?>: &nbsp; <?php echo $lists['item_type'];?>
				</td>
			</tr>
		</table>
		<table class="adminlist table table-striped" width="100%">
			<thead>
				<tr>
					<th width="2%">
				
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="7%" style="text-align:center;">
						<?php echo Text::_('OS_TYPE'); ?>
					</th>
					<th width="10%">
						<?php echo Text::_('OS_ITEM_NAME'); ?>
					</th>
					<th width="10%">
						<?php echo Text::_('OS_ITEM_URL'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo Text::_('OS_REPORT_REASON'); ?>
					</th>
					<th width="20%" style="text-align:center;">
						<?php echo Text::_('OS_REPORT_DETAILS'); ?>
					</th>
					<th width="7%" style="text-align:center;">
						<?php echo Text::_('OS_REPORT_EMAIL'); ?>
					</th>
					<th width="7%" style="text-align:center;">
						<?php echo Text::_('OS_IP_ADDRESS'); ?>
					</th>
					<th width="7%" style="text-align:center;">
						<?php echo Text::_('OS_REPORTED_ON'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="10" style="text-align:center;">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$db = Factory::getDBO();
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				if($_jversion == "1.5"){
					$checked = HTMLHelper::_('grid.checkedout',   $row, $i );
				}else{
					$checked = HTMLHelper::_('grid.id', $i, $row->id);
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="center">
						<?php
						switch ($row->item_type){
							case "0":
								echo Text::_('OS_PROPERTY');
							break;
							case "1":
								echo Text::_('OS_AGENT').'/ '.Text::_('OS_OWNER');;
							break;
							case "2":
								echo Text::_('OS_COMPANY');
							break;
						}
						?>
					</td>
					<td align="center">
						<?php
						switch ($row->item_type){
							case "0":
								$db->setQuery("Select ref,pro_name from #__osrs_properties where id = '$row->item_id'");
								$item = $db->loadObject();
								?>
								<a href="<?php echo $row->report_reason;?>" target="_blank">
								<?php
								if($item->ref != ""){
									echo $item->ref.", ";
								}
								echo $item->pro_name;
								?>
								</a>
								<?php
							break;
							case "1":
								$db->setQuery("Select name from #__osrs_agents where id = '$row->item_id'");
								echo $db->loadResult();
							break;
							case "2":
								$db->setQuery("Select company_name from #__osrs_companies where id = '$row->item_id'");
								echo $db->loadResult();
							break;
						}
						?>
					</td>
					<td align="left">
						<strong>
						<?php
						echo Text::_('OS_FRONTEND_URL').":";
						?>
						</strong>
						<a href="<?php echo $row->frontend_url;?>" target="_blank">
							<?php echo $row->frontend_url;?>
						</a>
						<BR/>
						<strong>
						<?php
						echo Text::_('OS_BACKEND_URL').":";
						?>
						</strong>
						<a href="<?php echo $row->backend_url;?>" target="_blank">
						<?php echo $row->backend_url;?>
						</a>
					</td>
					<td align="center">
						<?php
						echo Text::_($row->report_reason);
						?>
					</td>
					<td align="center">
						<?php
						echo $row->report_details;
						?>
					</td>
					<td align="center" class="order">
						<?php
						echo $row->report_email;
						?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $row->report_ip;?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo date("Y-m-d H:i:s",$row->report_on);?>
					</td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="report_listing" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		<?php
	}
}
?>