<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid')?>">
    <div class="<?php echo $bootstrapHelper->getClassMapping('span12')?>">
        <h4>
            <?php
            echo Text::_('OS_MOST_RATED');
            ?>
        </h4>
        <div class="clearfix"></div>
        <table width="100%" class="mostratedtable">
            <tr>
                <td width="60%" class="header_td">
                    <?php
                    echo Text::_('OS_PROPERTIES');
                    ?>
                </td>
                <td width="40%" class="header_td">
                    <?php
                    echo Text::_('OS_RATED');
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
                            echo OSPHelper::getLanguageFieldValue($row,'pro_name');
                            ?>
                        </a>
                        <?php
                        if(($row->ref != "") && ($configClass['show_ref'] == 1))
                        {
                            ?>
                            (<?php
                            echo $row->ref;
                            ?>)
                            <?php
                        }
                        ?>
                    </td>
                    <td class="center borderbottom1" style="background-color: <?php echo $bgcolor?>;">
                        <?php
                        $points = round($row->rated);
                        ?>
                        <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/stars-<?php echo $points?>.png" />
                        <?php
                        echo " <strong>(".$points."/5)</strong>";
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>