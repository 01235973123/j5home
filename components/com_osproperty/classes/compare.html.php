<?php
/*------------------------------------------------------------------------
# compare.html.php - Ossolution Property
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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class HTML_OspropertyCompare{
	/**
	 * Show comparision Form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $comparisionArr
	 */
	static function showCompareForm($option,$comparisionArr,$configs,$isPrint,$fields){
		global $bootstrapHelper, $jinput, $mainframe,$configClass;
		//calculate percent width
		$c = count($comparisionArr);
		$per_column = round(80/$c);
		OSPHelperJquery::colorbox('osmodal');
		//HTMLHelper::_('behavior.modal','a.osmodal');
		?>
		<script type="text/javascript">
		function removeProperty(pid){
			var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_THE_PROPERTY_OUT_OF_THE_COMPARE_LIST')?>");
			if(answer == 1){
				location.href = "<?php echo Uri::root()?>index.php?option=com_osproperty&task=compare_remove&pid=" + pid + "&Itemid=<?php echo $jinput->getInt('Itemid',0)?>";
			}
		}
		function showPrint(){
			window.open ("<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&task=compare_listing&no_html=1&p=1", "mywindow","status=0,toolbar=0,menubar=0,location=0,width=800,height=600,scrollbars=1,resizable=1");
		}
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="properties_comparing">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<?php 
					OSPHelper::generateHeading(2,Text::_('OS_COMPARE_LISTINGS'));
					?>
				
				<?php
				if($isPrint == 0){
				?>
				<div class="clearfix"></div>
				<div class="btn-toolbar <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> pull-right">
		            <div class="btn-group">
		                <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_PRINT_THIS_PAGE');?>" onclick="javascript:showPrint();">
		                    <i class="osicon-print"></i> <?php echo Text::_('OS_PRINT');?>
		                </button>
		            </div>
		        </div>
				<?php
				$rows = array();
				for($i=0;$i<count($comparisionArr);$i++)
				{
					$property = $comparisionArr[$i]->property;
					$photo = $comparisionArr[$i]->photo; 
					if($photo->image != "")
					{
						if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$property->id."/".$photo->image))
						{
							$comparisionArr[$i]->property->photo = Uri::root(true)."/images/osproperty/properties/".$property->id."/thumb/".$photo->image;
						}
						else
						{
							$comparisionArr[$i]->property->photo = Uri::root(true)."/media/com_osproperty/assets/images/nopropertyphoto.png";
						}
					}
					else
					{
						$comparisionArr[$i]->property->photo = Uri::root(true)."/media/com_osproperty/assets/images/nopropertyphoto.png";
					}
					$rows[] = $comparisionArr[$i]->property;
				}
                if($configClass['map_type'] == 0)
                {
                    if(HelperOspropertyGoogleMap::loadMapInListing($rows))
                    {
                        ?>
                        <div id="map_canvas" class="map2x relative"></div>
                        <?php
                    }
                }
                else
                {
                    HelperOspropertyOpenStreetMap::loadMapInListing($rows);
                }
				?>
				<div class="clearfix"></div>
				<?php
				}
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<table  width="100%" class="table table-striped" id="compare_table">
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_("Ref #");
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									$needs = array();
									$needs[] = "property_details";
									$needs[] = $property->id;
									$itemid  = OSPRoute::getItemid($needs);
									$link = "index.php?option=com_osproperty&task=property_details&id=".$property->id."&Itemid=".$itemid;
									?>
									<td width="<?php echo $per_column?>%" data-label="<?php echo Text::_("Ref #"); ?>&nbsp;" class="alignleft fontbold paddingleft5 backgroundlightgray">
										<?php if($configClass['show_ref'] == 1){ ?>
											<a href="<?php echo Route::_($link)?>" title="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name');?>">
											<?php
												echo $property->ref;
											?>
											</a>
										<?php } ?>
										<a href="javascript:removeProperty('<?php echo $property->id?>')" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_THE_COMPARE_LIST')?>">
											<i class="osicon-unpublish"></i>
										</a>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_PROPERTY_NAME');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									$link = Uri::root()."index.php?option=com_osproperty&task=property_details&id=".$property->id."&Itemid=".$jinput->getInt('Itemid',0);
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray fontbold" data-label="<?php echo Text::_("OS_PROPERTY_NAME"); ?>&nbsp;">
										<a href="<?php echo Route::_($link)?>" title="<?php echo $property->pro_name;?>">
										<?php
											echo OSPHelper::getLanguageFieldValue($property,'pro_name');
										?>
										</a>
										<a href="javascript:removeProperty('<?php echo $property->id?>')" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_THE_COMPARE_LIST')?>">
											<i class="osicon-unpublish"></i>
										</a>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%" valign="top">
									<?php 
									echo Text::_('OS_PHOTO');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$photo = $p->photo;
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" data-label="<?php echo Text::_("Ref #"); ?>&nbsp;" class="alignleft fontbold paddingleft5 backgroundlightgray">
										<?php
										if($photo->image != ""){
										?>
											<?php
											if(file_exists(JPATH_ROOT.DS."images/osproperty/properties/".$property->id."/".$photo->image)){
											?>
												<a href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $property->id;?>/<?php echo $photo->image?>" class="osmodal">
													<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $property->id;?>/thumb/<?php echo $photo->image?>" width="150" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>" />
												</a>
											<?php }else{ ?>
												<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" width="100" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>" />
											<?php
											}
											?>
										<?php }else{ ?>
											<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/nopropertyphoto.png" width="150" class="<?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?>" />
										<?php
										}
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_ADDRESS');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p		  = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray fontbold" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										if($p->show_address == 1){
											echo OSPHelper::generateAddress($property);
										}else{
											echo "N/A";
										}
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_CATEGORY');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo OSPHelper::getLanguageFieldValue($p,'category_name');
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_PROPERTY_TYPE');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo OSPHelper::getLanguageFieldValue($p,'property_type');
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_PRICE');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										if($property->price_call == 0 and $property->price > 0){
											echo OSPHelper::generatePrice($property->curr,$property->price);
											if($property->rent_time != ""){
												echo " /".Text::_($property->rent_time);
											}
										}else{
											echo Text::_('OS_CALL_FOR_PRICE');
										}
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							if($configClass['use_bedrooms']){
							?>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_BEDROOMS');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo $property->bed_room;
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							?>
							<?php
							if($configClass['use_bathrooms']){
							?>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_BATHROOMS');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo OSPHelper::showBath($property->bath_room);
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							?>
							<?php
							if($configClass['use_rooms']){
							?>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_ROOMS');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo $property->rooms;
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							?>
							<?php
							if($configClass['use_squarefeet']){
							?>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_SQUARE_FEET');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo OSPHelper::showSquare($property->square_feet) ." ".OSPHelper::showSquareSymbol();
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_LOT_SIZE');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo OSPHelper::showSquare($property->lot_size) ." ".OSPHelper::showSquareSymbol();
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							?>
							<?php
							if($configClass['use_nfloors']){
							?>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%">
									<?php 
									echo Text::_('OS_FLOORS');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										echo $property->number_of_floors;
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							?>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%" valign="top">
									<?php 
									echo Text::_('OS_PROPERTY_FEATURE');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$amenities = $p->amenities;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										$tempstr = "";
										for($j=0;$j<count($amenities);$j++){
											$amen = $amenities[$j];
											$tempstr .= OSPHelper::getLanguageFieldValue($amen,'amenities').", ";
										}
										echo substr($tempstr,0,strlen($tempstr)-2);
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row0">
								<td class="compare_title compare_title1" width="20%" valign="top">
									<?php 
									echo Text::_('OS_NEIGHBORHOOD');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										ob_start();
										HelperOspropertyCommon::loadNeighborHood($comparisionArr[$i]->property->id);
										$neighborhood = ob_get_contents();
										ob_end_clean();
										if($neighborhood != ""){
											echo $neighborhood ;
										}else{
											echo "N/A";
										}
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<tr class="row1">
								<td class="compare_title compare_title1" width="20%" valign="top">
									<?php 
									echo Text::_('OS_DESCRIPTION');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundlightgray" valign="top" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
											echo OSPHelper::getLanguageFieldValue($property,'pro_small_desc');
										?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							if($configClass['show_rating'] == 1){
							?>
							<tr class="row0"> 
								<td class="compare_title compare_title1" width="20%" >
									<?php 
									echo Text::_('OS_RATING');
									?>
								</td>
								<?php
								for($i=0;$i<count($comparisionArr);$i++){
									$p = $comparisionArr[$i];
									$property = $p->property;
									?>
									<td width="<?php echo $per_column?>%" class="alignleft paddingleft5 backgroundwhite" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
										<?php
										if($property->number_votes > 0){
										$points = round($property->total_points/$property->number_votes);
										?>
										<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />
										(<?php echo $points; ?>/5)
										<?php								
									}else{
										?>
										<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-0.png" /> (0/5)
										<?php
									}
									?>
									</td>
									<?php
								}
								?>
							</tr>
							<?php
							}
							if(count($fields) > 0){
								$k = 1;
								for($f=0;$f<count($fields);$f++){
									$field = $fields[$f];
									?>
									<tr class="row<?php echo $k?>">
										<td class="compare_title compare_title1" width="20%" valign="top">
											<?php 
											echo OSPHelper::getLanguageFieldValue($field,'field_label');
											?>
										</td>
										<?php
										for($i=0;$i<count($comparisionArr);$i++){
											$p = $comparisionArr[$i];
											$property = $p->fieldarr;
											?>
											<td width="<?php echo $per_column?>%" class="alignleft paddingleft5" valign="top" data-label="<?php echo OSPHelper::getLanguageFieldValue($property,'pro_name'); ?>">
												<?php
													echo $property[$f]->fieldvalue;
												?>
											</td>
											<?php
										}
										?>
									</tr>
									<?php
									$k = 1-$k;
								}
							}
							?>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
		if($isPrint == 1){
			?>
			<script type="text/javascript">
			window.print();
			</script>
			<?php
		}
	}
}
?>