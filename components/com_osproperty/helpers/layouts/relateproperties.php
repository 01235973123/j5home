<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$relate_columns = $configClass['relate_columns'];
if($relate_columns == ""){
	$relate_columns = 2;
}
if (isset($relates) && count($relates)){
	if($title != ""){
	?>
	<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
		<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
			<div class="block_caption">
				<strong><?php echo $title;?></strong>
			</div>
		</div>
	</div>
	<?php } ?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
<?php
$k = 0;
$span = 12/$relate_columns;
for($i=0;$i<count($relates);$i++){
	$k++;
	$rproperty = $relates[$i];
	?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('span'.$span); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span5'); ?>">
					<a  href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$rproperty->id."&Itemid=".$rproperty->itemid)?>" title="<?php echo (($rproperty->ref != "") and ($configClass['show_ref'] == 1))? $rproperty->ref.", ":""?><?php echo OSPHelper::getLanguageFieldValue($rproperty,'pro_name');?>">
						<img alt="<?php echo (($rproperty->ref != "") and ($configClass['show_ref'] == 1))? $rproperty->ref.", ":""?><?php echo OSPHelper::getLanguageFieldValue($rproperty,'pro_name');?>" title="<?php echo OSPHelper::getLanguageFieldValue($rproperty,'pro_name');?>" src="<?php echo $rproperty->photo; ?>" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>" />
					</a>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('span7'); ?> relate_property">
					<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=".$rproperty->id."&Itemid=".$rproperty->itemid)?>" title="<?php echo (($rproperty->ref != "") and ($configClass['show_ref'] == 1))? $rproperty->ref.", ":""?><?php echo OSPHelper::getLanguageFieldValue($rproperty,'pro_name');?>">
						<strong><?php echo (($rproperty->ref != "") and ($configClass['show_ref'] == 1))? $rproperty->ref.", ":""?><?php echo OSPHelper::getLanguageFieldValue($rproperty,'pro_name');?></strong>
					</a>
					<div class="clearfix"></div>
					<div class="property_description">
						<span class="property_type_name">
							<?php echo OSPHelper::getLanguageFieldValue($rproperty,'type_name'); ?>
						</span>
						<span class="price">
							<?php
							if($rproperty->price_text != "")
							{
								echo " <span class='market_price'>".OSPHelper::showPriceText(Text::_($rproperty->price_text))."</a>";
							}
							elseif($rproperty->price_call == 1){ //do nothing

							}
							elseif($rproperty->price > 0)
							{
								echo " <span class='market_price'>".OSPHelper::generatePrice($rproperty->curr,$rproperty->price);
								if($rproperty->rent_time != ""){
									echo "/".Text::_($rproperty->rent_time)."</span>";
								}
							}
							?>
						</span>
						<div class="clearfix"></div>
						<?php 
						if($rproperty->show_address == 1){
							?>
							<div class="property_address">
								<?php 
								echo  OSPHelper::generateAddress($rproperty);
								?>
							</div>
							<?php 
						}
					?>
					</div>
				</div>
			</div>
		</div>
	<?php
		if($k % $relate_columns == 0){
			$k = 0;
			?>
			</div>
			<div class="clearfix height20"></div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<?php 
		}
	}	
	?>
</div>
<?php 
 }
?>
<div class="clearfix height20"></div>