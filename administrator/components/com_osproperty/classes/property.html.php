<?php
/*------------------------------------------------------------------------
# property.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;

class HTML_OspropertyProperties
{	
	/**
	 * List properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function listProperties($option,$rows,$pageNav,$lists)
	{
		global $jinput, $mainframe ,$configClass,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		$span11Class   = $bootstrapHelper->getClassMapping('span11');
		$span1Class    = $bootstrapHelper->getClassMapping('span1');
		$span10Class   = $bootstrapHelper->getClassMapping('span10');
		$span2Class   = $bootstrapHelper->getClassMapping('span2');
		$db = Factory::getDBO();
		$config = Factory::getConfig();
		HTMLHelper::_('behavior.multiselect');
		//HTMLHelper::_('behavior.modal','a.osmodal');
		echo OSPHelper::loadTooltip();
		ToolBarHelper::title(Text::_('OS_MANAGE_PROPERTIES'),"home");
		ToolBarHelper::addNew('properties_add');

		if(OSPHelper::isJoomla4())
		{
			$toolbar = Toolbar::getInstance('toolbar');
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->standardButton('properties_edit')
					->text('OS_EDIT')
				    ->icon('fa fa-edit')
					->task('properties_edit')
					->listCheck(true);
			$childBar->standardButton('properties_copy')
					->text('OS_DUPLICATE')
				    ->icon('fa fa-copy')
					->task('properties_copy')
					->listCheck(true);
			$childBar->standardButton('properties_approval')
					->text('OS_APPROVAL')
				    ->icon('fa fa-check')
					->task('properties_approval')
					->listCheck(true);
			$childBar->standardButton('properties_unapproval')
					->text('OS_UNAPPROVAL')
				    ->icon('icon-delete')
					->task('properties_unapproval')
					->listCheck(true);
			$childBar->delete('properties_remove')
					->message('OS_ARE_YOU_SURE_TO_REMOVE_ITEM')
					->task('properties_remove')
					->listCheck(true);
			$childBar->publish('properties_publish')->listCheck(true);
			$childBar->unpublish('properties_unpublish')->listCheck(true);
		}
		else
		{
			if (count($rows))
			{
				ToolBarHelper::editList('properties_edit');
				ToolBarHelper::custom('properties_copy','copy.png','copy.png',Text::_('OS_DUPLICATE'));
				if (version_compare(JVERSION, '3.0', 'lt')) {
					ToolBarHelper::custom('properties_approval','approval.png','approval.png',Text::_('OS_APPROVAL'));
					ToolBarHelper::custom('properties_unapproval','unapproval.png','unapproval.png',Text::_('OS_UNAPPROVAL'));
				}
				else
				{
					ToolBarHelper::custom('properties_approval','ok.png','ok.png',Text::_('OS_APPROVAL'));
					ToolBarHelper::custom('properties_unapproval','remove.png','remove.png',Text::_('OS_UNAPPROVAL'));
				}
				ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'properties_remove');
				ToolBarHelper::publish('properties_publish');
				ToolBarHelper::unpublish('properties_unpublish');
			}
		}

		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		$tmpl = $jinput->getString('tmpl','');
		if($tmpl == "component")
		{
			$tmpl_url = "&tmpl=component";
		}
		else
		{
			$tmpl_url = "";
		}
        if($lists['show_form'] == 1)
		{
            $class = "btn-primary";
            $display = "block";
        }
		else
		{
            $class ="";
            $display = "none";
        }

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'a.ordering';
		$ordering	= ($listOrder == 'a.ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=properties_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'propertiesListTab', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
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
		<form method="POST" action="index.php?option=com_osproperty&task=properties_list<?php echo $tmpl_url;?>" name="adminForm" id="adminForm">

        <input type="hidden" name="open_search_from" id="open_search_from" value="<?php echo $lists['show_form'];?>" />
		<div class="<?php echo $rowFluidClass; ?>">
            <div class="<?php echo $span10Class; ?> js-stools-container-bar">
                <div class="btn-wrapper btn-group">
					<div class="input-group input-append">
						<input placeholder="<?php echo Text::_('OS_SEARCH');?>" type="text" id="keyword" name="keyword" value="<?php echo $mainframe->getUserState('pro_list.filter.keyword');?>" class="input-medium form-control" />
						<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
							<i class="icon-search"></i>
						</button>
					</div>
                </div>
				<?php
				if(OSPHelper::isJoomla4())
				{
					?>
					<div class="btn-group">
						<button type="button" id="filter_search_button" class="btn btn-primary js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
							<?php echo Text::_('JFILTER_OPTIONS'); ?>
							<span class="icon-angle-down" aria-hidden="true"></span>
						</button>
						<button type="button" id="clear_search_button" class="btn btn-warning js-stools-btn-clear" title="Clear">
							<?php echo Text::_('OS_CLEAR');?>
						</button>
					</div>
					<?php
				}
				else
				{
				?>
                <div class="btn-wrapper hidden-phone">
                    <button type="button" id="filter_search_button" class="btn btn-success hasTooltip js-stools-btn-filter <?php echo $class;?>" title="Filter the list items">
                        <?php echo Text::_('OS_SEARCH_TOOLS');?> <i class="caret"></i>
                    </button>
                </div>
                <div class="btn-wrapper hidden-phone">
                    <button type="button" id="clear_search_button" class="btn btn-warning hasTooltip js-stools-btn-clear" title="Clear">
                        <?php echo Text::_('OS_CLEAR');?>
                    </button>
                </div>
				<?php
				}	
				?>
            </div>
			<div class="<?php echo $span2Class; ?> btn-group pull-right hidden-phone">
				<?php
				echo $pageNav->getLimitBox();
				?>
			</div>
        </div>
        <div class="<?php echo $rowFluidClass; ?>" ID="search_param_div" style="display:<?php echo $display;?>;">
            <div class="<?php echo $span12Class; ?>">
                <div class="js-stools-container-filters hidden-phone clearfix shown">
                    <div class="js-stools-field-filter">
                        <?php
						if(OSPHelper::isJoomla4())
						{
							$class = "imedium form-select";
						}
						else
						{
							$class = "chosen";
						}
						OSPHelper::loadAgentTypeDropdownFilter($jinput->getInt('agent_type',-1), $class.' input-medium','onChange="javascript:document.adminForm.submit();"');
						?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['company'];?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['agent']?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['category']?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['type']?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['country'];?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['states']?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['cities']?>
                    </div>
                    <?php
                    if($configClass['use_bedrooms'] == 1){
                        ?>
                        <div class="js-stools-field-filter">
                            <?php echo $lists['nbed']?>
                        </div>
                    <?php
                    }
                    ?>
                    <?php
                    if($configClass['use_bathrooms'] == 1){
                        ?>
                        <div class="js-stools-field-filter">
                            <?php echo $lists['nbath']?>
                        </div>
                    <?php
                    }
                    ?>
                    <?php
                    if($configClass['use_rooms'] == 1){
                        ?>
                        <div class="js-stools-field-filter">
                            <?php echo $lists['nrooms']?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['state']?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['request'];?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['state_approval'];?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['request_to_approval'];?>
                    </div>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['isfeature'];?>
                    </div>
					<div class="js-stools-field-filter">
                        <?php echo $lists['propertiesposted'];?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if(count($rows) > 0) 
		{
            ?>

            <table class="adminlist table table-striped" id="propertiesListTab">
                <thead>
					<tr>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<?php
						if ($tmpl != "component") 
						{
							if(!OSPHelper::isJoomla4())
							{
							?>
							<th width="1%" class="center">
								#
							</th>
							<?php } ?>
							<th width="2%" class=" center hidden-phone">
								<input type="checkbox" name="checkall-toggle" value=""
									   title="<?php echo Text::_('Jglobal $jinput,_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
							</th>
						<?php } ?>
						<th width="2%" class="hidden-phone">
							<!-- Property image -->
						</th>
						<th width="20%">
							<?php //echo HTMLHelper::_('grid.sort', Text::_('OS_PROPERTY_TITLE'), 'a.pro_name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'properties_list'); ?>

							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_PROPERTY_TITLE'), 'a.pro_name', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="10%" class="hidden-phone center">
							<?php //echo HTMLHelper::_('grid.sort', Text::_('OS_OWNER'), 'c.name', @$lists['filter_order_Dir'], @$lists['filter_order'], 'properties_list'); ?>

							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_OWNER'), 'c.name', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
						<th width="17%" class="hidden-phone center">
							<?php echo Text::_('OS_ADDRESS');?>
						</th>
						<?php
						if ($tmpl != "component") 
						{
							//if(!OSPHelper::isJoomla4())
							//{
							?>
							<th width="7%" class="hidden-phone center">
								<?php //echo HTMLHelper::_('grid.sort', Text::_('OS_PRICE'), 'a.price', @$lists['filter_order_Dir'], @$lists['filter_order'], 'properties_list'); ?>

								<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_PRICE'), 'a.price', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
							</th>
							<?php
							//}	
							?>
							<th width="8%" class="hidden-phone center">
								<?php echo Text::_('OS_INFORMATION');?>
							</th>
							<th width="10%" class="hidden-phone center">
								<?php echo Text::_('OS_STATUS');?>
							</th>
							<?php
							if (file_exists(JPATH_ROOT ."/components/com_oscalendar/oscalendar.php")) 
							{
								if ($configClass['integrate_oscalendar'] == 1) {
									?>
									<th width="5%" class="hidden-phone center">
										<?php echo Text::_('OS_ROOMS');?>
									</th>
								<?php
								}
							}
							?>
						<?php } ?>
						<th width="2%" class="hidden-phone center">
							<?php //echo HTMLHelper::_('grid.sort', Text::_('ID'), 'a.id', @$lists['filter_order_Dir'], @$lists['filter_order'], 'properties_list'); ?>

							<?php echo HTMLHelper::_('searchtools.sort',   Text::_('ID'), 'a.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
						</th>
					</tr>
                </thead>
                <tfoot>
					<tr>
						<td width="100%" colspan="22" class="center">
							<?php
							echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
                </tfoot>
                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
					<?php
					$canChange = true;
					$db = Factory::getDBO();
					$k = 0;
					if(!OSPHelper::isJoomla4())
					{
						
					}
					$parentsStr = "";
					for ($i = 0, $n = count($rows); $i < $n; $i++) 
					{
						$row = $rows[$i];

						OSPHelper::createPhotoDirectory($row->id);
						OSPHelper::movingPhoto($row->id);

						$checked = HTMLHelper::_('grid.id', $i, $row->id);
						$link = Route::_('index.php?option=com_osproperty&task=properties_edit&cid[]=' . $row->id);
						$published = HTMLHelper::_('jgrid.published', $row->published, $i, 'properties_');

						$db->setQuery("Select * from #__osrs_expired where pid = '$row->id'");
						$expireds = $db->loadObjectList();
						$expired_time = "";
						$expired_feature_time = "";
						if (count($expireds) > 0) 
						{
							$expired = $expireds[0];
							$expired_time = $expired->expired_time;
							$expired_feature_time = $expired->expired_feature_time;
						} 
						else 
						{
							OspropertyProperties::setexpired($option, $row->id);
						}
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
							<?php
							if ($tmpl != "component") 
							{
								if(!OSPHelper::isJoomla4())
								{
								?>
								<td align="center" class="center">
									<?php echo $pageNav->getRowOffset($i); ?>
								</td>
								<?php } ?>
								<td align="center" class="hidden-phone center">
									<?php echo $checked; ?>
								</td>
							<?php } ?>
							<td align="center" class="hidden-phone">
								<?php
								$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
								$photo = $db->loadObjectList();
								if (count($photo) > 0) {
									$photo = $photo[0];
									?>
									<a href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/<?php echo $photo->image?>"
									   target="_blank">
										<?php
										OSPHelper::showPropertyPhoto($photo->image, 'thumb', $row->id, 'width:75px; max-width:none !important;', 'img-polaroid', '');
										?>
									</a>
								<?php
								} else {
									?>
									<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" style="width:70px;" class="img-polaroid" />
								<?php
								}
								?>
							</td>
							
							<td align="left">
								<?php
								if ($tmpl == "component"){
								?>
								<a class="pointer"
								   onclick="if (window.parent) window.parent.jSelectUser_pro_id('<?php echo $row->id?>', '<?php echo str_replace("'", "\'", $row->pro_name);?>');">
									<?php
									}else{
									?>
									<a href="<?php echo $link?>" title="<?php echo $row->pro_name?>">
										<?php } ?>
										<?php
										if ($row->ref != "") {
											echo $row->ref . ", ";
										}
										$pro_name = $row->pro_name;
										$pro_nameArr = explode(" ", $pro_name);
										if (count($pro_nameArr) > 8) {
											for ($j = 0; $j <= 8; $j++) {
												echo $pro_nameArr[$j] . " ";
											}
											echo "...";
										} else {
											echo $pro_name;
										}
										?>
									</a>
									<BR/>
									<span class="fontsmall colorgray">
										(<?php echo Text::_('OS_ALIAS'); ?>: <?php echo $row->pro_alias; ?>)
									</span>
									<BR/>
									<span class="fontsmall" style="color:#0088CC;">
										<strong><?php echo Text::_('OS_CATEGORY') ?>: </strong>
										<?php //echo $row->category_name
										$cat_name = OSPHelper::getCategoryNamesOfProperty($row->id);
										$cat_name_arr = explode(" ", $cat_name);
										if (count($cat_name_arr) > 10) {
											$j = 0;
											foreach ($cat_name_arr as $cat) {
												$j++;
												if ($j <= 10) {
													echo $cat . " ";
												}
											}
											echo "...";
										} else {
											echo $cat_name;
										}
										?>
										<BR />
										<strong><?php echo Text::_('OS_TYPE') ?>: </strong><?php echo $row->type_name ?>
										<br/>
										<?php
										echo Text::_('OS_CREATED').": ";
										//echo HTMLHelper::_('date', $row->created , 'D, jS F Y');
										$date   = Factory::getDate($row->created, $config->get('offset'));
										echo $date->format( 'D, jS F Y');
										if(($row->modified != "") and ($row->modified != "0000-00-00") and ($row->modified != $row->created)){
											echo "<BR />";
											echo Text::_('OS_MODIFIED').": ";
											$date   = Factory::getDate($row->modified, $config->get('offset'));
											echo $date->format( 'D, jS F Y');
										}
										?>
									</span>
							</td>
							<td align="left" class="hidden-phone">
								<a href="index.php?option=com_osproperty&task=agent_edit&cid[]=<?php echo $row->agent_id;?>" title="<?php echo $row->agent_name ?>" target="_blank">
									<?php echo $row->agent_name; ?>
								</a>
								<span style='color:blue;' class='fontsmall'>
								  <small>(<?php echo OSPHelper::loadAgentType($row->agent_id);?>)</small>
								</span>
								<BR />
								<?php
								if($row->company_id > 0)
								{
									echo "<span class='small'>".Text::_('OS_COMPANY').": <a href='index.php?option=com_osproperty&task=companies_edit&cid[]=".$row->company_id."' target='_blank' title='".$row->company_name."'>".$row->company_name."</a></span>";
									if($row->company_photo != ""){
										?>
										<BR />
										<center>
										<img src="<?php echo $row->company_photo;?>" class="img-polaroid" style="width:40px;" />
										</center>
										<?php
									}
								}
								?>
							</td>
							<td align="left" class="hidden-phone fontsmall">
								<?php echo OSPHelper::generateAddress($row); ?>
							</td>
							<?php
							if ($tmpl != "component") 
							{
								//if(!OSPHelper::isJoomla4())
								//{
								?>
								<td align="center" class="hidden-phone">
									<?php
									if ($row->price_call == 0) 
									{
										if ($row->price > 0) 
										{
											?>
											<?php echo "<span class='fontsmall colorred'>" . OSPHelper::generatePrice($row->curr, $row->price) . "</span>";
											if ($row->rent_time != "") {
												echo " / " . Text::_($row->rent_time);
											}
										}
									} elseif ($row->price_call == 1) {
										echo "<font color='green'>" . Text::_('OS_CALL_FOR_PRICE') . "</font>";
									}
									?>
								</td>
								<?php //} ?>
								<td class="hidden-phone alignleft fontsmall">
									<?php echo Text::_('OS_HITS');?>: <?php echo $row->hits; ?>
									<br/>                                
									<?php echo Text::_('OS_ACCESS');?>:
									<?php
									echo OSPHelper::returnAccessLevel($row->access);
									?>
									<br/>
									<?php echo Text::_('OS_REQUEST_INFO');?>: <?php echo $row->total_request_info; ?>
									<br/>
									<?php echo Text::_('OS_REPORT_ABUSE');?>: <?php echo $row->count_report;?>
									<br/>
									<?php echo Text::_('OS_RATING');?>: <?php echo $row->rating;?>
								</td>
								<td align="center" class="hidden-phone center">
									<?php
									if(OSPHelper::isJoomla4())	
									{
									?>
										<div class="btn-group">
											<div class="btn btn-secondary">
												<?php echo $published; ?>
											</div>
											<?php
											if ($row->isFeatured == 1) 
											{
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													<span class="hasTip hasTooltip" title="<?php echo Text::_('OS_FEATURED')?>::<?php echo Text::_('Expired on')?>: <?php echo $expired_feature_time?>">
												<?php
												}
												
												?>
												<a class="btn btn-micro btn-info active hasTooltip colororange"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=isFeatured&cid[]=<?php echo $row->id?>&v=0&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
													  <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
													</svg>
												</a>
												<?php
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													</span>
												<?php
												}
												?>
											<?php
											} else {
												?>
												<a class="btn btn-micro btn-info active hasTooltip colorblack"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=isFeatured&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
													  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
													  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>
												</a>
											<?php
											}
											?>
											<?php
											if ($row->approved == 1) 
											{
												if ($configClass['general_use_expiration_management'] == 1) 
												{
													?>
													<span class="hasTip hasTooltip" title="<?php echo Text::_('Approval')?>::<?php echo Text::_('Expired on')?>: <?php echo $expired_time?>">
												<?php } ?>
													<a class="btn btn-micro btn-info active hasTooltip colororange"
													   href="index.php?option=com_osproperty&task=properties_changeType&type=approved&cid[]=<?php echo $row->id?>&v=0&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
													   title="Change approval status">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="orange" class="bi bi-check-square-fill" viewBox="0 0 16 16">
														  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
														</svg>
													</a>
												<?php
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													</span>
												<?php
												}
											} else {
												?>
												<a class="btn btn-micro btn-info active hasTooltip"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=approved&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="Change approval status">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
													  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
													  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
													</svg>
												</a>
												<?php
												if ($row->request_to_approval == 1) echo HTMLHelper::_('tooltip', Text::_('OS_REQUEST_TO_APPROVAL'));
											}
											?>
										</div>
									<?php
									}
									else
									{
									?>
										<div class="btn-group">
											<?php echo $published; ?>
											<?php
											if ($row->isFeatured == 1) {
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													<span class="hasTip hasTooltip" title="<?php echo Text::_('OS_FEATURED')?>::<?php echo Text::_('Expired on')?>: <?php echo $expired_feature_time?>">
												<?php
												}
												?>
												<a class="btn btn-micro active hasTooltip colororange"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=isFeatured&cid[]=<?php echo $row->id?>&v=0&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
													<i class="osicon-star colororange"></i>
												</a>
												<?php
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													</span>
												<?php
												}
												?>
											<?php
											} else {
												?>
												<a class="btn btn-micro active hasTooltip colorblack"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=isFeatured&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
													<i class="osicon-star"></i>
												</a>
											<?php
											}
											?>
											<?php
											if ($row->approved == 1) {
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													<span class="hasTip hasTooltip" title="<?php echo Text::_('Approval')?>::<?php echo Text::_('Expired on')?>: <?php echo $expired_time?>">
												<?php } ?>
												<a class="btn btn-micro active hasTooltip"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=approved&cid[]=<?php echo $row->id?>&v=0&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="Change approval status">
													<img style="width:14px;"
														 src="<?php echo Uri::root()?>media/com_osproperty/assets/images/tick.png">
												</a>
												<?php
												if ($configClass['general_use_expiration_management'] == 1) {
													?>
													</span>
												<?php
												}
											} else {
												?>
												<a class="btn btn-micro active hasTooltip"
												   href="index.php?option=com_osproperty&task=properties_changeType&type=approved&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>"
												   title="Change approval status">
													<img style="width:14px;"
														 src="<?php echo Uri::root()?>media/com_osproperty/assets/images/publish_x.png">
												</a>
												<?php
												if ($row->request_to_approval == 1) echo HTMLHelper::_('tooltip', Text::_('OS_REQUEST_TO_APPROVAL'));
											}
											?>
											<?php $open_print = "window.open ('index.php?option=com_osproperty&tmpl=component&task=properties_print&cid[]=$row->id', 'mywindow','menubar=0,status=0,location=0,status=0,scrollbars=1,resizable=0,toolbar=0,directories=0, width=1000,height=700');";?>
											<a class="btn btn-micro active hasTooltip" onclick="<?php echo $open_print;?>" style="cursor: pointer;">
												<i class="icon-print"></i>
											</a>
										</div>
									<?php
									}
									?>
								</td>
								<?php
								if (file_exists(JPATH_ROOT ."/components/com_oscalendar/oscalendar.php") && $configClass['integrate_oscalendar'] == 1) 
								{
									$db->setQuery("Select count(id) from #__oscalendar_rooms where pid = '$row->id'");
									$count_rooms = $db->loadResult();
									?>
									<td align="center" class="data_td center hidden-phone">
										<a href="index.php?option=com_oscalendar&task=room_manage&pid=<?php echo $row->id;?>" target="_blank" title="<?php echo Text::_('OS_MANAGE_RENTAL_INFORMATION')?>">
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/room.png">
										</a>
										(<?php echo intval($count_rooms);?>)
									</td>
								<?php
								}
							} 
							?>
							<td align="center" class="hidden-phone center">
								<?php echo $row->id ?>
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
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="properties_list">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir'];?>" />
		<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="<?php echo $lists['filter_full_ordering'];?>" />
		</form>
        <script type="text/javascript">
            jQuery( "#filter_search_button" ).click( function() {
                var open_search_from = jQuery("#open_search_from").val();
                if(open_search_from == 0){
                    jQuery('#search_param_div').slideDown('slow');
                    jQuery("#open_search_from").val("1");
                    jQuery("#filter_search_button").addClass('btn-primary');
                }else{
                    jQuery('#search_param_div').slideUp('slow');
                    jQuery("#open_search_from").val("0");
                    jQuery("#filter_search_button").removeClass('btn-primary');
                }
            });
            jQuery( "#clear_search_button" ).click( function() {
                jQuery("#company_id").val("");
                jQuery("#agent_id").val("");
                jQuery("#pro_type").val("");
                jQuery("#state_id").val("");
                jQuery("#nbed").val("");
                jQuery("#nbath").val("");
                jQuery("#nrooms").val("");
                jQuery("#isfeature").val("");
                jQuery("#request_to_approval").val("");
                jQuery("#state").val("");
                jQuery("#agentType").val("-1");
                var country_id = document.getElementById('country_id');
                if(country_id != null){
                    jQuery("#country_id").val("");
                }
                jQuery("#keyword").val("");
                document.getElementById('adminForm').submit();
            });

        </script>
		<?php
	}


	/**
	 * Edit Properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editProperty($option,$row,$lists,$amenities,$amenitylists,$groups,$neighborhoods,$translatable)
	{
		global $jinput, $mainframe,$configs,$_jversion,$configClass,$languages,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		$span3Class    = $bootstrapHelper->getClassMapping('span3');
		$span9Class    = $bootstrapHelper->getClassMapping('span9');
		$inputLargeClass = $bootstrapHelper->getClassMapping('input-large');
		$db				= Factory::getDBO();
		$document		=& Factory::getDocument();
		$editor			= Editor::getInstance(Factory::getConfig()->get('editor'));
		//HTMLHelper::_('behavior.modal','a.osmodal');
		OSPHelper::loadTooltip();
		
		if($row->id > 0)
		{
			$edit = Text::_('OS_EDIT');
		}
		else
		{
			$edit = Text::_('OS_ADD');
		}
		ToolBarHelper::title(Text::_('OS_PROPERTY').' ['.$edit.']');
		ToolBarHelper::save('properties_save');
		ToolBarHelper::save2new('properties_new');
		ToolBarHelper::apply('properties_apply');
		ToolBarHelper::cancel('properties_gotolist');

		$max_width = $configClass['max_width_size'];
		$max_height = $configClass['max_height_size'];
		?> 
		<link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/css/textext.core.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/css/textext.plugin.tags.css" type="text/css" />
		<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/js/textext.core.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/js/textext.plugin.tags.js" type="text/javascript" charset="utf-8"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.6.0/Sortable.js" type="text/javascript" charset="utf-8"></script>
		
		<?php
		if (OSPHelper::isJoomla4())
		{
		?>
			
			<style>
			.navbar-toggler
			{
				display:none !important;
			}
			</style>
		<?php
		}	
		?>
		<script type="text/javascript">
		 function loadStateBackend(country_id,state_id,city_id)
        {
			var live_site = '<?php echo Uri::root()?>';
            loadLocationInfoStateCityBackend(country_id,state_id,city_id,'country','state',live_site);
		}
		 function loadCityBackend(state_id,city_id)
        {
			var live_site = '<?php echo Uri::root()?>';
            loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
		}
		 function addPhoto()
        {
			var current_number_photo = document.getElementById('current_number_photo');
			current_number = parseInt(current_number_photo.value);
			current_number++;
			var temp = document.getElementById('div_' + current_number);
			if(temp != null)
			{
				if(temp.style.display == "none")
				{
					temp.style.display = "block";
				}
			}
			current_number_photo.value = current_number;
		}
		 function check_file(id)
        {
			str=document.getElementById(id).value.toUpperCase();
			var elementspan = document.getElementById(id + 'div');
			//suffix=".JPG";
			blnValid = false;
			var _validFileExtensions = [".jpg", ".jpeg", ".png", ".gif", ".webp"];
			for (var j = 0; j < _validFileExtensions.length; j++)
			{
                var sCurExtension = _validFileExtensions[j];
                if (str.substr(str.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase())
                {
                    blnValid = true;
                    break;
                }
            }
			if(!blnValid)
			{
				alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.jpg, *.jpeg, *.gif, *.png, *.webp');
				document.getElementById(id).value='';
				if(elementspan != null)
				{
					elementspan.innerHTML = elementspan.innerHTML;
				}
			}
			else
			    {
				//clientWidth,clientHeight;
				clientWidth = document.getElementById(id).clientWidth;
				clientHeight = document.getElementById(id).clientHeight;
				<?php
				if((intval($max_width) > 0) and (intval($max_height) > 0))
				{
					?>
					var max_width = <?php echo $max_width?>;
					max_width = parseInt(max_width);
					var max_height = <?php echo $max_height?>;
					max_height = parseInt(max_height);
					if((clientWidth > max_width) || (clientHeight > max_height))
					{
						alert("<?php echo Text::_('OS_YOUR_PHOTO_IS_OVER_LIMIT_SIZE')?>");
						document.getElementById(id).value='';
						if(elementspan != null)
						{
							elementspan.innerHTML = elementspan.innerHTML;
						}
					}
					<?php
				}
				?>
			}
		}
		
		Joomla.submitbutton =  function(task) {
			var form = document.adminForm;
			category_name = form.category_name;
			if((task == "properties_save") || (task == "properties_apply"))
			{
				var temp1,temp2;
				var cansubmit = 1;
				var require_field = document.getElementById('require_field');
				require_field = require_field.value;
				var require_label = document.getElementById('require_label');
				require_label = require_label.value;
				var require_fieldArr = require_field.split(",");
				var require_labelArr = require_label.split(",");
				for(i=0;i<require_fieldArr.length;i++)
				{
					temp1 = require_fieldArr[i];
					if(temp1 == "category_id")
					{
						if (jQuery('#categoryIds option:selected').length == 0)
						{
							alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
							jQuery('#categoryIds').focus();
							cansubmit = 0;
							return false;
						}
					}
					temp2 = document.getElementById(temp1);
					if(temp2 != null)
					{
						if(temp2.value == "")
						{
							if(temp1 == "state")
							{
								temp3 = document.getElementById('nstate');
								if(temp3 != null)
								{
									if(temp3.value == "")
									{
										alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
										temp2.focus();
										cansubmit = 0;
										return false;
									}
								}
							}
							else
							{
								alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
								temp2.focus();
								cansubmit = 0;
								return false;
							}
						}
					}
					else
					{
						temp2 = document.getElementsByName(temp1);
						if(temp2.length > 0)
						{
							cansubmit = 0;
							for(var j=0; j < temp2.length; j++)
							{
								if(temp2[j].checked == true)
								{
									cansubmit = 1;
								}
							}
							if(cansubmit == 0)
							{
								alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
								temp2.focus();
								cansubmit = 0;
								return false;
							}
						}
						else
						{
							temp2 = document.getElementsByName(temp1 + "[]");
							if(temp2.length > 0){
								cansubmit = 0;
								for(var j=0; j < temp2.length; j++)
								{
									if(temp2[j].checked == true)
									{
										cansubmit = 1;
									}
								}
								if(cansubmit == 0)
								{
									alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
									temp2.focus();
									cansubmit = 0;
									return false;
								}
							}
						}
					}
				}

				var pro_type = document.getElementById('pro_type').value;
				if(pro_type != "")
				{
					var require_field = document.getElementById('type_id_' + pro_type + '_required_name');
					require_field = require_field.value;
					if(require_field != "")
					{
						var require_label = document.getElementById('type_id_' + pro_type + '_required_title');
						require_label = require_label.value;
						var require_fieldArr = require_field.split(",");
						var require_labelArr = require_label.split(",");
						for(i=0;i<require_fieldArr.length;i++)
						{
							temp1 = require_fieldArr[i];
							temp2 = document.getElementById(temp1);
							if(temp2 != null)
							{
								if(temp2.value == "")
								{
									alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
									temp2.focus();
									cansubmit = 0;
									return false;
								}
							}
							else
							{
								temp2 = document.getElementsByName(temp1);
								if(temp2.length > 0){
									cansubmit = 0;
									for(var j=0; j < temp2.length; j++)
									{
										if(temp2[j].checked == true)
										{
											cansubmit = 1;
										}
									}
									if(cansubmit == 0)
									{
										alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
										temp2.focus();
										cansubmit = 0;
										return false;
									}
								}
								else
								{
									temp2 = document.getElementsByName(temp1 + "[]");
									if(temp2.length > 0)
									{
										cansubmit = 0;
										for(var j=0; j < temp2.length; j++)
										{
											if(temp2[j].checked == true)
											{
												cansubmit = 1;
											}
										}
										if(cansubmit == 0)
										{
											alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY')?>");
											temp2.focus();
											cansubmit = 0;
											return false;
										}
									}
								}
							}
						}
					}
				}

				if(cansubmit == 1)
				{
					Joomla.submitform(task);
				}
			}
			else
			{
				Joomla.submitform(task);
			}
		}
		 function showPriceFields()
        {
			var price_call = document.getElementById('price_call');
			var pricediv   = document.getElementById('pricediv');
			if(price_call.value == 0)
			{
				pricediv.style.display = "block";
			}
			else
			    {
				pricediv.style.display = "none";
			}
		}
		
		 function check_file_type()
        {
			str=document.getElementById('zip_file').value.toUpperCase();
			suffix=".ZIP";
			if(!(str.indexOf(suffix, str.length - suffix.length) !== -1))
			{
				alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.zip');
				document.getElementById('zip_file').value='';
			}
		}
		</script>
		<?php
		if (version_compare(JVERSION, '3.5', 'ge') && !OSPHelper::isJoomla4())
		{
		?>
			<script src="<?php echo Uri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
			<script src="<?php echo Uri::root()?>media/system/js/modal.js" type="text/javascript"></script>
		<?php } ?>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
		
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'propertytranslation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'propertytranslation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'property', array('active' => 'basic-information-page')); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'basic-information-page', Text::_('OS_BASIC_INFORMATION', true)); ?>
			<table width="100%">
				<tr>
					<td width="50%" valign="top">
						<table  width="100%">
							<tr>
								<!-- General tab-->
								<td width="100%" valign="top" align="left">
									<div class="col width-100">
										<fieldset class="general form-horizontal options-form">
											<legend><?php echo Text::_( 'OS_GENERAL' ); ?></legend>
											<table  width="100%" class="admintable">
												<tr>
													<td class="key" width="20%">
														<?php echo Text::_('Ref #')?>
													</td>
													<td width="80%">
														<input type="text" name="ref" id="ref" value="<?php echo $row->ref?>" class="input-small form-control imedium" />
													</td>
												</tr>
												<?php
												$require_field = "";
												$require_field .= "pro_name,";
												$require_label = "";
												$require_label .= Text::_('OS_PROPERTY_NAME').",";
												?>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_PROPERTY_TITLE')?><span class="required">(*)</span>
													</td>
													<td>
														<input type="text" name="pro_name" id="pro_name" value="<?php echo htmlspecialchars($row->pro_name);?>" size="50" class="ilarge input-large form-control required" /> 
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_ALIAS')?>
													</td>
													<td>
														<input type="text" name="pro_alias" id="pro_alias" value="<?php echo $row->pro_alias?>" size="50" class="input-large ilarge form-control" />
													</td>
												</tr>
												<?php
												$require_field .= "category_id,";
												$require_label .= Text::_('OS_CATEGORY').",";
												?>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_CATEGORY')?><span class="required">(*)</span>
													</td>
													<td>
														<?php echo $lists['category']; ?> 
													</td>
												</tr>
												<?php
												$require_field .= "pro_type,";
												$require_label .= Text::_('OS_PROPERTY_TYPE').",";
												?>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_PROPERTY_TYPE')?><span class="required">(*)</span>
													</td>
													<td>
														<?php echo $lists['type']; ?>
														
													</td>
												</tr>
												<?php if($configClass['active_market_status'] == 1){ ?>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_MARKET_STATUS')?>
													</td>
													<td>
														<?php echo OSPHelper::buildDropdownMarketStatus($row->isSold); ?>
													</td>
												</tr>
												<?php } ?>
												<tr>
													<td class="key" valign="top">
														<?php echo Text::_("OS_PRICE_INFO")?>
													</td>
													<td>
														<div>
															<table width="100%">
																<tr>
																	<td class="key" width="20%">
																		<?php echo Text::_("OS_CALL_FOR_PRICE")?>
																	</td>
																	<td width="80%">
																		<?php
																		echo $lists['price_call'];
																		?>
																	</td>
																</tr>
															</table>
														</div>
														<div class="clearfix"></div>
														<?php
														if((int)$row->price_call == 0){
															$display = "block";
														}else{
															$display = "none";
														}
														if($row->id == 0){
															//$display = "none";
														}
														?>
														<div id="pricediv" style="display:<?php echo $display;?>;">
															<table width="100%">
																<tr>
																	<td class="key" width="20%">
																		<?php echo Text::_("OS_PRICE")?>
																	</td>
																	<td width="80%">
																		<input type="text" name="price" id="price" value="<?php echo $row->price?>" size="10" class="input-small form-control" />
																		<?php HelperOspropertyCommon::showCurrencySelectList($row->curr);?>
																	</td>
																</tr>
																<tr>
																	<td class="key">
																		<?php echo Text::_("OS_PRICE_FOR")?>
																	</td>
																	<td>
																		<?php echo $lists['time'];?>
																	</td>
																</tr>
																<tr>
																	<td class="key">
																		<?php
																		echo HelperOspropertyCommon::showLabel('price_text',Text::_("OS_PRICE_TEXT"),Text::_("OS_PRICE_TEXT_EXPLAIN"));
																		?>
																	</td>
																	<td>
																		<input type="text" class="input-large form-control" name="price_text" value="<?php echo $row->price_text;?>" />
																	</td>
																</tr>
															</table>
														</div>
													</td>
												</tr>
											</table>
										</fieldset>
									</div>
									<!-- End General tab-->
								</td>
							</tr>
							<tr>
							<!-- Other information -->
								<td width="100%">
									<div class="col width-100">
									<fieldset class="general form-horizontal options-form">
										<legend><?php echo Text::_( 'OS_TAGS' ); ?></legend>
										<table  width="100%" class="admintable">
											<tr>
												<td>
													<table width="100%" id="property_tag_table">
														<tr>
															<th>
																<?php echo Text::_('OS_KEYWORD')?>
															</th>
															<?php 
															if($translatable){
																foreach ($languages as $language)
																{												
																	$sef = $language->sef;
																	?>
																	<th>
																		<?php echo Text::_('OS_KEYWORD')?>
																		<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />
																	</th>
																	<?php 
																}
															}
															?>
															<th>
																&nbsp;
															</th>
														</tr>
														<?php 
														if(count((array)$lists['tags']) > 0)
														{
															foreach ($lists['tags'] as $tag)
															{
															?>
															<tr id="tag_table_tr">
																<td>
																	<input type="text" name="keyword[]" value="<?php echo $tag->keyword?>" class="input-small form-control ishort" />
																</td>
																<?php 
																if($translatable){
																	foreach ($languages as $language)
																	{												
																		$sef = $language->sef;
																		?>
																		<td>
																			<input type="text" name="keyword_<?php echo $sef;?>[]" value="<?php echo stripslashes($tag->{'keyword_'.$sef});?>" class="input-small form-control ishort" />
																		</td>
																		<?php 
																	}
																}
																?>
																<td>
																	<input type="button" class="btn removetag btn-secondary" value="<?php echo Text::_('OS_DELETE');?>" />
																</td>
															</tr>
															<?php 
															}
														}
														?>
														<tr id="tag_table_tr">
															<td>
																<input type="text" name="keyword[]" value="" class="ishort input-small form-control" />
															</td>
															<?php 
															if($translatable)
															{
																foreach ($languages as $language)
																{												
																	$sef = $language->sef;
																	?>
																	<td>
																		<input type="text" name="keyword_<?php echo $sef;?>[]" value="" class="input-small form-control ishort" />
																	</td>
																	<?php 
																}
															}
															?>
															<td>
																<input type="button" class="btn addtag btn-secondary" value="<?php echo Text::_('OS_ADD');?>" />
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</fieldset>
									<script type="text/javascript">
									jQuery(document).ready( function(){		
										jQuery('#property_tag_table').on('click', '.removetag', function(){
											jQuery(this).parent().parent().remove();
										});
										
										jQuery('#property_tag_table').on('click', '.addtag', function(){
											jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
											jQuery(this).attr('class','btn removetag btn-secondary');
											<?php 
											$value  = '<tr id="tag_table_tr"><td><input type="text" name="keyword[]" value="" class="input-small form-control" /></td>';
											//$value .= '<td><input type="text" name="history_event[]" value="" class="input-medium form-control" /></td>';
											if($translatable){
												foreach ($languages as $language)
												{												
													$sef = $language->sef;
													$value .= '<td><input type="text" name="keyword_'.$sef.'[]" value="" class="input-small form-control" /></td>';
												}
											}
											$value .= '<td><input type="button" class="btn addtag btn-secondary" value="'.Text::_('OS_ADD').'" /></td></tr>';
											?>
											var appendTxt = '<?php echo $value;?>';
											jQuery("#property_tag_table > tbody> tr:last").after(appendTxt);
										}); 
									});
									</script>
									</div>	
								</td>
								<!-- End Other information -->
							</tr>
						</table>
					</td>
					<td width="50%" valign="top">
						<table>
							<tr>
								<!-- Address -->
								<td width="100%">
									<div class="col width-100">
										<fieldset class="general form-horizontal options-form">
											<legend><?php echo Text::_( 'OS_ADDRESS' ); ?></legend>
											<table  width="100%" class="admintable">
												<tr>
													<td class="key" width="20%">
														<?php echo HelperOspropertyCommon::showLabel('show_address',Text::_('OS_SHOW_ADDRESS'),Text::_('OS_SHOW_ADDRESS_EXPLAIN'));?>
													</td>
													<td width="80%">
														<div id="div_states">
															<?php
															echo $lists['show_address'];
															?>
														</div>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_ADDRESS")?>
														<?php
														if($configClass['address_required'])
														{
															$require_field .= "address,";
															$require_label .= Text::_('OS_ADDRESS').",";
															?>
															<span class="required">(*)</span>
															<?php
														}
														?>
													</td>
													<td width="80%">
														<input type="text" name="address" id="address" value="<?php echo htmlspecialchars($row->address);?>" class="input-large ilarge form-control" size="30" />
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_POSTCODE")?>
														<?php
														if($configClass['require_postcode']==1){
															?>
															<span class="required">(*)</span>
															<?php
															$require_field .= "postcode,";
															$require_label .= Text::_('OS_POSTCODE').",";
														}
														?>
													</td>
													<td>
														<input type="text" name="postcode" id="postcode" value="<?php echo $row->postcode?>" size="10" class="input-large form-control ilarge" />
													</td>
												</tr>

												<?php
												if(HelperOspropertyCommon::checkCountry()){
													?>
													<tr>
														<td class="key">
															<?php echo Text::_("OS_COUNTRY")?>
															<span class="required">(*)</span>
															<?php
															$require_field .= "country,";
															$require_label .= Text::_('OS_COUNTRY').",";
															?>
														</td>
														<td>
															<?php
															echo $lists['country'];
															?>
														</td>
													</tr>
													<?php
												}else{
													echo $lists['country'];
												}
												?>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_STATE")?>
														<?php
														if($configClass['require_state']==1){
															?>
															<span class="required">(*)</span>
															<?php
															$require_field .= "state,";
															$require_label .= Text::_('OS_STATE').",";
														}
														?>
													</td>
													<td>
														<div id="country_state">
															<?php
															echo $lists['states'];
															?>
														</div>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_CITY")?>
													</td>
													<td>
														<!--<input type="text" name="city" id="city" value="<?php echo $row->city?>" size="20">-->
														<div id="city_div">
															<?php
															echo $lists['city'];
															?>
														</div>
														<?php
														if($configClass['require_city']==1){
															?>
															<!--<span class="required">(*)</span>-->
															<?php
															//$require_field .= "city,";
															//$require_label .= Text::_('OS_CITY').",";
														}
														?>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_REGION")?>
													</td>
													<td>
														<input type="text" name="region" id="region" value="<?php echo $row->region?>" size="30" class="input-large form-control ilarge" />

													</td>
												</tr>
												<tr>
													<td class="key">

														<?php echo Text::_("OS_LATITUDE")?>
													</td>
													<td>
														<input type="text" class="input-small form-control imedium" name="lat_add" id="lat_add" value="<?php echo $row->lat_add?>" size="30"><!--<span class="required">(*)</span> -->

													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_("OS_LONGTITUDE")?>
													</td>
													<td>
														<input type="text" class="input-small form-control imedium" name="long_add" id="long_add" value="<?php echo $row->long_add?>" size="30">
														<!--<span class="required">(*)</span>-->

													</td>
												</tr>
												<tr>
													<td class="key" valign="top" colspan="2">
														<?php echo Text::_('Drag and drop the map for coordinates')?>:
														<BR />
														<?php
                                                        if($row->lat_add == ""){
                                                            $row->lat_add = $configClass['goole_default_lat'];
                                                        }
                                                        if($row->long_add == ""){
                                                            $row->long_add = $configClass['goole_default_long'];
                                                        }
                                                        $geocode = array();
														$tmp	= new \stdClass();
														$tmp->lat = $row->lat_add;
														$tmp->long = $row->long_add;
														$geocode[0]	= $tmp;
                                                        if($configClass['map_type'] == 0)
                                                        {
                                                            require_once (JPATH_ROOT.DS."components/com_osproperty/helpers/googlemap.lib.php");
                                                            HelperOspropertyGoogleMap::loadGMapinEditProperty($geocode,"map","lat_add","long_add");
                                                            ?>
                                                            
                                                            <div id="map" style="width: 500px; height: 300px;border:1px solid #CCC;"></div>
                                                            
                                                            <div class="clearfix"></div>
                                                            <div>
                                                                <b><?php echo Text::_('Enter address to check lattitude and longtitude: ')?></b>
                                                                <BR>
                                                                <input type="text" name="add" id="add" value="" size="20" class="inputbox form-control"><input type="button" class="btn btn-primary" value="<?php echo Text::_("Search")?>" onclick="javascript:showAddress(document.adminForm.add.value);">
                                                            </div>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            include(JPATH_ROOT.DS."components/com_osproperty/helpers/openstreetmap.lib.php");
                                                            HelperOspropertyOpenStreetMap::loadGMapinEditProperty($geocode,"map","lat_add","long_add");
                                                            ?>
                                                            <div id="map" style="width: 500px; height: 300px;border:1px solid #CCC;"></div>
                                                            <?php
                                                        }
														?>
													</td>
												</tr>
											</table>
										</fieldset>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<!-- Other information -->
					<td width="100%" colspan="2">
						<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_DESCRIPTION' ); ?></legend>
							<table  width="100%" class="admintable">
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_SHORT_DESCRIPTION')?>
										<?php
										if($configClass['short_desc_required']){
										?>
											<span class="required">(*)</span>
											<?php
											$require_field .= "pro_small_desc,";
											$require_label .= Text::_('OS_SMALL_DESCRIPTION').",";
										}
										?>
									</td>
									<td width="80%">
										<textarea name="pro_small_desc" id="pro_small_desc" style="width:450px !important;" rows="5" class="input-large form-control"><?php echo stripslashes($row->pro_small_desc)?></textarea>
									</td>
								</tr>
								<?php
                                $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
								?>
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_FULL_DESCRIPTION')?>
									</td>
									<td width="80%">
										<?php
										echo $editor->display( 'pro_full_desc',  stripslashes($row->pro_full_desc) , '95%', '250', '75', '20' ,false) ;
										?>
									</td>
								</tr>							
							</table>
						</fieldset>
						</div>	
					</td>
					<!-- End Other information -->
				</tr>
			</table>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'details-page', Text::_('OS_DETAILS', true)); ?>
			<table width="100%">
			<tr>
                <td width="100%">
                    <div class="col width-100">
                    <fieldset class="general form-horizontal options-form">
                        <legend><?php echo Text::_( 'OS_PROPERTY_INFORMATION' ); ?></legend>
                        <table  width="100%" class="admintable">
                            <tr>
                                <td class="key" valign="top">
                                    <?php echo Text::_('OS_VIDEO_EMBED_CODE')?>
                                </td>
                                <td width="80%">
                                    <textarea name="pro_video" id="pro_video" cols="50" rows="3" class="inputbox form-control" style="width:300px !important;"><?php echo stripslashes($row->pro_video);?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo Text::_('OS_DOCUMENT_LINK')?>
                                </td>
                                <td>
                                    <input type="text" name="pro_pdf" id="pro_pdf" size="50" class="input-xlarge form-control ilarge" value="<?php echo $row->pro_pdf;?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="key" style="vertical-align:top; padding-top:10px;">
                                    <?php echo Text::_('OS_UPLOAD_DOCUMENT')?>
									<BR />
									<?php echo Text::_('OS_DOCUMENTS_ALLOWED');?>
                                </td>
                                <td>
                                    <?php
									$limit_pdfs = ($configClass['limit_upload_pdfs'] > 0) ? $configClass['limit_upload_pdfs']:10;
									$activate_pdfs = 0;
									for($f = 0; $f < $limit_pdfs ; $f++)
									{
										if($f == 0)
										{
											$fname = "";
										}
										else
										{
											$fname = $f;
										}
										$name = "pro_pdf_file".$fname;

										if($row->{$name} != "" && file_exists(JPATH_ROOT.'/media/com_osproperty/document/'.$row->{$name}))
										{
											$activate_pdfs++;
											$fileUrl = Uri::root().'media/com_osproperty/document/'.$row->{$name};
											?>
											<a href="<?php echo $fileUrl;?>" target="_blank" title="<?php echo Text::_('OS_VIEW_DOCUMENT')?>"><?php echo $row->{$name}; ?></a>

											<BR />
											<input type="checkbox" name="remove_pdf<?php echo $fname; ?>" id="remove_pdf<?php echo $fname; ?>" onclick="javascript:changeValue('remove_pdf<?php echo $fname; ?>')" value="0" /> <strong><?php echo Text::_('OS_REMOVE_DOCUMENT');?></strong>
											<BR />
											<?php
										}
										?>
										<input type="file" name="<?php echo $name;?>" id="<?php echo $name;?>" size="40" class="inputbox form-control ilarge" onchange="javascript:checkUploadDocumentFiles('<?php echo $name;?>')"> 
										<BR />
									<?php
									}
									
									?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key" >
                                    <?php echo Text::_('OS_PANORAMA');?>
                                </td>
                                <td>
                                    <?php
                                    if($row->panorama != ""){
                                        ?>
                                        <a href="<?php echo Uri::root()?>images/osproperty/properties/panorama/<?php echo $row->panorama?>" target="_blank" title="<?php echo Text::_('OS_VIEW_DOCUMENT')?>"><?php echo $row->panorama?></a>
                                        <BR />
                                        <input type="checkbox" name="remove_panorama" id="remove_panorama" onclick="javascript:changeValue('remove_panorama')" value="0"> <b><?php echo Text::_('OS_REMOVE_PANORAMA_PHOTO');?></b>
                                        <BR />
                                        <?php
                                    }
                                    ?>
                                    <input type="file" name="panorama" id="panorama" size="40" class="inputbox form-control ilarge" onchange="javacript:check_file('panorama');"/>
                                </td>
                            </tr>
                        </table>

                        <?php
						echo HTMLHelper::_('bootstrap.startAccordion', 'menu-pane3', array('active' => 'base_fields'));
						echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BASE_PROPERTY_FIELDS'), 'base_fields');
                        ?>
                        <table  width="100%" class="admintable">
                            <?php
                            if($configClass['use_rooms']== 1){
                            ?>
                            <tr>
                                <td class="key">
                                    # <?php echo Text::_('OS_NUMBER_ROOMS')?>
                                </td>
                                <td width="80%">
                                    <?php echo $lists['nrooms'];?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <?php
                            if($configClass['use_bathrooms']== 1){
                            ?>
                            <tr>
                                <td class="key">
                                    # <?php echo Text::_('OS_NUMBER_BATHROOMS')?>
                                </td>
                                <td>
                                    <?php echo $lists['nbath'];?>
                                </td>
                            </tr>
                            <?php
                            }
							if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
							{
								$bathInfor = $lists['bathInfor'];
                            ?>
                            <tr>
                                <td class="key" style="vertical-align:top">
                                    # <?php echo Text::_('OS_BATHROOM_INFORMATION')?>
                                </td>
                                <td style="align:left;">
									<table width="40%" class="admintable">
										<tr>
											<td class="key">
												<?php echo Text::_('OS_FULL');?>
											</td>
											<TD>
												<input type="text" name="os_full" class="input-small form-control" value="<?php echo OSPHelper::showSquare($bathInfor['OS_FULL']);?>"/>
											</TD>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_THREE_QUARTER');?>
											</td>
											<TD>
												<input type="text" name="os_three_quarter" class="input-small form-control" value="<?php echo OSPHelper::showSquare($bathInfor['OS_THREE_QUARTER']);?>"/>
											</TD>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_HALF');?>
											</td>
											<TD>
												<input type="text" name="os_half" class="input-small form-control" value="<?php echo OSPHelper::showSquare($bathInfor['OS_HALF']);?>"/>
											</TD>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_QUARTER');?>
											</td>
											<TD>
												<input type="text" name="os_quarter" class="input-small form-control" value="<?php echo OSPHelper::showSquare($bathInfor['OS_QUARTER']);?>"/>
											</TD>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_ENSUITE');?>
											</td>
											<TD>
												<input type="text" name="os_ensuite" class="input-small form-control" value="<?php echo OSPHelper::showSquare($bathInfor['OS_ENSUITE']);?>"/>
											</TD>
										</tr>
									</table>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <?php
                            if($configClass['use_bedrooms']== 1){
                            ?>
                            <tr>
                                <td class="key">
                                    # <?php echo Text::_('OS_NUMBER_BEDROOMS')?>
                                </td>
                                <td>
                                    <?php echo $lists['nbed'];?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td class="key">
                                    # <?php echo OSPHelper::showSquareLabels();?>(<?php echo OSPHelper::showSquareSymbol();?>)
                                </td>
                                <td width="80%">
                                    <input type="text" name="square_feet" id="square_feet" size="10" class="input-small form-control" value="<?php echo $row->square_feet?>"/>
                                </td>
                            </tr>
                        </table>

                        <?php
						
						echo HTMLHelper::_('bootstrap.endSlide');
						
                        if($configClass['use_parking']== 1)
						{
							
							echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_PARKING'), 'parking_fields');
                        ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_GARAGE_DESCRIPTION')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="garage_description" id="garage_description" size="20" class="input-large form-control" value="<?php echo $row->garage_description;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        # <?php echo Text::_('OS_PARKING')?>
                                    </td>
                                    <td>
                                        <input type="text" name="parking" id="parking" size="20" class="input-small form-control" value="<?php echo $row->parking; ?>" />
                                    </td>
                                </tr>
                            </table>
                        <?php
							
							echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        
                        if($configClass['use_nfloors']== 1)
						{

							echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BUILDING_INFORMATION'), 'building_info');
							
                        ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_YEAR_BUILT')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="built_on" id="built_on" size="20" class="input-small form-control" value="<?php echo $row->built_on; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_YEAR_REMODELED')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="remodeled_on" id="remodeled_on" size="20" class="input-small form-control" value="<?php echo $row->remodeled_on; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_HOUSE_STYLE')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="house_style" id="house_style" size="20" class="input-large form-control" value="<?php echo $row->house_style; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_HOUSE_CONSTRUCTION')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="house_construction" id="house_construction" size="20" class="input-large form-control" value="<?php echo $row->house_construction; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_EXTERIOR_FINISH')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="exterior_finish" id="exterior_finish" size="20" class="input-large form-control" value="<?php echo $row->exterior_finish; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_ROOF')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="roof" id="roof" size="20" class="input-large form-control" value="<?php echo $row->roof; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        # <?php echo Text::_('OS_NUMBER_OF_FLOORS')?>
                                    </td>
                                    <td>
                                        <?php echo $lists['nfloors'];?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FLOORING')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="flooring" id="flooring" size="20" class="input-large form-control" value="<?php echo $row->flooring; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_LOWER'); ?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="floor_area_lower" id="floor_area_lower" size="20" class="input-large form-control" value="<?php echo $row->floor_area_lower; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_MAIN_LEVEL'); ?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="floor_area_main_level" id="floor_area_main_level" size="20" class="input-large form-control" value="<?php echo $row->floor_area_main_level; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_UPPER'); ?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="floor_area_upper" id="floor_area_upper" size="20" class="input-large form-control" value="<?php echo $row->floor_area_upper; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_TOTAL'); ?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="floor_area_total" id="floor_area_total" size="20" class="input-large form-control" value="<?php echo $row->floor_area_total; ?>" />
                                    </td>
                                </tr>
                            </table>
                        <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        ?>
                        <?php
                        if($configClass['basement_foundation']== 1){

								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BASEMENT_FOUNDATION'), 'basement_foundation');

                        ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_BASEMENT_FOUNDATION')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="basement_foundation" id="basement_foundation" size="20" class="input-large form-control" value="<?php echo $row->basement_foundation;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        # <?php echo Text::_('OS_BASEMENT_SIZE')?>(<?php echo OSPHelper::showSquareSymbol();?>)
                                    </td>
                                    <td>
                                        <input type="text" name="basement_size" id="basement_size" size="20" class="input-small form-control" value="<?php echo $row->basement_size; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_PERCENT_FINISH')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="percent_finished" id="percent_finished" size="20" class="input-large form-control" value="<?php echo $row->percent_finished;?>" />
                                    </td>
                                </tr>
                            </table>
                        <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        ?>
                        <?php

                        if($configClass['use_squarefeet']== 1){
							
								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_LAND_INFORMATION'), 'land_info');
							
                        ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_SUBDIVISION')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="subdivision" id="subdivision" size="20" class="input-large form-control" value="<?php echo $row->subdivision;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_LAND_HOLDING_TYPE')?>
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="land_holding_type" id="land_holding_type" size="20" class="input-large form-control" value="<?php echo $row->land_holding_type;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key" style="height:200px; vertical-align:top;padding-top:10px;">
                                        <?php echo Text::_('OS_LIVING_AREAS')?>
                                    </td>
                                    <td style="vertical-align:top;padding-top:5px;">
                                        <input type="text" name="living_areas" id="living_areas" class="input-large form-control" value="<?php echo $row->living_areas;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        # <?php echo Text::_('OS_LOT_SIZE');?>(<?php echo OSPHelper::showSquareSymbol();?>)
                                    </td>
                                    <td width="80%">
                                        <input type="text" name="lot_size" id="lot_size" size="10" class="input-small form-control" value="<?php echo $row->lot_size;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        # <?php echo Text::_('OS_TOTAL_ACRES');?>
                                    </td>
                                    <td>
                                        <input type="text" name="total_acres" id="total_acres" size="10" class="input-small form-control" value="<?php echo $row->total_acres?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_LOT_DIMENSIONS');?>
                                    </td>
                                    <td>
                                        <input type="text" name="lot_dimensions" id="lot_dimensions" size="10" class="input-medium form-control" value="<?php echo $row->lot_dimensions?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_FRONTPAGE');?>
                                    </td>
                                    <td>
                                        <input type="text" name="frontpage" id="frontpage" size="10" class="input-medium form-control" value="<?php echo $row->frontpage;?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key">
                                        <?php echo Text::_('OS_DEPTH');?>
                                    </td>
                                    <td>
                                        <input type="text" name="depth" id="depth" size="10" class="input-medium form-control" value="<?php echo $row->depth;?>" />
                                    </td>
                                </tr>
                            </table>
                            <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }

                        if($configClass['use_business'] == 1){
							
								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BUSINESS_INFORMATION'), 'business_info');
							
                            ?>
                            <table  width="100%" class="admintable">
                                <?php
                                $businessArr = array('takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities');
                                foreach($businessArr as $business){
                                ?>
                                    <tr>
                                        <td class="key" >
                                            <?php echo Text::_("OS_".strtoupper($business))?>
                                        </td>
                                        <td width="80%">
                                            <input type="text" class="input-large form-control" name="<?php echo $business;?>" id="<?php echo $business;?>" value="<?php echo $row->{$business};?>">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }

                        if($configClass['use_rural'] == 1){
							
								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_RURAL_INFORMATION'), 'rural_info');
							
                            ?>
                            <table  width="100%" class="admintable">
                                <?php
                                $businessArr = array('fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
                                foreach($businessArr as $business){
                                ?>
                                    <tr>
                                        <td class="key" >
                                            <?php echo Text::_("OS_".strtoupper($business))?>
                                        </td>
                                        <td width="80%">
                                            <input type="text" class="input-large form-control" name="<?php echo $business;?>" id="<?php echo $business;?>" value="<?php echo $row->{$business};?>">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        ?>
                        <?php
                        if($configClass['energy'] == 1){
							
								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_ENERGY_AND_CLIMATE'), 'energy_and_climate');
							
                            ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key" >
                                        <?php echo Text::_('OS_ENERGY')?>
                                    </td>
                                    <td>
                                        <?php
                                        echo OSPHelper::getDpeClassDropdownlist(0,$row->e_class);
                                        ?>
                                        &nbsp;
                                        <input type="text" class="input-mini form-control imini" name="energy" id="energy" size="5" value="<?php echo $row->energy;?>"> kWH/m
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key" >
                                        <?php echo Text::_('OS_CLIMATE')?>
                                    </td>
                                    <td>
                                        <?php
                                        echo OSPHelper::getDpeClassDropdownlist(1,$row->c_class);
                                        ?>
                                        &nbsp;
                                        <input type="text" class="input-mini form-control imini" name="climate" id="climate" size="5" value="<?php echo $row->climate;?>"> kg/m

                                    </td>
                                </tr>
                            </table>
                        <?php
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        if($configClass['use_property_history'] == 1)
                        {
							
								echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_PROPERTY_HISTORY_TAX'), 'history_and_tax');
							
                            ?>
                            <table  width="100%" class="admintable">
                                <tr>
                                    <td class="key" valign="top">
                                        <?php echo Text::_('OS_PROPERTY_HISTORY')?>
                                    </td>
                                    <td>
                                        <table width="100%" id="property_history_table">
                                            <tr>
                                                <th>
                                                    <?php echo Text::_('OS_DATE')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_EVENT')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_PRICE')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_SOURCE')?>
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                            <?php
                                            if(count((array)$lists['history']) > 0){
                                                foreach ($lists['history'] as $his){
                                                ?>
                                                <tr id="history_table_tr">
                                                    <td>
                                                        <input type="text" name="history_date[]" value="<?php echo $his->date?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="history_event[]" value="<?php echo $his->event?>" class="input-medium form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="history_price[]" value="<?php echo $his->price?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="history_source[]" value="<?php echo $his->source?>" class="input-medium form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="button" class="btn removehistory" value="<?php echo Text::_('OS_DELETE');?>" />
                                                    </td>
                                                </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                            <tr id="history_table_tr">
                                                <td>
                                                    <input type="text" name="history_date[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="history_event[]" value="" class="input-medium form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="history_price[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="history_source[]" value="" class="input-medium form-control" />
                                                </td>
                                                <td>
                                                    <input type="button" class="btn addhistory" value="<?php echo Text::_('OS_ADD');?>" />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="key" valign="top">
                                        <?php echo Text::_('OS_PROPERTY_TAX')?>
                                    </td>
                                    <td>
                                        <table width="100%" id="property_tax_table">
                                            <tr>
                                                <th>
                                                    <?php echo Text::_('OS_YEAR')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_TAX')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_TAX_CHANGE')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_TAX_ASSESSMENT')?>
                                                </th>
                                                <th>
                                                    <?php echo Text::_('OS_TAX_ASSESSMENT_CHANGE')?>
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                            <?php
                                            if(count((array)$lists['tax']) > 0){
                                                foreach ($lists['tax'] as $tax){
                                                ?>
                                                <tr id="tax_table_tr">
                                                    <td>
                                                        <input type="text" name="tax_year[]" value="<?php echo $tax->tax_year?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax_value[]" value="<?php echo $tax->property_tax?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax_change[]" value="<?php echo $tax->tax_change?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax_assessment[]" value="<?php echo $tax->tax_assessment?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="text" name="tax_assessment_change[]" value="<?php echo $tax->tax_assessment_change?>" class="input-small form-control" />
                                                    </td>
                                                    <td>
                                                        <input type="button" class="btn removetax" value="<?php echo Text::_('OS_DELETE');?>" />
                                                    </td>
                                                </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                            <tr id="tax_table_tr">
                                                <td>
                                                    <input type="text" name="tax_year[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="tax_value[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="tax_change[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="tax_assessment[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="text" name="tax_assessment_change[]" value="" class="input-small form-control" />
                                                </td>
                                                <td>
                                                    <input type="button" class="btn addtax" value="<?php echo Text::_('OS_ADD');?>" />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <script type="text/javascript">
                            jQuery(document).ready( function(){
                                jQuery('#property_history_table').on('click', '.removehistory', function(){
                                    jQuery(this).parent().parent().remove();
                                });

                                jQuery('#property_history_table').on('click', '.addhistory', function(){
                                    jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
                                    jQuery(this).attr('class','btn removehistory');
                                    var appendTxt = '<tr id="history_table_tr"><td><input type="text" name="history_date[]" value="" class="input-small form-control" /></td><td><input type="text" name="history_event[]" value="" class="input-medium form-control" /></td><td><input type="text" name="history_price[]" value="" class="input-small form-control" /></td><td><input type="text" name="history_source[]" value="" class="input-medium form-control" /></td><td><input type="button" class="btn addhistory" value="<?php echo Text::_('OS_ADD');?>" /></td></tr>';
                                    jQuery("#property_history_table>tbody>tr:last").after(appendTxt);
                                });

                                jQuery('#property_tax_table').on('click', '.removetax', function(){
                                    jQuery(this).parent().parent().remove();
                                });

                                jQuery('#property_tax_table').on('click', '.addtax', function(){
                                    jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
                                    jQuery(this).attr('class','btn removetax');
                                    var appendTxt = '<tr id="tax_table_tr"><td><input type="text" name="tax_year[]" value="" class="input-small form-control" /></td><td><input type="text" name="tax_value[]" value="" class="input-small form-control" /></td><td><input type="text" name="tax_change[]" value="" class="input-small form-control" /></td><td><input type="text" name="tax_assessment[]" value="" class="input-small form-control" /></td><td><input type="text" name="tax_assessment_change[]" value="" class="input-small form-control" /></td><td><input type="button" class="btn addtax" value="<?php echo Text::_('OS_ADD');?>" /></td></tr>';
                                    jQuery("#property_tax_table>tbody>tr:last").after(appendTxt);
                                });
                            });
                            </script>
                        <?php

								echo HTMLHelper::_('bootstrap.endSlide');
							
                        }
                        
							echo HTMLHelper::_('bootstrap.endAccordion');
                        ?>
                    </fieldset>
                    </div>
                </td>
					<!-- End Other information -->
			</tr>
				
				<?php if(count((array)$groups) > 0){ ?>
				<tr>
					<td width="100%">
					<?php
					//echo $pane->endPanel();
						$fieldLists = array();
						

						
						echo HTMLHelper::_('bootstrap.startAccordion', 'extrafield-groups');	
						

						for($i=0;$i<count($groups);$i++){
							$group = $groups[$i];
							$fields = $group->fields;
						
							
							echo HTMLHelper::_('bootstrap.addSlide', 'extrafield-groups', $group->group_name, strtolower(str_replace(" ","",$group->group_name)));	
							
							?>
							<?php
							if(count($fields) > 0){
								for($j=0;$j<count($fields);$j++){
									$field = $fields[$j];
									$fieldLists[] = $field->id;
									if($field->required == 1){
										//$require_field .= $field->field_name.",";
										//$require_label .= $field->field_label.",";
									}
									if(intval($row->id) == 0){
										$display = "display:none;";
									}else{
										$db->setQuery("Select count(fid) from #__osrs_extra_field_types where type_id = '$row->pro_type' and fid = '$field->id'");
										$count = $db->loadResult();
										if($count > 0){
											$display = "";
										}else{
											$display = "display:none;";
										}
									}
									?>
									<div class="<?php echo $rowFluidClass; ?> noleftmargin" id="extrafield_<?php echo $field->id?>" style="<?php echo $display;?>">
										<div class="<?php echo $span3Class;?> extrafield_label noleftmargin">
											<?php echo $field->field_label?>
										</div>
										<div class="<?php echo $span9Class;?> extrafield_input noleftmargin">
											<?php
											HelperOspropertyFields::showField($field,$row->id);
											?>
										</div>
									</div>
									<div class="clearfix"></div>
									<?php
								}
							}
							
							
								echo HTMLHelper::_('bootstrap.endSlide');
							
						}
						//echo $pane->endPane();

							echo HTMLHelper::_('bootstrap.endAccordion');

						?>
					</td>
				</tr>
				<?php } ?>
			</table>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php 
		if(count($amenities) > 0){
			echo HTMLHelper::_('bootstrap.addTab', 'property', 'amenities-page', Text::_('OS_AMENITIES', true)); 
				$optionArr = array();
				$optionArr[] = Text::_('OS_GENERAL_AMENITIES');
				$optionArr[] = Text::_('OS_ACCESSIBILITY_AMENITIES');
				$optionArr[] = Text::_('OS_APPLIANCE_AMENITIES');
				$optionArr[] = Text::_('OS_COMMUNITY_AMENITIES');
				$optionArr[] = Text::_('OS_ENERGY_SAVINGS_AMENITIES');
				$optionArr[] = Text::_('OS_EXTERIOR_AMENITIES');
				$optionArr[] = Text::_('OS_INTERIOR_AMENITIES');
				$optionArr[] = Text::_('OS_LANDSCAPE_AMENITIES');
				$optionArr[] = Text::_('OS_SECURITY_AMENITIES');
				?>
				<table  width="100%">
					<tr>
						<?php
						$j = 0;
						for($k = 0;$k<count($optionArr);$k++){
							$db->setQuery("Select * from #__osrs_amenities where category_id = '".$k."' and published = '1' order by amenities");
							$tmpamenities = $db->loadObjectList();
							if(count($tmpamenities) > 0){
								$j++;
								?>
								<td width="50%" class="alignleft" style="padding:10px;vertical-align:top;">
									<table width="100%">
										<tr>
											<td width="100%" style="height:30px;font-size:16px;" class="backgroundorange colorwhite center fontbold">
												<?php echo $optionArr[$k];?>
											</td>
										</tr>
										<tr>
											<td width="100%">
											<?php 
											$amenitylists = (array) $amenitylists;
											for($i=0;$i<count($tmpamenities);$i++)
											{
												if(count($amenitylists) > 0)
												{
													if(in_array($tmpamenities[$i]->id,$amenitylists))
													{
														$checked = "checked";
													}else{
														$checked = "";
													}
												}else{
													$checked = "";
												}
												?>
												<input type="checkbox" id="amenity_<?php echo $tmpamenities[$i]->id?>" name="amenities[]" <?php echo $checked?> value="<?php echo $tmpamenities[$i]->id?>" /> &nbsp; 
												<label style="display:inline !important;" for="amenity_<?php echo $tmpamenities[$i]->id?>"><?php echo $tmpamenities[$i]->amenities;?></label>
												<BR />
												<?php
											}
											?>
											</td>
										</tr>
									</table>
								</td>
								<?php 
								if($j % 2 == 0){
									echo "</tr><tr>";
								}
							}
						}
						?>
					</tr>
				</table>
				<?php
			echo HTMLHelper::_('bootstrap.endTab'); 
		}?>
		<?php 
		if($configClass['use_open_house'] == 1){
		?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'opentime-page', Text::_('OS_PROPERTY_OPEN_HOUSE', true)); ?>
			<table width="100%">
				<tr>
					<!-- Other information -->
					<td width="100%">
						<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_PROPERTY_OPEN_HOUSE' ); ?></legend>
							<table  width="100%" class="admintable">
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_SELECT_OPENING_TIME')?>
									</td>
									<td>
										<table width="100%" id="property_open_table">
											<tr>
												<th>
													<?php echo Text::_('OS_FROM')?>
												</th>
												<th>
													<?php echo Text::_('OS_TO')?>
												</th>
											</tr>
											<?php
											$j = 0;
											if(count((array)$lists['open']) > 0){
												foreach ($lists['open'] as $cal){
													$j++;
													?>
													<tr>
														<td>
															<?php echo HTMLHelper::calendar($cal->start_from,'start_from[]','start_from'.$j,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
														</td>
														<td>
															<?php echo HTMLHelper::calendar($cal->end_to,'end_to[]','end_to'.$j,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
														</td>
													</tr>
													<?php 
												}
											}
											if($j < 5){
												for($i=$j+1;$i<=5;$i++){
												?>
												<tr id="history_table_tr">
													<td>
														<?php echo HTMLHelper::calendar('','start_from[]','start_from'.$i,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
													</td>
													<td>
														<?php echo HTMLHelper::calendar('','end_to[]','end_to'.$i,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
													</td>
												</tr>
												<?php 
												}
											}
											?>
										</table>
									</td>
								</tr>
							</table>
						</fieldset>
						</div>	
					</td>
					<!-- End Other information -->
				</tr>
			</table>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php } ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'neighbor-page', Text::_('OS_NEIGHBOR', true)); ?>
			<table  width="100%" class="admintable">
				<?php
				for($i=0;$i<count($neighborhoods);$i++)
				{
					$neighborhood = $neighborhoods[$i];
					$db->setQuery("Select * from #__osrs_neighborhood where pid = '$row->id' and neighbor_id = '$neighborhood->id'");
					$neighbor_value = $db->loadObjectList();
					if(count($neighbor_value) > 0)
					{
						$checked		= "checked";
						$value			= 1;
						$display		= "block";

						$neighbor_value = $neighbor_value[0];
						$mins			= $neighbor_value->mins;
						$traffic_type	= $neighbor_value->traffic_type;
						$walk			= "";
						$car			= "";
						$train			= "";
						switch ($traffic_type)
						{
							case "1":
								$walk = "checked";
								break;
							case "2":
								$car = "checked";
								break;
							case "3":
								$train = "checked";
								break;
						}
					}
					else
					{
						$checked		= "0";
						$value			= 0;
						$display		= "none";
					}

					?>
					<tr>
						<td class="key" width="15%">
							<label for="nei_<?php echo $neighborhood->id?>">
								<strong><?php echo Text::_($neighborhood->neighborhood)?></strong>
							</label>
						</td>
						<td width="5%">
							<input type="checkbox" value="<?php echo $value?>" name="nei_<?php echo $neighborhood->id?>" id="nei_<?php echo $neighborhood->id?>" <?php echo $checked?> onclick="javascript:showNeighborhood('<?php echo $neighborhood->id?>')" />
						</td>
						<td width="80%">
							<div id="div_nei_<?php echo $neighborhood->id?>" style="display:<?php echo $display?>;">
								<?php
								if($neighbor_value->distance > 0)
								{
									$distance = OSPHelper::showBath((float)$neighbor_value->distance);
									$min	  = '';
									$walk	  = "";
									$car	  = "";
									$train	  = "";
								}
								else
								{
									$distance = '';
									$min	  = $neighbor_value->mins;
								}
								?>
								<input type="text" name="distance_nei_<?php echo $neighborhood->id?>" size="10" value="<?php echo $distance;?>" class="input-small form-control" style="width:70px;" /> Km
								<?php echo Text::_('OS_OR'); ?>
								<input type="text" name="mins_nei_<?php echo $neighborhood->id?>" size="10" value="<?php echo $min;?>" class="input-small form-control" style="width:55px;" /> <?php echo Text::_('OS_MINS')?> <?php echo Text::_('OS_BY')?>
								&nbsp;&nbsp;&nbsp;
								<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="1" <?php echo $walk?>> <?php echo Text::_('OS_WALK')?>
								<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="2" <?php echo $car?>> <?php echo Text::_('OS_CAR')?>
								<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="3" <?php echo $train?>> <?php echo Text::_('OS_TRAIN')?>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
			<script type="text/javascript">
			 function showNeighborhood(nid){
				var temp = document.getElementById('nei_' + nid);
				var div  = document.getElementById('div_nei_' + nid);
				if(temp.value == 0){
					div.style.display = "block";
					temp.value = 1;
				}else{
					div.style.display = "none";
					temp.value = 0;
				}
			}
			</script>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'photo-page', Text::_('OS_PHOTO', true)); ?>
			<div class="col width-100">
				<fieldset id="photos<?php echo $row->id; ?>" class="form-horizontal options-form">
					<legend><?php echo Text::_('OS_PROPERTY_PHOTOS'); ?></legend>
					<!--
					<ul class="nav nav-pills">
						<li class="active"><a href="#photo-file" data-toggle="tab"><?php echo Text::_('OS_PHOTOS_FILE'); ?></a></li>
						<li><a href="#zip-file" data-toggle="tab"><?php echo Text::_('OS_ZIP_FILE'); ?></a></li>
						<li><a href="#ajax-file" data-toggle="tab"><?php echo Text::_('OS_DRAGDROP'); ?></a></li>
						<?php if($configClass['grabimages_backend'] == 1){ ?>
						<li><a href="#grab-file" data-toggle="tab"><?php echo Text::_('OS_GRAB_IMAGES'); ?></a></li>
						<?php } ?>
					</ul>
					-->

					<?php echo HTMLHelper::_('bootstrap.startTabSet', 'photoTab', array('active' => 'photo-file')); ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'photoTab', 'photo-file', Text::_('OS_PHOTOS_FILE')); ?>
							<div class="tab-pane active" id="photo-file">
								<?php echo sprintf(Text::_('OS_ACCORDING_TO_YOUR_CONFIG_NEW_PHOTOS_WILL_BE_RESIZE'), 100, 100); ?>
								<BR />
								<BR />
								<?php
								$i = 0;
								$temp = array();
								if(count((array)$row->photo) > 0)
								{
									?>
									<input type="checkbox" name="selectall" id="selectall" onClick="javascript:checkall();" value="0" />
									<strong><?php echo Text::_('OS_CHECKALL');?></strong>
									<BR />
									<div class="photoobjects">
										<?php
										$photos = $row->photo;
										for($i=0;$i<count($photos);$i++)
										{
											$photo = $photos[$i];
											$temp[] = $photo->id;
											?>
											<div class="margin5 padding3 border1 borderradius5 photoobject sortableitem" style="display:block;" id="div_<?php echo $i?>" data-state="<?php echo $i + 1;?>" data-value="<?php echo $photo->ordering;?>">
												<table class="admintable">
													<tr>
														<td class="key">
															<?php echo Text::_('OS_PHOTO'); ?> <?php echo $i + 1?>
														</td>
														<td width="70%">
															<?php
															OSPHelper::showPropertyPhoto($photo->image,'thumb',$row->id,'width:'.$configClass['images_thumbnail_width'].'px !important;max-width:150px !important;','img-rounded img-polaroid','');
															?>
															<span id="photo_<?php echo $i+1?>div">
																<input type="file" name="photo_<?php echo $i+1?>" id="photo_<?php echo $i+1?>" size="30" onchange="javacript:check_file('photo_<?php echo $i+1?>');" class="input-large form-control" />
															</span>
														</td>
													</tr>
													<tr>
														<td class="key">
															<?php echo Text::_('OS_PHOTO_DESCRIPTION'); ?>
														</td>
														<td>
															<textarea name="photodesc_<?php echo $i+1?>" id="photodesc_<?php echo $i+1?>" class="inputbox form-control" cols="40" rows="3"><?php echo $photo->image_desc?></textarea>
														</td>
													</tr>
													<tr>
														<td class="key">
															<?php echo Text::_('OS_ORDERING'); ?>
														</td>
														<td>
															<input type="text" name="ordering_<?php echo $i+1?>" id="ordering_<?php echo $i+1?>" class="input-mini form-control" style="width:50px;" value="<?php echo $photo->ordering?>">
														</td>
													</tr>
													<tr>
														<td class="key">

															<?php echo Text::_('OS_REMOVE'); ?>
														</td>
														<td>
															<input type="checkbox" name="remove_<?php echo $photo->id?>" id="remove_<?php echo $photo->id?>" value="0" onclick="javascript:changeValue('remove_<?php echo $photo->id?>')">
														</td>
													</tr>
												</table>
											</div>
											<?php
										}
										?>
									</div>
									<?php
									}
									
									if(intval($row->id) > 0)
									{
										$j = $i;
									}
									else
									{
										$j = 0;
									}
									$limit_photo = ($configClass['limit_upload_photos'] > 0) ? $configClass['limit_upload_photos']:24;
									for($i=$j;$i<$limit_photo;$i++)
									{
										?>
										<div class="margin5 padding3 border1 borderradius5 photoobject" style="display:none;" id="div_<?php echo $i?>">
											<table class="admintable">
												<tr>
													<td class="key">
														<?php echo Text::_('OS_PHOTO'); ?> <?php echo $i + 1?>
													</td>
													<td>
														<span id="photo_<?php echo $i+1?>div">
														<input type="file" name="photo_<?php echo $i+1?>" id="photo_<?php echo $i+1?>" size="30" onchange="javacript:check_file('photo_<?php echo $i+1?>');" class="input-large form-control" />
														</span>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_PHOTO_DESCRIPTION'); ?>
													</td>
													<td>
														<textarea name="photodesc_<?php echo $i+1?>" id="photodesc_<?php echo $i+1?>" class="inputbox form-control" cols="40" rows="3"></textarea>
													</td>
												</tr>
												<tr>
													<td class="key">
														<?php echo Text::_('OS_ORDERING'); ?>
													</td>
													<td>
														<?php echo Text::_('Ordering will increased automatically')?>
													</td>
												</tr>
											</table>
										</div>

										<?php
									}
									?>
								<BR />
								<div id="newphoto<?php echo $row->id; ?>" class="button2-left" style="display:block;">
									<div class="image">
										<a href="javascript:addPhoto();" class="btn btn-success"><i class="icon-new"></i>&nbsp;<?php echo Text::_( 'OS_ADD_PHOTO' ); ?></a>
									</div>
								</div>
							</div>
						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'photoTab', 'zip-file', Text::_('OS_ZIP_FILE')); ?>

							<div class="tab-pane center" id="zip-file">
								<input type="file" onchange="javascript:check_file_type()" class="inputbox form-control" id="zip_file" name="zip_file" size="30"/>
							</div>

						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'photoTab', 'ajax-file', Text::_('OS_DRAGDROP')); ?>
							<?php
							if (OSPHelper::isJoomla4())
							{
							?>
								
							<?php
							}
							?>
							<div class="tab-pane center" id="ajax-file">
								<div id="itemImagesWrap">
									<div id="itemImages">
									</div>
									<div class="clearfix"></div>
								</div>
								<?php echo $lists['uploader'];?>
							</div>
						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<!--
						<?php if($configClass['grabimages_backend'] == 1){ ?>
							<?php echo HTMLHelper::_('bootstrap.addTab', 'photoTab', 'grab-file', Text::_('OS_GRAB_IMAGES')); ?>
								<div class="tab-pane" id="grab-file">
									<div clas="<?php echo $rowFluidClass; ?>">
										<div class="<?php echo $span12Class; ?>">
											<?php echo Text::_('OS_GRAB_IMAGES_EXPLAIN');?>
											<Br /><Br />
											<div id="grab_images_raw">
												URL&nbsp;
												<span class="input-append">
												<input type="text" name="graburl" class="input-xxlarge" id="graburl" />
												<input type="button" value="<?php echo Text::_('OS_GRAB_IMAGES');?>" class="btn" onClick="javascript:doGrabImage();"/>
												</span>
											</div>
										</div>
									</div>
								</div>
							<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php }?>
						-->
					<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
				</fieldset>
			</div>
			<div>
				<!-- jQuery Upload go here -->

			</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'property', 'pulishing-page', Text::_('OS_PUBLISHING', true)); ?>
			<table width="100%" class="admintable" style="padding:5px;">
				<tr>
					<td class="key">
						<?php echo Text::_('OS_OWNER');?>
					</td>
					<td>
						<?php echo OspropertyProperties::getUserInput($row->agent_id);?>
						<span class="required">(*)</span>
						<?php
						$require_field .= "agent_id_id,";
						$require_label .= Text::_('OS_OWNER').",";
						?>
					</td>
				</tr>
				<tr>
					<td class="key" width="35%">
						<?php echo Text::_("OS_ACCESS")?>
					</td>
					<td width="65%">
						<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_FEATURED")?>
					</td>
					<td>
						<?php echo $lists['featured']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="15%">
						<?php echo Text::_("OS_APPROVED")?>
					</td>
					<td width="85%">
						<?php echo $lists['approved']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_PUBLISH")?>
					</td>
					<td>
						<?php echo $lists['state']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_START_PUBLISHING")?>
					</td>
					<td>
						<?php
						if(($row->publish_up == "") or ($row->publish_up == "0000-00-00")){
							$row->publish_up = date("Y-m-d",time());
						}
						?>
						<?php echo HTMLHelper::_('calendar', $row->publish_up, 'publish_up', 'publish_up', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_END_PUBLISHING")?>
					</td>
					<td>
						<?php
						if(($row->publish_down == "") or ($row->publish_down == "0000-00-00")){
							$row->publish_down = "";
						}
						?>
						<?php echo HTMLHelper::_('calendar', $row->publish_down, 'publish_down', 'publish_down', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_CREATED")?>
					</td>
					<td>
						<?php
						if($row->created == "" || $row->created == "0000-00-00")
						{
							$date   = Factory::getDate('now', Factory::getConfig()->get('offset'));
							$row->created = $date->format("Y-m-d");
						}
						?>
						<?php echo HTMLHelper::_('calendar', $row->created, 'created', 'created', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_("OS_MODIFIED")?>
					</td>
					<td>
						<?php
						if($row->modified == "" || $row->modified == "0000-00-00")
						{
							$date   = Factory::getDate('now', Factory::getConfig()->get('offset'));
							$row->created = $date->format("Y-m-d");
						}
						?>
						<?php echo HTMLHelper::_('calendar', $row->modified, 'modified', 'modified', '%Y-%m-%d', array('class'=>'input-small', 'size'=>'25',  'maxlength'=>'19')); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_HITS')?>
					</td>
					<td width="70%">
						<input type="text" class="input-mini form-control" size="5" name="hits" id="hits" value="<?php echo $row->hits; ?>" />
					</td>
				</tr>
			</table>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php 
		if($configClass['show_metatag'] ==1)
		{ 
			echo HTMLHelper::_('bootstrap.addTab', 'property', 'meta-page', Text::_('OS_METADATA', true));
			if ($translatable)
			{
				echo HTMLHelper::_('bootstrap.startTabSet', 'meta-page-tab', array('active' => 'meta-general-page'));
					echo HTMLHelper::_('bootstrap.addTab', 'meta-page-tab', 'meta-general-page', Text::_('OS_GENERAL', true));
			}
			?>
					<table  width="100%" class="admintable padding5">
						<tr>
							<td class="key" valign="top" width="15%">
								<?php echo Text::_('OS_BROWSER_PAGE_TITLE')?>
							</td>
							<td width="85%">
								<input type="text" class="input-large form-control" name="pro_browser_title" id="pro_browser_title" value="<?php echo $row->pro_browser_title; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key" valign="top">
								<?php echo Text::_('OS_META_DESCRIPTION')?>
							</td>
							<td width="70%">
								<textarea name="metadesc" id="metadesc" cols="40" rows="4" class="form-control"><?php echo $row->metadesc?></textarea>
							</td>
						</tr>
					</table>
					<?php
					if ($translatable)
					{
						?>
						<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'meta-page-tab', 'meta-translation-page', Text::_('OS_TRANSLATION', true)); ?>		
							<div class="tab-content">			
								<?php	
									$i = 0;
									$activate_sef = $languages[0]->sef;
									echo HTMLHelper::_('bootstrap.startTabSet', 'meta-languagetranslation', array('active' => 'meta-translation-page-'.$activate_sef));
									foreach ($languages as $language)
									{												
										$sef = $language->sef;
										echo HTMLHelper::_('bootstrap.addTab', 'meta-languagetranslation',  'meta-translation-page-'.$sef, '<img src="'.Uri::root().'media/com_osproperty/flags/'.$sef.'.png" />');
										?>
										<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="meta-translation-page-<?php echo $sef; ?>">
											<table  width="100%" class="admintable padding5">
												<tr>
													<td class="key" valign="top" width="15%">
														<?php echo Text::_('OS_BROWSER_PAGE_TITLE')?>
													</td>
													<td width="85%">
														<input type="text" class="input-large form-control" name="pro_browser_title_<?php echo $sef; ?>" id="pro_browser_title_<?php echo $sef; ?>" value="<?php echo $row->{'pro_browser_title_'.$sef}; ?>" />
													</td>
												</tr>
												<tr>
													<td class="key" valign="top">
														<?php echo Text::_('OS_META_DESCRIPTION')?>
													</td>
													<td width="70%">
														<textarea name="metadesc_<?php echo $sef; ?>" id="metadesc_<?php echo $sef; ?>" cols="40" rows="4"><?php echo $row->{'metadesc_'.$sef}?></textarea>
													</td>
												</tr>
											</table>
										</div>
										<?php				
										echo HTMLHelper::_('bootstrap.endTab');
										$i++;		
									}
									echo HTMLHelper::_('bootstrap.endTabSet');
								?>
							</div>	
						<?php
						echo HTMLHelper::_('bootstrap.endTab');
					echo HTMLHelper::_('bootstrap.endTabSet');
					}
					echo HTMLHelper::_('bootstrap.endTab'); 
				} 
				echo HTMLHelper::_('bootstrap.addTab', 'property', 'note-page', Text::_('OS_NOTES', true)); 
				?>
				<table width="100%" class="admintable">
					<tr>
						<td class="key" valign="top" width="15%">
							<?php echo Text::_('OS_AGENT_NOTE')?>
						</td>
						<td width="85%">
							<textarea name="note" id="note" cols="50" rows="5" class="inputbox form-control" style="width:400px !important;height:150px !important;"><?php echo $row->note?></textarea>
						</td>
					</tr>
				</table>
				<?php echo HTMLHelper::_('bootstrap.endTab'); 
				echo HTMLHelper::_('bootstrap.endTabSet'); 
			?>

		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.endTab');
			echo HTMLHelper::_('bootstrap.addTab', 'propertytranslation', 'translation-page', Text::_('OS_TRANSLATION', true)); ?>	
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
												<?php echo Text::_('OS_PROPERTY_TITLE')?>
											</td>
											<td>
												<input type="text" name="pro_name_<?php echo $sef;?>" id="pro_name_<?php echo $sef;?>" value="<?php echo $row->{'pro_name_'.$sef}?>" size="50" class="input-large form-control" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_ALIAS')?>
											</td>
											<td>
												<input type="text" name="pro_alias_<?php echo $sef;?>" id="pro_alias_<?php echo $sef;?>" value="<?php echo $row->{'pro_alias_'.$sef}?>" size="50" class="input-large form-control" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_PRICE_TEXT')?>
											</td>
											<td>
												<input type="text" name="price_text_<?php echo $sef;?>" id="price_text_<?php echo $sef;?>" value="<?php echo $row->{'price_text_'.$sef}?>" size="50" class="input-large form-control" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_ADDRESS')?>
											</td>
											<td>
												<input type="text" name="address_<?php echo $sef;?>" id="address_<?php echo $sef;?>" value="<?php echo $row->{'address_'.$sef}?>" size="50" class="input-large form-control" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo Text::_('OS_REGION')?>
											</td>
											<td>
												<input type="text" name="region_<?php echo $sef;?>" id="region_<?php echo $sef;?>" value="<?php echo $row->{'region_'.$sef}?>" size="50" class="input-large form-control" />
											</td>
										</tr>
										<tr>
											<td class="key" valign="top">
												<?php echo Text::_('OS_SHORT_DESCRIPTION')?>
											</td>
											<td>
												<textarea name="pro_small_desc_<?php echo $sef;?>" id="pro_small_desc_<?php echo $sef;?>" cols="50" rows="5" class="input-large form-control"><?php echo stripslashes($row->{'pro_small_desc_'.$sef})?></textarea>
											</td>
										</tr>
										<?php
										//$editor = &Factory::getEditor();
										?>
										<tr>
											<td class="key" valign="top">
												<?php echo Text::_('OS_FULL_DESCRIPTION')?>
											</td>
											<td>
												<?php
												echo $editor->display( 'pro_full_desc_'.$sef,  $row->{'pro_full_desc_'.$sef} , '95%', '250', '75', '20',false ) ;
												?>
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
		<?php
		if(intval($row->id) == 0){
			$j = -1;
		}else{
			$j = count($row->photo) - 1;
		}
		?>
		<input type="hidden" name="current_number_photo" id="current_number_photo" value="<?php echo $j?>" />
		<input type="hidden" name="newphoto" id="newphoto" value="<?php echo count((array)$row->photo)?>" />
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="id" value="<?php echo $row->id?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
		<input type="hidden" name="require_field" id="require_field" value="<?php echo substr($require_field,0,strlen($require_field)-1)?>" />
		<input type="hidden" name="require_label" id="require_label" value="<?php echo substr($require_label,0,strlen($require_label)-1)?>" />
		<input type="hidden" name="photo_ids" id="photo_ids" value="<?php echo implode(",",$temp);?>" />
		<?php 
		if(count($lists['types']) > 0){
			foreach ($lists['types'] as $type){
				?>
				<input type="hidden" name="type_id_<?php echo $type->id?>" id="type_id_<?php echo $type->id?>" value="<?php echo implode(",",$type->fields);?>"/>
				<input type="hidden" name="type_id_<?php echo $type->id?>_required" id="type_id_<?php echo $type->id?>_required" value="<?php echo implode(",",$type->required_fields);?>"/>
				<input type="hidden" name="type_id_<?php echo $type->id?>_required_name" id="type_id_<?php echo $type->id?>_required_name" value="<?php echo implode(",",$type->required_fields_name);?>"/>
				<input type="hidden" name="type_id_<?php echo $type->id?>_required_title" id="type_id_<?php echo $type->id?>_required_title" value="<?php echo implode(",",$type->required_fields_label);?>"/>
				<?php 
			}
		}
		?>
		<input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode(",",(array)$fieldLists)?>" />
		
		<?php 
		if($configClass['use_sold'] == 1){
			?>
			<input type="hidden" name="sold_property_types" id="sold_property_types" value="<?php echo $configClass['sold_property_types']?>" />
			<?php 
		}
		?>
		</form>
		<script type="text/javascript">
		 function doGrabImage(){
			var graburl = document.getElementById('graburl');
			if(graburl.value != ""){
				if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(graburl.value)){
					//call ajax script
					var live_site = "<?php echo Uri::root();?>";
					doGrabImageAjax(live_site,graburl.value);
				}else{
					alert("<?php echo Text::_('OS_PLEASE_ENTER_CORRECT_URL');?>");
				}
			}
		}
		jQuery("#pro_type").change( function(){
			var fields = jQuery("#field_ids").val();
			var fieldArr = fields.split(",");
			if(fieldArr.length > 0){
				for(i=0;i<fieldArr.length;i++){
					jQuery("#extrafield_" + fieldArr[i]).hide("fast");
				}
			}
			var selected_value = jQuery("#pro_type").val();
			var selected_fields = jQuery("#type_id_" + selected_value).val();
			var fieldArr = selected_fields.split(",");
			if(fieldArr.length > 0){
				for(i=0;i<fieldArr.length;i++){
					jQuery("#extrafield_" + fieldArr[i]).show("slow");
				}
			}
			<?php 
			if($configClass['use_sold'] == 1){
				?>
				var selected_value = jQuery("#pro_type").val();
				var selected_fields = jQuery("#sold_property_types").val();
				var fieldArr = selected_fields.split("|");
				if(fieldArr.length > 0){
					var show = 0;
					for(i=0;i<fieldArr.length;i++){
						if(fieldArr[i] == selected_value)
						{
							show = 1;
						}
					}
					if(show == 1){
						jQuery("#sold_information").show("slow");
					}else{
						jQuery("#sold_information").hide("slow");
					}
				}
				<?php 
			}
			?>
		});

		 function checkall(){
			var selectall = jQuery("#selectall").val();
			var photo_ids = jQuery("#photo_ids").val();
			if(selectall == 0){
				jQuery("#selectall").val("1");
				if(photo_ids != ""){
					var photo_ids_array = photo_ids.split(",");
					for(var i=0;i<photo_ids_array.length;i++){
						jQuery("#remove_" + photo_ids_array[i]).val("1");
						jQuery("#remove_" + photo_ids_array[i]).prop('checked',true);
					}
				}
			}else{
				jQuery("#selectall").val("0");
				if(photo_ids != ""){
					var photo_ids_array = photo_ids.split(",");
					for(var i=0;i<photo_ids_array.length;i++){
						jQuery("#remove_" + photo_ids_array[i]).val("0");
						jQuery("#remove_" + photo_ids_array[i]).prop('checked',false);
					}
				}
			}
		}
        jQuery( ".photoobjects" ).sortable({
            delay: 150,
            stop:  function() {
                var selectedData = new Array();
                var idData = new Array();
                var orderignData = new Array();
                jQuery('.photoobjects .sortableitem').each( function() {
                    selectedData.push(jQuery(this).attr("id"));
                    idData.push(jQuery(this).attr("data-state"));
                    orderignData.push(jQuery(this).attr("data-value"));
                });
                var temp;
                for(i=0;i<selectedData.length;i++){
                    temp = idData[i];
                    jQuery("#ordering_" + temp).val(i+1);
                }
            }

        });
		</script>
		<?php
	}

	/**
	 * Show photo in zip file
	 *
	 * @param unknown_type $id
	 * @param unknown_type $images
	 */
	static function showPhotoinZipFile($property,$images){
		global $jinput;
		ToolBarHelper::title(Text::_('OS_SELECT_PHOTOS_AND_IMPORT')." [".$property->pro_name."]");
		ToolBarHelper::save('properties_save_photos');
		ToolBarHelper::cancel('properties_cancel_photos');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
			
			<div id="result_zipfile">
			<?php 
			if(count((array)$images) > 0)
			{
				?>
				<div class="padding10">
					<span class="label">
						<?php
						echo sprintf(Text::_('OS_THERE_ARE_PHOTOS_IN_ZIP_FILE'),count($images));
						?>
					</span>
					>
					<span class="label label-info">
						<input type="checkbox" name="select_all" id="select_all" class="checkAll" /> 
						&nbsp;&nbsp;
						<?php echo Text::_('OS_CHECKALL'); ?>
					</span>
				</div>
				<?php 
			}
			?>
			<ul>
			<?php 
			foreach ($images as $key => $image)
			{
				?>
					<li id="oswrapper-<?php echo $key; ?>">
						<img class="os-images-thumbnail-<?php echo $key; ?>" alt="<?php echo $image->name; ?>" src="<?php echo Uri::root().'tmp/osphotos_'.$property->id.'/'.$image->name; ?>" style="width: 210px; height: 120px;"><br>
						<textarea disabled="disabled" id="show_<?php echo $key; ?>" class="os-disabled" rows="5" cols="8" name="photodesc_<?php echo $key?>"></textarea><br>
						<div class="controls">
							<label class="checkbox">
								<input id="<?php echo $key; ?>" type="checkbox" class="check" value="<?php echo $image->name; ?>" name="newphotos[]" /> 
								<?php 
								$name = $image->name;
								
								if(strlen($name) > 20){
									for($i=0;$i<20;$i++){
										echo substr($name,$i,1);
									}
									echo "...";
								}else{
									echo $name;
								}
								?>
							</label>
						</div>
					</li>
				<?php 
			}
			?>
			<ul>
			</div>
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="id" value="<?php echo $property->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="save" id="save" value="<?php echo $jinput->getInt('save',0)?>" />
		</form>
		<script type="text/javascript">
			( function($){
				$(document).ready( function(){
					$('#result_zipfile li').hover( function(){
						$(this).addClass('qick-photo-background');
					});
					$('#result_zipfile li').mouseleave( function(){
						$(this).removeClass('qick-photo-background');
					});
				});
				
				//set check all uncheck all
			    $('.checkAll').click( function()
				{
			        if($('.checkAll').is(':checked'))
					{
			        	$('#result_zipfile ul li').addClass('active');
			            $('input:checkbox').attr('checked',true);
			            $('.os-disabled').attr('disabled', false);
			        }
			        else
					{
			        	$('#result_zipfile ul li').removeClass('active');
			            $('input:checkbox').attr('checked',false);
			            $('.os-disabled').attr('disabled', true);
			            $('.os-disabled').val(' ');
			        }
			    });
	
			    //check disabled textarea
			    $('.check').click( function(){
				    var ID = this.id;
				    if($('#show_'+ ID).is(':disabled')){
				    	$('#show_'+ ID).removeAttr('disabled');
				   }
				    else{
				    	$('#show_'+ ID).attr('disabled',true);
				    }
	
				    if($('#oswrapper-'+ID).hasClass('active')) {
				    	$('#oswrapper-'+ID).removeClass('active');
				    }
				    else{
				    	$('#oswrapper-'+ID).addClass('active');
				    }
				});
			})(jQuery);
			 function check_file(){
				str=document.getElementById('photopackage').value.toUpperCase();
				suffix=".ZIP";
				if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
					alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.zip');
					document.getElementById('photopackage').value='';
				}
		}
		</script>
		<?php
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 * @param unknown_type $amenities
	 * @param unknown_type $amenitylists
	 * @param unknown_type $groups
	 */
	static function printProperties($option,$row,$lists,$amenities,$amenitylists,$groups){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDBO();
		$document =& Factory::getDocument();
		OSPHelper::loadTooltip();
		?>
		<style>
		fieldset label, fieldset span.faux-label {
		    clear: right;
		}
		</style>
		<table  width="100%">
			<tr>
				<td width="60%" valign="top" style="padding-right: 10px;">
					<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_GENERAL' ); ?></legend>
							<table  width="100%" class="admintable">
								<?php
								if($row->ref != ""){
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('Ref #')?></td>
										<td><?php echo $row->ref?></td>
									</tr>
									<?php
								}
								?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_PROPERTY_NAME')?></td>
									<td><?php echo $row->pro_name?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_CATEGORY')?></td>
									<td><?php echo $lists['category']; ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_AGENT').'/'.Text::_('OS_OWNER')?></td>
									<td><?php echo $row->agent->name; ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_PROPERTY_TYPE')?></td>
									<td><?php echo $lists['type']; ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_RENT_TIME_FRAME')?></td>
									<td><?php echo Text::_($row->rent_time);?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_APPROVED')?></td>
									<td><?php echo $row->approved? Text::_('OS_YES'):Text::_('OS_NO'); ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_IS_FEATURED')?></td>
									<td><?php echo $row->isFeatured? Text::_('OS_YES'):Text::_('OS_NO'); ?></td>
								</tr>
								<?php if($row->isSold == 1) {?>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_IS_SOLD')?></td>
									<td><?php echo $row->isSold? Text::_('OS_YES'):Text::_('OS_NO'); ?></td>
								</tr>
								<tr>
									<td class="key" width="30%"><?php echo Text::_('OS_SOLD_ON')?></td>
									<td><?php echo $row->soldOn; ?></td>
								</tr>
								<?php  } ?>
								<tr>
									<td class="key" valign="top"><?php echo Text::_('OS_ACCESS')?></td>
									<td><?php echo $lists['access'][$row->access]; ?></td>
								</tr>
								<?php
								if($row->price_call == 0){
									/*
									if($row->price_original > 0){
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('OS_ORIGINAL_PRICE')?></td>
										<td><?php echo HelperOspropertyCommon::loadCurrency($row->curr)?> <?php echo HelperOspropertyCommon::showPrice($row->price_original)?></td>
									</tr>
									<?php
									}
									*/
									if($row->price > 0){
									?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('OS_PRICE')?></td>
										<td><?php //echo HelperOspropertyCommon::loadCurrency($row->curr)?> <?php //echo HelperOspropertyCommon::showPrice($row->price)?> 
										<?php
										echo OSPHelper::generatePrice($row->curr,$row->price);
										if($row->rent_time != ""){
											echo " / ".Text::_($row->rent_time);
										}
										?>
										</td>
									</tr>
<?php
									}
									?>
								<?php
								}else{
								?>
									<tr>
										<td class="key" width="30%"><?php echo Text::_('OS_CALL_FOR_PRICE')?></td>
										<td><?php echo $row->price_call? Text::_('OS_YES'):Text::_('OS_NO'); ?></td>
									</tr>
								<?php
								}
								?>
							</table>
						</fieldset>
					</div>
					<!-- End General tab-->
					
					<!-- Address -->
					<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_ADDRESS' ); ?></legend>
							<table  width="100%" class="admintable">
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_ADDRESS')?>
									</td>
									<td>
										<?php echo $row->address?>
									</td>
								</tr>
								<?php if($row->postcode != "" ){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_POSTCODE')?>
									</td>
									<td>
										<?php echo $row->postcode?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->city != "" ){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_CITY')?>
									</td>
									<td>
										<?php echo HelperOspropertyCommon::loadCityName($row->city)?>
									</td>
								</tr>
								<?php } ?>
								<!--
								<?php if($row->province != "" ){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_PROVINCE')?>
									</td>
									<td>
										<?php echo $row->province?>
									</td>
								</tr>
								<?php } ?>
								-->
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_COUNTRY')?>
									</td>
									<td>
										<?php echo $lists['country']; ?>
									</td>
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_STATE')?>
									</td>
									<td>
										<div id="div_states">
										<?php
										echo $lists['states'];
										?>
									</td>
								</tr>
								<?php
								if($row->region != ""){
								?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_REGION')?>
									</td>
									<td>
										<?php echo $row->region?>
									</td>
								</tr>
								<?php } ?>
								
							</table>
						</fieldset>
					</div>	
					<!-- End Address -->
					
					<!-- Property information -->
					<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_PROPERTY_INFORMATION' ); ?></legend>
							<table  width="100%" class="admintable">
								<!--
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_VIDEO_EMBED_CODE')?>
									</td>
									<td>
										<?php echo $row->pro_video?>
									</td>
								</tr>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_DOCUMENT_LINK')?>
									</td>
									<td>
										<?php echo $row->pro_pdf?>
									</td>
								</tr>
								-->
								<?php if($row->rooms > 0){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_NUMBER_ROOMS')?>
									</td>
									<td>
										<?php echo $row->rooms;?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->bath_room > 0){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_NUMBER_BATHROOMS')?>
									</td>
									<td>
										<?php echo $row->bath_room; ?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->bed_room > 0){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_NUMBER_BEDROOMS')?>
									</td>
									<td>
										<?php echo $row->bed_room;?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->parking != ""){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_PARKING')?>
									</td>
									<td>
										<?php echo $row->parking?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->square_feet != ""){?>
								<tr>
									<td class="key" width="30%">
										<?php echo OSPHelper::showSquareLabels();?>
									</td>
									<td>
										<?php echo $row->square_feet?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->lot_size != ""){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_LOT_SIZE');?>
									</td>
									<td>
										<?php echo $row->lot_size?> <?php echo OSPHelper::showSquareSymbol();?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->number_of_floors > 0){?>
								<tr>
									<td class="key" width="30%">
										<?php echo Text::_('OS_NUMBER_OF_FLOORS')?>
									</td>
									<td>
										<?php echo $row->number_of_floors?>
									</td>
								</tr>
								<?php } ?>
							</table>
						</fieldset>
					</div>	
					<!-- End Other information -->

					<!-- Other information -->
					<div class="col width-100">
						<fieldset class="general form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_OTHER_INFORMATION' ); ?></legend>
							<table  width="100%" class="admintable">
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_SMALL_DESCRIPTION')?>
									</td>
									<td>
										<?php echo $row->pro_small_desc?>
									</td>
								</tr>
								<?php if($row->pro_full_desc != ""){?>
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_FULL_DESCRIPTION')?>
									</td>
									<td>
										<?php echo $row->pro_full_desc; ?>
									</td>
								</tr>
								<?php } ?>
								<?php if($row->note != ""){?>
								<tr>
									<td class="key" valign="top">
										<?php echo Text::_('OS_AGENT_NOTE')?>
									</td>
									<td>
										<?php echo $row->note?>
									</td>
								</tr>
								<?php } ?>
							</table>
						</fieldset>
					</div>	
					<div class="col width-100">
						<fieldset class="fieldset_photo form-horizontal options-form">
							<legend><?php echo Text::_('OS_AGENT_INFORMATION')?></legend>
								<table  width="100%" class="admintable">
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_PHOTO')?>
										</td>
										<td>
											<?php
											if($row->agent->photo != ""){
												?>
												<img style="width: 70px;" src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent->photo?>">
												<?php
											}else{
												?>
												<img style="width: 70px;" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/user.png" class="border1" width="120">
												<?php
											}
											?>
										</td>
									</tr>
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_NAME')?>
										</td>
										<td>
											<b><?php echo $row->agent->name;?></b>
										</td>
									</tr>
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_ADDRESS')?>
										</td>
										<td>
											<?php echo $row->agent->address;?>
										</td>
									</tr>
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_STATE')?>
										</td>
										<td>
											<?php echo $row->agent->state_name;?>
										</td>
									</tr>
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_COUNTRY')?>
										</td>
										<td>
											<?php echo $row->agent->country_name;?>
										</td>
									</tr>
									<?php if($row->agent->license != ""){?>
									<tr>
										<td class="key" >
											<?php echo Text::_('OS_LICENSE')?>
										</td>
										<td>
											<?php echo $row->agent->license;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->phone != ""){
									?>
									<tr>
										<td class="key" style="text-align:right;">
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/phone.png">
										</td>
										<td>
											<?php echo $row->agent->phone;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->mobile != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/mobile.png">
										</td>
										<td>
											<?php echo $row->agent->mobile;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->fax != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/fax.png">
										</td>
										<td>
											<?php echo $row->agent->fax;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->gtalk != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/gtalk.png">
										</td>
										<td>
											<?php echo $row->agent->gtalk;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->skype != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/skype.png">
										</td>
										<td>
											<?php echo $row->agent->skype;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->aim != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/aim.png">
										</td>
										<td>
											<?php echo $row->agent->aim;?>
										</td>
									</tr>
									<?php
									}
									if($row->agent->facebook != ""){
									?>
									<tr>
										<td class="key" >
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/facebook.png">
										</td>
										<td>
											<a href="<?php echo $row->agent->facebook;?>" target="_blank"><?php echo $row->agent->facebook;?></a>
										</td>
									</tr>
									<?php
									}
									?>
								</table>
						</fieldset>
					
					</div>
				<!-- End Other information -->
				</td>
				<td width="40%" valign="top">
					<div class="col width-100">
						<fieldset class="general">
							<legend><?php echo Text::_( 'OS_INFORMATION' ); ?></legend>
							<table  width="100%" class="admintable">
								<tr>
									<td class="key width110px">
										<?php echo Text::_('OS_PROPERTY_ID')?>:
									</td>
									<td class="padding5">
										<?php echo $row->id; ?>
									</td>
								</tr>
								<tr>
									<td class="key width110px">
										<?php echo Text::_('OS_HITS')?>:
									</td>
									<td class="padding5">
										<?php echo $row->hits; ?>
									</td>
								</tr>
								<tr>
									<td class="key width110px">
										<?php echo Text::_('OS_RATING')?>:
									</td>
									<td class="padding5">
										<?php
										if($row->number_votes > 0){
											$points = round($row->total_points/$row->number_votes);
											?>
											<table  width="100%">
												<tr>
													<td width="40%">
														<?php
														for($i=1;$i<=$points;$i++){
															?>
															<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star1.png">
															<?php
														}
														for($i=$points+1;$i<=5;$i++){
															?>
															<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star2.png">
															<?php
														}
														?>
													</td>
													<td align="left" width="60%">
														<?php
														echo " <b>(".$points."/5)</b>";
														?>
													</td>
												</tr>
											</table>
											<?php


										}else{
											for($i=1;$i<=5;$i++){
												?>
												<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star2.png">
												<?php
											}
										}
										?>
									</td>
								</tr>
								<tr>
									<td class="key width110px">
										<?php echo Text::_('OS_STATUS')?>:
									</td>
									<td class="padding5">
										<?php
										if($row->published == 0){
											echo Text::_('OS_UNPUBLISHED');
										}else{
											echo Text::_('OS_PUBLISHED');
										}
										?>
									</td>
								</tr>
								<tr>
									<td class="key width110px">
										<?php echo Text::_('OS_CREATED')?>:
									</td>
									<td class="padding5">
										<?php
										echo $row->created;
										?>
									</td>
								</tr>
							</table>
						</fieldset>
						<?php 
						$query = $db->getQuery(true);
						$query->select("*")->from("#__osrs_property_price_history")->where("pid = '$row->id'")->order("`date` desc");
						$db->setQuery($query);
						$prices = $db->loadObjectList();
						if($configClass['use_property_history'] == 1 && count((array)$prices) > 0){ ?>
						<!-- History -->
						<fieldset class="form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_PROPERTY_HISTORY' ); ?></legend>
							<div class="<?php echo $rowFluidClass; ?>">
								<div class="<?php echo $span12Class; ?> noleftmargin">
									<table class="table">
										<thead>
											<tr>
												<th>
													<?php echo Text::_('OS_DATE');?>
												</th>
												<th>
													<?php echo Text::_('OS_EVENT');?>
												</th>
												<th>
													<?php echo Text::_('OS_PRICE');?>
												</th>
												<th>
													<?php echo Text::_('OS_SOURCE');?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($prices as $price){
												?>
												<tr>
													<td>
														<?php echo $price->date;?>
													</td>
													<td>
														<?php echo $price->event;?>
													</td>
													<td>
														<?php echo OSPHelper::generatePrice('',$price->price);?>
													</td>
													<td>
														<?php echo $price->source;?>
													</td>
												</tr>
												<?php 
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
						<?php } 
						?>
						<!-- End History -->
						<?php 
						$query = $db->getQuery(true);
						$query->select("*")->from("#__osrs_property_history_tax")->where("pid = '$row->id'")->order("`tax_year` desc");
						$db->setQuery($query);
						$taxes = $db->loadObjectList();
						if($configClass['use_property_history'] == 1 && count((array)$taxes) > 0){ ?>
						<!-- tax -->
						<fieldset class="form-horizontal options-form">
							<legend><?php echo Text::_( 'OS_PROPERTY_TAX' ); ?></legend>
							<div class="<?php echo $rowFluidClass; ?>">
								<div class="<?php echo $span12Class; ?> noleftmargin">
									<table class="table">
										<thead>
											<tr>
												<th>
													<?php echo Text::_('OS_YEAR');?>
												</th>
												<th>
													<?php echo Text::_('OS_TAX');?>
												</th>
												<th>
													<?php echo Text::_('OS_CHANGE');?>
												</th>
												<th>
													<?php echo Text::_('OS_TAX_ASSESSMENT');?>
												</th>
												<th>
													<?php echo Text::_('OS_TAX_ASSESSMENT_CHANGE');?>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											foreach ($taxes as $tax){
												?>
												<tr>
													<td>
														<?php echo $tax->tax_year;?>
													</td>
													<td>
														<?php echo OSPHelper::generatePrice('',$tax->property_tax);?>
													</td>
													<td>
														<?php 
														if($tax->tax_change != ""){
														?>
															<?php echo $tax->tax_change;?> %
														<?php }else { ?>
															--
														<?php } ?>
													</td>
													<td>
														<?php echo OSPHelper::generatePrice('',$tax->tax_assessment);?>
													</td>
													<td>
														<?php 
														if($tax->tax_assessment_change != ""){
														?>
															<?php echo $tax->tax_assessment_change;?> %
														<?php }else { ?>
															--
														<?php } ?>
													</td>
												</tr>
												<?php 
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</fieldset>
						<?php }
						?>
						<?php				
						if(count($amenitylists) > 0){
						?>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo Text :: _('OS_CONVENIENCE'); ?></legend>
							<table  width="100%">
								<tr>
									<?php
									$j = 0;
									for($i=0;$i<count($amenities);$i++){
										if(count($amenitylists) > 0){
											if(in_array($amenities[$i]->id,$amenitylists)){
												$j++;
										?>
											<td align="left padding5">
												<?php echo $amenities[$i]->amenities?>
											</td>
										<?php
											}
										}else{
										?><td>&nbsp;</td>
										<?php 	break;
										}

										if($j==3){
											echo "</tr><tr>";
											$j = 0;
										}
									}
									?>
								</tr>
							</table>
						</fieldset>
							<?php
						}

						$db->setQuery("Select count(id) from #__osrs_neighborhood where pid = '$row->id'");
						$count = $db->loadResult();
						if($count > 0){
							?>
							<fieldset>
							<legend><?php echo Text :: _('OS_NEIGHBORHOOD'); ?></legend>
							<?php
							HelperOspropertyCommon::loadNeighborHood($row->id);
							?>
						</fieldset>
							<?php
						}
						if(count($groups) > 0){
							for($i=0;$i<count($groups);$i++){
								$group = $groups[$i];
								$fields = $group->fields;
								if(count($fields) > 0){
									if(HelperOspropertyFields::checkFieldData($row->id,$group->id) == 1){
									?>
										<fieldset class="form-horizontal options-form">
											<legend><?php echo $group->group_name;?></legend>
											<table  width="100%" class="admintable border1">
											<?php
											$fields = HelperOspropertyFields::getFieldsData($row->id,$group->id);
											for($j=0;$j<count($fields);$j++){
												$field = $fields[$j];
												//if(HelperOspropertyFieldsPrint::showField($field,$row->id) != ""){
												?>
												<tr>
													<td class="key width110px">
													<?php
													echo HelperOspropertyFieldsPrint::showField($field,$row->id);
													?>
														<?php echo $field->field_label?>
													</td>
													<td>
														<?php
														echo $field->value;
														?>
													</td>
												</tr>
												<?php
												//}
											}
											?>
											</table>
										</fieldset>
										<?php
									}
								}
							}
						}
					?>
					</div>
					<div class="col width-100">
						<fieldset id="photos<?php echo $row->id; ?>">
							<legend><?php echo Text::_('OS_PROPERTY_PHOTOS'); ?></legend>
							<?php
							$i = 0;
							if(count($row->photo) > 0){
								$photos = $row->photo;
								for($i=0;$i<count($photos);$i++){
									$photo = $photos[$i];
									?>
									<div class="padding3 border1" style="display:block;" id="div_<?php echo $i?>">
									<table class="admintable">
										<tr>
											<td class="key width110px">
												<?php echo Text::_('OS_PHOTO')?> <?php echo $i + 1?>
											</td>
											<td>
												<?php												
												OSPHelper::showPropertyPhoto($photo->image,'thumb',$row->id,'width: 100px;','img-polaroid','');
												?>
											</td>
										</tr>
										<?php if ($photo->image_desc != ""){?>
										<tr>
											<td class="key width110px">
												<?php echo Text::_('OS_PHOTO_DESCRIPTION')?>
											</td>
											<td>
												<?php echo $photo->image_desc?>
											</td>
										</tr>
										<?php } ?>
									</table>
									</div>
									<?php
								}
							}
							?>
						</fieldset>
					</div>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
		window.print();
		</script>
		<?php
	}

	/**
	 * Backup Form
	 *
	 * @param unknown_type $option
	 */
	static function backupForm($option){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_BACKUP'));
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminform" id="adminForm">
			<table  width="100%" class="backgroundlightgray border1">
				<tr>
					<td width="100%" class="padding5">
						<H2>
							<?php echo Text::_('OS_BACKUP')?>
						</H2>
						<?php echo Text::_("OS_BACKUP_INFOR")?>
						<BR><BR>
						<center>
							<input type="submit" class="btn btn-primary colorred fontbold" value="<?php echo Text::_('OS_BACKUP')?>" style="border:1px solid pink;">
						</center>
						<BR>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_osproperty">
			<input type="hidden" name="task" value="properties_dobackup">
			<input type="hidden" name="boxchecked" value="0">
		</form>
		<?php
	}


	static function restoreForm($option,$lists){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_RESTORE'));
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminform" id="adminForm">
			<table  width="100%" class="backgroundlightgray border1">
				<tr>
					<td width="100%" class="padding5">
						<H2>
							<?php echo Text::_('OS_RESTORE')?>
						</H2>
						<?php echo Text::_("OS_RESTORE_INFOR")?>
						<BR><BR>
						<center>
							<?php
							echo $lists['bkfiles'];
							?>
							<BR>
							<BR>
							<input type="submit" class="btn btn-primary colorred fontbold" value="<?php echo Text::_('OS_RESTORE')?>" style="border:1px solid pink;">
						</center>
						<BR>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_osproperty">
			<input type="hidden" name="task" value="properties_dorestore">
			<input type="hidden" name="boxchecked" value="0">
		</form>
		<?php
	}

	/**
	 * Upload photo packages
	 *
	 * @param unknown_type $option
	 */
	static function uploadPhotoPackages($option){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_IMPORT_PHOTOS'));
		ToolBarHelper::save('properties_douploadphotopackages',Text::_('OS_UPLOAD'));
		ToolBarHelper::cancel('');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<script type="text/javascript">
		 function check_file(){
			str=document.getElementById('photopackage').value.toUpperCase();
			suffix=".ZIP";
			if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
				alert('<?php echo Text::_('OS_ALLOW_FILE')?>: *.zip');
				document.getElementById('photopackage').value='';
			}
		}
		</script>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table 	  width="100%" class="admintable">
			<tr>
				<td width="100%" class="padding20">
					
					<table  width="100%" class="preinstalldemotable">
						<tr>
							<td width="100%" class="center padding20">
								<b>
									<?php echo Text::_('OS_PLEASE_SELECT_PHOTO_PACKAGE')?>
								</b>
								<BR>
								<BR>
								<input type="file" size="60" name="photopackage" id="photopackage" class="inputbox form-control" onchange="javascript:check_file()">
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000">
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		</form>
		<?php
	}

	/**
	 * Prepare install data form
	 *
	 * @param unknown_type $option
	 */
	static function prepareInstallSampleForm($option,$lists){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_INSTALLSAMPLEDATA'));
		ToolBarHelper::cancel();
		?>
		<script type="text/javascript">
		 function activeContinueButton(){
			checkbox = document.getElementById('agree');
			startbutton = document.getElementById('startbutton');
			if(checkbox.value == 0){
				checkbox.value = 1;
				startbutton.disabled = false;
			}else{
				checkbox.value = 0;
				startbutton.disabled = true;
			}
		}
		</script>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm">
		<table 	  width="100%" class="admintable">
			<tr>
				<td width="100%" class="padding20">
					
					<table width="100%" class="preinstalldemotable">
						<tr>
							<td width="100%" class="alignleft padding20">
								<b>
								<?php echo Text::_('OS_NOTICE')?>:
								</b>
								<br>
								<br>
								<?php
								if($lists['cuser'] == 0){
									echo Text::_('OS_ADD_NEW_COMPANY_USER');
									echo "<BR><BR>";
								}
								if($lists['ccompany'] == 0){
									echo Text::_('OS_ADD_NEW_COMPANY');
									echo "<BR><BR>";
								}
								if($lists['auser'] == 0){
									echo Text::_('OS_ADD_NEW_AGENT_USER');
									echo "<BR><BR>";
								}
								if($lists['cagent'] == 0){
									echo Text::_('OS_ADD_NEW_AGENT');
									echo "<BR><BR>";
								}
								echo Text::_('OS_REMOVE_DATA_FROM_SOME_TABLES');
								?>
							</td>
						</tr>
						<tr>
							<td width="100%" class="alignleft padding20" >
								<b>
								<?php echo Text::_('OS_SELECT_LOCATION_TO_INSTALL_SAMPLE_DATA')?>:
								</b>
								<br>
								<br>
								<table  width="100%"  class="admintable">
									<tr>
										<td width="33%" align="center" valign="top" style="font-size:1.0em;">
											<B>
												<?php echo Text::_('OS_COUNTRY')?>
											</B>
											<BR />
											<?php
											if(HelperOspropertyCommon::checkCountry()){
											?>
												<?php echo $lists['country'];?>
											<?php
											}else{
												$db = Factory::getDbo();
												$db->setQuery("Select country_name from #__osrs_countries where id = '".HelperOspropertyCommon::getDefaultCountry()."'");
												echo $db->loadResult();
												?>
												<?php echo $lists['country'];?>
												<?php
											}
											?>
										</td>
										<td width="33%" align="center" valign="top" style="font-size:1.0em;">
											<B>
												<?php echo Text::_('OS_STATE')?>
											</B>
											<BR />
											<div id="country_state">
												<?php echo $lists['states'];?>
											</div>
										</td>
										<td width="33%" align="center" valign="top" style="font-size:1.0em;">
											<B>
												<?php echo Text::_('OS_CITY')?>
											</B>
											<BR />
											<div id="city_div">
												<?php echo $lists['city'];?>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td  class="center fontbold padding20" style="border:1px solid red;background-color:pink;">
								<input type="checkbox" name="agree" id="agree" value="0" onclick="javascript:activeContinueButton()">&nbsp;
								<?php
								echo Text::_('OS_READ_AND_ACCEPTED');
								?>
								<BR><BR>
								<input type="button" id="startbutton" class="btn btn-primary" value="<?php echo Text::_('OS_START_INSTALL')?>" disabled="true" onclick="javascript:checkSubmitInstallSampleData()">
								
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_osproperty">
		<input type="hidden" name="task" value="properties_installdata">
		<input type="hidden" name="boxchecked" value="0">
		</form>
		<script type="text/javascript">
		 function change_country_agent(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
		}
		 function change_state(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCityInstallSampleData(state_id,city_id,'state_id',live_site);
		}
		 function loadCity(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCityInstallSampleData(state_id,city_id,'state',live_site);
		}
		 function checkSubmitInstallSampleData(){
			var country = document.getElementById('country');
			var state   = document.getElementById('state');
			var city    = document.getElementById('city');
			if((country.value == "") || (state.value == "") || (city.value == "")){
				alert("<?php echo Text::_('OS_PLEASE_SELECT_LOCATION_IF_YOU_WANT_TO_INSTALL_SAMPLE_DATA')?>");
				return false;
			}else{
				var answer = confirm("<?php echo Text::_('OS_ARE_YOU_SURE_TO_INSTALL_SAMPLE_DATA')?>");
				if(answer == 1){
					document.adminForm.submit();
				}
			}
		}
		</script>
		<?php
	}


	/**
	 * List cities
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function listCities($option,$rows,$pageNav,$lists){
		global $jinput, $mainframe,$_jversion;
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=properties_listcity" name="adminForm" id="adminForm">
		<table width="100%">
			<tr>
				<td class="padding5 alignright">
					<b>
						<?php echo Text::_('OS_FILTER')?>: <input type="text" class="input-medium form-control search-query" name="keyword" value="<?php echo $jinput->getString('keyword','')?>">
					</b>
					<input type="button" class="btn btn-primary" value="<?php echo Text::_('OS_SEARCH')?>">
				</td>
			</tr>
			<tr>
				<td class="padding5 alignleft fontbold" style="padding-top:0px;padding-bottom:10px;">
					<?php echo Text::_('OS_COUNTRY')?>: <?php echo $lists['country']?>
					&nbsp;&nbsp;&nbsp;
					<?php echo Text::_('OS_STATE')?>: <?php echo $lists['states']?>
				</td>
			</tr>
		</table>
		<table  width="100%" class="table table-striped">
		<thead>
			<tr>
				<th width="33%">
					<?php echo Text::_('OS_CITY')?>
				</th>
				<th width="33%">
					<?php echo Text::_('OS_STATE')?>
				</th>
				<th width="33%">
					<?php echo Text::_('OS_COUNTRY')?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3" class="center">
					<?php echo $pageNav->getListFooter();?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$db = Factory::getDBO();
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="left" style="padding-left:30px;">
						<a class="pointer" onclick="if (window.parent) window.parent.jSelectCity_jform_request_id('<?php echo $row->id?>', '<?php echo $row->city?>');">
							<?php echo $row->city?>
						</a>
					</td>
					<td align="left" style="padding-left:30px;">
						<?php echo $row->state_name?>
					</td>
					<td align="left" style="padding-left:30px;">
						<?php echo $row->country_name?>
					</td>
				</tr>
			<?php
			$k = 1 - $k;
			}
			?>
		</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="properties_listcity" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="tmpl" value="component" />
		</form>
		<?php
	}
	
	/**
	 * Optimize sef form
	 *
	 * @param unknown_type $option
	 */
	static function optimizeSefForm($option){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_SEF_URLS_OPTIMIZATION'));
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div class="span12 center">
				<div class="span3"></div>
				<div class="span6">
					<h3>
						<?php echo Text::_('OS_SEF_URLS_OPTIMIZATION');?>
					</h3>
					<div class="clearfix"></div>
					<div class="img-polaroid alignleft">
						<?php echo Text::_('OS_SEF_URLS_OPTIMIZATION_EXPLAIN');?>
					</div>
					<div class="clearfix"></div>
					<br />
					<input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_YES_I_AGREE');?>" />
					<input type="button" onclick="javascript:returnControlPanel();" class="btn btn-warning" value="<?php echo Text::_('OS_NO_I_DO_NOT_AGREE');?>" />
				</div>
				<div class="span3"></div>
			</div>
		</div>
		<input type="hidden" name="option" id="option" value="com_osproperty" />
		<input type="hidden" name="task" value="properties_doOptimizeSefUrls" id="task" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
		 function returnControlPanel(){
			location.href = "index.php?option=com_osproperty&task=cpanel_list";
		}
		</script>
		<?php
	}
	
	static function syncdatabaseForm($option){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_SYNCHONOUS_MULTIPLE_LANGUAGES_DATABASE'));
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div class="span12 center">
				<div class="span3"></div>
				<div class="span6">
					<h3>
						<?php echo Text::_('OS_SYNCHONOUS_MULTIPLE_LANGUAGES_DATABASE');?>
					</h3>
					<div class="clearfix"></div>
					<div class="img-polaroid alignleft">
						<?php echo Text::_('OS_SYNCHONOUS_MULTIPLE_LANGUAGES_DATABASE_EXPLAIN');?>
						<BR />
						<strong>
						
						</strong>
					</div>
					<div class="clearfix"></div>
					<br />
					<input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_YES_I_AGREE');?>" />
					<input type="button" onclick="javascript:returnControlPanel();" class="btn btn-warning" value="<?php echo Text::_('OS_NO_I_DO_NOT_AGREE');?>" />
				</div>
				<div class="span3"></div>
			</div>
		</div>
		<input type="hidden" name="option" id="option" value="com_osproperty" />
		<input type="hidden" name="task" value="properties_doSyncdatabase" id="task" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
		 function returnControlPanel(){
			location.href = "index.php?option=com_osproperty&task=cpanel_list";
		}
		</script>
		<?php	
	}
	
	static function reGeneratePicturesForm($option){
		global $jinput, $mainframe;
		ToolBarHelper::title(Text::_('OS_RE_GENERATE_PICTURES_OF_PROPERTIES'));
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div class="span12 center">
				<div class="span2"></div>
				<div class="span8">
					<h3>
						<?php echo Text::_('OS_RE_GENERATE_PICTURES_OF_PROPERTIES');?>
					</h3>
					<div class="clearfix"></div>
					<div class="img-polaroid alignleft">
						<?php echo Text::_('OS_RE_GENERATE_PICTURES_OF_PROPERTIES_EXPLAIN');?>
						<BR />
						<hr>
						<center>
						<strong>This function need quite a lot memory and time for processing. So please take care your PHP Setting</strong>
						</center>
						<div class="clearfix"></div>
						<table class="table-striped" width="100%">
							<tr>
								<td width="30%">
									<strong>
									Memory limit:
									</strong>
								</td>
								<td width="70%">
									<?php
									$memory_limit = ini_get('memory_limit');
									if($memory_limit != ""){
										echo $memory_limit;
										$memory_limit1 = intval(trim(str_replace("M","",$memory_limit)));
										if($memory_limit1 <  200){
											if(ini_set('memory_limit','999M')){
												?>
												&nbsp;<style class="fontgreen">OS Property can change this value when we run the function</style>
												<?php
											}else{
												?>
												&nbsp;<style class="fontred">OS Property cannot change this value when we run the function</style>
												<?php
											}
										}
									}
									?>
								</td>
							</tr>
							<tr>
								<td width="30%">
									<strong>Max execution time: </strong>
								</td>
								<td width="70%">
									<?php
									$max_execution_time = ini_get('max_execution_time');
									if($max_execution_time != ""){
										echo $max_execution_time/60;
										echo " seconds";
										$max_execution_time1 = intval(trim(str_replace("M","",$max_execution_time)));
										if($max_execution_time1 <  1000){
											if(ini_set('max_execution_time','3000')){
												?>
												&nbsp;<style class="fontgreen">OS Property can change this value when we run the function</style>
												<?php
											}else{
												?>
												&nbsp;<style class="fontred">OS Property cannot change this value when we run the function</style>
												<?php
											}
										}
									}
									?>
								</td>
							</tr>
						</table>
					</div>
					<div class="clearfix"></div>
					<br />
					<input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_YES_I_AGREE');?>" />
					<input type="button" onclick="javascript:returnControlPanel();" class="btn btn-warning" value="<?php echo Text::_('OS_NO_I_DO_NOT_AGREE');?>" />
				</div>
				<div class="span2"></div>
			</div>
		</div>
		<input type="hidden" name="option" id="option" value="com_osproperty" />
		<input type="hidden" name="task" value="properties_doReGeneratePictures" id="task" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<script type="text/javascript">
		 function returnControlPanel(){
			location.href = "index.php?option=com_osproperty&task=cpanel_list";
		}
		</script>
		<?php	
		
	}
	

	/**
	 * generate photo crop
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 * @param unknown_type $photoIds
	 * @param unknown_type $save
	 */
	static function generatePhotoCrop($option,$id,$photoIds,$save,$pro_name){
		global $jinput, $mainframe,$configClass;
		ToolBarHelper::title(Text::_('OS_CREATE_PHOTO_BY_MANUAL')." [".$pro_name."]");
		ToolBarHelper::apply('properties_savephoto');
		ToolBarHelper::custom('properties_completesaving','forward.png','forward.png',Text::_('OS_SKIP'),false);
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_photos where id in ($photoIds)");
		$photos = $db->loadObjectList();
		jimport('joomla.html.pane');

		?>
		<script type="text/javascript">
		//enable passthrough of errors from YUI Event:
		if ((typeof YAHOO !== "undefined") && (YAHOO.util) && (YAHOO.util.Event)) {
			YAHOO.util.Event.throwErrors = true;
		}
		 function showDiv(photo_id){
			var div = document.getElementById('div_' + photo_id);
			var href = document.getElementById('link_' + photo_id);
			if(div.style.display == "block"){
				div.style.display = "none";
				href.innerHTML = "[+]";
			}else{
				div.style.display = "block";
				href.innerHTML = "[-]";
			}
		}
		</script>
		<form method="POST" action="index.php" name="adminForm"  id="adminForm">
		<table width="100%" class="admintable">
			<?php
			if($photos > 0){

				for($i=0;$i<count($photos);$i++){

					$photo = $photos[$i];
					$photo_id = $photo->id;

					if($i == 0){
						$display = "block";
					}else{
						$display = "none";
					}
					
					$medium_width = $configClass['images_large_width'];
					$medium_height = $configClass['images_large_height'];
					$original_info = getimagesize(JPATH_ROOT.DS."images/osproperty/properties".DS.$id.DS.$photo->image);

					$origin_width  = $original_info[0];
					$origin_height = $original_info[1];
				?>
				<tr>
					<td width="100%">
						<table  width="100%" class="border1">
							<tr>
								<td class="padding3 fontbold center colorwhite" style="background-color:#7A7676;">
									<?php echo Text::_('OS_PHOTO')?> <?php echo $i+1;?> &nbsp;&nbsp;
									<?php
									if($display == "block"){
										?>
										<a href="javascript:showDiv(<?php echo $photo_id?>)" id="link_<?php echo $photo_id?>">[-]</a>
										<?php
									}else{
										?>
										<a href="javascript:showDiv(<?php echo $photo_id?>)" id="link_<?php echo $photo_id?>">[+]</a>
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td width="100%" valign="top">
									<div id="div_<?php echo $photo_id?>" style="display:<?php echo $display?>;">
									<?php
									//$pane =& JPane::getInstance('tabs');
									?>
									<table width="100%"> 
										<tr>
											<td width="100%" valign="top">
												<div class="row-fluid">
													<ul class="nav nav-tabs">
														<li class="active"><a href="#tab1<?php  echo $i?>" data-toggle="tab"><?php echo Text::_('OS_THUMBNAIL_PHOTO');?></a></li>
														<?php
														if(($medium_height < $origin_height) or ($medium_width < $origin_width)){
														?>
														<li><a href="#tab2<?php  echo $i?>" data-toggle="tab"><?php echo Text::_('OS_MEDIUM_PHOTO');?></a></li>
														<?php
														}
														?>
													</ul>
													<div class="tab-content">	
														<div class="tab-pane active" id="tab1<?php  echo $i?>">
														<table width="100%" class="admintable">
															<tr>
																<td class="key alignleft">
																	<input type="radio" name="tb_<?php echo $photo_id?>" id="tb_<?php echo $photo_id?>" value="0">
																	&nbsp;
																	<?php echo Text::_('OS_THUMBNAIL_PHOTO_IS_CREATED_BY_OSPROPERTY')?>
																	
																</td>
															</tr>
															<tr>
																<td width="100%" class="center">
																	<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/thumb/<?php echo $photo->image?>" width="<?php echo $configClass['images_thumbnail_width']?>" height="<?php echo $configClass['images_thumbnail_height']?>" />
																</td>
															</tr>
															<tr>
																<td class="key alignleft">
																	<input type="radio" name="tb_<?php echo $photo_id?>" id="tb_<?php echo $photo_id?>" value="1">
																	&nbsp;
																	<?php echo Text::_('OS_CREATE_THUMBNAIL_PHOTO_MANUALLY')?>
																</td>
															</tr>
															<tr>
																<td width="100%" class="center">>
																	<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/<?php echo $photo->image?>" id="tb_yui_img_<?php echo $photo_id?>"  width="<?php echo $origin_width?>"  height="<?php echo $origin_height?>" />
																	<input type="hidden" name="tb_h_<?php echo $photo_id?>" id="tb_h_<?php echo $photo_id?>" value="" />
																	<input type="hidden" name="tb_w_<?php echo $photo_id?>" id="tb_w_<?php echo $photo_id?>" value="" />
																	<input type="hidden" name="tb_t_<?php echo $photo_id?>" id="tb_t_<?php echo $photo_id?>" value="" />
																	<input type="hidden" name="tb_l_<?php echo $photo_id?>" id="tb_l_<?php echo $photo_id?>" value="" />
																</td>
																<td width="30%" valign="top">
																</td>
															</tr>
														</table>
														<script>
														( function() {
															var Dom = YAHOO.util.Dom,
															Event = YAHOO.util.Event,
															results = null;
		
															Event.onDOMReady( function() {
																var crop = new YAHOO.widget.ImageCropper('tb_yui_img_<?php echo $photo_id?>', {
																	initialXY: [20, 20],
																	initHeight:<?php echo $configClass['images_thumbnail_height']?>,
																	initWidth:<?php echo $configClass['images_thumbnail_width']?>,
																	useKeys:false,
																	keyTick: 5,
																	ratio:true,
																	shiftKeyTick: 50
																});
																crop.on('moveEvent',  function() {
																	var region = crop.getCropCoords();
																	Dom.get('tb_t_<?php echo $photo_id?>').value = region.top;
																	Dom.get('tb_l_<?php echo $photo_id?>').value = region.left;
																	Dom.get('tb_h_<?php echo $photo_id?>').value = region.height;
																	Dom.get('tb_w_<?php echo $photo_id?>').value = region.width;
																});
		
															});
														})();
													</script>
													</div>
													
													<?php
													//echo $pane->endPanel();
													?>
													<?php
		
													if(($medium_height < $origin_height) or ($medium_width < $origin_width)){
														
		
														if(($medium_height > $origin_height) and ($medium_width < $origin_width)){
															$height = $origin_height;
															$width = round($medium_width*$height/$medium_height);
														}elseif(($medium_height < $origin_height) and ($medium_width > $origin_width)){
															$width = $origin_width;
															$height = round($medium_height*$width/$medium_width);
														}else{
															$width = $medium_width;
															$height = $medium_height;
														}
														?>
														<div class="tab-pane" id="tab2<?php  echo $i?>">
														<?php
															//echo $pane->startPanel( Text::_('OS_MEDIUM_PHOTO'), 'tab2' );
															?>
															<table width="100%" class="admintable">
																<tr>
																	<td class="key alignleft">
																		<input type="radio" name="me_<?php echo $photo_id?>" id="me_<?php echo $photo_id?>" value="0">
																		&nbsp;
																		<?php echo Text::_('OS_MEDIUM_PHOTO_IS_CREATED_BY_OSPROPERTY')?>
																		
																	</td>
																</tr>
																<tr>
																	<td width="100%" class="center">
																		<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/medium/<?php echo $photo->image?>" width="<?php echo $configClass['images_large_width']?>" height="<?php echo $configClass['images_large_height']?>">
																	</td>
																</tr>
																<tr>
																	<td class="key alignleft">
																		<input type="radio" name="me_<?php echo $photo_id?>" id="me_<?php echo $photo_id?>" value="1">
																		&nbsp;
																		<?php echo Text::_('OS_CREATE_MEDIUM_PHOTO_MANUALLY')?>
																	</td>
																</tr>
																<tr>
																	<td width="100%" class="center">
																		<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/<?php echo $photo->image?>" id="me_yui_img_<?php echo $photo_id?>" width="<?php echo $origin_width?>"  height="<?php echo $origin_height?>" />
																		<input type="hidden" name="me_h_<?php echo $photo_id?>" id="me_h_<?php echo $photo_id?>" value="" />
																		<input type="hidden" name="me_w_<?php echo $photo_id?>" id="me_w_<?php echo $photo_id?>" value="" />
																		<input type="hidden" name="me_t_<?php echo $photo_id?>" id="me_t_<?php echo $photo_id?>" value="" />
																		<input type="hidden" name="me_l_<?php echo $photo_id?>" id="me_l_<?php echo $photo_id?>" value="" />
																	</td>
																	<td width="30%" valign="top">
																	</td>
																</tr>
															</table>
															<script>
															( function() {
																var Dom = YAHOO.util.Dom,
																Event = YAHOO.util.Event,
																results = null;
			
																Event.onDOMReady( function() {
																	var crop = new YAHOO.widget.ImageCropper('me_yui_img_<?php echo $photo_id?>', {
																		initialXY: [0, 0],
																		initHeight:<?php echo $height?>,
																		initWidth:<?php echo $width?>,
																		useKeys:false,
																		keyTick: 5,
																		ratio:true,
																		shiftKeyTick: 50
																	});
																	crop.on('moveEvent',  function() {
																		var region = crop.getCropCoords();
																		Dom.get('me_t_<?php echo $photo_id?>').value = region.top;
																		Dom.get('me_l_<?php echo $photo_id?>').value = region.left;
																		Dom.get('me_h_<?php echo $photo_id?>').value = region.height;
																		Dom.get('me_w_<?php echo $photo_id?>').value = region.width;
																	});
			
																});
															})();
															</script>
															<?php
															//echo $pane->endPanel();
															echo "</div>";
														}
														echo "</div>";
														?>
													</div>
											</td>
										</tr>
									</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
<?php
				}
			}
			?>
		</table>
		<input type="hidden" name="option" value="com_osproperty" /> 
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $id?>" />
		<input type="hidden" name="photoIds" id="photoIds" value="<?php echo $photoIds?>" />
		<input type="hidden" name="save" id="save" value="<?php echo $save?>" />
		</form>
		<?php
	}

			/**
	 * Update location form
	 *
	 * @param unknown_type $option
	 */
	static function updateLocationForm($option,$country,$lists){
		global $jinput, $mainframe,$langArr;
		ToolBarHelper::title(Text::_('OS_UPDATE_LOCATION_DATABASES')." [".$country->country_name."]");
		ToolBarHelper::save('properties_doimportlanguage',Text::_('OS_UPLOAD'));
		ToolBarHelper::cancel('');
		for($i=0;$i<count($langArr);$i++){
			if($country->id == $langArr[$i]->country_id){
				$file_name = $langArr[$i]->file_name;
			}
		}

		?>
		<script type="text/javascript">
		 function check_file(){
			str=document.getElementById('filename').value;
			file_mandatory_name = document.getElementById('file_mandatory_name').value;
			if(str.indexOf(file_mandatory_name) >= 0){
			}else{
				alert('<?php echo Text::_('OS_FILE_UPLOAD_MUST_BE')?> <?php echo $file_name?>');
				document.getElementById('filename').value = '';
				document.getElementById('uploaddiv').innerHTML = document.getElementById('uploaddiv').innerHTML;
			}
		}

		</script>
		
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table 	  width="100%" class="admintable">
			<tr>
				<td width="100%" class="padding20">
					<table  width="100%"  class="backgroundwhite" style="border-bottom:1px solid #CCC;border-right:1px solid #CCC;">
						<tr>
							<td width="100%" class="center colorred padding20" style="background-color:pink;">
								<?php
								printf(Text::_('OS_YOU_HAVE_STATES_CITIES'),$country->country_name,$country->nstates,$country->ncities,$country->country_name,$country->country_name);
								?>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
			<tr>
				<td width="100%" class="padding20">
					
					<table  width="100%" class="backgroundwhite" style="border-bottom:1px solid #CCC;border-right:1px solid #CCC;">
						<tr>
							<td width="100%" class="center padding20">
								<strong>
									<?php echo Text::_('OS_PLEASE_SELECT_DATABASE_FILE')?> (
									<?php
									echo $file_name;
									?>
									)
								</strong>
								<BR>
								<BR>
								<div id="uploaddiv">
									<input type="file" size="60" name="filename" id="filename" class="inputbox form-control" onchange="javascript:check_file()">
									<BR /><BR />
									<strong>
									<?php echo Text::_('OS_SELECT_STATUS');?> <?php echo $lists['state'];?>
									</strong>
								</div>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
			
		</table>
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" /> 
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="country_id" id="country_id" value="<?php echo $country->id?>" />
		<input type="hidden" name="file_mandatory_name" id="file_mandatory_name" value="<?php echo $file_name?>" />
		</form>
		<?php
	}

    /**
     * Remove Orphan Properties form
     */
    static function removeOrphanForm(){
        ToolBarHelper::title(Text::_('OS_REMOVE_ORPHAN_PROPERTIES'));
        ToolBarHelper::cancel('');
        ?>
        <form method="post" action="index.php" name="adminForm" id="adminForm">
            <table width="100%" style="border:1px solid red !important; background: pink !important;">
                <tr>
                    <td width="100%" class="center padding20">
                        <h2>
                            <?php echo Text::_('OS_REMOVE_ORPHAN_PROPERTIES');?>
                        </h2>
                        <?php echo Text::_('OS_REMOVE_ORPHAN_NOTICE');?>
                        <BR /><BR />
                        <a href="index.php?option=com_osproperty&task=properties_doremoveorphan" class="btn"><?php echo Text::_('OS_YES_AGREE');?></a>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="com_osproperty" />
            <input type="hidden" name="task" value="" />

        </form>
        <?php
    }

	public static function showInstallationComplete(){
		global $mainframe;
		?>
		<div class="row-fluid">
			<div class="span12">
				<div id="es-installer">
					<div class="box">
						<div class="box-hd">
							<div class="es-title">
								You are about to install <b>OS Property</b>.
							</div>

							<div class="es-social socialize">
								<div id="fb-root"></div>
								<script>( function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=406369119482668";
								fjs.parentNode.insertBefore(js, fjs);
								}(document, 'script', 'facebook-jssdk'));</script>
								<div class="fb-like" data-href="https://www.facebook.com/ossolution" data-width="90" data-layout="button_count" data-show-faces="false" data-send="false"></div>
							</div>

							<div class="es-logo">
								Another product by <a href="https://www.joomdonation.com" target="_blank"><img src="https://www.joomdonation.com/templates/ossolution/images/logo.png" alt="" style="height:22px !important;"></a>
							</div>

						</div>
						<!-- box-hd -->
						<div class="box-bd">
							<h1 class="es-title">
								Thank you for your recent purchase of OS Property.
							</h1>
							<p>
								Thank you for your recent purchase of OS Property and congratulations on making the choice to use the Best Real Estate Extension for Joomla!
							</p>

							<div class="actions">
								<a href="index.php?option=com_osproperty&amp;task=cpanel_list" class="btn btn-success btn-install">Go to OS Property Dashboard &raquo;</a>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	static function htmlStatistic($lists)
    {
        ToolBarHelper::title(Text::_('OS_STATISTIC'),"generic");
        ToolBarHelper::cancel('configuration_cancel')
        //heading statistic
        ?>
        <form method="POST" action="index.php?option=com_osproperty&task=properties_statistic" name="adminForm" id="adminForm" class="form-horizontal">
        <div class="row-fluid">
            <?php
            if(count($lists['newproperties']) > 0)
            {
            ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_NEW_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['newproperties'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=properties_edit&id=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_PROPERTY');?>">
                                    <?php echo $item->pro_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['modifiedproperties']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_JUST_MODIFIED_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['modifiedproperties'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=properties_edit&id=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_PROPERTY');?>">
                                    <?php echo $item->pro_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['topview']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_MOST_VIEWED_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['topview'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=properties_edit&id=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_PROPERTY');?>">
                                    <?php echo $item->pro_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['noview']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_NO_VIEWED_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['noview'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=properties_edit&id=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_PROPERTY');?>">
                                    <?php echo $item->pro_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <div class="row-fluid statisticbox2">
            <?php
            if(count($lists['newagents']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_NEW_AGENTS');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['newagents'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=agent_edit&cid[]=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_AGENT');?>">
                                    <?php echo $item->name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['newcompanies']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_NEW_COMPANIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['newcompanies'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=companies_edit&cid[]=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_COMPANY');?>">
                                    <?php echo $item->company_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['agentnoproperties']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_AGENTS_WITHOUT_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['agentnoproperties'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=agent_edit&cid[]=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_AGENT');?>">
                                    <?php echo $item->name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
            <?php
            if(count($lists['companynoproperties']) > 0)
            {
                ?>
                <div class="span3 statisticbox">
                    <strong>
                        <?php echo Text::_('OS_COMPANIES_WITHOUT_PROPERTIES');?>
                    </strong>
                    <div class="clearfix"></div>
                    <ul>
                        <?php
                        foreach($lists['companynoproperties'] as $item)
                        {
                            ?>
                            <li>
                                <a href="index.php?option=com_osproperty&task=companies_edit&cid[]=<?php echo $item->id;?>" title="<?php echo Text::_('OS_EDIT_COMPANY');?>">
                                    <?php echo $item->company_name;?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <input type="hidden" name="option" value="com_osproperty" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        </form>
        <?php
    }
}
?>
