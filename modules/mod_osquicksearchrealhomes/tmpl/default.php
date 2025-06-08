<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
/**
 * @subpackage  mod_osquicksearchrealhomes
 * @author      Dang Thuc Dam
 * @copyright   Copyright (C) 2007 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$db = Factory::getDbo();

?>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<script>
jQuery( function() {
    jQuery( "#address" ).autocomplete({
      source: "<?php echo Uri::root(); ?>index.php?option=com_osproperty&task=default_suggestion",
      minLength: 6
    });
} );
</script>
<style>
@media (min-width:801px) and (max-width: 1024px) { 
	/* tablet, landscape iPad, lo-res laptops ands desktops */ 
	.search_properties{
		max-width: <?php echo $widthsize; ?>px;
	}

	.hero-fieldset .search-location{
		width: 448px;
	}
}
@media (min-width:1025px) and (max-width: 1280px){ 
	/* big landscape tablets, laptops, and desktops */ 
	.search_properties{
		max-width: <?php echo $widthsize; ?>px;
	}
	.hero-fieldset .search-location{
		width: 448px;
	}
}
@media (min-width:1281px) { 
	/* hi-res laptops and desktops */
	.search_properties{
		max-width: <?php echo $widthsize; ?>px;
	}
	.hero-fieldset .search-location{
		width: 448px;
	}
}

@media (min-width:320px) and (max-width: 479px) { 
	/* smartphones, portrait iPhone, portrait 480x320 phones (Android) */ 
	.search_properties{
		max-width: 300px;
	}
	.hero-fieldset .search-location{
		width: 280px;
		margin-bottom:10px;
	}
}
@media (min-width:480px) and (max-width: 599px) { 
	/* smartphones, Android phones, landscape iPhone */ 
	.search_properties{
		max-width: 460px;
	}
	.hero-fieldset .search-location{
		width: 450px;
		margin-bottom:10px;
	}
}
@media (min-width:600px) and (max-width: 800px) { 
	/* portrait tablets, portrait iPad, e-readers (Nook/Kindle), landscape 800x480 phones (Android) */ 
	.search_properties{
		max-width: 580px;
	}
	.hero-fieldset .search-location{
		width: 350px;
	}
}

</style>
<div class="search_properties">
	<span class="block strapline-intro hidden-phone"><?php echo Text::_('OS_FIND_YOUR_HAPPY');?></span>
	<h1 class="hero-strapline hidden-phone"><?php echo Text::_('OS_SEARCH_PROPERTIES_INSTRODUCTION');?></h1>
	<form method="get" action="<?php echo Jroute::_('index.php?option=com_osproperty&task=property_advsearch&Itemid='.$itemid);?>" id="initialSearch">
		<fieldset class="hero-fieldset">
			<div class="main-form">
				<input type="text" autocomplete="off" value="<?php echo Factory::getApplication()->input->getString('address','');?>" placeholder="<?php echo Text::_('OS_PLACEHOLDERTEXT'); ?>" class="search-location js-typeahead-ready" name="address" id="address" />
				<?php if( $show_category == 1) {echo modOspropertySearchRealHomesHelper::listCategories($category_id,''); } ?>
				<?php
				if(count($osp_type) > 0)
				{
					foreach($osp_type as $type)
					{
						$db->setQuery("Select * from #__osrs_types where id = '$type'");
						$property_type = $db->loadObject();
						$type_name = OSPHelper::getLanguageFieldValue($property_type,'type_name');
						?>
						<button value="<?php echo $type_name; ?>" class="btn btn btn-primary hero-btn" type="submit" id="ostype<?php echo $property_type->id;?>"><?php echo $type_name; ?></button>
						<script type="text/javascript">
							jQuery( "#ostype<?php echo $property_type->id;?>" ).click(function() {
								jQuery( "#property_type" ).val('<?php echo $property_type->id;?>');
								document.initialSearch.submit();
							});
						</script>
						<?php
					}
				}
				?>
			</div>
		</fieldset>
		<input type="hidden" name="property_type" id="property_type" value="" />
		<input type="hidden" name="show_advancesearchform" id="show_advancesearchform" value="<?php echo $show_advancesearchform; ?>" />
	</form>
</div>
