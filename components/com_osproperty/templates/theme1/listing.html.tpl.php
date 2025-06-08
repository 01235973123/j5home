<?php
/*------------------------------------------------------------------------
# listing.html.tpl.php - Ossolution Property
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

echo OSPHelper::loadTooltip();
?>
<script type="text/javascript">
    function loadStateInListPage() {
        var country_id = document.getElementById('country_id');
        loadStateInListPageAjax(country_id.value, "<?php echo Uri::root() ?>");
    }

    function changeCity(state_id, city_id) {
        var live_site = '<?php echo Uri::root() ?>';
        loadLocationInfoCity(state_id, city_id, 'state_id', live_site);
    }
</script>
<div id="notice" style="display:none;">

</div>

<?php
$document = Factory::getDocument();
$document->addStyleSheet('//fonts.googleapis.com/css?family=Oswald:700');
$titleColor = $params->get('titleColor', '#03b4ea');
$show_google_map = $params->get('show_map', 1);
//HelperOspropertyCommon::filterForm($lists);
?>
<style>
    .os-propertytitle {
        color: <?php echo $titleColor;
                ?> !important;
    }
</style>
<?php
HelperOspropertyCommon::filterForm($lists);
?>
<div id="listings">
    <?php
    if (count($rows) > 0) {

        $db = Factory::getDbo();
        $db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where id <> '$row->curr' order by currency_code");
        $currencies   = $db->loadObjectList();
        $currenyArr[] = HTMLHelper::_('select.option', '', Text::_('OS_SELECT'));
        $currenyArr   = array_merge($currenyArr, $currencies);
    ?>
        <input type="hidden" name="currency_item" id="currency_item" value="" />
        <input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root() ?>" />
        <div class="clearfix"></div>
        <?php
        if ($show_google_map == 1) {
            if ($configClass['map_type'] == 0) {
                if (HelperOspropertyGoogleMap::loadMapInListing($rows)) {
        ?>
                    <div id="map_canvas" class="map2x relative"></div>
        <?php
                }
            } else {
                HelperOspropertyOpenStreetMap::loadMapInListing($rows);
            }
        }
        ?>
        <div class="latestproperties latestproperties_right">
            <?php
            $k = 0;
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $needs = array();
                $needs[] = "property_details";
                $needs[] = $row->id;
                $itemid = OSPRoute::getItemid($needs);
                $lists['curr'] = HTMLHelper::_('select.genericlist', $currenyArr, 'curr' . $i, 'onChange="javascript:updateCurrency(' . $i . ',' . $row->id . ',this.value)" class="input-small"', 'value', 'text');
                if ($configClass['load_lazy']) {
                    $photourl = Uri::root() . "media/com_osproperty/assets/images/loader.gif";
                } else {
                    $photourl = $row->photo;
                }
            ?>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> os_item">
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                            <div class="os_property-title <?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span9'); ?>">
                                        <span class="os-propertytitle title-blue">
                                            <a title="<?php echo Text::_('OS_PROPERTY_DETAILS'); ?>"
                                                href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=" . $row->id . "&Itemid=" . $itemid) ?>"
                                                class="os-propertytitle">
                                                <?php
                                                if (($row->ref != "") and ($configClass['show_ref'] == 1)) {
                                                ?>
                                                    <?php echo $row->ref ?>,
                                                <?php
                                                }
                                                ?>
                                                <?php echo $row->pro_name ?>
                                            </a>
                                            <?php
                                            if (($row->show_address == 1) && ($row->lat_add != "") && ($row->long_add != "") && ($show_google_map == 1)) {
                                                if ($configClass['map_type'] == 0) {
                                            ?>
                                                    <a href="#map_canvas"
                                                        onclick="javascript:openMarker(<?php echo $row->mapid; ?>);return false;"
                                                        class="maplink"><i class="osicon-location"></i> </a>
                                                <?php
                                                } else {
                                                ?>
                                                    <a href="#map_canvas" id="openmap<?php echo $row->mapid; ?>" class="maplink"><i
                                                            class="osicon-location"></i> </a>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        echo $row->featured_ico;
                                        echo $row->market_ico;
                                        echo $row->just_added_ico;
                                        echo $row->just_updated_ico;
                                        ?>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
                                        <span class="os_currency_red">
                                            <?php

                                            if (OSPHelper::getLanguageFieldValue($row, 'price_text') != "") {
                                                echo " " . OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row, 'price_text'));
                                            } elseif ($row->price_call == 0) {
                                                echo OSPHelper::generatePrice($row->curr, $row->price);
                                                if ($row->rent_time != "") {
                                                    echo " /" . Text::_($row->rent_time);
                                                }
                                                if ($configClass['show_convert_currency'] == 1) {
                                            ?>

                                            <?php
                                                }
                                            } else {
                                                echo " " . Text::_('OS_CALL_FOR_DETAILS_PRICE');
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> os_property-main">
                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
                                        <div id="os_images">
                                            <a title="<?php echo Text::_('OS_PROPERTY_DETAILS'); ?>"
                                                href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=" . $row->id . "&Itemid=" . $itemid) ?>"
                                                class="ositem-hrefphoto">
                                                <img alt="<?php echo $row->pro_name ?>" title="<?php echo $row->pro_name ?>"
                                                    src="<?php echo $photourl; ?>" data-original="<?php echo $row->photo; ?>"
                                                    class="ospitem-imgborder oslazy" />

                                            </a>
                                            <?php
                                            if ($row->isFeatured == 1) {
                                            ?>
                                                <div class="os_featured">
                                                    <?php echo Text::_('OS_FEATURED'); ?>
                                                </div>
                                            <?php
                                            }
                                            if (($configClass['listing_show_rating'] == 1) and ($configClass['comment_active_comment'] == 1)) {
                                            ?>
                                                <div class="os_rating">
                                                    <?php
                                                    OSPHelper::showRatingOverPicture($row->rate, $titleColor);
                                                    ?>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if (OSPHelper::isSoldProperty($row, $configClass)) {
                                            ?>
                                                <div class="os_sold">
                                                    <?php echo Text::_('OS_SOLD'); ?>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="os_types_red">
                                                <?php echo $row->type_name; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span8'); ?> os-leftpad">
                                        <div
                                            class="ospitem-leftpad <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ">
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                        <div class="os_category os-toppad">
                                                            <?php echo $row->category_name; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if (($row->show_address == 1) and ($configClass['listing_show_address'] == 1)) {
                                                ?>
                                                    <div
                                                        class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> os-address">
                                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                            <strong>
                                                                <?php echo Text::_('OS_ADDRESS') ?>:
                                                            </strong>
                                                            <?php
                                                            echo OSPHelper::generateAddress($row);
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php }

                                                ?>
                                                <div
                                                    class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> os-desc">
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                        <?php
                                                        $pro_small_desc = $row->pro_small_desc;
                                                        $pro_small_descArr = explode(" ", $pro_small_desc);
                                                        if (count($pro_small_descArr) > 15) {
                                                            for ($j = 0; $j < 15; $j++) {
                                                                echo $pro_small_descArr[$j] . " ";
                                                            }
                                                            echo "..";
                                                        } else {
                                                            echo $pro_small_desc;
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <?php
                                                $fieldarr = (array)$row->fieldarr;
                                                if (count($fieldarr) > 0) {
                                                ?>
                                                    <div
                                                        class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ospitem-bopad">
                                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                            <?php
                                                            for ($f = 0; $f < count($fieldarr); $f++) {
                                                                $field = $fieldarr[$f];
                                                                if ($field->fieldvalue != "") {
                                                            ?>
                                                                    <p><span class="field">
                                                                            <?php
                                                                            if ($field->label != "") {
                                                                                echo $field->label;
                                                                            ?>
                                                                        </span> <span>:
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                        <?php echo $field->fieldvalue; ?>
                                                                        </span></p>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>

                                                <div
                                                    class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> os_bottom">
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                        <?php
                                                        $user = Factory::getUser();
                                                        $db   = Factory::getDBO();

                                                        if ($configClass['show_compare_task'] == 1) {

                                                            if (! OSPHelper::isInCompareList($row->id)) {
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_COMPARE_LIST');
                                                                $msg = str_replace("'", "\'", $msg);
                                                        ?>
                                                                <span id="compare<?php echo $row->id; ?>">
                                                                    <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id; ?>','theme1','listing')"
                                                                        href="javascript:void(0)" class="btn btn-warning btn-small">
                                                                        <i class="osicon-bookmark osicon-white"></i>
                                                                        <?php echo Text::_('OS_ADD_TO_COMPARE_LIST'); ?>
                                                                    </a>
                                                                </span>
                                                            <?php
                                                            } else {
                                                                $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
                                                                $msg = str_replace("'", "\'", $msg);
                                                            ?>
                                                                <span id="compare<?php echo $row->id; ?>">
                                                                    <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $row->id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id; ?>','theme1','listing')"
                                                                        href="javascript:void(0)" class="btn btn-warning btn-small">
                                                                        <i class="osicon-bookmark osicon-white"></i>
                                                                        <?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST'); ?>
                                                                    </a>
                                                                </span>
                                                                <?php
                                                            }
                                                        }
                                                        if (intval($user->id) > 0) {
                                                            if ($configClass['property_save_to_favories'] == 1) {
                                                                //if($task != "property_favorites"){
                                                                $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
                                                                $count = $db->loadResult();
                                                                if ($count == 0) {
                                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTSbtrbtr');
                                                                    $msg = str_replace("'", "\'", $msg);
                                                                ?>
                                                                    <span id="fav<?php echo $row->id; ?>">
                                                                        <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addFavorites','<?php echo $row->id ?>','<?php echo Uri::root() ?>','fav<?php echo $row->id; ?>','theme1','listing')"
                                                                            href="javascript:void(0)" class="btn btn-success btn-small">
                                                                            <i class="osicon-ok osicon-white"></i>
                                                                            <?php echo Text::_('OS_ADD_TO_FAVORITEShtrtr'); ?>
                                                                        </a>
                                                                    </span>
                                                                <?php
                                                                }
                                                                if ($count > 0) {
                                                                    $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
                                                                    $msg = str_replace("'", "\'", $msg);
                                                                ?>
                                                                    <span id="fav<?php echo $row->id; ?>">
                                                                        <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeFavorites','<?php echo $row->id ?>','<?php echo Uri::root() ?>','fav<?php echo $row->id; ?>','theme1','listing')"
                                                                            href="javascript:void(0)" class="btn btn-success btn-small">
                                                                            <i class="osicon-remove osicon-white"></i>
                                                                            <?php echo Text::_('OS_REMOVE_FAVORITES'); ?>
                                                                        </a>
                                                                    </span>
                                                                <?php
                                                                }
                                                            }
                                                            if (HelperOspropertyCommon::isAgent()) {
                                                                $my_agent_id = HelperOspropertyCommon::getAgentID();

                                                                if ($my_agent_id == $row->agent_id) {
                                                                    $link = Uri::root() . "index.php?option=com_osproperty&task=property_edit&id=" . $row->id;
                                                                ?>
                                                                    <span id="favorite_1">
                                                                        <a href="<?php echo $link ?>"
                                                                            title="<?php echo Text::_('OS_EDIT_PROPERTY') ?>"
                                                                            class="btn btn-danger btn-small">
                                                                            <i class="osicon-edit osicon-white"></i>
                                                                            <?php echo Text::_('OS_EDIT_PROPERTY'); ?>

                                                                        </a>
                                                                    </span>
                                                        <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <a title="<?php echo Text::_('OS_PROPERTY_DETAILS'); ?>"
                                                            class="btn btn-info btn-small"
                                                            href="<?php echo Route::_("index.php?option=com_osproperty&task=property_details&id=" . $row->id . "&Itemid=" . $itemid) ?>">
                                                            <i class="osicon-file osicon-white"></i>
                                                            <?php echo Text::_('OS_DETAILS'); ?> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="os_property-meta">
                            <ul>
                                <?php
                                if ($configClass['use_squarefeet'] == 1 && $row->square_feet > 0) {
                                ?><li class="property-icon-square meta-block">
                                        <i class="os-icon-sqmt os-2x"></i>
                                        <span>
                                            <?php
                                            echo OSPHelper::showSquare($row->square_feet);
                                            echo "&nbsp;";
                                            echo OSPHelper::showSquareSymbol();
                                            ?>
                                        </span>
                                    </li>
                                <?php
                                }
                                ?>

                                <?php
                                if (($configClass['listing_show_nbedrooms'] == 1) and ($row->bed_room > 0)) {
                                ?><li class="property-icon-bed meta-block"><i class="os-icon-bedroom os-2x"></i>
                                        <span><?php echo $row->bed_room; ?></span>
                                    </li>
                                <?php
                                }
                                ?>


                                <?php
                                if (($configClass['listing_show_nbathrooms'] == 1) and ($row->bath_room > 0)) {
                                ?><li class="property-icon-bath meta-block"><i class="os-icon-bathroom os-2x"></i>
                                        <span> <?php echo OSPHelper::showBath($row->bath_room); ?></span>
                                    </li>
                                <?php
                                }
                                ?>


                                <?php
                                if (($configClass['use_parking'] == 1) and ($row->parking != "")) {
                                ?><li class="property-icon-parking meta-block"><i class="os-icon-parking os-2x"></i>
                                        <span><?php echo $row->parking; ?></span>

                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div>
            <?php
            if ((count($rows) > 0) and ($pageNav->total > $pageNav->limit)) {
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