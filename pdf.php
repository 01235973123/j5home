<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2023 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$config = EventbookingHelper::getConfig();
$db     = Factory::getDbo();
$query  = $db->getQuery(true);
/**
 * Layout variables
 *
 * @var array  $events
 * @var string $fromDate
 * @var string $toDate
 */

$active = Factory::getApplication()->getMenu()->getActive();

if ($active)
{
	$params = $active->getParams();
}
else
{
	$params = new \Joomla\Registry\Registry();
}
?>

<div class="eb-report-events">
    <table class="table eb-report-header">
        <thead>
            <tr class="eb-report-wrap">
                <td class="eb-report-headding">
                    <div class="eb-report-heading-wrap">
                        <?php
					$titleHeading = $params->get('report_title_heading',Text::_('EB_UPCOMING_ACTIVITIES_TIME'));
					?>
                        <h3 class="eb-report-title">
                            <?php
						echo $titleHeading. ' ';

						if ((int) $fromDate)
						{
							echo $fromDate;
						}

/*						echo '<span> to  </span>';

						if ((int) $toDate)
						{
							echo $toDate;
						}*/
						?>
                        </h3>
                        <p><i>Please book your chosen activities via the club website unless otherwise directed.</i></p>

                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr class="eb-report-wrap">
                <td class="eb-report-msg">
                    <?php
				if ($params->get('show_report_msg_text', 1))
				{
					?>
                    <p class="eb-report-msg-text">
                        <?php echo Text::_('EB_MSG_REMINDING'); ?>
                    </p>
                    <?php
				}
				else
				{
					echo '';
				}
				?>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
	foreach ($events as $row)
	{

		$userCreated = Factory::getUser($row->created_by);

		if ($row->created_by)
		{
			$query->clear()
				->select('b.field_value')
				->from('#__osmembership_subscribers AS a')
				->innerJoin('#__osmembership_field_value AS b ON a.id = b.subscriber_id')
				->where('a.user_id = ' . $row->created_by)
				->where('b.field_id = 14')
				->where('CHAR_LENGTH(b.field_value) > 0');
			$db->setQuery($query);
			$phone = (string) $db->loadResult();
		}
		else
		{
			$phone = '';
		}

		$categories = [];

		foreach ($row->categories as $category)
		{
			$categories[] = $category->name;
		}
		?>
    <table class="table eb-report-event" style="table-layout: auto; width: 100%;">
        <thead>
            <tr class="eb-report-wrap">
                <td class="eb-report-content" colspan="2">
                    <p class="eb-report-content-title font-italic">
                        <?php echo $row->title . ': ' . implode(' | ', $categories); ?> </p>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr class="eb-report-wrap">
                <td class="eb-report-info-start" width="50%" data-content="Event Start">
                    <p>
                        Starts : <span class="font-italic"> <?php
							if (strpos($row->event_date, '00:00:00') !== false)
							{
								$dateFormat = $config->date_format;
							}
							else
							{
								$dateFormat = $config->event_date_format;
							}

							echo HTMLHelper::_('date', $row->event_date, $dateFormat, null);
							?>

                        </span><br>
                        Bookings Open: <span class="font-italic"> <?php
							if ((int) $row->registration_start_date)
							{
								if (strpos($row->registration_start_date, '00:00:00') !== false)
								{
									$dateFormat = $config->date_format;
								}
								else
								{
									$dateFormat = $config->event_date_format;
								}

								echo HTMLHelper::_('date', $row->registration_start_date, $dateFormat, null);
							}
							?> </span>
                        <br>
                        Meet: <span
                            class="font-italic"><?php if ($row->location) echo $row->location->name; ?></span><br>
                        Leader's email:<span class="font-italic email-link"> <?php echo $userCreated->email; ?> </span>
                        <br>
                    </p>

                </td>
                <td class="eb-report-info-end" width="50%" data-content="Event End">
                    <p>
                        Finishes : <span class="font-italic">
                            <?php
                            if ((int) $row->event_end_date)
                            {
	                            if (strpos($row->event_end_date, '00:00:00') !== false)
	                            {
		                            $dateFormat = $config->date_format;
	                            }
	                            else
	                            {
		                            $dateFormat = $config->event_date_format;
	                            }

	                            echo HTMLHelper::_('date', $row->event_end_date, $dateFormat, null);
                            }
                            ?>
                        </span> <br>
                        Bookings Close:<span class="font-italic">
                            <?php
                            if ((int) $row->cut_off_date)
                            {
	                            if (strpos($row->cut_off_date, '00:00:00') !== false)
	                            {
		                            $dateFormat = $config->date_format;
	                            }
	                            else
	                            {
		                            $dateFormat = $config->event_date_format;
	                            }

	                            echo HTMLHelper::_('date', $row->cut_off_date, $dateFormat, null);
                            }
                            ?>
                        </span> <br>
                        Leader: <span class="font-italic">
                            <?php
                        if ($userCreated->name)
                        {
	                        echo $userCreated->name;
                        }

                        ?>
                        </span> <br>
                        Leader's phone: <span class="font-italic"> <?php echo $phone; ?></span> <br>
                        <!-- created by username -->
                    </p>

                </td>
            </tr>
            <tr class="eb-report-wrap ">
                <?php
				if ($row->short_description)
				{
					?>
                <td class="eb-report-desc text-desc" data-content="Description" colspan="2">
                    <?php
// Xóa các thẻ <br>, <br/>, <br />
$cleanDescription = str_ireplace(
    ['<br>', '<br/>', '<br />'],
    '',
    $row->short_description
);

// Xóa <p>&nbsp;</p> hoặc <div>&nbsp;</div> (có thể có khoảng trắng)
$cleanDescription = preg_replace('/<(p|div)>\s*&nbsp;\s*<\/\1>/i', '', $cleanDescription);

echo $cleanDescription;
?>

                </td>
                <?php
				}
				?>
            </tr>
        </tbody>
    </table>

    <?php
	}
	?>
</div>