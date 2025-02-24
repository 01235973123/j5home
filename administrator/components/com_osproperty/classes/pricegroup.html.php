<?php
/*------------------------------------------------------------------------
# pricegroup.html.php - Ossolution Property
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


class HTML_OspropertyPricegroup{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function pricegroup_list($option,$rows,$pageNav,$lists){
		global $mainframe,$_jversion;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_PRICELIST'),"list");
		ToolBarHelper::addNew('pricegroup_add');
		if (count($rows)){
			ToolBarHelper::editList('pricegroup_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM?'),'pricegroup_remove');
			ToolBarHelper::publish('pricegroup_publish');
			ToolBarHelper::unpublish('pricegroup_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		$ordering = ($lists['order'] == 'ordering');
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=pricegroup_list" name="adminForm" id="adminForm">
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%">
				
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PROPERTY_TYPE'), 'b.type_name', @$lists['order_Dir'], @$lists['order'],'pricegroup_list' ); ?>
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PRICE_FROM'), 'a.price_from', @$lists['order_Dir'], @$lists['order'],'pricegroup_list' ); ?>
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PRICE_TO'), '	a.price_to', @$lists['order_Dir'], @$lists['order'],'pricegroup_list' ); ?>	
					</th>
					<th width="15%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_ORDERING'), 'a.ordering', @$lists['order_Dir'], @$lists['order'],'pricegroup_list' ); ?>	
						<?php if ($ordering) echo HTMLHelper::_('grid.order',  $rows ,"filesave.png","pricegroup_saveorder"); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'a.published', @$lists['order_Dir'], @$lists['order'],'pricegroup_list' ); ?>	
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="7" style="text-align:center;">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=pricegroup_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'pricegroup_');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="left" style="padding-left: 10px;">
							<?php 
							if($row->type_name != ""){
								echo $row->type_name; 
							}else{
								echo Text::_("OS_ALL_TYPES");
							}
							?>
					</td>
					<td align="left" style="padding-left: 10px;">
						<a href="<?php echo $link; ?>">
							<?php echo OSPHelper::showPrice($row->price_from); ?>
						</a>
					</td>
					<td align="left" style="padding-left: 10px;">
						<a href="<?php echo $link; ?>">
							<?php echo OSPHelper::showPrice($row->price_to); ?>
						</a>
					</td>
					<td class="order"  style="text-align:right;">
						<span><?php echo $pageNav->orderUpIcon( $i, 1, 'pricegroup_orderup', 'Move Up', 1); ?></span>
						<span><?php echo $pageNav->orderDownIcon( $i, $n, 1, 'pricegroup_orderdown', 'Move Down',1); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text-area-order input-mini" style="text-align: center;width:50px;" />
					</td>
					<td align="center"  style="text-align:center;">
						<?php echo $published?>
					</td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="pricegroup_list" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	
	/**
	 * Edit Extra field
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editHTML($option,$row,$lists){
		global $mainframe,$jinput;
		$jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_PRICEGROUP').$title);
		ToolBarHelper::save('pricegroup_save');
		ToolBarHelper::save2new('pricegroup_new');
		ToolBarHelper::apply('pricegroup_apply');
		ToolBarHelper::cancel('pricegroup_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
			<table width="100%" class="admintable">
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PROPERTY_TYPE'); ?>
					</td>
					<td>
						<?php
						echo $lists['type'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PRICE_FROM'); ?>
					</td>
					<td>
						<input type="text" name="price_from" id="price_from" class="input-small form-control" value="<?php echo $row->price_from?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PRICE_TO'); ?>
					</td>
					<td>
						<input type="text" name="price_to" id="price_from" class="input-small form-control" value="<?php echo $row->price_to?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_ORDERING'); ?>
					</td>
					<td>
						<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PUBLISHED')?>
					</td>
					<td>
						<?php
						echo $lists['state'];
						?>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_osproperty">
			<input type="hidden" name="task" value="">
			<input type="hidden" name="id" value="<?php echo (int)$row->id?>">
			<input type="hidden" name="boxchecked" value="0">
		</form>
		<script type="text/javascript">
			Joomla.submitbutton = function(pressbutton)
			{
				form = document.adminForm;
				if (pressbutton == 'pricegroup_cancel'){
                    Joomla.submitform( pressbutton );
				}else{
					form.task.value = pressbutton;
					form.submit();
				}
			}
		</script>
		<?php
	}
}
?>