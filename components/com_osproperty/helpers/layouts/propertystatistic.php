<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
?>
<div class="statistic-page">
	<div class="property-info" >
		<?php
		if($row->photo != "")
		{
			if(File::exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$row->photo->image)){
				$src = Uri::root().'images/osproperty/properties/'.$row->id.'/medium/'.$row->photo->image;
			}else{
				$src = Uri::root().'media/com_osproperty/assets/images/nopropertyphoto.png';
			}
		}else{
			$src=Uri::root().'media/com_osproperty/assets/images/nopropertyphoto.png';
		}
		?>
		<div class="statistic-header" style="background-image: url('<?php echo $src; ?>');">
			<div class="property-data">
				<div class="pro-name">
					<?php
					if($row->pro_name != ""){
						echo $row->pro_name;
					}
					if(($row->ref != "") and ($configClass['show_ref'] == 1)){?>#<?php echo $row->ref?>,&nbsp;
					<?php
					}
					if($row->show_address == 1){?>
						<div class="address_details">
							<?php echo OSPHelper::generateAddress($row);?>
						</div>
					<?php }?>
				</div>
				<div class="clearfix height25"></div>
				<div class="information-pro">
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?> price-property border">
							<?php 
							if($row->price_text != "")
							{
								echo " ".OSPHelper::showPriceText(Text::_($row->price_text));
							}
							elseif($row->price_call == 0)
							{
								if($row->price > 0){
									echo OSPHelper::generatePrice($row->curr,$row->price);
									if($row->rent_time != ""){
										?>
										/ <span id="mthpayment"><?php echo Text::_($row->rent_time);?></span>
									<?php
									}
								}
							}elseif($row->price_call==0){
								?>
								<span class='fontsize16'><?php echo Text::_('OS_CALL_FOR_DETAILS_PRICE') ?></span>
							<?php
							} ?>
							<p class="property-type">
								<img src="<?php echo $row->type_icon;?>" />&nbsp;&nbsp;<?php echo $row->pro_type?>
							</p>
						</div>
						<?php
						if($configClass['use_bedrooms'] == 1){
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('span2'); ?> border">
							<div class="pro-numberbed">
								<p>
									<?php echo $row->bed_room; ?>
								</p>
								<p>
									<?php echo Text::_("OS_BED"); ?>
								</p>
							</div>
						</div>
						<?php } ?>
						<?php
						if($configClass['use_bathrooms'] == 1){
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('span2'); ?> border">
							<div class="pro-numberbed">
								<p>
									<?php echo OSPHelper::showBath($row->bath_room);?>
								</p>
								<p>
									<?php echo Text::_("OS_BATH"); ?>
								</p>
							</div>
						</div>
						<?php } ?>
						<?php
						if($configClass['use_squarefeet'] == 1){
						?>
						<div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?>">
							<div class="pro-numberbed">
								<p>
									<?php echo OSPHelper::showSquare($row->square_feet);?>
								</p>
								<p>
									<?php echo OSPHelper::showSquareSymbol(); // Text::_('OS_SQUARE_FEET')?>
								</p>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
    <div class="clearfix height20"></div>
	<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
		<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
            <?php
            $allowedit = 0;
            if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
                $allowedit = 1;
            }
            if((HelperOspropertyCommon::isCompanyAdmin()) and ($configClass['company_admin_add_properties'] == 1)){
                $allowedit = 1;
            }
            if((HelperOspropertyCommon::isOwner($row->id)) || (HelperOspropertyCommon::isCompanyOwner($row->id))){
                $allowedit = 1;
            }
            if(($configClass['general_agent_listings'] == 1) and (HelperOspropertyCommon::isAgent())){
                $allowedit = 1;
            }
            if($allowedit == 1)
			{
            
                $link = Route::_('index.php?option=com_osproperty&task=property_edit&id='.$row->id.'&Itemid='.Factory::getApplication()->input->getInt('Itemid'));
                ?>
                <i class="osicon-edit"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo $row->pro_name; ?>" title="<?php echo Text::_("OS_EDIT_LISTING"); ?>: <?php echo $row->pro_name ?>">
                    <?php echo Text::_("OS_EDIT_LISTING"); ?>
                </a>
                &nbsp;&nbsp;
            <?php
            }
            if(HelperOspropertyCommon::isAgent()){
            ?>
                <?php
                $needs[] = "aeditdetails";
                $needs[] = "agent_default";
                $needs[] = "agent_editprofile";
                $itemid = OSPRoute::getItemid($needs);
                $link = Route::_('index.php?option=com_osproperty&task=agent_default&Itemid='.$itemid);
                ?>
                <i class="osicon-list"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>" title="<?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>">
                    <?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>
                </a>
                &nbsp;&nbsp;
            <?php } ?>
            <?php
            if(HelperOspropertyCommon::isCompanyAdmin()){
            ?>
                <?php
                $needs = array();
                $needs[] = "ccompanydetails";
                $needs[] = "company_edit";
                $itemid  = OSPRoute::getItemid($needs);
                $link = Route::_('index.php?option=com_osproperty&task=company_edit&Itemid='.$itemid);
                ?>
                <i class="osicon-list"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo Text::_("OS_MANAGE_PROFILE"); ?>" title="<?php echo Text::_("OS_MANAGE_PROFILE"); ?>">
                    <?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>
                </a>
                &nbsp;&nbsp;
            <?php } ?>
            <?php
            if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
            ?>
                <?php
                $needs = array();
                $needs[] = "lmanageallproperties";
                $needs[] = "property_manageallproperties";
                $itemid  = OSPRoute::getItemid($needs);
                $link = Route::_('index.php?option=com_osproperty&view=lmanageallproperties&Itemid='.$itemid);
                ?>
                <i class="osicon-list"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>" title="<?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>">
                    <?php echo Text::_("OS_MANAGE_PROPERTIES"); ?>
                </a>
                &nbsp;&nbsp;
            <?php } ?>
            <?php
            if(!OSPHelper::isApprovedProperty($row->id)){
                $link = Route::_('index.php?option=com_osproperty&task=property_edit_activelisting&id='.$row->id);
                ?>
                <i class="osicon-database"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo Text::_("OS_REQUEST_APPROVAL"); ?>" title="<?php echo Text::_("OS_REQUEST_APPROVAL"); ?>">
                    <?php echo Text::_("OS_REQUEST_APPROVAL"); ?>
                </a>
                &nbsp;&nbsp;
                <?php
            }
            ?>
            <?php
            if($row->isFeatured == 0){
                $link = Route::_('index.php?option=com_osproperty&task=property_upgrade&cid[]='.$row->id);
                ?>
                <i class="osicon-upload"></i>
                &nbsp;
                <a href="<?php echo $link ?>" alt="<?php echo Text::_("OS_UPGRADE_FEATURE"); ?>" title="<?php echo Text::_("OS_UPGRADE_FEATURE"); ?>">
                    <?php echo Text::_("OS_UPGRADE_FEATURE"); ?>
                </a>
                &nbsp;&nbsp;
                <?php
            }
            ?>
		</div>
	</div>
</div>

<div class="clearfix"></div>

<div class="pro-graphical">
    <h2>
		<?php echo Text::_("OS_LATEST_ACTIVITY"); ?>
    </h2>
        <div class="pro-canvas">
            <canvas class="canvas" id="canvas" height="250"></canvas>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> hit">
                <?php echo Text::_('OS_TOTAL_HITS') ?>
                <p>
                    <?php if($row->hits == ""){
                        echo 0;
                    }else{
                        echo $row->hits;
                    } ?>
                </p>
            </div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> view">
                <?php echo Text::_('OS_SAVED') ?>
                <p>
                    <?php if($row->hits == ""){
                        echo 0;
                    }else{
                        echo $row->saved;
                    } ?>
                </p>
            </div>
        </div>
	</div>
	<!--
    <div class="clearfix height25"></div>
    <div class="export_hits">
        <div class="ranking-top">
            <h2>
                <?php echo Text::_("OS_EXPORT_HIT_STATISTIC"); ?>
            </h2>

        </div>
    </div>
	-->
    <div class="clearfix height25"></div>
    <div class="ranking-property">
        <div class="ranking-top">
          <h2>
              <?php echo Text::_('OS_TOP_HITS_LISTING');?>
          </h2>
        </div>
        <?php
		echo $row->relate_properties;
		?>
    </div>

	<script type="text/javascript">
	jQuery(document).ready(function(){
		var data = {
			labels: ["","<?php echo strtoupper(Text::_('OS_HITS'));?>", "<?php echo strtoupper(Text::_('OS_SAVED'));?>",""],
			datasets: [
				{
					backgroundColor: [
						'rgb(250, 250, 250)',
						'rgb(192, 224, 251)',
						'rgb(79, 202, 0)',
						'rgb(250, 250, 250)',
					],
					borderWidth: 0,
					data: [0, <?php echo $row->hits; ?>, <?php echo $row->saved; ?>, 0],
				}
			]
		};
		var options = {
			responsive:true,
			scaleBeginAtZero:false,
			barBeginAtOrigin:true
		};
		var myPieChart = new Chart(document.getElementById("canvas").getContext("2d")).Bar(data,options);
	});
	</script>
</div>