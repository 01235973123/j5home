<?php
/**
 * @version        1.7.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2015 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

if (!OSMembershipHelper::canBrowseUsersList())
{
	return;
}


if (OSMembershipHelper::isJoomla5())
{
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
	$wa->useScript('multiselect')->useScript('modal-content-select');
}

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['placement' => 'bottom']);
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.multiselect');
$field		= $this->state->field;
$function	= 'jSelectUser_' . $field;
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;

?>
<div class="container-popup">
<form action="" method="post" name="adminForm" id="adminForm">
	<table width="100%">
	<tr>
		<td align="left">
			<div class="btn-wrapper input-append">
				<input type="text" class="form-control" name="filter_search" style="width: 200px !important"  id="filter_search" value="<?php echo $this->escape($this->state->filter_search); ?>" size="40" title="<?php echo Text::_('EDOCMAN_SEARCH_IN_NAME'); ?>" />
				<button class="btn btn-primary" type="submit"><span class="icon-search"></span></button>
				<button class="btn btn-primary" type="button" onclick="document.id('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
				<button class="btn btn-primary button-select" type="button" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('', '<?php echo Text::_('JLIB_FORM_SELECT_USER') ?>');"><?php echo Text::_('No user')?></button>
			</div>
		</td>	
		<td style="float: right;">
			<?php echo HTMLHelper::_('access.usergroup', 'filter_group_id', $this->state->filter_group_id, 'onchange="this.form.submit()"'); ?>
		</td>
	</tr>
	</table>
	<table class="adminlist table table-striped table-condensed" style="width: 100%">
		<thead>
			<tr>
				<th align="left">
					<?php echo HTMLHelper::_('grid.sort', 'Name', 'tbl.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%" align="left">
					<?php echo HTMLHelper::_('grid.sort', 'Username', 'tbl.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo Text::_('User groups'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3" class="pagination">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			if (count($this->items))
			{
				foreach ($this->items as $item)
				{
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
							<?php
							$attribs = 'data-content-select data-content-type="com_users.user"'
								. ' data-id="' . ((int) $item->id) . '"'
								. ' data-name="' . $this->escape($item->name) . '"'
								// @TODO: data-user-value, data-user-name, data-user-field is for backward compatibility, remove in Joomla 6
								. ' data-user-value="' . ((int) $item->id) . '"'
								. ' data-user-name="' . $this->escape($item->name) . '"'
								. ' data-user-field="' . $this->escape($field) . '"';
							?>
							<a class="pointer button-select" href="#" <?php echo $attribs; ?>>
								<?php echo $this->escape($item->name); ?>
							</a>								
						</td>
						<td align="center">
							<?php echo $item->username; ?>
						</td>
						<td align="left">
							<?php echo nl2br($item->group_names); ?>
						</td>
					</tr>
				<?php
				}
			}
		?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
</div>
