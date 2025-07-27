<?php

/*------------------------------------------------------------------------
# cpanel.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
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
use Joomla\CMS\Language\Multilanguage;


class HTML_OspropertyCpanel
{
	static function cpanelHTML($option,$lists,$countries)
	{
		global $mainframe,$configClass,$languages,$bootstrapHelper;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		
		$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
		$span12Class = $bootstrapHelper->getClassMapping('span12');

		$versionObj = $lists['version'];

		$document = Factory::getDocument();
		$document->addScript(Uri::root().'media/com_osproperty/assets/js/ajax.js');
		$document->setTitle($configClass['general_bussiness_name']);
		//toolbar
		ToolBarHelper::title(Text::_('OS_CPANEL'),"home");
		//ToolBarHelper::cancel();
		ToolBarHelper::preferences('com_osproperty');
		$options = array(
				    'onActive' => 'function(title, description){
				        description.setStyle("display", "block");
				        title.addClass("open").removeClass("closed");
				    }',
				    'onBackground' => 'function(title, description){
				        description.setStyle("display", "none");
				        title.addClass("closed").removeClass("open");
				    }',
				    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
				    'useCookie' => true, // this must not be a string. Don't use quotes.
				);
		//html
		if(OSPHelper::isJoomla4())
		{
			$width1 = 35;
			$width2 = 65; 
		}
		else
		{
			$width1 = 40;
			$width2 = 60; 
		}
		?>
		<form method="POST" action="index.php" name="adminForm">
		<table  width="100%" class="table table-striped">
			<tr>
				<th>
					<?php echo Text::_('OS_SYSTEM_INFORMATION')?>
				</th>
				<th>
					<?php echo Text::_('OS_CONTROLPANEL')?>
				</th>
			</tr>
			<tr>
				<td width="<?php echo $width1?>%" style="vertical-align:top !important;">
					<div class="<?php echo $rowFluidClass;?> oscpanel">
					<?php echo HTMLHelper::_('bootstrap.startTabSet', 'controlPanel', array('active' => 'general-page')); ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'controlPanel', 'general-page', Text::_('OS_SETUP')); ?>
							<div class="width-100 fltlft">
									<table  width="100%">
										<tr>
											<td width="100%" class="fontbold backgroundlightgray padding10" style="font-size:14px;border-bottom:1px solid #CCC;">
												<?php echo Text::_('OS_SETUP')?>
											</td>
										</tr>
										<tr>
											<td width="100%">
												<?php
												$yesimg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="green" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>';
												$noimg  = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
</svg>';
												

												?>
												<?php
												echo HTMLHelper::_('bootstrap.startAccordion', 'slideStatistic', array('active' => 'stas1'));
												?>
												<?php
												echo HTMLHelper::_('bootstrap.addSlide', 'slideStatistic', '<i class="icon-file"></i>&nbsp;'.Text::_('OS_SETUP'), 'stas1');
												?>
												
													<table  width="100%" class="table table-striped">
														<thead>
															<tr>
																<th width="30%">
																	<?php echo Text::_('OS_ITEM_INSTALL')?>
																</th>
																<th width="10%">
																	<?php echo Text::_('OS_STATUS')?>
																</th>
																<th width="60%">
																	<?php echo Text::_('OS_DESCRIPTION')?>
																</th>
															</tr>
														</thead>
														<tbody>
															<?php
															$version = OSPHelper::getInstalledVersion();
															?>
															<tr>
																<td align="left" class="padding5" style="vertical-align:middle;">
																	<b><?php echo Text::_('OS_CURRENT_VERSION')?></b>
																</td>
																<td class="padding5 fontbold center" style="vertical-align:middle;">
																	<?php
																	  echo $version;
																	?>
																</td>
																<td align="left" class="padding5">
																	<div class="<?php echo $rowFluidClass;?>">
																		<div class="<?php echo $span12Class; ?> ospversion_div">
																			<?php
																			if ($versionObj['status'] == 2)
																			{
																			?>
																				<div id="updatealert">
																					<?php
																					echo Text::_('OS_PLEASE_UPGRADE_LATEST_VERSION')." ".$versionObj['version'];
																					?>
																				</div>
																			<?php
																			}
																			else
																			{
																				echo $versionObj['message'];
																			}
																			?>
																		</div>
																	</div>
																</TD>
															</tr>
															<!--
															<tr>
																<td align="left" class="padding5">
																	<b><?php echo Text::_('OS_OSPROPERTY_CRONJOB_PLUGIN')?></b>
																</td>
																<td class="padding5 center">
																	<?php
																	if($lists['plugin'] == 1){
																		echo $yesimg;
																	}else{
																		echo $noimg;
																	}
																	?>
																</td>
																<td align="left" class="padding5 fontsmall">
																	<?php
																	echo Text::_('OS_SYSTEM_CRONJOB_MUST_BE_INSTALLED');
																	?>
																</TD>
															</tr>
															-->
															<tr>
																<td align="left" class="padding5">
																	<b><?php echo Text::_('OS_GD_LIB')?></b>
																	<font color='red'>(<?php echo Text::_('OS_REQUIRED')?>)</font>
																</td>
																<td class="padding5 center">
																	<?php
																	if($lists['gd'] == 1){
																		echo $yesimg;
																	}else{
																		echo $noimg;
																	}
																	?>
																</td>
																<td align="left" class="padding5">
																	<?php
																	echo Text::_('OS_GD_LIB_EXPLAIN');
																	?>
																</TD>
															</tr>
															<tr>
																<td align="left" class="padding5">
																	<b><?php echo Text::_('OS_GD_LIB_JPEG')?></b>
																	<font color='red'>(<?php echo Text::_('OS_REQUIRED')?>)</font>
																</td>
																<td class="padding5 center">
																	<?php
																	if($lists['gd_jpg'] == 1){
																		echo $yesimg;
																	}else{
																		echo $noimg;
																	}
																	?>
																</td>
																<td align="left" class="padding5">
																	<?php
																	echo Text::_('OS_GD_LIB_JPEG_EXPLAIN');
																	?>
																</TD>
															</tr>
														</tbody>
													</table>
												<?php
												echo HTMLHelper::_('bootstrap.endSlide');
												echo HTMLHelper::_('bootstrap.addSlide', 'slideStatistic', '<i class="icon-icon-play"></i>&nbsp;'.Text::_('OS_ITEM_DATA'), 'stas2');
												?>
											
												<table  width="100%" class="table table-striped">
													<thead>
														<tr>
															<th width="30%">
																<?php echo Text::_('OS_ITEM_DATA')?>
															</th>
															<th width="10%">
																<?php echo Text::_('OS_STATUS')?>
															</th>
															<th width="60%">
																<?php echo Text::_('OS_DESCRIPTION')?>
															</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_PROPERTY')?></b><font color='red'>(<?php echo Text::_('OS_REQUIRED')?>)</font>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['properties'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_AT_LEAST_ONE_PROPERTY_MUST_BE_CREATED');
																?>
															</TD>
														</tr>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_CATEGORY')?></b><font color='red'>(<?php echo Text::_('OS_REQUIRED')?>)</font>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['categories'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_AT_LEAST_ONE_CATEGORY_MUST_BE_CREATED');
																?>
															</TD>
														</tr>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_PROPERTY_TYPE')?></b>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['type'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_YOU_HAVE_NOT_CREATED_ANY_PROPERTY_TYPE');
																?>
															</TD>
														</tr>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_AGENT')?></b><font color='red'>(<?php echo Text::_('OS_REQUIRED')?>)</font>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['agent'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_AT_LEAST_ONE_AGENT_MUST_BE_CREATED');
																?>
															</TD>
														</tr>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_AMENITIES')?></b>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['amenities'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_YOU_HAVE_NOT_CREATED_ANY_AMENITTY');
																?>
															</TD>
														</tr>
														<tr>
															<td align="left" class="padding5">
																<b><?php echo Text::_('OS_PRICEGROUP')?></b>
															</td>
															<td align="center" class="padding5">
																<?php
																if($lists['pricegroups'] > 0){
																	echo $yesimg;
																}else{
																	echo $noimg;
																}
																?>
															</td>
															<td align="left" class="padding5">
																<?php
																echo Text::_('OS_YOU_HAVE_NOT_CREATED_ANY_PRICE_GROUP');
																?>
															</TD>
														</tr>
													</tbody>
												</table>
											<?php
											echo HTMLHelper::_('bootstrap.endSlide');
											//echo HTMLHelper::_('bootstrap.addSlide', 'slideStatistic', '<i class="icon-icon-play"></i>&nbsp;'.Text::_('OS_ITEM_DATA'), 'stas3');
											?>
												<?php
												if (Factory::getUser()->authorise('location', 'com_osproperty')) 
												{
													echo HTMLHelper::_('bootstrap.addSlide', 'slideStatistic', '<i class="icon-icon-play"></i>&nbsp;'.Text::_('OS_LOCATION'), 'stas3');
													?>
													<div id="location_div">
													
													</div>
													<?php
													echo HTMLHelper::_('bootstrap.endSlide');
												}
												?>
												<script type="text/javascript">
													window.onload = function() {
													   initLocation();
													};
												</script>
											<?php
											echo HTMLHelper::_('bootstrap.endAccordion');		
											?>
											</td>
										</tr>
									</table>
							</div>
						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'controlPanel', 'statistic', Text::_('OS_STATISTIC')); ?>	
							<div class="width-100 fltlft">
								<table  width="100%">
									<tr>
										<td width="100%" class="padding10 fontbold backgroundlightgray" style="font-size:14px;border-bottom:1px solid #CCC;">
											<?php echo Text::_('OS_STATISTIC')?>
										</td>
									</tr>
									<tr>
										<td width="100%">
										<?php
										if (OSPHelper::isJoomla4())
										{
											echo HTMLHelper::_('bootstrap.startAccordion', 'slide_pane1', array('active' => 'contentstatistic'));
											echo HTMLHelper::_('bootstrap.addSlide', 'slide_pane1', '<i class="icon-file"></i>&nbsp;'.Text::_('OS_CONTENT_STATISTIC'), 'contentstatistic');	
										}
										else
										{
											echo HTMLHelper::_('sliders.start', 'slide_pane1');
											echo HTMLHelper::_('sliders.panel', Text::_('OS_CONTENT_STATISTIC'), 'contentstatistic');
										}
										?>
										<table  width="100%" class="table table-striped">
											<thead>
												<tr>
													<th colspan="2">
														<?php echo Text::_('OS_AGENT_ACCOUNT');?>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_TOTAL_ACCOUNT')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['agent_active'] + $lists['agent_unactive'];?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_ACTIVE_ACCOUNT')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['agent_active']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_UNACTIVE_ACCOUNT')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['agent_unactive']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_REQUEST_APPROVAL')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['agent_request']?>
													</td>
												</tr>
											</tbody>
											<thead>
												<tr>
													<th colspan="2">
														<?php echo Text::_('OS_LISTING');?>
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_TOTAL_PROPERTIES')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_approved'] + $lists['property_unapproved']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_ACTIVE_PROPERTY')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_approved']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_UNACTIVE_PROPERTY')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_unapproved']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_REQUEST_APPROVAL')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_request']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_FEATURED')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_featured']?>
													</td>
												</tr>
												<tr>
													<td width="80%" align="left" class="padding5">
														<?php echo Text::_('OS_REQUEST_TO_FEATURED')?>
													</td>
													<td width="20%" align="center" class="padding5">
														<?php echo $lists['property_request_featured']?>
													</td>
												</tr>
											</tbody>
										</table>
										<?php
										if (OSPHelper::isJoomla4()){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
										$row_mostview = $lists['mostviewed'];
										if(count($row_mostview) > 0){
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.addSlide', 'slide_pane1', '<i class="icon-clock"></i>&nbsp;'.Text::_('OS_MOST_VIEWED_PROPERTY'), 'propertystatistic');
											}
											else
											{
												echo HTMLHelper::_('sliders.panel', Text::_('OS_MOST_VIEWED_PROPERTY'), 'propertystatistic');	
											}
											?>
											<table  width="100%" class="table table-striped">
												<thead>
													<tr>
														<th width="5%">
															ID
														</th>
														<th width="5%">
															<?php echo Text::_('OS_EDIT')?>
														</th>
														<th width="60%">
															<?php echo Text::_('OS_PROPERTY')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_HITS')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_FAVORITES')?>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for($i=0;$i<count($row_mostview);$i++){
														$row = $row_mostview[$i];
														$link = "index.php?option=com_osproperty&task=properties_edit&id=$row->id";
														?>
														<tr>
															<td align="center">
																<?php echo $row->id?>
															</td>
															<td align="center">
																<a href="<?php echo $link?>" target="_blank">
																	<?php echo Text::_('OS_EDIT')?>
																</a>
															</td>
															<td align="left">
																<?php echo $row->pro_name?>
															</td>
															<td align="left">
																<?php echo $row->hits?>
															</td>
															<td align="center">
																<?php
																$db->setQuery("Select count(id) from #__osrs_favorites where pro_id = '$row->id'");
																$count = $db->loadResult();
																echo intval($count);
																?>
															</td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>
											<?php
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.endSlide');
											}
										}
										$row_favorites = $lists['mostfavorites'];
										if(count($row_mostview) > 0){
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.addSlide', 'slide_pane1', '<i class="icon-clock"></i>&nbsp;'.Text::_('OS_MOST_FAVORITES_PROPERTY'), 'propertyfav');
											}
											else
											{
												echo HTMLHelper::_('sliders.panel', Text::_('OS_MOST_FAVORITES_PROPERTY'), 'propertyfav');		
											}
											?>
											<table  width="100%" class="table table-striped">
												<thead>
													<tr>
														<th width="5%">
															ID
														</th>
														<th width="5%">
															<?php echo Text::_('OS_EDIT')?>
														</th>
														<th width="60%">
															<?php echo Text::_('OS_PROPERTY')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_HITS')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_FAVORITES')?>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for($i=0;$i<count($row_favorites);$i++){
														$row = $row_favorites[$i];
														$link = "index.php?option=com_osproperty&task=properties_edit&id=$row->pro_id";
														?>
														<tr>
															<td align="center">
																<?php echo $row->pro_id?>
															</td>
															<td align="center">
																<a href="<?php echo $link?>" target="_blank">
																	<?php echo Text::_('OS_EDIT')?>
																</a>
															</td>
															<td align="left">
																<?php echo $row->pro_name?>
															</td>
															<td align="left">
																<?php echo $row->hits?>
															</td>
															<td align="center">
																<?php
																$db->setQuery("Select count(id) from #__osrs_favorites where pro_id = '$row->pro_id'");
																$count = $db->loadResult();
																echo intval($count);
																?>
															</td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>
											<?php
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.endSlide');
											}
										}
										$row_rate= $lists['mostrate'];
										if(count($row_rate) > 0){
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.addSlide', 'slide_pane1', '<i class="icon-clock"></i>&nbsp;'.Text::_('OS_MOST_RATED_PROPERTY'), 'propertyrate');
											}
											else
											{
												echo HTMLHelper::_('sliders.panel', Text::_('OS_MOST_RATED_PROPERTY'), 'propertyrate');			
											}
											?>
											<table  width="100%" class="table table-striped">
												<thead>
													<tr>
														<th width="5%">
															ID
														</th>
														<th width="5%">
															<?php echo Text::_('OS_EDIT')?>
														</th>
														<th width="60%">
															<?php echo Text::_('OS_PROPERTY')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_HITS')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_FAVORITES')?>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for($i=0;$i<count($row_rate);$i++){
														$row = $row_rate[$i];
														$link = "index.php?option=com_osproperty&task=properties_edit&id=$row->id";
														?>
														<tr>
															<td align="center">
																<?php echo $row->id?>
															</td>
															<td align="center">
																<a href="<?php echo $link?>" target="_blank">
																	<?php echo Text::_('OS_EDIT')?>
																</a>
															</td>
															<td align="left">
																<?php echo $row->pro_name?>
															</td>
															<td align="left">
																<?php echo $row->hits?>
															</td>
															<td align="center">
																<?php
																$db->setQuery("Select count(id) from #__osrs_favorites where pro_id = '$row->id'");
																$count = $db->loadResult();
																echo intval($count);
																?>
															</td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>
											<?php
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.endSlide');
											}
										}
										$row_comment= $lists['mostcomments'];
										if(count($row_comment) > 0){
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.addSlide', 'slide_pane1', '<i class="icon-clock"></i>&nbsp;'.Text::_('OS_MOST_COMMENTED_PROPERTY'), 'propertycomment');
											}
											else
											{
												echo HTMLHelper::_('sliders.panel', Text::_('OS_MOST_COMMENTED_PROPERTY'), 'propertycomment');	
											}
											?>
											<table  width="100%" class="table table-striped">
												<thead>
													<tr>
														<th width="5%">
															ID
														</th>
														<th width="5%">
															<?php echo Text::_('OS_EDIT')?>
														</th>
														<th width="60%">
															<?php echo Text::_('OS_PROPERTY')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_HITS')?>
														</th>
														<th width="10%">
															<?php echo Text::_('OS_FAVORITES')?>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php
													for($i=0;$i<count($row_comment);$i++){
														$row = $row_comment[$i];
														$link = "index.php?option=com_osproperty&task=properties_edit&id=$row->pro_id";
														?>
														<tr>
															<td align="center">
																<?php echo $row->pro_id?>
															</td>
															<td align="center">
																<a href="<?php echo $link?>" target="_blank">
																	<?php echo Text::_('OS_EDIT')?>
																</a>
															</td>
															<td align="left">
																<?php echo $row->pro_name?>
															</td>
															<td align="left">
																<?php echo $row->hits?>
															</td>
															<td align="center">
																<?php
																$db->setQuery("Select count(id) from #__osrs_favorites where pro_id = '$row->pro_id'");
																$count = $db->loadResult();
																echo intval($count);
																?>
															</td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>
											<?php
											if (OSPHelper::isJoomla4()){
												echo HTMLHelper::_('bootstrap.endSlide');
											}
										}

										if (OSPHelper::isJoomla4()){
											echo HTMLHelper::_('bootstrap.endAccordion');
										}else{
											echo HTMLHelper::_('sliders.end');	
										}
										?>
										</td>
									</tr>
								</table>
						</div>
						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php
						if(file_exists(JPATH_ROOT."/components/com_osproperty/changelog.txt")){
						?>
						<?php echo HTMLHelper::_('bootstrap.addTab', 'controlPanel', 'changelog', Text::_('OS_CHANGELOG')); ?>
							<div class="width-100 fltlft">
								<table  width="100%">
									<tr>
										<td width="100%" class="padding10 fontbold backgroundlightgray" style="font-size:14px;border-bottom:1px solid #CCC;">
											<?php echo Text::_('OS_CHANGELOG')?>
										</td>
									</tr>
									<tr>
										<td width="100%" class="fontsmall padding5 backgroundlightgray" style="border-bottom:1px solid #CCC;">
											<div style="width:100%;height:400px;overflow-y:scroll;">
												<?php
												 $file = fopen(JPATH_ROOT."/components/com_osproperty/changelog.txt",'r');
												 while(!feof($file)) { 
													 $name = fgets($file);
													 echo $name; 
													 echo "<BR />";
												 }
												?>
											</div>
										</td>
									</tr>
								</table>
							</div>
						<?php echo HTMLHelper::_('bootstrap.endTab') ?>
						<?php
						}
						?>
						<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
					</div>
				</td>
				<td width="<?php echo $width2?>%" style="vertical-align:top !important;">
					<table  width="100%">
						<tr>
							<td>
								<div id="cpanel">
									<?php
									OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=configuration_list', 'setting.png', Text::_('OS_CONFIGURATION'));
									if (Factory::getUser()->authorise('categories', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=categories_list', 'categories.png', Text::_('OS_MANAGE_CATEGORIES'));
									}
									if (Factory::getUser()->authorise('type', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=type_list', 'type.png', Text::_('OS_MANAGE_PROPERTY_TYPES'));
									}
									if (Factory::getUser()->authorise('convenience', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=amenities_list', 'convenience.png', Text::_('OS_MANAGE_CONVENIENCE'));
									}
									if (Factory::getUser()->authorise('properties', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=properties_list', 'property.png', Text::_('OS_MANAGE_PROPERTIES'));
									}
									if (Factory::getUser()->authorise('pricelists', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=pricegroup_list', 'price.png', Text::_('OS_MANAGE_PRICELIST'));
									}
									if (Factory::getUser()->authorise('agents', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=agent_list', 'users.png', Text::_('OS_MANAGE_AGENTS'));
									}
									if (Factory::getUser()->authorise('companies', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=companies_list', 'company.png', Text::_('OS_MANAGE_COMPANIES'));
									}
									if (Factory::getUser()->authorise('extrafieldgroups', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=fieldgroup_list', 'group.png', Text::_('OS_MANAGE_FIELD_GROUPS'));
									}
									if (Factory::getUser()->authorise('extrafields', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=extrafield_list', 'fields.png', Text::_('OS_MANAGE_EXTRA_FIELDS'));
									}
									if (Factory::getUser()->authorise('location', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=state_list', 'state.png', Text::_('OS_MANAGE_STATES'));
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=city_list', 'city.png', Text::_('OS_MANAGE_CITY'));
									}
									if (Factory::getUser()->authorise('email', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=email_list', 'email.png', Text::_('OS_MANAGE_EMAIL_FORMS'));
									}
									if (Factory::getUser()->authorise('comments', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=comment_list', 'comment.png', Text::_('OS_MANAGE_COMMENTS'));
									}
									if (Factory::getUser()->authorise('tags', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=tag_list', 'tag.png', Text::_('OS_MANAGE_TAGS'));
									}
									if (Factory::getUser()->authorise('themes', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=theme_list', 'theme.png', Text::_('OS_MANAGE_THEMES'));
									}
									if (Factory::getUser()->authorise('transaction', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=transaction_list', 'order.png', Text::_('OS_MANAGE_TRANSACTION'));
									}
									if (Factory::getUser()->authorise('plugin_list', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=plugin_list', 'payment_plugin.png', Text::_('OS_PAYMENT_PLUGINS'));
									}
									if (Factory::getUser()->authorise('csv', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=form_default', 'csv.png', Text::_('OS_CSV_FORM'));
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=csvexport_default', 'csvexport.png', Text::_('OS_EXPORT_CSV'));
									}
									if (Factory::getUser()->authorise('xml', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=xml_default', 'xmlexport.png', Text::_('OS_EXPORT_XML'));
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=xml_defaultimport', 'xmlimport.png', Text::_('OS_IMPORT_XML'));
									}
									if (Factory::getUser()->authorise('translation', 'com_osproperty')) {
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=translation_list', 'translate.png', Text::_('OS_TRANSLATION_LIST'));
									}
									OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&task=properties_prepareinstallsample', 'install.png', Text::_('OS_INSTALLSAMPLEDATA'));
									OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=properties_sefoptimize', 'icon-48-sef.png', Text::_('OS_OPTIMIZE_SEF_URLS'));
									$translatable = Multilanguage::isEnabled() && count($languages);
									if($translatable){
										OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=properties_syncdatabase', 'sync.png', Text::_('OS_SYNC_MULTILINGUAL_DATABASE'));
									}
									if($configClass['enable_report'] == 1){
										$db->setQuery("Select count(id) from #__osrs_report where is_checked = '0'");
										$count_report = $db->loadResult();
										if($count_report > 0){
											OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=report_listing', 'notice_new.png', Text::_('OS_USER_REPORT'));
										}else{
											OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=report_listing', 'notice.png', Text::_('OS_USER_REPORT'));
										}
									}
									OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=properties_reGeneratePictures', 'image.png', Text::_('OS_REGENERATE_PICTURES'));
                                    OspropertyCpanel::quickiconButton('index.php?option=com_osproperty&amp;task=configuration_help', 'help.png', Text::_('JTOOLBAR_HELP'));
									?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root();?>" />
		</form>
	<?php
	}
}
?>
