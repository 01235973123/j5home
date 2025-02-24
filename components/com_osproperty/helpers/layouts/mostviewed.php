<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid')?>">
    <div class="<?php echo $bootstrapHelper->getClassMapping('span12')?>">
        <h4>
            <?php
            echo Text::_('OS_MOST_VIEW');
            ?>
        </h4>
        <div class="clearfix"></div>
        <table width="100%">
            <tr>
                <td width="70%" class="header_td">
                    <?php
                    echo Text::_('OS_PROPERTIES');
                    ?>
                </td>
                <td width="30%" class="header_td">
                    <?php
                    echo Text::_('OS_HITS');
                    ?>
                </td>
            </tr>
            <?php
            for($i=0;$i<count($rows);$i++)
            {
                $row = $rows[$i];
                $needs = array();
                $needs[] = "property_details";
                $needs[] = $row->id;
                $itemid = OSPRoute::getItemid($needs);
                $link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);
                if($i % 2 == 0)
                {
                    $bgcolor = "#efefef";
                }
                else
                {
                    $bgcolor = "#FFF";
                }
                ?>
                <tr>
                    <td class="borderbottom1 padding3 paddingleft10" style="background-color: <?php echo $bgcolor?>;">
                        <a href="<?php echo $link?>" title="<?php echo Text::_('OS_PROPERTY_DETAILS')?>">
                            <?php
                            if(($row->ref != "") && ($configClass['show_ref'] == 1))
                            {
                                ?>
                                (<?php
                                echo $row->ref;
                                ?>)
                                <?php
                            }
                            echo OSPHelper::getLanguageFieldValue($row,'pro_name');
                            ?>
                        </a>

                    </td>
                    <td class="center borderbottom1" style="background-color: <?php echo $bgcolor?>;">
                        <?php
                        echo $row->hits;
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>