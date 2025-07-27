<?php

/*------------------------------------------------------------------------
# default.php - mod_ospropertysearch
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<script type="text/javascript">
    function updateMyLocation(){
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition(modSearchShowPosition,
                function(error){
                    alert("<?php echo str_ireplace('"', "'",Text::_(''));?>");

                }, {
                    timeout: 30000, enableHighAccuracy: true, maximumAge: 90000
                });
        }
    }

    function modSearchShowPosition(position){
        var exdate=new Date();
        exdate.setDate(exdate.getDate() + 1);
        var ll = position.coords.latitude+'_'+position.coords.longitude;
        document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString()+";path=/";
        document.getElementById('se_geoloc<?php echo $random_id?>').value = '1';
        document.getElementById('ossearchForm<?php echo $random_id?>').submit();
    }

    function submitSearchForm<?php echo $random_id?>(){
        var ossearchForm = document.getElementById('ossearchForm<?php echo $random_id?>');
        var keyword = ossearchForm.keyword;
        var category_id = ossearchForm.category_id;
        var agent_type = ossearchForm.agent_type;
        var property_type = ossearchForm.property_type;
        var agent_id = ossearchForm.agent_id;
        var state = ossearchForm.state;
        var fields = ossearchForm.fields.value;
        var canSubmit = 1;
        var emptyFiemd = 0;
        var mcountry_id = document.getElementById('mcountry_id<?php echo $random_id?>');
        var country_id = ossearchForm.country_id;
        var city = ossearchForm.city;
        var mstate_id = document.getElementById('mstate_id<?php echo $random_id?>');
        var state_id = ossearchForm.state_id;
        var mcity = document.getElementById('city<?php echo $random_id?>');
        if((mcountry_id != null) && (country_id != null)){
            country_id.value = mcountry_id.value;
        }
        if((mstate_id != null) && (state_id != null)){
            state_id.value = mstate_id.value;
        }
        if(( mcity != null) && (city != null)){
            city.value = mcity.value;
        }
        if(fields != ""){
            var fieldArr = fields.split(",");
            var length = fieldArr.length;
            if(keyword != null){
                if(keyword.value == ""){
                    emptyFiemd++;
                }
            }
            if(agent_type != null){
                if(agent_type.value == ""){
                    emptyFiemd++;
                }
            }
            if(property_type != null){
                if(property_type.value == ""){
                    emptyFiemd++;
                }
            }
            if(state != null){
                if(state.value == ""){
                    emptyFiemd++;
                }
            }

        }else{
            ossearchForm.submit();
        }
    }
</script>
<?php
if($samepage == 1){
    $itemid  = $jinput->getInt('Itemid');
}else{
    $needs = array();
    $needs[] = "ladvsearch";
    $needs[] = "property_advsearch";
    $itemid  = OSPRoute::getItemid($needs);
}
$field = "";
?>
<style>
    @media (min-width:801px) and (max-width: 1024px) {
        /* tablet, landscape iPad, lo-res laptops ands desktops */
        .horizontal_search{
            max-width: <?php echo $default_width; ?>px;
            top: <?php echo $default_top; ?>%;
        }
    }
    @media (min-width:1025px) and (max-width: 1280px){
        /* big landscape tablets, laptops, and desktops */
        .horizontal_search{
            max-width: <?php echo $default_width; ?>px;
            top: <?php echo $default_top; ?>%;
        }

    }
    @media (min-width:1281px) {
        /* hi-res laptops and desktops */
        .horizontal_search{
            max-width: <?php echo $default_width; ?>px;
            top: <?php echo $default_top; ?>%;
        }

    }

    @media (min-width:320px) and (max-width: 479px) {
        /* smartphones, portrait iPhone, portrait 480x320 phones (Android) */
        .horizontal_search{
            max-width: 300px;
        }
    }
    @media (min-width:480px) and (max-width: 599px) {
        /* smartphones, Android phones, landscape iPhone */
        .horizontal_search{
            max-width: 460px;
        }
    }
    @media (min-width:600px) and (max-width: 800px) {
        /* portrait tablets, portrait iPad, e-readers (Nook/Kindle), landscape 800x480 phones (Android) */
        .horizontal_search{
            max-width: 580px;
        }
        .
    }

</style>
<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&view=ladvsearch&Itemid='.$itemid)?>" name="ossearchForm<?php echo $random_id?>" id="ossearchForm<?php echo $random_id?>">
    <div class="ospsearch horizontal_search <?php echo $moduleclass_sfx ?> <?php echo $rowFluidClass;?>">
        <div class="<?php echo $span12Class?>">
            <div class="<?php echo $rowFluidClass;?> horizontal_searchrowfluid">
                <div class="<?php echo $span3Class; ?> hitem">
                    <input type="text" class="input-medium form-control" style="width:<?php echo $inputbox_width_site?>px;"  value="<?php echo OSPHelper::getStringRequest('keyword','')?>" id="keyword" name="keyword" placeholder="<?php echo Text::_('OS_SEARCH_KEYWORD')?>" />
                </div>
                <div class="<?php echo $span3Class; ?> hitem">
                    <?php echo $lists['type'];?>
                </div>
                <div class="<?php echo $span3Class; ?> hitem">
                    <?php echo modOspropertySearchHelper::listCategoriesHorizontal($category_id,'',$inputbox_width_site); ?>
                </div>
                <div class="<?php echo $span3Class; ?> hitem">
                    <button class="btn btn btn-danger horizontalsearchbutton" onclick="javascript:submitSearchForm<?php echo $random_id?>()" type="button"><i class="osicon-search icon-search"></i>&nbsp;<?php echo Text::_('OS_SEARCH')?></button>
                    &nbsp;
                    <a href="javascript:void(0);" id="moreoption">
                        <?php echo Text::_('OS_MORE_OPTIONS');?>
                    </a>
                </div>
            </div>
            <?php
            $col = 0;
            ?>
            <div class="<?php echo $rowFluidClass;?> horizontal_searchrowfluid">
                <?php
                if(HelperOspropertyCommon::checkCountry())
                {
                    ?>
                    <div class="<?php echo $span3Class; ?> hitem">
                        <?php echo $lists['country'];?>
                    </div>
                    <?php
                    $col++;
                }
                if(!OSPHelper::userOneState())
                {
                    ?>
                    <div class="<?php echo $span3Class; ?> hitem" id="country_state_search_module<?php echo $random_id?>">
                        <?php echo $lists['state'];?>
                    </div>
                    <?php
                    $col++;
                }
                ?>
                <div class="<?php echo $span3Class; ?> hitem" id="city_div_search_module<?php echo $random_id?>">
                    <?php
                    echo $lists['city'];
                    $col++;
                    ?>
                </div>
                <?php
                if($col == 3)
                {
                    $class = $span3Class;
                }
                else
                {
                    $class = $span6Class;
                }
                ?>
                <div class="<?php echo $class; ?> hitem" id="mod_ossearch_price">
                    <?php
                    OSPHelper::showPriceFilter($price,$jinput->getInt('min_price',0),$jinput->getInt('max_price',0),$property_type,'input-medium',$module->id);
                    ?>
                </div>
            </div>
            <div class="<?php echo $rowFluidClass;?> horizontal_searchrowfluid">
                <?php
                if($configClass['use_bathrooms'] == 1){
                    ?>
                    <div class="<?php echo $span3Class; ?> hitem">
                        <?php echo $lists['nbath'];?>
                    </div>
                    <?php
                }
                if($configClass['use_bedrooms'] == 1){
                    ?>
                    <div class="<?php echo $span3Class; ?> hitem">
                        <?php echo $lists['nbed'];?>
                    </div>
                    <?php
                }
                if($configClass['use_squarefeet'] == 1) {
                    ?>
                    <div class="<?php echo $span3Class; ?> hitem">
                        <input type="text" class="input-mini" name="sqft_min" id="sqft_min" placeholder="<?php echo Text::_('OS_MIN_SQUARE') ?> (<?php echo OSPHelper::showSquareSymbol();?>)"
                               value="<?php echo ($lists['sqft_min'] > 0) ? $lists['sqft_min'] : ""; ?>"/>
                    </div>
                    <div class="<?php echo $span3Class; ?> hitem">
                        <input type="text" class="input-mini" name="sqft_max" id="sqft_max" placeholder="<?php echo Text::_('OS_MAX_SQUARE') ?> (<?php echo OSPHelper::showSquareSymbol();?>)"
                               value="<?php echo ($lists['sqft_max'] > 0) ? $lists['sqft_max'] : ""; ?>"/>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="<?php echo $rowFluidClass;?>" id="conveniencegroups" style="display:none;">
                <div class="<?php echo $span12Class;?>">
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
                    for($k = 0;$k<count($optionArr);$k++) {
                        $db->setQuery("Select * from #__osrs_amenities where category_id = '" . $k . "' and published = '1'");
                        $tmpamenities = $db->loadObjectList();
                        if (count($tmpamenities) > 0) {
                            echo "<strong>" . $optionArr[$k] . "</strong>";
                            echo "<BR />";
                            ?>
                        <div class="<?php echo $rowFluidClass;?>">
                            <?php
                            $j = 0;
                            for ($i = 0; $i < count($tmpamenities); $i++) {
                                if (count($amenities_post) > 0) {
                                    if (in_array($tmpamenities[$i]->id, $amenities_post)) {
                                        $checked = "checked";
                                    } else {
                                        $checked = "";
                                    }
                                }
                                ?>
                                <div class="<?php echo $span3Class;?>">
                                    <input type="checkbox" name="amenities[]" value="<?php echo $tmpamenities[$i]->id;?>" <?php echo $checked;?> /> <?php echo OSPHelper::getLanguageFieldValue($tmpamenities[$i], 'amenities');?>
                                </div>
                                <?php
                                $j++;
                                if($j == 4)
                                {
                                    ?>
                                    </div><div class="<?php echo $rowFluidClass;?>">
                                    <?php
                                    $j = 0;
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
    <?php
    if(!HelperOspropertyCommon::checkCountry())
    {
        echo $lists['country'];
    }
    if(OSPHelper::userOneState())
    {
        echo $lists['state'];
    }
    ?>
    <input type="hidden" name="state_id"  value="" />
    <input type="hidden" name="country_id"  value="" />
    <input type="hidden" name="city" value="" />
    <input type="hidden" name="fields" id="fields" value="<?php echo $field?>" />
    <input type="hidden" name="option" value="com_osproperty" />
    <input type="hidden" name="task" value="property_advsearch" />
    <input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
    <input type="hidden" name="show_advancesearchform" value="<?php echo $show_advancesearchform?>" />
    <?php
    OSPHelper::showPriceTypesConfig();
    ?>
    <?php
    if(count((array)$types) > 0){
        foreach ($types as $type){
            ?>
            <input type="hidden" name="searchmoduletype_id_<?php echo $type->id?>" id="searchmoduletype_id_<?php echo $type->id?>" value="<?php echo implode(",",$type->fields);?>"/>
            <?php
        }
    }
    ?>
</form>
<script type="text/javascript">
    jQuery('#moreoption').click(function(){
        var text = jQuery("#moreoption").text();

        if(text == '<?php echo Text::_('OS_MORE_OPTIONS');?>')
        {
            jQuery('#conveniencegroups').slideDown('slow');
            jQuery("#moreoption").text('<?php echo Text::_('OS_LESS_OPTIONS');?>');
        }
        else
        {
            jQuery('#conveniencegroups').slideUp('slow');
            jQuery("#moreoption").text('<?php echo Text::_('OS_MORE_OPTIONS');?>');
        }
    });
    function modOspropertySearchChangeDiv(div_name){
        var div  = document.getElementById(div_name);
        var atag = document.getElementById('a' + div_name);
        if(div.style.display == "block"){
            div.style.display = "none";
            atag.innerHTML = '[+]';

        }else{
            div.style.display = "block";
            atag.innerHTML = '[-]';
        }
    }

    function modOspropertyChangeValue(item){
        var temp  = document.getElementById(item);
        if(temp.value == 0){
            temp.value = 1;
        }else{
            temp.value = 0;
        }
    }
    function change_country_companyModule<?php echo $random_id?>(country_id,state_id,city_id,random_id){
        var live_site = '<?php echo Uri::root()?>';
        loadLocationInfoStateCityLocatorModule(country_id,state_id,city_id,'mcountry_id' + random_id,'mstate_id' + random_id,live_site,random_id);
    }
    function change_stateModule<?php echo $random_id?>(state_id,city_id,random_id){
        var live_site = '<?php echo Uri::root()?>';
        loadLocationInfoCityModule(state_id,city_id,'mstate_id' + random_id,live_site,random_id);
    }
    <?php if($show_customfields == 1){?>
    jQuery("#property_type<?php echo $module->id?>").change(function(){
        var fields = jQuery("#searchmodulefield_ids").val();
        var fieldArr = fields.split(",");
        if(fieldArr.length > 0){
            for(i=0;i<fieldArr.length;i++){
                jQuery("#searchmoduleextrafields_" + fieldArr[i]).hide("fast");
            }
        }
        var selected_value = jQuery("#property_type<?php echo $module->id?>").val();
        var selected_fields = jQuery("#searchmoduletype_id_" + selected_value).val();
        var fieldArr = selected_fields.split(",");
        if(fieldArr.length > 0){
            for(i=0;i<fieldArr.length;i++){
                jQuery("#searchmoduleextrafields_" + fieldArr[i]).show("slow");
            }
        }
    });
    <?php } ?>
    <?php
    if($show_price == 1){
    ?>
    jQuery("#property_type<?php echo $module->id;?>").change(function() {
        updatePrice(jQuery("#property_type<?php echo $module->id;?>").val(),"<?php echo Uri::root(); ?>");
    });
    <?php } ?>
    function updatePrice(type_id,live_site){
        xmlHttp=GetXmlHttpObject();
        url = live_site + "index.php?option=com_osproperty&no_html=1&tmpl=component&task=ajax_updatePrice&type_id=" + type_id + "&option_id=<?php echo $price;?>&min_price=<?php echo $jinput->getInt('min_price',0);?>&max_price=<?php echo $jinput->getInt('max_price',0);?>&module_id=<?php echo $module->id;?>";
        xmlHttp.onreadystatechange = ajax_updateSearch;
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
    }

    function ajax_updateSearch(){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
            var mod_osservice_price = document.getElementById("mod_ossearch_price");
            if(mod_osservice_price != null) {
                mod_osservice_price.innerHTML = xmlHttp.responseText;
                var ptype = jQuery("#property_type<?php echo $module->id;?>").val();
                jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
                jQuery(function () {
                    var min_value = jQuery("#min" + ptype).val();
                    min_value = parseFloat(min_value);
                    var step_value = jQuery("#step" + ptype).val();
                    step_value = parseFloat(step_value);
                    var max_value = jQuery("#max" + ptype).val();
                    max_value = parseFloat(max_value);
                    jQuery("#<?php echo $module->id;?>sliderange").slider({
                        range: true,
                        min: min_value,
                        step: step_value,
                        max: max_value,
                        values: [min_value, max_value],
                        slide: function (event, ui) {
                            var price_from = ui.values[0];
                            var price_to = ui.values[1];
                            jQuery("#<?php echo $module->id;?>price_from_input1").val(price_from);
                            jQuery("#<?php echo $module->id;?>price_to_input1").val(price_to);

                            price_from = price_from.formatMoney(0, ',', '.');
                            price_to = price_to.formatMoney(0, ',', '.');

                            jQuery("#<?php echo $module->id;?>price_from_input").text(price_from);
                            jQuery("#<?php echo $module->id;?>price_to_input").text(price_to);
                        }
                    });
                });
                Number.prototype.formatMoney = function (decPlaces, thouSeparator, decSeparator) {
                    var n = this,
                        decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
                        decSeparator = decSeparator == undefined ? "." : decSeparator,
                        thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
                        sign = n < 0 ? "-" : "",
                        i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
                        j = (j = i.length) > 3 ? j % 3 : 0;
                    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
                };
            }
        }
    }
</script>
