<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowfluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/theme3/style/font.css");
?>

<div class="<?php echo $rowfluidClass; ?>" id="agentslisting">
    <div class="<?php echo $span12Class; ?>">
		<div class="<?php echo $rowfluidClass; ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> noleftmargin alignleft"> 
				<strong><?php echo Text::_('OS_FILTER');?>:</strong>
				<input type="text" class="input-large search-query form-control" name="keyword" id="keyword" value="<?php echo OSPHelper::getStringRequest('keyword','')?>" />					
				<input type="submit" value="<?php echo Text::_('OS_SUBMIT')?>" class="btn btn-info" />
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> pull-right alignright">
				<?php
				if($ordertype == "asc"){
					$class1 = "btn btn-info";
					$class2 = "btn btn-warning";
				}else{
					$class2 = "btn btn-info";
					$class1 = "btn btn-warning";
				}
				?>
				<strong><?php echo Text::_('OS_SORT_BY')?>: </strong>
				<a href="javascript:updateOrderType('asc');" class="<?php echo $class1;?>" title="<?php echo Text::_('OS_ASC')?>">
					<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/order_down.png" />
				</a>
				<a href="javascript:updateOrderType('desc');" class="<?php echo $class2;?>" title="<?php echo Text::_('OS_DESC')?>">
					<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/order_up.png" />
				</a>
				<input type="hidden" name="default_orderby" id="ordertype" value="<?php echo $ordertype?>" />
			</div>
		</div>
		<HR />
        <?php
        for($i=0;$i<count($rows);$i++){
            $row = $rows[$i];
            ?>
            <div class="<?php echo $rowfluidClass; ?> ospitem-separator">
                <div class="<?php echo $span12Class; ?>">
                    <div class="<?php echo $rowfluidClass; ?>">
                        <div class="<?php echo $bootstrapHelper->getClassMapping('span3'); ?> agentphotobox">
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?> agentphotobox1">
                                    <?php
                                    if($configClass['show_agent_image'] == 1)
                                    {
                                        ?>
                                        <?php
                                        if(($row->photo != "") && (file_exists(JPATH_ROOT."/images/osproperty/agent/".$row->photo)))
                                        {
                                            if($configClass['load_lazy'])
                                            {
                                                ?>
                                                <img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/loader.gif' border="0" data-original='<?php echo Uri::root()?>images/osproperty/agent/<?php echo $row->photo?>' title='<?php echo $row->name?>' class='oslazy'/>
                                                <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <img src='<?php echo Uri::root()?>images/osproperty/agent/<?php echo $row->photo?>' border="0" title='<?php echo $row->name?>'/>
                                            <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.jpg' border="0" title='<?php echo $row->name?>'/>
                                            <?php
                                        }
                                        
                                        if($row->featured == 1)
                                        {
                                        ?>
                                            <img alt="<?php echo Text::_('OS_FEATURED');?>" class="spotlight_watermark" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/featured_medium.png" />
                                         <?php
                                        }

										?>
										<span class="agentType">
											<?php
											echo OSPHelper::loadAgentType($row->id);
											?>
										</span>
										<?php
                                        
                                    }
                                    else
                                    {
                                        ?>
                                        <img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.jpg' border="0" title='<?php echo $row->name?>'/>
                                        <?php
										if($row->featured == 1)
                                        {
                                        ?>
                                            <img alt="<?php echo Text::_('OS_FEATURED');?>" class="spotlight_watermark" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/featured_medium.png" />
                                         <?php
                                        }

										?>
										<span class="agentType">
											<?php
											echo OSPHelper::loadAgentType($row->id);
											?>
										</span>
										<?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?> agentdetailslink">
                                    <a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$row->id.'&Itemid='.OSPRoute::getAgentItemid($row->id));?>" class="agentdetailsbtn" title="<?php echo Text::_('OS_VIEW_DETAILS');?>"><?php echo Text::_('OS_VIEW_DETAILS');?></a>
                                </div>
                            </div>
                        </div>
						<?php
						if(!OSPHelper::isJoomla4())
						{
							$extraClass = "ospitem-leftpad";
						}
						else
						{
							$extraClass = "";
						}
						?>
                        <div class="<?php echo $bootstrapHelper->getClassMapping('span9'); ?> <?php echo $extraClass; ?>">
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?> agenttitle">
                                    <h3 class="agency-name">
                                        <?php echo $row->name?>
                                    </h3>
                                </div>
                            </div>
                            <?php
                            if($configClass['show_agent_address']==1)
                            {
                                ?>
                                <div class="<?php echo $rowfluidClass; ?>">
                                    <div class="<?php echo $span12Class; ?> agentaddress">
                                        <?php
                                        echo "<i class='fa fa-map-marker'></i> ";
                                        echo OSPHelper::generateAddress($row);
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        <?php
                        if($configClass['show_agent_email'] == 1 && $row->email != '')
                        {
                        ?>
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
									  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
									</svg>
                                    <?php
                                    //echo "<span class='agent_label'>".Text::_('OS_EMAIL')."</span>";
                                    //echo ":&nbsp;";
                                    echo "&nbsp;";
                                    echo "<a href='mailto:$row->email'>$row->email</a>";
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        if($row->phone != "" && $configClass['show_agent_phone'] == 1)
                        {
                        ?>
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
									  <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
									</svg>
                                    <?php
                                    //echo "<span class='agent_label'>".Text::_('OS_PHONE')."</span>";
                                    //echo ":&nbsp;";
                                    echo "&nbsp;";
                                    echo $row->phone;
                                    ?>
                                </div>
                            </div>
                            <?php
                        }

                        if($row->mobile != "" && $configClass['show_agent_mobile'] == 1)
                        {
                        ?>
                            <div class="<?php echo $rowfluidClass; ?>">
                                <div class="<?php echo $span12Class; ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone-fill" viewBox="0 0 16 16">
									  <path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/>
									</svg>
                                    <?php
                                    //echo "<span class='agent_label'>".Text::_('OS_MOBILE')."</span>";
                                    //echo ":&nbsp;";
                                    echo "&nbsp;";
                                    echo $row->mobile;
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        $bio = OSPHelper::getLanguageFieldValue($row,'bio');
                        if($bio != ""){
                            $bio =  stripslashes(strip_tags($bio));
                            $bioArr = explode(" ",$bio);
                            if(count($bioArr) > 20){
                                $tempBio = "";
                                for($b=0;$b<=20;$b++){
                                    $tempBio.= $bioArr[$b]." ";
                                }
                                echo substr($tempBio,0,strlen($tempBio) - 1)."..";
                            }else{
                                echo $bio;
                            }
                        ?>
                        <?php
                        }
                        ?>
                        <div class="<?php echo $rowfluidClass; ?>">
                            <div class="<?php echo $span12Class; ?>">
                                <ul class="social marT15 marL0">
                                    <?php
                                    if(($row->facebook != "") and ($configClass['show_agent_facebook'] == 1)){
                                        ?>
                                        <li class="facebook">
                                            <a href="<?php echo $row->facebook; ?>" target="_blank">
                                                <i class="edicon edicon-facebook"></i>
                                            </a>
                                        </li>
                                    <?php }
                                    if(($row->aim != "") and ($configClass['show_agent_twitter'] == 1)){
                                        ?>
                                        <li class="twitter">
                                            <a href="<?php echo $row->aim; ?>" target="_blank">
                                                <i class="edicon edicon-twitter"></i>
                                            </a>
                                        </li>
                                    <?php }
                                    if(($row->yahoo != "") and ($configClass['show_agent_linkin'] == 1)){
                                        ?>
                                        <li class="linkin">
                                            <a href="<?php echo $row->yahoo; ?>" target="_blank">
                                                <i class="edicon edicon-linkedin2"></i>
                                            </a>
                                        </li>
                                    <?php }
                                    if(($row->gtalk != "") and ($configClass['show_agent_gplus'] == 1)){
                                        ?>
                                        <li class="gplus">
                                            <a href="<?php echo $row->gtalk; ?>" target="_blank">
                                                <i class="edicon edicon-google-plus"></i>
                                            </a>
                                        </li>
                                    <?php }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }

        if($pageNav->total > $pageNav->limit){
        ?>
            <div class="clearfix"></div>
            <DIV class="pageNavdiv">
                <?php echo $pageNav->getListFooter();?>
            </DIV>
        <?php } ?>
    </div>
</div>