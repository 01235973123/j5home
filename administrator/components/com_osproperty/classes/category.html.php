<?php

/*------------------------------------------------------------------------
# category.html.php - Ossolution Property
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
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HTML_OspropertyCategories{
	/**
	 * List categories
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function listCategories($option,$rows,$pageNav,$lists,$children)
	{
		global $mainframe,$jinput;
		//HTMLHelper::_('behavior.modal');
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_CATEGORIES'),"folder");
		ToolBarHelper::addNew('categories_add');
		if (count($rows)){
			ToolBarHelper::editList('categories_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'categories_remove');
			ToolBarHelper::publish('categories_publish');
			ToolBarHelper::unpublish('categories_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		
		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'ordering';
		$ordering	= ($listOrder == 'ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=categories_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
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
			$ordering = array();
			foreach ($rows as $item)
			{
				$ordering[$item->parent_id][] = $item->id;
			}
		}

		$db = Factory::getDBO();
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=categories_list" name="adminForm" id="adminForm">
		<div id="j-main-container jooma4">
			<div id="filter-bar" class="btn-toolbar js-stools">
				<div class="filter-search btn-group pull-left input-append">
                        <input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
                        <button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
                            <i class="icon-search"></i>
                        </button>
                    </DIV>
				</DIV>
			</div>
			<?php
			if(count($rows) > 0) {
			?>
			<table class="adminlist table table-striped" width="100%" id="categoryList"> 
				<thead>
					<tr>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="3%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="5%" style="text-align:center;">
							<?php echo Text::_('OS_PHOTO')?>
						</th>
						<th width="40%">
							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_CATEGORY_NAME'), 'category_name', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="15%">
							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_ACCESS'), 'access', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo Text::_('OS_PROPERTIES');?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo HTMLHelper::_('searchtools.sort',  Text::_('OS_PUBLISH'), 'published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="5%" style="text-align:center;">
							<?php echo HTMLHelper::_('grid.sort',   'ID', 'id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
				<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
				<?php
				$db = Factory::getDBO();
				$k = 0;
				$canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$orderkey   = array_search($row->id, $children[$row->parent_id]);
					$checked    = HTMLHelper::_('grid.id', $i, $row->id);
					$link 		= Route::_( 'index.php?option=com_osproperty&task=categories_edit&cid[]='. $row->id );
					$published 	= HTMLHelper::_('jgrid.published', $row->published, $i , 'categories_');
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
						<td align="center" style="text-align:center;">
							<?php
							if($row->category_image == ""){
								?>
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" style="height:50px;">
								<?php
							}else{
								?>
								<a href="<?php echo Uri::root()?>images/osproperty/category/<?php echo $row->category_image?>" target="_blank">
									<img src="<?php echo Uri::root()?>images/osproperty/category/thumbnail/<?php echo $row->category_image?>" style="height:50px;" border="0">
								</a>
								<?php
							}
							?>
						</td>
						<td align="left">
							
							<a href="<?php echo $link?>">
								<?php echo $row->treename;?>
							</a>
							<BR />
							(Alias: <?php echo $row->category_alias;?>)
						</td>
						<td align="center" >
							<?php
							echo OSPHelper::returnAccessLevel($row->access);
							?>
						</td>
						<td align="center" style="text-align:center;">
							<?php
							//$db->setQuery("Select count(id) from #__osrs_properties where category_id = '$row->id'");
							$total = 0;
							echo OspropertyCategories::countProperties($row->id,$total);
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
		<input type="hidden" name="task" value="categories_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order"  id="filter_order" value="<?php echo $lists['filter_order']; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
		<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}
	
	
	/**
	 * Edit Categories
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editCategory($option,$row,$lists,$translatable){
		global $mainframe,$languages,$configClass;
		//HTMLHelper::_('behavior.modal');
		$db = Factory::getDBO();
		if($row->id > 0){
			$edit = Text::_('OS_EDIT');
		}else{
			$edit = Text::_('OS_ADD');
		}
		ToolBarHelper::title(Text::_('OS_CATEGORY').Text::_(' ['.$edit.']'));
		ToolBarHelper::save('categories_save');
		ToolBarHelper::save2new('categories_new');
		ToolBarHelper::apply('categories_apply');
		ToolBarHelper::cancel('categories_gotolist');
		$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		?>
		<script language="text/javascript">
		Joomla.submitbutton = function(task) {
			var form = document.adminForm;
			category_name = form.category_name;
			if((task == "categories_save") || (task == "categories_apply")){
				if(category_name.value == ""){
					alert("<?php echo Text::_('OS_PLEASE_ENTER_CATEGORY_NAME')?>");
					category_name.focus();
				}else{
					Joomla.submitform(task);
				}
			}else{
				Joomla.submitform(task);
			}
		}
		</script>
		
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
		
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
		<table  width="100%" class="admintable" >
			<tr>
				<td class="key" width="23%">
					<?php echo Text::_('OS_CATEGORY_NAME')?>
				</td>
				<td width="80%">
					<input type="text" name="category_name" class="input-large form-control ilarge" id="category_name" size="40" value="<?php echo $row->category_name?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo Text::_('OS_ALIAS')?>
				</td>
				<td>
					<input type="text" name="category_alias" class="input-large form-control ilarge" id="category_alias" size="40" value="<?php echo $row->category_alias?>" />
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_PARENT_CAT')?>
				</td>
				<td>
					<?php echo $lists['parent'];?>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_PHOTO')?>
				</td>
				<td>
					<?php
					if($row->category_image){
						?>
						<a href="<?php echo Uri::root()?>images/osproperty/category/<?php echo $row->category_image?>" target="_blank">
						<img style="width:150px;" src="<?php echo Uri::root()?>images/osproperty/category/thumbnail/<?php echo $row->category_image?>" border="0" />
						</a>
						<BR>
						<input type="checkbox" name="remove_photo" id="remove_photo" value="0" onclick="javascript:changeValue('remove_photo')" /> &nbsp;<b><?php echo Text::_('OS_REMOVE_PHOTO');?></b><BR />
						<?php
					}
					?>
					<input type="file" class="input-large form-control" name="photo" id="photo" size="40" onchange="javascript:checkUploadPhotoFiles('photo')"> (<?php echo Text::_('OS_ONLY_SUPPORT_JPG_IMAGES');?>)
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_ACCESS')?>
				</td>
				<td>
					<?php echo $lists['access'];?>
				</td>
			</tr>
			<!--
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_DEFAULT_ORDERING')?>
				</td>
				<td>
					<?php echo $lists['ordering']?>
				</td>
			</tr>
			-->
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_PUBLISH')?>
				</td>
				<td>
					<?php echo $lists['state']?>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_META_DESCRIPTION')?>
				</td>
				<td>
					<textarea name="category_meta" id="category_meta" class="input-large form-control"  style="width:300px !important;"><?php echo $row->category_meta; ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php echo Text::_('OS_DESCRIPTION')?>
				</td>
				<td>
					<?php echo $editor->display( 'category_description',  stripslashes($row->category_description) , '60%', '200', '55', '20' ) ; ?>
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
										<td class="key"><?php echo Text::_('OS_CATEGORY_NAME'); ?></td>
										<td >
											<input type="text" class="input-large form-control ilarge" name="category_name_<?php echo $sef; ?>" id="category_name_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'category_name_'.$sef}?>" />
										</td>
									</tr>
									<tr>
										<td class="key">
											<?php echo Text::_('OS_ALIAS')?>
										</td>
										<td>
											<input type="text" class="input-large form-control ilarge" name="category_alias_<?php echo $sef; ?>" id="category_alias_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'category_alias_'.$sef}?>" />
										</td>
									</tr>
									<tr>
										<td class="key" valign="top">
											<?php echo Text::_('OS_DESCRIPTION')?>
										</td>
										<td>
											<?php echo $editor->display( 'category_description_'.$sef,  stripslashes($row->{'category_description_'.$sef}) , '80%', '250', '75', '20' ) ; ?>
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
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
		</form>
		<?php
	}
}
?>
