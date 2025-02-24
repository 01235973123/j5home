<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$rootUri = Uri::root(true);

Factory::getApplication()->getDocument()
	->addScript($rootUri . '/media/com_osmembership/assets/js/chartjs/Chart.min.js')
	->addScript($rootUri . '/media/com_osmembership/assets/js/chartjs/chartjs-plugin-datalabels.min.js')
	->addScript($rootUri . '/media/com_osmembership/js/admin-dashboard-daily-sales-chart.min.js')
	->addScriptOptions('dailyLabels', $this->dailySales['labels'])
	->addScriptOptions('dailySales', $this->dailySales['income'])
	->addScriptOptions('dailySubscriptionsCount', $this->dailySales['count'])
	->addScriptOptions('dailySalesAjaxUrl', Uri::base(true) . '/index.php?option=com_osmembership&task=subscription.get_daily_sales_chart_data');

Text::script('OSM_SALES_INCOME');
?>
<canvas id="osm-daily-sales-chart"></canvas>