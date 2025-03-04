<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2025 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

$app      = Factory::getApplication();
$editor   = $app->getInput()->getCmd('editor', '');
$function = $app->getInput()->getCmd('function', 'jSelectEbevent');
$onclick  = $this->escape($function);

$ordering  = $this->state->filter_order == 'tbl.ordering';

$app->getDocument()->addScriptOptions('EBEditor', $editor)
	->addScriptOptions('isJoomla4', true)
	->getWebAssetManager()
	->useScript('core')
	->registerAndUseScript('com_eventbooking.admin-events-modal', 'media/com_eventbooking/js/admin-events-modal.min.js');
?>
<form action="<?php echo Route::_('index.php?option=com_eventbooking&view=events&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('EB_FILTER_SEARCH_EVENTS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" inputmode="search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip input-medium form-control" title="<?php echo HTMLHelper::tooltipText('EB_SEARCH_EVENTS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn btn-primary hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn btn-primary hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right">
				<?php
					echo $this->lists['filter_category_id'];
					echo $this->lists['filter_location_id'];
					echo $this->lists['filter_state'];
					echo $this->lists['filter_access'];
					echo $this->lists['filter_events'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="table itemList table-striped">
			<thead>
				<tr>
					<th class="title">
						<?php echo $this->gridSort('EB_TITLE',  'tbl.title'); ?>
					</th>
					<th class="title" width="18%">
						<?php echo Text::_('EB_CATEGORY'); ?>
					</th>
					<th class="title center" width="10%">
						<?php echo $this->gridSort('EB_EVENT_DATE', 'tbl.event_date'); ?>
					</th>
					<th class="title" width="7%">
						<?php echo $this->gridSort('EB_CAPACITY', 'tbl.event_capacity'); ?>
					</th>
					<th width="5%" nowrap="nowrap">
						<?php echo $this->gridSort('EB_PUBLISHED', 'tbl.published'); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo $this->gridSort('EB_ID', 'tbl.id'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getPaginationLinks(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
			$iconPublish     = $bootstrapHelper->getClassMapping('icon-publish');
			$iconUnPublish   = $bootstrapHelper->getClassMapping('icon-unpublish');

			for ($i = 0, $n = count($this->items); $i < $n; $i++)
			{
				$row       = $this->items[$i];
				$link      = Route::_('index.php?option=com_eventbooking&view=event&id=' . $row->id);
				$checked   = HTMLHelper::_('grid.id', $i, $row->id);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<a class="pointer select-link" href="javascript:void(0)" data-function="<?php echo $this->escape($onclick); ?>" data-id="<?php echo $row->id; ?>" data-title="<?php echo $this->escape(addslashes($row->title)); ?>">
							<?php echo $row->title; ?>
						</a>
					</td>
					<td>
						<?php echo $row->category_name ; ?>
					</td>
					<td class="center">
						<?php echo HTMLHelper::_('date', $row->event_date, $this->config->date_format, null); ?>
					</td>
					<td class="center">
						<?php echo $row->event_capacity; ?>
					</td>
					<td class="center">
						<a class="tbody-icon"><span class="<?php echo $row->published ? $iconPublish : $iconUnPublish; ?>"></span></a>
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>