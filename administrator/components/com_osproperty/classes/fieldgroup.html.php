<?php
/*------------------------------------------------------------------------
# fieldgroup.php - Ossolution Property
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


class HTML_OspropertyFieldgroup
{
	static function listfieldgroup($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$_jversion,$jinput;
		
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_FIELD_GROUPS'),"list");
		ToolBarHelper::addNew('fieldgroup_add');
		if (count($rows)){
			ToolBarHelper::editList('fieldgroup_edit');
			ToolBarHelper::deleteList('Are you sure you want to remove item(s)?','fieldgroup_remove');
			ToolBarHelper::publish('fieldgroup_publish');
			ToolBarHelper::unpublish('fieldgroup_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'ordering';
		$ordering	= ($listOrder == 'ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=fieldgroup_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'groupList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
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
		
		<form method="POST" action="index.php?option=com_osproperty&task=fieldgroup_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%">
                    <div class="filter-search btn-group pull-left input-append">
                        <input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
                        <button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
                            <i class="icon-search"></i>
                        </button>
                    </DIV>
				</td>
			</tr>
		</table>
        <?php
        if(count($rows) > 0) {
        ?>
		<table class="adminlist table table-striped" id="groupList">
			<thead>
				<tr>
					<th width="3%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="2%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="30%">
						<?php echo HTMLHelper::_('searchtools.sort',   'Group name', 'group_name', @$lists['filter_order_Dir'], @$lists['filter_order'] ,'fieldgroup_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_ACCESS') , 'access', @$lists['filter_order_Dir'], @$lists['filter_order'] ,'fieldgroup_list'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo Text::_('OS_ENTRIES')?>
					</th>
					<!--
					<th width="15%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   'Ordering', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'] ,'fieldgroup_list'); ?>
						<?php echo HTMLHelper::_('grid.order',  $rows ,"filesave.png","fieldgroup_saveorder"); ?>
					</th>
					-->
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_PUBLISH')?>
					</th>
				</tr>
			</thead>
			<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
			<?php
			$db = Factory::getDBO();
			$k = 0;
			$canChange = true;
			for ($i=0, $n=count($rows); $i < $n; $i++) 
			{
				$row = $rows[$i];
				//$orderkey = array_search($row->id, $children[$row->parent_id]);
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=fieldgroup_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'fieldgroup_');
				$orderkey   = array_search($row->id, $ordering[$row->parent_id]);
				?>
				<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->parent_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
					<td class="order nowrap center hidden-phone" style="text-align:center;">
						<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
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
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="left">
						<a href="<?php echo $link?>">
							<?php echo $row->group_name; ?>
						</a>
					</td>
					<TD align="left">
						<?php
                        echo OSPHelper::returnAccessLevel($row->access);
						?>
					</TD>
					<td align="center" style="text-align:center;">
						<?php
						$db->setQuery("Select count(id) from #__osrs_extra_fields where group_id = '$row->id'");
						echo $db->loadResult();
						?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $published?>
					</td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<td width="100%" colspan="7" style="text-align:center;">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
		</table>
        <?php
        }else{
            ?>
            <div class="alert alert-no-items"><?php echo Text::_('OS_NO_MATCHING_RESULTS');?></div>
        <?php
        }
        ?>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="fieldgroup_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order"  id="filter_order" value="<?php echo $lists['filter_order']; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
		<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}
	
	
	/**
	 * Edit Group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editGroup($option,$row,$lists,$translatable)
	{
		global $mainframe,$languages;
		if($row->id > 0){
			$edit = "Edit";
		}else{
			$edit = "Add new";
		}
		ToolBarHelper::title(Text::_('Field group ['.$edit.']'));
		ToolBarHelper::save('fieldgroup_save');
		ToolBarHelper::save2new('fieldgroup_new');
		ToolBarHelper::apply('fieldgroup_apply');
		ToolBarHelper::cancel('fieldgroup_gotolist');
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task) {
			var form = document.adminForm;
			group_name = form.group_name;
			if((task == "fieldgroup_save") || (task == "fieldgroup_apply")){
				if(group_name.value == ""){
					alert("<?php echo Text::_('OS_PLEASE_ENTER_FIELD_GROUP_TITLE')?>");
					group_name.focus();
				}else{
					Joomla.submitform(task);
				}
			}else{
				Joomla.submitform(task);
			}
		}
		</script>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
			<table  width="100%" class="admintable" >
				<tr>
					<td class="key">
						<?php echo Text::_('OS_FIELDGROUP_TITLE')?>
					</td>
					<td>
						<input type="text" class="input-large form-control ilarge" name="group_name" size="50" value="<?php echo $row->group_name?>">
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_ACCESS')?>
					</td>
					<td>
						<?php echo $lists['access']?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PUBLISHED')?>
					</td>
					<td>
						<?php echo $lists['state']?>
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
								<table width="100%" class="admintable" >
									<tr>
										<td class="key">
											<?php echo Text::_('OS_FIELDGROUP_TITLE')?>
										</td>
										<td>
											<input type="text" class="form-control input-medium ilarge" name="group_name_<?php echo $sef; ?>" size="50" value="<?php echo $row->{'group_name_'.$sef}?>">
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
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		</form>
		<?php
	}
	
	
}
?>