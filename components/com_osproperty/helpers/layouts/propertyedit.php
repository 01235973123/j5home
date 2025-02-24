<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Language\Text;
?>
<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&Itemid='.$itemid)?>" name="ftForm1" id="ftForm1" enctype="multipart/form-data" class="form-horizontal">
<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
<?php
$translatable = Multilanguage::isEnabled() && count($languages);
$editor = Editor::getInstance(Factory::getConfig()->get('editor'));
$require_field = "";
$require_label = "";
$db = Factory::getDbo();
OSPHelperJquery::colorbox('a.osmodal');
$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini');
?>
<link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/css/textext.core.css" type="text/css" />
<link rel="stylesheet" href="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/css/textext.plugin.tags.css" type="text/css" />
<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/js/textext.core.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/tag/js/textext.plugin.tags.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo Uri::root()?>media/com_osproperty/assets/js/paymentmethods.js" type="text/javascript"></script>
<?php
if (OSPHelper::isJoomla4() && $configClass['frontend_upload_type'] == 0)
{
?>
	
<?php
}

if (OSPHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
	Factory::getDocument()->getWebAssetManager()->useScript('showon');
}
else
{
	$tabApiPrefix = 'bootstrap.';
	HTMLHelper::_('script', 'jui/cms.js', ['version' => 'auto', 'relative' => true]);
}
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> osp-container" id="propertyModification">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<h1 class="componentheading">
			<?php
			echo Text::_('OS_PROPERTY'). " <small>[".$edit."]</small>";
			?>
		</h1>
		<div class="clearfix"></div>
		<div class="btn-toolbar">
            <div class="btn-group pull-right">
                <?php
                $buttons = (array)OSPHelper::returnToolbarButtons($row->id);
                ?>
                <?php if(in_array(0,$buttons)){?>
                    <button type="button" class="btn hasTooltip btn-danger" title="<?php echo Text::_('OS_SAVE');?>" onclick="javascript:submitftForm(2);">
                        <i class="osicon-save"></i> <?php echo Text::_('OS_SAVE');?> & <?php echo Text::_('OS_ACTIVATE_LISTING');?>
                    </button>
                <?php } ?>
                <?php if(in_array(1,$buttons)){?>
                    <button type="button" class="btn hasTooltip btn-success" title="<?php echo Text::_('OS_SAVE');?>" onclick="javascript:submitftForm(1);">
                        <i class="osicon-save"></i> <?php echo Text::_('OS_SAVE');?>
                    </button>
                <?php } ?>
                <?php if(in_array(2,$buttons)){?>
                    <button type="button" class="btn hasTooltip btn-info" title="<?php echo Text::_('OS_APPLY');?>" onclick="javascript:submitftForm(0);">
                        <i class="osicon-apply"></i> <?php echo Text::_('OS_APPLY');?>
                    </button>
                <?php  } ?>
                <?php if(in_array(2,$buttons)){?>
                    <button type="button" class="btn hasTooltip btn-warning" title="<?php echo Text::_('OS_CANCEL');?>" onclick="javascript:gotoDefaultPage();">
                        <i class="osicon-unpublish"></i> <?php echo Text::_('OS_CANCEL');?>
                    </button>
                <?php  } ?>
            </div>
        </div>
		<div class="clearfix"></div>
		
		<?php
        /*
		if($configClass['integrate_membership'] == 1){//use membership integration
			$agentAcc = $lists['agentAcc'];
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
			<?php
			HelperOspropertyCommon::generateMembershipForm($agentAcc,'property',$row->id);
			?>
			</div>
			<div class="clearfix"></div>
			<?php
		}
        */
		?>
		<!-- General tab-->
		<!-- OS_ADDRESS panel2-->
		<!-- OS_GENERAL_INFORMATION panel1-->
		<!-- OS_OTHER_INFORMATION panel7-->
		<!-- OS_PHOTOS panel3-->
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> margintop10">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php 
				if ($translatable)
				{
					echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'propertytranslation', array('active' => 'general-page'));
					echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertytranslation', 'general-page', Text::_('OS_GENERAL', true));
				}
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row'); ?>" id="general-page">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<?php
						if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
						{
							echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'propertyinformation', array('active' => 'addpropertypanel1'));
						}
						else
						{
							echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'propertyinformation', array('active' => 'addpropertypanel1'));
						}
						?>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel1', Text::_('OS_GENERAL_INFORMATION', true));
						?>
						<div class="tab-pane" id="addpropertypanel1">
						<!-- End General tab-->
							<div class="col width-100 nomargin nopadding">
								<fieldset class="fieldsetpropertydetails">
									<legend><strong><i class="edicon edicon-home"></i>&nbsp;<?php echo strtoupper(Text::_('OS_GENERAL_INFORMATION' )); ?></strong></legend>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6');?>">
                                            <?php
                                            if($configClass['ref_field'] == 0){
                                                ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                        <span class="hasTip" title="<?php echo Text::_('Ref #');?>">
                                                            <?php echo Text::_('Ref #');?>
                                                        </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input class="<?php echo $inputLargeClass; ?>" type="text" name="ref" id="ref" value="<?php echo htmlspecialchars($row->ref);?>" placeholder="<?php echo Text::_('Ref #');?>" />
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="<?php echo Text::_('OS_TITLE');?>::<?php echo Text::_('OS_PROPERTY_TITLE_EXPLAIN');?>">
                                                        <?php echo Text::_('OS_TITLE')?> *
                                                    </span>
                                                </label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <input placeholder="<?php echo Text::_('OS_TITLE');?>" class="<?php echo $inputLargeClass; ?>" type="text" name="pro_name" id="pro_name" value="<?php echo htmlspecialchars($row->pro_name);?>" />
                                                    <input type="hidden" name="pro_alias" id="pro_alias" value="<?php echo htmlspecialchars($row->pro_alias);?>" />
                                                </div>
                                            </div>
                                            <?php
                                            $require_field .= "pro_name,";
                                            $require_label .= Text::_('OS_PROPERTY_TITLE').",";
                                            ?>
                                            <?php
                                            $require_field .= "category_id,";
                                            $require_label .= Text::_('OS_CATEGORY').",";
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="<?php echo Text::_('OS_CATEGORY');?>::<?php echo Text::_('OS_CATEGORY_EXPLAIN');?>">
                                                        <?php echo Text::_('OS_CATEGORY')?> *
                                                    </span>
                                                </label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php echo $lists['category']; ?>
                                                </div>
                                            </div>
                                            <?php
                                            $require_field .= "pro_type,";
                                            $require_label .= Text::_('OS_PROPERTY_TYPE').",";
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="<?php echo Text::_('OS_PROPERTY_TYPE');?>::<?php echo Text::_('OS_PROPERTY_TYPE');?>">
                                                        <?php echo Text::_('OS_PROPERTY_TYPE')?> *
                                                    </span>
                                                </label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php echo $lists['type']; ?>
                                                </div>
                                            </div>
                                            <?php
                                            if($configClass['active_market_status'] == 1){ ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_MARKET_STATUS');?>::<?php echo Text::_('OS_MARKET_STATUS');?>">
												<?php echo Text::_('OS_MARKET_STATUS')?>
											</span>
                                                </label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php echo OSPHelper::buildDropdownMarketStatus($row->isSold); ?>
                                                </div>
                                            </div>
                                            <?php }
                                            ?>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6');?>">
                                            <?php
                                            if(HelperOspropertyCommon::isCompanyAdmin()){
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="<?php echo Text::_('OS_SELECT_AGENT');?>">
                                                        <?php echo Text::_('OS_SELECT_AGENT')?> *
                                                    </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php
                                                        echo $lists['agent'];
                                                        if($lists['agentname'] != ""){
                                                            echo $lists['agentname'];
                                                        }
                                                        $require_field .= "agent_id,";
                                                        $require_label .= Text::_('OS_AGENT').",";
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            elseif (Factory::getUser()->authorise('frontendmanage', 'com_osproperty'))
                                            {
                                                ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                        <?php echo Text::_('OS_AGENT')?>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?> agentnameAddProperty">
                                                        <?php echo $lists['agentname']; ?>
                                                    </div>
                                                </div>
                                                <?php
                                                echo $lists['agent'];
                                            }
                                            elseif(HelperOspropertyCommon::isAgent())
                                            {
                                                echo $lists['agent'];
                                            }
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_("OS_CALL_FOR_PRICE")?></label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php
                                                    echo $lists['price_call'];
                                                    //$require_field .= "price_call,";
                                                    //$require_label .= Text::_('OS_CALL_FOR_PRICE').",";
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <?php
                                            if($row->price_call == 0){
                                                $display = "block";
                                            }else{
                                                $display = "none";
                                            }
                                            ?>
                                            <div id="pricediv" style="display:<?php echo $display;?>;">
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                        <?php echo Text::_("OS_PRICE")?>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <span class="input-append">
                                                    <input type="text" name="price" id="price" value="<?php echo $row->price?>" class="<?php echo $inputSmallClass; ?> inlinedisplay" placeholder="0.00" />
                                                        <?php
                                                        HelperOspropertyCommon::showCurrencySelectList($row->curr);
                                                        ?>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                        <span class="hasTip" title="<?php echo Text::_('OS_PRICE_FOR');?>">
                                                            <?php echo Text::_('OS_PRICE_FOR')?>
                                                        </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['time'];?>
                                                    </div>
                                                </div>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                        <span class="hasTip" title="<?php echo Text::_('OS_PRICE_TEXT_EXPLAIN');?>">
                                                            <?php echo Text::_('OS_PRICE_TEXT')?>
                                                        </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="price_text" id="price_text" value="<?php echo $row->price_text; ?>" class="<?php echo $inputLargeClass; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									
									<?php
									if($row->id == 0){
										$row->access = $configClass['default_access_level'];
									}
									?>
									<input type="hidden" name="access" id="access" value="<?php echo $row->access; ?>" />
								</fieldset>

                                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span6');?>">
                                        <fieldset class="fieldsetpropertydetails">
                                        <legend><strong><i class="edicon edicon-location"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_LOCATION' )); ?></strong></legend>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                            <span class="hasTip" title="<?php echo Text::_('OS_SHOW_ADDRESS');?>::<?php echo Text::_('OS_SHOW_ADDRESS_EXPLAIN');?>">
                                                <?php echo Text::_('OS_SHOW_ADDRESS');?>
                                            </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                <?php
                                                echo $lists['show_address'];
                                                ?>
                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_ADDRESS');?>::<?php echo Text::_('OS_ADDRESS_EXPLAIN');?>">
                                                    <?php echo Text::_('OS_ADDRESS');?> <?php
                                                    if($configClass['address_required']){
                                                    ?>*<?php } ?>
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="address_controls">
                                                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($row->address);?>" size="50" class="<?php echo $inputLargeClass; ?>" placeholder="<?php echo Text::_('OS_ADDRESS');?>" />
                                                <input type="text" name="postcode" id="postcode" value="<?php echo htmlspecialchars($row->postcode);?>" class="<?php echo $inputMiniClass; ?>" placeholder="<?php echo Text::_('OS_POSTCODE');?>" />
                                                <?php
                                                if($configClass['address_required']){
                                                ?>
                                                    <?php
                                                    $require_field .= "address,";
                                                    $require_label .= Text::_('OS_ADDRESS').",";
                                                    ?>
                                                <?php }
                                                if($configClass['require_postcode']==1){
                                                    $require_field .= "postcode,";
                                                    $require_label .= Text::_('OS_POSTCODE').",";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        if(HelperOspropertyCommon::checkCountry()){
                                        ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_COUNTRY');?>::<?php echo Text::_('OS_COUNTRY_EXPLAIN');?>">
                                                    <?php echo Text::_('OS_COUNTRY');?> *
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                <?php
                                                echo $lists['country'];
                                                ?>
                                                <?php
                                                $require_field .= "country,";
                                                $require_label .= Text::_('OS_COUNTRY').",";
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        }else{
                                            echo $lists['country'];
                                        }
                                        if(OSPHelper::userOneState()){
                                            echo $lists['state'];
                                        }else{
                                        ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_STATE');?>::<?php echo Text::_('OS_STATE_EXPLAIN');?>">
                                                    <?php echo Text::_('OS_STATE');?> *
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                <div id="country_state">
                                                <?php
                                                echo $lists['state'];
                                                $configClass['require_state'] = 1;
                                                if($configClass['require_state'] == 1){
                                                    $require_field .= "state,";
                                                    $require_label .= Text::_('OS_STATE').",";
                                                }
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_CITY');?>::<?php echo Text::_('OS_CITY_EXPLAIN');?>">
                                                    <?php echo Text::_('OS_CITY');?> *
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                <div id="city_div">
                                                    <?php
                                                    echo $lists['city'];
                                                    ?>
                                                </div>
                                                <?php
                                                if($configClass['require_city']==1){
                                                    ?>
                                                    <?php
                                                    $require_field .= "city,";
                                                    $require_label .= Text::_('OS_CITY').",";
                                                }

                                                ?>
                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_REGION');?>">
                                                    <?php echo Text::_('OS_REGION');?>
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                <input  type="text" name="region" id="region" value="<?php echo htmlspecialchars($row->region);?>" class="<?php echo $inputLargeClass; ?>" placeholder="<?php echo Text::_('OS_REGION');?>" />

                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                            <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                <span class="hasTip" title="<?php echo Text::_('OS_COORDINATES');?>">
                                                    <?php echo Text::_('OS_COORDINATES');?>
                                                </span>
                                            </label>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="coordinates_controls">
                                                <input class="<?php echo $inputSmallClass; ?>" type="text" name="lat_add" id="lat_add" value="<?php echo htmlspecialchars($row->lat_add);?>" size="30" placeholder="<?php echo Text::_('OS_LATTITUDE');?>" />
                                                &nbsp;
                                                <input class="<?php echo $inputSmallClass; ?>" type="text" name="long_add" id="long_add" value="<?php echo htmlspecialchars($row->long_add);?>" size="30" placeholder="<?php echo Text::_('OS_LONGTITUDE');?>" />
                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>" id="coordinates_controls1">
                                            <?php echo Text::_('OS_GRAD_AND_DROP_THE_MAP_FOR_COORDINATES');?>
                                            <div class="clearfix"></div>
                                            <?php

                                            if(($row->lat_add == "") or (intval($row->id) == 0))
											{
                                                $row->lat_add = $configClass['goole_default_lat'];
                                            }
                                            if(($row->long_add == "") or (intval($row->id) == 0))
											{
                                                $row->long_add = $configClass['goole_default_long'];
                                            }
                                            $geocode	= array();
											$tmp		= new \stdClass();
                                            $tmp->lat	= $row->lat_add;
                                            $tmp->long	= $row->long_add;
											$geocode[0] = $tmp;

                                            if($configClass['map_type'] == 0)
                                            {
                                                HelperOspropertyGoogleMap::loadGMapinEditProperty($geocode,"mapDiv","lat_add","long_add");
                                                ?>
                                                <div id="mapDiv" class="width100pc border1" style="height: <?php echo $configClass['property_map_height']?>px;"></div>
                                                <BR />
                                                <div>
                                                    <strong><?php echo Text::_('OS_ENTER_ADDRESS_TO_CHECK_LATTITUDE_AND_LONGTITUDE')?></strong>
                                                    <input type="text" name="add" id="add" value="" size="20" class="<?php echo $inputLargeClass; ?>" />
                                                    <a href="javascript:showAddress(document.ftForm1.add.value);" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" /><?php echo Text::_("OS_SEARCH")?></a>
                                                </div>
                                                <?php
                                            }
                                            else
                                            {
                                                HelperOspropertyOpenStreetMap::loadGMapinEditProperty($geocode,"map","lat_add","long_add");
                                                ?>
                                                <div id="map" class="width100pc border1" style="height: <?php echo $configClass['property_map_height']?>px;overflow:hidden;"></div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </fieldset>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('span6');?>">
                                        <fieldset class="fieldsetpropertydetails">
                                            <legend><strong><i class="edicon edicon-list"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_INFORMATION' )); ?></strong></legend>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <?php echo Text::_('OS_SMALL_DESCRIPTION')?> <?php if($configClass['short_desc_required']){?>*<?php } ?>
                                                <textarea style="width:95% !important;" name="pro_small_desc" id="pro_small_desc" cols="50" rows="5" class="inputbox"><?php echo $row->pro_small_desc?></textarea>
                                                <?php
                                                if($configClass['short_desc_required']){
                                                    ?>
                                                    <?php
                                                    $require_field .= "pro_small_desc,";
                                                    $require_label .= Text::_('OS_SHORT_DESCRIPTION').",";
                                                    ?>
                                                <?php } ?>
                                            </div>
                                            <?php if($configClass['use_rooms']== 1){ ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="# <?php echo Text::_('OS_NUMBER_ROOMS')?>">
                                                        # <?php echo Text::_('OS_NUMBER_ROOMS')?>
                                                    </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['nrooms'];?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if($configClass['use_bathrooms']== 1){ ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="# <?php echo Text::_('OS_NUMBER_BATHROOMS')?>">
                                                        # <?php echo Text::_('OS_NUMBER_BATHROOMS')?>
                                                    </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['nbath'];?>
                                                    </div>
                                                </div>
                                            <?php } 
											if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
											{
												$bathInfor = $lists['bathInfor'];
											?>
												<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
													<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
														# <?php echo Text::_('OS_BATHROOM_INFORMATION')?>
													</label>
													<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
														<table width="40%" class="admintable">
															<tr>
																<td class="key">
																	<?php echo Text::_('OS_FULL');?>
																</td>
																<TD>
																	<input type="text" name="os_full" class="<?php echo $inputSmallClass; ?>" value="<?php echo OSPHelper::showSquare($bathInfor['OS_FULL']);?>"/>
																</TD>
															</tr>
															<tr>
																<td class="key">
																	<?php echo Text::_('OS_THREE_QUARTER');?>
																</td>
																<TD>
																	<input type="text" name="os_three_quarter" class="<?php echo $inputSmallClass; ?>" value="<?php echo OSPHelper::showSquare($bathInfor['OS_THREE_QUARTER']);?>"/>
																</TD>
															</tr>
															<tr>
																<td class="key">
																	<?php echo Text::_('OS_HALF');?>
																</td>
																<TD>
																	<input type="text" name="os_half" class="<?php echo $inputSmallClass; ?>" value="<?php echo OSPHelper::showSquare($bathInfor['OS_HALF']);?>"/>
																</TD>
															</tr>
															<tr>
																<td class="key">
																	<?php echo Text::_('OS_QUARTER');?>
																</td>
																<TD>
																	<input type="text" name="os_quarter" class="<?php echo $inputSmallClass; ?>" value="<?php echo OSPHelper::showSquare($bathInfor['OS_QUARTER']);?>"/>
																</TD>
															</tr>
															<tr>
																<td class="key">
																	<?php echo Text::_('OS_ENSUITE');?>
																</td>
																<TD>
																	<input type="text" name="os_ensuite" class="<?php echo $inputSmallClass; ?>" value="<?php echo OSPHelper::showSquare($bathInfor['OS_ENSUITE']);?>"/>
																</TD>
															</tr>
														</table>
													</div>
												</div>
											<?php
											}
											?>
											
                                            <?php if($configClass['use_bedrooms']== 1){ ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                    <span class="hasTip" title="# <?php echo Text::_('OS_NUMBER_BEDROOMS')?>">
                                                        # <?php echo Text::_('OS_NUMBER_BEDROOMS')?>
                                                    </span>
                                                    </label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['nbed'];?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <?php echo Text::_('OS_AGENT_NOTE')?>
                                                <textarea class="<?php echo $inputLargeClass; ?>" style="width:95% !important;" name="note" id="note" cols="50" rows="5"><?php echo stripslashes($row->note);?></textarea>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
							</div>
                            <?php
                            if(count($amenities) > 0){
                            ?>
                            <fieldset class="fieldsetpropertydetails">
                                <legend><strong><i class="edicon edicon-folder-open"></i>&nbsp;<?php echo strtoupper(Text::_('OS_CONVENIENCE' )); ?></strong></legend>
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
                                for($k = 0;$k<count($optionArr);$k++){
                                    $db->setQuery("Select * from #__osrs_amenities where category_id = '".$k."' and published = '1'");
                                    $tmpamenities = $db->loadObjectList();
                                    if(count($tmpamenities) > 0){
                                        ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                <strong><?php echo $optionArr[$k];?></strong>
                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> paddingleft10">
                                            <?php
                                            $j = 0;
                                            for($i=0;$i<count($tmpamenities);$i++){
                                                $j++;
                                                if(count($amenitylists) > 0){
                                                    if(in_array($tmpamenities[$i]->id,$amenitylists)){
                                                        $checked = "checked";
                                                    }else{
                                                        $checked = "";
                                                    }
                                                }else{
                                                    $checked = "";
                                                }
                                                ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
                                                    <label for="amenities<?php echo $tmpamenities[$i]->id; ?>">
                                                        <input type="checkbox" name="amenities[]" <?php echo $checked?> value="<?php echo $tmpamenities[$i]->id?>" id="amenities<?php echo $tmpamenities[$i]->id; ?>" /> &nbsp;
                                                        <?php echo OSPHelper::getLanguageFieldValue($tmpamenities[$i],'amenities'); //$tmpamenities[$i]->amenities;?>
                                                    </label>
                                                </div>
                                                <?php
                                                if($j % 4 == 0){
                                                    echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')." paddingleft10'>";
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </fieldset>
                            <?php
                            }
                            ?>
						</div>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTab');
						?>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel2', Text::_('OS_DETAILS', true));
						?>
						<div class="tab-pane" id="addpropertypanel2">
							<div class="col width-100 nomargin nopadding">
								<fieldset class="fieldsetpropertydetails">
									<legend><strong><i class="edicon edicon-zoom-in"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_DETAILS' )); ?></strong></legend>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<?php echo Text::_('OS_FULL_DESCRIPTION')?>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php
											echo $editor->display( 'pro_full_desc',  stripslashes($row->pro_full_desc) , '95%', '250', '75', '20',false ) ;
											?>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<?php echo Text::_('OS_TAGS')?>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<table  width="100%" class="admintable">
												<tr>
													<td>
														<table width="100%" id="property_tag_table">
															<tr>
																<th>
																	<?php echo Text::_('OS_KEYWORD')?>
																</th>
																<?php 
																if($translatable){
																	foreach ($languages as $language)
																	{												
																		$sef = $language->sef;
																		?>
																		<th>
																			<?php echo Text::_('OS_KEYWORD')?>
																			<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" />
																		</th>
																		<?php 
																	}
																}
																?>
																<th>
																	&nbsp;
																</th>
															</tr>
															<?php 
															if(count((array)$lists['tags']) > 0){
																foreach ($lists['tags'] as $tag){
																?>
																<tr id="tag_table_tr">
																	<td>
																		<input type="text" name="keyword[]" value="<?php echo $tag->keyword?>" class="<?php echo $inputSmallClass; ?>" />
																	</td>
																	<?php 
																	if($translatable){
																		foreach ($languages as $language)
																		{												
																			$sef = $language->sef;
																			?>
																			<td>
																				<input type="text" name="keyword_<?php echo $sef;?>[]" value="<?php echo $tag->{'keyword_'.$sef}?>" class="<?php echo $inputSmallClass; ?>" />
																			</td>
																			<?php 
																		}
																	}
																	?>
																	<td>
																		<input type="button" class="btn removetag" value="<?php echo Text::_('OS_DELETE');?>" />
																	</td>
																</tr>
																<?php 
																}
															}
															?>
															<tr id="tag_table_tr">
																<td>
																	<input type="text" name="keyword[]" value="" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<?php 
																if($translatable){
																	foreach ($languages as $language)
																	{												
																		$sef = $language->sef;
																		?>
																		<td>
																			<input type="text" name="keyword_<?php echo $sef;?>[]" value="" class="<?php echo $inputSmallClass; ?>" />
																		</td>
																		<?php 
																	}
																}
																?>
																<td>
																	<input type="button" class="btn addtag" value="<?php echo Text::_('OS_ADD');?>" />
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<script type="text/javascript">
											jQuery(document).ready(function(){		
												jQuery('#property_tag_table').on('click', '.removetag', function(){
													jQuery(this).parent().parent().remove();
												});
												
												jQuery('#property_tag_table').on('click', '.addtag', function(){
													jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
													jQuery(this).attr('class','btn removetag');
													<?php 
													$value  = '<tr id="tag_table_tr"><td><input type="text" name="keyword[]" value="" class="<?php echo $inputSmallClass; ?>" /></td>';
													if($translatable){
														foreach ($languages as $language)
														{												
															$sef = $language->sef;
															$value .= '<td><input type="text" name="keyword_'.$sef.'[]" value="" class="<?php echo $inputSmallClass; ?>" /></td>';
														}
													}
													$value .= '<td><input type="button" class="btn addtag" value="'.Text::_('OS_ADD').'" /></td></tr>';
													?>
													var appendTxt = '<?php echo $value;?>';
													jQuery("#property_tag_table>tbody>tr:last").after(appendTxt);			
												}); 
											});
											</script>
										</div>
									</div>
                                    <?php
                                    $fieldLists = array();
                                    if(count($groups) > 0){
                                        if($row->id > 0){
                                            $cssclass = "display:block;";
                                        }else{
                                            $cssclass = "display:none;";
                                        }
                                        ?>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="fieldgroups_div" style="<?php echo $cssclass;?>">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                <?php
                                                if($translatable){
                                                    $lang_suffix = OSPHelper::getFieldSuffix();
                                                }
                                                for($i=0;$i<count($groups);$i++){
                                                    $group = $groups[$i];
                                                    $fields = $group->fields;
                                                    ?>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                                            <?php
                                                            echo "<strong><h4>".OSPHelper::getLanguageFieldValue($group,'group_name')."</h4></strong>";
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $n = 0;
                                                    ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                                    <?php
                                                    for($j=0;$j<count($fields);$j++){
                                                        $n++;
                                                        $field = $fields[$j];
                                                        $fieldLists[] = $field->id;
                                                        if($field->required == 1){
                                                            //$require_field .= $field->field_name.",";
                                                            //$require_label .= $field->{'field_label'.$lang_suffix}.",";
                                                        }
                                                        if(intval($row->id) == 0){
                                                            $display = "display:none;";
                                                        }else{
                                                            $db->setQuery("Select count(fid) from #__osrs_extra_field_types where type_id = '$row->pro_type' and fid = '$field->id'");
                                                            $count = $db->loadResult();
                                                            if($count > 0){
                                                                $display = "";
                                                            }else{
                                                                $display = "display:none;";
                                                            }
                                                        }
                                                        ?>
                                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>" id="extrafield_<?php echo $field->id?>" style="<?php echo $display;?>">
                                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
                                                                <span class="hasTip" title="<?php echo $field->field_label?>::<?php echo $field->field_description?>">
                                                                     <?php echo $field->{'field_label'.$lang_suffix}?>
                                                                </span>
                                                                </label>
                                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                                    <?php
                                                                    HelperOspropertyFields::showField($field,$row->id);
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        if($n % 3 == 0){
                                                            ?>
                                                            </div><div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
									<?php
									if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
									{
										echo HTMLHelper::_('bootstrap.startAccordion', 'menu-pane3', array('active' => 'base_fields'));
									}
									else
									{
										echo HTMLHelper::_('sliders.start', 'menu-pane3');
									}
									if($configClass['use_parking']== 1)
									{
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BASE_PROPERTY_FIELDS'), 'base_fields');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_PARKING'), 'parking_fields');
										}
									?>
										<table  width="100%" class="admintable" id="parking_fields_table">
											<tr>
												<td class="key">
													<?php echo Text::_('OS_GARAGE_DESCRIPTION')?>
												</td>
												<td width="80%">
													<input type="text" name="garage_description" id="garage_description" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->garage_description;?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													# <?php echo Text::_('OS_PARKING')?>
												</td>
												<td>
													<input type="text" name="parking" id="parking" size="20" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->parking; ?>" />
												</td>
											</tr>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									?>
									<?php
									if($configClass['use_nfloors']== 1)
									{
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BUILDING_INFORMATION'), 'building_info');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_BUILDING_INFORMATION'), 'building_info');
										}
										
									?>
										<table  width="100%" class="admintable" id="building_info_table">
											<tr>
												<td class="key">
													<?php echo Text::_('OS_YEAR_BUILT')?>
												</td>
												<td width="80%">
													<input type="text" name="built_on" id="built_on" size="20" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->built_on; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_YEAR_REMODELED')?>
												</td>
												<td width="80%">
													<input type="text" name="remodeled_on" id="remodeled_on" size="20" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->remodeled_on; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_HOUSE_STYLE')?>
												</td>
												<td width="80%">
													<input type="text" name="house_style" id="house_style" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->house_style; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_HOUSE_CONSTRUCTION')?>
												</td>
												<td width="80%">
													<input type="text" name="house_construction" id="house_construction" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->house_construction; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_EXTERIOR_FINISH')?>
												</td>
												<td width="80%">
													<input type="text" name="exterior_finish" id="exterior_finish" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->exterior_finish; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_ROOF')?>
												</td>
												<td width="80%">
													<input type="text" name="roof" id="roof" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->roof; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													# <?php echo Text::_('OS_NUMBER_OF_FLOORS')?>
												</td>
												<td>
													<?php echo $lists['nfloors'];?>
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FLOORING')?>
												</td>
												<td width="80%">
													<input type="text" name="flooring" id="flooring" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->flooring; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_LOWER'); ?>
												</td>
												<td width="80%">
													<input type="text" name="floor_area_lower" id="floor_area_lower" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->floor_area_lower; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_MAIN_LEVEL'); ?>
												</td>
												<td width="80%">
													<input type="text" name="floor_area_main_level" id="floor_area_main_level" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->floor_area_main_level; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_UPPER'); ?>
												</td>
												<td width="80%">
													<input type="text" name="floor_area_upper" id="floor_area_upper" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->floor_area_upper; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FLOOR_AREA')?> <?php echo Text::_('OS_TOTAL'); ?>
												</td>
												<td width="80%">
													<input type="text" name="floor_area_total" id="floor_area_total" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->floor_area_total; ?>" />
												</td>
											</tr>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									?>
									<?php
									if($configClass['basement_foundation']== 1)
									{

										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BASEMENT_FOUNDATION'), 'basement_foundation');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_BASEMENT_FOUNDATION'), 'basement_foundation');
										}
										
									?>
										<table  width="100%" class="admintable" id="basement_foundation_table">
											<tr>
												<td class="key">
													<?php echo Text::_('OS_BASEMENT_FOUNDATION')?>
												</td>
												<td width="80%">
													<input type="text" name="basement_foundation" id="basement_foundation" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->basement_foundation;?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													# <?php echo Text::_('OS_BASEMENT_SIZE')?>(<?php echo OSPHelper::showSquareSymbol();?>)
												</td>
												<td>
													<input type="text" name="basement_size" id="basement_size" size="20" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->basement_size; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_PERCENT_FINISH')?>
												</td>
												<td width="80%">
													<input type="text" name="percent_finished" id="percent_finished" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->percent_finished;?>" />
												</td>
											</tr>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									?>
									<?php
									
									if($configClass['use_squarefeet']== 1)
									{
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_LAND_INFORMATION'), 'land_info');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_LAND_INFORMATION'), 'land_info');
										}
										
									?>
										<table  width="100%" class="admintable" id="land_info_table">
											<tr>
												<td class="key">
													<?php echo Text::_('OS_SUBDIVISION')?>
												</td>
												<td width="80%">
													<input type="text" name="subdivision" id="subdivision" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->subdivision;?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_LAND_HOLDING_TYPE')?>
												</td>
												<td width="80%">
													<input type="text" name="land_holding_type" id="land_holding_type" size="20" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->land_holding_type;?>" />
												</td>
											</tr>
											<!-- living areas-->
                                            <tr>
                                                <td class="key">
                                                    <?php echo Text::_('OS_LIVING_AREAS')?>
                                                </td>
                                                <td width="80%">
                                                    <input type="text" name="living_areas" id="living_areas" class="<?php echo $inputLargeClass; ?>" value="<?php echo $row->living_areas;?>" />
                                                </td>
                                            </tr>
											<tr>
												<td class="key">
													# <?php echo Text::_('OS_LOT_SIZE');?>(<?php echo OSPHelper::showSquareSymbol();?>)
												</td>
												<td width="80%">
													<input type="text" name="lot_size" id="lot_size" size="10" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->lot_size;?>" />
												</td>
											</tr>
                                            <!-- Square feet -->
                                            <tr>
                                                <td class="key">
                                                    # <?php echo OSPHelper::showSquareLabels();?>(<?php echo OSPHelper::showSquareSymbol();?>)
                                                </td>
                                                <td width="80%">
                                                    <input type="text" name="square_feet" id="square_feet" size="10" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->square_feet?>"/>
                                                </td>
                                            </tr>
											<tr>
												<td class="key">
													# <?php echo Text::_('OS_TOTAL_ACRES');?>
												</td>
												<td>
													<input type="text" name="total_acres" id="total_acres" size="10" class="<?php echo $inputSmallClass; ?>" value="<?php echo $row->total_acres?>"/>
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_LOT_DIMENSIONS');?>
												</td>
												<td>
													<input type="text" name="lot_dimensions" id="lot_dimensions" size="10" class="<?php echo $inputMediumClass; ?>" value="<?php echo $row->lot_dimensions?>"/>
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_FRONTPAGE');?>
												</td>
												<td>
													<input type="text" name="frontpage" id="frontpage" size="10" class="<?php echo $inputMediumClass; ?>" value="<?php echo $row->frontpage;?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_DEPTH');?>
												</td>
												<td>
													<input type="text" name="depth" id="depth" size="10" class="<?php echo $inputMediumClass; ?>" value="<?php echo $row->depth;?>" />
												</td>
											</tr>
										</table>
										<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}

									if($configClass['use_business'] == 1)
									{
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_BUSINESS_INFORMATION'), 'business_info');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_BUSINESS_INFORMATION'), 'business_info');
										}
										
										?>
										<table  width="100%" class="admintable" id="business_info_table">
											<?php
											$businessArr = array('takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities');
											foreach($businessArr as $business){
											?>
												<tr>
													<td class="key" >
														<?php echo Text::_("OS_".strtoupper($business))?>
													</td>
													<td width="80%">
														<input type="text" class="<?php echo $inputLargeClass; ?>" name="<?php echo $business;?>" id="<?php echo $business;?>" value="<?php echo $row->{$business};?>">
													</td>
												</tr>
											<?php } ?>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}

									if($configClass['use_rural'] == 1)
									{
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_RURAL_INFORMATION'), 'rural_info');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_RURAL_INFORMATION'), 'rural_info');
										}
										
										?>
										<table  width="100%" class="admintable" id="rural_info_table">
											<?php
											$businessArr = array('fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
											foreach($businessArr as $business){
											?>
												<tr>
													<td class="key" >
														<?php echo Text::_("OS_".strtoupper($business))?>
													</td>
													<td width="80%">
														<input type="text" class="<?php echo $inputLargeClass; ?>" name="<?php echo $business;?>" id="<?php echo $business;?>" value="<?php echo $row->{$business};?>">
													</td>
												</tr>
											<?php } ?>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									?>
									<?php
									if($configClass['energy'] == 1)
									{
										
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane3', Text::_('OS_ENERGY_AND_CLIMATE'), 'energy_and_climate');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_ENERGY_AND_CLIMATE'), 'energy_and_climate');
										}
										
										?>
										<table  width="100%" class="admintable" id="energy_and_climate_table">
											<tr>
												<td class="key" >
													<?php echo Text::_('OS_ENERGY')?>
												</td>
												<td>
													<?php
													echo OSPHelper::getDpeClassDropdownlist(0,$row->e_class);
													?>
													&nbsp;
													<input type="text" class="<?php echo $inputMiniClass; ?>" name="energy" id="energy" size="5" value="<?php echo $row->energy;?>"> kWH/m
												</td>
											</tr>
											<tr>
												<td class="key" >
													<?php echo Text::_('OS_CLIMATE')?>
												</td>
												<td>
													<?php
													echo OSPHelper::getDpeClassDropdownlist(1,$row->c_class);
													?>
													&nbsp;
													<input type="text" class="<?php echo $inputMiniClass; ?>" name="climate" id="climate" size="5" value="<?php echo $row->climate;?>"> kg/m
												</td>
											</tr>
										</table>
									<?php
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
										echo HTMLHelper::_('bootstrap.endAccordion');
									}else{
										echo HTMLHelper::_('sliders.end');	
									}
									?>
                                    </div>
								</fieldset>
							</div>
						</div>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTab');
						echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel3', Text::_('OS_OTHER_INFORMATION', true));
						?>
						<div class="tab-pane" id="addpropertypanel3">	
							<div class="col width-100 nomargin nopadding">
								<fieldset id="otherinformation" class="fieldsetpropertydetails">
									<legend><strong><i class="edicon edicon-info"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_OTHER_INFORMATION' )); ?></strong></legend>
									<?php
									
									if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
									{
										echo HTMLHelper::_('bootstrap.startAccordion', 'menu-pane2', array('active' => 'setup'));
									}
									else
									{
										echo HTMLHelper::_('sliders.start', 'menu-pane2');   
									}
									if($configClass['use_open_house'] == 1)
									{
									
										if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
										{
											echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane2', Text::_('OS_PROPERTY_OPEN_HOUSE'), 'setup');
										}
										else
										{
											echo HTMLHelper::_('sliders.panel', Text::_('OS_PROPERTY_OPEN_HOUSE'), 'setup');   
										}
									
										?>
										<table  width="100%" class="admintable">
											<tr>
												<td>
													<table width="100%" id="property_open_table">
														<tr>
															<th>
																<?php echo Text::_('OS_FROM')?>
															</th>
															<th>
																<?php echo Text::_('OS_TO')?>
															</th>
														</tr>
														<?php 
														$j = 0;
														if(count((array)$lists['open']) > 0){
															foreach ($lists['open'] as $cal){
																$j++;
																?>
																<tr>
																	<td>
																		<?php echo HTMLHelper::calendar($cal->start_from,'start_from[]','start_from'.$j,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
																	</td>
																	<td>
																		<?php echo HTMLHelper::calendar($cal->end_to,'end_to[]','end_to'.$j,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
																	</td>
																</tr>
																<?php 
															}
														}
														if($j < 5){
															for($i=$j+1;$i<=5;$i++){
															?>
															<tr id="history_table_tr">
																<td>
																	<?php echo HTMLHelper::calendar('','start_from[]','start_from'.$i,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
																</td>
																<td>
																	<?php echo HTMLHelper::calendar('','end_to[]','end_to'.$i,'%Y-%m-%d %H:%M:%S',array('showTime' => true));?>
																</td>
															</tr>
															<?php 
															}
														}
														?>
													</table>
												</td>
											</tr>
										</table>
									<?php 
										if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
											echo HTMLHelper::_('bootstrap.endSlide');
										}
									}
									?>
									<?php
									if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
									{
										echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane2', Text::_('OS_OTHER_INFORMATION'), 'meta');
									}
									else
									{
										echo HTMLHelper::_('sliders.panel', Text::_('OS_OTHER_INFORMATION'), 'meta');   
									}
									?>
									<?php  if($configClass['show_metatag'] ==1){ ?>
									
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_META_DESCRIPTION');?>::<?php echo Text::_('OS_META_EXPLAIN');?>">
												<?php echo Text::_('OS_META_DESCRIPTION')?>
											</span>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php 
											if ($translatable)
											{
											?>
												<ul class="nav nav-tabs">
													<li class="active"><a href="#meta-general-page" data-toggle="tab"><?php echo Text::_('OS_GENERAL'); ?></a></li>
													<li><a href="#meta-translation-page" data-toggle="tab"><?php echo Text::_('OS_TRANSLATION'); ?></a></li>									
												</ul>		
												<div class="tab-content">
													<div class="tab-pane active" id="meta-general-page">			
											<?php	
											}
											?>
												<table  width="100%" class="admintable padding5">
													<tr>
														<td class="key" valign="top">
															<?php echo Text::_('OS_META_DESCRIPTION')?>
														</td>
														<td>
															<textarea name="metadesc" id="metadesc" cols="40" rows="4"><?php echo $row->metadesc?></textarea>
														</td>
													</tr>
												</table>
											<?php 
											if ($translatable)
											{
												?>
												</div>
												<div class="tab-pane" id="meta-translation-page">
													<ul class="nav nav-tabs">
														<?php
															$i = 0;
															foreach ($languages as $language) {						
																$sef = $language->sef;
																?>
																<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#meta-translation-page-<?php echo $sef; ?>" data-toggle="tab"><?php echo $language->title; ?>
																	<img src="<?php echo Uri::root(); ?>media/com_osproperty/flags/<?php echo $sef.'.png'; ?>" /></a></li>
																<?php
																$i++;	
															}
														?>			
													</ul>		
													<div class="tab-content">			
														<?php	
														$i = 0;
														foreach ($languages as $language)
														{												
															$sef = $language->sef;
														?>
														<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="meta-translation-page-<?php echo $sef; ?>">
															<table  width="100%" class="admintable padding5">
																<tr>
																	<td class="key" valign="top">
																		<?php echo Text::_('OS_META_DESCRIPTION')?>
																	</td>
																	<td>
																		<textarea  name="metadesc_<?php echo $sef; ?>" id="metadesc_<?php echo $sef; ?>" cols="40" rows="4"><?php echo $row->{'metadesc_'.$sef}?></textarea>
																	</td>
																</tr>
															</table>
														</div>
														<?php
														$i++;	
														}
														?>
													</div>
												</div>
												</div>
												<?php
											}
											?>
										</div>
									</div>
									<?php 
													
									} ?>
										
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_VIDEO_EMBED_CODE');?>::<?php echo Text::_('OS_VIDEO_EMBED_CODE_EXPLAIN');?>">
												<?php echo Text::_('OS_VIDEO_EMBED_CODE')?>
											</span>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<textarea class="inputbox" name="pro_video" id="pro_video" cols="50" rows="3" class="inputbox"><?php echo $row->pro_video?></textarea>
										</div>
									</div>
										
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_DOCUMENT_LINK');?>::<?php echo Text::_('OS_DOCUMENT_LINK_EXPLAIN');?>">
												<?php echo Text::_('OS_DOCUMENT_LINK')?>
											</span>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<input class="<?php echo $inputLargeClass; ?>" type="text" name="pro_pdf" id="pro_pdf" class="input-xlarge" value="<?php echo $row->pro_pdf;?>" />
										</div>
									</div>
										
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_UPLOAD_DOCUMENT');?>::<?php echo Text::_('OS_UPLOAD_DOCUMENT_EXPLAIN');?>">
												<?php echo Text::_('OS_UPLOAD_DOCUMENT')?>
											</span>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php
											$limit_pdfs = ($configClass['limit_upload_pdfs'] > 0) ? $configClass['limit_upload_pdfs']:10;
											$activate_pdfs = 0;
											for($f = 0; $f < $limit_pdfs ; $f++)
											{
												if($f == 0)
												{
													$fname = "";
												}
												else
												{
													$fname = $f;
												}
												$name = "pro_pdf_file".$fname;

												if($row->{$name} != "" && file_exists(JPATH_ROOT.'/media/com_osproperty/document/'.$row->{$name}))
												{
													$activate_pdfs++;
													$fileUrl = Uri::root().'media/com_osproperty/document/'.$row->{$name};
													?>
													<a href="<?php echo $fileUrl;?>" target="_blank" title="<?php echo Text::_('OS_VIEW_DOCUMENT')?>"><?php echo $row->{$name}; ?></a>

													<BR />
													<input type="checkbox" name="remove_pdf<?php echo $fname; ?>" id="remove_pdf<?php echo $fname; ?>" onclick="javascript:changeValue('remove_pdf<?php echo $fname; ?>')" value="0" /> <strong><?php echo Text::_('OS_REMOVE_DOCUMENT');?></strong>
													<BR />
													<?php
												}
												?>
												<span id="pro_pdf_filediv">
												<input type="file" name="<?php echo $name;?>" id="<?php echo $name;?>" size="40" class="inputbox form-control ilarge" onchange="javascript:checkUploadDocumentFiles('<?php echo $name;?>')"> 
												<div class="clearfix"></div>
												<?php echo Text::_('OS_DOCUMENTS_ALLOWED');?>
												</span>

											<?php
											}
											
											?>
										</div>
									</div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
										<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
											<span class="hasTip" title="<?php echo Text::_('OS_PANORAMA');?>::<?php echo Text::_('OS_PANORAMA_EXPLAIN');?>">
												<?php echo Text::_('OS_PANORAMA')?>
											</span>
										</label>
										<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
											<?php
											if($row->panorama != ""){
												?>
												<a href="<?php echo Uri::root()?>images/osproperty/properties/panorama/<?php echo $row->panorama?>" target="_blank" title="<?php echo Text::_('OS_VIEW_DOCUMENT')?>"><?php echo $row->panorama?></a>
												<BR />
												<input type="checkbox" name="remove_panorama" id="remove_panorama" onclick="javascript:changeValue('remove_panorama')" value="0" /> <strong><?php echo Text::_('OS_REMOVE');?></strong>
												<BR />
												<?php
											}
											?>
											<span id="pro_pdf_filediv">
											<input type="file" name="panorama" id="panorama" size="40" class="<?php echo $inputLargeClass; ?>" onchange="javacript:check_file('panorama');" />
											<div class="clearfix"></div>
											</span>
										</div>
									</div>
									<?php
									if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
										echo HTMLHelper::_('bootstrap.endSlide');
									}
									if($configClass['show_neighborhood_group'] == 1)
									{
										if(count($neighborhoods) > 0)
										{
											if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
											{
												echo HTMLHelper::_('bootstrap.addSlide', 'menu-pane2', Text::_('OS_NEIGHBORHOOD'), 'neighborhood');
											}
											else
											{
												echo HTMLHelper::_('sliders.panel', Text::_('OS_NEIGHBORHOOD'), 'neighborhood'); 
											}
													
											?>
											<table  width="100%" class="admintable" id="neighborhoodtable">
												<?php
												for($i=0;$i<count($neighborhoods);$i++)
												{
													$neighborhood = $neighborhoods[$i];
													$db->setQuery("Select * from #__osrs_neighborhood where pid = '$row->id' and neighbor_id = '$neighborhood->id'");
													$neighbor_value = $db->loadObjectList();
													if(count($neighbor_value) > 0)
													{
														$checked = "checked";
														$value = 1;
														$display = "block";
														
														$neighbor_value = $neighbor_value[0];
														$mins = $neighbor_value->mins;
														$traffic_type = $neighbor_value->traffic_type;
														$walk = "";
														$car = "";
														$train = "";
														switch ($traffic_type)
														{
															case "1":
																$walk = "checked";
															break;
															case "2":
																$car = "checked";
															break;
															case "3":
																$train = "checked";
															break;
														}
													}
													else
													{
														$checked = "0";
														$value = 0;
														$display = "none";
													}
													?>
													<tr>
														<td class="key alignright" width="20%">
															<label for="nei_<?php echo $neighborhood->id?>"><?php echo Text::_($neighborhood->neighborhood)?></label>
														</td>
														<td width="5%">
															<input type="checkbox" value="<?php echo $value?>" name="nei_<?php echo $neighborhood->id?>" id="nei_<?php echo $neighborhood->id?>" <?php echo $checked?> onclick="javascript:showNeighborhood('<?php echo $neighborhood->id?>')" />
														</td>
														<td width="75%">
															<div id="div_nei_<?php echo $neighborhood->id?>" style="display:<?php echo $display?>;">
																<?php
																if($neighbor_value->distance > 0)
																{
																	$distance = OSPHelper::showBath($neighbor_value->distance);
																	$min	  = '';
																	$walk	  = "";
																	$car	  = "";
																	$train	  = "";
																}
																else
																{
																	$distance = '';
																	$min	  = $neighbor_value->mins;
																}
																?>
																<input type="text" name="distance_nei_<?php echo $neighborhood->id?>" size="10" value="<?php echo $distance;?>" class="input-small form-control" style="width:70px;" /> Km
																<?php echo Text::_('OS_OR'); ?>
																<input type="text" name="mins_nei_<?php echo $neighborhood->id?>" class="width40 <?php echo $inputMiniClass;?>" value="<?php echo $min;?>" /> <?php echo Text::_('OS_MINS')?> <?php echo Text::_('OS_BY')?>
																&nbsp;&nbsp;&nbsp;
																<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="1" <?php echo $walk?> /> <?php echo Text::_('OS_WALK')?>
																<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="2" <?php echo $car?> /> <?php echo Text::_('OS_CAR')?>
																<input type="radio" name="traffic_type_<?php echo $neighborhood->id?>" id="traffic_type_<?php echo $neighborhood->id?>" value="3" <?php echo $train?> /> <?php echo Text::_('OS_TRAIN')?>
															</div>
														</td>
													</tr>
													<?php
												}
												?>
											</table>
											<script type="text/javascript">
											function showNeighborhood(nid){
												var temp = document.getElementById('nei_' + nid);
												var div  = document.getElementById('div_nei_' + nid);
												if(temp.value == 0){
													div.style.display = "block";
													temp.value = 1;
												}else{
													div.style.display = "none";
													temp.value = 0;
												}
											}
											</script>
											<?php
											if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
												echo HTMLHelper::_('bootstrap.endSlide');
											}
											//echo $pane->endPanel();
										}
									}
									//echo $pane->endPane();
									if (version_compare(JVERSION, '4.0.0-dev', 'ge')){
										echo HTMLHelper::_('bootstrap.endAccordion');
									}else{
										echo HTMLHelper::_('sliders.end');	
									}
								?>
								</fieldset>
							</div>
						</div>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTab');
						?>
						<?php if($configClass['use_property_history'] == 1){?>
							
							<?php
							echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel5', Text::_('OS_HISTORY_TAX', true));
							?>
							<div class="tab-pane" id="addpropertypanel5">
								<div class="col width-100 nomargin nopadding">
									<fieldset id="history_tax" class="fieldsetpropertydetails">
										<legend><strong><i class="edicon edicon-coin-dollar"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_HISTORY_TAX' )); ?></strong></legend>
										<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
											<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
												<?php echo Text::_('OS_PROPERTY_HISTORY');?>
											</label>
											<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
												<table width="100%" id="property_history_table">
													<tr>
														<th>
															<?php echo Text::_('OS_DATE')?>
														</th>
														<th>
															<?php echo Text::_('OS_EVENT')?>
														</th>
														<th>
															<?php echo Text::_('OS_PRICE')?>
														</th>
														<th>
															<?php echo Text::_('OS_SOURCE')?>
														</th>
														<th>
															&nbsp;
														</th>
													</tr>
													<?php
													if(count((array)$lists['history']) > 0){
														foreach ($lists['history'] as $his){
															?>
															<tr id="history_table_tr">
																<td>
																	<input type="text" name="history_date[]" value="<?php echo $his->date?>" class="<?php echo $inputSmallClass; ?>" placeholder="0000-00-00" />
																</td>
																<td>
																	<input type="text" name="history_event[]" value="<?php echo $his->event?>" class="<?php echo $inputMediumClass; ?>" />
																</td>
																<td>
																	<input type="text" name="history_price[]" value="<?php echo $his->price?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="text" name="history_source[]" value="<?php echo $his->source?>" class="<?php echo $inputMediumClass; ?>" />
																</td>
																<td>
																	<input type="button" class="btn removehistory" value="<?php echo Text::_('OS_DELETE');?>" />
																</td>
															</tr>
														<?php
														}
													}
													?>
													<tr id="history_table_tr">
														<td>
															<input type="text" name="history_date[]" value="" class="<?php echo $inputSmallClass; ?>" placeholder="0000-00-00" />
														</td>
														<td>
															<input type="text" name="history_event[]" value="" class="<?php echo $inputMediumClass; ?>" />
														</td>
														<td>
															<input type="text" name="history_price[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="text" name="history_source[]" value="" class="<?php echo $inputMediumClass; ?>" />
														</td>
														<td>
															<input type="button" class="btn addhistory" value="<?php echo Text::_('OS_ADD');?>" />
														</td>
													</tr>
												</table>
											</div>
										</div>
										<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
											<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>">
												<?php echo Text::_('OS_PROPERTY_TAX');?>
											</label>
											<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
												<table width="100%" id="property_tax_table">
													<tr>
														<th>
															<?php echo Text::_('OS_YEAR')?>
														</th>
														<th>
															<?php echo Text::_('OS_TAX')?>
														</th>
														<th>
															<?php echo Text::_('OS_TAX_CHANGE')?>
														</th>
														<th>
															<?php echo Text::_('OS_TAX_ASSESSMENT')?>
														</th>
														<th>
															<?php echo Text::_('OS_TAX_ASSESSMENT_CHANGE')?>
														</th>
														<th>
															&nbsp;
														</th>
													</tr>
													<?php
													if(count((array)$lists['tax']) > 0){
														foreach ($lists['tax'] as $tax){
															?>
															<tr id="tax_table_tr">
																<td>
																	<input type="text" name="tax_year[]" value="<?php echo $tax->tax_year?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="text" name="tax_value[]" value="<?php echo $tax->property_tax?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="text" name="tax_change[]" value="<?php echo $tax->tax_change?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="text" name="tax_assessment[]" value="<?php echo $tax->tax_assessment?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="text" name="tax_assessment_change[]" value="<?php echo $tax->tax_assessment_change?>" class="<?php echo $inputSmallClass; ?>" />
																</td>
																<td>
																	<input type="button" class="btn removetax" value="<?php echo Text::_('OS_DELETE');?>" />
																</td>
															</tr>
														<?php
														}
													}
													?>
													<tr id="tax_table_tr">
														<td>
															<input type="text" name="tax_year[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="text" name="tax_value[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="text" name="tax_change[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="text" name="tax_assessment[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="text" name="tax_assessment_change[]" value="" class="<?php echo $inputSmallClass; ?>" />
														</td>
														<td>
															<input type="button" class="btn addtax" value="<?php echo Text::_('OS_ADD');?>" />
														</td>
													</tr>
												</table>
											</div>
										</div>
									</fieldset>
								</div>
							</div>
							<?php
							echo HTMLHelper::_($tabApiPrefix.'endTab');
							?>
						<?php } ?>		
						<?php
						echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel4', Text::_('OS_PHOTOS', true));
						?>
						<div class="tab-pane" id="addpropertypanel4">
							<div class="col width-100 nomargin nopadding">
								<!--
								<?php
								if($configClass['grabimages_frontend'] == 1){
								?>
									<fieldset id="grabphotos<?php echo $row->id; ?>" class="fieldsetpropertydetails">
										<legend><strong><i class="edicon edicon-folder-download"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_GRAB_IMAGES' )); ?></strong></legend>
										<div clas="row-fluid">
											<div class="span12">
												<?php echo Text::_('OS_GRAB_IMAGES_EXPLAIN');?>
												<Br /><Br />
												<div id="grab_images_raw">
													URL&nbsp;
													<span class="input-append">
													<input type="text" name="graburl" class="input-xxlarge" id="graburl" />
													<input type="button" value="<?php echo Text::_('OS_GRAB_IMAGES');?>" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" onClick="javascript:doGrabImage();"/>
													</span>
												</div>
											</div>
										</div>
									</fieldset>
									<BR />
								<?php
								}
								?>
								-->
								<fieldset id="photos<?php echo $row->id; ?>" class="fieldsetpropertydetails">
									<?php
									if($configClass['frontend_upload_type'] == 0)
									{
										?>
										<legend><strong><i class="edicon edicon-upload"></i>&nbsp;<?php echo strtoupper(Text::_( 'OS_AJAX_UPLOAD' )); ?></strong></legend>
										<div id="itemImagesWrap">
											<div id="itemImages">
												<?php  if(count((array)$row->photo) > 0){
													$photos = $row->photo;
													foreach($photos as $img) { ?>
														<div class="itemImage">
															<img src="<?php echo Uri::root().'images/osproperty/properties/'.$row->id.'/thumb/'.$img->image; ?>" alt="<?php echo $db->escape($img->image_desc); ?>" />
															<div class="imgMask">
																<input type="hidden" name="img_id[]" value="<?php echo $db->escape($img->id); ?>">
																<input type="hidden" name="img_image[]" value="">
																<input type="text" class="itemInput editTitle" name="img_caption[]" value="<?php echo $db->escape($img->image_desc); ?>">
																<span class="delBtn"></span>
															</div>
														</div>
													<?php }
												}  ?>
											</div>
											<div class="clearfix"></div>
										</div>
										<?php echo $lists['uploader'];?>
										<?php
									}else{
									?>
										<legend><strong><i class="edicon edicon-upload"></i>&nbsp;<?php echo strtoupper(Text::_('OS_MANUAL_UPLOAD')); ?></strong></legend>
										<?php 
										$total = ($configClass['limit_upload_photos'] > 0) ? $configClass['limit_upload_photos']:24;
										echo sprintf(Text::_('OS_PROPERTY_WILL_HAVE_PHOTOS'), $total); ?>
										<BR />
										<?php echo sprintf(Text::_('OS_ACCORDING_CONFIG_PHOTO_WILL_BE_RESIZED'), $configClass['images_thumbnail_width'], $configClass['images_thumbnail_height']); ?>
										<BR />
										<small><i>(<?php echo Text::_('OS_ONLY_SUPPORT_JPG_IMAGES');?>)</i></small>
										<BR /><BR />
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ">
                                                <?php
                                                $i = 0;
                                                if(count((array)$row->photo) > 0)
												{
                                                    $photos = $row->photo;
                                                    ?>
                                                    <input type="checkbox" name="selectall" id="selectall" onClick="javascript:checkall();" value="0" />
                                                    <strong><?php echo Text::_('JGLOBAL_CHECK_ALL');?></strong>
                                                    <BR /><BR />
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> photoobjects">
                                                        <?php
                                                        $j = 0;
                                                        $temp = array();
                                                        for($i=0;$i<count($photos);$i++)
														{
                                                            $j++;
                                                            $photo = $photos[$i];
                                                            $temp[] = $photo->id;
                                                            ?>
                                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?> marginbottom10 sortableitem padding5 margin0" data-state="<?php echo $i + 1;?>" data-value="<?php echo $photo->ordering;?>" style="background-color:#efefef;">
                                                                <div class="admin-photo">
                                                                    <div class="admin-photo-title"> <?php echo Text::_('OS_PHOTO');?> <?php echo $i + 1;?> </div>
                                                                    <?php
                                                                    if($photo->image != "")
																	{
                                                                        OSPHelper::showPropertyPhoto($photo->image,'medium',$row->id,'','','');
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <span id="photo_<?php echo $i+1?>div">
														        <input type="file" name="photo_<?php echo $i+1?>" id="photo_<?php echo $i+1?>" size="50" class="input-large form-control" onchange="javascript:check_file('photo_<?php echo $i+1?>')" />
													        </span>
                                                            <div class="clearfix"></div>
                                                            <strong><?php echo Text::_('OS_PHOTO_DESCRIPTION')?></strong>

                                                            <textarea class="inputbox" name="photodesc_<?php echo $i+1?>" id="photodesc_<?php echo $i+1?>" class="inputbox" cols="30" rows="3"><?php echo $photo->image_desc?></textarea>

                                                            <div class="clearfix"></div>
                                                            <strong><?php echo Text::_('OS_ORDERING')?></strong>
                                                            <input class="<?php echo $inputMiniClass; ?>" type="text" name="ordering_<?php echo $i+1?>" id="ordering_<?php echo $i+1?>" size="3" value="<?php echo $photo->ordering?>" />

                                                            <div class="clearfix"></div>
                                                            <strong><?php echo Text::_('OS_REMOVE')?></strong>
                                                            <input type="checkbox" name="remove_<?php echo $photo->id?>" id="remove_<?php echo $photo->id?>" value="0" onclick="javascript:changeValue('remove_<?php echo $photo->id?>')" />
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>

                                                <?php

                                                if(intval($row->id) > 0){
                                                    $j = $i;
                                                }else{
                                                    $j = 0;
                                                }
                                                if($j == 0){
                                                    ?>
                                                    <div class="manualuploadpicture" id="div_<?php echo $i?>">
                                                        <table class="admintable">
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_REMOVE')?> <?php echo $i + 1?>
                                                                </td>
                                                                <td>
															<span id="photo_<?php echo $i+1?>div">
															<input type="file" class="input-large form-control" name="photo_<?php echo $i+1?>" id="photo_<?php echo $i+1?>" size="50" onchange="javascript:check_file('photo_<?php echo $i+1?>')" />
															</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_PHOTO_DESCRIPTION')?>
                                                                </td>
                                                                <td>
                                                                    <textarea class="inputbox" name="photodesc_<?php echo $i+1?>" id="photodesc_<?php echo $i+1?>" class="inputbox" cols="40" rows="3"></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_ORDERING')?>
                                                                </td>
                                                                <td>
                                                                    <?php echo Text::_('OS_ORDERING_WILL_BE_INCREASED')?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <?php
                                                    $j++;
                                                }
                                                for($i=$j;$i<$total;$i++){
                                                    ?>
                                                    <div class="manualuploadpicture nodisplay" id="div_<?php echo $i?>">
                                                        <table class="admintable">
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_REMOVE')?> <?php echo $i + 1?>
                                                                </td>
                                                                <td>
															<span id="photo_<?php echo $i+1?>div">
															<input type="file" name="photo_<?php echo $i+1?>" id="photo_<?php echo $i+1?>" size="30" onchange="javascript:check_file('photo_<?php echo $i+1?>')" class="input-medium form-control" />
															</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_PHOTO_DESCRIPTION')?>
                                                                </td>
                                                                <td>
                                                                    <textarea class="inputbox" name="photodesc_<?php echo $i+1?>" id="photodesc_<?php echo $i+1?>" class="inputbox" cols="40" rows="3"></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="key">
                                                                    <?php echo Text::_('OS_ORDERING')?>
                                                                </td>
                                                                <td>
                                                                    <?php echo Text::_('OS_ORDERING_WILL_BE_INCREASED')?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

										<BR />
										<div id="newphoto<?php echo $id; ?>" class="button2-left displayblock">
											<div class="image">
												<a href="javascript:addPhoto()" class="btn btn-success"><i class="osicon-new"></i>&nbsp;<?php echo Text::_( 'OS_ADD_PHOTO' ); ?></a>
											</div>
										</div>
									<?php } ?>
								</fieldset>
							</div>
						</div>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTab');
						?>
						<?php
                        /*
						if(($configClass['active_payment'] == 1) and ($configClass['integrate_membership'] == 0) and ($show_payment_tab == 1)){
						?>	
						<?php
						echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertyinformation', 'addpropertypanel6', Text::_('OS_LIVE_TIME', true).' & '.Text::_('OS_PAYMENT', true));
						?>
						<div class="tab-pane <?php echo $tab2;?>" id="addpropertypanel6">
							<div class="col width-100 nomargin nopadding">
								<?php
								if(($configClass['general_use_expiration_management'] == 1) and ($isNew == 0)){
									$expiration = $row->expiration;
									?>
									 <table class="addproperty-membership-table" width="100%">
										 <tr>
											 <th width="50%" class="alignleft paddingleft15">
												 <?php echo Text::_('OS_EXPIRATION_INFORMATION');?>
											 </th>
											 <th width="50%" class="alignleft paddingleft15">
												 <?php echo Text::_('OS_PROPERTY_TYPE');?>
											 </th>
										 </tr>
										 <?php
										 if($row->isFeatured == 1){
										 ?>
										 <tr>
											 <td>
												 <?php echo Text::_('OS_FEATURED_UNTIL');?>
											 </td>
											 <td>
												 <?php
													$expiration_featured = strtotime($expiration->expired_feature_time);
													if($expiration_featured < time()){
														$style = "extend_alert";
													}else{
														$style = "";
													}
													echo "<span class='$style'>".date($configClass['general_date_format'],$expiration_featured)."</a>";
												 ?>
											 </td>
										 </tr>
										 <?php } 
										 if($expiration->expired_time != "0000-00-00 00:00:00"){
										 ?>
										 <tr>
											 <td>
												 <?php echo Text::_('OS_UNPUBLISHED_ON');?>
											 </td>
											 <td>
												 <?php
													$expiration_time = strtotime($expiration->expired_time);
													if($expiration_time < time()){
														$style = "extend_alert";
													}else{
														$style = "";
													}
													echo "<span class='$style'>".date($configClass['general_date_format'],$expiration_time)."</a>";
												 ?>
											 </td>
										 </tr>
										 <?php } 
										 if($expiration->remove_from_database != "0000-00-00 00:00:00"){
										 ?>
										 <tr>
											 <td>
												 <?php echo Text::_('OS_REMOVED_ON');?>
											 </td>
											 <td>
												 <?php
													$expiration_remove = strtotime($expiration->remove_from_database);
													echo "<span>".date($configClass['general_date_format'],$expiration_remove)."</a>";
												 ?>
											 </td>
										 </tr>
										 <?php } ?>
									 </table>
								<?php } ?>
								 <BR />
								 <table class="addproperty-membership-table" width="100%">
									 <tr>
										 <th width="15%">
											#
										 </th>
										 <th width="85%">
											 <?php echo Text::_('OS_PROPERTY_TYPE');?>
										 </th>
									 </tr>
									 <tr>
										 <td>
											 <input type="radio" name="isFeatured" id="isFeatured1" value="0" onClick="javascript:showPaymentForm(0);" checked /> &nbsp;<label for="isFeatured1"><?php echo Text::_('OS_STANDARD');?></label>
										 </td>
										 <td>
											 <span><?php echo Text::_('OS_STANDARD_EXPLANATION'); ?></span>
											 <div class="clearfix"></div>
											 <table class="addproperty-membership-subtable">
												 <tr>
													 <?php
													 if($configClass['general_use_expiration_management'] == 1){ ?>
													 <th width="50%">
														 <?php echo Text::_('OS_LIFE_TIME');?>
													 </th>
													 <?php } ?>
													 <th width="50%">
														 <?php echo Text::_('OS_PRICE');?>
													 </th>
												 </tr>
												 <tr>
													 <?php
													 if($configClass['general_use_expiration_management'] == 1){ ?>
													 <td>
														 <?php
														 echo $configClass['general_time_in_days'];
														 echo " ".Text::_('OS_DAYS');
														 ?>
													 </td>
													 <?php } ?>
													 <td width="50%">
														 <?php
														 if($configClass['normal_cost'] == "0"){
															 echo Text::_('OS_FREE');
														 }else{
															 echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency($configClass['general_currency_default']),$configClass['normal_cost']);
														 }
														 ?>
													 </td>
												 </tr>
											 </table>
										 </td>
									 </tr>
									 <tr>
										 <td>
											 <input type="radio" name="isFeatured" id="isFeatured2" onClick="javascript:showPaymentForm(1);" value="1" /> &nbsp;<label for="isFeatured2"><?php echo Text::_('OS_FEATURED');?></label>
										 </td>
										 <td>
											 <span><?php echo Text::_('OS_FEATURED_EXPLANATION'); ?></span>
											 <div class="clearfix"></div>
											 <table class="addproperty-membership-subtable">
												 <tr>
													 <?php
													 if($configClass['general_use_expiration_management'] == 1){ ?>
														 <th width="50%">
															 <?php echo Text::_('OS_LIFE_TIME');?>
														 </th>
													 <?php } ?>
													 <th width="50%">
														 <?php echo Text::_('OS_PRICE');?>
													 </th>
												 </tr>
												 <tr>
													 <?php
													 if($configClass['general_use_expiration_management'] == 1){ ?>
														 <td>
															 <?php
															 echo $configClass['general_time_in_days_featured'];
															 echo " ".Text::_('OS_DAYS');
															 ?>
														 </td>
													 <?php } ?>
													 <td width="50%">
														 <?php
														 if($configClass['general_featured_upgrade_amount'] == "0"){
															 echo Text::_('OS_FREE');
														 }else{
															 echo OSPHelper::generatePrice(HelperOspropertyCommon::loadCurrency($configClass['general_currency_default']),$configClass['general_featured_upgrade_amount']);
														 }
														 ?>
													 </td>
												 </tr>
											 </table>
										 </td>
									 </tr>
								 </table>
								 <?php
								 if(floatVal($configClass['normal_cost']) > 0){
									$display = "block";
								 }else{
									 $display = "none";
								 }
								 ?>
								 <div id="payment_list" style="display:<?php echo $display;?>;">
								 <?php
								 $methods = $lists['methods'];
								 if(count($methods) > 0){
								 ?>
									 <input type="hidden" name="nmethods" id="nmethods" value="<?php echo count($methods)?>" />
									 <table class="addproperty-membership-payments-table" width="100%">
										 <tr>
											 <th width="5%">
												#
											 </th>
											 <th width="20%">
												 <?php echo Text::_('OS_PAYMENT_NAME');?>
											 </th>
											 <th width="55%">
												 <?php echo Text::_('OS_PAYMENT_DESC');?>
											 </th>
											 <th width="20%" class="<?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
											 </th>
										 </tr>
										 <?php
											$method = null ;
											for ($i = 0 , $n = count($methods); $i < $n; $i++) {
												$paymentMethod = $methods[$i];
												if ($paymentMethod->getName() == $lists['paymentMethod']) {
													$checked = ' checked="checked" ';
													$method = $paymentMethod ;
												}										
												else 
													$checked = '';	
												?>
												<tr>
													<td class="center">
														<input onclick="javascript:changePaymentMethod();" type="radio" name="payment_method" id="pmt<?php echo $i?>" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> />
													</td>
													<td>
														<label for="pmt<?php echo $i?>"><?php echo Text::_($paymentMethod->title) ; ?></label>
													</td>
													<td>
														<?php echo $paymentMethod->description ; ?>
													</td>
													<td class="<?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?> center">
														<?php
														if(file_exists(JPATH_ROOT.'/images/osproperty/plugins/'.$paymentMethod->pure_name.'.png')){
															?>
															<img src="<?php echo Uri::root().'images/osproperty/plugins/'.$paymentMethod->pure_name.'.png'?>"  width="110" />
															<?php
														}
														?>
													</td>
												</tr>
												
												<?php		
											}
										 ?>
									  </table>
								<?php
								} else {
									$method = $methods[0] ;
								}		
								
								if ($method->getCreditCard()) {
									$style = '' ;	
								} else {
									$style = 'style = "display:none"';
								}			
								?>
								<table class="addproperty-membership-credit-table">
									<tr id="tr_card_head">
										<th colspan=2>
											<?php echo Text::_('OS_CREDIT_CARD_INFORMATION'); ?>
										</th>
									</tr>
									<tr id="tr_card_number" <?php echo $style; ?>>
										<td class="infor_left_col"><?php echo  Text::_('OS_AUTH_CARD_NUMBER'); ?><span class="required">*</span></td>
										<td class="infor_right_col">
											<input type="text" name="x_card_num" id="x_card_num" class="<?php echo $inputMediumClass; ?>" onkeyup="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $x_card_num; ?>" size="20" />
										</td>					
									</tr>
									<tr id="tr_exp_date" <?php echo $style; ?>>
										<td class="infor_left_col">
											<?php echo Text::_('OS_AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
										</td>
										<td class="infor_right_col">	
											<?php echo $lists['exp_month'] .'  /  '.$lists['exp_year'] ; ?>
										</td>					
									</tr>
									<tr id="tr_cvv_code" <?php echo $style; ?>>
										<td class="infor_left_col">
											<?php echo Text::_('OS_AUTH_CVV_CODE'); ?><span class="required">*</span>
										</td>
										<td class="infor_right_col">
											<input type="text" name="x_card_code" id="x_card_code" class="<?php echo $inputMediumClass; ?>" onKeyUp="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>')" value="<?php echo $x_card_code; ?>" size="20" />
										</td>					
									</tr>
									<?php
										if ($method->getCardType()) {
											$style = '' ;
										} else {
											$style = ' style = "display:none;" ' ;										
										}
									?>
										<tr id="tr_card_type" <?php echo $style; ?>>
											<td class="infor_left_col">
												<?php echo Text::_('OS_CARD_TYPE'); ?><span class="required">*</span>
											</td>
											<td class="infor_right_col">
												<?php echo $lists['card_type'] ; ?>
											</td>						
										</tr>					
									<?php
										if ($method->getCardHolderName()) {
											$style = '' ;
										} else {
											$style = ' style = "display:none;" ' ;										
										}
									?>
										<tr id="tr_card_holder_name" <?php echo $style; ?>>
											<td class="infor_left_col">
												<?php echo Text::_('OS_CARD_HOLDER_NAME'); ?><span class="required">*</span>
											</td>
											<td class="infor_right_col">
												<input type="text" name="card_holder_name" id="card_holder_name" class="<?php echo $inputMediumClass; ?>"  value="<?php echo $cardHolderName; ?>" size="40" />
											</td>						
										</tr>
									<?php									
										if ($method->getName() == 'os_echeck') {
											$style = '';												
										} else {
											$style = ' style = "display:none;" ' ;
										}
									?>
									
										<tr id="tr_bank_rounting_number" <?php echo $style; ?>>
											<td class="infor_left_col"  class="infor_left_col"><?php echo Text::_('OSM_BANK_ROUTING_NUMBER'); ?><span class="required">*</span></td>
											<td class="infor_right_col"><input type="text" name="x_bank_aba_code" class="<?php echo $inputMediumClass; ?>"  value="<?php echo $x_bank_aba_code; ?>" size="40" onKeyUp="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>');" /></td>
										</tr>
										<tr id="tr_bank_account_number" <?php echo $style; ?>>
											<td class="infor_left_col" class="infor_left_col"><?php echo Text::_('OSM_BANK_ACCOUNT_NUMBER'); ?><span class="required">*</span></td>
											<td class="infor_right_col"><input type="text" name="x_bank_acct_num" class="<?php echo $inputMediumClass; ?>"  value="<?php echo $x_bank_acct_num; ?>" size="40" onKeyUp="checkNumber(this,'<?php echo Text::_('OS_ONLY_NUMBER'); ?>');" /></td>
										</tr>
										<tr id="tr_bank_account_type" <?php echo $style; ?>>
											<td class="infor_left_col"  class="infor_left_col"><?php echo Text::_('OSM_BANK_ACCOUNT_TYPE'); ?><span class="required">*</span></td>
											<td class="infor_right_col"><?php echo $lists['x_bank_acct_type']; ?></td>
										</tr>
										<tr id="tr_bank_name" <?php echo $style; ?>>
											<td class="infor_left_col" class="infor_left_col"><?php echo Text::_('OSM_BANK_NAME'); ?><span class="required">*</span></td>
											<td class="infor_right_col"><input type="text" name="x_bank_name" class="<?php echo $inputMediumClass; ?>"  value="<?php echo $x_bank_name; ?>" size="40" /></td>
										</tr>
										<tr id="tr_bank_account_holder" <?php echo $style; ?>>
											<td class="infor_left_col" class="infor_left_col"><?php echo Text::_('OSM_ACCOUNT_HOLDER_NAME'); ?><span class="required">*</span></td>
											<td class="infor_right_col"><input type="text" name="x_bank_acct_name" class="<?php echo $inputMediumClass; ?>"  value="<?php echo $x_bank_acct_name; ?>" size="40" /></td>
										</tr>	
									
								</table>
								</div>
							</div>
						</div>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTab');
						?>
						<?php }
                        **/
                        ?>
						<?php
						echo HTMLHelper::_($tabApiPrefix.'endTabSet');
						?>
					</div>
				</div>
				<script type="text/javascript">
				<?php
					os_payments::writeJavascriptObjects();
				?>
				function showPaymentForm(value){
					if(value == 0){
						<?php
						if(floatVal($configClass['normal_cost']) > 0){
						?>
							document.getElementById('payment_list').style.display = "block";
						<?php } else { ?>
							document.getElementById('payment_list').style.display = "none";
						<?php } ?>
					}else if(value == 1){
						<?php
						if(floatVal($configClass['general_featured_upgrade_amount']) > 0){
						?>
							document.getElementById('payment_list').style.display = "block";
						<?php } else { ?>
							document.getElementById('payment_list').style.display = "none";
						<?php } ?>
					}
				}

				jQuery(document).ready(function(){		
					jQuery('#property_history_table').on('click', '.removehistory', function(){
						jQuery(this).parent().parent().remove();
					});
					
					jQuery('#property_history_table').on('click', '.addhistory', function(){
						jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
						jQuery(this).attr('class','btn removehistory');
						var appendTxt = '<tr id="history_table_tr"><td><input type="text" name="history_date[]" value="" placeholder="0000-00-00" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="history_event[]" value="" class="<?php echo $inputMediumClass; ?>" /></td><td><input type="text" name="history_price[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="history_source[]" value="" class="<?php echo $inputMediumClass; ?>" /></td><td><input type="button" class="btn addhistory" value="<?php echo Text::_('OS_ADD');?>" /></td></tr>';
						jQuery("#property_history_table>tbody>tr:last").after(appendTxt);			
					});  

					jQuery('#property_tax_table').on('click', '.removetax', function(){
						jQuery(this).parent().parent().remove();
					});    

					jQuery('#property_tax_table').on('click', '.addtax', function(){
						jQuery(this).val('<?php echo Text::_('OS_DELETE');?>');
						jQuery(this).attr('class','btn removetax');
						var appendTxt = '<tr id="tax_table_tr"><td><input type="text" name="tax_year[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="tax_value[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="tax_change[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="tax_assessment[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="text" name="tax_assessment_change[]" value="" class="<?php echo $inputSmallClass; ?>" /></td><td><input type="button" class="btn addtax" value="<?php echo Text::_('OS_ADD');?>" /></td></tr>';
						jQuery("#property_tax_table>tbody>tr:last").after(appendTxt);			
					}); 
				});
				
				
				</script>
				<?php 
				if ($translatable)
				{
					echo HTMLHelper::_($tabApiPrefix.'endTab');
					echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertytranslation', 'translation-page', Text::_('OS_TRANSLATION', true));
				?>
					<div class="tab-pane" id="translation-page">	
						<div class="tab-content">
							
							<?php
							echo HTMLHelper::_($tabApiPrefix.'startTabSet', 'propertylanguageelements', array('active' => 'translation-page-'.$sef));
								$i = 0;
								foreach ($languages as $language)
								{												
									$sef = $language->sef;
									echo HTMLHelper::_($tabApiPrefix.'addTab', 'propertylanguageelements', 'translation-page-'.$sef, $language->title. ' '. ' <img src="'.Uri::root().'media/com_osproperty/flags/'.$sef.'.png" /> ');
								?>
									<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
										<table width="100%" class="admintable backgroundwhite">
											<tr>
												<td class="key">
													<?php echo Text::_('OS_PROPERTY_TITLE')?>
												</td>
												<td>
													<input type="text" name="pro_name_<?php echo $sef;?>" id="pro_name_<?php echo $sef;?>" value="<?php echo $row->{'pro_name_'.$sef}?>" size="50" class="<?php echo $inputLargeClass; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_ALIAS')?>
												</td>
												<td>
													<input type="text" name="pro_alias_<?php echo $sef;?>" id="pro_alias_<?php echo $sef;?>" value="<?php echo $row->{'pro_alias_'.$sef}?>" size="50" class="<?php echo $inputLargeClass; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key">
													<?php echo Text::_('OS_ADDRESS')?>
												</td>
												<td>
													<input type="text" name="address_<?php echo $sef;?>" id="address_<?php echo $sef;?>" value="<?php echo $row->{'address_'.$sef}?>" size="50" class="<?php echo $inputLargeClass; ?>" />
												</td>
											</tr>
											<tr>
												<td class="key" valign="top">
													<?php echo Text::_('OS_SMALL_DESCRIPTION')?>
												</td>
												<td>
													<textarea  name="pro_small_desc_<?php echo $sef;?>" id="pro_small_desc_<?php echo $sef;?>" cols="50" rows="5" class="<?php echo $inputLargeClass; ?>"><?php echo stripslashes($row->{'pro_small_desc_'.$sef})?></textarea>
												</td>
											</tr>
											<tr>
												<td class="key" valign="top">
													<?php echo Text::_('OS_FULL_DESCRIPTION')?>
												</td>
												<td>
													<?php
													echo $editor->display( 'pro_full_desc_'.$sef,  stripslashes($row->{'pro_full_desc_'.$sef}) , '95%', '250', '75', '20',false ) ;
													?>
												</td>
											</tr>
										</table>
									</div>										
								<?php
									echo HTMLHelper::_($tabApiPrefix.'endTab');
									$i++;		
								}
								echo HTMLHelper::_($tabApiPrefix.'endTabSet');
							?>
						</div>	
					</div>
				<?php
					echo HTMLHelper::_($tabApiPrefix.'endTab');
					echo HTMLHelper::_($tabApiPrefix.'endTabSet');
				}
				?>
			</div>
		</div>
	</div>
</div>
<?php
if(intval($row->id) == 0){
	$j = 0;
}else{
	$j = count($row->photo) - 1;
}
?>
<input type="hidden" name="current_number_photo" id="current_number_photo" value="<?php echo $j?>" />
<input type="hidden" name="newphoto" id="newphoto" value="<?php echo count((array)$row->photo)?>" />
<input type="hidden" name="option" value="com_osproperty" />
<input type="hidden" name="task" value="property_save" />
<input type="hidden" name="id" value="<?php echo $row->id?>" />
<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
<input type="hidden" name="require_field" id="require_field" value="<?php echo substr($require_field,0,strlen($require_field)-1)?>" />
<input type="hidden" name="require_label" id="require_label" value="<?php echo substr($require_label,0,strlen($require_label)-1)?>" />
<input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
<input type="hidden" name="extend" id="extend" value="<?php echo $extend; ?>" />
<input type="hidden" name="photo_ids" id="photo_ids" value="<?php echo implode(",",(array)$temp);?>" />
<input type="hidden" name="hits" id="hits" value="<?php echo (int)$row->hits; ?>" />
<?php

if($row->id > 0){
	?>
	<input type="hidden" name="company_id" value="<?php echo $row->company_id; ?>" />
	<?php
}


if(count($lists['types']) > 0){
	foreach ($lists['types'] as $type){
		?>
		<input type="hidden" name="type_id_<?php echo $type->id?>" id="type_id_<?php echo $type->id?>" value="<?php echo implode(",",$type->fields);?>"/>
		<input type="hidden" name="type_id_<?php echo $type->id?>_required" id="type_id_<?php echo $type->id?>_required" value="<?php echo implode(",",$type->required_fields);?>"/>
		<input type="hidden" name="type_id_<?php echo $type->id?>_required_name" id="type_id_<?php echo $type->id?>_required_name" value="<?php echo implode(",",$type->required_fields_name);?>"/>
		<input type="hidden" name="type_id_<?php echo $type->id?>_required_title" id="type_id_<?php echo $type->id?>_required_title" value="<?php echo implode(",",$type->required_fields_label);?>"/>
		<?php 
	}
}
?>
<input type="hidden" name="field_ids" id="field_ids" value="<?php echo implode(",",$fieldLists)?>" />
<?php 
if($configClass['use_sold'] == 1){
	?>
	<input type="hidden" name="sold_property_types" id="sold_property_types" value="<?php echo $configClass['sold_property_types']?>" />
	<?php 
}
?>
</form>
<script type="text/javascript">
function doGrabImage(){
	var graburl = document.getElementById('graburl');
	if(graburl.value != ""){
		if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(graburl.value)){
			//call ajax script
			var live_site = "<?php echo Uri::root();?>";
			doGrabImageAjax(live_site,graburl.value);
		}else{
			alert("<?php echo Text::_('OS_PLEASE_ENTER_CORRECT_URL');?>");
		}
	}
}
<?php
if($configClass['map_type'] == 0)
{
?>
jQuery( document ).ready(function() {
    initialize();
});
<?php
}	
?>
jQuery("#pro_type").change(function(){
	var fields = jQuery("#field_ids").val();
	var fieldArr = fields.split(",");
	if(fieldArr.length > 0){
		for(i=0;i<fieldArr.length;i++){
			jQuery("#extrafield_" + fieldArr[i]).hide("fast");
		}
	}
	var selected_value = jQuery("#pro_type").val();
	if(parseInt(selected_value) > 0){
		jQuery("#fieldgroups_div").css("display","block");
	}else{
		jQuery("#fieldgroups_div").css("display","none");
	}
	var selected_fields = jQuery("#type_id_" + selected_value).val();
	var fieldArr = selected_fields.split(",");
	if(fieldArr.length > 0){
		for(i=0;i<fieldArr.length;i++){
			jQuery("#extrafield_" + fieldArr[i]).show("slow");
		}
	}
	<?php 
	if($configClass['use_sold'] == 1){
		?>
		var selected_fields = jQuery("#sold_property_types").val();
		if(selected_fields != null){
			var fieldArr = selected_fields.split("|");
			if(fieldArr.length > 0){
				var show = 0;
				for(i=0;i<fieldArr.length;i++){
					if(fieldArr[i] == selected_value)
					{
						show = 1;
					}
				}
				if(show == 1){
					jQuery("#sold_information").show("slow");
				}else{
					jQuery("#sold_information").hide("slow");
				}
			}
		}
		<?php 
	}
	?>
});
function checkall(){
    var selectall = jQuery("#selectall").val();
    var photo_ids = jQuery("#photo_ids").val();
    if(selectall == 0){
        jQuery("#selectall").val("1");
        if(photo_ids != ""){
            var photo_ids_array = photo_ids.split(",");
            for(var i=0;i<photo_ids_array.length;i++){
                jQuery("#remove_" + photo_ids_array[i]).val("1");
                jQuery("#remove_" + photo_ids_array[i]).prop('checked',true);
            }
        }
    }else{
        jQuery("#selectall").val("0");
        if(photo_ids != ""){
            var photo_ids_array = photo_ids.split(",");
            for(var i=0;i<photo_ids_array.length;i++){
                jQuery("#remove_" + photo_ids_array[i]).val("0");
                jQuery("#remove_" + photo_ids_array[i]).prop('checked',false);
            }
        }
    }
}
jQuery( ".photoobjects" ).sortable({
    delay: 150,
    stop: function() {
        var selectedData = new Array();
        var idData = new Array();
        var orderignData = new Array();
        jQuery('.photoobjects .sortableitem').each(function() {
            selectedData.push(jQuery(this).attr("id"));
            idData.push(jQuery(this).attr("data-state"));
            orderignData.push(jQuery(this).attr("data-value"));
        });
        var temp;
        for(i=0;i<selectedData.length;i++){
            temp = idData[i];
            jQuery("#ordering_" + temp).val(i+1);
        }
    }

});
</script>
