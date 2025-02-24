<?php
/*------------------------------------------------------------------------
# country.html.php - Ossolution Property
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


class HTML_OspropertyCountry{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function country_list($option,$rows,$pageNav,$lists,$modal,$keyword){
		global $mainframe;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_COUNTRIES'),"folder");
		if (count($rows)){
			ToolBarHelper::editList('country_edit');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=country_list" name="adminForm" id="adminForm">
		<table  width="100%">
			<tr>
				<td width="100%">
					<input type="text" name="keyword" value="<?php echo $keyword;?>" class="input-medium search-query form-control" placeholder="<?php echo Text::_('OS_KEYWORD'); ?>"/>
					<input type="submit" class="btn btn-primary" value="<?php echo Text::_('OS_SUBMIT')?>" />
				</td>
			</tr>
		</table>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5%" style="text-align:center;">
					#
					</th>
					<th width="90%">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COUNTRY_NAME'), 'country_name', @$lists['order_Dir'], @$lists['order'] ,'country_list'); ?>
					</th>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'id', @$lists['order_Dir'], @$lists['order'] ,'country_list'); ?>
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
				$link 		= Route::_( 'index.php?option=com_osproperty&task=country_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i, 'country_');
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center;">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="left">
						<?php
						if(file_exists(JPATH_ROOT.'/media/com_osproperty/flags/'.strtolower($row->country_code).'.png')){
							?>
							<img style="width:16px;" src="<?php echo Uri::root() ?>media/com_osproperty/flags/<?php echo strtolower($row->country_code); ?>.png" />
							<?php
						}
						?>
						<a href="<?php echo $link; ?>">
							<?php echo $row->country_name; ?>
						</a>
					</td>
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
		<input type="hidden" name="task" value="country_list" />
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
		ToolBarHelper::title(Text::_('OS_COUNTRY').$title);
		ToolBarHelper::save('country_save');
		ToolBarHelper::apply('country_apply');
		ToolBarHelper::cancel('country_cancel');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<?php 
		if ($translatable)
		{
			echo HTMLHelper::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo HTMLHelper::_('bootstrap.addTab', 'translation', 'general-page', Text::_('OS_GENERAL', true));
		}
		?>
			<table width="100%" class="admintable" >
				<tr>
					<td class="key">
						<?php echo Text::_('OS_COUNTRY_NAME'); ?>
					</td>
					<td>
						<input type="text" class="input-large form-control" name="country_name" id="country_name" size="40" value="<?php echo $row->country_name;?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_COUNTRY_CODE')?>
					</td>
					<td>
						<input type="text" name="country_code" class="input-large form-control" id="country_code" size="40" value="<?php echo $row->country_code;?>" disabled />
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
											<?php echo Text::_('OS_COUNTRY_NAME'); ?>
										</td>
										<td>
											<input type="text" class="form-control input-medium" name="country_name_<?php echo $sef; ?>" id="country_name_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'country_name_'.$sef}?>" />
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
				if (pressbutton == 'country_cancel'){
					Joomla.submitform( pressbutton );
					return;
				}else if (form.country_name.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_COUNTRY'); ?>');
					form.country_name.focus();
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