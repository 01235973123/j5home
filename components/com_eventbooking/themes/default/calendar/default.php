<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

//Calculate next and previous month, year
if ($this->month == 12)
{
	$nextMonth = 1 ;
	$nextYear = $this->year + 1 ;
	$previousMonth = 11 ;
	$previousYear = $this->year ;
}
elseif ($this->month == 1)
{
	$nextMonth = 2 ;
	$nextYear = $this->year ;
	$previousMonth = 12 ;
	$previousYear = $this->year - 1 ;
}
else
{
	$nextMonth = $this->month + 1 ;
	$nextYear = $this->year ;
	$previousMonth = $this->month - 1 ;
	$previousYear = $this->year ;
}
?>
<div id="eb-calendar-page" class="eb-container">
	<?php
	if ($this->params->get('show_page_heading', 1))
	{
		$pageHeading = $this->params->get('page_heading') ?: Text::_('EB_CALENDAR');

		if ($this->input->getInt('hmvc_call'))
		{
			$hTag = 'h2';
		}
		else
		{
			$hTag = 'h1';
		}
	?>
		<<?php echo $hTag; ?> class="eb-page-heading"><?php echo $this->escape($pageHeading);?></<?php echo $hTag; ?>>
	<?php
	}

	if (EventbookingHelper::isValidMessage($this->introText))
	{
	?>
		<div class="eb-description"><?php echo $this->introText;?></div>
	<?php
	}
	?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo Route::_('index.php?option=com_eventbooking&view=calendar&layout=default&Itemid=' . $this->Itemid); ?>">
		<div id="eb-calendarwrap">
			<?php
			if ($this->showCalendarMenu)
			{
				echo $this->loadCommonLayout('calendar/navigation.php', ['layout' => 'default']);
			}

			if (EventbookingHelperHtml::isLayoutOverridden('common/calendar.php'))
			{
				$layoutData = [
					'Itemid'            => $this->Itemid,
					'config'            => $this->config,
					'month'             => $this->month,
					'previousMonth'     => $previousMonth,
					'nextMonth'         => $nextMonth,
					'previousMonthLink' => Route::_('index.php?option=com_eventbooking&view=calendar&layout=default&month=' . $previousMonth . '&year=' . $previousYear . '&Itemid=' . $this->Itemid),
					'nextMonthLink'     => Route::_('index.php?option=com_eventbooking&view=calendar&layout=default&month=' . $nextMonth . '&year=' . $nextYear . '&next=1&Itemid=' . $this->Itemid),
					'listMonth'         => $this->listMonth,
					'searchMonth'       => $this->searchMonth,
					'searchYear'        => $this->searchYear,
					'data'              => $this->data,
				];

				echo EventbookingHelperHtml::loadCommonLayout('common/calendar.php', $layoutData);
			}
			else
			{
				$layoutData = [
					'previousMonth'     => $previousMonth,
					'nextMonth'         => $nextMonth,
					'previousMonthLink' => Route::_('index.php?option=com_eventbooking&view=calendar&layout=default&month=' . $previousMonth . '&year=' . $previousYear . '&Itemid=' . $this->Itemid),
					'nextMonthLink'     => Route::_('index.php?option=com_eventbooking&view=calendar&layout=default&month=' . $nextMonth . '&year=' . $nextYear . '&next=1&Itemid=' . $this->Itemid),
				];

				echo $this->loadTemplate('calendar', $layoutData);
			}
			?>
		</div>
	</form>
</div>