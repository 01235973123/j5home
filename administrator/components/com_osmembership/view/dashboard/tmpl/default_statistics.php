<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$config = OSMembershipHelper::getConfig();
?>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="title"><?php echo Text::_('OSM_TIME')?></th>
			<th class="center"><?php echo Text::_('OSM_NUMBER_SUBSCRIPTIONS')?></th>
			<th class="title"><?php echo Text::_('OSM_INCOME')?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo Text::_('OSM_TODAY'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['today']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['today']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_YESTERDAY'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['yesterday']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['yesterday']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_THIS_WEEK'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['this_week']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['this_week']['total_amount'], $config) ?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo Text::_('OSM_LAST_WEEK'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['last_week']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['last_week']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_THIS_MONTH'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['this_month']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['this_month']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_AVERAGE_DAY_THIS_MONTH'); ?>
			</td>
			<td class="center">
				<?php echo OSMembershipHelper::formatAmount($this->data['average_day_this_month']['number_subscriptions'], $config); ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['average_day_this_month']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_PROJECTION_THIS_MONTH'); ?>
			</td>
			<td class="center">
				<?php echo OSMembershipHelper::formatAmount($this->data['projection_this_month']['number_subscriptions'], $config); ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['projection_this_month']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_LAST_MONTH'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['last_month']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['last_month']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_THIS_YEAR'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['this_year']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['this_year']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_LAST_YEAR'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['last_year']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['last_year']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_TOTAL_SUBSCRIPTIONS'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['total_subscriptions']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['total_subscriptions']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_ACTIVE_SUBSCRIPTIONS'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['active_subscriptions']['number_subscriptions']; ?>
			</td>
			<td>
				<?php echo OSMembershipHelper::formatCurrency($this->data['active_subscriptions']['total_amount'], $config) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo Text::_('OSM_ACTIVE_SUSCRIBERS'); ?>
			</td>
			<td class="center">
				<?php echo $this->data['active_subscribers']['number_subscriptions']; ?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</tbody>
</table>