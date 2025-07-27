<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

$filters = [];

$filters[] = $this->lists['plan_id'];
$filters[] = $this->lists['filter_state'];
$filters[] = $this->lists['filter_fieldtype'];
$filters[] = $this->lists['show_core_field'];
$filters[] = $this->lists['filter_fee_field'];

foreach ($filters as $filter)
{
?>
    <div class="js-stools-field-filter">
		<?php echo $filter; ?>
    </div>
<?php
}