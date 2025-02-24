<?php

/*------------------------------------------------------------------------
# email.html.php - Ossolution Property
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

class HTML_OspropertyEmailBackend{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function email_list($option,$rows,$pageNav,$lists){
		global $mainframe,$_jversion,$jinput;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_EMAIL_FORMS'),"envelope");
		ToolBarHelper::editList('email_edit');
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'email_remove');
		ToolBarHelper::publish('email_publish');
		ToolBarHelper::unpublish('email_unpublish');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=email_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%">
                    <DIV class="btn-wrapper btn-group">
						<div class="input-group input-append">
							<input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
							<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
								<i class="icon-search"></i>
							</button>
						</div>
                    </DIV>
				</td>
			</tr>
		</table>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%">
				
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_SUBJECT'), 'email_title', @$lists['order_Dir'], @$lists['order'] ,'email_list'); ?>
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_KEY'), 'email_key', @$lists['order_Dir'], @$lists['order'] ,'email_list'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['order_Dir'], @$lists['order'],'email_list' ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="5" style="text-align:center;">
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
				$link 		= Route::_( 'index.php?option=com_osproperty&task=email_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'email_');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
				
					<td align="center"><?php echo $pageNav->getRowOffset( $i ); ?></td>
					
					<td align="center" style="text-align:center;"><?php echo $checked; ?></td>
					
					<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->email_title; ?></a></td>
					
					<td align="left"><?php echo $row->email_key ?> </td>
					
					<td align="center" style="text-align:center;"><?php echo $published?></td>
				</tr>
			<?php
				$k = 1 - $k;	
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="email_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>"  />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>"  />
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
	static function editHTML($option,$row,$lists,$translatable)
	{
		global $mainframe,$_jversion,$languages,$jinput;
		$jinput->set( 'hidemainmenu', 1 );
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_EMAIL').$title);
		ToolBarHelper::save('email_save');
		ToolBarHelper::apply('email_apply');
		ToolBarHelper::cancel('email_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>	
		<table width="100%" class="admintable" >
			<tr>
				<td class="key"><?php echo Text::_('OS_KEY')?></td>
				<td ><input type="text" name="email_key" id="email_key" disabled="disabled"  size="40" value="<?php echo $row->email_key?>" class="input-large form-control" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo Text::_('OS_SUBJECT'); ?></td>
				<td ><input type="text" name="email_title" id="email_title" size="40" value="<?php echo $row->email_title?>" class="input-large form-control" /></td>
			</tr>
			<tr>
				<td class="key"><?php echo Text::_('OS_PUBLISHED'); ?></td>
				<td ><?php echo $lists['published'];?></td>
			</tr>
			
			<tr>
				<td class="key" valign="top"><?php echo Text::_('OS_CONTENT'); ?></td>
				<td >
					<?php
					$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
					echo $editor->display( 'email_content',  stripslashes($row->email_content) , '95%', '250', '75', '20' ) ;
					?>
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
										<td class="key"><?php echo Text::_('OS_SUBJECT'); ?></td>
										<td ><input type="text" name="email_title_<?php echo $sef; ?>" id="email_title_<?php echo $sef; ?>" size="40" class="input-large form-control" value="<?php echo $row->{'email_title_'.$sef}?>" class="input-large"></td>
									</tr>
									<tr>
										<td class="key" valign="top"><?php echo Text::_('OS_CONTENT'); ?></td>
										<td >
											<?php
											echo $editor->display( 'email_content_'.$sef,  stripslashes($row->{'email_content_'.$sef}) , '95%', '250', '75', '20' ) ;
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
		<input type="hidden" name="id" value="<?php echo (int) $row->id?>" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		</form>
		
		<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton){
			var form = document.adminForm;
			if (pressbutton == 'email_cancel'){
				Joomla.submitform( pressbutton );
				return;
			}else if (form.email_title.value == ''){
				alert('<?php echo Text::_('OS_PLEASE_ENTER_SUBJECT'); ?>');
				form.email_title.focus();
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
