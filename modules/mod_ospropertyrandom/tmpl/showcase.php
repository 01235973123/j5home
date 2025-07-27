<?php
/*------------------------------------------------------------------------
# showcase.php - mod_ospropertyrandom
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="container-showcase">
<?php
$k = 0;
	foreach ($properties as $property) 
	{
		$itemid = modOSpropertyramdomHelper::getItemid($property->id);
		
		if($property->photo != '')
		{
			?>	
			<div class="showcase-property-box" id="showcase-box-<?php echo $property->id?>">
				<a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid)?>"  title="<?php echo $property->pro_name; ?>">
					<?php
					OSPHelper::showPropertyPhoto($property->photo,'medium',$property->id,'','','',0);
					?>
					<div class="showcase-overlay"></div>
					<div class="showcase-info">
						<h3>
							<?php
							if($property->ref != "")
							{
								echo $property->ref.", ";
							}
							?>
							<?php
							$arr_title_word = explode(' ',$property->pro_name);
							if (!$limit_title_word || $limit_title_word > count($arr_title_word))
							{
								echo $property->pro_name;
							}
							else 
							{
								$tmp_title = [];
								for ($i=0; $i < $limit_title_word;$i++)
								{
									$tmp_title[] = $arr_title_word[$i];
									if ($i > 2*count($arr_title_word)/3 && stristr($arr_title_word[$i],'.')) break;
								}
								echo implode(' ',$tmp_title);
								echo "...";
							}
							?>
						</h3>
						<?php
						if ($show_price ) {
							?>
							<span class="showcase-price">
								<?php
								if(OSPHelper::getLanguageFieldValue($property,'price_text') != "")
								{
									echo OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($property,'price_text'));
								}
								elseif($property->price_call == 1)
								{
									echo Text::_('OSPROPERTY_CALL_FOR_PRICE');
								}
								else
								{
									if($property->price > 0)
									{
										echo OSPHelper::generatePrice($property->curr,$property->price);
									}
									if($property->rent_time != "")
									{
										echo " /".Text::_($property->rent_time);
									}
								}
								?>
							</span>
							<?php
						}
						?>
						<p>
						<?php
						$addtionalArr = [];
						if(($show_bedrooms == 1) && ($property->bed_room > 0)){
							$addtionalArr[] = $property->bed_room." bd";
						}
						if(($show_bathrooms == 1) && ($property->bath_room > 0)){
							$addtionalArr[] = OSPHelper::showBath($property->bath_room)." ba";
						}
						if(($show_parking  == 1) && ($property->parking != "")){
							$addtionalArr[] = $property->parking." pa";
						}
						if(($show_square  == 1) && ($property->square_feet > 0)){
							$addtionalArr[] = $property->square_feet." ".OSPHelper::showSquareSymbol();
						}
						if(count($addtionalArr) > 0)
						{
							?>
							<?php echo implode("&nbsp;|&nbsp;",$addtionalArr);?>
							<?php
						}
						?>
						</p>
					</div>
				</a>
			</div>
		<?php
		} 
	}		
?>
</div>
