<?php
/*------------------------------------------------------------------------
# default.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;


//}else{
$db = Factory::getDbo();
?>

<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
<?php
$k = 0;
if($enable_nav == 1 && $nproperties > 0)
{
	$npages = round(count($properties) / $nproperties);
	if($nproperties * $npages < count($properties))
	{
		$npages++;
	}
	$startPoint = 0;
	$pageClass  = 1;
}
foreach ($properties as $property) 
{
    $k++;
	
	if($startPoint == $nproperties)
	{
		$startPoint = 0;
		$pageClass++;
	}
	$startPoint++;

	if($pageClass > 1)
	{
		$extraStyle = "display:none;";
	}
	else
	{
		$extraStyle = "";
	}
    $itemid = modOSpropertyramdomHelper::getItemid($property->id);
	?>	
    <div class="<?php echo $bootstrapHelper->getClassMapping('span'.$divstyle); ?> element_property property_<?php echo $pageClass;?>" style="<?php echo $extraStyle;?>">
        <?php
        if($show_photo == 1){
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> image_property" style="margin-left:0px !important;">
            <?php
            if($property->isFeatured == 1){
                ?>
                <div class="randompropertyfeatured"><strong><?php echo Text::_('OS_FEATURED')?></strong></div>
				<?php
            }
			if(($configClass['active_market_status'] == 1) && ($property->isSold > 0)){
				?>
                <div class="randompropertymarket"><strong><?php echo OSPHelper::returnMarketStatus($property->isSold);?></strong></div>
				<?php
			}
            
			if($show_type == 1){
				?>
				<div class="randompropertytype type<?php echo $property->pro_type;?>"><strong><?php echo OSPHelper::loadTypeName($property->pro_type);?></strong></div>
				<?php
            }

            if ($property->photo != ''){?>
                <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OSPROPERTY_MOREDETAILS');?>">
                    <?php
					OSPHelper::showPropertyPhoto($property->photo,'medium',$property->id,'','','',0);
                    ?>
                </a>
            <?php }else {?>
                <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OSPROPERTY_MOREDETAILS');?>">
                    <img alt="<?php echo $property->pro_name?>" src="<?php echo JURI::root(true)?>/media/com_osproperty/assets/images/nopropertyphoto.png" />
                </a>
            <?php }?>
        </div>
        <?php
        }
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>" style="margin-left:0px !important;">
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> element_title">
                    <h4>
                        <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>" title="<?php echo Text::_('OSPROPERTY_MOREDETAILS');?>">
                            <?php
                            if($property->ref != "" && $configClass['show_ref'] = 1)
							{
                                echo $property->ref.", ";
                            }
                            ?>
                            <?php
                            $arr_title_word = explode(' ',$property->pro_name);
                            if (!$limit_title_word || $limit_title_word > count($arr_title_word)){
                                echo $property->pro_name;
                            }else {
                                $tmp_title = array();
                                for ($i=0; $i < $limit_title_word;$i++){
                                    $tmp_title[] = $arr_title_word[$i];
                                    if ($i > 2*count($arr_title_word)/3 && stristr($arr_title_word[$i],'.')) break;
                                }
                                echo implode(' ',$tmp_title);
                                echo "...";
                            }
                            ?>
                        </a>
                        <?php
                        if ($show_price ) {
							?>
							<span class="property_price"> <?php echo $property->price_information;?></span>
							<?php
                        }
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>" style="margin-left:0px !important;">
            <?php
            $arr_desc = array();
            if($show_category == 1){
                $link = Route::_('index.php?option=com_osproperty&task=category_details&id='.$property->catid.'&Itemid='.$itemid);
                echo "<span class='category_label'>".Text::_('OSPROPERTY_CATEGORY').": </span>";
                echo "<a href='$link'>";
                echo $property->category_name;
                echo "</a>";
                echo '<div class="clearfix"></div>';
            }
            if ($show_address ) {
                if($property->show_address == 1){
                    echo "<span class='address_value'>";
                    echo OSPHelper::generateAddress($property);
                    echo "</span>";
                    echo '<div class="clearfix"></div>';
                }
            }
            if (($show_small_desc == 1) and ($property->pro_small_desc != "")){
                $small_desc = $property->pro_small_desc;
                $small_descArr = explode(" ",$small_desc);
                $count_small_desc = count($small_descArr);
                echo "<span class='desc_module".$bstyle."'>";
                if(($count_small_desc > $limit_word) and ($limit_word > 0)){
                    for($i=0;$i<$limit_word;$i++){
                        echo $small_descArr[$i]." ";
                    }
                    echo "...";
                }else{
                    echo $small_desc;
                }
                echo "</span>";
                echo '<div class="clearfix"></div>';
            }
            $addtionalArr = array();
            if(($show_bedrooms == 1) and ($property->bed_room > 0)){
                $addtionalArr[] = "<span class='bedroom_label'><i class='ospico-bed ".$font_height."'></i> ".$property->bed_room."</span>";
            }
            if(($show_bathrooms == 1) and ($property->bath_room > 0)){
                $addtionalArr[] = "<span class='bedroom_label'><i class='ospico-bath ".$font_height."'></i> ".OSPHelper::showBath($property->bath_room)."</span>";
            }
            if(($show_parking  == 1) and ($property->parking != "")){
                $addtionalArr[] = "<span class='bedroom_label'><i class='ospico-parking ".$font_height."'></i> ".$property->parking."</span>";
            }
			if(($show_square  == 1) and ($property->square_feet > 0)){
                $addtionalArr[] = "<span class='square_label'><i class='ospico-square ".$font_height."'></i> ".OSPHelper::showSquare($property->square_feet)." ".OSPHelper::showSquareSymbol()."</span>";
            }
            if(count($addtionalArr) > 0){
                ?>
                <span class="additional_information">
                    <?php echo implode(" &nbsp; ",$addtionalArr);?>
                </span>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    if($k == $properties_per_row){
        $k = 0;
        ?>
        </div><div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
        <?php
    }
} ?>
</div>
<?php
if($enable_nav == 1 && $nproperties > 0 && $npages > 0)
{
	?>
	<div class="ospRandomPageNav">
		<?php
		for($i=1;$i<=$npages;$i++)
		{
			if($i == 1)
			{
				$extraClass = "osprandomactivated";
			}
			else
			{
				$extraClass = "";
			}
			?>
			<a href="javascript:void(0);" id="pageNavRandom<?php echo $i;?>" class="<?php echo $extraClass; ?>">
				<?php echo $i;?>
			</a>
			<script type="text/javascript">
			jQuery( "#pageNavRandom<?php echo $i;?>" ).click(function() {
				for(var i = 1; i <= <?php echo $npages;?>; i++)  
				{
					jQuery(".property_" + i).hide();
					jQuery("#pageNavRandom" + i).removeClass('osprandomactivated');
				}
				jQuery(".property_<?php echo $i;?>").fadeIn();
				jQuery("#pageNavRandom<?php echo $i;?>").addClass('osprandomactivated');
			});
			</script>
			<?php
		}
		?>
	</div>
	<?php
}
?>
