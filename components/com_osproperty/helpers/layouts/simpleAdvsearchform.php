<?php
/*------------------------------------------------------------------------
# simpleAdvSearchForm.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$span10Class        = $bootstrapHelper->getClassMapping('span10');
$span4Class         = $bootstrapHelper->getClassMapping('span4');
$span3Class         = $bootstrapHelper->getClassMapping('span3');
$span2Class         = $bootstrapHelper->getClassMapping('span2');
?>
<div class="<?php echo $rowFluidClass; ?>" id="ospropertyadvsearch">
	<div class="<?php echo $span12Class; ?>">
        <div class="<?php echo $rowFluidClass; ?>">
            <div class="<?php echo $span10Class?> keyworddiv">
                <input type="text" class="input-large" placeholder="<?php echo Text::_('OS_KEYWORD_PLACEHOLDER');?>" value="<?php echo htmlspecialchars($lists['keyword_value'])?>" name="keyword"/>
            </div>
            <div class="<?php echo $span2Class?> searchbtn">
                <input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_SEARCH')?>" />
            </div>
        </div>
        <div class="<?php echo $rowFluidClass; ?> searchfields">
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_CATEGORY')?>
                </label>
                <?php echo $lists['category']; ?>
            </div>
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_PROPERTY_TYPE')?>
                </label>
                <?php echo $lists['type'];?>
            </div>
            <div class="<?php echo $span4Class;?> pricegroups" >
                <label>
                    <?php echo Text::_('OS_PRICE_RANGE')?>
                </label>
                <?php
                    OSPHelper::showPriceFilter($lists['price_value'],$lists['min_price'],$lists['max_price'],$lists['adv_type'],'','adv');
                ?>
            </div>
        </div>
        <div class="<?php echo $rowFluidClass; ?> searchfields">
            <?php
            $increase_div = 0;
            if(HelperOspropertyCommon::checkCountry())
            {
                $increase_div++;
                ?>
                <div class="<?php echo $span4Class;?>">
                    <label>
                        <?php echo Text::_('OS_COUNTRY')?>
                    </label>
                    <?php echo $lists['country']?>
                </div>
                <?php
            }
            if(OSPHelper::userOneState())
            {
                echo $lists['state'];
            }
            else
            {
                $increase_div++; //state
                ?>
                <div class="<?php echo $span4Class;?>">
                    <label>
                        <?php echo Text::_('OS_STATE')?>
                    </label>
                    <span id="country_state">
                        <?php echo $lists['state']?>
                    </span>
                </div>
            <?php
            }
            $increase_div++;
            ?>
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_CITY')?>
                </label>

                <span id="city_div">
                    <?php echo $lists['city']?>
                </span>
            </div>
        <?php
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass;?> searchfields">
            <?php
        }
        ?>
        <?php
        if($configClass['active_market_status'] == 1)
        {
        ?>
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_MARKET_STATUS');?>
                </label>
                <?php
                    echo $lists['marketstatus'];
                ?>
            </div>
            <?php $increase_div++;?>
        <?php
        }
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass;?> searchfields">
            <?php
        }

        if($configClass['use_bedrooms'] == 1)
        {
            $increase_div++;
            ?>
            <div class="<?php echo $span4Class; ?>">
                <label>
                    <?php echo Text::_('OS_BEDROOMS')?>
                </label>
                <?php echo $lists['nbed'];?>
            </div>
            <?php
        }
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass; ?>">
            <?php
        }
        if($configClass['use_bathrooms'] == 1)
        {
            $increase_div++;
            ?>
            <div class="<?php echo $span4Class; ?>">
                <label>
                    <?php echo Text::_('OS_BATHROOMS')?>
                </label>
                <?php echo $lists['nbath'];?>
            </div>
            <?php
        }
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass;?> searchfields">
            <?php
        }
        if($configClass['use_rooms'] == 1)
        {
            $increase_div++;
            ?>
            <div class="<?php echo $span4Class; ?>">
                <label>
                    <?php echo Text::_('OS_ROOMS')?>
                </label>
                <?php echo $lists['nroom'];?>
            </div>
            <?php
        }
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass;?> searchfields">
            <?php
        }

        if($configClass['use_squarefeet'] == 1)
        {
            $increase_div++;
            ?>
            <div class="<?php echo $span4Class; ?> squaresearch">
                <label>
                    <?php
                    if($configClass['use_square'] == 0){
                        echo Text::_('OS_SQUARE_FEET');
                    }else{
                        echo Text::_('OS_SQUARE_METER');
                    }
                    ?>
                    <?php
                    echo "(";
                    echo OSPHelper::showSquareSymbol();
                    echo ")";
                    ?>
                </label>
                <input type="text" class="input-mini" name="sqft_min" id="sqft_min" placeholder="<?php echo Text::_('OS_MIN')?>" value="<?php echo isset($lists['sqft_min']) ? $lists['sqft_min']:"";?>" />
                &nbsp;-&nbsp;
                <input type="text" class="input-mini" name="sqft_max" id="sqft_max" placeholder="<?php echo Text::_('OS_MAX')?>" value="<?php echo isset($lists['sqft_max']) ? $lists['sqft_max']:"";?>"/>
            </div>
            <?php
        }
        if($increase_div == 3)
        {
            $increase_div = 0;
            ?>
            </div>
            <div class="<?php echo $rowFluidClass;?> searchfields">
            <?php
        }
        ?>
        </div>
        <div class="<?php echo $rowFluidClass; ?> searchfields">
            <div class="<?php echo $span4Class;?> moreoption">
                <?php echo Text::_('OS_MORE_OPTION');?>
            </div>
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_SORTBY')?>
                </label>
                <?php echo $lists['sortby'];?>
            </div>
            <div class="<?php echo $span4Class;?>">
                <label>
                    <?php echo Text::_('OS_ORDERBY')?>
                </label>
                <?php echo $lists['orderby'];?>
            </div>
        </div>
        <div class="<?php echo $rowFluidClass; ?>">
            <div class="<?php echo $span12Class; ?> alignright searchbtns">
                <input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_SEARCH')?>" />
                <input type="button" onclick="javascript:document.ftForm.reset();" class="btn btn-warning" value="<?php echo Text::_('OS_RESET')?>" />
                <?php
                $user = Factory::getUser();
                if(intval($user->id) > 0)
                {
                    ?>
                    <input type="button" class="btn btn-warning" value="<?php echo Text::_('OS_SAVE_TO_SEARCH_LIST_ADDNEW')?>" onclick="javascript:saveSearchList();" />
                    <?php
                }
                if($jinput->getInt('list_id',0) > 0)
                {
                    ?>
                    <input type="button" class="btn btn-success" value="<?php echo Text::_('OS_SAVE_TO_SEARCH_LIST_UPDATE')?>" onclick="javascript:updateSearchList();" />
                    <?php
                }
                //}
                ?>
            </div>
        </div>
        <div class="<?php echo $rowFluidClass; ?>" style="display:none;" id="amenitiesdiv">
            <div class="<?php echo $span12Class; ?>">
                <?php
                $optionArr = array();
                $optionArr[] = Text::_('OS_GENERAL_AMENITIES');
                $optionArr[] = Text::_('OS_ACCESSIBILITY_AMENITIES');
                $optionArr[] = Text::_('OS_APPLIANCE_AMENITIES');
                $optionArr[] = Text::_('OS_COMMUNITY_AMENITIES');
                $optionArr[] = Text::_('OS_ENERGY_SAVINGS_AMENITIES');
                $optionArr[] = Text::_('OS_EXTERIOR_AMENITIES');
                $optionArr[] = Text::_('OS_INTERIOR_AMENITIES');
                $optionArr[] = Text::_('OS_LANDSCAPE_AMENITIES');
                $optionArr[] = Text::_('OS_SECURITY_AMENITIES');

                $amenities_post = $jinput->get('amenities',array(),'ARRAY');
                $j = 0;
                for($k = 0;$k<count($optionArr);$k++)
                {
                    $j++;
                    $db->setQuery("Select * from #__osrs_amenities where category_id = '".$k."' and published = '1'");
                    $amenities = $db->loadObjectList();
                    if(count($amenities) > 0)
                    {
                        ?>
                        <div class="<?php echo $rowFluidClass; ?>">
                            <div class="<?php echo $span12Class; ?>">
                                <strong>
                                    <?php echo $optionArr[$k];?>
                                </strong>
                            </div>
                        </div>
                        <div class="<?php echo $rowFluidClass; ?>">
                            <?php
                            $j = 0;
                            for($i=0;$i<count($amenities);$i++)
                            {
                                $j++;
                                if(isset($amenities_post))
                                {
                                    if(in_array($amenities[$i]->id,$amenities_post))
                                    {
                                        $checked = "checked";
                                    }
                                    else
                                    {
                                        $checked = "";
                                    }
                                }
                                else
                                {
                                    $checked = "";
                                }
                                ?>
                                <div class="<?php echo $span3Class;?>">
                                    <label for="amenities<?php echo $amenities[$i]->id; ?>">
                                        <input type="checkbox" name="amenities[]" id="amenities<?php echo $amenities[$i]->id; ?>" <?php echo $checked?> value="<?php echo $amenities[$i]->id;?>" /> <?php echo OSPHelper::getLanguageFieldValue($amenities[$i],'amenities');?>
                                    </label>
                                </div>
                                <?php
                                if($j == 4)
                                {
                                    $j = 0;
                                    ?>
                                    </div><div class="<?php echo $rowFluidClass; ?>">
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="openmoreoption" id="openmoreoption" value="0" />
<script type="text/javascript">
jQuery('.moreoption').click(function() {
    if(jQuery('#openmoreoption').val() == 0)
    {
        jQuery('#amenitiesdiv').slideDown();
        jQuery('#openmoreoption').val(1);
        jQuery('.moreoption').text("<?php echo Text::_('OS_LESS_OPTION');?>");
    }
    else
    {
        jQuery('#amenitiesdiv').slideUp();
        jQuery('#openmoreoption').val(0);
        jQuery('.moreoption').text("<?php echo Text::_('OS_MORE_OPTION');?>");
    }

});
</script>