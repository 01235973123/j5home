<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
//$needs = array();
//$needs[] = "property_details";
//$needs[] = $row->id;
$itemId = Factory::getApplication()->input->getInt('Itemid');

$document = Factory::getDocument();
$document->addScriptDeclaration("
	document.addEventListener('DOMContentLoaded', function () {
		var tooltipOptions = {'html' : true, 'sanitize': false};      
			if (window.jQuery && window.jQuery().tooltip){
				window.jQuery('#divmanageproperties').find('.hasTooltip').tooltip(tooltipOptions);
			} else if (bootstrap.Tooltip) {
				var tooltipTriggerList = [].slice.call(document.querySelectorAll('.hasTooltip'));
				var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				  return new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);
				});                                     
			}     
	});
 ");
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="divmanageproperties">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<?php if($supervisor == 0){ ?>
		<div class="block_caption">
			<strong><?php echo Text::_('OS_YOUR_PROPERTIES')?></strong>
		</div>
		<?php } ?>
		<div class="clearfix"></div>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<input type="text" name="filter_search" class="input-medium form-control search-query" id="filter_search" value="<?php echo OSPHelper::getStringRequest('filter_search','','')?>" title="<?php echo Text::_('OS_SEARCH');?>" />
			</div>
			<div class="btn-group pull-left <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
				<button class="btn btn-primary hasTooltip" type="submit" title="<?php echo HTMLHelper::tooltipText('OS_SEARCH');?>"><i class="osicon-search"></i></button>
				<button class="btn btn-secondary hasTooltip" type="button" onclick="javascript:document.getElementById('filter_search').value='';document.getElementById('ftForm').submit();" rel="tooltip" title="<?php echo Text::_('OS_CLEAR');?>"><i class="osicon-remove"></i></button>
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
				<?php echo $lists['featured'];?>
			</div>
			<div class="btn-group pull-right">
				<?php echo $lists['approved'];?>
			</div>
			<div class="btn-group pull-right">
				<?php echo $lists['type'];?>
			</div>
			<div class="btn-group pull-right">
				<?php echo $lists['category'];?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div id="filter-bar" class="btn-toolbar" style="margin-top:10px;">
			<div class="btn-group pull-right">
				<?php echo $lists['cities']?>
			</div>
			<div class="btn-group pull-right">
				<?php echo $lists['states']?>
			</div>
			<div class="btn-group pull-">
				<?php echo $lists['country'];?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="btn-toolbar <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
			<div class="btn-group">
				<?php
				if($supervisor == 0){
				?>
				<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_UPGRADE_FEATURED');?>" onclick="javascript:submitForm('property_upgrade');">
					<i class="osicon-featured"></i> <?php echo Text::_('OS_UPGRADE_FEATURED');?>
				</button>
				<?php
				}
				if(($configClass['general_agent_listings'] == 1) and ($supervisor == 0)){
				?>
				<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_ADD');?>" onclick="javascript:submitForm('new');">
					<i class="osicon-new"></i> <?php echo Text::_('OS_ADD');?>
				</button>
				<?php } 
				?>
				<?php
				if($configClass['active_payment'] == 0){
				?>
				<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_REQUEST_APPROVAL');?>" onclick="javascript:submitForm('requestapproval');">
					<i class="osicon-support"></i> <?php echo Text::_('OS_REQUEST_APPROVAL');?>
				</button>
				<?php } ?>
				<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_EDIT');?>" onclick="javascript:submitForm('editproperty');">
					<i class="osicon-edit"></i> <?php echo Text::_('OS_EDIT');?>
				</button>
				<button type="button" class="btn hasTooltip btn-success" title="<?php echo Text::_('OS_PUBLISHED');?>" onclick="javascript:submitForm('publishproperties');">
					<i class="osicon-publish"></i> <?php echo Text::_('OS_PUBLISHED');?>
				</button>
				<button type="button" class="btn hasTooltip btn-warning" title="<?php echo Text::_('OS_UNPUBLISHED');?>" onclick="javascript:submitForm('unpublishproperties');">
					<i class="osicon-unpublish"></i> <?php echo Text::_('OS_UNPUBLISHED');?>
				</button>
				
				<button type="button" class="btn hasTooltip btn-danger" title="Delete" onclick="javascript:submitForm('deleteproperties');">
					<i class="osicon-trash"></i> <?php echo Text::_('OS_REMOVE');?>
				</button>
			</div>
		</div>
		<table class="ptable table table-striped tablelistproperties" id="propertyList">
			<thead>
				<tr>
					<th class="nowrap" width="1%">
						ID
					</th>
					<th class="nowrap " width="2%">
						<input type="checkbox" name="checkall" id="checkall" value="0" onclick="javascript:allCheck('checkall')" />
					</th>
					<th width="35%" class="nowrap">
					   <?php echo Text::_('OS_LOCATION').' / '.Text::_('OS_TITLE').' / '.Text::_('Ref #').' / '.Text::_('OS_PRICE');?>
					</th>
					<th class="nowrap center" width="10%">
						<?php echo Text::_('OS_FEATURED')?>
					</th>
					<th class="nowrap center" width="10%">
						<?php echo Text::_('OS_APPROVED')?>
					</th>
					<th class="nowrap center" width="5%">
						<?php echo Text::_('OS_PUBLISH')?>
					</th>
					
					<?php
					if($configClass['general_use_expiration_management'] == 1){
					?>
					<th class="nowrap center" width="15%">
						<?php echo Text::_('OS_EXPIRED')?>
					</th>
					<?php
					}
					?>
					<th class="nowrap center" width="5%">
						<?php echo Text::_('OS_PRINT')?>
                        /
                        <?php echo Text::_('OS_PREVIEW')?>
					</th>
				
					<?php
					if($supervisor == 0){
						if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php"))
						{
							if($configClass['integrate_oscalendar'] == 1)
							{
								?>
								<th class="nowrap center" width="5%">
									<?php echo Text::_('OS_MANAGE_ROOMS');?>
								</th>
								<?php
							}
						}
					}
					?>
				</tr>
			</thead>
			<?php
			if(count($rows) > 0)
			{
				$k = 0;
				for($i=0;$i<count($rows);$i++)
				{
					$row            = $rows[$i];
					$link           = Route::_('index.php?option=com_osproperty&task=property_stas&id='.$row->id.'&Itemid='.$jinput->getInt('Itemid', 0));
					$extend_link    = Route::_("index.php?option=com_osproperty&task=property_edit_activelisting&id=".$row->id."&type=3");
					?>
					<tr class="row<?php echo $k;?>">
						<td class="small center" data-label="<?php echo Text::_('ID')?>">
							<?php
								echo $row->id;
							?>
						</td>
						<td class="small center">
							<input type="checkbox" name="cid[]" value="<?php echo $row->id?>" id="cb<?php echo $i?>" /> 
						</td>
						<td class="has-context" data-label="<?php echo Text::_('OS_LOCATION').' / '.Text::_('OS_TITLE').' / '.Text::_('Ref #').' / '.Text::_('OS_PRICE');?>">
							<div class="pull-left">
								<a data-toggle="tooltip" style="display:inline-block !important;" data-toggle="tooltip" class="hasTooltip" title="<img src='<?php echo $row->photo;?>'/>"><i class="osicon-camera"></i></a> | 
								 <a href="<?php echo $link;?>" title="<?php echo Text::_('OS_VIEW_PROPERTY_STATISTIC');?>">
								 <?php echo OSPHelper::getLanguageFieldValue($row,'pro_name');?>
								 <?php
								 if($row->show_address == 1){
								 ?>
								 , <?php echo $row->city;?> - <?php echo $row->state_name;?>
								 <?php
								 }
								 ?>
								 </a>
								 <?php
								 if($row->isFeatured == 1){
									?>
									<span title="<?php echo Text::_('OS_FEATURED_PROPERTY');?>">
										<i class="osicon-star icon-red"></i>
									</span>
									<?php
								 }
								 ?>
								 <?php
								 if($row->show_address == 1){
								 ?>
								 <br /><i class="fa fa-map-marker"></i>&nbsp;<span class="small"><?php echo $row->address?> </span>
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
								if($row->price_text != "")
								{
									echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
								}
								elseif($row->price_call == 0)
								{
								 ?>
									<strong><?php echo Text::_('OS_PRICE')?>:</strong> 
									<?php echo OSPHelper::generatePrice($row->curr,$row->price)?>
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
								 if($supervisor == 1){
									 ?>
									 <BR />
									 <strong><?php echo Text::_('OS_OWNER')?>: </strong>
									 <?php
									 $needs = array();
									 $needs[] = "agent_info";
									 $needs[] = $row->agent_id;
									 $itemid  = OSPRoute::getItemid($needs);
									 $link    = Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->agent_id.'&Itemid='.$itemid);
									 echo "<a href='".$link."' title='".Text::_('OS_AGENT_DETAILS')."' target='_blank'>".$row->agent_name."</a>";
								 }
								 ?>
								 <BR />
								 <strong>
									<?php echo Text::_('OS_CATEGORY')?>:
								 </strong>
								 <?php echo OSPHelper::getCategoryNamesOfProperty($row->id);?>
								 <BR />
								 <strong>
									<?php echo Text::_('OS_PROPERTY_TYPE')?>:
								 </strong>
								 <?php echo $row->type_name;?>
								 <?php
								 if($row->posted_by == 1){
									$db = Factory::getDbo();
									$db->setQuery("Select company_name from #__osrs_companies where id = '$row->company_id'");
									$company_name = $db->loadResult();
									 ?>
									 <BR />
									 <strong>
										<?php echo Text::_('OS_POSTED_BY');?>&nbsp;<?php echo Text::_('OS_COMPANY')?>:
									 </strong>
									 <?php
									 echo $company_name;
								 }
								 ?>
								 <BR />
								 <strong>
								 <?php echo Text::_('OS_REQUEST_INFO')?>:
								 </strong>
								 <?php
								 if($row->total_request_info){
									echo $row->total_request_info;
								 }else{
									echo Text::_('OS_NOT_SET');
								 }
								 ?>
								 &nbsp;|&nbsp;
								 <strong>
									<?php echo Text::_('OS_HITS')?>:
								 </strong>
								 <?php
								 echo intval($row->hits);
								 ?>
								 &nbsp;|&nbsp;
								 <strong>
								 <?php echo Text::_('OS_RATING')?>:
								 </strong>
								<?php
								if($row->number_votes > 0){
									$points = round($row->total_points/$row->number_votes);
									?>
									<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />
									<?php
								}else{
									?>
									<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/stars-0.png" />
									<?php
								}
								?>
							</div>
						</td>
						
						<td class="small center" data-label="<?php echo Text::_('OS_FEATURED')?>">
							<?php
							$tooltip = HelperOspropertyCommon::loadFeatureInfo($row->id);
							if($supervisor == 0){
								if($row->isFeatured == 1){
									if($configClass['general_use_expiration_management']  == 1){
										?>
										<span class="hasTooltip" title="<?php echo Text::_('OS_FEATURED')?>::<?php echo $tooltip; ?>">
										<?php
									}
									?>
									<a class="btn btn-micro active btn-info hasTooltip" href="javascript:unfeaturedproperty(<?php echo $row->id?>);" title="<?php echo Text::_('OS_CLICK_HERE_TO_UNFEATURED_THIS_PROPERTY');?>">
										<i class="osicon-star"></i>
									</a>
									<?php
									if($configClass['general_use_expiration_management']  == 1){
										echo "</span>";
									}
								}else{
									?>
									<a class="btn btn-micro active btn-warning disabled hasTooltip" title="<?php echo Text::_('OS_UNFEATURED')?>">
										<i class="osicon-star osicon-white"></i>
									</a>
									<?php
									if($configClass['integrate_membership'] == 0){
									?>
									<BR />
									<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_upgrade&cid[]=<?php echo $row->id?>&Itemid=<?php echo $jinput->getInt('Itemid',0);?>" class="fontsmall colorgray"><?php echo Text::_('OS_UPGRADE_FEATURED');?></a>
									<?php
									}
								}
							}else{
								if($row->isFeatured == 1){
								?>
									<a class="btn btn-micro active btn-success hasTooltip" href="javascript:changeStatus(<?php echo $row->id?>,'isFeatured',0);" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_FEATURED_STATUS');?>">
										<i class="osicon-star"></i>
									</a>
								<?php
								}else{
									?>
									<a class="btn btn-micro active btn-danger hasTooltip" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_FEATURED_STATUS')?>" href="javascript:changeStatus(<?php echo $row->id?>,'isFeatured',1);">
										<i class="osicon-star osicon-white"></i>
									</a>
									<?php
								}
							}
							?>
						</td>
						<td class="small center" data-label="<?php echo Text::_('OS_APPROVED')?>">
							<?php
							$tooltip = HelperOspropertyCommon::loadApprovalInfo($row->id);
							if($supervisor == 0){
								if($row->approved == 1){
									if($configClass['general_use_expiration_management']  == 1){
									?>
										<span class="hasTooltip" title="<?php echo Text::_('OS_APPROVAL')?>::<?php echo $tooltip; ?>">
										<?php
									}
									?>
									<a class="btn btn-micro active btn-info disabled hasTooltip" title="<?php echo Text::_('OS_APPROVAL')?>">
									<i class="osicon-ok"></i>
									</a>
									<?php
									if($configClass['general_use_expiration_management']  == 1){
										echo "</span>";
									}
								}else{
									if($configClass['general_use_expiration_management'] == 1){
										?>
										<a class="btn btn-micro active btn-warning hasTooltip" title="<?php echo Text::_('OS_EXTEND_LIVE_TIME')?>" href="<?php echo $extend_link;?>">
											<i class="osicon-loop osicon-white"></i>
										</a>
										<?php
									}else{
										?>
										<a class="btn btn-micro active btn-warning disabled" title="<?php echo Text::_('OS_UNAPPROVAL')?>">
											<i class="osicon-cancel osicon-white"></i>
										</a>
										<?php
									}
								}
							}else{
								if($row->approved == 1){
								?>
									<a class="btn btn-micro active btn-success hasTooltip" href="javascript:changeStatus(<?php echo $row->id?>,'approved',0);" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_APPROVAL_STATUS');?>">
										<i class="osicon-star"></i>
									</a>
								<?php
								}else{
									?>
									<a class="btn btn-micro active btn-danger hasTooltip" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_APPROVAL_STATUS')?>" href="javascript:changeStatus(<?php echo $row->id?>,'approved',1);">
										<i class="osicon-star osicon-white"></i>
									</a>
									<?php
								}
							}
							?>
						</td>
						<td class="small center" data-label="<?php echo Text::_('OS_PUBLISH')?>">
							<?php
							if($supervisor == 0)
							{
								if($row->published == 1)
								{
									?>
									<a class="btn btn-micro active btn-success hasTooltip" title="<?php echo Text::_('OS_UNPUBLISH')?> <?php echo Text::_('OS_ITEM');?>" href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_unpublishproperties&cid[]='.$row->id.'&Itemid='.$itemId);?>">
										<i class="osicon-publish"></i>
									</a>
									<?php
								}
								else
								{
									?>
									<a class="btn btn-micro active btn-danger hasTooltip" title="<?php echo Text::_('OS_PUBLISH')?> <?php echo Text::_('OS_ITEM');?>" href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_publishproperties&cid[]='.$row->id.'&Itemid='.$itemId)?>">
										<i class="osicon-unpublish"></i>
									</a>
									<?php
								}
							}
							else
							{
								if($row->published == 1)
								{
									?>
									<a class="btn btn-micro active btn-success hasTooltip" href="javascript:changeStatus(<?php echo $row->id?>,'published',0);" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_PUBLISH_STATUS');?>">
										<i class="osicon-star"></i>
									</a>
									<?php
								}
								else
								{
									?>
									<a class="btn btn-micro active btn-danger hasTooltip" title="<?php echo Text::_('OS_CLICK_HERE_TO_CHANGE_PUBLISH_STATUS')?>" href="javascript:changeStatus(<?php echo $row->id?>,'published',1);">
										<i class="osicon-star osicon-white"></i>
									</a>
									<?php
								}
							}
							?>
						</td>
						<?php
						if($configClass['general_use_expiration_management']==1){
						?>
						<td class="center" data-label="<?php echo Text::_('OS_EXPIRED')?>">
							<?php
							if(($row->expired_time != "0000-00-00 00:00:00") and ($row->expired_time != "")){
								echo HelperOspropertyCommon::loadTime($row->expired_time,2)	;											
							}
							if($row->isFeatured == 1){
								echo "<BR />";
								if(($row->expired_feature_time != "0000-00-00 00:00:00") and ($row->expired_feature_time != "")){
									echo "<span class='fontsmall colorred'>".Text::_('OS_EXPIRED_FEATURED_TIME')."</span>";
									echo HelperOspropertyCommon::loadTime($row->expired_feature_time,2);
								}
							}
							?>
						</td>
						
						<?php
						}
						?>
						
						<td class="small center" data-label="<?php echo Text::_('OS_PRINT')?>">
							<?php $open_print = "window.open ('".Uri::root()."index.php?option=com_osproperty&tmpl=component&task=property_print&id=". $row->id ."', 'mywindow','menubar=0,status=0,location=0,status=0,scrollbars=1,resizable=0,toolbar=0,directories=0, width=1000,height=700')";?>
							<a href="javascript:void(0);" onclick="javascript:<?php echo $open_print?>;" class="<?php echo $bootstrapHelper->getClassMapping('btn');?> hasTooltip" title="<?php echo Text::_('OS_PRINT')?>">
								<i class="osicon-print"></i>
							</a>
                            <?php
                            
                            if($row->published == 1)
                            {
                                $link = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')) . Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemId);
                            }
                            else
                            {
                                $link = Uri::root().'index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemId.'&preview=1';
                            }
                            ?>
                            <a href="<?php echo $link?>" target="_blank" class="<?php echo $bootstrapHelper->getClassMapping('btn');?> hasTooltip" title="<?php echo Text::_('OS_PREVIEW')?>">
                                <i class="osicon-camera"></i>
                            </a>
						</td>
						
						<?php
						if($supervisor == 0){
							if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php"))
							{
								if($configClass['integrate_oscalendar'] == 1)
								{
									?>
									<td align="center" class="data_td center" style="background-color:<?php echo $bgcolor?>;" data-label="<?php echo Text::_('OS_MANAGE_ROOMS')?>">
										<a href="<?php echo Route::_("index.php?option=com_oscalendar&task=room_manage&pid=".$row->id."&Itemid=".$jinput->getInt('Itemid',0));?>" target="_blank" title="<?php echo Text::_('OS_MANAGE_ROOMS')?>" class="btn hasTooltip" />
											<i class="osicon-calendar"></i>
										</a>
									</td>
									<?php	
								}
							}
						}
						?>
					</tr>
					<?php
				}
				?>
				<tfoot>
					<tr>
						<td width="100%" align="center" colspan="11" class="padding5 center">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<?php
			}
			?>
		</table>
	</div>	
</div>
