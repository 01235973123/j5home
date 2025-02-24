<?php
/*------------------------------------------------------------------------
# theme.html.php - Ossolution Property
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


class HTML_OspropertyTheme{
	/**
	 * List themes
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function listThemes($option,$rows,$pageNav){
		global $mainframe,$configClass;
		ToolBarHelper::title(Text::_('OS_MANAGE_THEMES'));
		ToolBarHelper::editList('theme_edit');
		ToolBarHelper::custom('theme_copy','copy.png','copy.png',Text::_('Copy theme'));
		ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'theme_remove');
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_CONTROLPANEL'),false);
		?>
		<form method="POST" action="index.php?option=<?php echo $option?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<table width="100%" class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%">
				
					</th>
					<th width="3%" style="text-align:center;">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="15%" style="text-align:center;">
						<?php echo Text::_('OS_THEME_NAME');?>
					</th>
					<th width="15%" style="text-align:center;">
						<?php echo Text::_('OS_THEME_TITLE');?>
					</th>
					<th width="15%" style="text-align:center;">
						<?php echo Text::_('OS_AUTHOR');?>
					</th>
					<th width="15%" style="text-align:center;">
						<?php echo Text::_('OS_EMAIL');?>
					</th>
					<th width="10%" style="text-align:center;">
						<?php echo Text::_('OS_SUPPORT_MOBILE_DEVICE');?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('OS_STATUS');?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo Text::_('ID');?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td width="100%" colspan="9">
						<?php echo $pageNav->getListFooter();?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
					$checked = HTMLHelper::_('grid.id', $i, $row->id);
					$link 		= Route::_( 'index.php?option=com_osproperty&task=theme_edit&cid[]='. $row->id );
					$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'theme_');
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
								<?php echo $row->name; ?>
							</a>
						</td>
						<td align="left">
							<a href="<?php echo $link; ?>">
								<?php echo $row->title; ?>
							</a>
						</td>
						<td align="left">
							<?php echo $row->author; ?>
						</td>
						<td align="left">
							<?php echo $row->author_email; ?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo ($row->support_mobile_device==1) ? Text::_('OS_YES'):Text::_('OS_NO');?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $published;?>
						</td>
						<td align="center" style="text-align:center;">
							<?php echo $row->id;?>
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
						<legend><?php echo Text::_('OS_INSTALL_NEW_THEME'); ?></legend>
						<table>
							<tr>
								<td>
									<input type="file" name="theme_package" id="theme_package" size="50" class="input-large form-control" /> <input type="button" class="btn btn-info" value="<?php echo Text::_('OS_INSTALL'); ?>" onclick="installTheme();" />
								</td>
							</tr>
						</table>					
					</fieldset>
				</td>
			</tr>		
		</table>
		</div>
		<?php echo HTMLHelper::_( 'form.token' ); ?>				 
		<script type="text/javascript">
			function installTheme() {
				var form = document.adminForm ;
				if (form.theme_package.value =="") {
					alert("<?php echo Text::_('OS_CHOOSE_THEME'); ?>");
					return ;	
				}
				
				form.task.value = 'theme_install' ;
				form.submit();
			}
		</script>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="theme_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="MAX_UPLOAD_SIZE" value="9000000" /> 
		</form>
		<?php
	}
	
	/**
	 * Edit theme
	 *
	 * @param unknown_type $option
	 * @param unknown_type $item
	 * @param unknown_type $lists
	 * @param unknown_type $form
	 */
	static function editTheme($option,$item,$lists,$form){
		global $mainframe,$configClass;
		global $mainframe;
		OSPHelper::loadTooltip();
		if($item->id > 0){
			$type = "[".Text::_('OS_EDIT')."]";
		}else{
			$type = "[".Text::_('OS_ADD')."]";
		}
		ToolBarHelper::title(Text::_('OS_THEME')." ".$type);
		ToolBarHelper::save('theme_save');
		ToolBarHelper::apply('theme_apply');
		ToolBarHelper::cancel('theme_gotolist');
		?>
		<script language="javascript" type="text/javascript">
			<?php
				if (version_compare(JVERSION, '1.6.0', 'ge')) {
				?>
					Joomla.submitbutton = function(pressbutton)
					{
						var form = document.adminForm;
						if (pressbutton == 'theme_cancel') {
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
						if (pressbutton == 'theme_cancel') {
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
				<legend><?php echo Text::_('OS_THEME_DETAIL'); ?></legend>
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
								<?php echo $item->description; ?>
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
				<legend><?php echo Text::_('OS_THEME_PARAMETERS'); ?></legend>
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
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
}