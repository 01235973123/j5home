<?php
/*------------------------------------------------------------------------
# city.html.php - Ossolution Property
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


class HTML_OspropertyCity{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function city_list($option,$rows,$pageNav,$lists,$modal,$keyword)
	{
		global $mainframe,$jinput,$bootstrapHelper;
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span6Class    = $bootstrapHelper->getClassMapping('span6');
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_MANAGE_CITY'),"folder");
		ToolBarHelper::addNew('city_add');
		if (count($rows)){
			ToolBarHelper::editList('city_edit');
			ToolBarHelper::deleteList(Text::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEM'),'city_remove');
			ToolBarHelper::publish('city_publish');
			ToolBarHelper::unpublish('city_unpublish');
		}
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty&task=city_list" name="adminForm" id="adminForm">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="60%" align="left">
					<div class="<?php echo $rowFluidClass; ?>">
						<div class="<?php echo $span6Class;?>">
							<?php echo $lists['country']?>
						</div>
						<div class="<?php echo $span6Class;?>">
							<?php echo $lists['states']?>
						</div>
					</div>
				</td>
				<td width="40%" align="right">
					<input type="text" class="input-medium search-query form-control" name="keyword" id="keyword" value="<?php echo $keyword;?>" placeholder="<?php echo Text::_('Keyword');?>"/>
					<input type="submit" class="btn btn-warning" value="<?php echo Text::_('OS_SEARCH')?>" />
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
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_CITY'), 'a.city', @$lists['order_Dir'], @$lists['order'] ,'city_list'); ?>
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_COUNTRY'), 'b.country_name', @$lists['order_Dir'], @$lists['order'] ,'city_list'); ?>
					</th>
					<th >
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_STATE'), 'c.state_name', @$lists['order_Dir'], @$lists['order'] ,'city_list'); ?>
					</th>
					<?php
					if($modal == 0){
					?>
					<th width="10%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('OS_PUBLISH'), 'published', @$lists['order_Dir'], @$lists['order'] ,'city_list'); ?>
					</th>
					<?php } ?>
					<th width="5%" style="text-align:center;">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ID'), 'id', @$lists['order_Dir'], @$lists['order'] ,'city_list'); ?>
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
			$function = $jinput->getString('function','jSelectCity_city');
			for ($i=0, $n=count($rows); $i < $n; $i++) {
				$row = $rows[$i];
				$checked = HTMLHelper::_('grid.id', $i, $row->id);
				$link 		= Route::_( 'index.php?option=com_osproperty&task=city_edit&cid[]='. $row->id );
				$published 	= HTMLHelper::_('jgrid.published', $row->published, $i , 'city_');
				
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset( $i ); ?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $checked; ?>
					</td>
					<td align="left" style="padding-left: 10px;">
						<?php
						if($modal == 0){
						?>
							<a href="<?php echo $link; ?>">
						<?php
						}else{
						?>
							<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $row->id?>', '<?php echo $row->city; ?>');">
						<?php
						}
						?>
							<?php echo $row->city; ?>
						</a>
					</td>
					<td align="left" style="padding-left: 10px;">
						<?php
						if($modal == 0){
						?>
							<a href="<?php echo $link; ?>">
						<?php
						}
						?>
							<?php echo $row->country_name; ?>
						</a>
					</td>
					<td align="left" style="padding-left: 10px;">
						<?php
						if($modal == 0){
						?>
							<a href="<?php echo $link; ?>">
						<?php
						}
						?>
							<?php echo $row->state_name; ?>
						</a>
					</td>
					<?php
					if($modal == 0){
					?>
					<td align="center" style="text-align:center;">
						<?php echo $published?>
					</td>
					<?php }  ?>
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
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="city_list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
		<input type="hidden" name="modal" value="<?php echo $modal?>" />
		<input type="hidden" name="function" value="<?php echo $function?>" />
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
	 * Edit city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editHTML($option,$row,$lists,$translatable){
		global $mainframe,$languages,$configClass,$jinput;
        $jinput->set( 'hidemainmenu', 1 );
		$db = Factory::getDBO();
		OSPHelper::loadTooltip();
		if ($row->id){
			$title = ' ['.Text::_('OS_EDIT').']';
		}else{
			$title = ' ['.Text::_('OS_NEW').']';
		}
		ToolBarHelper::title(Text::_('OS_CITY').$title);
		ToolBarHelper::save('city_save');
		ToolBarHelper::save2new('city_new');
		ToolBarHelper::apply('city_apply');
		ToolBarHelper::cancel('city_cancel');

        $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
		?>
		<script type="text/javascript">
		var live_site = '<?php echo Uri::root()?>';
		function change_country_agent(country_id,agent_id){
			var url = 'index.php?option=com_osproperty&no_html=1&tmpl=component&task=agent_getstate';
			xmlHttp=GetXmlHttpObject();	
			xmlHttp.onreadystatechange = function() {
				if ( xmlHttp.readyState == 4 ) {
					var response = xmlHttp.responseText;
					document.getElementById("country_state").innerHTML = response;					
				}else{
					document.getElementById("country_state").innerHTML = '<img src="' + live_site + 'media/com_osproperty/assets/images/wait.gif"';
					
				}
			}
			xmlHttp.open( "POST", url, true );
			xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');			
			xmlHttp.send('country_id=' + country_id + '&agent_id=' + agent_id);
		}
		</script>
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
						<?php echo Text::_('OS_CITY'); ?>
					</td>
					<td>
						<input type="text" name="city" id="city" size="40" value="<?php echo $row->city?>" class="input-large form-control" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('OS_COUNTRY'); ?>
					</td>
					<td>
						<?php echo $lists['country']?>
					</td>
				</tr>
				<tr>
					<td class="key" style="width:20%;">
						<?php echo Text::_('OS_STATE'); ?>
					</td>
					<td style="width:80%;">
						<div id="country_state" style="width:100%;">
							<?php echo $lists['state']?>
						</div>
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
											<?php echo Text::_('OS_CITY'); ?>
										</td>
										<td>
											<input type="text" class="form-control input-medium" name="city_<?php echo $sef; ?>" id="city_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'city_'.$sef}?>">
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
				if (pressbutton == 'city_cancel'){
                    Joomla.submitform( pressbutton );
					return;
				}else if (form.city.value == ''){
					alert('<?php echo Text::_('OS_PLEASE_ENTER_CITY'); ?>');
					return;
				}else{
                    Joomla.submitform( pressbutton );
					return;
				}
			}
		</script>
		</form>
		<?php
	}
}

?>
