<?php 
/*------------------------------------------------------------------------
# image.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini'). ' smallSizeBox';
$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('Images Settings')?></legend>
		<?php
		if(($configs['watermark_type'] == 2) and ($configs['watermark_photo']=="")){
			?>
			<tr>
				<td class="td_warning" nowrap="nowrap" colspan="2">
					<i class="icon-cog"></i>
					<?php
					echo Text::_('OS_YOU_HAVE_NOT_SELECT_WATERMARK_PHOTO');
					?>
				</td>
			</tr>
			<?php
		}elseif(($configs['watermark_text'] == 4) and ($configs['custom_text']=="")  and ($configs['watermark_type']==1)){
			?>
			<tr>
				<td class="td_warning" nowrap="nowrap" colspan="2">
					<i class="icon-cog"></i>
					<?php
					echo Text::_('OS_YOU_HAVE_NOT_ENTER_WATERMARK_CUSTOM_TEXT');
					?>
				</td>
			</tr>
			<?php
		}
		?>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('custom_thumbnail_photo',Text::_( 'OS_RESIZE_IMAGE' ),Text::_('OS_RESIZE_IMAGE_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <?php
                //OspropertyConfiguration::showCheckboxfield('custom_thumbnail_photo',$configs['custom_thumbnail_photo']);
                $option_resize = array();
                $option_resize[] =  HTMLHelper::_('select.option','0',Text::_('OS_AUTO_RESIZE_PICTURES_WITHOUT_CROPPING'));
                $option_resize[] =  HTMLHelper::_('select.option','2',Text::_('OS_AUTO_RESIZE_PICTURES_WITH_CROPPING'));
                echo HTMLHelper::_('select.genericlist',$option_resize,'configuration[custom_thumbnail_photo]','class="input-xlarge ilarge form-select"','value','text',isset($configs['custom_thumbnail_photo'])? $configs['custom_thumbnail_photo']:'0');
                ?>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_thumbnail_width',TextOs::_( 'Thumbnail width' ),TextOs::_('THUMB_WIDTH_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[images_thumbnail_width]" value="<?php echo isset($configs['images_thumbnail_width'])?$configs['images_thumbnail_width']:'' ?>">px
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_thumbnail_height',TextOs::_( 'Thumbnail height' ),TextOs::_('THUMB_HEIGHT_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[images_thumbnail_height]" value="<?php echo isset($configs['images_thumbnail_height'])?$configs['images_thumbnail_height']:'' ?>">px
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_large_width',TextOs::_( 'Large width' ),TextOs::_('LARGE_WIDTH_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[images_large_width]" value="<?php echo isset($configs['images_large_width'])?$configs['images_large_width']:'' ?>">px
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_large_height',TextOs::_( 'Large height' ),TextOs::_('LARGE_HEIGHT_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[images_large_height]" value="<?php echo isset($configs['images_large_height'])?$configs['images_large_height']:'' ?>">px
            </div>
        </div>
		<div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('max_width_size',TextOs::_( 'Max width' ),TextOs::_('MAX_WIDTH_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[max_width_size]" value="<?php echo isset($configs['max_width_size'])?$configs['max_width_size']:'' ?>">px
            </div>
        </div>
		<div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('max_height_size',TextOs::_( 'Max height' ),TextOs::_('MAX_HEIGHT_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[max_height_size]" value="<?php echo isset($configs['max_height_size'])?$configs['max_height_size']:'' ?>">px
            </div>
        </div>
		<div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('image_background_color',TextOs::_( 'Background color' ),TextOs::_('Background color explain')); ?>
            </div>
            <div class="controls">
                <?php
                $document = Factory::getDocument();
                $document->addScript(Uri::root()."media/com_osproperty/assets/js/jscolor.js");
                ?>
                <input type="text" class="color input-small form-control" value="<?php echo isset($configs['image_background_color'])?$configs['image_background_color']:'' ?>" size="5" maxlength="5" name="image_code" placeholder="<?php echo isset($configs['image_background_color'])?$configs['image_background_color']:'' ?>"/>
                <input type="hidden" class="input-small" size="5" maxlength="5" name="configuration[image_background_color]" value="<?php echo isset($configs['image_background_color'])?$configs['image_background_color']:'' ?>" />
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_use_image_watermarks',TextOs::_( 'Use Image Watermarks'),TextOs::_('Do you want to use watermarking on your images. Watermarks currently include a sold graphic for sold listings, and a WOW graphic for featured listings.')); ?>
            </div>
            <div class="controls">
                <?php
                OspropertyConfiguration::showCheckboxfield('images_use_image_watermarks',$configs['images_use_image_watermarks']);
                ?>
            </div>
        </div>
        <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('watermark_all',Text::_( 'Apply for all Medium pictures' ),Text::_('OS_APPLY_WATERMARK_TO_ALL_PROPERTY_PHOTOS_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <?php
                OspropertyConfiguration::showCheckboxfield('watermark_all',$configs['watermark_all']);
                ?>
            </div>
        </div>
        <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('images_quality',TextOs::_( 'Image Quality'),TextOs::_('Quality of the images uploaded and created. The higher the percentage - the larger the image size.')); ?>
            </div>
            <div class="controls">
                <input type="text" class="text-area-order <?php echo $inputMiniClass; ?>" size="5" maxlength="5" name="configuration[images_quality]" value="<?php echo isset($configs['images_quality'])?$configs['images_quality']:'' ?>">&nbsp;%
            </div>
        </div>
         <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('watermark_position',Text::_( 'OS_WATERMARK_POSITION' ),Text::_('OS_WATERMARK_POSITION_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <?php
                $optionArr = array();
                $optionArr[] =  HTMLHelper::_('select.option','1',Text::_('OS_TOP_LEFT'));
                $optionArr[] =  HTMLHelper::_('select.option','2',Text::_('OS_TOP_CENTER'));
                $optionArr[] =  HTMLHelper::_('select.option','3',Text::_('OS_TOP_RIGHT'));
                $optionArr[] =  HTMLHelper::_('select.option','4',Text::_('OS_MIDDLE_RIGHT'));
                $optionArr[] =  HTMLHelper::_('select.option','5',Text::_('OS_MIDDLE_CENTER'));
                $optionArr[] =  HTMLHelper::_('select.option','6',Text::_('OS_MIDDLE_LEFT'));
                $optionArr[] =  HTMLHelper::_('select.option','7',Text::_('OS_BOTTOM_RIGHT'));
                $optionArr[] =  HTMLHelper::_('select.option','8',Text::_('OS_BOTTOM_CENTER'));
                $optionArr[] =  HTMLHelper::_('select.option','9',Text::_('OS_BOTTOM_LEFT'));
                echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_position]','class="ilarge form-select input-medium" style="width:150px;"','value','text',$configs['watermark_position']);
                ?>
            </div>
        </div>
        <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
            <div class="control-label">
                <?php echo HelperOspropertyCommon::showLabel('watermark_type',TextOs::_( 'Watermark type' ),Text::_('OS_WATERMARK_TYPE_EXPLAIN')); ?>
            </div>
            <div class="controls">
                <?php
                $optionArr = array();
				if( !OSPHelper::isJoomla5())
				{
					$optionArr[] =  HTMLHelper::_('select.option','1',Text::_('OS_TEXT'));
				}
                $optionArr[] =  HTMLHelper::_('select.option','2',Text::_('OS_IMAGE'));
                echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_type]','class="ilarge form-select input-medium"','value','text',$configs['watermark_type']);
                ?>
            </div>
        </div>
		<?php
		if( !OSPHelper::isJoomla5())
		{
		?>
			<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('watermark_font',Text::_( 'OS_FONT' ),Text::_('OS_FONT_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					$optionArr = array();
					$optionArr[] =  HTMLHelper::_('select.option','arial.ttf','Unicode');
					$optionArr[] =  HTMLHelper::_('select.option','Exo2-Bold.ttf','Non-Unicode');
					$optionArr[] =  HTMLHelper::_('select.option','koodak1.ttf','Arab & Persian');
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_font]','class="ilarge form-select input-medium"','value','text',$configs['watermark_font']);
					?>
				</div>
			</div>
			<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('watermark_fontsize_thumb',Text::_( 'OS_WATERMARK_TEXT_FONT_SIZE_THUMB' ),Text::_('OS_WATERMARK_TEXT_FONT_SIZE_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					$optionArr = array();
					$optionArr[] =  HTMLHelper::_('select.option','10','10 px');
					$optionArr[] =  HTMLHelper::_('select.option','20','20 px');
					$optionArr[] =  HTMLHelper::_('select.option','30','30 px');
					$optionArr[] =  HTMLHelper::_('select.option','40','40 px');
					$optionArr[] =  HTMLHelper::_('select.option','50','50 px');
					$optionArr[] =  HTMLHelper::_('select.option','60','60 px');
					$optionArr[] =  HTMLHelper::_('select.option','70','70 px');
					$optionArr[] =  HTMLHelper::_('select.option','80','80 px');
					$optionArr[] =  HTMLHelper::_('select.option','90','90 px');
					$optionArr[] =  HTMLHelper::_('select.option','100','100 px');
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_fontsize_thumb]','class="ilarge form-select input-medium" ','value','text',$configs['watermark_fontsize_thumb']);
					?>
				</div>
			</div>
			<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('watermark_fontsize',Text::_( 'OS_WATERMARK_TEXT_FONT_SIZE_MEDIUM' ),Text::_('OS_WATERMARK_TEXT_FONT_SIZE_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					$optionArr = array();
					$optionArr[] =  HTMLHelper::_('select.option','10','10 px');
					$optionArr[] =  HTMLHelper::_('select.option','20','20 px');
					$optionArr[] =  HTMLHelper::_('select.option','30','30 px');
					$optionArr[] =  HTMLHelper::_('select.option','40','40 px');
					$optionArr[] =  HTMLHelper::_('select.option','50','50 px');
					$optionArr[] =  HTMLHelper::_('select.option','60','60 px');
					$optionArr[] =  HTMLHelper::_('select.option','70','70 px');
					$optionArr[] =  HTMLHelper::_('select.option','80','80 px');
					$optionArr[] =  HTMLHelper::_('select.option','90','90 px');
					$optionArr[] =  HTMLHelper::_('select.option','100','100 px');
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_fontsize]','class="ilarge form-select input-medium" ','value','text',$configs['watermark_fontsize']);
					?>
				</div>
			</div>
			<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('watermark_fontsize_original',Text::_( 'OS_WATERMARK_TEXT_FONT_SIZE_ORIGINAL' ),Text::_('OS_WATERMARK_TEXT_FONT_SIZE_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					$optionArr = array();
					$optionArr[] =  HTMLHelper::_('select.option','10','10 px');
					$optionArr[] =  HTMLHelper::_('select.option','20','20 px');
					$optionArr[] =  HTMLHelper::_('select.option','30','30 px');
					$optionArr[] =  HTMLHelper::_('select.option','40','40 px');
					$optionArr[] =  HTMLHelper::_('select.option','50','50 px');
					$optionArr[] =  HTMLHelper::_('select.option','60','60 px');
					$optionArr[] =  HTMLHelper::_('select.option','70','70 px');
					$optionArr[] =  HTMLHelper::_('select.option','80','80 px');
					$optionArr[] =  HTMLHelper::_('select.option','90','90 px');
					$optionArr[] =  HTMLHelper::_('select.option','100','100 px');
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_fontsize_original]','class="ilarge form-select input-medium" ','value','text',$configs['watermark_fontsize_original']);
					?>
				</div>
			</div>
			<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
				<div class="control-label">
					<?php echo HelperOspropertyCommon::showLabel('watermark_color',Text::_( 'OS_WATERMARK_TEXT_COLOR' ),Text::_('OS_WATERMARK_TEXT_COLOR_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php
					$optionArr = array();
					$optionArr[] =  HTMLHelper::_('select.option','245,43,16',Text::_('OS_RED'));
					$optionArr[] =  HTMLHelper::_('select.option','29,188,13',Text::_('OS_GREEN'));
					$optionArr[] =  HTMLHelper::_('select.option','16,91,242',Text::_('OS_BLUE'));
					$optionArr[] =  HTMLHelper::_('select.option','237,245,16',Text::_('OS_YELLOW'));
					$optionArr[] =  HTMLHelper::_('select.option','246,151,16',Text::_('OS_ORANGE'));
					$optionArr[] =  HTMLHelper::_('select.option','0,0,0',Text::_('OS_BLACK'));
					$optionArr[] =  HTMLHelper::_('select.option','255,255,255',Text::_('OS_WHITE'));
					$optionArr[] =  HTMLHelper::_('select.option','59,75,65',Text::_('OS_GRAY'));
					echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_color]','class="ilarge form-select input-medium" ','value','text',$configs['watermark_color']);
					?>
				</div>
			</div>
		<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
			<div class="control-label">
				<?php echo HelperOspropertyCommon::showLabel('watermark_text',Text::_( 'OS_WATERMARK_TEXT' ),Text::_('OS_WATERMARK_TEXT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php
				$optionArr = array();
				$optionArr[] =  HTMLHelper::_('select.option','1',Text::_('OS_CATEGORY'));
				$optionArr[] =  HTMLHelper::_('select.option','2',Text::_('OS_PROPERTY_TYPE'));
				$optionArr[] =  HTMLHelper::_('select.option','3',Text::_('OS_BUSINESS_NAME'));
				$optionArr[] =  HTMLHelper::_('select.option','4',Text::_('OS_CUSTOM_TEXT'));
				echo HTMLHelper::_('select.genericlist',$optionArr,'configuration[watermark_text]','class="ilarge form-select input-medium" ','value','text',$configs['watermark_text']);
				?>
			</div>
		</div>
		<div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
			<div class="control-label">
				<?php echo HelperOspropertyCommon::showLabel('custom_text',Text::_( 'OS_CUSTOM_TEXT' ),Text::_('OS_CUSTOM_TEXT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<input type="text" class="ilarge text-area-order <?php echo $inputMediumClass; ?>" name="configuration[custom_text]" value="<?php echo isset($configs['custom_text'])?$configs['custom_text']:'' ?>">
			</div>
		</div>
	<?php
	}				
	?>
    <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
        <div class="control-label">
            <?php echo  HelperOspropertyCommon::showLabel('watermark_photo_thumb',Text::_( 'OS_WATERMARK_LOGO_FOR_THUMB'),Text::_('OS_WATERMARK_PHOTO_EXPLAIN'));?>
        </div>
        <div class="controls">
            <?php
            if($configs['watermark_photo_thumb'] != ""){
                if(file_exists(JPATH_ROOT.DS."images".DS.$configs['watermark_photo_thumb'])){
                    ?>
                    <img src="<?php echo Uri::root()?>images/<?php echo $configs['watermark_photo_thumb']?>" />
                    <?php
                }
                ?>
                <div style="clear:both;"></div>
                <input type="checkbox" name="remove_watermark_photo_thumb" id="remove_watermark_photo_thumb"  value="" onchange="javascript:changeValue('remove_watermark_photo_thumb');"/> <?php echo Text::_('OS_REMOVE_PHOTO');?>
                <?php
            }
            ?>
            <input type="file" name="watermark_photo_thumb" id="watermark_photo_thumb" class="input-large form-control ilarge"/>
        </div>
    </div>
    <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
        <div class="control-label">
            <?php echo  HelperOspropertyCommon::showLabel('watermark_photo',Text::_( 'OS_WATERMARK_PHOTO_FOR_MEDIUM'),Text::_('OS_WATERMARK_PHOTO_EXPLAIN'));?>
        </div>
        <div class="controls">
            <?php
            if($configs['watermark_photo'] != ""){
                if(file_exists(JPATH_ROOT.DS."images".DS.$configs['watermark_photo'])){
                    ?>
                    <img src="<?php echo Uri::root()?>images/<?php echo $configs['watermark_photo']?>" />
                    <?php
                }
                ?>
                <div style="clear:both;"></div>
                <input type="checkbox" name="remove_watermark_photo" id="remove_watermark_photo"  value="" onchange="javascript:changeValue('remove_watermark_photo');"/> <?php echo Text::_('OS_REMOVE_PHOTO');?>
                <?php
            }
            ?>
            <input type="file" name="watermark_photo" id="watermark_photo" class="input-large form-control ilarge"/>
        </div>
    </div>
    <div class="control-group" data-showon='<?php echo HelperOspropertyCommon::renderShowon(array('configuration[images_use_image_watermarks]' => '1')); ?>'>
        <div class="control-label">
            <?php echo  HelperOspropertyCommon::showLabel('watermark_photo_original',Text::_( 'OS_WATERMARK_PHOTO_FOR_ORIGINAL'),Text::_('OS_WATERMARK_PHOTO_EXPLAIN'));?>
        </div>
        <div class="controls">
            <?php
            if($configs['watermark_photo_original'] != ""){
                if(file_exists(JPATH_ROOT.DS."images".DS.$configs['watermark_photo_original'])){
                    ?>
                    <img src="<?php echo Uri::root()?>images/<?php echo $configs['watermark_photo_original']?>" />
                <?php
                }
                ?>
                <div style="clear:both;"></div>
                <input type="checkbox" name="remove_watermark_photo_original" id="remove_watermark_photo_original"  value="" onchange="javascript:changeValue('remove_watermark_photo_original');"/> <?php echo Text::_('OS_REMOVE_PHOTO');?>
            <?php
            }
            ?>
            <input type="file" name="watermark_photo_original" id="watermark_photo_original" class="form-control input-large ilarge"/>
        </div>
    </div>
</fieldset>