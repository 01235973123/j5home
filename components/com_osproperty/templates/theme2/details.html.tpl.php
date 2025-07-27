<?php

/*------------------------------------------------------------------------
# details.html.tpl.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
$db					= Factory::getDbo();
$document			= Factory::getDocument();
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/".$themename."/style/style.css");
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/".$themename."/style/font.css");
$extrafieldncolumns = $params->get('extrafieldncolumns',3);
$show_location		= $params->get('show_location',1);
$module_ids			= $params->get('module_ids','');
$user				= Factory::getUser();
OSPHelperJquery::colorbox('osmodal');
?>
<style>
#main ul{
	margin:0px;
}
</style>
<div id="notice" style="display:none;">
</div>
<?php
if(count($topPlugin) > 0){
	for($i=0;$i<count($topPlugin);$i++){
		echo $topPlugin[$i];
	}
}

if(!OSPHelper::isJoomla4())
{
	$extraClass = "joomla3";
}
else
{
	$extraClass = "joomla4";
}
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> <?php echo $extraClass; ?>" id="propertydetails">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> flexdisplay">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> g5ere__property-meta-action">
				<ul class="g5ere__property-actions list-inline d-flex align-items-center">
					<?php
					if($configClass['show_getdirection'] == 1 && $row->show_address == 1)
					{
						?>
						<li class="direction">
							<a href="<?php echo Route::_("index.php?option=com_osproperty&task=direction_map&id=".$row->id."&Itemid=".Factory::getApplication()->input->getInt('Itemid'))?>" title="<?php echo Text::_('OS_GET_DIRECTIONS')?>" class="get_direction_link">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-compass" viewBox="0 0 16 16">
								  <path d="M8 16.016a7.5 7.5 0 0 0 1.962-14.74A1 1 0 0 0 9 0H7a1 1 0 0 0-.962 1.276A7.5 7.5 0 0 0 8 16.016zm6.5-7.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
								  <path d="m6.94 7.44 4.95-2.83-2.83 4.95-4.949 2.83 2.828-4.95z"/>
								</svg>
							</a>
						</lu>
						<?php
					}
					if($configClass['property_save_to_favories'] == 1 && $user->id > 0)
					{
						if($inFav == 0)
						{
							?>
							<li class="favorite">
								<span id="fav<?php echo $row->id;?>">
									<?php
									$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
									$msg = str_replace("'","\'",$msg);
									?>
									<a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','details')" class="favLink">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
										  <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
										</svg>
									</a>
								</span>
							</li>
							<?php
						}
						else
						{
							?>
							<li class="favorite">
								<span id="fav<?php echo $row->id;?>">
									<?php
									$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
									$msg = str_replace("'","\'",$msg);
									?>
									<a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme2','details')" href="javascript:void(0)" class="favLinkActive">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-heart-fill" viewBox="0 0 16 16">
										  <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
										</svg>
									</a>
								</span>
							</li>
							<?php
						}
					}
					if($configClass['show_compare_task'] == 1)
					{
						?>
						<li class="compare">
							<span id="compare<?php echo $row->id;?>">
								<?php
								if(! OSPHelper::isInCompareList($row->id)) 
								{
									$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_COMPARE_LIST');
									$msg = str_replace("'","\'",$msg);
									?>
									<a title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme2','details')" href="javascript:void(0)" class="compareLink">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
										  <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
										</svg>
									</a>
									<?php
								}
								else
								{
									$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
									$msg = str_replace("'","\'",$msg);
									?>
									<a title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme2','details')" href="javascript:void(0)" class="compareLink">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
										  <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
										</svg>
									</a>
									<?php
								}
								?>
							</span>
						</li>
						<?php
					}
					if($configClass['property_pdf_layout'] == 1)
					{
						?>
						<li class="pdf_export">
							<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_pdf&id=<?php echo $row->id?>" title="<?php echo Text::_('OS_EXPORT_PDF')?>"  rel="nofollow" target="_blank" class="pdf_export_link">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16">
								  <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
								  <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.701 19.701 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.716 5.716 0 0 1-.911-.95 11.642 11.642 0 0 0-1.997.406 11.311 11.311 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.27.27 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.647 12.647 0 0 1 1.01-.193 11.666 11.666 0 0 1-.51-.858 20.741 20.741 0 0 1-.5 1.05zm2.446.45c.15.162.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.881 3.881 0 0 0-.612-.053zM8.078 5.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
								</svg>
							</a>
						</li>
						<?php
					}
					if($configClass['property_show_print'] == 1)
					{
						?>
						<li class="print_export">
							<a target="_blank" href="<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&no_html=1&task=property_print&id=<?php echo $row->id?>" class="print_export_link" title="<?php echo Text::_('OS_PRINT_THIS_PAGE')?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
								  <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
								  <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
								</svg>
							</a>
						</li>
						<?php
					}
					?>
				</ul>
				<ul class="g5ere__property-meta list-inline d-flex flex-wrap align-items-center">
					<li class="featured-status">
						<div class="g5ere__loop-property-badge g5ere__lpb-featured-status">
							<?php
							if($row->isFeatured == 1)
							{
								?>
								<span class="g5ere__property-badge g5ere__featured" style="background-color:#ef0606;">
									<?php echo Text::_('OS_FEATURED');?>
								</span>
								<?php
							}
							?>
							<?php
							if($configClass['active_market_status'] == 1 && $row->isSold > 0)
							{
							?>
								<span class="g5ere__property-badge g5ere__featured">
									<?php echo OSPHelper::returnMarketStatus($row->isSold);?>
								</span>
							<?php
							}	
							?>
							<span class="g5ere__property-badge g5ere__status property-type" style="background-color: #153b3e;">
								<?php echo $row->type_name;?>
							</span>
							<?php
							if($configClass['enable_report'] == 1)
							{
								OSPHelperJquery::colorbox('a.reportmodal');
							?>
								<span class="g5ere__property-badge g5ere__status report" style="background-color: #f10ab3;">
									<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&item_type=0&task=property_reportForm&id=<?php echo $row->id?>" class="reportmodal reportlink" title="<?php echo Text::_('OS_REPORT_LISTING');?>">
											<?php echo Text::_('OS_REPORT');?>
									</a>
								</span>
							<?php
							}	
							?>
						</div>
					</li>
					<li class="view">
						<div class="g5ere__property-view-count">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
							  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
							  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
							</svg>
							<?php
							echo $row->hits;
							?>
							<?php echo Text::_('OS_VIEWS');?>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> g5ere__property-title-price">
				<div class="g5ere__property-title-address">
					<h1 class="g5ere__property-title">
						<?php
						if($row->ref != "" && $configClass['show_ref'] == 1)
						{
							?>
							<span color="orange">
								<?php echo $row->ref?>
							</span>
							-
							<?php
						}
						?>
						<?php echo $row->pro_name?>
					</h1>
					<div class="g5ere__property-address">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
						  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
						  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
						</svg>
						<?php 
						if($row->show_address == 1)
						{
							echo OSPHelper::generateAddress($row);
						}
						?>
					</div>
				</div>
				
				<span class="g5ere__property-price">
					<?php echo $row->price1;?>
				</span>
			</div>
		</div>
		<?php
		if(count($photos) > 0)
		{
		?>
			<script type="text/javascript" src="<?php echo Uri::root()?>media/com_osproperty/assets/js/colorbox/jquery.colorbox.js"></script>
			 <link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/js/colorbox/colorbox.css" type="text/css" media="screen" />
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> no-gutters" id="propertyGallery">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?>">
					<div class="g5core__embed-responsive g5core__post-featured g5core__metro g5core__image-size-6x3">
						<div class="g5core__metro-inner">
							<?php
							if(count($photos) >= 1)
							{
							?>
								<div class="g5core__entry-thumbnail w-100 h-100" style="background-image:url(<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/<?php echo $photos[0]->image?>);">
									<a data-g5core-mfp href="#" onClick="openSlideshow(0)" data-index="0" class="g5core__zoom-image propertyphotogroup">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#FFFFFF" class="bi bi-arrows-fullscreen" viewBox="0 0 16 16">
										  <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707zm0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707zm-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707z"/>
										</svg>
									</a>
								</div>
							<?php
							}		
							?>
						</div>
						<?php
						if($configClass['goole_use_map'] == 1 && $row->lat_add != "" && $row->long_add != "")
						{
							?>
							<div class="mapLinkDiv">
								<a href="javascript:void(0);" id="mapLinkScroll" title="<?php echo Text::_('OS_VIEW_PROPERTY_ON_MAP');?>" class="mapLink">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#FFFFFF" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
									  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
									</svg>
								</a>
							</div>
							<script type="text/javascript">
							jQuery("#mapLinkScroll").click(function() {
								jQuery('html, body').animate({
									scrollTop: jQuery("#shelllocation").offset().top
								}, 1000);
							});
							</script>
							<?php
						}
						?>
					</div>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?> thumbnailbox">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> no-gutters">
						<?php
						$k = 0;
						if(count($photos) >= 10)
						{
							$max_photo = 10;
						}
						else
						{
							$max_photo = count($photos);
						}
						for($i = 1; $i< $max_photo; $i++)
						{
							$photo = $photos[$i];
							if(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/thumb/'.$photo->image)
							{
								$k++;
								?>
								<div class="cols-4">
									<div class="g5core__embed-responsive g5core__post-featured g5core__metro g5core__image-size-1x1">
										<div class="g5core__metro-inner">
											<div class="g5core__entry-thumbnail w-100 h-100" style="background-image:url(<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id;?>/thumb/<?php echo $photo->image?>);">
												 <a data-g5core-mfp href="#" class="g5core__zoom-image" onClick="openSlideshow(<?php echo $i;?>)" data-index="<?php echo $i;?>">
													<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#FFFFFF" class="bi bi-arrows-fullscreen" viewBox="0 0 16 16">
													  <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707zm4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707zm0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707zm-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707z"/>
													</svg>
												</a>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>
		<?php 
		}					
		?>
		
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> summary singleTop">
			<?php
			if($configClass['show_agent_details'] == 1)
			{
				$span = $bootstrapHelper->getClassMapping('span8');
			}
			else
			{
				$span = $bootstrapHelper->getClassMapping('span12');
			}
			?>
			<div class="<?php echo $span; ?>">
				<div class="descriptionBox">
					<h3><?php echo Text::_('OS_DESCRIPTION')?></h3>
					<?php echo $row->category_name?>
					<div class="clearfix"></div>
					<?php
					$row->pro_small_desc = OSPHelper::getLanguageFieldValue($row,'pro_small_desc');
					echo $row->pro_small_desc;
					if($configClass['use_open_house'] == 1){
						?>
						<div class="floatright">
							<?php echo $row->open_hours;?>
						</div>
						<?php
					}
					$pro_full_desc = OSPHelper::getLanguageFieldValue($row,'pro_full_desc');
					$row->pro_full_desc =  HTMLHelper::_('content.prepare', $pro_full_desc);
					echo stripslashes($row->pro_full_desc);
					?>
				</div>
			</div>

			<?php
			if($configClass['show_agent_details'] == 1)
			{
			?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?> summaryItem center ">
					<div class="agentBox">
						<?php
						if($configClass['show_agent_image'] == 1 && $row->agentdetails->photo != "" && file_exists(JPATH_ROOT.'/images/osproperty/agent/thumbnail/'.$row->agentdetails->photo))
						{
							$link = Route::_("index.php?option=com_osproperty&task=agent_info&id=".$row->agent_id."&Itemid=".OSPRoute::getAgentItemid($row->agent_id));
							?>
							<div class="g5core__post-featured-agent g5ere__agent-featured g5ere__post-featured-circle">
								<a href="<?php echo $link; ?>" class="g5core__entry-thumbnail g5core__embed-responsive" style="--g5core-image-ratio : 100%;background-image: url('<?php echo Uri::root()."images/osproperty/agent/thumbnail/".$row->agentdetails->photo ?>')" alt="<?php echo $row->agentdetails->name;?>">
									
								</a>
							</div>
							<?php
						}
						?>
						<div class="agentName"><?php echo $row->agentdetails->name;?></div>
						<?php
						$link = Route::_("index.php?option=com_osproperty&task=agent_info&id=".$row->agent_id."&Itemid=".OSPRoute::getAgentItemid());
						?>
						<?php
						if($configClass['show_agent_email'] == 1 && $row->agentdetails->email != "")
						{
						?>
							<a href="mailto:<?php echo $row->agentdetails->email;?>" target="_blank"><?php echo $row->agentdetails->email;?></a>
						<?php
						}	
						?>
						<BR />
						<?php
						if($configClass['show_agent_phone'] == 1 && $row->agentdetails->phone != "")
						{
							?>
							<a href="tel:<?php echo $row->agentdetails->phone; ?>" target="_blank">
								<?php echo $row->agentdetails->phone; ?>
							</a>
							<?php
						}
						?>
							<BR />
						<a class="btn btn-green contactBtn" href="<?php echo $link;?>"><?php echo Text::_('OS_CONTACT_OWNER');?></a>
						<BR /><BR />
						<div class= "agentdetails">
							<ul class="social marT15 marL0">
								<?php
								if(($configClass['show_agent_skype'] == 1) && ($row->agentdetails->skype != ""))
								{
									?>
									<li class="skype">
										<a href="skype:<?php echo $row->agentdetails->skype; ?>" target="_blank">
											<i class="edicon edicon-skype"></i>
										</a>
									</li>
									<?php
								}
								?>
								<?php
								if(($row->agentdetails->facebook != "") && ($configClass['show_agent_facebook'] == 1)){
									?>
									<li class="facebook">
										<a href="<?php echo $row->agentdetails->facebook; ?>" target="_blank">
											<i class="edicon edicon-facebook"></i>
										</a>
									</li>
								<?php }
								if(($row->agentdetails->aim != "") && ($configClass['show_agent_twitter'] == 1)){
									?>
									<li class="twitter">
										<a href="<?php echo $row->agentdetails->aim; ?>" target="_blank">
											<i class="edicon edicon-twitter"></i>
										</a>
									</li>
								<?php }
								if(($row->agentdetails->yahoo != "") && ($configClass['show_agent_linkin'] == 1)){
									?>
									<li class="linkin">
										<a href="<?php echo $row->agentdetails->yahoo; ?>" target="_blank">
											<i class="edicon edicon-linkedin2"></i>
										</a>
									</li>
								<?php }
								if(($row->agentdetails->gtalk != "") && ($configClass['show_agent_gplus'] == 1)){
									?>
									<li class="gplus">
										<a href="<?php echo $row->agentdetails->gtalk; ?>" target="_blank">
											<i class="edicon edicon-google-plus"></i>
										</a>
									</li>
								<?php }
								?>
								<?php
								if(($configClass['show_agent_msn'] == 1) && ($row->agentdetails->msn != ""))
								{
									?>
									 <li class="line">
										<a href="https://line.me/R/home/public/main?id=<?php echo $row->agentdetails->msn; ?>" target="_blank">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-line" viewBox="0 0 16 16">
											  <path d="M8 0c4.411 0 8 2.912 8 6.492 0 1.433-.555 2.723-1.715 3.994-1.678 1.932-5.431 4.285-6.285 4.645-.83.35-.734-.197-.696-.413l.003-.018.114-.685c.027-.204.055-.521-.026-.723-.09-.223-.444-.339-.704-.395C2.846 12.39 0 9.701 0 6.492 0 2.912 3.59 0 8 0ZM5.022 7.686H3.497V4.918a.156.156 0 0 0-.155-.156H2.78a.156.156 0 0 0-.156.156v3.486c0 .041.017.08.044.107v.001l.002.002.002.002a.154.154 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157Zm.791-2.924a.156.156 0 0 0-.156.156v3.486c0 .086.07.155.156.155h.562c.086 0 .155-.07.155-.155V4.918a.156.156 0 0 0-.155-.156h-.562Zm3.863 0a.156.156 0 0 0-.156.156v2.07L7.923 4.832a.17.17 0 0 0-.013-.015v-.001a.139.139 0 0 0-.01-.01l-.003-.003a.092.092 0 0 0-.011-.009h-.001L7.88 4.79l-.003-.002a.029.029 0 0 0-.005-.003l-.008-.005h-.002l-.003-.002-.01-.004-.004-.002a.093.093 0 0 0-.01-.003h-.002l-.003-.001-.009-.002h-.006l-.003-.001h-.004l-.002-.001h-.574a.156.156 0 0 0-.156.155v3.486c0 .086.07.155.156.155h.56c.087 0 .157-.07.157-.155v-2.07l1.6 2.16a.154.154 0 0 0 .039.038l.001.001.01.006.004.002a.066.066 0 0 0 .008.004l.007.003.005.002a.168.168 0 0 0 .01.003h.003a.155.155 0 0 0 .04.006h.56c.087 0 .157-.07.157-.155V4.918a.156.156 0 0 0-.156-.156h-.561Zm3.815.717v-.56a.156.156 0 0 0-.155-.157h-2.242a.155.155 0 0 0-.108.044h-.001l-.001.002-.002.003a.155.155 0 0 0-.044.107v3.486c0 .041.017.08.044.107l.002.003.002.002a.155.155 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156Z"/>
											</svg>
										</a>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			<?php 
			} 
			?>
		</div>

		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> descriptionTop">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> description">
				<?php
				if(($configClass['energy'] == 1) && (($row->energy > 0) || ($row->climate > 0) || ($row->e_class != "") || ($row->c_class != ""))){
				?>
					<h3><?php echo Text::_('OS_EPC')?></h3>
					<div class="entry-content">
						<?php
						echo HelperOspropertyCommon::drawGraph($row->energy, $row->climate,$row->e_class,$row->c_class);
						?>
					</div>
				<?php } ?>
				
				<?php if(count((array)$tagArr) > 0){ ?>
				<h3><i class="edicon edicon-tags"></i>&nbsp;<?php echo Text::_('OS_TAGS')?></h3>
				<div class="entry-content">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
						<?php echo implode(" ",$tagArr);?>
					</div>
					<div class="clearfix"></div>
				</DIV>
				<?php } ?>

				<?php if($configClass['social_sharing']== 1){ ?>
					<h3><i class="edicon edicon-share2"></i>&nbsp;<?php echo Text::_('OS_SHARE')?></h3>
					<div class="entry-content">
						<?php
						$url = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id");
						$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$url;
						?>
							<a href="http://www.facebook.com/share.php?u=<?php echo $url;?>" target="_blank" class="btn btn-sm btn-o btn-facebook" title="<?php echo Text::_('OS_ASK_YOUR_FACEBOOK_FRIENDS');?>" id="link2Listing" rel="canonical">
								<i class="edicon edicon-facebook"></i>
								<?php echo Text::_('OS_FACEBOOK')?>
							</a>
							&nbsp;
							<a href="https://twitter.com/intent/tweet?original_referer=<?php echo $url;?>&tw_p=tweetbutton&url=<?php echo $url;?>" target="_blank" class="btn btn-sm btn-o btn-twitter" title="<?php echo Text::_('OS_ASK_YOUR_TWITTER_FRIENDS');?>" id="link2Listing" rel="canonical">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
								  <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/>
								</svg>
								<?php echo Text::_('OS_TWEET')?>
							</a>

						<div class="clearfix"></div>
					</DIV>
				<?php } ?>
			</div>
		</div>

		<?php
		if(count($middlePlugin) > 0){
			for($i=0;$i<count($middlePlugin);$i++){
				echo $middlePlugin[$i];
			}
		}
		?>
		<!-- description list -->
		<?php
		$fieldok = 0;
		if(count($row->extra_field_groups) > 0){
			$extra_field_groups = $row->extra_field_groups;
			for($i=0;$i<count($extra_field_groups);$i++){
				$group = $extra_field_groups[$i];
				$group_name = $group->group_name;
				$fields = $group->fields;
				if(count($fields)> 0){
					$fieldok = 1;
				}
			}
		}
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<nav id="listing-sections">
					<ul>
						<li class="listing-nav-icon">
							<i class="edicon edicon-paragraph-justify"></i>
						</li>
						<?php
						if(($configClass['show_amenity_group'] == 1) || ($fieldok == 1) || ($configClass['show_neighborhood_group'] == 1)){
							?>
							<li>
								<a href="#shellfeatures"><?php echo Text::_('OS_FEATURES');?></a>
							</li>
						<?php } ?>
						<?php
						if(($configClass['goole_use_map'] == 1) && ($row->lat_add != "") && ($row->long_add != "")){
							?>
							<li>
								<a href="#shelllocation"><?php echo Text::_('OS_LOCATION')?></a>
							</li>
						<?php } ?>
						<?php
						if(($configClass['show_walkscore'] == 1) && ($configClass['ws_id'] != "")){
							?>
							<li>
								<a href="#shellwalkscore"><?php echo Text::_('OS_NEARBY')?></a>
							</li>
						<?php } ?>
						<?php
						if($row->pro_video != "") {
							?>
							<li>
								<a href="#shellvideo"><?php echo Text::_('OS_VIDEO') ?></a>
							</li>
							<?php
						}
						if($configClass['comment_active_comment'] == 1){
							?>
							<li>
								<a href="#shellcomments"><?php echo Text::_('OS_COMMENTS');?></a>
							</li>
						<?php } ?>
						<?php
						if($configClass['property_mail_to_friends'] == 1){
						?>
						<li>
							<a href="#shellsharing"><?php echo Text::_('OS_SHARING');?></a>
						</li>
						<?php }
						if($row->panorama != "") {
							?>
							<li>
								<a href="<?php echo Uri::root(); ?>index.php?option=com_osproperty&task=property_showpano&id=<?php echo $row->id ?>&tmpl=component"
								   class="osmodal" rel="{handler: 'iframe', size: {x: 650, y: 420}}">
									<?php echo Text::_('OS_PANORAMA'); ?>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
				</nav>
			</div>
		</div>

		<?php
		//load module at right hand side
		if($module_ids != "")
		{
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?>">
			<?php
		}
		?>

		<?php
		if(($configClass['show_amenity_group'] == 1) || ($fieldok == 1) || ($configClass['show_neighborhood_group'] == 1)){
		?>
		<div id="shellfeatures <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" class="overview">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<h3>
					<?php echo Text::_('OS_FEATURES')?>
				</h3>
				<div class="listing-features">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> corefields">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
									<?php
									echo $row->core_fields1;
									?>
								</div>
							</div>
							<?php

							if(($configClass['show_amenity_group'] == 1) && ($row->amens_str1 != "")){
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> amenitiesfields">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<h4>
												<?php echo Text::_('OS_AMENITIES')?>
											</h4>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<?php echo $row->amens_str1;?>
										</div>
									</div>
								</div>
							</div>
							<?php
							}
							?>
							<?php
							if(($configClass['show_neighborhood_group'] == 1) && ($row->neighborhood != "")){
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> neighborfields">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<h4>
												<?php echo Text::_('OS_NEIGHBORHOOD')?>
											</h4>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<?php 
										echo $row->neighborhood;
										?>
									</div>
								</div>
							</div>
							
							<?php } ?>
							<?php
							if($row->pro_pdf != "" || $row->pro_pdf_file != "" || $row->pro_pdf_file1 != "" || $row->pro_pdf_file2 != ""|| $row->pro_pdf_file3 != ""|| $row->pro_pdf_file4 != ""|| $row->pro_pdf_file5 != ""|| $row->pro_pdf_file6 != ""|| $row->pro_pdf_file7 != ""|| $row->pro_pdf_file8 != ""|| $row->pro_pdf_file9 != ""){
							?>
								<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> documentfields">
									<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<h4><?php echo Text::_('OS_DOCUMENT')?></h4>
											</div>
										</div>
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<?php
												if($row->pro_pdf != "")
												{
													?>
													<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> documentElement">
														<figure class="media-thumb">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
															  <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
															  <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
															</svg>
														</figure>
														<div class="media-info">
															<p>
																<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>
															</p>
															<a href="<?php echo $row->pro_pdf?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank" class="btn btn-primary btn-download">
															<?php echo Text::_('OS_OPEN_DOCUMENT');?>
															<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
															  <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/>
															  <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/>
															</svg>
															</a>
														</div>
													</div>
													<?php
												}
												for($f = 0; $f < 10 ; $f++)
												{
													if($f == 0)
													{
														$fname = "";
													}
													else
													{
														$fname = $f;
													}
													$name = "pro_pdf_file".$fname;
													if($row->{$name} != "")
													{
														if(file_exists(JPATH_ROOT.'/media/com_osproperty/document/'.$row->{$name}))
														{
															$fileUrl = Uri::root().'media/com_osproperty/document/'.$row->{$name};
														}
														else
														{
															$fileUrl = Uri::root().'components/com_osproperty/document/'.$row->{$name};
														}
														?>
														<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> documentElement">
															

															<figure class="media-thumb">
																<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
																  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
																  <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
																</svg>
															</figure>
															<div class="media-info">
																<p>
																	<?php echo $row->{$name}?>
																</p>
																<a href="<?php echo $fileUrl; ?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank" class="btn btn-primary btn-download">
																	<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>
																	
																	<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
																	  <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1h-2z"/>
																	  <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708l3 3z"/>
																	</svg>
																</a>
															</div>
														</div>
														<?php
													}
												}
												?>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php
							if(count($row->extra_field_groups) > 0)
							{
								?>
								<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> customfields">
									<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<h4>
													<?php echo Text::_('OS_OTHER_INFORMATION')?>
												</h4>
											</div>
										</div>
										<?php
										if($extrafieldncolumns == 2){
											$span = $bootstrapHelper->getClassMapping('span6');
											$jump = 2;
										}else{
											$span = $bootstrapHelper->getClassMapping('span4');
											$jump = 3;
										}
										$extra_field_groups = $row->extra_field_groups;
										for($i=0;$i<count($extra_field_groups);$i++){
											$group = $extra_field_groups[$i];
											$group_name = $group->group_name;
											$fields = $group->fields;
											if(count($fields)> 0){
											?>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
												<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
													<h5>
														<?php echo $group_name;?>
													</h5>
												</div>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
												<?php
												$k = 0;
												for($j=0;$j<count($fields);$j++){
													$field = $fields[$j];
													if($field->field_type != "textarea"){
														$k++;
														?>
														<div class="<?php echo $span; ?>">
															<?php
															if(($field->displaytitle == 1) || ($field->displaytitle == 2)){
																?>
																<?php
																if($field->field_description != ""){
																	?>
																	<span class="editlinktip hasTooltip" title="<?php echo $field->field_label;?>::<?php echo $field->field_description?>">
																		<?php echo $field->field_label;?>
																	</span>
																<?php
																}else{
																	echo $field->field_label;
																}
															}
															?>
															<?php
															if($field->displaytitle == 1){
																?>
																:&nbsp;
															<?php } ?>
															<?php if(($field->displaytitle == 1) || ($field->displaytitle == 3)){?>
																<?php echo $field->value;?> <?php } ?>
														</div>
														<?php
														if($k == $jump){
															?>
															</div><div class='<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> minheight0'>
															<?php
															$k = 0;
														}
													}
												}
												?>
											</div>
											<?php
												for($j=0;$j<count($fields);$j++) {
													$field = $fields[$j];
													if ($field->field_type == "textarea") {
														?>
														<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
															<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
																<?php
																if (($field->displaytitle == 1) || ($field->displaytitle == 2)) {
																	?>
																	<?php
																	if ($field->field_description != "") {
																		?>
																		<span class="editlinktip hasTooltip"
																			  title="<?php echo $field->field_label;?>::<?php echo $field->field_description?>">
																			<strong><?php echo $field->field_label;?></strong>
																		</span>
																		<BR/>
																	<?php
																	} else {
																		?>
																		<strong><?php echo $field->field_label;?></strong>
																	<?php
																	}
																}
																?>
																<?php if (($field->displaytitle == 1) || ($field->displaytitle == 3)) { ?>
																	<?php echo $field->value; ?>
																<?php } ?>
															</div>
														</div>
													<?php
													}
												}
											}
										?>
										<div class='amenitygroup <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>'></div>
										<?php
										}
									?>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
		}
		?>
		<?php
		if($module_ids != "")
		{
			?>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span4');?>">
                    <?php
                    $document = Factory::getDocument();
                    $renderer = $document->loadRenderer('module');
					$moduleArr = explode(",", $module_ids);

					foreach($moduleArr as $module)
					{
						$db->setQuery("Select title from #__modules where id = '$module' and showtitle = '1'");
						$module_title = $db->loadResult();
                    ?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('span12');?>">
								<h3 class="module_title"><?php echo $module_title;?></h3>
								<?php
								$moduleContent->text = "{loadmoduleid ".$module."}";
								$moduleContent->text = HTMLHelper::_('content.prepare', $moduleContent->text);
								echo $moduleContent->text;
								?>
							</div>
						</div>
						<?php
					}
                    ?>
                </div>
			</div>
			<?php
		}
		?>
		<!-- end des -->
		<?php
		if($row->show_address == 1 && $row->lat_add != "" && $row->long_add != "" && $row->lat_add != "0" && $row->long_add != "0")
		{

		$address = OSPHelper::generateAddress($row);
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> location">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<div id="shelllocation">
					<h3>
						<?php echo Text::_('OS_LOCATION')?>
					</h3>
					<?php
                    if($configClass['map_type'] == 1)
                    {
                        HelperOspropertyOpenStreetMap::loadOpenStreetMapDetails($row, $configClass, '', 1);
                    }
                    else
                    {
                        HelperOspropertyGoogleMap::loadGoogleMapDetails($row, $configClass, '', 1);
                    }
					?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<?php
		if(($configClass['show_walkscore'] == 1) && ($configClass['ws_id'] != "")){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> walkscore">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div id="shellwalkscore">
						<h3>
							<?php echo Text::_('OS_WALK_SCORE')?>
						</h3>
						<?php
						echo $row->ws;
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<?php
		if($row->pro_video != "")
		{
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> video">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div id="shellvideo">
						<h3>
							<?php echo Text::_('OS_VIDEO')?>
						</h3>
						<?php
						if (stripos(strtolower($row->pro_video), "watch") !== false) 
						{
							$row->pro_video = OSPHelper::convertToEmbedLink($row->pro_video);
						}
						if (stripos(strtolower($row->pro_video), "iframe") === false) {
							?>
							<iframe src="<?php echo $row->pro_video; ?>" allowfullscreen="allowfullscreen" frameborder="0" height="480" width="100%"></iframe>
							<?php
						}
						else
						{
							echo $row->pro_video;
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}

		if($row->tour_link != "")
		{
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> virtual_tour">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div id="shelltour">
						<h3 id="shelltourh3">
							<?php echo Text::_('OS_VIRTUAL_TOUR')?>
						</h3>
						<?php
						if (stripos(strtolower($row->tour_link), "iframe") === false) {
		
							?>
							<iframe src="<?php echo $row->tour_link; ?>" allowfullscreen="allowfullscreen" frameborder="0" height="480" width="100%"></iframe>
							<?php
						}
						else
						{
							echo $row->tour_link;
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<?php
		if($configClass['comment_active_comment'] == 1){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> comment">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
					<div id="shellcomments">
						<h3>
							<?php echo Text::_('OS_COMMENTS')?>
						</h3>
						<?php
						echo $row->comments;
						if(($owner == 0) && ($can_add_cmt == 1)){
							HelperOspropertyCommon::reviewForm($row,$itemid,$configClass);
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<?php
		if(($configClass['use_property_history'] == 1) && (($row->price_history != "") || ($row->tax != ""))){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> history">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div id="shellhistorytax">
						<h3>
							<?php echo Text::_('OS_HISTORY_TAX')?>
						</h3>
						<?php
						if($row->price_history != ""){
							echo $row->price_history;
							echo "<BR />";
						}
						if($row->tax != ""){
							echo $row->tax;
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<?php
		if($configClass['property_mail_to_friends'] == 1){
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> tellfrendform" id="shellsharing">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> tellfrendformsub">
				<h3>
					<?php echo Text::_('OS_TELL_A_FRIEND')?>
				</h3>
				<?php HelperOspropertyCommon::sharingForm($row,$itemid); ?>
			</div>
		</div>
		<?php } ?>
		<?php
			if(file_exists(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."oscalendar.php")){
				if(($configClass['integrate_oscalendar'] == 1) && (in_array($row->pro_type,explode("|",$configClass['show_date_search_in'])))){
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> tellfrendform" id="shellsharing">
								<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> tellfrendformsub">
									<h2>
										<i class="edicon edicon-calendar"></i>&nbsp;<?php echo Text::_('OS_AVAILABILITY')?>
									</h2>
									<?php
									require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.php");
									require_once(JPATH_ROOT.DS."components".DS."com_oscalendar".DS."classes".DS."default.html.php");
									$otherlanguage =& Factory::getLanguage();
									$otherlanguage->load( 'com_oscalendar', JPATH_SITE );
									OsCalendarDefault::calendarForm($row->id);
									?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
		
		if($configClass['show_agent_details'] == 1 || $configClass['show_request_more_details'] == 1){
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> contactagent">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> agentsharingform" id="agentsharing">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> agentsharingformsub">
							<?php
							if($configClass['show_agent_details'] == 1){
								$link = Route::_("index.php?option=com_osproperty&task=agent_info&id=".$row->agent_id."&Itemid=".OSPRoute::getAgentItemid());
								if($row->agentdetails->agent_type == 0){
									$title = Text::_('OS_AGENT_INFO');
								}else{
									$title = Text::_('OS_OWNER_INFO');
								}
							?>
							<h3>
								<a href="<?php echo $link;?>" title="<?php echo $title;?>">
									<?php echo $row->agent_name;?>
								</a>
							</h3>
							<?php 
							}
							if(($configClass['show_agent_details'] == 1) && ($configClass['show_request_more_details'] == 1)){
								$span1 = $bootstrapHelper->getClassMapping('span4');//"span4";
								$span2 = "";
								$span3 = $bootstrapHelper->getClassMapping('span8');//"span8";
							}elseif(($configClass['show_agent_details'] == 1) && ($configClass['show_request_more_details'] == 0)){
								$span1 = $bootstrapHelper->getClassMapping('span4');
								$span2 = $bootstrapHelper->getClassMapping('span8');
								$span3 = "";
							}elseif(($configClass['show_agent_details'] == 0) && ($configClass['show_request_more_details'] == 1)){
								$span1 = "";
								$span2 = "";
								$span3 = $bootstrapHelper->getClassMapping('span12');
							}
							?>
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
								<?php
								if($configClass['show_agent_details'] == 1){
								?>
								<div class="<?php echo $span1;?>">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<?php
											if($configClass['show_agent_image'] == 1){
												$agent_photo = $row->agentdetails->photo;
												if(($agent_photo != "") && (file_exists(JPATH_ROOT.'/images/osproperty/agent/'.$agent_photo))){
													?>
													<img src="<?php echo Uri::root(true)?>/images/osproperty/agent/<?php echo $agent_photo; ?>" class="agentphoto"/>
													<?php
												}else{
													?>
													<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/user.jpg" class="agentphoto"/>
													<?php
												}
											}
											?>
										</div>
									</div>
									<?php
									if($configClass['show_request_more_details'] == 1){
									?>
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<ul class="marB0 agentbasicinformation divbottom">
													<?php if(($row->agentdetails->phone != "") && ($configClass['show_agent_phone'] == 1)){?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-phone-hang-up"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->phone;?></span>
													</li>
													<?php } 
													if(($row->agentdetails->mobile != "") && ($configClass['show_agent_mobile'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-mobile"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->mobile;?></span>
													</li>
													<?php } 
													if(($row->agentdetails->fax != "") && ($configClass['show_agent_fax'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-printer"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->fax;?></span>
													</li>
													<?php }
													if(($row->agentdetails->email != "") && ($configClass['show_agent_email'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="osicon-mail"></i>
														</span>
														<span class="right"><a href="mailto:<?php echo $row->agentdetails->email;?>" target="_blank"><?php echo $row->agentdetails->email;?></a></span>
													</li>
													<?php }
													if(($row->agentdetails->skype != "") && ($configClass['show_agent_skype'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-skype"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->skype;?></span>
													</li>
													<?php }
													if(($row->agentdetails->msn != "") && ($configClass['show_agent_msn'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-bubble2"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->msn;?></span>
													</li>
													<?php }
													?>
												</ul>
											</div>
										</div>		
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> divbottom">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<ul class="social marT15 marL0">
													<?php
													if(($row->agentdetails->facebook != "") && ($configClass['show_agent_facebook'] == 1)){
													?>
													<li class="facebook">
														<a href="<?php echo $row->agentdetails->facebook; ?>" target="_blank">
															<i class="edicon edicon-facebook"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->aim != "") && ($configClass['show_agent_twitter'] == 1)){
													?>
													<li class="twitter">
														<a href="<?php echo $row->agentdetails->aim; ?>" target="_blank">
															<i class="edicon edicon-twitter"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->yahoo != "") && ($configClass['show_agent_linkin'] == 1)){
													?>
													<li class="linkin">
														<a href="<?php echo $row->agentdetails->yahoo; ?>" target="_blank">
															<i class="edicon edicon-linkedin2"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->gtalk != "") && ($configClass['show_agent_gplus'] == 1)){
													?>
													<li class="gplus">
														<a href="<?php echo $row->agentdetails->gtalk; ?>" target="_blank">
															<i class="edicon edicon-google-plus"></i>
														</a>
													</li>
													<?php }
													?>
												</ul>
											</div>
										</div>
									<?php } ?>
								</div>
								<?php
								}
								if($configClass['show_request_more_details'] == 0){
								?>
								<div class="<?php echo $span2;?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<ul class="marB0 agentbasicinformation">
													<?php if(($row->agentdetails->phone != "") && ($configClass['show_agent_phone'] == 1)){?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-phone-hang-up"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->phone;?></span>
													</li>
													<?php } 
													if(($row->agentdetails->mobile != "") && ($configClass['show_agent_mobile'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-mobile"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->mobile;?></span>
													</li>
													<?php } 
													if(($row->agentdetails->fax != "") && ($configClass['show_agent_fax'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-printer"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->fax;?></span>
													</li>
													<?php }
													if(($row->agentdetails->email != "") && ($configClass['show_agent_email'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="osicon-mail"></i>
														</span>
														<span class="right"><a href="mailto:<?php echo $row->agentdetails->email;?>" target="_blank"><?php echo $row->agentdetails->email;?></a></span>
													</li>
													<?php }
													if(($row->agentdetails->skype != "") && ($configClass['show_agent_skype'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-skype"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->skype;?></span>
													</li>
													<?php }
													if(($row->agentdetails->msn != "") && ($configClass['show_agent_msn'] == 1)){
													?>
													<li class="marT3 marB0">
														<span class="left">
															<i class="edicon edicon-bubble2"></i>
														</span>
														<span class="right"><?php echo $row->agentdetails->msn;?></span>
													</li>
													<?php }
													?>
												</ul>
											</div>
										</div>		
										<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> divbottom">
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
												<ul class="social marT15 marL0">
													<?php
													if(($row->agentdetails->facebook != "") && ($configClass['show_agent_facebook'] == 1)){
													?>
													<li class="facebook">
														<a href="<?php echo $row->agentdetails->facebook; ?>" target="_blank">
															<i class="edicon edicon-facebook"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->aim != "") && ($configClass['show_agent_twitter'] == 1)){
													?>
													<li class="twitter">
														<a href="<?php echo $row->agentdetails->aim; ?>" target="_blank">
															<i class="edicon edicon-twitter"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->yahoo != "") && ($configClass['show_agent_linkin'] == 1)){
													?>
													<li class="linkin">
														<a href="<?php echo $row->agentdetails->yahoo; ?>" target="_blank">
															<i class="edicon edicon-linkedin2"></i>
														</a>
													</li>
													<?php }
													if(($row->agentdetails->gtalk != "") && ($configClass['show_agent_gplus'] == 1)){
													?>
													<li class="gplus">
														<a href="<?php echo $row->agentdetails->gtalk; ?>" target="_blank">
															<i class="edicon edicon-google-plus"></i>
														</a>
													</li>
													<?php }
													?>
												</ul>
											</div>
										</div>
									</div>
								<?php } ?>
								<?php
								if($configClass['show_request_more_details'] == 1){
								?>
								<div class="<?php echo $span3;?> requestmoredetails">
									<h4>
										<i class="edicon edicon-question"></i>&nbsp;
										<?php echo Text::_('OS_REQUEST_MORE_INFOR');?>
									</h4>
									<?php echo HelperOspropertyCommon::requestMoreDetails($row,$itemid); ?>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<?php
		if($configClass['relate_properties'] == 1 && $row->relate != "")
		{
		?>
			<div class="detailsBar clearfix">
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> relate_properties">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="shellrelatedproperties">
							<h3><?php echo Text::_('OS_RELATE_PROPERTY')?></h3>
							<?php
							echo $row->relate;
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
		?>
		<?php
		if($integrateJComments == 1){
		?>
			<div class="detailsBar clearfix">
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="shell">
							<fieldset><legend><span><?php echo Text::_('OS_JCOMMENTS')?></span></legend></fieldset>
							<?php
							$comments = JPATH_SITE . DS .'components' . DS . 'com_jcomments' . DS . 'jcomments.php';
							if (file_exists($comments)) {
								require_once($comments);
								echo JComments::showComments($row->id, 'com_osproperty', $row->pro_name);
							}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
		?>
		<?php
		if(count($bottomPlugin) > 0){
			for($i=0;$i<count($bottomPlugin);$i++){
				echo $bottomPlugin[$i];
			}
		}
		if($configClass['social_sharing'] == 1){
		?>
		<div class="clearfix"></div>
		<?php
		echo $row->social_sharing;
		}
		?>
		<!----------------- end social --------------->
		<!-- end tabs bottom -->
	</div>
</div>
<!-- end wrap content -->
<input type="hidden" name="process_element" id="process_element" value="" />
<script type="text/javascript">
var width = jQuery(".propertyinfoli").width();
jQuery(".pictureslideshow").attr("width",width);
</script>

<script type="text/javascript">
var allElements = document.getElementsByTagName("*");
var allIds = {};
var found = false;
for (var i = 0, n = allElements.length; i < n; ++i) {
  var id = allElements[i].id;
  if (id) {
    if (allIds[id] === undefined) {
      allIds[id] = 1;
    } else {
      found = true;
      console.warn('Duplicate ID #' + id);
    }
  }
}
if (!found) {
  console.log('No duplicate IDs found');
}
</script>

<?php
if(count($photos) > 0)
{
	?>
	<div class="slideshow-container" id="slideshowContainer">
		<div class="grid-close-container">
			<button class="grid-btn" id="gridBtn">&#x2630;</button>
			<button class="pause-btn" id="pauseBtn">❙❙</button>
			<button class="close-btn" id="closeBtn">✖</button>
		</div>
		<div class="slideshow-wrapper">
			<div class="slideshow-track" id="slideshowTrack"></div>
		</div>
		<div class="photo-description" id="photoDescription"></div>
		<div class="thumbnail-container" id="thumbnailContainer"></div>
		<button class="nav-buttons left" id="prevBtn">&#10094;</button>
		<button class="nav-buttons right" id="nextBtn">&#10095;</button>
	</div>
	<?php
	$srcArr = [];
	foreach($photos as $photo)
	{
		$srcArr[] = '{src: "'.$photo->image.'", description: "'.$photo->image_desc.'"}';

	}
	$photoString = implode(",",$srcArr);
?>
	<script>
		const images1 = [
			<?php echo $photoString; ?>
		];
		const propertyId = <?php echo $row->id; ?>;
		const baseUrl = "<?php echo Uri::base();?>";
	</script>
	<script src="<?php echo Uri::root(true)?>/components/com_osproperty/templates/<?php echo $themename;?>/js/scripts.js"></script>
<?php
} //end count photos
?>