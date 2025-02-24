<?php
/*------------------------------------------------------------------------
# company.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Editor\Editor;


class HTML_OspropertyCompany{
	/**
	 * Edit company
	 *
	 * @param unknown_type $option
	 */
	static function editCompany($option,$row,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$_jversion;
		$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		//HTMLHelper::_('behavior.modal','a.osmodal');
		OSPHelper::loadTooltip();
		OSPHelperJquery::colorbox('osmodal');
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="editCompany">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
				<?php 
				OSPHelper::generateHeading(2,Text::_('OS_COMPANY_DETAILS'));
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
				<?php
				self::generateNav("company_edit");
				?>
				<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm" enctype="multipart/form-data" class="form-horizontal">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COMPANY_NAME')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="company_name" id="company_name" size="30" value="<?php echo htmlspecialchars($row->company_name) ;?>" class="input-large" placeholder="<?php echo Text::_('OS_COMPANY_NAME')?>"/>
								</div>
							</div>
							<?php
							if(HelperOspropertyCommon::checkCountry()){
							?>
								<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
									<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COUNTRY')?></label>
									<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
										<?php echo $lists['country'];?>
									</div>
								</div>
							<?php
							}else{
								echo $lists['country'];
							}
							if(OSPHelper::userOneState()){
								echo $lists['state'];
							}else{
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_STATE')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="country_state">
									<?php echo $lists['state']; ?>
								</div>
							</div>
							<?php } ?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_CITY')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="city_div">
									<?php echo $lists['city']; ?>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_ADDRESS')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="address" id="address" size="30" value="<?php echo htmlspecialchars($row->address);?>" class="input-large" placeholder="<?php echo Text::_('OS_ADDRESS')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_POSTCODE')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="postcode" id="postcode" size="30" value="<?php echo htmlspecialchars($row->postcode);?>" class="input-large" placeholder="<?php echo Text::_('OS_POSTCODE')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PHONE')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="phone" id="phone" size="30" value="<?php echo htmlspecialchars($row->phone);?>" class="input-large" placeholder="<?php echo Text::_('OS_PHONE')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_FAX')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="fax" id="fax" size="30" value="<?php echo htmlspecialchars($row->fax);?>" class="input-large" placeholder="<?php echo Text::_('OS_FAX')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_EMAIL')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="email" id="email" size="30" value="<?php echo htmlspecialchars($row->email);?>" class="input-large" placeholder="<?php echo Text::_('OS_EMAIL')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_WEB')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="website" id="website" size="30" value="<?php echo htmlspecialchars($row->website);?>" class="input-large" placeholder="<?php echo Text::_('OS_WEB')?>"/>
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PHOTO')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<?php if($row->id && $row->photo){?>
									<a class="osmodal" href="<?php echo Uri::root()?>images/osproperty/company/<?php echo $row->photo?>">
										<img width="80" alt="" src="<?php echo Uri::root()?>images/osproperty/company/thumbnail/<?php echo $row->photo?>" />
									</a>
									<div class="clearfix"></div>
									<input type="checkbox" class="inputbox" name="remove_photo" value="0" />&nbsp;<?php echo Text::_("OS_REMOVE_PHOTO")?>
									<div class="clearfix"></div>
								<?php }?>
								<span id="file_photodiv">
								<input type="file" name="file_photo" id="file_photo" onchange="javascript:checkUploadPhotoFiles('file_photo')" class="input-medium form-control" /> 
								<div class="clearfix"></div>
								<?php echo Text::_('OS_ONLY_SUPPORT_JPG_IMAGES');?>
								</span>
								<input type="hidden" name="photo" id="photo" size="40" value="<?php echo $row->photo?>" />
								</div>
							</div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_DESCRIPTION')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<?php
									// parameters : areaname, content, width, height, cols, rows, show xtd buttons
									echo $editor->display( 'company_description',  htmlspecialchars($row->company_description, ENT_QUOTES), '250', '200', '60', '20', false) ;
									?>
								</div>
							</div>
                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PUBLIC_MY_PROFILE')?></label>
                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                    <?php echo $lists['optin']; ?>
                                </div>
                            </div>
							<div class="clearfix"></div>
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
								<input type="button" class="btn  btn-info" value="<?php echo Text::_('OS_SAVE')?>" onclick="javascript:submitbutton();" />
								<input type="reset" class="btn  btn-warning" value="<?php echo Text::_('OS_RESET')?>" />
							</div>
						</div>
					</div>
					<script type="text/javascript">
					var live_site = '<?php echo Uri::root()?>';
					function change_country_company(country_id,state_id,city_id){
						var live_site = '<?php echo Uri::root()?>';
                        loadLocationInfoStateCityBackend(country_id,state_id,city_id,'country','state',live_site);
					}
					
					function change_state_company(state_id,city_id){
						var live_site = '<?php echo Uri::root()?>';
                        loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
					}
					
					function loadCity(state_id,city_id){
						var live_site = '<?php echo Uri::root()?>';
						loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
					}

					function loadCityBackend(state_id,city_id){
						var live_site = '<?php echo Uri::root()?>';
						loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
					}
					
					function submitbutton(){
						var form = document.ftForm;
						var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
						if (form.company_name.value == ''){
							//alert("<?php echo Text::_('OS_PLEASE_ENTER_COMPANY_NAME'); ?>");
							alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_ENTER_COMPANY_NAME", 1, array("script"=>true));?>'));
							form.company_name.focus();
							return;
						}else if (form.country.value == '0'){
							//alert("<?php echo Text::_('OS_PLEASE_SELECT_COUNTRY'); ?>");
							alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_SELECT_COUNTRY", 1, array("script"=>true));?>'));
							form.country.focus();
							return;
						}else if (form.state.value == '0'){
							///alert("<?php echo Text::_('OS_PLEASE_SELECT_STATE'); ?>");
							alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_SELECT_STATE", 1, array("script"=>true));?>'));
							form.state.focus();
							return;
						}else if (form.city.value == ''){
							//alert("<?php echo Text::_('OS_PLEASE_SELECT_CITY'); ?>");
							alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_SELECT_CITY", 1, array("script"=>true));?>'));
							form.city.focus();
							return;
						}else if (form.email.value == ''){
							//alert("<?php echo Text::_('OS_PLEASE_SELECT_EMAIL'); ?>");
							alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_SELECT_EMAIL", 1, array("script"=>true));?>'));
							form.email.focus();
							return;	
						}else{
							form.submit();
							return;
						}
					}
					</script>
					
					<input type="hidden" name="option" value="com_osproperty" />
					<input type="hidden" name="task" value="company_save_info" />
					<input type="hidden" name="id" value="<?php echo intval($row->id);?>" />
					<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
					<input type="hidden" name="MAX_FILE_SIZE" value="900000000" />
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Manage agents of company
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 * @param unknown_type $agents
	 */
	static function editAgent($option,$row,$lists,$agents){
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$_jversion;
		$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		//HTMLHelper::_('behavior.modal');
		OSPHelper::loadTooltip();
		?>
		<script type="text/javascript">
			var live_site = '<?php echo Uri::root()?>';
			function removeAgent(agent_id){
				var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_ITEMS')?>");
				if(answer == 1){
					removeAgentAjax(agent_id,live_site);
				}
			}
			function searchAgent(){
				var keyword = document.getElementById('agkeyword');
				if(keyword.value != ""){
					if(keyword.value.length >= 2){
						searchAgentajax(keyword.value,live_site)
					}
				}
			}
			function addAgent(agent_id){
				var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_ADD_THIS_AGENT_TO_YOUR_COMPANY')?>");
				if(answer == 1){
					var keyword = document.getElementById('agkeyword');
					if(keyword.value != ""){
						addAgentAjax(keyword.value,agent_id,live_site);
					}
				}
			}
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="manageAgents">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <?php
                OSPHelper::generateHeading(2,Text::_('OS_MANAGE_AGENTS'));

				self::generateNav('company_agent');
				?>
				<div class="clearfix"></div>
				<div class="block_caption">
					<strong><?php echo Text::_('OS_MANAGE_AGENTS')?></strong>
					<span class="bock_caption_explain">
						<?php echo Text::_('OS_MANAGE_AGENT_EXPLAIN')?>
					</span>
				</div>
				<div class="clearfix"></div>
				<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty')?>" name="manageagent" id="manageagent">
				 <div id="filter-bar" class="btn-toolbar">
		            <div class="filter-search btn-group pull-left">
		                <input type="text" name="filter_search" class="inputbox input-medium" id="filter_search" value="<?php echo OSPHelper::getStringRequest('filter_search');?>" title="<?php echo Text::_('OS_SEARCH');?>" />
		            </div>
		            <div class="btn-group pull-left <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
		                <button class="btn hasTooltip" type="submit" title="<?php echo Text::_('OS_SEARCH');?>" >
		                	<i class="osicon-search"></i>
		                </button>
		                <button class="btn hasTooltip" type="button" onclick="javascript:document.getElementById('filter_search').value='';document.getElementById('ftForm').submit();" rel="tooltip" title="<?php echo Text::_('OS_CANCEL');?>">
		                	<i class="osicon-remove"></i>
		                </button>
		            </div>
		            <div class="btn-group pull-right <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
		               <?php echo $lists['orderby'];?>
		            </div>
		            <div class="btn-group pull-right">
		               <?php echo $lists['sortby'];?>
		            </div>
		        </div>
		        <div class="clearfix"></div>
		        <div class="btn-toolbar <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
		            <div class="btn-group">
		            	<?php
		            	if($configClass['company_admin_add_agent'] == 1){
		            	?>
		                <button type="button" class="btn btn-primary" onclick="javascript:submitForm('company_addagents');">
		                    <i class="osicon-plus"></i><?php echo Text::_('OS_NEW');?>
		                </button>
		                <?php
		            	}
		            	if($configClass['allow_company_assign_agent'] == 1){
		                ?>
		                <button type="button" class="btn btn-warning" onclick="javascript:submitForm('company_addnew');">
		                    <i class="osicon-plus"></i><?php echo Text::_('OS_ASSIGN_NEW_AGENT');?>
		                </button>
		                <?php } ?>
		                <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_PUBLISHED');?>" onclick="javascript:submitForm('company_publishagents');">
		                    <i class="osicon-ok"></i>
		                </button>
		                <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_UNPUBLISHED');?>" onclick="javascript:submitForm('company_unpublishagents');">
		                    <i class="osicon-unpublish"></i>
		                </button>
						<?php
						if($configClass['company_changefeaturedstatus'] == 1){
						?>
		                <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_FEATURED');?>" onclick="javascript:submitForm('company_featureagents');">
		                    <i class="osicon-star"></i> 
	                    </button>
	                    <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_UNFEATURED');?>" onclick="javascript:submitForm('company_unfeatureagents');">
		                    <i class="osicon-star-empty"></i> 
		                </button>
						<?php } ?>
		                <button type="button" class="btn hasTooltip" title="Delete" onclick="if(confirm('<?php echo Text::_('OS_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM')?> ?')){submitForm('company_removeagents');}else{return false;}">
		                    <i class="osicon-trash"></i> 
		                </button>
		            </div>
		        </div>
		        
		        <div class="clearfix"> </div>
				<div id="agent_div">
					<table width="100%" class="ptable table table-striped tablelistproperties" id="agentlist">
						<thead>
							<tr>
								<th width="1%" class="center">
			                        <input type="checkbox" name="checkall-toggle" value="" title="Check All" onclick="Joomla.checkAll(this)" />
			                    </th>
			                    <th width="10%" class="nowrap">
									<?php
									echo Text::_('OS_PHOTO');
									?>
								</th> 
								<th width="15%" class="nowrap">
									<?php
									echo Text::_('OS_AGENT');
									?>
								</th>
								<th width="25%" class="nowrap">
									<?php
									echo Text::_('OS_ADDRESS');
									?>
								</th>
								<?php if($configClass['show_agent_email'] == 1){ ?>
								<th width="15%" class="nowrap">
									<?php
									echo Text::_('OS_EMAIL');
									?>
								</th>
								<?php } ?>
								<th width="10%" class="nowrap">
									<?php
									echo Text::_('OS_PROPERTIES');
									?>
								</th>
								<th width="10%" class="nowrap center">
									<?php
									echo Text::_('OS_STATUS');
									?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$k = 0;
							for($i=0;$i<count($agents);$i++){
								$agent = $agents[$i];
								$link = Route::_('index.php?option=com_osproperty&task=company_editagent&id='.$agent->id);
								$publish_url = Uri::root()."index.php?option=com_osproperty&task=company_publishagents&cid[]=$agent->id";
								$unpublish_url = Uri::root()."index.php?option=com_osproperty&task=company_unpublishagents&cid[]=$agent->id";
								$feature_url = Uri::root()."index.php?option=com_osproperty&task=company_featureagents&cid[]=$agent->id";
								$unfeature_url = Uri::root()."index.php?option=com_osproperty&task=company_unfeatureagents&cid[]=$agent->id";
								?>
								<tr class="row<?php echo $k;?>">
									<td class="center" data-label="">
		                                <input type="checkbox" id="cb0" name="cid[]" value="<?php echo $agent->id;?>" onclick="Joomla.isChecked(this.checked);" title="" />                            
	                                </td>
									<td class="data_td" data-label="<?php echo Text::_('OS_PHOTO');?>">
										<?php
										if(($agent->photo != "") and (file_exists(JPATH_ROOT."/images/osproperty/agent/thumbnail/".$agent->photo))){
										?>
											<img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $agent->photo?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> width60" />
										<?php
										}else{
										?>
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage64.png" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> width60" />
										<?php
										}
										?>
									</td>
									<td class="data_td" data-label="<?php echo Text::_('OS_AGENT');?>">
										<a href="<?php echo $link?>" title="<?php echo Text::_('OS_AGENT_DETAILS')?>">
											<?php echo $agent->name?>
										</a>
									</td>
									<td class="data_td fontsmall" data-label="<?php echo Text::_('OS_ADDRESS');?>">
										<?php
										echo OSPHelper::generateAddress($agent);
										?>
									</td>
									<?php if($configClass['show_agent_email'] == 1){ ?>
									<td class="data_td fontsmall" data-label="<?php echo Text::_('OS_EMAIL');?>">
										<?php 
											echo $agent->email;
										?>
									</td>
									<?php } ?>
									<td class="data_td fontsmall" data-label="<?php echo Text::_('OS_PROPERTIES');?>">
										<?php echo $agent->nproperties?>
									</td>
									<td class="data_td center" data-label="<?php echo Text::_('OS_STATUS');?>">
										<div class="btn-group">
											<?php
											if($agent->published == 1){
											?>
                                    		<a class="btn btn-micro jgrid"  title="<?php echo Text::_('OS_PUBLISHED');?>" href="<?php echo $unpublish_url;?>">
                                    			<i class="osicon-publish"></i>
                                    		</a>                       
                                    		<?php
											}else{
											?>
											<a class="btn btn-micro jgrid"  title="<?php echo Text::_('OS_UNPUBLISHED');?>" href="<?php echo $publish_url;?>">
                                    			<i class="osicon-unpublish"></i>
                                    		</a> 
											<?php
											}
                                    		?>
                                    		<?php
											if($configClass['company_changefeaturedstatus'] == 1){
												if($agent->featured == 1){
												?>
													<a href="<?php echo $unfeature_url;?>" class="btn btn-micro hasTooltip active" rel="tooltip" title="<?php echo Text::_('OS_UNFEATURED');?>">
														<i class="osicon-star"></i>
													</a> 
												<?php
												}else{
												?>
													<a href="<?php echo $feature_url;?>" class="btn btn-micro hasTooltip active" rel="tooltip" title="<?php echo Text::_('OS_FEATURED');?>">
														<i class="osicon-star-empty"></i>
													</a> 
												<?php } ?>
											<?php
											}
											?>
										</div>
									</td>
								</tr>
								<?php
								$k = 1 - $k;
							}
							?>
						</tbody>
					</table>
				</div>
				<input type="hidden" name="option" value="com_osproperty" />
				<input type="hidden" name="task" value="company_agent" />
				<input type="hidden" name="id" value="<?php echo $row->id?>" />
				<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
				<input type="hidden" name="MAX_FILE_SIZE" value="900000000" />
				</FORM>
			</div>
		</div>
		<script type="text/javascript">
		function listItemTask( id, task )
		{
		    var form = document.manageagent;
		    form.task.value 	 = task;
		    form.submit( task );
		}
		
		function submitForm(task){
			var form = document.manageagent;
		    form.task.value 	 = task;
		    form.submit( task );
		}
		</script>
		<?php
	}
	
	/**
	 * Show company agent
	 *
	 * @param unknown_type $option
	 * @param unknown_type $agents
	 */
	static function showCompanyAgent($option,$agents){
		global $bootstrapHelper, $configClass,$jinput;
		?>
		<table width="100%">
			<tr>
				<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					
				</td>
				<td class="header_td">
					<?php
					echo Text::_('OS_AGENT')
					?>
				</td>
				<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					<?php
					echo Text::_('OS_ADDRESS')
					?>
				</td>
				<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					<?php
					echo Text::_('OS_EMAIL')
					?>
				</td>
				<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					<?php
					echo Text::_('OS_NPROPERTIES')
					?>
				</td>
				<td class="header_td">
					<?php
					echo Text::_('OS_DELETE')
					?>
				</td>
			</tr>
			<?php
			for($i=0;$i<count($agents);$i++){
				$agent = $agents[$i];
				$link = Route::_('index.php?option=com_osproperty&task=agent_info&id='.$agent->agentid.'&Itemid='.$jinput->getInt('Itemid',0));
				?>
				<tr>
					<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
						<?php
						if(($agent->photo != "") and (file_exists(JPATH_ROOT."/images/osproperty/agent/thumbnail/".$agent->photo))){
						?>
						<img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $agent->photo?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> width60" />
						<?php
						}else{
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage64.png" class="img-polaroid width60" />
						<?php
						}
						?>
					</td>
					<td class="data_td">
						<a href="<?php echo $link?>" title="<?php echo Text::_('OS_AGENT_DETAILS')?>">
							<?php echo $agent->name?>
						</a>
					</td>
					<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> fontsmall">
						<?php
						echo OSPHelper::generateAddress($agent);
						?>
					</td>
					<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> fontsmall">
						<?php 
						if($configClass['show_agent_email'] == 1){
							echo $agent->email;
						}
						?>
					</td>
					<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> center">
						<?php echo $agent->nproperties?>
					</td>
					<td class="data_td center">
						<a href="javascript:removeAgent(<?php echo $agent->id?>);" title="<?php echo Text::_('OS_REMOVE_AGENT')?>">
						<img src ="<?php echo Uri::root()?>media/com_osproperty/assets/images/delete.png" border="0" />
						</a>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
	
	/**
	 * Show search agent results
	 *
	 * @param unknown_type $option
	 * @param unknown_type $agents
	 */
	static function showSearchAgentResults($option,$agents){
		global $bootstrapHelper, $configClass,$jinput;
		?>
		<table  width="100%" class="tableshowSearchAgentResults">
		<tr>
			<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
				
			</td>
			<td class="header_td">
				<?php
				echo Text::_('OS_AGENT')
				?>
			</td>
			<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
				<?php
				echo Text::_('OS_ADDRESS')
				?>
			</td>
			<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
				<?php
				echo Text::_('OS_EMAIL')
				?>
			</td>
			<td class="header_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
				<?php
				echo Text::_('OS_PHONE')
				?>
			</td>
			<td class="header_td">
				<?php
				echo Text::_('OS_ADD_AGENT')
				?>
			</td>
		</tr>
		<?php
		for($i=0;$i<count($agents);$i++){
			$agent = $agents[$i];
			if($i % 2 == 0){
				$bgcolor = "#F6F9D0";
			}else{
				$bgcolor = "#FBECD5";
			}
			$link = Route::_('index.php?option=com_osproperty&task=agent_info&id='.$agent->id.'&Itemid='.$jinput->getInt('Itemid',0));
			?>
			<tr>
				<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" style="background-color:<?php echo $bgcolor?>;">
					<img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $agent->photo?>" height="60" />
				</td>
				<td class="data_td" style="background-color:<?php echo $bgcolor?>;">
					<a href="<?php echo $link?>" title="<?php echo Text::_('OS_AGENT_DETAILS')?>">
						<?php echo $agent->name?>
					</a>
				</td>
				<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" style="background-color:<?php echo $bgcolor?>;">
					<?php echo OSPHelper::generateAddress($agent);?>
				</td>
				<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" style="background-color:<?php echo $bgcolor?>;">
					<?php 
					if($configClass['show_agent_email'] == 1){
						echo $agent->email;
					}?>
				</td>
				<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" style="background-color:<?php echo $bgcolor?>;">
					<?php echo $agent->phone?>
				</td>
				<td class="data_td center" style="background-color:<?php echo $bgcolor?>;">
					<a href="javascript:addAgent(<?php echo $agent->id?>);" title="<?php echo Text::_('OS_REMOVE_AGENT')?>">
					<img src ="<?php echo Uri::root()?>media/com_osproperty/assets/images/tick.png" border="0" />
					</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	/**
	 * List companies
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function listCompanies($option,$rows,$pageNav,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$ismobile,$configClass,$lang_suffix;
		$itemid = $jinput->getInt('Itemid',0);
		$ordertype = OSPHelper::getStringRequest('ordertype','asc');
		?>
		<script type="text/javascript">
		function updateOrderType(ordertype_value){
			var ordertype = document.getElementById('ordertype');
			if(ordertype.value != ordertype_value){
				ordertype.value = ordertype_value;
				document.ftForm.submit();
			}
		}
		</script>
		<?php 
		OSPHelper::generateHeading(2,Text::_('OS_LIST_COMPANIES'));
		?>
		<div class="clearfix"></div>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&view=lcompanies&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
		<?php 
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/companieslist.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('ordertype',$ordertype);
		$tpl->set('itemid',$itemid);
		$tpl->set('mainframe',$mainframe);
		$tpl->set('lists',$lists);
		$tpl->set('option',$option);
		$tpl->set('configClass',$configClass);
		$tpl->set('rows',$rows);
		$tpl->set('pageNav',$pageNav);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("companieslist.php");
		echo $body;
		?>	
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="company_listing" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		</form>
		<?php
	}
	
	/**
	 * Company details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $company
	 * @param unknown_type $agents
	 */
	static function companyDetailsForm($option,$company,$agents)
    {
		global $bootstrapHelper, $mainframe,$jinput,$languages,$configClass,$lang_suffix;
		$document = Factory::getDocument();
        $document->addStyleSheet(Uri::root()."components/com_osproperty/templates/theme2/style/font.css");
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="companydetails">		
			<?php 
			jimport('joomla.filesystem.file');
			if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/companydetails.php')){
				$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
			}else{
				$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
			}
			$tpl->set('mainframe',$mainframe);
			$tpl->set('option',$option);
			$tpl->set('configClass',$configClass);
			$tpl->set('company',$company);
			$tpl->set('agents',$agents);
			$tpl->set('jinput', $jinput);
			$tpl->set('lang_suffix', $lang_suffix);
			$tpl->set('bootstrapHelper',$bootstrapHelper);
			$body = $tpl->fetch("companydetails.php");
			echo $body;
			?>	
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<?php
					echo HTMLHelper::_('bootstrap.startTabSet', 'companyinfo', array('active' => 'panel1'));
					?>
					<?php
					echo HTMLHelper::_('bootstrap.addTab', 'companyinfo', 'panel1', Text::_('OS_PROPERTIES', true));
					?>
					<div class="tab-pane" id="panel1">
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
								<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
								<?php
								$filterParams = array();
								//show cat
								$filterParams[0] = 1;
								//agent
								$filterParams[1] = 1;
								//keyword
								$filterParams[2] = 1;
								//bed
								$filterParams[3] = 1;
								//bath
								$filterParams[4] = 1;
								//rooms
								$filterParams[5] = 1;
								//price
								$filterParams[6] = 1;
								$category_id 	= $jinput->getInt('category_id',0);
								$property_type	= $jinput->getInt('property_type',0);
								$keyword		= OSPHelper::getStringRequest('keyword','','');
								$nbed			= $jinput->getInt('nbed',0);
								$nbath			= $jinput->getInt('nbath',0);
								$isfeatured		= $jinput->getInt('isfeatured',0);
								$nrooms			= $jinput->getInt('nrooms',0);
								$orderby		= $jinput->getString('orderby','a.id');
								$ordertype		= $jinput->getString('ordertype','desc');
								$limitstart		= $jinput->getInt('limitstart',0);
								$limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
								$favorites		= $jinput->getInt('favorites',0);
								$price			= $jinput->getInt('price',0);
								OspropertyListing::listProperties($option,$company->id,null,'',$property_type,$keyword,$nbed,$nbath,0,0,$nrooms,$orderby,$ordertype,$limitstart,$limit,'',$price,$filterParams,0,0,0,0,0);
								?>
								<input type="hidden" name="option" value="com_osproperty" />
								<input type="hidden" name="task" value="company_info" />
								<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
								<input type="hidden" name="id" id="id" value="<?php echo $company->id?>" />
								</form>
							</div>
						</div>
					</div>
					<?php
					echo HTMLHelper::_('bootstrap.endTab');
					?>
					<?php
					echo HTMLHelper::_('bootstrap.addTab', 'companyinfo', 'panel2', Text::_('OS_AGENT', true));
					?>
					<div class="tab-pane <?php echo $bootstrapHelper->getClassMapping('span12'); ?>" id="panel2">
                        <?php
                        if(count((array)$agents) > 0)
                        {
                            jimport('joomla.filesystem.file');
                            if (File::exists(JPATH_ROOT . '/templates/' . $mainframe->getTemplate() . '/html/com_osproperty/layouts/agentslist.php')) {
                                $tpl = new OspropertyTemplate(JPATH_ROOT . '/templates/' . $mainframe->getTemplate() . '/html/com_osproperty/layouts/');
                            } else {
                                $tpl = new OspropertyTemplate(JPATH_COMPONENT . '/helpers/layouts/');
                            }
                            $tpl->set('mainframe', $mainframe);
                            $tpl->set('option', $option);
                            $tpl->set('configClass', $configClass);
                            $tpl->set('rows', $agents);
                            $tpl->set('bootstrapHelper', $bootstrapHelper);
                            $body = $tpl->fetch("agentslist.php");
                            echo $body;
                        }
                        else
                        {
                            echo Text::_('OS_NO_AGENTS_FOUND');
                        }
                        ?>
					</div>
					<?php
					echo HTMLHelper::_('bootstrap.endTab');
					echo HTMLHelper::_('bootstrap.endTabSet');
					?>
				</div>
			</div>
		</div>
		
		<?php
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function companyProperties($option,$lists,$company){
		global $bootstrapHelper, $mainframe,$jinput,$symbol,$configClass;
		$filterParams = array();
		//show cat
		$filterParams[0] = 0;
		//agent
		$filterParams[1] = 0;
		//keyword
		$filterParams[2] = 0;
		//bed
		$filterParams[3] = 0;
		//bath
		$filterParams[4] = 0;
		//rooms
		$filterParams[5] = 0;
		//price
		$filterParams[6] = 0;
		?>
		<h1 class="componentheading">
			<?php echo Text::_('OS_LIST_PROPERTIES')." [$company->company_name]";?>
		</h1>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=company_listproperties&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm">
		<?php 
		$price			= $jinput->getInt('price',0);
		$city_id		= $jinput->getInt('city',0);
		$state_id		= $jinput->getInt('state_id',0);
		$country_id		= $jinput->getInt('country_id',HelperOspropertyCommon::getDefaultCountry());
		OspropertyListing::listProperties($option,$company->id,null,0,0,'',0,0,0,0,0,$lists['orderby'],$lists['ordertype'],$lists['limitstart'],$lists['limit'],'',$price,$filterParams,$city_id,$state_id,$country_id,0,0);
		?>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="company_listproperties" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		<input type="hidden" name="id" value="<?php echo $company->id?>" />
		</form>
		<?php
	}
	
	
	
	/**
	 * Show Search form to add agents
	 *
	 * @param unknown_type $option
	 */
	static function showSearchAgentForm($option,$agents){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		OSPHelper::generateHeading(2,Text::_('OS_FIND_AGENTS_TO_ADD_TO_YOUR_COMPANY'));
		?>
		<div class="clearfix"></div>
		<form class="form-inline" action="<?php echo Route::_("index.php?option=com_osproperty&task=company_addnew");?>" method="POST">
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="searchAgent">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php
				self::generateNav('company_addnew');
				?>
				<div class="clearfix"></div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> center">
			    	<span><strong><?php echo Text::_('OS_FILTER');?>:</strong></span>&nbsp;&nbsp;<input type="text" class="input-medium search-query" placeholder="<?php echo Text::_('OS_KEYWORD');?>" name="keyword" value="<?php echo OSPHelper::getStringRequest('keyword','','');?>">
			    	<button type="submit" class="btn btn-warning"><i class="osicon-search"></i><?php echo Text::_('OS_SEARCH');?></button>
				</div>
				<div class="clearfix"></div>
				<?php
				if(count($agents) > 0){
				?>
				<BR />
				<table width="100%" class="class="ptable table table-striped tablelistproperties">
					<thead>
						<tr>
							<th class="nowrap <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> borderbottom1" width="15%">
								
							</th>
							<th class="nowrap borderbottom1" width="15%">
								<?php
								echo Text::_('OS_AGENT')
								?>
							</th>
							<th class="nowrap borderbottom1 <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" width="25%">
								<?php
								echo Text::_('OS_ADDRESS')
								?>
							</th>
							<th class="nowrap borderbottom1 <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>" width="15%">
								<?php
								echo Text::_('OS_EMAIL')
								?>
							</th>
							<th class="nowrap borderbottom1 center" width="15%">
								<?php
								echo Text::_('OS_ADD_AGENT')
								?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;
					for($i=0;$i<count($agents);$i++){
						$agent = $agents[$i];
						if($i % 2 == 0){
							$bgcolor = "#F6F9D0";
						}else{
							$bgcolor = "#FBECD5";
						}
						$link = Route::_('index.php?option=com_osproperty&task=agent_info&id='.$agent->id.'&Itemid='.$jinput->getInt('Itemid',0));
						?>
						<tr class="row<?php echo $k;?>">
							<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
								<?php
								if($agent->photo != ""){
								?>
								<img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $agent->photo?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> height60" />
								<?php
								}else{
								?>
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" class="img-polaroid height60" />
								<?php
								}
								?>
							</td>
							<td class="data_td">
								<a href="<?php echo $link?>" target="_blank" title="<?php echo Text::_('OS_AGENT_DETAILS')?>">
									<?php
										echo $agent->name;
									?>	
								</a>
							</td>
							<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
								<?php echo OSPHelper::generateAddress($agent);?>
							</td>
							<td class="data_td <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
								<?php 
								if($configClass['show_agent_email'] == 1){
									echo $agent->email;
								}
								?>
							</td>
							<td class="data_td center">
								<a href="javascript:addAgent('<?php echo $agent->id?>')">
									<i class="osicon-plus"></i>
								</a>
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
					<BR />
					<div class="clearfix"></div>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> center">
							<strong><?php echo Text::_('OS_NO_AGENTS_FOUND');?></strong>	
						</div>
					</div>
					
					<?php
				}
				?>
			</div>
		</div>
		<input type="hidden" name="task" value="company_addnew" />
    	<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
    	<input type="hidden" name="option" value="com_osproperty" />
	    </form>
	    <script type="text/javascript">
	    function addAgent(agent_id){
	    	var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_ADD_THIS_AGENT_TO_YOUR_COMPANY')?>");
	    	if(answer == 1){
	    		location.href = "<?php echo Uri::root()?>index.php?option=com_osproperty&task=company_addnew1&agent_id=" + agent_id;
	    	}
	    }
	    </script>
		<?php
	}
	
	
	/**
	 * Show Agent form
	 *
	 * @param unknown_type $option
	 */
	static function showAddAgentForm($option,$lists,$company_id,$agent){
		global $bootstrapHelper, $mainframe,$jinput;
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> osp-container" id="agentForm">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				
					<?php 
					if($agent->id > 0){
						OSPHelper::generateHeading(2,Text::_('OS_MODIFY_AGENT'));
					}else{
						OSPHelper::generateHeading(2,Text::_('OS_ADD_AGENT'));
					}
					?>
				
				<?php
				if($agent->id == 0){
					self::generateNav('company_addagents');
				}else{
					self::generateNav('company_editagent');
				}
				?>
				<form class="form-horizontal" action="<?php echo Route::_("index.php?option=com_osproperty&task=company_savenewagent");?>" method="POST" name="add_agent_form" id="add_agent_form" enctype="multipart/form-data">
				<div class="btn-toolbar">
					<div class="btn-group">
		                <button type="button" class="btn btn-primary" onclick="javascript:submitForm('company_applyagent')">
		                    <?php echo Text::_('OS_APPLY')?>                
		                </button>
		                <button type="button" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" onclick="javascript:submitForm('company_saveagent')">
		                    <?php echo Text::_('OS_SAVE')?>                 
		                </button>
		                <button type="button" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" onclick="javascript:submitForm('company_cancelagent')">
		                    <?php echo Text::_('OS_CANCEL')?>                
		                </button>
		            </div>
		        </div>
		        <ul class="nav nav-tabs">
		            <li class="active"><a href="#agentdetails" data-toggle="tab"><?php echo Text::_('OS_DETAILS');?></a></li>
		            <li><a href="#agentbio" data-toggle="tab"><?php echo Text::_('OS_BIO');?> & <?php echo Text::_('OS_IMAGE');?></a></li>
		           	<li><a href="#agentother" data-toggle="tab"><?php echo Text::_('OS_OTHER')?></a></li>
		        </ul>
		        <div class="tab-content">
		            <div class="tab-pane active" id="agentdetails">
		                <fieldset>
		                    <legend><?php echo Text::_('OS_DETAILS');?></legend>
		                    
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_NAME')?> *</label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="name" placeholder="<?php echo Text::_('OS_NAME')?>" name="name" class="input-large" value="<?php echo htmlspecialchars($agent->name);?>"/>
							 		 <input type="hidden" id="alias" name="alias" value="<?php echo htmlspecialchars($agent->alias);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_EMAIL')?> *</label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="email" placeholder="<?php echo Text::_('OS_EMAIL')?>" name="email" class="input-large" value="<?php echo htmlspecialchars($agent->email);?>" />
							 	 </div>
							 </div>
		                    <?php
		                    if($agent->id == 0){
		                    ?>
		                     <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_USERNAME')?> *</label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="username" placeholder="<?php echo Text::_('OS_USERNAME')?>" name="username" class="input-large" />
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PASSWORD')?> *</label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="password" id="password"  name="password" class="input-large" />
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_VPWD')?> *</label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="password" id="password1"  name="password1" class="input-large" />
							 	 </div>
							 </div>
							 <?php
		                     }
							 ?>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PHONE')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="phone"  name="phone" class="input-large" placeholder="<?php echo Text::_('OS_PHONE')?>" value="<?php echo htmlspecialchars($agent->phone);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MOBILE')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="mobile"  name="mobile" class="input-large" placeholder="<?php echo Text::_('OS_MOBILE')?>" value="<?php echo htmlspecialchars($agent->mobile);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FAX')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="fax"  name="fax" class="input-large" placeholder="<?php echo Text::_('OS_FAX')?>" value="<?php echo htmlspecialchars($agent->fax);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_LICENSE')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="license"  name="license" class="input-large" placeholder="<?php echo Text::_('OS_LICENSE')?>" value="<?php echo htmlspecialchars($agent->license);?>"/>
							 	 </div>
							 </div>
		                </fieldset>
                		<fieldset>
                    		<legend><?php echo Text::_('OS_ADDRESS');?></legend>
                    		<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_ADDRESS')?></label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<input type="text" name="address" id="address" size="30" value="<?php echo htmlspecialchars($agent->address);?>" class="input-large" placeholder="<?php echo Text::_('OS_ADDRESS')?>"/>
								</div>
							</div>
							<?php
							if(HelperOspropertyCommon::checkCountry()){
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COUNTRY')?> *</label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
									<?php echo $lists['country']?>
								</div>
							</div>	
							<?php
							}else{
								echo $lists['country'];
							}
							if(OSPHelper::userOneState()){
								echo $lists['state'];
							}else{
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_STATE')?> *</label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="country_state">
									<?php
									echo $lists['state'];
									?>
								</div>
							</div>
							<?php } ?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
								<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_CITY')?> *</label>
								<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="city_div">
									<?php
									echo $lists['city'];
									?>
								</div>
							</div>
                    	</fieldset>
                    	<fieldset>
                    		<legend><?php echo Text::_('OS_WEB');?></legend>
                    		<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_YAHOO')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="yahoo"  name="yahoo" class="input-medium" placeholder="<?php echo Text::_('OS_YAHOO')?>" value="<?php echo htmlspecialchars($agent->yahoo);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_SKYPE')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="skype"  name="skype" class="input-medium" placeholder="<?php echo Text::_('OS_SKYPE')?>" value="<?php echo htmlspecialchars($agent->skype);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_AIM')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="skype"  name="aim" class="input-medium" placeholder="<?php echo Text::_('OS_AIM')?>" value="<?php echo htmlspecialchars($agent->aim);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MSN')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="msn"  name="msn" class="input-medium" placeholder="<?php echo Text::_('OS_MSN')?>" value="<?php echo htmlspecialchars($agent->msn);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_GTALK')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="gtalk"  name="gtalk" class="input-medium" placeholder="<?php echo Text::_('OS_GTALK')?>" value="<?php echo htmlspecialchars($agent->gtalk);?>"/>
							 	 </div>
							 </div>
							 <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
							 	 <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FACEBOOK')?></label>
							 	 <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
							 		 <input type="text" id="facebook"  name="facebook" class="input-medium" placeholder="<?php echo Text::_('OS_FACEBOOK')?>" value="<?php echo htmlspecialchars($agent->facebook);?>"/>
							 	 </div>
							 </div>
                    	</fieldset>
            		</div>
            		<div class="tab-pane" id="agentbio">
		                <fieldset>
		                    <legend><?php echo Text::_('OS_PHOTO');?></legend>
		                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
		                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PHOTO')?></label>
		                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
		                        	<?php
									if($agent->photo != ""){
										?>
										<img src="<?php echo Uri::root()?>images/osproperty/agent/<?php echo $agent->photo?>" width="100" />
										<BR />
										<input type="checkbox" name="remove_photo" id="remove_photo" onclick="javascript:changeValue('remove_photo')" value="0" /> <?php echo Text::_('OS_REMOVE_PHOTO');?>
										<div class="clearfix"></div>
										<?php
									}
									?>
									<span id="photodiv">
		                        	<input type="file" name="file_photo" id="file_photo" size="30" class="input-medium form-control" onchange="javascript:checkUploadPhotoFiles('file_photo')" />
		                        	<div class="clearfix"></div>
									<span class="small">(<?php echo Text::_('OS_ONLY_SUPPORT_JPG_IMAGES');?>)</span>
									</span>
		                        </div>
		                    </div>
		                </fieldset>
		                <fieldset>
		                    <legend><?php echo Text::_('OS_BIO');?></legend>
		                    <?php
		                    $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		                    echo $editor->display( 'bio',  stripslashes($agent->bio) , '95%', '250', '90', '20',false ) ;
		                    ?>
		                </fieldset>
		            </div>
		            <div class="tab-pane" id="agentother">
		                <fieldset>
		                    <legend><?php echo Text::_('OS_OTHER');?></legend>
		                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
		                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PUBLISH')?></label>
		                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
		                        	<?php echo $lists['published'];?>
		                        </div>
		                    </div>
		                    <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
		                        <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FEATURED')?></label>
		                        <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
		                        	<?php echo $lists['featured'];?>
		                        </div>
		                    </div>
		                </fieldset>
		            </div>
		            <input type="hidden" name="option" value="com_osproperty" />
		            <input type="hidden" name="task" value="" />
		            <input type="hidden" name="id" id="id" value="<?php echo $agent->id?>" />
		            <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id?>" />
		            <input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root();?>" />
		            <input type="hidden" name="require_field_profileForm" id="require_field_profileForm" value="name,username,email,state,city" />
					<input type="hidden" name="require_label_profileForm" id="require_label_profileForm" value="<?php echo Text::_("Name")?>,<?php echo Text::_('OS_LOGIN_NAME')?>,<?php echo Text::_("OS_EMAIL")?>,<?php echo Text::_("OS_STATE")?>,<?php echo Text::_("OS_CITY")?>" />
					<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
					<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
				</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		function submitForm(task){
			var form = document.add_agent_form;
			if(task == "company_cancelagent"){
				form.task.value 	 = task;
				form.submit( task );
			}else{
				var temp1,temp2;
				var cansubmit = 1;
				var require_field = document.getElementById('require_field_profileForm');
				require_field = require_field.value;
				var require_label = document.getElementById('require_label_profileForm');
				require_label = require_label.value;
				var require_fieldArr = require_field.split(",");
				var require_labelArr = require_label.split(",");
				for(i=0;i<require_fieldArr.length;i++){
					temp1 = require_fieldArr[i];
					temp2 = form[temp1]; // hungvd repair
					//temp2 = document.getElementById(temp1);
					if(temp2 != null){
						if((temp2.value == "") && (cansubmit == 1)){
							alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
							temp2.focus();
							cansubmit = 0;
						}
					}
				}
				<?php
				if($agent->id == 0){
				?>
				if (cansubmit = 1){
					password 	= form['password'];
					password2 	= form['password1'];
					if (password.value != '' && password.value != password2.value){
						alert("<?php echo Text::_('OS_NEW_PASSWORD_IS_NOT_CORRECT')?>");
						cansubmit = 0;
					}
				}
				<?php
				}
				?>
				if(cansubmit == 1){
					form.task.value 	 = task;
			   		form.submit( task );
				}
			}
		}
		function loadState(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoStateCityBackend(country_id,state_id,city_id,'country','state',live_site);
		}
		function loadCity(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
		}
		function loadCityBackend(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
		}
		</script>
		<?php
	}
	
	/**
	 * Manage Properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function manageProperties($option,$rows,$pageNav,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		OSPHelper::loadTooltip();
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php 
				OSPHelper::generateHeading(2,Text::_('OS_MANAGE_PROPERTIES'));
				?>
				<?php
				self::generateNav('company_properties');
				?>
				<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty')?>" name="ftForm" id="ftForm" >
				<div id="filter-bar" class="btn-toolbar">
		            <div class="filter-search btn-group pull-left">
		                <input type="text" name="filter_search" class="inputbox input-medium" id="filter_search" value="<?php echo OSPHelper::getStringRequest('filter_search','','')?>" title="<?php echo Text::_('OS_SEARCH');?>" />
		            </div>
		            <div class="btn-group pull-left <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
		                <button class="btn hasTooltip" type="submit" title="<?php echo Text::_('OS_SEARCH');?>"><i class="osicon-search"></i></button>
		                <button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" rel="tooltip" title="<?php echo Text::_('OS_CLEAR');?>"><i class="osicon-remove"></i></button>
		            </div>
		            <div class="btn-group pull-right <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
		                <?php echo $lists['orderby'];?>
		            </div>
		            <div class="btn-group pull-right">
		            	<?php echo $lists['sortby'];?>
		            </div>
		        </div>
		        <div class="clearfix"> </div>
		        
		        <div id="filter-bar" class="btn-toolbar">
		            <div class="btn-group pull-right">
		                <?php echo $lists['status'];?>
		            </div>
		            <div class="btn-group pull-right">
		                <?php echo $lists['type'];?>
		            </div>
		            <div class="btn-group pull-right">
		                <?php echo $lists['category'];?>
		            </div>
		        </div>
		        <div class="clearfix"></div>
				<div class="btn-toolbar">
		            <div class="btn-group">
		            	<?php 
		                if($configClass['company_admin_add_properties'] == 1){
		                	?>
		                	<button type="button" class="btn hasTooltip btn-info" title="<?php echo Text::_('OS_ADD');?>" onclick="javascript:submitForm('company_addproperty');">
			                    <i class="osicon-new"></i> <?php echo Text::_('OS_ADD');?>
			                </button>
		                	<?php 
		                }
		                ?>
		                <button type="button" class="btn hasTooltip btn-success" title="<?php echo Text::_('OS_PUBLISH');?>" onclick="javascript:submitForm('company_publishproperties');">
		                    <i class="osicon-ok"></i> <?php echo Text::_('OS_PUBLISH');?>
		                </button>
		                <button type="button" class="btn hasTooltip btn-warning" title="<?php echo Text::_('OS_UNPUBLISH');?>" onclick="javascript:submitForm('company_unpublishproperties');">
		                    <i class="osicon-unpublish"></i> <?php echo Text::_('OS_UNPUBLISH');?>
		                </button>
		                <button type="button" class="btn hasTooltip btn-danger" title="<?php echo Text::_('OS_REMOVE');?>" onclick="if(confirm('<?php echo Text::_('OS_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM')?>')){javascript:submitForm('company_removeproperties');}else{return false;}">
		                    <i class="osicon-remove"></i> <?php echo Text::_('OS_REMOVE');?>
		                </button>
		            </div>
		        </div>
		        <div class="clearfix"></div>
		        <table class="ptable table table-striped tablelistproperties" id="propertyList">
		            <thead>
		                <tr>
		                    <th width="1%" class="center ">
		                        <input type="checkbox" name="checkall-toggle" value="" title="Check All" onclick="Joomla.checkAll(this)" />
		                    </th>                    
		                    <th width="40%" class="nowrap">
		                       <?php echo Text::_('OS_LOCATION').' / '.Text::_('OS_TITLE').' / '.Text::_('Ref #').' / '.Text::_('OS_PRICE');?>
		                    </th>
		                    <th width="15%" class="nowrap "><?php echo Text::_('OS_CATEGORY');?></th>
		                    <th width="15%" class="nowrap "><?php echo Text::_('OS_TYPE');?></th>
		                    <th width="15%" class="nowrap "><?php echo Text::_('OS_OWNER');?></th>
		                    <th width="10%" class="nowrap center "><?php echo Text::_('OS_ACTION');?></th>
		                    <th width="5%" class="nowrap center">
		                        ID                    
		                    </th>
		                </tr>
		            </thead>
                    <?php
                    if(count($rows) > 0){
                    ?>
		            <tfoot>
		            	<tr>
		            		<td width="100%" colspan="7" class="center">
		            			<?php echo $pageNav->getListFooter();?>
		            		</td>
		            	</tr>
		            </tfoot>
		            <tbody>
		            	<?php
		            	$k = 0;
		            	for($i=0;$i<count($rows);$i++){
		            		$row = $rows[$i];
		            		?>
		            		<tr class="row<?php echo $k;?>">
		            			<td class="center " data-label="">
                                	<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id;?>" onclick="Joomla.isChecked(this.checked);" title="Checkbox for row <?php echo $i + 1;?>" />
                                </td>                            
                           		<td class="has-context" data-label="<?php echo Text::_('OS_PROPERTY');?>">
                                	<div class="pull-left">
                                        <span class="hasTip" title="&lt;img src=&quot;<?php echo $row->photo;?>&quot; alt=&quot;<?php echo str_replace("'","",$row->pro_name);?>&quot; width=&quot;100&quot; /&gt;"><i class="osicon-camera"></i></span> | 
                                        <?php
                                        $needs = array();
                                        $needs[] = "property_details";
                                        $needs[] = $row->id;
                                        $itemid  = OSPRoute::getItemid($needs);
                                        ?>
                                        <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_stas&id='.$row->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>">
                                         <?php echo $row->pro_name?>
                                         <?php
                                         if($row->show_address == 1){
                                         ?>
                                         , <?php echo $row->city;?> - <?php echo $row->state_name;?>
                                         <?php
                                         }
                                         ?>
                                         </a>
                                         <?php 
                                         if($configClass['company_admin_add_properties'] == 1){
                                         ?>
                                         	(<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_edit&id='.$row->id);?>"><?php echo Text::_('OS_EDIT')?></a>)
                                         <?php
                                         }
                                         if($row->isFeatured == 1){
                                         	?>
                                         	<span title="<?php echo Text::_('OS_FEATURED_PROPERTY');?>" class="colororange">
                                         		<i class="osicon-star icon-white"></i>
                                         	</span>
                                         	<?php
                                         }
                                         ?>
                                         <?php
                                         if($row->show_address == 1){
                                         ?>
                                         <br /><span class="small"><?php echo $row->address?> </span>
                                         <?php
                                         }
                                         if(($row->ref != "") and ($configClass['show_ref'] == 1)){
                                         ?>
                                         <br /><strong>Ref #:</strong> <?php echo $row->ref;?>
                                         <?php
                                         }
                                         ?>
                                         <br />
                                         <?php
                                         if($row->price_call == 0){
                                         ?>
                                         	<strong><?php echo Text::_('OS_PRICE')?>:</strong> <?php echo OSPHelper::generatePrice($row->curr,$row->price);?>
	                                         <?php
	                                         if($row->rent_time != ""){
	                                         	echo "/".Text::_($row->rent_time);
	                                         }
	                                         ?>
                                         <?php
                                         }else{
                                         ?>
                                         <strong>
                                         	<?php echo Text::_('OS_CALL_FOR_PRICE');?>
                                         </strong>
                                         <?php
                                         }
                                         ?>
										 <?php
										 if($configClass['general_use_expiration_management']==1){
										 ?>
										 <div class="clearfix"></div>
										 <?php
											if(($row->expiration->expired_time != "0000-00-00 00:00:00") and ($row->expiration->expired_time != "")){
												if($row->approved == 1){
													echo Text::_('OS_EXPIRED_ON').": ".HelperOspropertyCommon::loadTime($row->expiration->expired_time,2)	;
                                                    echo '<div class="clearfix"></div>';
												}else{
													?>
													<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_edit_activelisting&id='.$row->id.'&new=0&Itemid='.$itemid)?>" title="<?php echo Text::_('OS_REQUEST_APPROVAL');?>" class="btn btn-warning">
														<?php echo Text::_('OS_REQUEST_APPROVAL');?>
													</a>
													<?php
												}
											}
											if($row->isFeatured == 1){
												echo '<div class="clearfix"></div>';
												if(($row->expiration->expired_feature_time != "0000-00-00 00:00:00") and ($row->expiration->expired_feature_time != "")){
													echo Text::_('OS_EXPIRED_FEATURED_TIME').": ";
													echo HelperOspropertyCommon::loadTime($row->expiration->expired_feature_time,2);
												}
											}else{
												//echo '<div class="clearfix"></div>';
												?>
												<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_upgrade&cid[]='.$row->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OS_UPGRADE_FEATURED');?>" class="btn btn-danger">
													<?php echo Text::_('OS_UPGRADE_FEATURED');?>
												</a>
<?php
											}
										 ?>
										 <?php } ?>
                                    </div>
                           		</td>
                           		<td class="small " data-label="<?php echo Text::_('OS_CATEGORY');?>">
                           			<?php echo OSPHelper::getCategoryNamesOfProperty($row->id);//echo $row->category_name;?>
                           		</td>
                           		<td class="small " data-label="<?php echo Text::_('OS_TYPE');?>">
                           			<?php echo $row->type_name;?>
                           		</td>
                           		<td class="small " data-label="<?php echo Text::_('OS_OWNER');?>">
                           			<?php echo $row->agent_name;?>
                           		</td>
                           		<td class="small center">
                           			<?php
                           			if($row->published == 1){
                           			?>
                           			<a class="btn btn-micro active btn-success" title="<?php echo Text::_('OS_UNPUBLISH')?> <?php echo Text::_('OS_ITEM');?>" href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=company_unpublishproperties&cid[]=<?php echo $row->id?>">
										<i class="osicon-publish"></i>
									</a>
									<?php
                           			}else{
                           			?>
                           			<a class="btn btn-micro active btn-danger" title="<?php echo Text::_('OS_PUBLISH')?> <?php echo Text::_('OS_ITEM');?>" href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=company_publishproperties&cid[]=<?php echo $row->id?>">
										<i class="osicon-unpublish"></i>
									</a>
                           			<?php
                           			}
									?>
                           		</td>
                           		<td class="small center" data-label="<?php echo Text::_('ID');?>">
                           			<?php echo $row->id; ?>
                           		</td>
		            		</tr>
		            		<?php
		            		$k = 1 - $k;
		            	}
		            	?>
		            </tbody>
                    <?php
                    }else{
                        ?>
                        <tbody><tr><td colspan="7"><div class="alert alert-no-items">
                        <?php
                        echo Text::_('OS_NO_PROPERTIES_FOUND');
                        ?></div></td></tr></tbody>
                        <?php
                    }
                    ?>
		        </table>
		        <input type="hidden" name="option" value="com_osproperty" />
		        <input type="hidden" name="task" value="company_properties" />
		        <input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		        </form>
			</div>
		</div>
		<script type="text/javascript">
	    function submitForm(task){
			var form = document.ftForm;
		    form.task.value 	 = task;
		    form.submit( task );
		}
	    </script>
		<?php
	}
	
	/**
	 * Generate the Nav bar of company edit layout
	 *
	 */
	public static function generateNav($task){
        global $bootstrapHelper, $jinput;
		$configClass = OSPHelper::loadConfig();	
		?>
		<div class="osnavbar">
		    <div class="osnavbar-inner">
			    <a class="brand" href="#"><?php echo Text::_('OS_MANAGE');?></a>
			    <ul class="nav">
			    	<?php
			    	if($task == "company_edit"){
			    		$class = "active";
			    	}else{
			    		$class = "";
			    	}
			    	?>
				    <li class="adjustli <?php echo $class;?>"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_edit');?>"><?php echo Text::_('OS_COMPANY_EDIT_PROFILE')?></a></li>
				    <?php
			    	if($task == "company_agent"){
			    		$class = "active";
			    	}else{
			    		$class = "";
			    	}
			    	?>
				    <li class="adjustli <?php echo $class;?>"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_agent');?>"><?php echo Text::_('OS_MANAGE_AGENTS')?></a></li>
				    <?php
			    	if($task == "company_properties"){
			    		$class = "active";
			    	}else{
			    		$class = "";
			    	}
			    	?>
				    <li class="adjustli <?php echo $class;?>"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_properties');?>"><?php echo Text::_('OS_MANAGE_PROPERTIES')?></a></li>
				    <?php
				    if($task == "company_addnew"){
				    	?>
				    	 <li class="adjustli active"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_addnew');?>"><?php echo Text::_('OS_ASSIGN_AGENTS_TO_COMPANY')?></a></li>
				    	<?php
				    }
				    ?>
				    <?php
				    if($task == "company_addagents"){
				    	?>
				    	 <li class="adjustli active"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_addagents');?>"><?php echo Text::_('OS_ADD_AGENT')?></a></li>
				    	<?php
				    }
				    if($task == "company_editagent"){
				    	?>
				    	 <li class="adjustli active"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_editagent&id='.$jinput->getInt('id',0));?>"><?php echo Text::_('OS_MODIFY_AGENT')?></a></li>
				    	<?php
				    }
				    ?>
				    <?php
				    if($configClass['integrate_membership'] == 1){
			    	if($task == "company_plans"){
			    		$class = "active";
			    	}else{
			    		$class = "";
			    	}
			    	?>
				    <li class="adjustli <?php echo $class;?>"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_plans');?>"><?php echo Text::_('OS_YOUR_MEMBERSHIP')?></a></li>
				    <?php }?>
			
					<?php
				    if($configClass['active_payment'] == 1){
			    	if($task == "company_ordershistory"){
			    		$class = "active";
			    	}else{
			    		$class = "";
			    	}
			    	?>
				    <li class="adjustli <?php echo $class;?>"><a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_ordershistory');?>"><?php echo Text::_('OS_YOUR_ORDERS_HISTORY')?></a></li>
				    <?php }?>
			    </ul>
		    </div>
	    </div>
		<?php
	}
	
	/**
	 * Company registration
	 *
	 * @param unknown_type $option
	 * @param unknown_type $user
	 * @param unknown_type $lists
	 */
	static function companyRegisterForm($option,$user,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/companyregistration.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('option',$option);
		$tpl->set('user',$user);
		$tpl->set('lists',$lists);
		$tpl->set('configClass',$configClass);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("companyregistration.php");
		echo $body;
	}
	
	/**
	 * List Company Plans
	 *
	 * @param unknown_type $plans
	 */
	static function listCompanyPlans($agentAcc){
        global $bootstrapHelper,$mainframe;
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="listCompanyPlans">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php 
				OSPHelper::generateHeading(2,Text::_('OS_MY_PLANS'));
				self::generateNav("company_plans");
                if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/usercredits.php')){
                    $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
                }else{
                    $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
                }
                $tpl->set('agentAcc',$agentAcc);
                if(HelperOspropertyCommon::isAgent()){
                    $tpl->set('usertype','0');
                }elseif(HelperOspropertyCommon::isCompanyAdmin()){
                    $tpl->set('usertype','2');
                }
                $body = $tpl->fetch("usercredits.php");
                echo $body;
                ?>
			</div>
		</div>
		<?php 
	}
}
?>
