<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowfluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
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
				<input type="hidden" name="ordertype" id="ordertype" value="<?php echo $ordertype?>" />
			</div>
		</div>
		<HR />
		<?php
		if(count($rows) > 0){
		?>
		<div class="latestproperties latestproperties_right" >
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
                                        if($row->photo != "")
                                        {
                                            if(file_exists(JPATH_ROOT.'/images/osproperty/company/'.$row->photo))
                                            {
                                            ?>
                                                <img src='<?php echo Uri::root()?>images/osproperty/company/<?php echo $row->photo?>' border="0"  />
                                            <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png' border="0"  />
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png' border="0"  />
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="<?php echo $rowfluidClass; ?>">
                                    <div class="<?php echo $span12Class; ?> agentdetailslink">
                                        <a href="<?php echo Route::_('index.php?option=com_osproperty&task=company_info&id='.$row->id.'&Itemid='.$itemid);?>" class="agentdetailsbtn" title="<?php echo Text::_('OS_VIEW_DETAILS');?>"><?php echo Text::_('OS_VIEW_DETAILS');?></a>
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
                                            <?php echo $row->company_name?>
                                        </h3>
                                    </div>
                                </div>
                                <div class="<?php echo $rowfluidClass; ?>">
                                    <div class="<?php echo $span12Class; ?> agentaddress">
                                        <?php
                                        echo "<i class='fa fa-map-marker'></i> ";
                                        echo OSPHelper::generateAddress($row);
                                        ?>
                                    </div>
                                </div>
                                <?php
                                if($row->email != ""){
                                    ?>
                                    <div class="<?php echo $rowfluidClass; ?>">
                                        <div class="<?php echo $span12Class; ?>">
                                            <?php
                                            echo "<span class='agent_label'>".Text::_('OS_EMAIL')."</span>";
                                            echo ":&nbsp;";
                                            echo "&nbsp;";
                                            echo "<a href='mailto:$row->email'>$row->email</a>";
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if($row->phone != "")
                                {
                                    ?>
                                    <div class="<?php echo $rowfluidClass; ?>">
                                        <div class="<?php echo $span12Class; ?>">
                                            <?php
                                            echo "<span class='agent_label'>".Text::_('OS_PHONE')."</span>";
                                            echo ":&nbsp;";
                                            echo "&nbsp;";
                                            echo $row->phone;
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if($row->fax != ""){
                                    ?>
                                    <div class="<?php echo $rowfluidClass; ?>">
                                        <div class="<?php echo $span12Class; ?>">
                                            <?php
                                            echo "<span class='agent_label'>".Text::_('OS_MOBILE')."</span>";
                                            echo ":&nbsp;";
                                            echo "&nbsp;";
                                            echo $row->fax;
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if($row->website != ""){
                                    ?>
                                    <div class="<?php echo $rowfluidClass; ?>">
                                        <div class="<?php echo $span12Class; ?>">
                                            <?php
                                            echo "<span class='agent_label'>".Text::_('OS_WEBSITE')."</span>";
                                            echo ":&nbsp;";
                                            $website = $row->website;
                                            if(substr($website,0,4) == "http"){

                                            }else{
                                                $website = "http://".$website;
                                            }
                                            echo "<a href='$website' target='_blank'>";
                                            echo $row->website;
                                            echo "</a>";
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="<?php echo $rowfluidClass; ?>">
                                    <div class="<?php echo $span12Class; ?>">
                                        <?php
                                        $desc = strip_tags($row->{'company_description'.$lang_suffix},"<BR><a><B>");

                                        if($desc  != ""){
                                            $descArr = explode(" ",$desc);
                                            if(count($descArr) > 50){
                                                for($j=0;$j<=50;$j++){
                                                    echo $descArr[$j]." ";
                                                }
                                                echo "...";
                                            }else{
                                                echo $desc;
                                            }
                                            ?>
                                            <BR />
                                        <?php } ?>
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
			<div class="pageNavdiv">
					<?php echo $pageNav->getListFooter();?>
			</div>
            <?php } ?>
		</div>
		<?php
		}
		?>
	</div>
</div>	