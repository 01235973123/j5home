<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$btnClass    = $this->bootstrapHelper->getClassMapping('btn');
$span12      = $this->bootstrapHelper->getClassMapping('span12');
$checkoutUrl = Route::_('index.php?option=com_eventbooking&task=view_checkout&Itemid=' . $this->Itemid . EventbookingHelper::addTimeToUrl(), false);
?>
<script src="<?php echo Uri::root(true); ?>/media/com_eventbooking/js/site-cart-mini.min.js"></script>
<h1 class="eb-page-heading"><?php echo $this->escape(Text::_('EB_ADDED_EVENTS')); ?></h1>
<div id="eb-mini-cart-page" class="eb-container eb-cart-content">
<?php
if (count($this->items)) {
?>
	<form method="post" name="adminForm" id="adminForm" action="index.php">
		<?php
		$total = 0 ;
		$k = 0 ;

		$inputGroupClass = ' input-group';

		for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
		{
			$item = $this->items[$i];

			if ($item->prevent_duplicate_registration === '')
			{
				$preventDuplicateRegistration = $this->config->prevent_duplicate_registration;
			}
			else
			{
				$preventDuplicateRegistration = $item->prevent_duplicate_registration;
			}

			if ($preventDuplicateRegistration)
			{
				$readOnly = ' readonly="readonly" ';
			}
			else
			{
				$readOnly = '';
			}

			$rate  = $this->config->show_discounted_price ? $item->discounted_rate : $item->rate;
			$total += $item->quantity * $rate;

			$url = Route::_(
				EventbookingHelperRoute::getEventRoute($item->id, $item->main_category_id, $this->Itemid) . '&tmpl=component',
				false
			);
		?>
		<div class="well clearfix">
			<div class="row-fluid">
				<div class="<?php echo $span12; ?> eb-mobile-event-title col_event_title col_event">
					<?php
					if ($this->config->get('link_to_event_details_from_cart', 1))
					{
					?>
						<a href="<?php echo $url; ?>"><?php echo $item->title; ?></a>
					<?php
					}
					else
					{
						echo $item->title;
					}
					?>
				</div>
				<?php
					if ($this->config->show_event_date)
					{
					?>
						<div class="<?php echo $span12; ?> eb-mobile-event-date">
							<strong><?php echo Text::_('EB_EVENT_DATE'); ?>: </strong>
							<?php
								if ($item->event_date == EB_TBC_DATE)
								{
									echo Text::_('EB_TBC');
								}
								else
								{
									echo HTMLHelper::_('date', $item->event_date, $this->config->event_date_format, null);
								}
							?>
						</div>
					<?php
					}
				?>
				<div class="<?php echo $span12; ?> eb-mobile-event-price">
					<strong><?php echo Text::_('EB_PRICE'); ?> :</strong>
					<?php echo EventbookingHelper::formatCurrency($rate, $this->config); ?>
				</div>
				<div class="<?php echo $span12; ?> eb-mobile-quantity">
					<strong><?php echo Text::_('EB_QUANTITY'); ?> :</strong>
					<div class="btn-wrapper input-append<?php echo $inputGroupClass; ?>">
						<input id="quantity" type="number"<?php if ($item->maxQuantity > 0) echo ' max="' . $item->maxQuantity . '"'; ?> min="0" class="form-control input-mini quantity_box" size="3" value="<?php echo $item->quantity ; ?>" name="quantity[]" <?php echo $readOnly ; ?> onchange="updateCart();" />
						<button onclick="javascript:removeCart(<?php echo $item->id; ?>);" id="update_cart" class="<?php echo $btnClass; ?> btn-default" type="button">
							<i class="fa fa-times-circle"></i>
						</button>
						<input type="hidden" name="event_id[]" value="<?php echo $item->id; ?>" />
					</div>
				</div>
				<div class="<?php echo $span12; ?> eb-mobile-sub-total">
					<strong><?php echo Text::_('EB_SUB_TOTAL'); ?> :</strong>
					<?php echo EventbookingHelper::formatCurrency($rate*$item->quantity, $this->config); ?>
				</div>
			</div>
		</div>
			<?php
			}
			?>
			<div style="text-align: center" class="totals clearfix">
				<div>
					<?php echo Text::_('EB_TOTAL') . ' ' . EventBookingHelper::formatCurrency($total, $this->config); ?>
				</div>
			</div>
			<?php
			?>
		<div style="text-align: center;" class="form-actions bottom">
			<div>
				<button onclick="javascript:closeCartPopup();" id="add_more_item" class="<?php echo $btnClass; ?> btn-success" type="button">
					<i class="icon-new"></i> <?php echo Text::_('EB_ADD_MORE_EVENTS'); ?>
				</button>
				<button onclick="javascript:checkOut('<?php echo $checkoutUrl; ?>');" id="check_out" class="<?php echo $btnClass; ?> btn-primary" type="button">
					<i class="fa fa-mail-forward"></i> <?php echo Text::_('EB_CHECKOUT'); ?>
				</button>
			</div>
		</div>
		<input type="hidden" name="option" value="com_eventbooking" />
		<input type="hidden" name="task" value="cart.update_cart" />
	</form>
<?php
} else {
?>
	<p class="message"><?php echo Text::_('EB_NO_EVENTS_IN_CART'); ?></p>
<?php
}
?>
</div>
<script type="text/javascript">
	<?php echo $this->jsString ; ?>
	var EB_INVALID_QUANTITY = '<?php echo Text::_('EB_INVALID_QUANTITY', true); ?>';
</script>