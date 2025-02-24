<?php
/*------------------------------------------------------------------------
# companies.php - Ossolution Property
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


class HTML_OspropertyCompanies{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function companies_list($option,$rows,$pageNav,$lists){
		global $mainframe,$_jversion;
		$db = Factory::getDBO();		
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_COMPANIES'),"user");
		ToolBarHelper::addNew('companies_add');
		if (count($rows)){
			ToolBarHelper::editList('companies_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'companies_remove');
			ToolBarHelper::publish('companies_publish');
			ToolBarHelper::unpublish('companies_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>

		<form method="POST" action="index.php?option=com_osproperty&task=companies_list" name="adminForm" id="adminForm">
		<div id="j-main-container jooma4">
			<div id="filter-bar" class="btn-toolbar js-stools">
				<div class="filter-search btn-group pull-left input-append">
						<?php
						echo $pageNav->getLimitBox();
						?>
                        <input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo Factory::getApplication()->input->getString('keyword','')?>" class="input-medium form-control" />
                        <button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
                            <i class="icon-search"></i>
                        </button>
                    </DIV>
				</div>
			</div>
			<?php
			if(count($rows) > 0) {
			?>
			<table class="adminlist table table-striped">
				<thead>
					<tr>
						<th width="2%">
					
						</th>
						<th width="3%"  style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="10%">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COMPANY_NAME'), 'company_name', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
						<th width="10%">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COMPANY_ADMIN'), 'company_name', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
						<th width="10%">
							<?php echo Text::_('OS_ADDRESS'); ?>
						</th>
						<th width="5%">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PHONE'), 'phone', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
						<th width="8%">
							<?php echo Text::_('OS_PHOTO'); ?>
						</th>
						<th width="5%" style="text-align:center;">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
						<?php
						if($configClass['auto_approval_company_register_request']==0){
						?>
						<th width="5%" style="text-align:center;">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_APPROVAL'), 'request_to_approval', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
						<?php
						}
						?>
						<th width="5%">
							<?php echo Text::_('OS_AGENTS')?>
						</th>
						<th width="3%" style="text-align:center;">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'id', @$lists['order_Dir'], @$lists['order'] ,'companies_list'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="13" style="text-align:center;">
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
					$link 		= Route::_( 'index.php?option=com_osproperty&task=companies_edit&cid[]='. $row->id );
					$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'companies_');
					
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
							<?php echo $pageNav->getRowOffset( $i ); ?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $checked; ?>
						</td>
						<td align="left">
							<a href="<?php echo $link; ?>">
								<?php echo $row->company_name; ?>
							</a>
							<BR />
							(Alias: <?php echo $row->company_alias;?>)
						</td>
						<td align="left">
							<?php
							$u = Factory::getUser($row->user_id);
							echo $u->name;
							?>
						</td>
						<td align="left">
							<?php echo OSPHelper::generateAddress($row); ?>
						</td>
						<td align="left">
							<?php echo $row->phone; ?>
						</td>
						<td align="center">
							<a target="_blank" href="<?php echo PATH_URL_PHOTO_COMPANY_FULL; ?><?php echo $row->photo?>">
								<img width="80" alt="" src="<?php echo PATH_URL_PHOTO_COMPANY_THUMB; ?><?php echo $row->photo?>">
							</a>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $published?>
						</td>
						<?php
						if($configClass['auto_approval_company_register_request'] == 0){
						?>
							<td align="center" style="text-align:center;">
								<?php
									if($row->request_to_approval == 1)
									{
										?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
										</svg>
										<?php
									}
									else
									{
										?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
										  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
										</svg>
										<?php
									}
								?>
							</td>
						<?php
						}
						?>
						<td align="center" style="text-align:center;">
							<?php
							$db->setQuery("Select count(id) from #__osrs_company_agents where company_id = '$row->id'");
							$nagents = $db->loadResult();
							echo intval($nagents);
							?>
						</td>
						<td align="center" style="text-align:center;">
							<?php
							echo $row->id;
							?>
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
		<input type="hidden" name="task" value="companies_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
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
		global $mainframe,$configClass,$_jversion,$languages,$bootstrapHelper;
		$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small'). ' smallSizeBox';
		$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini'). ' smallSizeBox';
		$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
		$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
		Factory::getApplication()->input->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_COMPANY').$title);
		ToolBarHelper::save('companies_save');
		ToolBarHelper::save2new('companies_new');
		ToolBarHelper::apply('companies_apply');
		ToolBarHelper::cancel('companies_cancel');
        $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		if(OSPHelper::isJoomla4())
		{
			OSPHelperJquery::colorbox('osmodal');
		}
		else
		{
			HTMLHelper::_('behavior.modal', 'osmodal');
		}
		?>
		<script type="text/javascript">
		/**
		 * move option this select box to that select box
		 * @param from
		 * @param to
		 * @param from_tmp
		 * @param to_tmp
		 * @return
		 */
		function moveOptions(from,to,from_tmp,to_tmp) {
		  // Move them over
		  for (var i=0; i<from.options.length; i++) {
			var o = from.options[i];
			if (o.selected) {
			  to.options[to.options.length] = new Option( o.text, o.value, false, false);
			  to_tmp.options[to_tmp.options.length] = new Option( o.text, o.value, false, false);
			}
		  }
		  // Delete them from original
		  for (var i=(from.options.length-1); i>=0; i--) {
			var o = from.options[i];
			if (o.selected) {
			  for (var j=(from_tmp.options.length-1); j>=0; j--) {
				 var o_tmp = from_tmp.options[j];
				 if (o.value == o_tmp.value){
					 from_tmp.options[j] = null;
				 }
			  }
			  from.options[i] = null;
			}
		  }
		  from.selectedIndex = -1;
		  to.selectedIndex = -1;
		  from_tmp.selectedIndex = -1;
		  to_tmp.selectedIndex = -1;
		}
		
		/**
		 * select all option in selec box
		 * @param element
		 * @return
		 */	
		function allSelected(element) {
		   for (var i=0; i<element.options.length; i++) {
				var o = element.options[i];
				o.selected = true;
			}
		 }
		 
		function change_country_agent(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
		}
		function change_state(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCity(state_id,city_id,'state_id',live_site);
		}
		function loadCity(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCity(state_id,city_id,'state',live_site);
		}
		</script>
		<?php
		if (version_compare(JVERSION, '3.5', 'ge')){
		?>
			<script src="<?php echo Uri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
		<?php } ?>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
		<table width="100%">
			<tr>
				<td width="62%" valign="top">
					<table  width="100%" class="admintable" >
                        <tr>
                            <td width="100%" id="joomlauser" colspan="2" style="padding:10px; border:1px solid #EEE; margin:10px;">
                                <strong>
                                    <?php echo Text::_('OS_JOOMLA_USER');?>
                                </strong>
                                <BR />
                                <input type="radio" name="existing_user" id="existing_user1" value="1" checked onclick="javascript:showDiv('existing_user_div','new_user_div');"/>&nbsp; <label for="existing_user1"><?php echo Text::_('OS_EXISTING_USER');?></label>
                                <BR />
                                <input type="radio" name="existing_user" id="existing_user0" value="0" onclick="javascript:showDiv('new_user_div','existing_user_div');"/>&nbsp; <label for="existing_user0"><?php echo Text::_('OS_NEW_JOOMLA_USER');?></label>
                                <BR />
                                <div id="existing_user_div" style="width: 100%;">
                                    <strong><?php echo Text::_('OS_SELECT_JOOMLA_USER')?></strong>
                                    <?php
                                    echo OspropertyAgent::getUserInput($row->user_id,$row->id);
                                    ?>
                                </div>
                                <div id="new_user_div" style="width: 100%;display:none;">
                                    <strong><?php echo Text::_('OS_CREATE_NEW_USER');?></strong>
                                    <table width="100%">
                                        <tr>
                                            <td class="key"><?php echo Text::_('OS_USERNAME'); ?></td>
                                            <td width="80%"><input type="text" name="username" id="username" class="input-medium form-control" value="" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="key"><?php echo Text::_('OS_PASSWORD'); ?></td>
                                            <td width="80%"><input type="password" name="password" id="password" class="input-medium form-control" value="" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">
                                <?php echo Text::_('OS_EMAIL'); ?>
                            </td>
                            <td>
                                <input type="text" name="email" id="email" size="20" value="<?php echo $row->email?>" class="input-medium form-control ilarge" />
                            </td>
                        </tr>
						<tr>
							<td class="key" width="20%">
								<?php echo Text::_('OS_COMPANY_NAME'); ?>
							</td>
							<td width="80%">
								<input type="text" name="company_name" id="company_name" size="40" value="<?php echo $row->company_name?>" class="input-large form-control ilarge" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_ALIAS'); ?>
							</td>
							<td>
								<input type="text" name="company_alias" id="company_alias" size="40" value="<?php echo $row->company_alias?>" class="input-large form-control ilarge" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_PUBLISHED')?>
							</td>
							<td>
								<?php
								echo $lists['published'];
								?>
							</td>
						</tr>
						<?php
						if(($configClass['auto_approval_company_register_request'] == 0) and ($row->request_to_approval == 1)){
							?>
							<tr>
								<td class="key">
									<?php echo Text::_('OS_APPROVAL')?>
								</td>
								<td>
									<?php
									echo $lists['approval'];
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</td>
				<td width="38%" valign="top">
					<table  width="100%" class="admintable" >
						<tr>
							<td class="key" width="30%">
								<?php echo Text::_('OS_ADDRESS')?>
							</td>
							<td width="70%">
								<input type="text" name="address" id="address" class="input-medium form-control ilarge" value="<?php echo $row->address?>" />
							</td>
						</tr>
						<?php
						if(HelperOspropertyCommon::checkCountry()){
						?>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_COUNTRY'); ?>
							</td>
							<td>
								<?php echo $lists['country'];?>
							</td>
						</tr>
						<?php
						}else{
							echo $lists['country'];
						}
						?>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_STATE'); ?>
							</td>
							<td id="country_state">
								<?php echo $lists['states']; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_CITY'); ?>
							</td>
							<td>
								<div id="city_div">
								<?php
								echo $lists['city'];
								?>
								</div>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_POSTCODE'); ?>
							</td>
							<td>
								<input type="text" name="postcode" id="postcode" size="10" value="<?php echo $row->postcode?>" class="input-small form-control imedium" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_PHONE'); ?>
							</td>
							<td>
								<input type="text" name="phone" id="phone" size="10" value="<?php echo $row->phone?>" class="input-small form-control imedium" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_FAX'); ?>
							</td>
							<td>
								<input type="text" name="fax" id="fax" size="10" value="<?php echo $row->fax?>" class="input-small form-control imedium" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_WEB'); ?>
							</td>
							<td>
								<input type="text" name="website" id="website" size="30" value="<?php echo $row->website?>" class="input-medium form-control imedium" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_PHOTO'); ?>
							</td>
							<td>
								<?php if($row->id && $row->photo){?>
									<a class="osmodal" href="<?php echo PATH_URL_PHOTO_COMPANY_FULL; ?><?php echo $row->photo?>">
										<img width="80" alt="" src="<?php echo PATH_URL_PHOTO_COMPANY_THUMB; ?><?php echo $row->photo?>" />
									</a>
									<div class="clearfix"></div>
									<input type="checkbox" name="remove_photo" value="1">&nbsp;<?php echo Text::_("OS_REMOVE_PHOTO")?>
									<br>
								<?php }?>
								<div class="clearfix"></div>
								<input type="file" name="file_photo" id="file_photo" size="40" onchange="javascript:checkUploadPhotoFiles('file_photo')" class="input-large form-control" /> 
								<div class="clearfix"></div>
								(<?php echo Text::_('OS_IMAGE_TYPES_SUPPORTED');?>)
								<input type="hidden" name="photo" id="photo" size="40" value="<?php echo $row->photo?>" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table class="admintable" width="100%">
				<tr>
					<td class="key" valign="top">
						<?php echo Text::_('OS_DESCRIPTION')?>
					</td>
					<td>
						<?php
						// parameters : areaname, content, width, height, cols, rows, show xtd buttons
						echo $editor->display( 'company_description',  htmlspecialchars($row->company_description, ENT_QUOTES), '550', '300', '60', '20', array() ) ;
						?>
					</td>
				</tr>
				<tr>
					<td class="key" valign="top">
						<?php echo Text::_('OS_MANAGE_AGENTS')?>
					</td>
					<td style="border:1px solid #CCC;padding:5px;">
						<table  width="100%">
							<tr>
								<td width="40%" valign="top" style="text-align:right;">
									<b><?php echo Text::_('OS_FREE_AGENT')?></b>
									<BR>
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['agentsnotinCompany'],'users_not_selected[]','class="input-large form-select" multiple style="height:180px;" onDblClick="moveOptions(document.adminForm.users_not_selected, document.adminForm[\'users_selected[]\'],document.adminForm.users_not_selected_tmp,document.adminForm.users_selected_tmp)"','value','text');
									
									echo HTMLHelper::_('select.genericlist',$lists['agentsnotinCompany'],'users_not_selected_tmp','style="display:none;"','value','text');
									?>
								</td>
								<td width="20%" valign="middle" style="text-align:center;">
									<input  type="button" name="Button" value="&gt;&gt;" onclick="moveOptions(document.adminForm.users_not_selected, document.adminForm['users_selected[]'],document.adminForm.users_not_selected_tmp,document.adminForm.users_selected_tmp)" />
									<br/> <br/>
								 	<input  type="button" name="Button" value="&lt;&lt;" onclick="moveOptions(document.adminForm.users_selected_tmp,document.adminForm.users_not_selected,document.adminForm['users_selected[]'],document.adminForm.users_not_selected_tmp)" />
								</td>
								<td width="40%" valign="top" style="text-align:left;">
									<b><?php echo Text::_('OS_AGENT_OF_THIS_COMPANY')?></b>
									<BR>
									<?php
									echo HTMLHelper::_('select.genericlist',$lists['agentinCompany'],'users_selected_tmp','class="input-large form-select" multiple style="height:180px;" onDblClick="moveOptions(document.adminForm.users_selected_tmp,document.adminForm.users_not_selected,document.adminForm[\'users_selected[]\'], document.adminForm.users_not_selected_tmp)"','value','text');
									echo HTMLHelper::_('select.genericlist',$lists['agentinCompany'],'users_selected[]','style="display:none;" multiple','value','text');
									?>
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
										<td class="key" valign="top">
											<?php echo Text::_('OS_DESCRIPTION')?>
										</td>
										<td>
											<?php echo $editor->display( 'company_description_'.$sef,  stripslashes($row->{'company_description_'.$sef}) , '80%', '250', '75', '20' ) ; ?>
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
		<input type="hidden" name="id" value="<?php echo intval($row->id);?>" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
            function showDiv(item1, item2)
            {
                jQuery("#" + item1).show('slow');
                jQuery("#" + item2).hide('slow');
            }
			var live_site = '<?php echo Uri::root()?>';
			function change_country_company(country_id,state_id,city_id){
				var live_site = '<?php echo Uri::root()?>';
				loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
			}
			
			function loadCity(state_id,city_id){
				var live_site = '<?php echo Uri::root()?>';
				loadLocationInfoCity(state_id,city_id,'state',live_site);
			}

            function loadStateBackend(country_id,state_id,city_id){
                var live_site = '<?php echo Uri::root()?>';
                loadLocationInfoStateCityBackend(country_id,state_id,city_id,'country','state',live_site);
            }
            function loadCityBackend(state_id,city_id){
                var live_site = '<?php echo Uri::root()?>';
                loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
            }
			
			
			Joomla.submitbutton = function(pressbutton)
			{
				var form = document.adminForm;
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				var user_id = document.getElementById('user_id_id');
                if(user_id != null) {
                    user_id = user_id.value;
                }else{
                    user_id = document.adminForm.user_id.value;
                }
				user_id = parseInt(user_id);
				var username = document.getElementById('username');
				var password = document.getElementById('password');

				if (pressbutton == 'companies_cancel'){
                    Joomla.submitform( pressbutton );
					return;
				}else if ((user_id == 0) && (username.value == "") && (password.value == "")){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_OR_CREATE_NEW_JOOMLA_USER_FOR_THIS_AGENT'); ?>');
					form.username.focus();
					return;
				}else if (form.company_name.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_COMPANY_NAME'); ?>');
					form.company_name.focus();
					return;
				}else if (form.email.value != '' && !filter.test(form.email.value)){
					alert('<?php echo Text::_('OS_EMAIL_INVALID'); ?>');
					form.email.value = '';
					form.email.focus();
					return;
				}else if (form.country.value == '0'){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_COUNTRY'); ?>');
					form.country.focus();
					return;
				}else if ((form.state.value == '0') && (form.nstate.value == "")){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_STATE'); ?>');
					form.state.focus();
					return;	
				}else{
					if((pressbutton == "companies_apply") || (pressbutton == "companies_save")){
						allSelected(document.adminForm['users_selected[]']);
					}
                    Joomla.submitform( pressbutton );
					return;
				}
			}
			jQuery(document).ready(function () {
				populateAgentData = (function(){
					var id = jQuery('#user_id_id').val();
					jQuery.ajax({
						type : 'POST',
						url : live_site + 'index.php?option=com_osproperty&task=ajax_userdata&tmpl=component&user_id=' + id,
						dataType: 'json',
						success : function(json){
							var selecteds = [];
							for (var field in json)
							{
								value = json[field];
								if (jQuery("input[name='" + field + "[]']").length)
								{
									//This is a checkbox or multiple select
									if (jQuery.isArray(value))
									{
										selecteds = value;
									}
									else
									{
										selecteds.push(value);
									}
									jQuery("input[name='" + field + "[]']").val(selecteds);
								}
								else if (jQuery("input[type='radio'][name='" + field + "']").length)
								{
									jQuery("input[name="+field+"][value='" + value + "']").attr('checked', 'checked');
								}
								else
								{
									jQuery('#' + field).val(value);
								}
							}
						}
					})
				});
			});
		</script>
		<?php
	}
}
?>
