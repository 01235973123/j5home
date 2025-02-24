<?php
/*------------------------------------------------------------------------
# comment.html.php - Ossolution Property
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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

class HTML_OspropertyComment{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function comment_list($option,$rows,$pageNav,$lists){
		global $jinput, $mainframe,$_jversion;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_COMMENTS'),"comment");
		ToolBarHelper::addNew('comment_add');
		if (count($rows)){
			ToolBarHelper::editList('comment_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'comment_remove');
			ToolBarHelper::publish('comment_publish');
			ToolBarHelper::unpublish('comment_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		
		<form method="POST" action="index.php?option=com_osproperty&task=comment_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%">
                    <DIV class="btn-wrapper button-group">
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
        <?php
        if(count($rows) > 0) {
        ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="2%">
						#
					</th>
					<th width="3%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('Jglobal $jinput,_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COMMENT_TITLE'), 'c.title', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_AUTHOR'), 'c.name', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_RATE'), 'c.rate', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_CREATED'), 'c.created_on', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="20%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PROPERTY'), 'p.pro_name', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_IP_ADDRESS'), 'c.ip_address', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'c.published', @$lists['order_Dir'], @$lists['order'] ,'comment_list'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="9" style="text-align:center;" align="center">
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
				$link 		= Route::_( 'index.php?option=com_osproperty&task=comment_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'comment_');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center">
						<?php echo $checked; ?>
					</td>
					<td align="left" style="padding-left: 10px;">
						<a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
						</a>
					</td>
					<td style="padding-left: 10px;">
						<?php echo $row->name?>
					</td>
					<td style="text-align:center;">
						<?php echo $row->rate?>/5
					</td>
					<td align="center">
						<?php echo date('M d,Y H:i',strtotime($row->created_on))?>
					</td>
					<td style="padding-left: 10px;">
						<?php echo $row->pro_name?>
					</td>
					<td style="text-align:left;">
						<?php echo $row->ip_address;?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $published?>
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
		<input type="hidden" name="task" value="comment_list" />
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
	static function editHTML($option,$row,$lists){
		global $jinput, $mainframe;
		$jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('Comment').$title);
		ToolBarHelper::save('comment_save');
		ToolBarHelper::save2new('comment_new');
		ToolBarHelper::apply('comment_apply');
		ToolBarHelper::cancel('comment_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
			<table  width="100%" class="admintable" >
				<tr>
					<td class="key">
						<?php echo Text::_('OS_COMMENT_TITLE'); ?>
					</td>
					<td>
						<input type="text" name="title" id="title" size="40" value="<?php echo $row->title; ?>" class="input-xxlarge form-control ilarge" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_RATE_FOR'); ?>&nbsp;<?php echo Text::_('OS_RATE_OPTION1'); ?>
					</td>
					<td>
						<?php
						echo $lists['rate1'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_RATE_FOR'); ?>&nbsp;<?php echo Text::_('OS_RATE_OPTION2'); ?>
					</td>
					<td>
						<?php
						echo $lists['rate2'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_RATE_FOR'); ?>&nbsp;<?php echo Text::_('OS_RATE_OPTION3'); ?>
					</td>
					<td>
						<?php
						echo $lists['rate3'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_RATE_FOR'); ?>&nbsp;<?php echo Text::_('OS_RATE_OPTION4'); ?>
					</td>
					<td>
						<?php
						echo $lists['rate4'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_TOTAL_RATE'); ?>
					</td>
					<td>
						<strong>
							<?php
							echo $row->rate;
							?>
						</strong>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_AUTHOR'); ?>
					</td>
					<td>
						<?php
						if($row->id == 0){
							$user = Factory::getUser();
							$row->user_id = $user->id;
						}
						echo OspropertyAgent::getUserInput($row->user_id,0);
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_PROPERTY'); ?>
					</td>
					<td>
						<?php
						if(OSPHelper::isJoomla4())
						{
							?>
							<input type="text" name="pro_id" id="pro_id" size="5" value="<?php echo $row->pro_id; ?>" class="input-small form-control" />
							<?php
						}
						else
						{
							echo OspropertyComment::getPropertyInput($row->pro_id);
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_NAME'); ?>
					</td>
					<td>
						<input type="text" name="name" id="name" size="40" value="<?php echo $row->name; ?>" class="ilarge input-large form-control" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_CREATED'); ?>
					</td>
					<td class="created_date_calendar">
						<?php
						echo HTMLHelper::_('calendar',$row->created_on,'created_on','created_on','%Y-%m-%d',array( 'class' => 'input-small'));
						echo $lists['hours'];
						echo ":";
						echo $lists['min'];
						?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_IP_ADDRESS'); ?>
					</td>
					<td>
						<input type="text" name="ip_address" id="ip_address" size="40" value="<?php echo $row->ip_address; ?>" class="input-small form-control ilarge" />
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
				<tr>
					<td class="key" valign="top">
						<?php echo Text::_('OS_REVIEW')?>
					</td>
					<td>
						<textarea rows="5" class="form-control" style="width: 550px;" name="content" id="content"><?php echo $row->content; ?></textarea>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_osproperty" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo (int) $row->id?>" />
		</form>
		<script type="text/javascript">
			Joomla.submitbutton = function(pressbutton)
			{
				form = document.adminForm;
				if (pressbutton == 'comment_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.title.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_COMMENT_TITLE'); ?>');
					return;
				}else if (form.pro_id.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_SELECT_PROPERTY'); ?>');
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