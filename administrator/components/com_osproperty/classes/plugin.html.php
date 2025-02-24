<?php
/*------------------------------------------------------------------------
# plugin.php - OS Property
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;


class HTML_OspropertyPlugin{
	/**
	 * List plugins
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function listPlugins($option,$rows,$pageNav,$lists){
		global $mainframe,$configClass,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		ToolBarHelper::title(Text::_('OS_MANAGE_PAYMENT_PLUGINS'),"stack");
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'plugin_remove');
		ToolBarHelper::publish('plugin_publish');
		ToolBarHelper::unpublish('plugin_unpublish');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		$ordering = ($lists['order'] == 'ordering');
		?>
		<form action="index.php?option=com_osproperty&view=plugins&type=0" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
		<div class="<?php echo $rowFluidClass; ?>">
            <div class="<?php echo $span12Class;?> js-stools-container-bar">
                <div class="btn-wrapper input-append btn-group">
					<div class="input-group input-append">
						<input placeholder="<?php echo Text::_('OS_SEARCH');?>" type="text" id="keyword" name="keyword" value="<?php echo $mainframe->getUserState('pro_list.filter.keyword');?>" class="input-medium form-control" />
						<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
							<i class="icon-search"></i>
						</button>
					</div>
                </div>
            </div>
        </div>
		<div id="editcell">
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5">
						#
					</th>
					<th width="20">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="title">
						
					</th>
					<th class="title">
						<?php echo Text::_('OS_PLUGIN_NAME'); ?>
					</th>
					<th class="title" width="20%">
						<?php echo Text::_('OS_PLUGIN_TITLE'); ?>
					</th>			
					<th class="title">
						<?php echo Text::_('OS_PLUGIN_AUTHOR'); ?>
					</th>			
					<th class="title">
						<?php echo Text::_('OS_PLUGIN_EMAIL'); ?>
					</th>	
					<th style="text-align:center;">
						<?php echo Text::_('OS_PUBLISHED'); ?>
					</th>
					<th width="10%" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort',  'OS_ORDER', 'ordering', $lists['order_Dir'], $lists['order'] ); ?>
						<?php echo HTMLHelper::_('grid.order',  $rows , 'filesave.png', 'save_plugin_order' ); ?>
					</th>												
					<th style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('Id') , 'id', $lists['order_Dir'], $lists['order'] ); ?>
					</th>
				</tr>		
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++)
			{
				$row = &$rows[$i];
				$link 	= Route::_( 'index.php?option=com_osproperty&task=plugin_edit&cid[]='. $row->id );
				$checked 	= HTMLHelper::_('grid.id',   $i, $row->id );				
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'plugin_' );		
		
				//$img 	= $row->support_recurring_subscription ? 'tick.png' : 'publish_x.png';
				//$img = HTMLHelper::_('image','admin/'.$img, '', array('border' => 0), true);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<?php
						if(file_exists(JPATH_ROOT.'/images/osproperty/plugins/'.$row->name.'.png')){ 
						?>
							<a href="<?php echo $link; ?>" title="<?php echo $row->title; ?>">
								<img src="<?php echo Uri::root().'images/osproperty/plugins/'.$row->name.'.png'; ?>" width="100" />
							</a>
						<?php } ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>" title="<?php echo $row->title; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->title; ?>
					</td>												
					<td>
						<?php echo $row->author; ?>
					</td>
					<td align="center">
						<?php echo $row->author_email;?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $published ; ?>
					</td>			
					<td class="order" style="text-align:right;">
						<span><?php echo $pageNav->orderUpIcon( $i, true,'plugin_orderup', 'Move Up', $ordering ); ?></span>
						<span><?php echo $pageNav->orderDownIcon( $i, $n, true, 'plugin_orderdown', 'Move Down', $ordering ); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="input-mini" style="text-align: center;width:15px;" />
					</td>			
					<td align="center"  style="text-align:center;">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<table class="adminform" style="margin-top: 50px;">
				<tr>
					<td>
						<fieldset class="adminform form-horizontal options-form">
							<legend><?php echo Text::_('OS_INSTALL_NEW_PLUGIN'); ?></legend>
							<table>
								<tr>
									<td>
										<input type="file" name="plugin_package" id="plugin_package" size="50" class="inputbox form-control" /> <input type="button" class="btn btn-info" value="<?php echo Text::_('OS_INSTALL'); ?>" onclick="installPlugin();" />
									</td>
								</tr>
							</table>					
						</fieldset>
					</td>
				</tr>		
			</table>
			</div>
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" value="" id="task" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_( 'form.token' ); ?>				 
			<script type="text/javascript">
				function installPlugin() {
					var form = document.adminForm ;
					if (form.plugin_package.value =="") {
						alert("<?php echo Text::_('OS_CHOOSE_PLUGIN'); ?>");
						return ;	
					}
					
					form.task.value = 'plugin_install' ;
					form.submit();
				}
			</script>
		</form>
		<?php
	}
	
	
	/**
	 * Edit plugin
	 *
	 * @param unknown_type $option
	 * @param unknown_type $item
	 * @param unknown_type $params
	 */
	static function editPlugin($option,$item,$lists,$form){
		global $mainframe;
		OSPHelper::loadTooltip();
		if($item->id > 0){
			$type = "[".Text::_('OS_EDIT')."]";
		}else{
			$type = "[".Text::_('OS_ADD')."]";
		}
		ToolBarHelper::title(Text::_('OS_PLUGIN')." ".$type);
		ToolBarHelper::save('plugin_save');
		ToolBarHelper::apply('plugin_apply');
		ToolBarHelper::cancel('plugin_gotolist');
		?>
		<script language="javascript" type="text/javascript">
			<?php
				if (version_compare(JVERSION, '1.6.0', 'ge')) {
				?>
					Joomla.submitbutton = function(pressbutton)
					{
						var form = document.adminForm;
						if (pressbutton == 'plugin.cancel') {
							Joomla.submitform(pressbutton, form);
							return;				
						} else {
							//Validate the entered data before submitting													
							Joomla.submitform(pressbutton, form);
						}								
					}
				<?php	
				} else {
				?>
					function submitbutton(pressbutton) {
						var form = document.adminForm;
						if (pressbutton == 'cancel_plugin') {
							submitform( pressbutton );
							return;				
						} else {
							submitform( pressbutton );
						}
					}	
				<?php	
				}
			?>	
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<div class="col" style="float:left; width:65%">
			<fieldset class="adminform form-horizontal options-form">
				<legend><?php echo Text::_('OS_PLUGIN_DETAIL'); ?></legend>
					<table class="admintable adminform">
						<tr>
							<td width="100" align="right" class="key">
								<?php echo  Text::_('OS_NAME'); ?>
							</td>
							<td>
								<?php echo $item->name ; ?>
							</td>
						</tr>
						<tr>
							<td width="100" align="right" class="key">
								<?php echo  Text::_('OS_TITLE'); ?>
							</td>
							<td>
								<input class="input-large form-control" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $item->title;?>" />
							</td>
						</tr>					
						<tr>
							<td class="key">
								<?php echo Text::_('OS_AUTHOR'); ?>
							</td>
							<td>
								<input class="input-large form-control" type="text" name="author" id="author" size="40" maxlength="250" value="<?php echo $item->author;?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_CREATION_DATE'); ?>
							</td>
							<td>
								<?php echo $item->creation_date; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_COPYRIGHT') ; ?>
							</td>
							<td>
								<?php echo $item->copyright; ?>
							</td>
						</tr>	
						<tr>
							<td class="key">
								<?php echo Text::_('OS_LICENSE'); ?>
							</td>
							<td>
								<?php echo $item->license; ?>
							</td>
						</tr>							
						<tr>
							<td class="key">
								<?php echo Text::_('OS_AUTHOR_EMAIL'); ?>
							</td>
							<td>
								<?php echo $item->author_email; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_AUTHOR_URL'); ?>
							</td>
							<td>
								<?php echo $item->author_url; ?>
							</td>
						</tr>				
						<tr>
							<td class="key">
								<?php echo Text::_('OS_VERSION'); ?>
							</td>
							<td>
								<?php echo $item->version; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_DESCRIPTION'); ?>
							</td>
							<td>
								<input class="input-xxlarge" type="text" name="description" id="description"  value="<?php echo $item->description;?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo Text::_('OS_PUBLISHED'); ?>
							</td>
							<td>
								<?php					
									echo $lists['published'];					
								?>						
							</td>
						</tr>
				</table>
			</fieldset>				
		</div>						
		<div class="col" style="float:left; width:35%">
			<fieldset class="adminform form-horizontal options-form">
				<legend><?php echo Text::_('OS_PLUGIN_PARAMETERS'); ?></legend>
				<?php
					foreach ($form->getFieldset('basic') as $field) {
					?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $field->label ;?>
						</div>					
						<div class="controls">
							<?php echo  $field->input ; ?>
						</div>
					</div>	
				<?php
					}					
				?>				
			</fieldset>				
		</div>
				
		<div class="clr"></div>	
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="cid[]" value="<?php echo $item->id; ?>" />
			<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>