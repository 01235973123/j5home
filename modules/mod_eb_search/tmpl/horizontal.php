<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 *
 * @var \Joomla\Registry\Registry   $params
 * @var EventbookingHelperBootstrap $bootstrapHelper
 * @var string                      $inputClass
 * @var string                      $selectClass
 * @var array                       $lists
 * @var bool                        $showFromDate
 * @var bool                        $showToDate
 * @var bool                        $showCategory
 * @var int                         $presetCategoryId
 * @var bool                        $showLocation
 * @var bool                        $enableRadiusSearch
 * @var string                      $filterAddress
 * @var string                      $layout
 * @var string                      $fromDate
 * @var string                      $toDate
 * @var string                      $datePickerFormat
 * @var int                         $itemId
 * @var string                      $text
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$btnToolbarClass   = $bootstrapHelper->getClassMapping('btn-toolbar');
$btnGroupClass     = $bootstrapHelper->getClassMapping('btn-group');
$pullLeftClass     = $bootstrapHelper->getClassMapping('pull-left');
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<form method="post" name="eb_search_form" id="eb_search_form" action="<?php echo Route::_('index.php?option=com_eventbooking&task=search&&Itemid='.$itemId);  ?>">
    <div class="filters <?php echo $btnToolbarClass; ?> eb-search-bar-container clearfix">
        <div class="filter-search <?php echo $btnGroupClass . ' ' . $pullLeftClass;;?>">
            <input name="search" id="search_eb_box" maxlength="50"  class="<?php echo $inputClass; ?>" type="text" size="20" value="<?php echo $text; ?>" placeholder="<?php echo Text::_('EB_SEARCH_WORD'); ?>" />
        </div>
        <?php
            if ($showFromDate)
            {
            ?>
                <div class="<?php echo $btnGroupClass . ' ' . $pullLeftClass; ?>">
	                <?php echo HTMLHelper::_('calendar', $fromDate, 'filter_from_date', 'filter_from_date', $datePickerFormat, ['class' => 'input-medium ' . $inputClass, 'placeholder' => Text::_('EB_SEARCH_FROM_DATE')]); ?>
                </div>
            <?php
            }

            if ($showToDate)
            {
            ?>
                <div class="<?php echo $btnGroupClass . ' ' . $pullLeftClass; ?>">
                    <?php echo HTMLHelper::_('calendar', $toDate, 'filter_to_date', 'filter_to_date', $datePickerFormat, ['class' => 'input-medium ' . $inputClass, 'placeholder' => Text::_('EB_SEARCH_TO_DATE')]); ?>
                </div>
            <?php
            }
        ?>
        <div class="<?php echo $btnGroupClass . ' ' . $pullLeftClass; ?>">
			<?php
			if ($showCategory && !$presetCategoryId)
            {
	            echo $lists['category_id'];
            }

			if ($showLocation)
            {
	            echo $lists['location_id'];
            }
			?>
        </div>

        <?php
        if ($enableRadiusSearch)
        {
        ?>
        <div class="<?php echo $btnGroupClass . ' ' . $pullLeftClass; ?>">
            <input type="text" name="filter_address"<?php echo $bootstrapHelper->getFrameworkClass('form-control', 3); ?> placeholder="<?php echo Text::_('EB_ADDRESS_CITY_POSTCODE'); ?>" value="<?php echo htmlspecialchars($filterAddress, ENT_COMPAT, 'UTF-8'); ?>" />
            <?php echo $lists['filter_distance']; ?>
        </div>
        <?php
        }
        ?>

        <div class="<?php echo $btnGroupClass . ' ' . $pullLeftClass; ?>">
            <input type="submit" class="<?php echo $bootstrapHelper->getClassMapping('btn btn-primary') ?> search_button" value="<?php echo Text::_('EB_SEARCH'); ?>" />
        </div>
    </div>
	<input type="hidden" name="layout" value="<?php echo $layout; ?>" />
	<?php
	if ($presetCategoryId)
	{
	?>
        <input type="hidden" name="category_id" value="<?php echo $presetCategoryId; ?>" />
	<?php
	}

	if ($params->get('search_result_filter_order'))
	{
	?>
		<input type="hidden" name="filter_order" value="<?php echo $params->get('search_result_filter_order'); ?>" />
	<?php
	}

	if ($params->get('search_result_filter_order_dir'))
	{
	?>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $params->get('search_result_filter_order_dir'); ?>" />
	<?php
	}
	?>
</form>