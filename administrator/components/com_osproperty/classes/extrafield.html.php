<?php
/*------------------------------------------------------------------------
# extrafield.html.php - Ossolution Property
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

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class HTML_OspropertyExtrafield{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function extrafield_list($option,$rows,$pageNav,$lists){
		global $mainframe,$_jversion,$jinput,$bootstrapHelper;
		$rowFluidClass	= $bootstrapHelper->getClassMapping('row-fluid');
		$span4Class		= $bootstrapHelper->getClassMapping('span4');
		$span8Class		= $bootstrapHelper->getClassMapping('span8');
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_EXTRA_FIELDS'),"file");
		ToolBarHelper::addNew('extrafield_add');
		if (count($rows)){
			ToolBarHelper::editList('extrafield_edit');
            ToolBarHelper::custom('extrafield_copy','copy.png','copy.png',Text::_('OS_COPY_FIELD'));
			ToolBarHelper::deleteList('OS_ARE_YOU_SURE_TO_REMOVE_ITEM','extrafield_remove');
			ToolBarHelper::publish('extrafield_publish');
			ToolBarHelper::unpublish('extrafield_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'a.ordering';
		$ordering	= ($listOrder == 'a.ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=extrafield_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'fieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
		}

		$customOptions = array(
			'filtersHidden'       => true,
			'defaultLimit'        => Factory::getApplication()->get('list_limit', 20),
			'orderFieldSelector'  => '#filter_full_ordering'
		);

		HTMLHelper::_('searchtools.form', '#adminForm', $customOptions);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=extrafield_list" name="adminForm" id="adminForm">
		<div class="<?php echo $rowFluidClass; ?>">
			<div class="<?php echo $span4Class; ?>">
                    <div class="filter-search btn-group pull-left input-append">
                        <input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
                        <button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
                            <i class="icon-search"></i>
                        </button>
                    </DIV>
			</div>
			<div class="<?php echo $span8Class; ?>">
				<div class="btn-wrapper">
					<?php
					echo $lists['type'];
					?>
				</div>
				<div class="btn-wrapper">
					<?php
					echo $lists['group'];
					?>
				</div>
				<div class="btn-group">
					<?php
					echo $lists['fieldtype'];
					?>
					<?php
					echo $pageNav->getLimitBox();
					?>
				</div>
			</div>
		</div>
        <?php
        if(count($rows) > 0) {
        ?>
		<table class="adminlist table table-striped" id="fieldList">
			<thead>
				<tr>
					<th width="2%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_FIELD_TITLE'), 'a.field_label', @$lists['filter_order_Dir'], @$lists['filter_order'],'extrafield_list' ); ?>
					</th>
					
					<th width="15%">
						<?php echo Text::_('OS_PROPERTY_TYPE');?>
					</th>
					<th width="5%">
						<?php echo Text::_('OS_ACCESS')?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_FIELD_NAME'), 'a.field_name', @$lists['filter_order_Dir'], @$lists['filter_order'],'extrafield_list' ); ?>
					</th>
					
					<th width="7%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_FIELD_TYPE'), 'a.field_type', @$lists['filter_order_Dir'], @$lists['filter_order'],'extrafield_list' ); ?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_REQUIRED')?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_SEARCHABLE')?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_READONLY')?>
					</th>
					<th width="7%" style="text-align:center;">
						<?php echo Text::_('OS_SHOW_ON_LIST');?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_PUBLISH')?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="15" style="text-align:center;">
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
				$link 		= Route::_( 'index.php?option=com_osproperty&task=extrafield_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i , 'extrafield_');
				switch ($row->field_type){
					case "radio":
					case "singleselect":
					case "multipleselect":
					case "checkbox":
						$db->setQuery("Select count(id) from #__osrs_extra_field_options where field_id = '$row->id'");
						$count = $db->loadResult();
						if(($count == 0) and ($row->options != "")){
							HelperOspropertyFields::saveNewOption($row->options,$row->id);
						}
					break;
				}
				
				?>
				<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->group_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
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
						<a href="<?php echo $link?>" title="Edit">
						<?php
							echo $row->field_label;
						?>
						</a>
						<div class="small">
							<?php echo Text::_('OS_GROUP').": ";?>
							<?php
							echo $row->group_name;
							?>
						</div>
					</td>
					<td align="left">
						<?php
							echo $row->typeLists;
						?>
					</td>
					<td align="left">
						<?php
                        echo OSPHelper::returnAccessLevel($row->access);
						?>
					</td>
					<td align="left">
						<a href="<?php echo $link?>" title="Edit">
						<?php
							echo $row->field_name;
						?>
						</a>
					</td>
					<td align="center">
						<?php
						if($row->field_type == "text"){
							switch ($row->value_type){
								case "1":
									echo Text::_('OS_INTEGER');
								break;
								case "2":
									echo Text::_('OS_DECIMAL');
								break;
								default:
									echo $row->field_type;
								break;
							}
						}else{
							echo $row->field_type;
						}
						?>
					</td>
					<td align="center"  style="text-align:center;">
						<?php
							if($row->required == 1){
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=required&cid[]=<?php echo $row->id?>&v=0" title="Change required status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
									  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
									  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
									</svg>
								</a>
								<?php
							}else{
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=required&cid[]=<?php echo $row->id?>&v=1" title="Change required status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
									  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
									  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
									</svg>
								</a>
								<?php
							}
						?>
					</td>
					<td align="center" style="text-align:center;">
						<?php
							if($row->searchable == 1){
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=searchable&cid[]=<?php echo $row->id?>&v=0" title="Change searchable status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
									  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
									  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
									</svg>
								</a>
								<?php
							}else{
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=searchable&cid[]=<?php echo $row->id?>&v=1" title="Change searchable status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
									  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
									  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
									</svg>
								</a>
								<?php
							}
						?>
					</td>
					<td align="center" style="text-align:center;">
						<?php
							if($row->readonly == 1){
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=readonly&cid[]=<?php echo $row->id?>&v=0" title="Change readonly status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
									  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
									  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
									</svg>
								</a>
								<?php
							}else{
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=readonly&cid[]=<?php echo $row->id?>&v=1" title="Change readonly status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
									  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
									  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
									</svg>
								</a>
								<?php
							}
						?>
					</td>
					<td align="center" style="text-align:center;">
						<?php
							if($row->show_on_list == 1){
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=show_on_list&cid[]=<?php echo $row->id?>&v=0" title="Change on list status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="green" class="bi bi-check2-circle" viewBox="0 0 16 16">
									  <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
									  <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
									</svg>
								</a>
								<?php
							}else{
								?>
								<a href="index.php?option=com_osproperty&task=extrafield_changeType&type=show_on_list&cid[]=<?php echo $row->id?>&v=1" title="Change on list status">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
									  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
									  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
									</svg>
								</a>
								<?php
							}
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
		</table>
        <?php
        }else{
            ?>
            <div class="alert alert-no-items"><?php echo Text::_('OS_NO_MATCHING_RESULTS');?></div>
        <?php
        }
        ?>
		<input type="hidden" name="option" value="com_osproperty" /> 
		<input type="hidden" name="task" value="extrafield_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order"  id="filter_order" value="<?php echo $lists['filter_order']; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
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
		global $mainframe,$configClass,$languages;
		
		$db = Factory::getDBO();
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/ajax.js");
		//HTMLHelper::_('behavior.formvalidation');
		$functionvalue = "Joomla.submitbutton = function(task)";
		$submitvalue = " Joomla.submitform(task);return true; ";
		
		OSPHelper::loadTooltip();
	
		if($row->id > 0){
			$edit = Text::_('OS_EDIT');
		}else{
			$edit = Text::_('OS_NEW');
		}
		ToolBarHelper::title(Text::_('OS_EXTRA_FIELD')." ".Text::_('['.$edit.']'));
		ToolBarHelper::save('extrafield_save');
		ToolBarHelper::save2new('extrafield_new');
		ToolBarHelper::apply('extrafield_apply');
		ToolBarHelper::cancel('extrafield_gotolist');
		?>
		<script type="text/javascript">
		 function showDiv(){
			var div_select     = document.getElementById('select');
			var div_checkbox   = document.getElementById('checkbox');
			var div_textarea   = document.getElementById('textarea');
			var div_inputbox   = document.getElementById('inputbox');
			div_select.style.display   = "none";
			div_checkbox.style.display = "none";
			div_textarea.style.display = "none";
			div_inputbox.style.display = "none";
			
			var field_type = document.getElementById('field_type');
			if((field_type.value == "text") || (field_type.value == "date")){
				div_inputbox.style.display = "block";
			}else if((field_type.value == "checkbox") || (field_type.value == "radio")){
				div_checkbox.style.display = "block";
			}else if((field_type.value == "singleselect") || (field_type.value == "multipleselect")){
				div_select.style.display = "block";
			}else if(field_type.value == "textarea"){
				div_textarea.style.display = "block";
			}
		}
		
		<?php echo $functionvalue?>{
			var form 		 = document.adminForm;
			group_id 		 = form.group_id;
			field_name  	 = form.field_name;
			field_label 	 = form.field_label;
			field_type  	 = form.field_type;
			type_id			 = document.getElementById('type_id');
			
			if((task == "extrafield_save") || (task == "extrafield_apply")){
				if(group_id.value == ""){
					alert("Please select group name");
					group_id.focus();
				}else if (field_name.value == ""){
					alert("Please enter field name");
					field_name.focus();
				}else if( ! multiselect_validate(type_id)){
					alert("Please select property types");
				}else{
					<?php echo $submitvalue?>
				}
			}else{
				<?php echo $submitvalue?>
			}
		}
		
		 function saveOption(fid,div_name,type){
			var str_value = "";
			var div = document.getElementById('div_name');
			div.value = div_name;
			var live_site = document.getElementById('live_site');
			live_site = live_site.value;
			var languages = document.getElementById('languages');
			languages = languages.value;
			languages = languages.split("|");
			if(languages.length > 0){
				for(i=0;i<languages.length;i++){
					lng = languages[i];
					var option_name = document.getElementById('option_name_' + lng + type);
					value = option_name.value;
					value = value.replace("+","@plus@");
					str_value += lng + "@@" + value + "||";
					option_name.value = "";
				}
			}
			//alert(str_value);
			str_value = str_value.substring(0,str_value.length-2);
			saveOptionAjax(fid,str_value,live_site,div_name,type);
		}
		
		 function removeOption(oid,fid,div_name,type){
			var div = document.getElementById('div_name');
			div.value = div_name;
			var live_site = document.getElementById('live_site');
			live_site = live_site.value;
			var answer = confirm("<?php echo Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM')?>");
			if(answer == 1){
				removeOptionAjax(oid,fid,live_site,div_name,type);
			}
		}
		
		 function saveChange(oid,fid,div_name,type){
			var str_value = "";
			var div = document.getElementById('div_name');
			div.value = div_name;
			var live_site = document.getElementById('live_site');
			live_site = live_site.value;
			var languages = document.getElementById('languages');
			languages = languages.value;
			languages = languages.split("|");
			if(languages.length > 0){
				for(i=0;i<languages.length;i++){
					lng = languages[i];
					var option_name = document.getElementById('option_' + lng + oid + type);
					value = option_name.value;
					value = value.replace("+","@plus@");
					str_value += lng + "@@" + value + "||";
					option_name.value = "";
				}
			}
			str_value = str_value.substring(0,str_value.length-2);
			var ordering	= document.getElementById('ordering_' + oid + type);
			ordering = ordering.value;
			saveChangeOptionAjax(oid,str_value,ordering,fid,live_site,div_name,type);
		}

		 function multiselect_validate(select) {  
			var valid = false;  
			for(var i = 0; i < select.options.length; i++) {  
			    if(select.options[i].selected) {  
					valid = true;  
					break;
			    }  
			}  
			return valid;  
		}
		</script>
		<form class="form-validate" method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm">
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
							<?php echo Text::_('OS_TYPE')?>
						</td>
						<td>
							<?php
								echo $lists['field_type'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_FIELD_GROUP')?>
						</td>
						<td>
							<?php
								echo $lists['group'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top">
							<?php echo Text::_('OS_PROPERTY_TYPE')?>
						</td>
						<td>
							<?php
								echo OSPHelper::getChoicesJsSelect($lists['type']);
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_FIELD_NAME')?>
						</td>
						<td>
							<input type="text" class="input-medium form-control ilarge" name="field_name" id="field_name" size="40" value="<?php echo $row->field_name?>" />
                            &nbsp;*<?php echo Text::_('OS_FIELD_NAME_EXPLAIN');?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_FIELD_TITLE')?>
						</td>
						<td>
							<input type="text" name="field_label" class="form-control input-medium ilarge" id="field_label" size="60" value="<?php echo $row->field_label?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_REQUIRED')?>
						</td>
						<td>
							<?php
							echo $lists['required'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_READONLY')?>
						</td>
						<td>
							<?php
							echo $lists['readonly'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_SEARCHABLE')?>
						</td>
						<td>
							<?php
							echo $lists['searchable'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_DISPLAY');?>
						</td>
						<td>
							<?php
							echo $lists['displaytitle'];
							?>
                            &nbsp;*<?php echo Text::_('OS_DISPLAY_EXPLAIN');?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_SHOW_ON_LIST_PROPERTIES')?>
						</td>
						<td>
							<?php
							echo $lists['show_on_list'];
							?>
                            &nbsp;*<?php echo Text::_('OS_SHOW_ON_LIST_EXPLAIN');?>
						</td>
					</tr>
					<tr>
						<td class="key" valign="top">
							<?php echo Text::_('OS_DESCRIPTION')?>
						</td>
						<td>
							<textarea name="field_description" id="field_description" cols="50" rows="3" class="form-control"><?php echo $row->field_description?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_SHOW_DESCRIPTION')?>
						</td>
						<td>
							<?php
							echo $lists['show_description'];
							?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_DEFAULT_VALUE')?>
						</td>
						<td>
							<input type="text" class="input-medium form-control ilarge" name="default_value" id="default_value" size="60" value="<?php echo $row->default_value?>">
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo Text::_('OS_ACCESS')?>
						</td>
						<td>
							<?php
							echo $lists['access'];
							?>
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
					<tr>
						<td class="key" valign="top">
							<?php echo Text::_('OS_OTHER_INFORMATION')?>
						</td>
						<td>
							<table  width="90%">
								<tr>
									<td valign="top" width="100%">
										<?php
										switch ($row->field_type){
											case "singleselect":
											case "multipleselect":
												$div_select    = "block";
												$div_checbox   = "none";
												$div_textarea  = "none";
												$div_inputbox  = "none";
												$div_date	   = "none";
											break;
											case "checkbox":
											case "radio":
												$div_select    = "none";
												$div_checbox   = "block";
												$div_textarea  = "none";
												$div_inputbox  = "none";
												$div_date	   = "none";
											break;
											default:
											case "date":
												$div_select    = "none";
												$div_checbox   = "none";
												$div_textarea  = "none";
												$div_inputbox  = "none";
												$div_date	   = "block";
											break;
											case "text":
												$div_select    = "none";
												$div_checbox   = "none";
												$div_textarea  = "none";
												$div_inputbox  = "block";
												$div_date	   = "none";
											break;
											case "textarea":
												$div_select    = "none";
												$div_checbox   = "none";
												$div_textarea  = "block";
												$div_inputbox  = "none";
												$div_date	   = "none";
											break;
										}
										?>
										<!-- For select tags -->
										<div style="width:100%;font-size:12px;display:<?php echo $div_select?>;" id="select">
											<table  width="100%" class="admintable">
												<tr>
													<td style="padding:3px;border-bottom:1px solid #efefef;font-weight:bold;color:#519DC5;background-color:#DAECF5;" align="left" colspan="2">
														
														<?php echo Text::_('OS_SINGLE_SELECT_MULTIPLE_SELECT')?>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_SIZE')?>
													</td>
													<td>
														<input type="text" name="select_size" id="select_size" size="10" value="<?php echo $row->size?>" class="input-mini form-control" /> px
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
														
														<span class="editlinktip hasTip hasTooltip" title="Options::Please enter options value of the tag. Each option in one line.">
														<?php echo Text::_('OS_OPTIONS')?></span>
													</td>
													<td style="font-size:11px !important;">
														<?php
														if($row->id == 0){
														?>
														<textarea name="select_options" id="select_options" cols="40" rows="7" class="form-control"><?php echo $row->options;?></textarea>
														<?php
														}else{
															?>
															<div id="option_div">
															<?php
															HelperOspropertyFields::manageFieldOptions($row->id,'option_div',0);
															?>
															</div>
															<?php
														}
														?>
													</td>
												</tr>
											</table>
										</div>
										<!-- For checkbox and radio tags -->
										<div style="width:100%;font-size:12px;display:<?php echo $div_checbox?>;" id="checkbox">
											<table  width="100%" class="admintable">
												<tr>
													<td style="padding:3px;border-bottom:1px solid #efefef;font-weight:bold;color:#519DC5;background-color:#DAECF5;" align="left" colspan="2">
														<?php echo Text::_('OS_CHECKBOX_RADIO')?>
														
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
														
														<span class="editlinktip hasTip hasTooltip" title="Options::Please enter options value of the tag. Each option in one line.">
														<?php echo Text::_('OS_OPTIONS')?></span>
													</td>
													<td style="font-size:11px !important;">
														<?php
														if($row->id == 0){
														?>
														<textarea name="checkbox_options" id="checkbox_options" cols="40" rows="7" class="form-control"><?php echo $row->options;?></textarea>
														<?php
														}else{
															?>
															<div id="option_div1">
															<?php
															HelperOspropertyFields::manageFieldOptions($row->id,'option_div1',1);
															?>
															</div>
															<?php
														}
														?>
													</td>
												</tr>
											</table>
										</div>
										<!-- For textarea tags -->
										<div style="width:100%;font-size:12px;display:<?php echo $div_textarea?>;" id="textarea">
											<table  width="100%" class="admintable">
												<tr>
													<td style="padding:3px;border-bottom:1px solid #efefef;font-weight:bold;color:#519DC5;background-color:#DAECF5;" align="left" colspan="2">
														<?php echo Text::_('OS_TEXTAREA')?>
														
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_NUMBER_COLUMNS')?>
													</td>
													<td>
														<input type="text" class="form-control" name="ncols" id="ncols" value="<?php echo $row->ncols?>" size="10">
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_NUMBER_ROWS')?>
													</td>
													<td>
														<input type="text" name="nrows" class="form-control"  id="nrows" value="<?php echo $row->nrows?>" size="10">
													</td>
												</tr>
											</table>
										</div>
										<!-- For other fields -->
										<div style="width:100%;font-size:12px;display:<?php echo $div_inputbox?>;" id="inputbox">
											<table  width="100%" class="admintable">
											
												<tr>
													<td style="padding:3px;border-bottom:1px solid #efefef;font-weight:bold;color:#519DC5;background-color:#DAECF5;" align="left" colspan="2">
														<?php echo Text::_('OS_TEXTBOX_DATE_FIELD')?>
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_VALUE_TYPE');?>
													</td>
													<td>
														<?php
														$valueType = array();
														$valueType[] = HTMLHelper::_('select.option','0',Text::_('OS_TEXT'));
														$valueType[] = HTMLHelper::_('select.option','1',Text::_('OS_INTEGER'));
														$valueType[] = HTMLHelper::_('select.option','2',Text::_('OS_DECIMAL'));
														echo HTMLHelper::_('select.genericlist',$valueType,'value_type','class="input-small form-select ilarge"','value','text',$row->value_type);
														?>
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_SIZE')?>
													</td>
													<td>
														<input type="text" name="text_size"  id="text_size" value="<?php echo $row->size?>" size="10" class="input-mini form-control ishort">
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_MAXLENGTH')?>
													</td>
													<td>
														<input type="text" name="maxlength" id="maxlength" value="<?php echo $row->maxlength?>" size="10" class="input-mini form-control ishort">
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
													 	<?php echo Text::_('OS_CLICKABLE')?>
													</td>
													<td>
														<?php
														$optionArr[] 					= HTMLHelper::_('select.option',1,Text::_('OS_YES'));
														$optionArr[] 					= HTMLHelper::_('select.option',0,Text::_('OS_NO'));
														echo  HTMLHelper::_('select.genericlist',$optionArr,'clickable','class="input-mini form-select ishort"','value','text',$row->clickable);
														?>
													</td>
												</tr>
											</table>
										</div>
										<div style="width:100%;font-size:12px;display:<?php echo $div_date?>;" id="">
											<?php echo Text::_('OS_DATEFIELD_INFORMATION');?>
										</div>
									</td>
								</tr>
							</table>
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
										<?php echo Text::_('OS_FIELD_TITLE')?>
									</td>
									<td>
										<input type="text" class="form-control input-medium ilarge" name="field_label_<?php echo $sef;?>" id="field_label_<?php echo $sef;?>" size="60" value="<?php echo $row->{'field_label_'.$sef}?>" />
									</td>
								</tr>
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_DESCRIPTION')?>
									</td>
									<td>
										<textarea class="form-control" name="field_description_<?php echo $sef;?>" id="field_description_<?php echo $sef;?>" cols="50" rows="3"><?php echo $row->{'field_description_'.$sef}?></textarea>
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
		<input type="hidden" name="div_name" id="div_name" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		</form>
		<?php
	}
}
?>