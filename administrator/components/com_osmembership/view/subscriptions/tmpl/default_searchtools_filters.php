<?php
/**
 * @package		   Joomla
 * @subpackage	   Membership Pro
 * @author		   Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2012 - 2025 Ossolution Team
 * @license		   GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$filters = [];

if (isset($this->lists['filter_category_id']))
{
    $filters[] = $this->lists['filter_category_id'];
}

$filters[] = $this->lists['plan_id'];
$filters[] = $this->lists['subscription_type'];
$filters[] = $this->lists['published'];
$filters[] = $this->lists['filter_subscription_duration'];
$filters[] = $this->lists['filter_date_field'];
$filters[] = HTMLHelper::_('calendar', (int) $this->state->filter_from_date ? $this->state->filter_from_date : '', 'filter_from_date', 'filter_from_date', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium', 'showTime' => true, 'placeholder' => Text::_('OSM_FROM')]);
$filters[] = HTMLHelper::_('calendar', (int) $this->state->filter_to_date ? $this->state->filter_to_date : '', 'filter_to_date', 'filter_to_date', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium', 'showTime' => true, 'placeholder' => Text::_('OSM_TO')]);

// Fixed filter
foreach ($filters as $filter)
{
?>
    <div class="js-stools-field-filter">
		<?php echo $filter; ?>
    </div>
<?php
}

// Filters from filter fields
foreach ($this->filters as $filter)
{
?>
    <div class="js-stools-field-filter">
        <?php echo $filter; ?>
    </div>
<?php
}