<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2024 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Layout variables
 *
 * @var stdClass $item
 */

$config = EventbookingHelper::getConfig();

HTMLHelper::_('bootstrap.dropdown');
?>
<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="icon-plus"></i> <?php echo Text::_('EB_SAVE_TO'); ?> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu eb-save-to-calendar-container">
        <li><a  href="<?php echo EventbookingHelperHtml::getAddToGoogleCalendarUrl($item); ?>" target="_blank"><i class="fa fab fa-google-plus"></i><?php echo Text::_('EB_GOOGLE_CALENDAR'); ?></a></li>
        <li><a href="<?php echo EventbookingHelperHtml::getAddToYahooCalendarUrl($item);?>" target="_blank"><i class="fa fab fa-yahoo"></i> <?php echo Text::_('EB_YAHOO_CALENDAR'); ?></a></li>
        <li><a href="<?php echo Route::_('index.php?option=com_eventbooking&task=event.download_ical&event_id=' . $item->id . '&Itemid=' . $Itemid); ?>"> <i class="fa fa-download"></i> <?php echo Text::_('EB_DOWNLOAD_ICAL'); ?></a></li>
    </ul>
</div>
