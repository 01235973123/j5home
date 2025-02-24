<?php
/*------------------------------------------------------------------------
# csvform.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2024 joomdonation.com. All Rights Reserved.
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


class HTML_OspropertyCsvform{
	/**
	 * Import CSV
	 *
	 * @param unknown_type $option
	 * @param unknown_type $log
	 */
	static function completeImportCsv($option,$log){
		global $mainframe;
		ToolBarHelper::title(Text::_('OS_IMPORT_CSV_COMPLETE'));
		ToolBarHelper::custom('form_default','checkin.png', 'checkin.png',Text::_('OS_CSV_FORMS'),false);
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm">
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="50%">
						<?php echo Text::_('OS_IMPORT_COMPLETED')?> 
					</th>
					<th width="50%">
						<?php echo Text::_('OS_IMPORT_NOT_COMPLETED')?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="50%" style="text-align:left;border-right:1px solid #CCC;border-bottom:1px solid #CCC;border-left:1px solid #CCC;background-color:#E5FAE3;">
						<?php
						echo $log->log2;
						?>
					</td>
					<td width="50%" style="text-align:left;border-bottom:1px solid #CCC;border-right:1px solid #CCC;background-color:#F8E5E5;">
						<?php
						echo $log->log1;
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" id="boxchecked" value="0">
		</form>
		<?php
	}
	
	static function updateOtherInformationForm($option,$properties,$row,$lists){
		global $mainframe,$configClass,$_jversion;
		ToolBarHelper::title(Text::_('OS_IMPORT_OTHER_INFORMATION')." [".$row->form_name."]");
		ToolBarHelper::save('form_saveotherinformation');
		ToolBarHelper::cancel('form_cancel');
		
		?>
		<script type="text/javascript" src="<?php echo Uri::root()?>media/com_osproperty/assets/js/ajax.js"></script>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div style="width:100%;">
			<fieldset>
				<legend><?php echo Text::_('OS_STEP3')?>: <?php echo Text::_('OS_IMPORT_OTHER_INFORMATION')?></legend>
				<div style="width:98%;padding:10px;">
				
				<table class="table table-striped">
					<thead>
						<tr>
							<th width="2%">
								#
							</th>
							<th width="15%">
								<?php echo Text::_('OS_PROPERTY_NAME')?>
							</th>
							<th width="20%">
								<?php echo Text::_('OS_AGENT')?>
							</th>
							<th width="10%">
								<?php echo Text::_('OS_CATEGORY')?>
							</th>
							<th width="10%">
								<?php echo Text::_('OS_PROPERTY_TYPE')?>
							</th>
							<?php 
							if(HelperOspropertyCommon::checkCountry()) {
							?>
							<th width="10%">
								<?php echo Text::_('OS_COUNTRY')?>
							</th>
							<?php
							}
							?>
							<th width="10%">
								<?php echo Text::_('OS_STATE')?>
							</th>
							<th width="10%">
								<?php echo Text::_('OS_CITY')?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$proArr = array();
						for($i=0;$i<count($properties);$i++){
							$property = $properties[$i];
							$proArr[] = $property->id;
							$country_script = " onChange='javascript:changeCountryValue($property->id)'";
							$state_script = " onChange='javascript:changeStateValue($property->id)' ";
							?>
							<tr>
								<td style="text-align:center;">
									<?php echo $i + 1?>
								</td>
								<td style="text-align:left;">
									<?php echo $property->pro_name?>
								</td>
								<td style="text-align:center;">
									
									<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['agentArr'],'agent_id'.$property->id,'class="input-small"','value','text');
									?>
									</td><td>
									<label> Or <?php echo Text::_('OS_AGENT_ID')?>:</label> <input type="text" class="input-mini" size="4" name="agent_id_value<?php echo $property->id?>" id="agent_id_value<?php echo $property->id?>">
									</td>
									</tr>
									</table>
									
								</td>
								<td style="text-align:center;">
									<?php
									$parentArr = OspropertyProperties::loadCategoryOptions($category_id,$onChangeScript);
									echo HTMLHelper::_('select.genericlist',$parentArr,'category_id'.$property->id,'style="width:120px;" class="inputbox" ','value','text');
									?>
								</td>
								<td style="text-align:center;">
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['type'],'pro_type'.$property->id,'style="width:120px;" class="inputbox" ','value','text');
									?>
								</td>
								<?php 
								if(HelperOspropertyCommon::checkCountry()) {
								?>
								<td style="text-align:center;">
									
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['country'],'country'.$property->id,'style="width:140px;" class="input-small" '.$country_script,'value','text');
									
									$disabled = "disabled";
									?>
									
								</td>
								<?php
								}else{
									?>
									<input type="hidden" name="country<?php echo $property->id?>" id="country<?php echo $property->id?>" value="<?php echo HelperOspropertyCommon::getDefaultCountry();?>">
									<?php
									$disabled = "";
								}
								?>
								<td style="text-align:center;">
									
									<div id="div_state_<?php echo $property->id?>">
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['state'],'state'.$property->id,'style="width:140px;" class="input-small" '.$disabled.' '.$state_script,'value','text');
									?>
									</div>
									
								</td>
								<td style="text-align:center;">
									
									<div id="div_city_<?php echo $property->id?>">
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['city'],'city'.$property->id,'style="width:140px;"  class="input-small" disabled','value','text');
									?>
									</div>
									
								</td>
							</tr>
							<?php
						}
						
						$property_str = implode(",",$proArr);
						?>
					</tbody>
				</table>
			</fieldset>
		</div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="1" />
		<input type="hidden" name="id" value="<?php echo $row->id;?>" />
		<input type="hidden" name="current_item" id="current_item" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
		<input type="hidden" name="property_str" id="property_str" value="<?php echo $property_str?>"  />
		</form>
		<script type="text/javascript">
		function changeCountryValue(pid){
			var current_item = document.getElementById('current_item');
			current_item.value = pid;
			var country = document.getElementById('country' + pid);
			if(country != null){
				changeCountryAjax(pid,country.value,"<?php echo Uri::root()?>");
			}
		}
		
		function changeStateValue(pid){
			var current_item = document.getElementById('current_item');
			current_item.value = pid;
			var state = document.getElementById('state' + pid);
			if(state != null){
				changeStateAjax(pid,state.value,"<?php echo Uri::root()?>");
			}
		}
		
		<?php if ($_jversion == "1.5"){?>
				function submitbutton(pressbutton)
			<?php }else{?>
				Joomla.submitbutton = function(pressbutton)
			<?php }?>{
					var form = document.adminForm;
					if(pressbutton == "form_saveotherinformation"){
						var canSubmit = 1;
						var property_str = form.property_str.value;
						var proArr = property_str.split(",");
						if(proArr.length > 0){
							for(i=0;i<proArr.length;i++){
								property_id = proArr[i];
								
								agent_id = document.getElementById('agent_id' + property_id);
								agent_id_value = document.getElementById('agent_id_value' + property_id);
								if((agent_id.value == "") && (agent_id_value.value == "")){
									canSubmit = 0;
								}
								country = document.getElementById('country' + property_id);
								state = document.getElementById('state' + property_id);
								city = document.getElementById('city' + property_id);
								category_id = document.getElementById('category_id' + property_id);
								pro_type = document.getElementById('pro_type' + property_id);
								
								if(category_id.value == ""){
									canSubmit = 0;
								}
								
								if(pro_type.value == ""){
									canSubmit = 0;
								}
								
								if(country.value == ""){
									canSubmit = 0;
								}
								
								if(state.value == ""){
									canSubmit = 0;
								}
								if(city.value == ""){
									canSubmit = 0;
								}
							}
							
							if(canSubmit == 0){
								alert("<?php echo Text::_('OS_PLEASE_FILL_ALL_INFORMATION')?>");
							}else{
								form.task.value = pressbutton;
								form.submit();		
							}
						}
					}else{
						form.submit();
					}
				}
		</script>
		<?php
	}
	
	/**
	 * Photo form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $form_id
	 */
	static function importPhotoForm($option,$form_id){
		global $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_csv_forms where id = '$form_id'");
		$row = $db->loadObject();
		ToolBarHelper::title(Text::_('OS_IMPORT_PHOTO_PACK')." [".$row->form_name."]");
		if (version_compare(JVERSION, '3.0', 'lt')) {
			ToolBarHelper::custom('form_completeimport','forward.png','forward.png','Skip');
			ToolBarHelper::custom('form_doimportphoto','export.png','export.png',Text::_('OS_IMPORTPHOTO'));
		}else{
			ToolBarHelper::custom('form_completeimport','refresh.png','refresh.png','Skip',false);
			ToolBarHelper::custom('form_doimportphoto','upload.png','upload.png',Text::_('OS_IMPORTPHOTO'),false);
		}
		ToolBarHelper::cancel('form_cancel');
		?>
		<script type="text/javascript">
		function check_file(){
            str=document.getElementById('photopack').value.toUpperCase();
	        suffix=".ZIP";
	        if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
	        	alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.zip');
	            document.getElementById('photopack').value='';
	        }
	    }

		</script>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div style="width:100%;">
			<fieldset>
				<legend><?php echo Text::_('OS_STEP2')?>: <?php echo Text::_('OS_SELECT_PHOTO_PACKAGAE')?></legend>
				<div style="width:98%;padding:10px;">
				<div style="background-color:pink;border:1px solid red !important;padding:10px;">
				<b>
					<?php echo Text::_('OS_NOTICE')?>:
				</b>
				<br/>
                    <span class='badge badge-success'>1</span> &nbsp;Make sure you have the photos in the zip file. The photos must be in the root folder of zip file. This function won't import photos from the sub-directory folder. <BR /><BR /><span class='badge badge-success'>2</span> &nbsp; Photo name should not have white space before, after and between it.<BR /><BR /><span class='badge badge-success'>3</span> &nbsp;In case you want to upload photos for a large amount properties, you need to reduce the size of photos before uploading to sudden failure from server<BR /><BR /><span class='badge badge-success'>4</span> &nbsp;Photo must be *.JPG,*.JPEG files
				</div>
				<BR />
				<div class="row-fluid">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#uploaddiv" data-toggle="tab"><?php echo Text::_('OS_UPLOAD_PHOTO_PACKAGE');?></a></li>
						<li><a href="#directdiv" data-toggle="tab"><?php echo Text::_('OS_INSTALL_FROM_DIRECTORY');?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="uploaddiv">
							<div class="width-100 fltlft">
								<fieldset class="adminform form-horizontal options-form">
									<input type="file" name="photopack" id="photopack" size="50" class="input-large form-control" onchange="javascript:check_file()"> (Only allow: *.zip)
									<div class="clr">
				
									</div>
									<div>
										<i><?php echo Text::_('OS_ONLY_SUPPORT_ZIP_FILE')?></i>
									</div>
								</fieldset>
							</div>
						</div>
						<div class="tab-pane" id="directdiv">
							<div class="width-100 fltlft">
								<fieldset class="adminform form-horizontal options-form">
									<input type="text" name="photodirectory" id="photodirectory" class="input-large" value="<?php echo JPATH_ROOT?>/tmp" style="width:300px;"> (Only allow: *.zip)
									<div class="clr">
				
									</div>
								</fieldset>
							</div>
						</div>
					</dov>
				</div>
			</fieldset>
		</div>
		<input type="hidden" name="option" value="com_osproperty" /> 
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="1" />
		<input type="hidden" name="id" value="<?php echo $form_id;?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="900000000" />
		</form>
		<?php
	}
	/**
	 * Import CSV Form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 */
	static function importCsvForm($option,$row,$lists){
		global $mainframe;
		$configClass = OSPHelper::loadConfig();
		ToolBarHelper::title(Text::_('OS_IMPORT_CSV')." [".$row->form_name."]");
		if (version_compare(JVERSION, '3.0', 'lt')) 
		{
			ToolBarHelper::custom('form_doimportcsv','export.png','export.png',Text::_('OS_IMPORTCSV'), true);
		}
		else
		{
			ToolBarHelper::custom('form_doimportcsv','upload.png','upload.png',Text::_('OS_IMPORTCSV'), false);
		}
		ToolBarHelper::cancel('form_cancel');
		?>
		<script type="text/javascript">
		function check_file(){
            str=document.getElementById('csv_file').value.toUpperCase();
	        suffix=".CSV";
	        if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
	        	alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.csv');
	            document.getElementById('csv_file').value='';
	        }
	    }

		</script>
		<form method="POST" action="index.php?option=com_osproperty&task=form_default" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div style="width:100%;">
			<fieldset>
				<legend><?php echo Text::_('OS_STEP1')?>: <?php echo Text::_('OS_SELECT_CSV_FILE')?></legend>
				<div style="width:98%;padding:10px;">
				<div style="background-color:pink;border:1px solid red !important;padding:10px;">
				<b>
					<?php echo Text::_('OS_NOTICE')?>:
				</b>
				<br/><span class='badge badge-success'>1</span> &nbsp;Photo names will be in one column, separate by comma. The Order of columns should be the same with CSV form - you can download in the CSV form lists - We are not responsible in case the order of the columns are wrong.  The price must have the format : 10000.00 (no comma, no white space in the price value), do not use Currency signal in the price column. We import data, OS Property will use the currency of the system. We are not responsible in case the price violated this notice. <BR/><BR/><span class='badge badge-success'>2</span>&nbsp;Fields: Price Call, Show Address must use value Yes/No as you already defined in Form details. <BR /><BR/><span class='badge badge-success'>3</span>&nbsp;Convenience, photos must be separate by | symbol
				<Br />
				<span class="badge badge-success">4</span>
				<?php echo Text::_('OS_IMPORT_NON_UTF8_DATA');?> <?php echo $lists['utf'];?>
				<Br />
				<span class="badge badge-success">5</span>
				<?php echo Text::_('OS_REMOVE_ALL_PROPERTIES_BEFORE_IMPORTING');?> <?php echo $lists['removeproperties'];?>
				<Br />
				<span class="badge badge-success">6</span>
				<?php echo Text::_('OS_RENT_TIME_FRAME_NOTICE');?>
				<BR/><BR/>
				OS_NOT_APPLICABLE: <?php echo Text::_('OS_NOT_APPLICABLE');?><BR/>
				OS_PER_SQUARE_METRE: <?php echo Text::_('OS_PER_SQUARE_METRE');?><BR/>
				OS_PER_SQUARE_FEET: <?php echo Text::_('OS_PER_SQUARE_FEET');?><BR/>
				OS_PER_MONTH: <?php echo Text::_('OS_PER_MONTH');?><BR/>
				OS_PER_FORTHNIGHT: <?php echo Text::_('OS_PER_FORTHNIGHT');?><BR/>
				OS_PER_WEEK: <?php echo Text::_('OS_PER_WEEK');?><BR/>
				<BR/>
				<?php
				$market_status 		= $configClass['market_status'];
				if(($market_status != "") && ($configClass['active_market_status'])){
					?>
					<span class="badge badge-success">7</span>
					<?php
					echo Text::_('OS_MARKET_STATUS_FIELD');
					$market_status_array = explode(",",$market_status);
					?>
					<BR />
					<ul>
						<?php
						if(in_array('1',$market_status_array)){
							?>
							<li>
								<?php echo Text::_('OS_SOLD');?> - <strong>1</strong>
							</li>
							<?php
						}
						?>
						<?php
						if(in_array('2',$market_status_array)){
							?>
							<li>
								<?php echo Text::_('OS_CURRENT');?> - <strong>2</strong>
							</li>
							<?php
						}
						?>
						<?php
						if(in_array('3',$market_status_array)){
							?>
							<li>
								<?php echo Text::_('OS_RENTED');?> - <strong>3</strong>
							</li>
							<?php
						}
						?>
					</ul>
				<?php } ?>
				</div>				
				
				<input type="file" name="csv_file" id="csv_file" size="50" class="input-large form-control" onchange="javascript:check_file()">
				<div class="clr">
				
				</div>
				<i><?php echo Text::_('OS_MAX_FILE_SIZE')?> <?php echo $row->max_file_size?> MB</i>
				
				<div class="clr">
				
				</div>
				<!--
				<label><?php echo Text::_('OS_SELECT_AGENT')?>: &nbsp;</label><?php echo $lists['agent'];?>
				<div class="clr">
				
				</div>
				<label><?php echo Text::_('OS_PHOTO_SOURCE')?>: &nbsp;</label> <?php echo $lists['photo'];?>
				</div>
				<div class="clr">
				-->
				</div>
				
			</fieldset>
		</div>
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" id="boxchecked" value="1" />
		<input type="hidden" name="id" value="<?php echo $row->id?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $row->max_file_size*1024*1024?>"/>
		</form>
		<?php
	}
	/**
	 * Default listing csv form lists
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function defaultList($option,$rows,$pageNav){
		global $mainframe,$_jversion,$configClass;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_CSVFORMS'),"download");
		ToolBarHelper::addNew('form_add');
		ToolBarHelper::editList('form_edit');
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'form_remove');
		ToolBarHelper::publish('form_publish');
		ToolBarHelper::unpublish('form_unpublish');
		if (version_compare(JVERSION, '3.0', 'lt')) {
			ToolBarHelper::custom('form_importcsv','export.png','export.png',Text::_('OS_IMPORTCSV'));
		}else{
			ToolBarHelper::custom('form_importcsv','upload.png','upload.png',Text::_('OS_IMPORTCSV'));
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=form_default" name="adminForm" id="adminForm">
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="5%">
					
					</th>
					<th width="5%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="20%">
						<?php echo Text::_('OS_FORM_NAME')?>
					</th>
					<th width="10%">
						<?php echo Text::_('OS_CSV_FORM')?>
					</th>
					<th width="15%">
						<?php echo Text::_('OS_MAX_FILE_SIZE')?>
					</th>
					<th width="10%">
						<?php echo Text::_('OS_IMPORT')?>
					</th>
					<th width="15%">
						<?php echo Text::_('OS_LAST_IMPORTED')?>
					</th>
					<th width="15%">
						<?php echo Text::_('OS_CREATED_ON')?>
					</th>
					<th width="10%">
						<?php echo Text::_('OS_PUBLISHED')?>
					</th>
					
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td style="text-align:center;" colspan="9">
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
					$link 		= Route::_( 'index.php?option=com_osproperty&task=form_edit&cid[]='. $row->id );
					$published 	= HTMLHelper::_('jgrid.published', $row->published, $i , 'form_');
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center"><?php echo $pageNav->getRowOffset( $i ); ?></td>
						<td align="center"><?php echo $checked; ?></td>
						<td>
							<a href="<?php echo $link?>">
								<?php echo $row->form_name?>
							</a>
						</td>
						<td style="text-align:center;">
							<a href="index.php?option=com_osproperty&task=form_downloadcsv&cid[]=<?php echo $row->id?>&no_html=1" title="<?php echo Text::_('OS_DOWNLOAD_CSV_FORM')?>">
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/csv16.png" border="0" >
							</a>
						</td>
						<td style="text-align:center;">
							<?php echo $row->max_file_size?> MB
						</td>
						<td style="text-align:center;">
							<a href="index.php?option=com_osproperty&task=form_importcsv&cid[]=<?php echo $row->id?>" title="<?php echo Text::_('OS_IMPORT_CSV')?>">
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/import16.png" border="0">
							</a>
						</td>
						<td>
							<?php echo $row->last_import?>
						</td>
						<td style="text-align:center;">
							<?php echo $row->created_on?>
						</td>
						<td style="text-align:center;">
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
		<input type="hidden" name="task" value="form_default" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	/**
	 * Edit HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editHTML($option,$row,$lists){
		global $mainframe,$_jversion,$configClass,$jinput;
		OSPHelper::loadTooltip();
		$jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_CSV_FORM').$title);
		ToolBarHelper::save('form_save');
		ToolBarHelper::apply('form_apply');
		ToolBarHelper::cancel('form_cancel');
		
		if($_jversion != "1.5"){
			$functionvalue = "Joomla.submitbutton = function(task)";
			$submitvalue = "Joomla.submitform(task);";
		}else{
			$functionvalue = "function submitbutton(task)";
			$submitvalue = "submitform(task);";
		}
		
		$requirelabelArr = $lists['requirelabels'];
		$requirefieldArr = $lists['requirefields'];
		$require_fields = $lists['requireid'];
		$fields	= $lists['fields'];
		$labels	= $lists['labels'];
		
		$optionArr = array();
		$tmp	   = new \stdClass();
		$tmp->value = "";
		$tmp->text = "";
		$optionArr[0] = $tmp;

		for($i=0;$i<count($fields);$i++)
		{
			$tmp	   = new \stdClass();
			$tmp->value = $fields[$i];
			$tmp->text = $labels[$i];
			$optionArr[$i+1] = $tmp;
		}
		
		$optionArr1 = array();
		for($i=0;$i<count($fields);$i++)
		{	
			$tmp	   = new \stdClass();
			$tmp->value = $fields[$i];
			$tmp->text = $labels[$i];
			$optionArr1[$i+1] = $tmp;
		}
		?>
		<script type="text/javascript">
		<?php
		echo $functionvalue;
		?>{
			
			if(task == "form_cancel"){
				<?php
				echo $submitvalue;
				?>
			}else{
				var form = document.adminForm;
				var require_labels = form.require_labels;
				var require_str = "";
				if(require_labels.options.length > 0){
					for(i=0;i<require_labels.options.length;i++){
						if(require_labels.options[i].selected == false){
							require_str += require_labels.options[i].text + ", ";
						}
					}
					if(require_str != ""){
						require_str = require_str.substring(0,require_str.length-2);
						alert("<?php echo Text::_('OS_PLEASE_ENTER_FIELDS')?> " + require_str);
					}else{
						<?php
						echo $submitvalue;
						?>
					}
				}else{
					<?php
					echo $submitvalue;
					?>
				}
			}
		}
		
		function updateSelectList(fieldid){
			var fieldname = document.getElementById("fields" + fieldid);
			var itemvalue = fieldname.value;
			var form = document.adminForm;
			var require_labels = form.require_labels;
			if(require_labels.options.length > 0){
				for(i=0;i<require_labels.options.length;i++){
					//if(require_labels.options[i].value == itemvalue){
						require_labels.options[i].selected = false;
					//}
				}
			}
			for(i=1;i<=50;i++){
				fieldname = "fields" + i;
				fielditem = document.getElementById(fieldname);
				if(fielditem.value != ""){
					for(j=0;j<require_labels.options.length;j++){
						if(require_labels.options[j].value == fielditem.value){
							require_labels.options[j].selected = true;
						}
					}
				}
			}
			
			
			
			var selected_field = document.getElementById('selected_field');
			var length = selected_field.length;
			for(i=0;i<length;i++){
				if((selected_field.options[i].value == itemvalue) && (selected_field.options[i].selected == true)){
					alert("You cannot select this field again");
					document.getElementById("fields" + fieldid).value = "";
					selected_field.options[i].selected = false;
				}
			}
			
			
			for(i=1;i<=50;i++){
				fieldname = "fields" + i;
				fielditem = document.getElementById(fieldname);
				if(fielditem.value != ""){
					for(j=0;j<selected_field.options.length;j++){
						if(selected_field.options[j].value == fielditem.value){
							selected_field.options[j].selected = true;
						}
					}
				}
			}
		}
		
		function showDiv(type,div_name){
			var temp = document.getElementById(div_name);
			if(type == 0){
				temp.style.display = "none";
			}else if(type==1){
				temp.style.display = "block";
			}
		}
		</script>
		
		<form method="POST" action="index.php?option=com_osproperty&task=form_default" name="adminForm" id="adminForm">
		<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
			<tr>
				<td class="key">
					<?php
						echo Text::_('OS_FORM_NAME');
					?>
				</td>
				<td>
					<input type="text" class="input-large form-control" name="form_name" id="form_name" size="40" value="<?php echo $row->form_name;?>">
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php
						echo Text::_('OS_MAX_FILE_SIZE');
					?>
				</td>
				<td>
					<input type="text" class="input-mini form-control" name="max_file_size" id="max_file_size" size="2" value="<?php echo $row->max_file_size;?>"> MB
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php
						echo Text::_('OS_INFORMATION');
					?>
				</td>
				<td>
					<div class="csvmodificationformpart">
						<span class="label label-important"><?php echo Text::_('OS_PLEASE_READ_AND_ENTER_CONFIG_PARAMETERS');?></span>
						<BR /><BR />
						
						<span class="badge badge-warning">1</span> &nbsp;
						<?php echo Text::_('OS_FIEDS');?>: <?php echo Text::_('OS_CALL_FOR_PRICE')?>, <?php echo Text::_('OS_SHOW_ADDRESS')?> <?php echo Text::_('OS_ARE_YES_NO_FIELDS');?>
						<BR/>
						<span class="badge badge-warning">2</span> &nbsp;
						<?php echo Text::_('OS_PLEASE_ENTER_YES_NO_VALUE_THAT_YOU_WILL_ENTER_IN_CSV_FILE')?>
						<BR/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo Text::_('OS_YES')?>:<input type="text" class="input-mini form-control smallSizeBox" name="yes_value" id="yes_value" value="<?php echo $row->yes_value;?>" />
						&nbsp;&nbsp;&nbsp;
						<?php echo Text::_('OS_NO')?>:<input type="text" class="input-mini form-control smallSizeBox" name="no_value" id="no_value" value="<?php echo $row->no_value;?>"/>
						<BR/>
						<span class="badge badge-warning">3</span> &nbsp;
						<?php echo Text::_('OS_PHOTO_FIELD_ENTER_PHOTO_NAMES_SEPERATED_BY_SEMICOLON');?>
						<BR/>
						<span class="badge badge-warning">4</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_PROPERTY_TYPE_THE_SYSTEM_WILL');?>:
						<BR />
						<?php
						$display   = "none";
						if($row->ftype == 0){
							$fchecked1 = "checked";
							$fchecked2 = "";
							$display   = "none";
						}else{
							$fchecked1 = "";
							$fchecked2 = "checked";
							$display   = "block";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="ftype" id="ftype" value="0" onClick="javascript:showDiv(0,'type_div');" <?php echo $fchecked1;?> /> <?php echo Text::_('OS_CREATE_NEW_PROPERTY_TYPE')?>
							<BR />
							<input type="radio" name="ftype" id="ftype" value="1" onClick="javascript:showDiv(1,'type_div');" <?php echo $fchecked2;?> /> <?php echo Text::_('OS_SELECT_DEFAULT_PROPERTY_TYPE')?>
							<div style="display:<?php echo $display?>;" id="type_div">
								<?php echo $lists['type'];?>
							</div>
						</div>
						<BR/>
						<span class="badge badge-warning">5</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_CATEGORY_THE_SYSTEM_WILL');?>:
						<BR />
						<?php
						$display   = "none";
						if($row->fcategory == 0){
							$cchecked1 = "checked";
							$cchecked2 = "";
							$display   = "none";
						}else{
							$cchecked1 = "";
							$cchecked2 = "checked";
							$display   = "block";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="fcategory" id="fcategory" value="0" onClick="javascript:showDiv(0,'category_div');" <?php echo $cchecked1;?> /> <?php echo Text::_('OS_CREATE_NEW_CATEGORY')?>
							<BR />
							<input type="radio" name="fcategory" id="fcategory" value="1" onClick="javascript:showDiv(1,'category_div');" <?php echo $cchecked2;?> /> <?php echo Text::_('OS_SELECT_DEFAULT_CATEGORY')?>
							<div style="display:<?php echo $display;?>;" id="category_div">
								<?php echo $lists['category'];?>
							</div>
						</div>
						<BR/>
						<span class="badge badge-warning">6</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_AGENT_PLEASE_SELECT_DEFAULT_AGENT');?>:
						<?php echo $lists['agent'];?>
						<BR/>
						<span class="badge badge-warning">7</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_COUNTRY_PLEASE_ENTER_DEFAULT_COUNTRY_ID');?>:
						<?php echo $lists['country'];?>
						<BR/>
						<span class="badge badge-warning">8</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_STATE_PLEASE_SELECT_DEFAULT_STATE');?>:
						<?php
						$display   = "none";
						if($row->fstate == 0){
							$schecked1 = "checked";
							$schecked2 = "";
							$display   = "none";
						}else{
							$schecked1 = "";
							$schecked2 = "checked";
							$display   = "block";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="fstate" id="fstate" value="0" onClick="javascript:showDiv(0,'state_div');"  <?php echo $schecked1;?> /> <?php echo Text::_('OS_CREATE_NEW_STATE')?>
							<BR />
							<input type="radio" name="fstate" id="fstate" value="1" onClick="javascript:showDiv(1,'state_div');"  <?php echo $schecked2;?> /> <?php echo Text::_('OS_SELECT_DEFAULT_STATE')?>
							<div style="display:<?php echo $display;?>;" id="state_div">
								<?php 
								echo OspropertyCsvform::getStateInput($row->state);
								?>
							</div>
						</div>
						<BR/>
						<span class="badge badge-warning">9</span> &nbsp;
						<?php echo Text::_('OS_WHEN_SYSTEM_CANNOT_FIND_CITY_PLEASE_SELECT_DEFAULT_CITY');?>:
						<?php
						$display   = "none";
						if($row->fcity == 0){
							$tchecked1 = "checked";
							$tchecked2 = "";
							$display   = "none";
						}else{
							$tchecked1 = "";
							$tchecked2 = "checked";
							$display   = "block";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="fcity" id="fcity" value="0" onClick="javascript:showDiv(0,'city_div');" <?php echo $tchecked1;?> /> <?php echo Text::_('OS_CREATE_NEW_CITY')?>
							<BR />
							<input type="radio" name="fcity" id="fcity" value="1" onClick="javascript:showDiv(1,'city_div');" <?php echo $tchecked2;?>/> <?php echo Text::_('OS_SELECT_DEFAULT_CITY')?>
							<div style="display:<?php echo $display;?>;" id="city_div">
								<?php 
								echo OspropertyCsvform::getCityInput($row->city);
								?>
							</div>
						</div>
						<BR/>
						<span class="badge badge-warning">10</span> &nbsp;
						<?php echo Text::_('OS_IMPORT_PICTURE_FROM');?>:
						<?php
						if($row->image_type == 0){
							$tchecked1 = "checked";
							$tchecked2 = "";
						}else{
							$tchecked1 = "";
							$tchecked2 = "checked";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="image_type" id="image_type" value="0" <?php echo $tchecked1;?> /> <?php echo Text::_('OS_FROM_COMPRESS_FILE')?>
							<BR />
							<input type="radio" name="image_type" id="image_type" value="1" <?php echo $tchecked2;?>/> <?php echo Text::_('OS_FROM_OTHER_HOSTING')?>
						</div>
						<BR/>
						<span class="badge badge-warning">11</span> &nbsp;
						<?php echo Text::_('OS_UPDATE_PROPERTIES_WHEN');?>:
						<?php
						if($row->update_type == 0){
							$tchecked1 = "checked";
							$tchecked2 = "";
						}else{
							$tchecked1 = "";
							$tchecked2 = "checked";
						}
						?>
						<div style="padding-left:35px;">
							<input type="radio" name="update_type" id="update_type" value="0" <?php echo $tchecked1;?> /> <?php echo Text::_('OS_HAVING_SAME_REF_AND_TITLE')?>
							<BR />
							<input type="radio" name="update_type" id="update_type" value="1" <?php echo $tchecked2;?>/> <?php echo Text::_('OS_HAVING_SAME_REF')?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top">
					<?php
					echo Text::_('OS_ACTIVE_CRON_IMPORT');
					?>
				</td>
				<td>
					<?php echo $lists['active_cron_import'];?>
					<BR />
					<?php echo Text::_('OS_ACTIVE_CRON_IMPORT_EXPLAIN');?>
					<BR />
					<div class="csv_import">
						<strong><?php echo Text::_('OS_CSV_PATH'); ?></strong>
						<?php echo JPATH_ROOT.'/';?><input type="text" id="csv_file" class="input-xxlarge" name="csv_file" value="<?php echo $row->csv_file; ?>" />
					</div>

				</td>
			</tr>
			<tr>
				<td class="key">
					<?php
						echo Text::_('OS_PUBLISHED');
					?>
				</td>
				<td>
					<?php echo $lists['published'];?>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td class="csvmodificationformpart1">
					<div style="width:100%">
						<div style="float:left;margin-right:10px;">
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/notice.png">
						</div>
						<?php
						echo Text::_('OS_CSV_FORM_EXPLAIN');
						?>
					</div>
				</td>
			</tr>
		</table>
		<BR>
		<table width="100%">
			<tr>
				<td class="csvmodificationformpart2">
					<b>
						<?php
							echo Text::_('OS_REQUIRED_FIELDS').": ";
						?>
					</b>
					<div id="required_div" >
						
					</div>
				</td>
			</tr>
		</table>
		
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<?php
				$j = 0;
				$fieldArr = array();
				for($i=1;$i<=50;$i++){
					$j++;
					?>
					<td width="20%" style="padding:10px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="40%" style="padding:2px;">
									<?php echo Text::_('OS_COLUMN')?> <?php echo $i;?>:
								</td>
								<td width="60%" style="padding:2px;text-align:left;">
									<?php
									$db->setQuery("Select `field` from #__osrs_form_fields where form_id = '$row->id' and column_number = '$i'");
									$fieldvalue = $db->loadResult();
									$fieldArr[] = $fieldvalue;
									echo HTMLHelper::_('select.genericlist',$optionArr,'fields'.$i,'onChange="javascript:updateSelectList('.$i.')" style="width:130px;" class="inputbox form-select"','value','text',$fieldvalue);
									?>
								</td>
							</tr>
							<tr>
								<td width="40%" style="padding:2px;">
									<?php echo Text::_('OS_HEADER')?>:
								</td>
								<td width="60%" style="padding:2px;text-align:left;">
									<?php
									$header_text = "";
									$db->setQuery("Select `header_text` from #__osrs_form_fields where form_id = '$row->id' and column_number = '$i'");
									$header_text = $db->loadResult();
									?>
									<input type="text" class="input-small form-control ishort" style="width:120px;" name="header<?php echo $i?>" value="<?php echo $header_text?>">
								</td>
							</tr>
						</table>
					</td>
					<?php
					if($j==5){
						$j = 0;
						echo "</tr><tr>";
					}
				}
				?>
			</tr>
		</table>
		<div style="display:none;">
		<?php
		echo HTMLHelper::_('select.genericlist',$optionArr1,'selected_field[]','multiple','value','text');
		?>
		</div>
		<select name="require_labels[]" id="require_labels" style="display:none;" multiple>
		<?php
		for($i=0;$i<count($requirelabelArr);$i++){
			if(in_array($requirefieldArr[$i],$fieldArr)){
				$selected = "selected";
			}else{
				$selected = "";
			}
			?>
			<option value="<?php echo $requirefieldArr[$i]?>" <?php echo $selected?>><?php echo $requirelabelArr[$i]?></option>
			<?php
		}
		?>
		</select>
		<script type="text/javascript">
		function loadRequireFields(){
			var require_labels = document.getElementById('require_labels');
			var required_div = document.getElementById('required_div');
			if(require_labels.options.length > 0){
				var rqs = "";
				for(i=0;i<require_labels.options.length;i++){
					if(require_labels.options[i].selected == false){
						rqs += require_labels.options[i].text + ", ";
					}
				}
				rqs = rqs.substring(0,rqs.length - 2);
				required_div.innerHTML = rqs;
			}
		}
		loadRequireFields();
		</script>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		</form>
		<?php
	}
}
?>