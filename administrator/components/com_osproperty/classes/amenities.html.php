<?php
/*------------------------------------------------------------------------
# amenities.html.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HTML_OspropertyAmenities{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function amenities_list($option,$rows,$pageNav,$lists){
		global $jinput, $mainframe,$_jversion;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_CONVENIENCE'),"star");
		ToolBarHelper::addNew('amenities_add');
		if (count($rows)){
			ToolBarHelper::editList('amenities_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'amenities_remove');
			ToolBarHelper::publish('amenities_publish');
			ToolBarHelper::unpublish('amenities_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'ordering';
		$ordering	= ($listOrder == 'ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=amenities_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'amenitiesList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
		}

		$customOptions = array(
			'filtersHidden'       => true,
			'defaultLimit'        => Factory::getApplication()->get('list_limit', 20),
			'orderFieldSelector'  => '#filter_full_ordering'
		);

		HTMLHelper::_('searchtools.form', '#adminForm', $customOptions);
		if (count($rows))
		{
			$ordering = [];
			foreach ($rows as $item)
			{
				$ordering[$item->parent_id][] = $item->id;
			}
		}

		?>
		<form method="POST" action="index.php?option=com_osproperty&task=amenities_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="50%" class="pull-left">
                    <DIV class="btn-wrapper btn-group">
						<div class="input-group input-append">
							<input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
							<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
								<i class="icon-search"></i>
							</button>
						</div>
                    </DIV>
				</td>
                <td width="50%" class="alignright floatright">
                    <?php echo $lists['categories']; ?>
                </td>
			</tr>
		</table>
		<table class="table table-striped" id="amenitiesList">
			<thead>
				<tr>
					<th width="5%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="3%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('Jglobal $jinput,_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="40%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_AMENITY_NAME'), 'amenities', @$lists['order_Dir'], @$lists['order'] ,'amenities_list'); ?>
					</th>
					<th width="25%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_CATEGORY'), 'category_id', @$lists['order_Dir'], @$lists['order'] ,'amenities_list'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['order_Dir'], @$lists['order']  ,'amenities_list'); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'id', @$lists['order_Dir'], @$lists['order']  ,'amenities_list'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="7" class="center">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
			<?php
			$db = Factory::getDBO();
			$k = 0;
			$canChange = true;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=amenities_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'amenities_');
				//$orderkey   = array_search($row->id, $children[$row->parent_id]);
				?>
				<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->category_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$canChange){
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder){
							$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-menu"></span>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" />
						<?php endif; ?>
					</td>
					<td align="center">
						<?php echo $checked; ?>
					</td>
					<td align="left">
						<a href="<?php echo $link; ?>">
							<?php echo $row->amenities; ?>
						</a>
					</td>
					<td align="left">
						<?php echo OspropertyAmenities::returnAmenityCategory($row->category_id);?>
					</td>
					<td align="center" class="center">
						<?php echo $published?>
					</td>
					<td align="center">
						<?php echo $row->id;?>
					</td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="amenities_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
		<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
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
	static function editHTML($option,$row,$lists,$translatable){
		global $jinput, $mainframe,$languages;
        $jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('Convenience').$title);
		ToolBarHelper::save('amenities_save');
		ToolBarHelper::save2new('amenities_new');
		ToolBarHelper::apply('amenities_apply');
		ToolBarHelper::cancel('amenities_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<?php 
		if ($translatable)
		{
		
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
			<table  width="100%" class="admintable backgroundwhite">
				<tr>
					<td class="key">
						<?php echo Text::_('OS_CONVENIENCE_NAME'); ?>
					</td>
					<td>
						<input type="text" name="amenities" id="amenities" size="40" value="<?php echo $row->amenities?>" class="input-large form-control ilarge" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_CATEGORY'); ?>
					</td>
					<td>
						<?php 
							echo OspropertyAmenities::makeAmenityCategoryDropdown($row->category_id);
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_ICON')?>
					</td>
					<td>
						<input type="text" name="icon" value="<?php echo $row->icon; ?>" class="input-medium form-control ilarge" />
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
		<?php 
		if ($translatable)
		{
		?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'translation', 'translation-page', Text::_('OS_TRANSLATION', true)); ?>	
				<div class="tab-content">			
					<?php	
						$i = 0;
						$activate_sef = $languages[0]->sef;
						echo HTMLHelper::_('bootstrap.startTabSet', 'languagetranslation', array('active' => 'translation-page-'.$activate_sef));
						foreach ($languages as $language)
						{												
							$sef = $language->sef;
							echo HTMLHelper::_('bootstrap.addTab', 'languagetranslation',  'translation-page-'.$sef, '<img src="'.Uri::root().'media/com_osproperty/flags/'.$sef.'.png" />');
						?>
							<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
								<table width="100%" class="admintable backgroundwhite">
									<tr>
										<td class="key">
											<?php echo Text::_('OS_CONVENIENCE_NAME'); ?>
										</td>
										<td>
											<input type="text" class="input-large form-control ilarge" name="amenities_<?php echo $sef; ?>" id="amenities_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'amenities_'.$sef}?>">
										</td>
									</tr>
								</table>
							</div>										
						<?php
							echo HTMLHelper::_('bootstrap.endTabSet');
							$i++;		
						}
						echo HTMLHelper::_('bootstrap.endTabSet');
					?>
				</div>	
		<?php
			echo HTMLHelper::_('bootstrap.endTab');
		}
		echo HTMLHelper::_('bootstrap.endTabSet');
		?>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
				Joomla.submitbutton = function(pressbutton)
				{
				form = document.adminForm;
				if (pressbutton == 'amenities_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.amenities.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_AMENINTY_NAME'); ?>');
					return;
				}else{
					Joomla.submitform( pressbutton );
					return;
				}
			}
		</script>
		<?php
	}
}
?>