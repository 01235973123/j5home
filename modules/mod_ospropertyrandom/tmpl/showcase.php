<?php
/*------------------------------------------------------------------------
# showcase.php - mod_ospropertyrandom
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

$db = Factory::getDbo();
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
<?php
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
$k = 0;
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
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> image_property_showcase" style="margin-left:0px !important;">
            <?php
            if($property->isFeatured == 1){
                ?>
                <div class="randompropertyfeatured"><strong><?php echo Text::_('OS_FEATURED')?></strong></div>
            <?php
            }

			if($show_type == 1){
				?>
				<div class="randompropertytype"><strong><?php echo OSPHelper::loadTypeName($property->pro_type);?></strong></div>
				<?php
            }
			
			if(($configClass['active_market_status'] == 1) && ($property->isSold > 0)){
				?>
                <div class="randompropertymarket1"><strong><?php echo OSPHelper::returnMarketStatus($property->isSold);?></strong></div>
				<?php
			}

            if ($property->photo != ''){?>
                <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>"  title="<?php echo $property->pro_name; ?>">
                    <?php
                    OSPHelper::showPropertyPhoto($property->photo,'medium',$property->id,'','','',0);
                    ?>
                </a>
            <?php }else {?>
                <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>"  title="<?php echo $property->pro_name; ?>">
                    <img alt="<?php echo $property->pro_name?>" src="<?php echo JURI::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" />

                </a>
            <?php }?>
			<span class="overlayPhoto overlayFull mls"></span>
			<div class="overlayTransparent overlayBottom typeReversed hpCardText">
				<ul class="mbm property-card-details">
					<li class="man">
						<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>" title="<?php echo $property->pro_name; ?>">
							<?php
							if($property->ref != ""){
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
					</li>
					<li class="man">
						<?php
                        if ($show_price ) {
							?>
							<span class="property-price-showcase typeEmphasize mvn"><?php echo $property->price_information;?>&nbsp;</span>
							<?php
                        }
                        ?>
						<?php
						$addtionalArr = array();
						if(($show_bedrooms == 1) and ($property->bed_room > 0)){
							$addtionalArr[] = $property->bed_room." bd";
						}
						if(($show_bathrooms == 1) and ($property->bath_room > 0)){
							$addtionalArr[] = OSPHelper::showBath($property->bath_room)." ba";
						}
						if(($show_parking  == 1) and ($property->parking != "")){
							$addtionalArr[] = $property->parking." pa";
						}
						if(($show_square  == 1) and ($property->square_feet > 0)){
							$addtionalArr[] = $property->square_feet." ".OSPHelper::showSquareSymbol();
						}
						if(count($addtionalArr) > 0){
							?>
							<span class="man noWrap showcase_address">
								<?php echo implode(" &nbsp; ",$addtionalArr);?>
							</span>
							<?php
						}
						?>
					</li>
					<?php
					if ($show_address ) {
						if($property->show_address == 1){
							echo "<li class='man showcase_address'>";
							echo $property->address;
							echo "</li>";
							?>
							<li class="man showcase_address">
								<span class="man noWrap"> <?php echo OSPHelper::loadCityName($property->city);?>
								<?php
								if(!OSPHelper::userOneState()){
									echo ", ".OSPHelper::loadSateName($property->state);
								}

								?>
								</span>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
        </div>
        <?php
        }
        ?>
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
