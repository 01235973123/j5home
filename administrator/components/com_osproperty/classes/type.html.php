<?php
/*------------------------------------------------------------------------
# type.html.php - Ossolution Property
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
use Joomla\CMS\Editor\Editor;


class HTML_OspropertyType{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function type_list($option,$rows,$pageNav,$lists){
		global $mainframe,$jinput;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_PROPERTY_TYPE'),'folder');
		ToolBarHelper::addNew('type_add');
		if (count($rows)){
			ToolBarHelper::editList('type_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'type_remove');
			ToolBarHelper::publish('type_publish');
			ToolBarHelper::unpublish('type_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'ordering';
		$ordering	= ($listOrder == 'ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=type_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'typeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
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
		<form method="POST" action="index.php?option=com_osproperty&task=type_list" name="adminForm" id="adminForm">
		<div id="j-main-container">
			<div id="filter-bar" class="btn-toolbar js-stools">
				<div class="filter-search btn-group pull-left input-append">
					<input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
					<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
						<i class="icon-search"></i>
					</button>
				</DIV>
			</div>
			<?php
			if(count($rows) > 0) {
			?>
			<table class="adminlist table table-striped" id="typeList">
				<thead>
					<tr>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="5%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="30%">
							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_TYPE_NAME'), 'type_name', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="15%" style="text-align:center;">
							<?php echo Text::_('OS_PROPERTIES');?>
						</th>
						<th width="15%" style="text-align:center;">
							<?php echo Text::_('OS_ICON');?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('ID'), 'id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
				<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
				<?php
				$db = Factory::getDBO();
				$k = 0;
				$parentsStr = "";
				$canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = HTMLHelper::_('grid.id', $i, $row->id);
					$link 		= Route::_( 'index.php?option=com_osproperty&task=type_edit&cid[]='. $row->id );
					$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'type_');
					$orderkey   = array_search($row->id, $ordering[$row->parent_id]);
					?>
					<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->parent_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
						<td class="order nowrap center hidden-phone" style="text-align:center;">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								//echo "1";
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								//echo "2";
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
						<td align="left" style="padding-left: 10px;">
							<a href="<?php echo $link; ?>">
								<?php echo $row->type_name; ?>
							</a>
							<BR />
							(<?php echo Text::_('OS_ALIAS')?>: <?php echo $row->type_alias;?>)
						</td>
						<td align="center" style="text-align:center;"> 
							<?php 
							echo $row->nproperties;
							?>
						</td>
						<td align="center" style="text-align:center;"> 
							<?php 
							if($row->type_icon == ""){
								?>
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/1.png" />
								<?php 
							}else{
								?>
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $row->type_icon;?>" />
								<?php 
							}
							?>
						</td>
						<td align="center" style="text-align:center;"> 
							<?php echo $published?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $row->id?>
						</td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<?php
			}else{
			?>
			<div class="alert alert-no-items"><?php echo Text::_('OS_NO_MATCHING_RESULTS');?></div>
			<?php
			}
			?>
		</div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="type_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir'];?>" />
		<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
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
		global $mainframe,$languages,$configClass,$jinput;
        $jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('Type').$title);
		ToolBarHelper::save('type_save');
		ToolBarHelper::save2new('type_new');
		ToolBarHelper::apply('type_apply');
		ToolBarHelper::cancel('type_cancel');
		
		$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		?>
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
						<?php echo Text::_('OS_PROPERTY_TYPE_NAME'); ?>
					</td>
					<td>
						<input type="text" class="input-large form-control ilarge" name="type_name" id="type_name" size="40" value="<?php echo $row->type_name?>">
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_ALIAS'); ?>
					</td>
					<td>
						<input type="text" name="type_alias" class="input-large form-control ilarge" id="type_alias" size="40" value="<?php echo $row->type_alias;?>">
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
				<?php
				if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php"))
				{
					if($configClass['integrate_oscalendar'] == 1)
					{
						?>
						<tr>
							<td class="key" valign="top">
								<?php echo Text::_('OS_PRICE_TYPE')?>
							</td>
							<td>
								<?php echo $lists['price_type']?> 
								<BR />
								<?php echo Text::_('OS_PRICE_TYPE_EXPLAIN');?>
								<BR />
								<?php echo Text::_('OS_PRICE_TYPE_EXPLAIN1');?>
							</td>
						</tr>
						<?php
					}
				}
				?>
				<tr>
					<td class="key" valign="top">
						<?php echo Text::_('OS_ICON')?>
					</td>
					<td>
						<?php 
						if($row->type_icon == ""){
							$row->type_icon = "1.png";
						}
						$k = 0;
						for($i=1;$i<=20;$i++){
							$k++;
							if($row->type_icon == $i.".png"){
								$selected = "checked";
							}else{
								$selected = "";
							}
							?>
							<input type="radio" name="type_icon" value="<?php echo $i.".png"?>" <?php echo $selected?> />
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/googlemapicons/<?php echo $i?>.png" />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php 
							if($k == 10){
								echo "<BR /><BR />";
								$k = 0;
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_DESCRIPTION')?>
					</td>
					<td>
						<?php
						// parameters : areaname, content, width, height, cols, rows, show xtd buttons
						echo $editor->display( 'type_description',  htmlspecialchars($row->type_description, ENT_QUOTES), '550', '300', '60', '20', false ) ;
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
								<table width="100%" class="admintable" >
									<tr>
										<td class="key">
											<?php echo Text::_('OS_PROPERTY_TYPE_NAME'); ?>
										</td>
										<td>
											<input type="text" class="input-large form-control ilarge" name="type_name_<?php echo $sef;?>" id="type_name_<?php echo $sef;?>" size="40" value="<?php echo $row->{'type_name_'.$sef};?>">
										</td>
									</tr>
									<tr>
										<td class="key">
											<?php echo Text::_('OS_ALIAS'); ?>
										</td>
										<td>
											<input type="text" class="input-large form-control ilarge" name="type_alias_<?php echo $sef;?>" id="type_alias_<?php echo $sef;?>" size="40" value="<?php echo $row->{'type_alias_'.$sef};?>">
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
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>">
		</form>
		<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton)
			{
				form = document.adminForm;
				if (pressbutton == 'type_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.type_name.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_PROPERTY_TYPE_NAME'); ?>');
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
