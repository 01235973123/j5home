<?php

/*------------------------------------------------------------------------
# agent.html.php - Ossolution Property
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
use Joomla\CMS\Editor\Editor;

class HTML_OspropertyAgent{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function agent_list($option,$rows,$pageNav,$lists)
	{
		global $jinput, $mainframe,$configClass,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		$span6Class    = $bootstrapHelper->getClassMapping('span6');
		$span4Class    = $bootstrapHelper->getClassMapping('span4');
		$span8Class    = $bootstrapHelper->getClassMapping('span8');
		HTMLHelper::_('behavior.multiselect');
		//HTMLHelper::_('behavior.modal', 'a.osmodal');
		ToolBarHelper::title(Text::_('OS_MANAGE_AGENTS'),"user");
		ToolBarHelper::addNew('agent_add');
		if (count($rows)){
			ToolBarHelper::editList('agent_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'agent_remove');
			ToolBarHelper::publish('agent_publish');
			ToolBarHelper::unpublish('agent_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		
		$tmpl = $jinput->getString('tmpl','');
		if($tmpl == "component"){
			$tmpl_url = "&tmpl=component";
		}else{
			$tmpl_url = "";
		}

		$listOrder	= $lists['filter_order'];
		$listDirn	= $lists['filter_order_Dir'];

		$saveOrder	= $listOrder == 'a.ordering';
		$ordering	= ($listOrder == 'a.ordering');

		if ($saveOrder)
		{
			$saveOrderingUrl = 'index.php?option=com_osproperty&task=agent_saveorderAjax';
			if (OSPHelper::isJoomla4())
			{
				HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				HTMLHelper::_('sortablelist.sortable', 'agentList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
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
			$ordering= [];
			foreach ($rows as $item)
			{
				$ordering[$item->parent_id][] = $item->id;
			}
		}

		?>
		<form method="POST" action="index.php?option=com_osproperty&task=agent_list&layout=<?php echo $jinput->getString('layout','')?><?php echo $tmpl_url?>" name="adminForm" id="adminForm">
			<div class="<?php echo $rowFluidClass; ?> js-stools clearfix">
				<div class="<?php echo $span4Class;?> js-stools-container-bar">
					<div class="filter-search btn-group pull-left input-append">
						<input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
						<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
							<i class="icon-search"></i>
						</button>
					</DIV>
				</div>
				<div class="<?php echo $span8Class;?> pull-right js-stools-container-list hidden-phone hidden-tablet shown">
					<div class="btn-group pull-right">
						<?php echo OSPHelper::loadAgentTypeDropdownFilter($jinput->getInt('agent_type',-1),'imedium form-select input-medium','onChange="javascript:document.adminForm.submit();"');?>
						<?php echo $lists['filter_company'];?>
						<?php echo $lists['filter_request']; ?>
						<?php
						echo $pageNav->getLimitBox();
						?>
					</div>
				</div>
			</div>
		<div id="editcell">
        <?php
        if(count($rows) > 0) {
        ?>
		<table width="100%" class="adminlist table table-striped" id="agentList">
			<thead>
				<tr>
					<?php
					if($tmpl != "component"){
						?>
						<th width="5%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="3%" class="center">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('Jglobal $jinput,_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
					<?php } ?>
					<th width="7%" class="center">
						<?php echo Text::_("OS_IMAGE")?>
					</th>
					
					<th width="5%" class="center">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_TYPE'), 'a.agent_type', @$lists['filter_order_Dir'], @$lists['order'] ,'agent_list'); ?>
					</th>
					
					<th width="15%" class="left">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_NAME'), 'a.name', @$lists['filter_order_Dir'], @$lists['filter_order'] ,'agent_list'); ?>
					</th>
					
					<th width="10%" class="left">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('Joomla User'), 'u.username', @$lists['filter_order_Dir'], @$lists['filter_order'],'agent_list' ); ?>
					</th>
					<th width="10%" class="left">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_COMPANY'), 'c.company_name', @$lists['filter_order_Dir'], @$lists['filter_order'],'agent_list' ); ?>
					</th>
					<?php
					if(!OSPHelper::isJoomla4()){	
					?>
					<th width="10%" class="left">
						<?php echo HTMLHelper::_('searchtools.sort',   Text::_('OS_EMAIL'), 'a.email', @$lists['filter_order_Dir'], @$lists['filter_order'],'agent_list' ); ?>
					</th>
					<?php
					}
					if($tmpl != "component"){
						?>
						<th width="15%" class="center">
							<?php echo Text::_('OS_FEATURED')."/ ".Text::_('OS_PUBLISH')."/ ".Text::_('OS_DEFAULT'); ?>
						</th>
						<?php
						if($configClass['auto_approval_agent_registration']==0){
						?>
						<th width="10%" class="center">
							<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_APPROVED'), 'a.request_to_approval', @$lists['filter_order_Dir'], @$lists['filter_order'],'agent_list' ); ?>
						</th>
						<?php
						}
						?>
					<?php 
					} 
					?>
					<th class="center" width="3%">
					<?php echo HTMLHelper::_('grid.sort',   'ID', 'a.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ,'agent_list'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="14" class="center">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
			<?php
			$k = 0;
			$canChange = true;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=agent_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i , 'agent_');
				$img 		= $row->request_to_approval ? 'tick.png' : 'publish_x.png';
				$alt		= Text::_( 'OS_REQUEST_TO_APPROVAL' );
				$request_to_approval = HTMLHelper::_('image','admin/'.$img, $alt, NULL, true);
				?>
				<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->company_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
					<?php
					if($tmpl != "component"){
						?>
						<td class="order nowrap center hidden-phone">
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
						
						<td align="center" class="center"><?php echo $checked; ?></td>
					<?php } ?>
					<td align="center" class="center">
						<?php
						
						if(file_exists(JPATH_ROOT.'/images/osproperty/agent/thumbnail'.DS.$row->photo) && $row->photo != "")
						{
						?>
							<a target="_blank" href="<?php echo PATH_URL_PHOTO_AGENT_FULL; ?><?php echo $row->photo?>">
								<img alt="" style="height:55px;" src="<?php echo PATH_URL_PHOTO_AGENT_THUMB?><?php echo $row->photo?>">
							</a>
						<?php
						}
						else
						{
							?>
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.jpg" style="height:55px" />
							<?php
						}
						?>
					</td>
					<td align="center" class="center">
						<?php
						echo OSPHelper::loadAgentType($row->id);
						?>
					</td>
					<td align="left">
						<?php
						if($tmpl == "component"){
						?>
						<a class="pointer" onclick="if (window.parent) window.parent.jSelectUser_agent_id('<?php echo $row->id?>', '<?php echo str_replace("'","\'",$row->name);?>');">
						<?php	
						}else{
						?>
						<a href="<?php echo $link; ?>">
						<?php
						}
						?>
							<?php echo $row->name; ?>
						</a>
						<BR />
						(Alias: <?php echo $row->alias;?>)
					</td>
					
					<td align="left"><?php echo $row->username?> </td>
					
					<td><?php 
					if($row->company_name != "")
					{
						echo $row->company_name;
					}else{
						echo "--";
					}
					?></td>
					<?php
					if(!OSPHelper::isJoomla4()){	
					?>
						<td><a href="mailto:<?php echo $row->email?>" target="_blank"><?php echo $row->email?></a></td>
					<?php
					}
					if($tmpl != "component"){
					?>
						<td align="center" class="center">
							<?php
							if(OSPHelper::isJoomla4())
							{
							?>
								<div class="btn-group">
									<?php
									if($row->featured == 1)
									{
										?>
										<a class="btn btn-micro btn-secondary active hasTooltip colororange" href="index.php?option=com_osproperty&task=agent_changeunfeatured&cid[]=<?php echo $row->id?>&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
											  <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
											</svg>
										</a>
										<?php
									}
									else
									{
										?>
										<a class="btn btn-micro btn-secondary active hasTooltip colorblack" href="index.php?option=com_osproperty&task=agent_changefeatured&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
											  <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
											</svg>
										</a>
										<?php
									}
									?>
									<div class="btn btn-secondary">
										<?php echo $published?>
									</div>
									<?php
									if($row->default_agent == 1)
									{
										?>
										<a class="btn btn-micro btn-secondary active hasTooltip colororange" href="index.php?option=com_osproperty&task=agent_changedefault&status=0&cid[]=<?php echo $row->id?>&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_DEFAULT_STATUS_FOR_THIS_USER');?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16">
											  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
											</svg>
										</a>
										<?php
									}
									else
									{
										?>
										<a class="btn btn-micro btn-secondary active hasTooltip colorblack" href="index.php?option=com_osproperty&task=agent_changedefault&status=1&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_DEFAULT_STATUS_FOR_THIS_USER');?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square-fill" viewBox="0 0 16 16">
											  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708z"/>
											</svg>
										</a>
										<?php
									}
									?>
								</div>
							<?php
							}
							else
							{
							?>
								<div class="btn-group">
									<?php
									if($row->featured == 1)
									{
										?>
										<a class="btn btn-micro active hasTooltip colororange" href="index.php?option=com_osproperty&task=agent_changeunfeatured&cid[]=<?php echo $row->id?>&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
											<i class="osicon-star"></i>
										</a>
										<?php
									}
									else
									{
										?>
										<a class="btn btn-micro active hasTooltip colorblack" href="index.php?option=com_osproperty&task=agent_changefeatured&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_FEATURED_STATUS');?>">
											<i class="osicon-star"></i>
										</a>
										<?php
									}
									?>
									<?php echo $published?>
									<?php
									if($row->default_agent == 1)
									{
										?>
										<a class="btn btn-micro active hasTooltip colororange" href="index.php?option=com_osproperty&task=agent_changedefault&status=0&cid[]=<?php echo $row->id?>&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_DEFAULT_STATUS_FOR_THIS_USER');?>">
											<i class="osicon-star"></i>
										</a>
										<?php
									}
									else
									{
										?>
										<a class="btn btn-micro active hasTooltip colorblack" href="index.php?option=com_osproperty&task=agent_changedefault&status=1&cid[]=<?php echo $row->id?>&v=1&limitstart=<?php echo $pageNav->limitstart?>&limit=<?php echo $pageNav->limit?>" title="<?php echo Text::_('OS_CHANGE_DEFAULT_STATUS_FOR_THIS_USER');?>">
											<i class="osicon-star"></i>
										</a>
										<?php
									}
									?>
								</div>
							<?php } ?>
						</td>
						<?php
						if($configClass['auto_approval_agent_registration']==0)
						{
						?>
						<td align="center" class="center">
							<?php 
								if($row->request_to_approval == 1)
								{
									?>
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hourglass" viewBox="0 0 16 16">
									  <path d="M2 1.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1h-11a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1-.5-.5m2.5.5v1a3.5 3.5 0 0 0 1.989 3.158c.533.256 1.011.791 1.011 1.491v.702c0 .7-.478 1.235-1.011 1.491A3.5 3.5 0 0 0 4.5 13v1h7v-1a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351v-.702c0-.7.478-1.235 1.011-1.491A3.5 3.5 0 0 0 11.5 3V2z"/>
									</svg>
									<?php
								}
								else
								{
									?>
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
  <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
  <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
</svg>
									<?php
								}
							?>
						</td>
						<?php
						}
					}//tmpl
					?>
					<td  class="center">
						<?php echo $row->id;?>
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
		<input type="hidden" name="task" value="agent_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order"  id="filter_order" value="<?php echo $lists['filter_order']; ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
		<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}
	
	
	/**
	 * Agent field
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editHTML($option,$row,$lists,$translatable)
	{
		global $jinput, $mainframe,$_jversion,$configClass,$languages;
        $jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_AGENT').'/'.Text::_('OS_OWNER').$title);
		ToolBarHelper::save('agent_save');
		ToolBarHelper::save2new('agent_new');
		ToolBarHelper::apply('agent_apply');
		ToolBarHelper::cancel('agent_cancel');
		
		$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		if (version_compare(JVERSION, '3.5', 'ge')){
		?>
			<script src="<?php echo Uri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
		<?php } ?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
			<table  width="100%" class="admintable backgroundwhite">
				<tr>
					<td width="65%" valign="top">
						<fieldset class="fieldset_detail form-horizontal options-form" >
							<legend><?php echo Text::_("OS_DETAILS")?></legend>
							<table width="100%" >
								<tr>
									<td class="key"><?php echo Text::_('OS_TYPE'); ?></td>
									<td width="80%">
										<?php OSPHelper::loadAgentTypeDropdown($row->agent_type,"input-medium form-select","");?>
									</td>
								</tr>
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
                                    <td class="key"><?php echo Text::_('OS_EMAIL'); ?></td>
                                    <td width="80%"><input type="text" name="email" id="email" size="40" value="<?php echo $row->email?>" class="input-medium form-control ilarge"></td>
                                </tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_NAME'); ?></td>
									<td width="80%"><input type="text" name="name" id="name" size="40" value="<?php echo $row->name?>" class="input-medium form-control ilarge"></td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_ALIAS'); ?></td>
									<td width="80%"><input type="text" name="alias" id="alias" size="40" value="<?php echo $row->alias?>" class="input-medium form-control ilarge"></td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_COMPANY'); ?></td>
									<td width="80%"><?php echo $lists['company_id']; ?></td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_LICENSE'); ?></td>
									<td width="80%"><input type="text" name="license" id="license" size="40" value="<?php echo $row->license?>" class="input-medium form-control ilarge" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_PUBLISHED'); ?></td>
									<td width="80%"><?php echo $lists['published'];?></td>
								</tr>
								<tr>
									<td class="key" valign="top"><?php echo Text::_('OS_BIO'); ?></td>
									<td width="80%" >
										<?php
										echo $editor->display( 'bio',  stripslashes($row->bio) , '95%', '250', '75', '20' ) ;
										?>
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
					<td width="35%" valign="top">
						<fieldset class="fieldset_photo form-horizontal options-form">
							<legend><?php echo Text::_('OS_PHOTO')?></legend>
							<table width="100%">
								<tr>
									<td width="100%">
										<?php if ($row->id && $row->photo){?>
											<a target="_blank" href="<?php echo PATH_URL_PHOTO_AGENT_FULL; ?><?php echo $row->photo?>">
												<img style="width: 150px;" alt="" src="<?php echo PATH_URL_PHOTO_AGENT_THUMB?><?php echo $row->photo?>" />
											</a>
											<div class="clearfix"></div>
											<input type="checkbox" name="remove_photo" value="1"><?php echo Text::_('OS_REMOVE_PHOTO')?>
											<div class="clearfix"></div>
											
										<?php }?>
										<input type="file" name="file_photo" id="file_photo" size="40" onchange="javascript:checkUploadPhotoFiles('file_photo')" class="input-large form-control" /> 
										<div class="clearfix"></div>
										(<?php echo Text::_('OS_IMAGE_TYPES_SUPPORTED');?>)
										<input type="hidden" name="photo" id="photo" value="<?php echo $row->photo?>" />
									</td>
								</tr>
							</table>
						</fieldset>
						<fieldset class="fieldset_web form-horizontal options-form">
							<legend><?php echo Text::_('OS_USER_ADDRESS')?></legend>
							<table width="100%">
								<?php
								if(HelperOspropertyCommon::checkCountry()){
								?>
								<tr>
									<td class="key"><?php echo Text::_('OS_COUNTRY'); ?></td>
									<td ><?php echo $lists['country']; ?></td>
								</tr>
								<?php
								}else{
									echo $lists['country'];
								}
								?>
								<tr>
									<td class="key"><?php echo Text::_('OS_STATE'); ?></td>
									<td >
										<div id="country_state">
											<?php echo $lists['states']; ?>
										</div>
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_CITY'); ?></td>
									<td >
										<div id="city_div">
											<?php echo $lists['city']?>
										</div>
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo Text::_('OS_ADDRESS'); ?></td>
									<td ><input type="text" name="address" id="address" size="40" value="<?php echo $row->address?>" class="input-large form-control ilarge"/></td>
								</tr>
							</table>
						</fieldset>

						<fieldset class="fieldset_web form-horizontal options-form">
							<legend><?php echo Text::_('OS_OTHER_FIELDS')?></legend>
							<table width="100%">
								
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/phone.jpg"></td>
									<td><input type="text" name="phone" id="phone" size="40" value="<?php echo $row->phone?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('OS_PHONE');?>"></td>
								</tr>
								
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/mobile.jpg"></td>
									<td><input type="text" name="mobile" id="mobile" size="40" value="<?php echo $row->mobile?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('OS_MOBILE');?>" /></td>
								</tr>
								<?php
								if($configClass['show_agent_fax'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/fax.jpg"></td>
									<td><input type="text" name="fax" id="fax" size="40" value="<?php echo $row->fax?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('OS_FAX');?>" /></td>
								</tr>
								<?php
								}
								if($configClass['show_agent_skype'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/skype.jpg"> </td>
									<td width="70%"><input type="text" name="skype" id="skype" size="40" maxlength="100" value="<?php echo $row->skype?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('Skype');?>"/></td>
								</tr>
								<?php } 
								if($configClass['show_agent_msn'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/msn.jpg"> </td>
									<td width="70%"><input type="text" name="msn" id="msn" size="40" maxlength="100" value="<?php echo $row->msn?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('Line Messages');?>"/></td>
								</tr>
								<?php } 
								if($configClass['show_agent_linkin'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/linkin.jpg"> </td>
									<td width="70%"><input type="text" name="yahoo" id="yahoo" size="40" maxlength="100" value="<?php echo $row->yahoo;?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('OS_LINKEDIN');?>"/></td>
								</tr>
								<?php } 
								if($configClass['show_agent_gplus'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/gplus.jpg"> </td>
									<td width="70%"><input type="text" name="gtalk" id="gtalk" size="40" maxlength="100" value="<?php echo $row->gtalk?>" class="input-medium form-control imedium"  placeholder="<?php echo Text::_('Google Plus');?>"/></td>
								</tr>
								<?php } 
								if($configClass['show_agent_facebook'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/facebook.jpg"> </td>
									<td width="70%"><input type="text" name="facebook" id="facebook" size="40" maxlength="100" value="<?php echo $row->facebook?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('Facebook');?>" /></td>
								</tr>
								<?php } 
								if($configClass['show_agent_twitter'] == 1)
								{
								?>
								<tr>
									<td class="key" class="center"><img alt="" src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/twitter.jpg"> </td>
									<td width="70%"><input type="text" name="aim" id="aim" size="40" maxlength="100" value="<?php echo $row->aim?>" class="input-medium form-control imedium" placeholder="<?php echo Text::_('Twitter');?>" /></td>
								</tr>
								<?php } ?>
							</table>
						</fieldset>
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
										<td class="key" valign="top"><?php echo Text::_('OS_BIO'); ?></td>
										<td width="80%" >
											<?php
											echo $editor->display( 'bio_'.$sef,  stripslashes($row->{'bio_'.$sef}) , '95%', '250', '75', '20',false ) ;
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
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		</form>
		<script type="text/javascript">
            function showDiv(item1, item2)
            {
                jQuery("#" + item1).show('slow');
                jQuery("#" + item2).hide('slow');
            }
			var live_site = '<?php echo Uri::root()?>';

			jQuery(document).ready(function () {
				function init() {
					jQuery('#username').val("");
					jQuery('#password').val("");
				}
				init();

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
				var user_id = document.getElementById('user_id_id');
                if(user_id != null) {
                    user_id = user_id.value;
                }else{
                    user_id = document.adminForm.user_id.value;
                }
				user_id = parseInt(user_id);
				var username = document.getElementById('username');
				var password = document.getElementById('password');

				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if (pressbutton == 'agent_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.name.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_AGENT_NAME'); ?>');
					form.name.focus();
					return;
				<?php
				if($configClass['joomlauser'] == 0)
				{
				?>
				}else if ((user_id == 0) && (username.value == "") && (password.value == "")){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_OR_CREATE_NEW_JOOMLA_USER_FOR_THIS_AGENT'); ?>');
					form.username.focus();
					return;
				<?php
				}	
				?>
				}else if (form.email.value != "" && !filter.test(form.email.value)){
					alert('<?php echo Text::_('OS_EMAIL_INVALID'); ?>');
					form.email.value = '';
					form.email.focus();
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
