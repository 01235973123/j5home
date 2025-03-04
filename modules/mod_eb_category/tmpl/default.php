<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Layout variables
 *
 * @var array     $rows
 * @var RADConfig $config
 * @var int       $itemId
 */
?>
<ul class="menu">
    <?php
    foreach ($rows as $row)
    {
        if (!$config->show_empty_cat && !$row->total_events)
        {
            continue;
        }

	    if ($row->category_detail_url)
	    {
		    $url = $row->category_detail_url;
	    }
		else
		{
			$url = Route::_(EventbookingHelperRoute::getCategoryRoute($row->id, $itemId));
		}
        ?>
        <li>
            <a href="<?php echo $url; ?>">
                <?php
                echo $row->name;

                if ($config->show_number_events)
                {
                ?>
                    <span class="number_events">( <?php echo $row->total_events . ' ' . ($row->total_events > 1 ? Text::_('EB_EVENTS') : Text::_('EB_EVENT')) ?>)</span>
                <?php
                }
                ?>
            </a>
        </li>
        <?php
    }
    ?>
</ul>

