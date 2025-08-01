<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2024 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div id="eb-search-result-list-layout" class="eb-container">
<h1 class="eb-page-heading"><?php echo $this->escape(Text::_('EB_SEARCH_RESULT')); ?></h1>
	<?php
	if (count($this->items))
	{
		echo $this->loadCommonLayout('common/events_list_layout.php');
	}
	else
	{
	?>
		<p class="text-info"><?php echo Text::_('EB_NO_EVENTS_FOUND') ?></p>
	<?php
	}

	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo Route::_('index.php?option=com_eventbooking&view=search&layout=list&Itemid=' . $this->Itemid); ?>">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	</form>
</div>