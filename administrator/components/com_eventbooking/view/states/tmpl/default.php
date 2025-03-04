<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$this->includeTemplate('script');
?>
<form action="<?php echo $this->getFormAction(); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="eb-joomla4-container">
		<div id="filter-bar" class="btn-toolbar js-stools-container-filters-visible">
			<?php $this->renderSearchBar(); ?>
			<div class="btn-group pull-right hidden-phone">
				<?php
					echo EventbookingHelperHtml::getChoicesJsSelect($this->lists['filter_country_id']);
					echo $this->lists['filter_state'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="table itemList table-striped">
			<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title" style="text-align: left;">
					<?php echo $this->gridSort('EB_STATE_NAME',  'tbl.state_name'); ?>
				</th>
				<th class="title" style="text-align: left;">
					<?php echo $this->gridSort('EB_COUNTRY_NAME',  'b.name'); ?>
				</th>
				<th class="center title" width="15%">
					<?php echo $this->gridSort('EB_STATE_CODE_3',  'tbl.state_3_code'); ?>
				</th>
				<th class="center title" width="15%">
					<?php echo $this->gridSort('EB_STATE_CODE_2',  'tbl.state_2_code'); ?>
				</th>
				<th class="center" width="10%">
					<?php echo $this->gridSort('EB_PUBLISHED',  'tbl.published'); ?>
				</th>
				<th class="center" width="5%">
					<?php echo $this->gridSort('EB_ID',  'tbl.id'); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getPaginationLinks(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i = 0, $n = count($this->items); $i < $n; $i++)
			{
				$row       = $this->items[$i];
				$link      = $this->getEditItemLink($row);
				$checked   = HTMLHelper::_('grid.id', $i, $row->id);
				$published = HTMLHelper::_('jgrid.published', $row->published, $i);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->state_name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->country_name; ?>
					</td>
					<td class="center">
						<?php echo $row->state_3_code; ?>
					</td>
					<td class="center">
						<?php echo $row->state_2_code; ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
					</td>
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<?php $this->renderFormHiddenVariables(); ?>
</form>