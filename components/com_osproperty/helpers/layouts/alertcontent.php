<?php
/*------------------------------------------------------------------------
# alertcontent.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2021 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

?>
<HTML>
<BODY STYLE="font-family: Arial Narrow, sans-serif;width:100%;">
    <table style="width:100%;">
        <tr>
            <td style="width:100%;text-align:center;">
                <table style="width:100%;">
                    <?php
                    foreach($properties as $property)
					{
                        ?>
                        <tr>
                            <td style="width:100%;text-align:left;border-bottom:1px solid #CCC;border-right:1px solid #CCC;border-top:1px solid #efefef;border-left:1px solid #efefef;">
                                <table style="width:100%">
                                    <tr>
                                        <td width="20%" valign="top">
											<div style="position:relative;">
												<img src="<?php echo $property->image; ?>" style="border:1px solid #CCC;margin:5px;width:300px;"/>
												<span style="text-transform: uppercase;position: absolute;bottom: 10px;right: 10px;background-color: white;opacity: 0.7;padding: 0px 5px;">
													<?php
													if($property->is_new == 1)
													{
														echo Text::_('OS_NEW');
													}
													else
													{
														echo Text::_('OS_UPDATED');
													}
													?>
												</span>
											</div>
                                        </td>
                                        <td width="80%" valign="top" style="background-color:#efefef;">
                                            <table style="width:100%;">
                                                <tr>
                                                    <td style="width:100%;">
                                                        <h3>
                                                            <?php
                                                            echo "<a href='$property->detailsurl' target='_blank'>";
															if($property->ref){
																echo $property->ref.", ";
															}
															echo OSPHelper::getLanguageFieldValue($property,'pro_name');
                                                            echo "</a>";
                                                            ?>
                                                            &nbsp;|&nbsp;
                                                            <?php
                                                            if($property->price_call == 1){
                                                                echo Text::_('OS_CALL_FOR_PRICE');
                                                            }else{
                                                                echo OSPHelper::generatePrice($property->curr,$property->price);
                                                                if($property->rent_time != ""){
                                                                    echo "/".Text::_($property->rent_time);
                                                                }
                                                            }
                                                            if($property->isFeatured == 1){
                                                                ?>
                                                                &nbsp;|&nbsp;
                                                                <?php
                                                                echo Text::_('OS_FEATURED');
                                                            }
                                                            ?>
                                                        </h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:100%;">
                                                        <?php
                                                        if($property->show_address == 1){
                                                            echo OSPHelper::generateAddress($property);
                                                        }
                                                        ?>
                                                        <BR />
                                                        <?php echo Text::_('OS_CATEGORY');?>: <?php echo OSPHelper::getCategoryNamesOfProperty($property->id) ; ?>
                                                        <BR />
                                                        <?php echo Text::_('OS_TYPE');?>: <?php echo OSPHelper::getLanguageFieldValue($property->property_type,'type_name') ; ?>
														<?php
														if($configClass['active_market_status'] == 1 && $property->isSold > 0){
														?>
														<BR />
														<?php 
														
														echo Text::_('OS_MARKET_STATUS');?>: <?php echo OSPHelper::returnMarketStatus($property->isSold);?>
														<?php
														}
														?>
                                                        <BR /><BR />
                                                        <?php
                                                        $addInfo = array();
                                                        if(($property->bed_room > 0) and ($configClass['use_bedrooms'] == 1)){
                                                            $addInfo[] = $property->bed_room." ".Text::_('OS_BEDROOMS');
                                                        }
                                                        if(($property->bath_room > 0) and ($configClass['use_bathrooms'] == 1)){
                                                            $addInfo[] = OSPHelper::showBath($property->bath_room)." ".Text::_('OS_BATHROOMS');
                                                        }
                                                        if(($property->rooms > 0) and ($configClass['use_rooms'] == 1)){
                                                            $addInfo[] = $property->rooms." ".Text::_('OS_ROOMS');
                                                        }
                                                        if(($property->square_feet > 0) and ($configClass['use_squarefeet'] == 1)){
                                                            $addInfo[] = OSPHelper::showSquareLabels().": ".$property->square_feet." ".OSPHelper::showSquareSymbol();
                                                        }
                                                        if(($property->lot_size > 0) and ($configClass['use_squarefeet'] == 1)){
                                                            $addInfo[] = Text::_('OS_LOT_SIZE').": ".$property->lot_size." ". OSPHelper::showSquareSymbol();
                                                        }
                                                        ?>
                                                        <?php
                                                        if(count($addInfo) > 0){
                                                            echo implode(" | ",$addInfo);
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr><td height="15"></td></tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
</BODY>
</HTML>