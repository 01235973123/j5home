<?php
/*------------------------------------------------------------------------
# listing.html.tpl.php - Ossolution Property
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
echo OSPHelper::loadTooltip();
$user               = Factory::getUser();
$db                 = Factory::getDBO();
$grid_view_columns	= $params->get('grid_view_columns',2);
$document           = Factory::getDocument();
$document->addStyleSheet(Uri::root(true).'/components/com_osproperty/templates/default/style/font.css');
$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$span10Class        = $bootstrapHelper->getClassMapping('span10');
$span2Class         = $bootstrapHelper->getClassMapping('span2');
$span6Class         = $bootstrapHelper->getClassMapping('span6');

?>
<style>
<?php
if($grid_view_columns == 2)
{
	?>
	.grid-view
	{
		grid-template-columns: repeat(2, 1fr);
	}

	.grid-view .property-card .swiper
	{
		height:250px;
	}
	<?php
}
elseif($grid_view_columns == 3)
{
	?>
	.grid-view
	{
		grid-template-columns: repeat(3, 1fr);
	}
	<?php
}
?>
</style>
<div id="notice" style="display:none;">
	
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<?php
HTMLHelper::_('bootstrap.dropdown');
?>
<div style="display:none;">
<?php
echo $lists['sortby'];
echo $lists['ordertype'];
?>
</div>
<div id="listings">
	<?php
	if(count($rows) > 0)
	{
	?>
        <div class="<?php echo $rowFluidClass; ?> defaultbar">
            <div class="<?php echo $span6Class; ?> pull-left">
                <div style="margin:0 0 10px 0;">
					<button id="toggleGridView" class="btn btn-outline-primary">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-3x3-gap-fill" viewBox="0 0 16 16">
						  <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
						</svg>
					</button>
					<button id="toggleListView" class="btn btn-outline-secondary">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
						  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
						</svg>
					</button>
				</div>
            </div>
            <div class="<?php echo $span6Class; ?> pull-right alignright">
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<div class="dropdown inlinedisplay">
							<button class="btn dropdown-toggle" type="button" id="propertiesSortButton" data-bs-toggle="dropdown" aria-expanded="false">
							  <?php
								echo Text::_('OS_SORT_BY'). " ".$lists['orderby_selected_text']." ".$lists['ordertype_selected_text'];
							  ?>
							</button>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							  <li><a class="dropdown-item" id="a.ordering" data-sortby="a.ordering" data-orderby="" href="javascript:updateSort('a.ordering','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_ORDERING')?>')"><?php echo Text::_('OS_ORDERING')?></a></li>
							  <li><a class="dropdown-item" id="a.created_desc" data-sortby="a.created" data-orderby="desc" href="javascript:updateSort('a.created_desc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_CREATED')."  ".Text::_('OS_DESC')?>')"><?php echo Text::_('OS_CREATED')."  ".Text::_('OS_DESC'); ?></a></li>
							  <li><a class="dropdown-item" id="a.created_asc" data-sortby="a.created" data-orderby="asc" href="javascript:updateSort('a.created_asc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_CREATED')."  ".Text::_('OS_ASC')?>')"><?php echo Text::_('OS_CREATED')."  ".Text::_('OS_ASC'); ?></a></li>

							  <li><a class="dropdown-item" id="a.modified_desc" data-sortby="a.modified" data-orderby="desc" href="javascript:updateSort('a.modified_desc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_MODIFIED')."  ".Text::_('OS_DESC')?>')"><?php echo Text::_('OS_MODIFIED')."  ".Text::_('OS_DESC'); ?></a></li>
							  <li><a class="dropdown-item" id="a.modified_asc" data-sortby="a.modified" data-orderby="asc" href="javascript:updateSort('a.modified_asc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_MODIFIED')."  ".Text::_('OS_ASC')?>')"><?php echo Text::_('OS_MODIFIED')."  ".Text::_('OS_ASC'); ?></a></li>

							  <li><a class="dropdown-item" id="a.pro_name_desc" data-sortby="a.pro_name" data-orderby="desc" href="javascript:updateSort('a.pro_name_desc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_TITLE')."  ".Text::_('OS_DESC')?>')"><?php echo Text::_('OS_TITLE')."  ".Text::_('OS_DESC'); ?></a></li>
							  <li><a class="dropdown-item" id="a.pro_name_asc" data-sortby="a.pro_name" data-orderby="asc" href="javascript:updateSort('a.pro_name_asc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_TITLE')."  ".Text::_('OS_ASC')?>')"><?php echo Text::_('OS_TITLE')."  ".Text::_('OS_ASC'); ?></a></li>
							  <li><a class="dropdown-item" id="a.price_desc" data-sortby="a.price" data-orderby="desc" href="javascript:updateSort('a.price_desc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_PRICE')."  ".Text::_('OS_DESC')?>')"><?php echo Text::_('OS_PRICE')."  ".Text::_('OS_DESC'); ?></a></li>
							  <li><a class="dropdown-item" id="a.price_asc" data-sortby="a.price" data-orderby="asc" href="javascript:updateSort('a.price_asc','<?php echo Text::_('OS_SORT_BY')." ".Text::_('OS_PRICE')."  ".Text::_('OS_ASC')?>')"><?php echo Text::_('OS_PRICE')."  ".Text::_('OS_ASC'); ?></a></li>
							</ul>
						  </div>
					</div>
				</div>
				<script type="text/javascript">
				//window.onload = function() {
				function updateSort(type, text)
				{
					item = document.getElementById(type);
					sortby = item.getAttribute('data-sortby');
					orderby = item.getAttribute('data-orderby');
					document.getElementById('orderby').value = sortby;
					document.getElementById('ordertype').value = orderby;
					var button = document.getElementById('propertiesSortButton');
					button.textContent = text;
					document.ftForm.submit();
				}
				//}
				</script>
            </div>
        </div>
		<input type="hidden" name="currency_item" id="currency_item" value="" />
		<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />

		<div id="propertyContainer" class="grid-view">
			<?php
			for($i=0; $i<count($rows); $i++)
			{
				$row = $rows[$i];
				$needs = [];
				$needs[] = "property_details";
				$needs[] = $row->id;
				$itemid = OSPRoute::getItemid($needs);
				
				$lists['curr'] = HTMLHelper::_('select.genericlist',$currenyArr,'curr'.$i,'onChange="javascript:convertCurrencyDefault('.$row->id.',this.value,0)" class="input-small"','value','text');
				?>
				<div class="property-card">
					<div class="swiper mySwiper">
						<div class="property-type"><?php echo $row->type_name; ?></div>
						<?php
						if(intval($user->id) > 0)
						{
							if($configClass['property_save_to_favories'] == 1)
							{
								//if($task != "property_favorites"){
								$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
								$count = $db->loadResult();
								if($count == 0)
								{
									$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
									$msg = str_replace("'","\'",$msg);
									?>
									<div class="favorite-btn" id="fav<?php echo $row->id;?>">
										<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo $msg;?>">
											<i class="icon-heart white"></i>
										</a>
									</div>
									<?php
								}
								if($count > 0)
								{
									$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
									$msg = str_replace("'","\'",$msg);
									?>
									<div class="favorite-btn" id="fav<?php echo $row->id;?>">
										<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo $msg;?>">
											<i class="icon-heart favred"></i>
										</a>
									</div>
									<?php
								}
							}
						}
						?>
						<div class="swiper-wrapper">
							<?php
							if($row->count_photo > 0)
							{
								$photos = $row->photoArr;
								foreach($photos as $photo)
								{
									?>
									<div class="swiper-slide" data-url="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid);?>"><img src="<?php echo Uri::base()?>images/osproperty/properties/<?php echo $row->id?>/medium/<?php echo $photo;?>" alt=""></div>
									<?php
								}
							}	
							?>
						</div>
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
					</div>
					<div class="card-body">
						<h5 class="card-title"><a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$row->id."&Itemid=".$itemid);?>" title="<?php echo $row->pro_name?>"><?php echo $row->pro_name?></a></h5>

						<div class="property-price">
							<?php
							if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
							{
								echo " ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
							}
							elseif($row->price_call == 0)
							{
								if($row->price > 0)
								{
									?>
									<div id="currency_div<?php echo $row->id;?>">
											<?php
											echo OSPHelper::generatePrice($row->curr,$row->price);
											if($row->rent_time != ""){
												echo "/".Text::_($row->rent_time);
											}
											?>
										</div>
									<?php
								}
							}
							else
							{
								echo Text::_('OS_CALL_FOR_PRICE');
							}
							?>
						</div>
						<?php
						if($row->show_address == 1 && $configClass['listing_show_address'] == 1){
							?>
							<p class="property-address">
								<i class="fa fa-map-marker"></i>
								<?php
								echo OSPHelper::generateAddress($row);
								?>
							</p>
							<?php
						}
						?>
						<div class="property-description">
							<?php 
							$pro_small_desc = $row->pro_small_desc;
							$pro_small_descArr = explode(" ",$pro_small_desc);
							if(count($pro_small_descArr) > 15){
								for($j=0;$j<15;$j++){
									echo $pro_small_descArr[$j]." ";
								}
								echo "..";
							}else{
								echo $pro_small_desc;
							}
							?>
						</div>
						
						<div class="property-details">
							<?php
							if($configClass['listing_show_nbedrooms'] == 1 && $row->bed_room > 0)
							{
								?>
									<div class="property-detail">
										<i class="fas fa-bed"></i>&nbsp;<?php echo $row->bed_room;?> 
									</div>
								<?php
							}
							?>
							<?php
							if($configClass['listing_show_nbathrooms'] == 1 && $row->bath_room > 0)
							{
								?>
								<div class="property-detail">
									<i class="fas fa-bath"></i>&nbsp;<?php echo OSPHelper::showBath($row->bath_room);?>
								</div>
								<?php
							}
							?>
							<?php
							if($configClass['use_squarefeet'] == 1 && $row->square_feet > 0)
							{
								?>
								<div class="property-detail">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-fullscreen" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707m4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707m0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707m-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707"/>
</svg>&nbsp;<?php
									echo OSPHelper::showSquare($row->square_feet);
									echo "&nbsp;";
									echo OSPHelper::showSquareSymbol();
									?>
								</div>
								<?php
							}
							?>
						</div>						
						<div class="divider"></div>
						<div class="agent-info">
							<div class="agent-avatar">
								<a title="<?php echo $row->agent_name?>" href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->agent_id.'&Itemid='.$jinput->getInt('Itemid',0));?>">
								<?php
								if($row->agent_photo != "")
								{
									if(file_exists(JPATH_ROOT.DS."images".DS."osproperty".DS."agent".DS."thumbnail".DS.$row->agent_photo))
									{
										?>
										<img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent_photo?>" class="border1 padding3 height60px"/>
										<?php
									}
									else
									{
										?>
										<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" class="border1 padding3 height60px" />
										<?php
									}
								}
								else
								{
									?>
									<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" class="border1 padding3 height60px"/>
									<?php
								}
								?>
								</a>
							</div>

							<div class="agent-name">
								<p><?php echo $row->agent_name?></p>
								<p class="agent-phone">
								<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
								</svg>
								<?php echo $row->agent_phone?></p>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>
<div>
    <?php
    if(count($rows) > 0 && $pageNav->total > $pageNav->limit){
        ?>
        <div class="pageNavdiv">
			<?php
				echo $pageNav->getListFooter();
			?>
		</div>
		<?php
	}
	?>
</div>
<input type="hidden" name="process_element" id="process_element" value="" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.querySelectorAll(".mySwiper").forEach(swiper => {
	new Swiper(swiper, {
		loop: true,
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
	});
});

document.addEventListener("DOMContentLoaded", function () {
	const propertyContainer = document.getElementById("propertyContainer");
	const btnGridView = document.getElementById("toggleGridView");
	const btnListView = document.getElementById("toggleListView");

	// Kiểm tra kích thước màn hình khi tải trang
	function checkScreenSize() {
		if (window.innerWidth < 768) {
			// Mobile: Luôn ở Grid View
			propertyContainer.classList.add("grid-view");
			propertyContainer.classList.remove("list-view");

			// Ẩn nút chuyển đổi trên mobile
			btnGridView.style.display = "none";
			btnListView.style.display = "none";
		} else {
			// Desktop: Hiển thị cả hai chế độ
			btnGridView.style.display = "inline-block";
			btnListView.style.display = "inline-block";
		}
	}

	// Gọi hàm khi tải trang và khi resize cửa sổ
	checkScreenSize();
	window.addEventListener("resize", checkScreenSize);

	function toggleView(view) {
		if (view === "grid") {
			propertyContainer.classList.add("hidden");
			setTimeout(() => {
				propertyContainer.classList.remove("hidden");
				propertyContainer.classList.remove("list-view");
				propertyContainer.classList.add("grid-view");
			},300);
		} else {
			propertyContainer.classList.add("hidden");
			setTimeout(() => {
				propertyContainer.classList.remove("grid-view");
				propertyContainer.classList.add("list-view");
				propertyContainer.classList.remove("hidden");
			}, 300); // Đợi hiệu ứng fade-out rồi mới đổi
		}
	}

	// Xử lý khi bấm nút Grid View
	btnGridView.addEventListener("click", function () {
		event.preventDefault();
		toggleView("grid");
		btnGridView.classList.add("active");
		btnListView.classList.remove("active");
	});

	// Xử lý khi bấm nút List View
	btnListView.addEventListener("click", function () {
		event.preventDefault();
		toggleView("list");
		btnListView.classList.add("active");
		btnGridView.classList.remove("active");
	});

	document.querySelectorAll('.swiper-slide').forEach(slide => {
		slide.addEventListener('click', () => {
			const url = slide.getAttribute('data-url');
			if (url) {
				window.location.href = url;
			}
		});
	});
});

function loadStateInListPage(){
	var country_id = document.getElementById('country_id');
	loadStateInListPageAjax(country_id.value,"<?php echo Uri::root()?>");
}
function changeCity(state_id,city_id){
	var live_site = '<?php echo Uri::root()?>';
	loadLocationInfoCity(state_id,city_id,'state_id',live_site);
}
</script>