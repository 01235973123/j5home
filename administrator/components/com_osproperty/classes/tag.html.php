<?php
/*------------------------------------------------------------------------
# tag.html.php - Ossolution Property
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


class HTML_OspropertyTag{
	/**
	 * List tags
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $lists
	 */
	static function listTags($option,$rows,$lists,$pageNav){
		global $mainframe,$configClass,$jinput;
		HTMLHelper::_('behavior.multiselect');
		
		ToolBarHelper::title(Text::_('OS_MANAGE_TAGS'),"tags");
		ToolBarHelper::addNew('tag_add');
		if (count($rows)){
			ToolBarHelper::editList('tag_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'tag_remove');
			ToolBarHelper::publish('tag_publish');
			ToolBarHelper::unpublish('tag_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=tag_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="50%">
                    <DIV class="btn-wrapper btn-group">
						<div class="input-group input-append">
							<input type="text" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH');?>" value="<?php echo $jinput->getString('keyword','')?>" class="input-medium form-control" />
							<button class="btn btn-primary hasTooltip" title="" type="submit" data-original-title="<?php echo Text::_('OS_SEARCH');?>">
								<i class="icon-search"></i>
							</button>
						</div>
						
                    </DIV>
				</td>
				<td width="50%" style="text-align:right;float:right;">
					<?php echo $lists['status'];?>
				</td>
			</tr>
		</table>
        <?php
        if(count($rows) > 0) {
        ?>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5%">
				
					</th>
					<th width="5%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						
					</th>
					<th width="35%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_TAG'), 'a.keyword', @$lists['order_Dir'], @$lists['order'] ,'tag_list'); ?>
					</th>
					<th width="35%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_ITEM'), 'count_tag', @$lists['order_Dir'], @$lists['order'] ,'tag_list'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'a.published', @$lists['order_Dir'], @$lists['order'] ,'tag_list'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'a.id', @$lists['order_Dir'], @$lists['order'] ,'tag_list'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="6" style="text-align:center;">
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
				$link 		= Route::_( 'index.php?option=com_osproperty&task=tag_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'tag_');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="left" style="padding-left: 10px;">
						<a href="<?php echo $link; ?>">
							<?php echo $row->keyword; ?>
						</a>
						<BR />
					</td>
					<td align="left" style="text-align:center;">
						<?php echo $row->count_tag; ?>
					</td>
					<td align="center" style="text-align:center;"> 
						<?php echo $published?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $row->id?>
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
		<input type="hidden" name="task" value="tag_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
		</form>
		<?php
	}
	
	/**
	 * Add/edit tag
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
		ToolBarHelper::title(Text::_('OS_TAG').$title);
		ToolBarHelper::save('tag_save');
		ToolBarHelper::save2new('tag_new');
		ToolBarHelper::apply('tag_apply');
		ToolBarHelper::cancel('tag_cancel');

        $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
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
					<?php echo Text::_('OS_TAG'); ?>
				</td>
				<td>
					<input type="text" name="keyword" id="keyword" size="40" value="<?php echo $row->keyword;?>" class="input-large form-control ilarge" />
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
								<table width="100%" class="admintable" >
									<tr>
										<td class="key">
											<?php echo Text::_('OS_TAG'); ?>
										</td>
										<td>
											<input type="text" name="keyword_<?php echo $sef; ?>" id="keyword_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'keyword_'.$sef};?>" class="input-large ilarge form-control" />
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
		Joomla.submitbutton = function(pressbutton)
		{
			form = document.adminForm;
			if (pressbutton == 'tag_cancel'){
                Joomla.submitform( pressbutton );
				return;
			}else if (form.keyword.value == ''){
				alert('<?php echo Text::_('OS_PLEASE_ENTER_PROPERTY_TAG'); ?>');
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
