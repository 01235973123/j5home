<?php
/*------------------------------------------------------------------------
# results.html.tpl.php - Ossolution Property
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
$document           = Factory::getDocument();
echo OSPHelper::loadTooltip();
$ncolumns           = $params->get('ncolumns',1);
$color              = $params->get('themeBackgroundColor','#88C354');
$showcategoryprice	= $params->get('showcategoryprice','1');
$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
?>
<link rel="stylesheet" href="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename;?>/font/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename;?>/style/layout<?php echo $ncolumns;?>.css">
<script src="<?php echo Uri::root()?>components/com_osproperty/templates/<?php echo $themename;?>/js/modernizr.custom.js"></script>
<style>
.cat-price {
	background:<?php echo $color?>;
}
.grid li:hover .property-info { background:<?php echo $color?>;} 
.pimage figcaption i, .feat-thumb figcaption i, .feat-medium figcaption i{
	background:<?php echo $color?>;
}
.agent-info label {
    color: <?php echo $color?>;
}
<?php
if($showcategoryprice == 0){
	$priceClass = $bootstrapHelper->getClassMapping('hidden-phone');
?>
	@media only screen
	and (min-device-width : 768px)
	and (max-device-width : 1024px)
	and (-webkit-min-device-pixel-ratio: 2)
	and (-webkit-min-device-pixel-ratio: 1) {
		.cs-style-3 figure .property-price{
			display:none;
		}
		.cs-style-2 figure .property-price{
			display:none;
		}
	}
<?php
}
?>
</style>
<script type="text/javascript">
function loadStateInListPage(){
	var country_id = document.getElementById('country_id');
	loadStateInListPageAjax(country_id.value,"<?php echo Uri::root()?>");
}
function changeCity(state_id,city_id){
	var live_site = '<?php echo Uri::root()?>';
	loadLocationInfoCity(state_id,city_id,'state_id',live_site);
}
</script>
<div id="notice" style="display:none;">
	
</div>
<div id="listings">
    <?php
    if(count($rows) > 0){

        $db = Factory::getDbo();
        $db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where id <> '$row->curr' order by currency_code");
        $currencies   = $db->loadObjectList();
        $currenyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT'));
        $currenyArr   = array_merge($currenyArr,$currencies);
        ?>
        <input type="hidden" name="currency_item" id="currency_item" value="" />
        <input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
        <div class="clearfix"></div>

        <div class="agent-properties property-list <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="grid cs-style-3">
                <ul class="margin0 padding0 <?php echo $rowFluidClass; ?>">
                    <?php
                    if($ncolumns == 1){
                        for($i=0;$i<count($rows);$i++){
                            $row = $rows[$i];
                            $needs = array();
                            $needs[] = "property_details";
                            $needs[] = $row->id;
                            $itemid = OSPRoute::getItemid($needs);

                            if($configClass['load_lazy']){
                                $photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
                            }else{
                                $photourl = $row->photo;
                            }
                            ?>
                            <li class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> marginleft0 marginright0 margintop0 marginbottom20 propertyitem">
                                <div class="<?php echo $rowFluidClass; ?>">
                                    <div class="property-mask property-image <?php echo $bootstrapHelper->getClassMapping('span5'); ?> padding0 margin0">
                                        <figure class="pimage">
                                            <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" class="property_mark_a">
                                                <img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="ospitem-imgborder oslazy" id="picture_<?php echo $i?>" />
                                            </a>
                                            <figcaption><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>"><i class="fa fa-link fa-lg"></i></a></figcaption>
                                            <?php
                                            if(($configClass['active_market_status'] == 1) && ($row->isSold > 0)){
                                                ?>
                                                <h4 class="os-sold">
                                                    <a rel="tag" href="#">
                                                        <?php echo OSPHelper::returnMarketStatus($row->isSold);?>
                                                    </a>
                                                </h4>
                                                <?php
                                            }
                                            ?>
                                            <?php
                                            if($row->isFeatured == 1){
                                                ?>
                                                <h4 class="os-featured"><a rel="tag" href="#"><?php echo Text::_('OS_FEATURED')?></a></h4>
                                                <?php
                                            }
                                            ?>
                                            <h4> <a rel="tag" href="#"><?php echo $row->type_name;?></a></h4>
                                            <?php
                                            if(($configClass['listing_show_rating'] == 1) and ($configClass['comment_active_comment'] == 1)){
                                                ?>
                                                <h4 class="os-start">
                                                    <?php
                                                    OSPHelper::showRatingOverPicture($row->rate,$color);
                                                    ?>
                                                </h4>
                                            <?php } ?>
                                            <div class="property-price clear">
                                                <div class="cat-price">
													<span class="pcategory">
														<a rel="tag" href="<?php echo Route::_('index.php?option=com_osproperty&task=category_details&id='.$row->category_id);?>" title="<?php echo Text::_('OS_CATEGORY_DETAILS');?>">
															<?php echo $row->category_name_short;?>
														</a>
													</span>
                                                    <span class="price">
													<?php
                                                    if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
													{
                                                        echo " ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
                                                    }
													elseif($row->price_call == 0)
													{
                                                        echo OSPHelper::generatePrice($row->curr,$row->price);
                                                        if($row->rent_time != ""){
                                                            echo " /".Text::_($row->rent_time);
                                                        }
                                                    }else{
                                                        echo Text::_('OS_CALL_FOR_PRICE');
                                                    }
                                                    ?>
													</span>
                                                </div>
                                                <span class="picon"><i class="fa fa-tag"></i></span>
                                            </div>
                                        </figure>
                                    </div>
                                    <div class="agent-property-desc <?php echo $bootstrapHelper->getClassMapping('span7'); ?>">
                                        <div class="<?php echo $rowFluidClass?>">
                                            <div class="<?php echo $span12Class?>">
                                                <div class="property-desc">
                                                    <div class="<?php echo $rowFluidClass?>">
                                                        <div class="<?php echo $span12Class?>">
                                                            <h4><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><?php echo $row->pro_name?></a>
                                                                <?php
                                                                if($configClass['show_compare_task'] == 1){
                                                                    if(! OSPHelper::isInCompareList($row->id)) {

                                                                        $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
                                                                        $msg = str_replace("'","\'",$msg);
                                                                        ?>
                                                                        <span id="compare<?php echo $row->id;?>">
                                                                        <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
                                                                            <img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24_gray.png" width="16"/>
                                                                        </a>
                                                                    </span>
                                                                        <?php
                                                                    }else{
                                                                        $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                                        $msg = str_replace("'","\'",$msg);
                                                                        ?>
                                                                        <span id="compare<?php echo $row->id;?>">
                                                                        <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
                                                                            <img title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24.png" width="16"/>
                                                                        </a>
                                                                    </span>
                                                                        <?php
                                                                    }
                                                                }
                                                                if(intval($user->id) > 0){
                                                                    if($configClass['property_save_to_favories'] == 1){
                                                                        if($task != "property_favorites"){
                                                                            $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
                                                                            $count = $db->loadResult();
                                                                            if($count == 0){
                                                                                ?>
                                                                                <span id="favorite_<?php echo $row->id; ?>">
                                                                            <?php
                                                                            $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
                                                                            $msg = str_replace("'","\'",$msg);
                                                                            ?>
                                                                                    <span id="fav<?php echo $row->id;?>">
                                                                                <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
                                                                                    <img title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24_gray.png" width="16"/>
                                                                                </a>
                                                                            </span>
                                                                        </span>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        if($count > 0){
                                                                            ?>
                                                                            <span id="favorite_<?php echo $row->id; ?>">
                                                                            <?php
                                                                            $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
                                                                            $msg = str_replace("'","\'",$msg);
                                                                            ?>
                                                                                <span id="fav<?php echo $row->id;?>">
                                                                                <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>">
                                                                                    <img title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24.png" width="16"/>
                                                                                </a>
                                                                            </span>
                                                                        </span>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                                if(HelperOspropertyCommon::isAgent()){
                                                                    $my_agent_id = HelperOspropertyCommon::getAgentID();
                                                                    if($my_agent_id == $row->agent_id){
                                                                        $link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
                                                                        ?>
                                                                        <a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>" class="fontsize14 paddingleft2 paddingright2">
                                                                            <i class="osicon-edit"></i>
                                                                        </a>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <?php
                                                                $sold_property_types = $configClass['sold_property_types'];
                                                                $show_sold = 0;
                                                                if($sold_property_types != ""){
                                                                    $sold_property_typesArr = explode("|",$sold_property_types);
                                                                    if(in_array($row->pro_type, $sold_property_typesArr)){
                                                                        $show_sold = 1;
                                                                    }
                                                                }
                                                                ?>
                                                                <?php if(($configClass['active_market_status'] == 1) and ($row->isSold > 0))
                                                                {
                                                                    ?>
                                                                    <span class="badge badge-warning"><?php echo OSPHelper::returnMarketStatus($row->isSold)?></span>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </h4>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    if(($row->show_address == 1) && ($configClass['listing_show_address'] == 1))
                                                    {
                                                        ?>
                                                        <div class="<?php echo $rowFluidClass?>">
                                                            <div class="<?php echo $span12Class?> agentaddress">
                                                                <i class="fa fa-map-marker"></i> <?php echo OSPHelper::generateAddress($row);?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="property-info-agent noleftmargin">
                                            <ul>
                                                <?php
                                                if(($configClass['use_squarefeet'] == 1) and ($row->square_feet > 0)){
                                                    ?><li class="property-icon-square meta-block">
                                                    <i class="ospico-square"></i>
                                                    <span>
													<?php
                                                    echo OSPHelper::showSquare($row->square_feet);
                                                    echo "&nbsp;";
                                                    echo OSPHelper::showSquareSymbol();
                                                    ?>
												</span></li>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                if(($configClass['listing_show_nbedrooms'] == 1) and ($row->bed_room > 0)){
                                                    ?>
                                                    <li class="property-icon-bed meta-block">
                                                        <i class="ospico-bed"></i>
                                                        <span><?php echo $row->bed_room;?> <?php echo Text::_('OS_BEDS');?></span></li>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                if(($configClass['listing_show_nbathrooms'] == 1) and ($row->bath_room > 0)){
                                                    ?>
                                                    <li class="property-icon-bath meta-block">
                                                        <i class="ospico-bath"></i>
                                                        <span> <?php echo OSPHelper::showBath($row->bath_room);?> <?php echo Text::_('OS_BATHS');?></span>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                                <?php
                                                if($row->parking != ""){
                                                    ?>
                                                    <li class="property-icon-parking meta-block">
                                                        <i class="ospico-parking"></i>
                                                        <span><?php echo $row->parking;?></span>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <div class="<?php echo $rowFluidClass?> propertydetails">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span6')?>">
                                                <?php
                                                if($configClass['listing_show_agent'] == 1)
                                                {
                                                    ?>
                                                    <div class="agent-info">
                                                        <?php
                                                        if($configClass['show_agent_image'] == 1)
                                                        {
                                                            ?>
                                                            <?php
                                                            if($row->agent_photo != "")
                                                            {
                                                                if(file_exists(JPATH_ROOT."/images/osproperty/agent/thumbnail/".$row->agent_photo))
                                                                {
                                                                    ?>
                                                                    <a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->agent_id);?>" title="<?php echo Text::_('OS_AGENT_DETAILS');?>">
                                                                        <img src="<?php echo Uri::root()?>images/osproperty/agent/thumbnail/<?php echo $row->agent_photo?>" width="35"  class="" />
                                                                    </a>
                                                                    <?php
                                                                }
                                                                else
                                                                {
                                                                    ?>
                                                                    <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" height="70" width="35"  class="" />
                                                                    <?php
                                                                }
                                                            }else{
                                                                ?>
                                                                <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png" height="70" width="35"  class="" />
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="agent-name">
                                                        <span class="agent-name-info">
                                                            <a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->agent_id);?>" title="<?php echo Text::_('OS_AGENT_DETAILS');?>">
                                                                <?php echo $row->agent_name;?>
                                                            </a>
                                                        </span>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span6')?> pricevalue" style="color:<?php echo $color;?>">
                                                <?php
                                                if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
                                                {
                                                    echo " ".OSPHelper::showPriceText(OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text')));
                                                }
                                                elseif($row->price_call == 0)
                                                {
                                                    echo OSPHelper::generatePrice($row->curr,$row->price);
                                                    if($row->rent_time != "")
                                                    {
                                                        echo " /".Text::_($row->rent_time);
                                                    }
                                                }
                                                else
                                                {
                                                    echo Text::_('OS_CALL_FOR_PRICE');
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php
                        }
                    }elseif($ncolumns == 2){
                        for($i=0;$i<count($rows);$i++){
                            $row = $rows[$i];
                            $needs = array();
                            $needs[] = "property_details";
                            $needs[] = $row->id;
                            $itemid = OSPRoute::getItemid($needs);
                            if($configClass['load_lazy']){
                                $photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
                            }else{
                                $photourl = $row->photo;
                            }
                            ?>
                            <li class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
                                <div class="property-mask property-image">
                                    <figure class="pimage">
                                        <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" class="property_mark_a">
                                            <img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="ospitem-imgborder oslazy" id="picture_<?php echo $i?>" />
                                        </a>
                                        <figcaption><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>"><i class="fa fa-link fa-lg"></i></a></figcaption>
                                        <?php
                                        if(($configClass['active_market_status'] == 1) && ($row->isSold > 0)){
                                            ?>
                                            <h4 class="os-sold"><a rel="tag" href="#"><?php echo OSPHelper::returnMarketStatus($row->isSold);?></a></h4>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if($row->isFeatured == 1){
                                            ?>
                                            <h4 class="os-featured"><a rel="tag" href="#"><?php echo Text::_('OS_FEATURED')?></a></h4>
                                            <?php
                                        }
                                        ?>
                                        <h4 > <a rel="tag" href="#"><?php echo $row->type_name;?></a></h4>
                                        <?php
                                        if(($configClass['listing_show_rating'] == 1) and ($configClass['comment_active_comment'] == 1)){
                                            ?>
                                            <h4 class="os-start">
                                                <?php
                                                OSPHelper::showRatingOverPicture($row->rate,$color);
                                                ?>
                                            </h4>
                                        <?php } ?>
                                        <div class="property-price clear <?php echo $priceClass; ?>">
                                            <div class="cat-price">
												<span class="pcategory"> 
													<a rel="tag" href="<?php echo Route::_('index.php?option=com_osproperty&task=category_details&id='.$row->category_id);?>" title="<?php echo Text::_('OS_CATEGORY_DETAILS');?>"><?php echo $row->category_name_short;?>
													</a>
												</span>
                                                <span class="price">
												<?php
                                                if($row->price_text != ""){
                                                    echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
                                                }elseif($row->price_call == 0){
                                                    echo OSPHelper::generatePrice($row->curr,$row->price);
                                                    if($row->rent_time != ""){
                                                        echo " /".Text::_($row->rent_time);
                                                    }
                                                }else{
                                                    echo Text::_('OS_CALL_FOR_PRICE');
                                                }
                                                ?>
												</span>
                                            </div>
                                            <span class="picon"><i class="fa fa-tag"></i></span>
                                        </div>
                                    </figure>
                                </div>

                                <div class="property-info noleftmargin">
                                    <ul>

                                        <?php
                                        if(($configClass['use_squarefeet'] == 1) and ($row->square_feet > 0)){
                                            ?><li class="property-icon-square meta-block">
                                            <i class="ospico-square"></i>
                                            <span>
											<?php
                                            echo OSPHelper::showSquare($row->square_feet);
                                            echo "&nbsp;";
                                            echo OSPHelper::showSquareSymbol();
                                            ?>
										</span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if(($configClass['listing_show_nbedrooms'] == 1) and ($row->bed_room > 0)){
                                            ?><li class="property-icon-bed meta-block"><i class="ospico-bed"></i>
                                            <span><?php echo $row->bed_room;?></span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if(($configClass['listing_show_nbathrooms'] == 1) and ($row->bath_room > 0)){
                                            ?><li class="property-icon-bath meta-block"><i class="ospico-bath"></i>
                                            <span> <?php echo OSPHelper::showBath($row->bath_room);?></span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if($row->parking != ""){
                                            ?><li class="property-icon-parking meta-block"><i class="ospico-parking"></i>
                                            <span><?php echo $row->parking;?></span></li>
                                            <?php
                                        }
                                        ?>

                                    </ul>
                                </div>

                                <div class="property-desc ">
                                    <h4><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><?php echo $row->pro_name?></a>
                                        <?php
                                        if($configClass['show_compare_task'] == 1){
                                            if(! OSPHelper::isInCompareList($row->id)) {

                                                $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
                                                $msg = str_replace("'","\'",$msg);
                                                ?>
                                                <span id="compare<?php echo $row->id;?>">
												<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
													<img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24_gray.png" width="16"/>
												</a>
											</span>
                                                <?php
                                            }else{
                                                $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                $msg = str_replace("'","\'",$msg);
                                                ?>
                                                <span id="compare<?php echo $row->id;?>">
												<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
													<img title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24.png" width="16"/>
												</a>
											</span>
                                                <?php
                                            }
                                        }
                                        if(intval($user->id) > 0){
                                            if($configClass['property_save_to_favories'] == 1){
                                                if($task != "property_favorites"){
                                                    $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
                                                    $count = $db->loadResult();
                                                    if($count == 0){
                                                        ?>
                                                        <span id="favorite_<?php echo $row->id; ?>">
													<?php
                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
                                                    $msg = str_replace("'","\'",$msg);
                                                    ?>
                                                            <span id="fav<?php echo $row->id;?>">
														<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
															<img title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24_gray.png" width="16"/>
														</a>
													</span>
												</span>
                                                        <?php
                                                    }
                                                }
                                                if($count > 0){
                                                    ?>
                                                    <span id="favorite_<?php echo $row->id; ?>">
													<?php
                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
                                                    $msg = str_replace("'","\'",$msg);
                                                    ?>
                                                        <span id="fav<?php echo $row->id;?>">
														<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>">
															<img title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24.png" width="16"/>
														</a>
													</span>
												</span>
                                                    <?php
                                                }
                                            }
                                        }
                                        if(HelperOspropertyCommon::isAgent()){
                                            $my_agent_id = HelperOspropertyCommon::getAgentID();
                                            if($my_agent_id == $row->agent_id){
                                                $link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
                                                ?>
                                                <a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>"  class="fontsize15 paddingleft2 paddingright2">
                                                    <i class="osicon-edit"></i>
                                                </a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </h4><label></label>
                                    <p>
                                        <?php
                                        $small_desc = $row->pro_small_desc;
                                        $small_desc_arr = explode(" ",$small_desc);
                                        if(count($small_desc_arr) > 30){
                                            for($k=0;$k<30;$k++){
                                                echo $small_desc_arr[$k]." ";
                                            }
                                            echo "..";
                                        }else{
                                            echo $small_desc;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </li>
                            <?php
                            if($i % 2 == 1){
                                echo "</ul><div class='clearfix'></div><ul class='margin0 padding0 ".$bootstrapHelper->getClassMapping('row-fluid')."'>";
                            }
                        }
                    }elseif($ncolumns == 3){
                        $l = 0;
                        for($i=0;$i<count($rows);$i++){
                            $l++;
                            $row = $rows[$i];
                            $needs = array();
                            $needs[] = "property_details";
                            $needs[] = $row->id;
                            $itemid = OSPRoute::getItemid($needs);
                            if($configClass['load_lazy']){
                                $photourl = Uri::root()."media/com_osproperty/assets/images/loader.gif";
                            }else{
                                $photourl = $row->photo;
                            }
                            ?>
                            <li class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
                                <div class="property-mask property-image">
                                    <figure class="pimage">
                                        <a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" class="property_mark_a">
                                            <img alt="<?php echo $row->pro_name?>" title="<?php echo $row->pro_name?>" src="<?php echo $photourl;?>" data-original="<?php echo $row->photo; ?>" class="ospitem-imgborder oslazy" id="picture_<?php echo $i?>" />
                                        </a>
                                        <figcaption><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>"><i class="fa fa-link fa-lg"></i></a></figcaption>
                                        <?php
                                        if(($configClass['active_market_status'] == 1) && ($row->isSold > 0)){
                                            ?>
                                            <h4 class="os-sold"><a rel="tag" href="#"><?php echo OSPHelper::returnMarketStatus($row->isSold);?></a></h4>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if($row->isFeatured == 1){
                                            ?>
                                            <h4 class="os-featured"><a rel="tag" href="#"><?php echo Text::_('OS_FEATURED')?></a></h4>
                                            <?php
                                        }
                                        ?>
                                        <h4 > <a rel="tag" href="#"><?php echo $row->type_name;?></a></h4>
                                        <?php
                                        if(($configClass['listing_show_rating'] == 1) and ($configClass['comment_active_comment'] == 1)){
                                            ?>
                                            <h4 class="os-start">
                                                <?php
                                                OSPHelper::showRatingOverPicture($row->rate,$color);
                                                ?>
                                            </h4>
                                        <?php } ?>
                                        <div class="property-price clear <?php echo $priceClass; ?>">
                                            <div class="cat-price">
												<span class="pcategory"> 
													<a rel="tag" href="<?php echo Route::_('index.php?option=com_osproperty&task=category_details&id='.$row->category_id);?>" title="<?php echo Text::_('OS_CATEGORY_DETAILS');?>"><?php echo $row->category_name_short;?>
													</a>
												</span>
                                                <span class="price">
												<?php
                                                if($row->price_text != ""){
                                                    echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
                                                }elseif($row->price_call == 0){
                                                    echo OSPHelper::generatePrice($row->curr,$row->price);
                                                    if($row->rent_time != ""){
                                                        echo " /".Text::_($row->rent_time);
                                                    }
                                                }else{
                                                    echo Text::_('OS_CALL_FOR_PRICE');
                                                }
                                                ?>
												</span>
                                            </div>

                                        </div>
                                    </figure>
                                </div>

                                <div class="property-info noleftmargin">
                                    <ul>

                                        <?php
                                        if(($configClass['use_squarefeet'] == 1) and ($row->square_feet > 0)){
                                            ?><li class="property-icon-square meta-block">
                                            <i class="ospico-square"></i>
                                            <span>
											<?php
                                            echo OSPHelper::showSquare($row->square_feet);
                                            echo "&nbsp;";
                                            echo OSPHelper::showSquareSymbol();
                                            ?>
										</span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if(($configClass['listing_show_nbedrooms'] == 1) and ($row->bed_room > 0)){
                                            ?><li class="property-icon-bed meta-block"><i class="ospico-bed"></i>
                                            <span><?php echo $row->bed_room;?></span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if(($configClass['listing_show_nbathrooms'] == 1) and ($row->bath_room > 0)){
                                            ?><li class="property-icon-bath meta-block"><i class="ospico-bath"></i>
                                            <span> <?php echo OSPHelper::showBath($row->bath_room);?></span></li>
                                            <?php
                                        }
                                        ?>


                                        <?php
                                        if($row->parking != ""){
                                            ?><li class="property-icon-parking meta-block"><i class="ospico-parking"></i>
                                            <span><?php echo $row->parking;?></span></li>
                                            <?php
                                        }
                                        ?>

                                    </ul>
                                </div>

                                <div class="property-desc ">
                                    <h4><a href="<?php echo Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS');?>"><?php echo $row->pro_name?></a>
                                        <?php
                                        if($configClass['show_compare_task'] == 1){
                                            if(! OSPHelper::isInCompareList($row->id)) {

                                                $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
                                                $msg = str_replace("'","\'",$msg);
                                                ?>
                                                <span id="compare<?php echo $row->id;?>">
												<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
													<img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24_gray.png" width="16"/>
												</a>
											</span>
                                                <?php
                                            }else{
                                                $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                $msg = str_replace("'","\'",$msg);
                                                ?>
                                                <span id="compare<?php echo $row->id;?>">
												<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
													<img title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24.png" width="16"/>
												</a>
											</span>
                                                <?php
                                            }
                                        }
                                        if(intval($user->id) > 0){
                                            if($configClass['property_save_to_favories'] == 1){
                                                if($task != "property_favorites"){
                                                    $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
                                                    $count = $db->loadResult();
                                                    if($count == 0){
                                                        ?>
                                                        <span id="favorite_<?php echo $row->id; ?>">
													<?php
                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
                                                    $msg = str_replace("'","\'",$msg);
                                                    ?>
                                                            <span id="fav<?php echo $row->id;?>">
														<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
															<img title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24_gray.png" width="16"/>
														</a>
													</span>
												</span>
                                                        <?php
                                                    }
                                                }
                                                if($count > 0){
                                                    ?>
                                                    <span id="favorite_<?php echo $row->id; ?>">
													<?php
                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
                                                    $msg = str_replace("'","\'",$msg);
                                                    ?>
                                                        <span id="fav<?php echo $row->id;?>">
														<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $row->id?>','<?php echo Uri::root()?>','fav<?php echo $row->id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>">
															<img title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24.png" width="16"/>
														</a>
													</span>
												</span>
                                                    <?php
                                                }
                                            }
                                        }
                                        if(HelperOspropertyCommon::isAgent()){
                                            $my_agent_id = HelperOspropertyCommon::getAgentID();
                                            if($my_agent_id == $row->agent_id){
                                                $link = Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$row->id;
                                                ?>
                                                <a href="<?php echo $link?>" title="<?php echo Text::_('OS_EDIT_PROPERTY')?>" class="fontsize15 paddingleft2 paddingright2">
                                                    <i class="osicon-edit"></i>
                                                </a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </h4><label></label>

                                    <p>
                                        <?php
                                        $small_desc = $row->pro_small_desc;
                                        $small_desc_arr = explode(" ",$small_desc);
                                        if(count($small_desc_arr) > 30){
                                            for($k=0;$k<30;$k++){
                                                echo $small_desc_arr[$k]." ";
                                            }
                                            echo "..";
                                        }else{
                                            echo $small_desc;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </li>
                            <?php

                            if($l == 3){
                                echo "</ul><div class='clearfix'></div><ul class='margin0 padding0 ".$bootstrapHelper->getClassMapping('row-fluid')."'>";
                                $l = 0;
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div>
            <?php
            if((count($rows) > 0) and ($pageNav->total > $pageNav->limit)){
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
        <?php
    }
    ?>
</div>
<input type="hidden" name="process_element" id="process_element" value="" />