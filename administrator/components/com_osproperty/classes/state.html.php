<?php
/*------------------------------------------------------------------------
# state.html.php - Ossolution Property
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


class HTML_OspropertyState{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function state_list($option,$rows,$pageNav,$lists,$modal,$keyword){
		global $mainframe,$_jversion,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class   = $bootstrapHelper->getClassMapping('span12');
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_STATES'),"folder");
		ToolBarHelper::addNew('state_add');
		if (count($rows)){
			ToolBarHelper::editList('state_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'state_remove');
			ToolBarHelper::publish('state_publish');
			ToolBarHelper::unpublish('state_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=state_list" name="adminForm" id="adminForm">
		<div class="<?php echo $rowFluidClass; ?>">
            <div class="<?php echo $span12Class; ?> js-stools-container-bar">
                <div class="btn-wrapper btn-group">
					<div class="input-group">
						<input type="text" name="keyword" value="<?php echo $keyword;?>" class="input-medium form-control"/>
						<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
							<i class="icon-search"></i>
						</button>
						&nbsp;
						<?php echo $lists['country']; ?>
					</div>
					
				</div>
			</div>
		</div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%">
				
					</th>
					<th width="3%" style="text-align:center;">
						
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_STATE'), 'state_name', @$lists['order_Dir'], @$lists['order'] ,'state_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_STATE_CODE'), 'state_code', @$lists['order_Dir'], @$lists['order'] ,'state_list'); ?>
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COUNTRY'), 'country_name', @$lists['order_Dir'], @$lists['order'] ,'state_list'); ?>
					</th>
					<?php
					if($modal == 0){
					?>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['order_Dir'], @$lists['order'] ,'state_list'); ?>
					</th>
					<?php
					}
					?>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'id', @$lists['order_Dir'], @$lists['order'] ,'state_list'); ?>
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
			<tbody>
			<?php
			$db = Factory::getDBO();
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=state_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'state_');
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="left">
					<?php
					if($modal == 0){
					?>
						<a href="<?php echo $link; ?>">
					<?php
					}else{
					?>
						<a class="pointer" onclick="if (window.parent) window.parent.jSelectState_state('<?php echo $row->id?>', '<?php echo $row->state_name; ?>');">
					<?php
					}
					?>
							<?php echo $row->state_name; ?>
						</a>
					</td>
					<td align="left" style="padding-left: 10px;">
						<?php echo $row->state_code?>
					</td>
					<td align="left">
						<?php echo $row->country_name?>
					</td>
					<?php
					if($modal == 0){
					?>
						<td align="center" style="text-align:center;">
							<?php echo $published;?>
						</td>
					<?php
					}
					?>
					<td style="text-align:center;">
						<?php echo $row->id;?>
					</td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="state_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>"  />
		<input type="hidden" name="modal" value="<?php echo $modal?>" />
		<?php
		if($modal == 1){
		?>
			<input type="hidden" name="tmpl" id="tmpl" value="component" />
		<?php
		}
		?>
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
		global $mainframe,$languages,$jinput;
		$jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('State').$title);
		ToolBarHelper::save('state_save');
		ToolBarHelper::save2new('state_new');
		ToolBarHelper::apply('state_apply');
		ToolBarHelper::cancel('state_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
			<table cellpadding="0" cellspacing="0" width="100%" class="admintable" >
				<tr>
					<td class="key">
						<?php echo Text::_('OS_COUNTRY'); ?>
					</td>
					<td>
						<?php echo $lists['country_id']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_STATE'); ?>
					</td>
					<td>
						<input type="text" name="state_name" id="state_name" class="input-large form-control" size="40" value="<?php echo $row->state_name?>">
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_STATE_CODE')?>
					</td>
					<td>
						<input type="text" name="state_code" id="state_code" class="input-large form-control" size="40" value="<?php echo $row->state_code?>">
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PUBLISH')?>
					</td>
					<td>
						<?php echo $lists['published'];?>
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
											<?php echo Text::_('OS_STATE'); ?>
										</td>
										<td>
											<input type="text" class="input-large form-control" name="state_name_<?php echo $sef; ?>" id="state_name_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'state_name_'.$sef}?>">
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
		<?php if ($_jversion == "1.5"){?>
			function submitbutton(pressbutton)
		<?php }else{?>
			Joomla.submitbutton = function(pressbutton)
		<?php }?>
			{
				form = document.adminForm;
				if (pressbutton == 'state_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.state_name.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_STATE'); ?>');
					form.state_name.focus();
					return;
				}else if (form.country_id.value == '0'){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_COUNTRY'); ?>');
					form.country_id.focus();
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