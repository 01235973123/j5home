<?php
/*------------------------------------------------------------------------
# photos.html.tpl.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
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
OSPHelperJquery::colorbox('a.osmodal');
?>
<script type="text/javascript">
function showImage(image,id){
	var temp = document.getElementById('img_main_photo');
	temp.src = "<?php echo Uri::root()?>components/com_osproperty/images/properties/medium/" + image;
	temp = document.getElementById('main_photo');
	temp.href = "<?php echo Uri::root()?>components/com_osproperty/images/properties/" + image;
	var current_image = document.getElementById('current_image');
	current_thumb_photo = current_image.value;
	temp = document.getElementById('thumb_photo_' + current_thumb_photo);
	temp.style.opacity = '1.0';
    temp.style.filter = 'alpha(opacity = 100)';
	temp = document.getElementById('thumb_photo_' + id);
	temp.style.opacity = '0.4';
    temp.style.filter = 'alpha(opacity = 40)';
	current_image.value = id;
}
</script>
		
<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:separate;border:0px;" id="photo_table">
	<tr>
		<td width="100%">
		<div class="div_left_col_photo">
			<table cellpadding="0" cellspacing="0" class="sTable">
				<tr>
					<td style="width: 350px;" valign="top" rowspan="2">
						<table class="photo_item" cellpadding="0" cellspacing="0" style="width:100%;">
							<tr>
								<td class="center_middle" align="center">
									<a  href="<?php echo Uri::root()?>components/com_osproperty/images/properties/<?php echo $photos[0]->image?>" id="main_photo" class="modal">
										<img src="<?php echo Uri::root()?>components/com_osproperty/images/properties/medium/<?php echo $photos[0]->image?>" id="img_main_photo">
									</a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div class="div_right_col_photo">
			<div class="div_thumb_photo_scroll">
				<table cellpadding="0" cellspacing="0" width="100%" style="border:0px;border-collapse:separate;">
					<tr>
						<?php
						$j = 0;
						for($i=0;$i<count($photos);$i++){
							$photo = $photos[$i];
							$j++;
							if($i==0){
								$style = "opacity:0.4;filter:alpha(opacity=40)";
							}else{
								$style = "";
							}
							?>
							<td width="50%" align="<?php echo $align?>" style="padding:0px;border:0px;padding-bottom:0px;padding-top:0px;">
								<a href="javascript:showImage('<?php echo $photo->image?>','<?php echo $i?>');" title="<?php echo $photo->image_desc?>" alt="<?php echo $photo->image_desc?>" style="margin:0px !important;padding:0px;">
									<img src="<?php echo Uri::root()?>components/com_osproperty/images/properties/medium/<?php echo $photo->image?>" id="thumb_photo_<?php echo $i?>" style="<?php echo $style;?>">
								</a>
							</td>
							<?php
							if($j == 2){
								echo "</tr><tr>";
								$j = 0;
							}
						}
						?>
					</tr>
				</table>
			</div>
		</div>
		</td>
	</tr>
</table>
<input type="hidden" name="current_image" id="current_image" value="0">