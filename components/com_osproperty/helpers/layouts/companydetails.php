<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> agentdetails">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
		<?php
		if($company->photo != "")
		{
			if(file_exists(JPATH_ROOT.'/images/osproperty/company/'.$company->photo))
			{
			?>
				<img src='<?php echo Uri::root()?>images/osproperty/company/<?php echo $company->photo?>' border="0"  />
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
			<img src='<?php echo Uri::root()?>media/com_osproperty/assets/images/noimage.png' class="img-polaroid"/>
			<?php
		}
		?>
	</div>

    <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
        <h1 class="componentheading agent_title">
            <?php echo $company->company_name?>
            <?php
            if($configClass['enable_report'] == 1)
            {
                //HTMLHelper::_('behavior.modal','a.osmodal');
				OSPHelperJquery::colorbox('a.reportmodal');
                ?>
                &nbsp;
                <a href="<?php echo Uri::root()?>index.php?option=com_osproperty&tmpl=component&task=property_reportForm&item_type=1&id=<?php echo $agent->id?>" class="reportmodal reportlink" rel="{handler: 'iframe', size: {x: 350, y: 600}}" title="<?php echo Text::_('OS_REPORT_COMPANY');?>">
                    <i class="edicon edicon-flag"></i>
                </a>
                <?php
            }
            if(Factory::getUser()->id == $agent->user_id && Factory::getUser()->id > 0)
            {
                ?>
                &nbsp;
                <a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_editprofile');?>" title="<?php echo Text::_('OS_EDIT_PROFILE');?>" class="editprofilelink">
                    <i class="edicon edicon-pencil"></i>
                </a>
                <?php
            }
            ?>
        </h1>
        <?php
        $address = OSPHelper::generateAddress($company);
        if($address != ""){
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> address">
                    <?php
                    echo "<i class='edicon edicon-location'></i>&nbsp;";
                    echo $address;
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
        <?php
        if($company->email != "")
        {
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <span class="agent_label"><?php echo Text::_('OS_EMAIL');?></span>:&nbsp;<a href="mailto:<?php echo $company->email;?>" target="_blank"><?php echo $company->email;?></a>
                </div>
            </div>
            <?php
        }
        if($company->phone != "")
        {
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <span class="agent_label"><?php echo Text::_('OS_PHONE');?></span>:&nbsp;
                    <a href="tel:<?php echo $company->phone; ?>" target="_blank">
                        <?php echo $company->phone; ?>
                    </a>
                </div>
            </div>
            <?php
        }
        if($company->fax != "")
        {
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <span class="agent_label"><?php echo Text::_('OS_FAX');?></span>:&nbsp;
                    <a href="tel:<?php echo $company->fax; ?>" target="_blank">
                        <?php echo $company->fax; ?>
                    </a>
                </div>
            </div>
            <?php
        }
        if($company->website != "")
        {
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <span class="agent_label"><?php echo Text::_('OS_WEBSITE');?></span>:&nbsp;
                    <a href="<?php echo "http://".str_replace("http://","",$company->website);?>" target="_blank">
                        <?php echo $company->website; ?>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="<?php echo $bootstrapHelper->getClassMapping('span4'); ?>">
        <form method="POST" action="index.php" name="contactForm" id="contactForm">
            <?php
            HelperOspropertyCommon::contactForm('contactForm',$configClass['general_bussiness_name'], $company->company_name);
            ?>
            <input type="hidden" name="option" value="com_osproperty" />
            <input type="hidden" name="task" value="company_submitcontact" />
            <input type="hidden" name="id" value="<?php echo $company->id?>" />
            <input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
        </form>
    </div>
</div>
<?php
if($company->company_description != ""){
    ?>
    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> companydescription">
        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
            <div class="companydescriptionheading">
                <?php
                printf(Text::_('OS_ABOUT_COMPANY'), $company->company_name);
                ?>
            </div>
            <?php
                echo HTMLHelper::_('content.prepare',stripslashes($company->{'company_description'.$lang_suffix}));
            ?>
        </div>
    </div>
<?php
}
?>
<div class="clearfix"></div>