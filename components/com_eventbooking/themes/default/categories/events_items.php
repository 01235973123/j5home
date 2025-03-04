<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2025 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$bootstrapHelper       = EventbookingHelperBootstrap::getInstance();
$clearfixClass         = $bootstrapHelper->getClassMapping('clearfix');
$this->bootstrapHelper = $bootstrapHelper;
$this->nullDate        = Factory::getContainer()->get('db')->getNullDate();
?>
<div id="eb-categories">
	<?php
	foreach ($this->items as $category)
	{
		if (!$this->config->show_empty_cat && !count($category->events))
		{
			continue ;
		}

		if ($category->category_detail_url)
		{
			$categoryLink = $category->category_detail_url;
		}
		elseif ($itemId = EventbookingHelperRoute::getCategoriesMenuId($category->id))
		{
			$categoryLink = Route::_('index.php?option=com_eventbooking&view=categories&id=' . $category->id . '&Itemid=' . $itemId);
		}
		else
		{
			$categoryLink = Route::_(EventbookingHelperRoute::getCategoryRoute($category->id, $this->Itemid));
		}
		?>
		<div class="row-fluid <?php echo $clearfixClass; ?>">
			<h2 class="eb-category-title">
				<a href="<?php echo $categoryLink; ?>" class="eb-category-title-link">
					<?php echo $category->name; ?>
				</a>
			</h2>
			<?php
				if($category->description)
				{
				?>
					<div class="<?php echo $clearfixClass; ?>">
						<?php
						if ($this->params->get('category_description_limit'))
						{
							echo HTMLHelper::_('string.truncate', $category->description, $this->params->get('category_description_limit', 120));
						}
						else
						{
							echo $category->description;
						}
						?>
					</div>
				<?php
				}

				if (count($category->events))
				{
					$viewLevels = Factory::getApplication()->getIdentity()->getAuthorisedViewLevels();

					if (EventbookingHelperHtml::isLayoutOverridden('common/events_table.php'))
					{
						echo EventbookingHelperHtml::loadCommonLayout('common/events_table.php', ['items' => $category->events, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => Factory::getContainer()->get('db')->getNullDate(), 'ssl' => 0, 'viewLevels' => $viewLevels, 'categoryId' => $category->id, 'bootstrapHelper' => $bootstrapHelper]);
					}
					else
					{
						// Prepare data to display
						$this->categoryId      = $category->id;
						$this->category        = $category;

						// Backup items property
						$items = $this->items;

						// Set items to events to display
						$this->items = $category->events;

						// Render the layout
						echo $this->loadCommonLayout('common/events_table_layout.php');

						// Restore the items property
						$this->items = $items;
					}
				}
			?>
		</div>
	<?php
	}
	?>
</div>