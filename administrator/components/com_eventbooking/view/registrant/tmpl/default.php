<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

HTMLHelper::_('behavior.core');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);
$document = Factory::getApplication()->getDocument();
$document->addStyleDeclaration('.hasTip{display:block !important}');

// Little command to allow viewing subscription data easier without having to edit code during support
if ($this->input->getInt('debug'))
{
	print_r($this->item);
}

$selectedState  = '';
$numberDecimals = (int) $this->config->get('decimals', 2);

// Add support for custom settings layout
if (file_exists(__DIR__ . '/default_custom_settings.php'))
{
	$hasCustomSettings = true;
}
else
{
	$hasCustomSettings = false;
}

$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
$rowFluidClass   = $bootstrapHelper->getClassMapping('row-fluid');
$span6Class      = $bootstrapHelper->getClassMapping('span6');

?>
<form action="<?php echo $this->getFormAction(); ?>" method="post" name="adminForm" id="adminForm" class="form form-horizontal" enctype="multipart/form-data">
	<?php
	if ($hasCustomSettings)
	{
		echo HTMLHelper::_( 'uitab.startTabSet', 'registrant', ['active' => 'general-page', 'recall' => true]);
		echo HTMLHelper::_( 'uitab.addTab', 'registrant', 'general-page', Text::_('EB_GENERAL'));
	}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_EVENT'); ?>
		</div>
		<div class="controls">
			<?php echo EventbookingHelperHtml::getEventSelectionInput($this->item->event_id, 'event_id'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_NB_REGISTRANTS'); ?>
		</div>
		<div class="controls">
			<?php
				if ($this->item->number_registrants > 0)
				{
					if ($this->item->is_group_billing && $this->config->allow_changing_number_registrants) {
						$readonly = '';
					}
					else {
						$readonly = 'readonly="readonly"';
					}
				?>
					<input type="text" name="number_registrants" class="form-control" value="<?php echo $this->item->number_registrants ?>" <?php echo $readonly; ?> />
				<?php
				}
				else
				{
				?>
					<input class="form-control" type="text" name="number_registrants" id="number_registrants" size="40" maxlength="250" value="1" />
					<small><?php echo Text::_('EB_NUMBER_REGISTRANTS_EXPLAIN'); ?></small>
				<?php
				}
			?>
		</div>
	</div>
	<?php
		if (!empty($this->ticketTypes))
		{
		?>
			<h3><?php echo Text::_('EB_TICKET_INFORMATION'); ?></h3>
		<?php
			foreach($this->ticketTypes as $ticketType)
			{
				if ($ticketType->capacity)
				{
					$available = $ticketType->capacity - $ticketType->registered;
				}
				elseif (!empty($this->event->event_capacity))
				{
					$available = max($this->event->event_capacity - $this->event->total_registrants, 0);
				}
				elseif ($ticketType->max_tickets_per_booking)
				{
					$available = $ticketType->max_tickets_per_booking;
				}
				else
				{
					$available = 10;
				}

				$quantity = 0;

				if (!empty($this->registrantTickets[$ticketType->id]))
				{
					$quantity = $this->registrantTickets[$ticketType->id]->quantity;
				}
				?>
				<div class="control-group">
					<div class="control-label">
						<?php echo  $ticketType->title; ?>
					</div>
					<div class="controls">
						<?php
						if ($available > 0 || $quantity > 0)
						{
							$fieldName = 'ticket_type_' . $ticketType->id;

							if ($available < $quantity)
							{
								$available = $quantity;
							}

							echo HTMLHelper::_('select.integerlist', 0, $available, 1, $fieldName, 'class="ticket_type_quantity form-control input-small"', $quantity);
						}
						else
						{
							echo Text::_('EB_NA');
						}
						?>
					</div>
				</div>
				<?php
			}
		}
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_USER'); ?>
			</div>
			<div class="controls">
				<?php echo EventbookingHelperHtml::getUserInput($this->item->user_id, 'user_id', (int) $this->item->id) ; ?>
			</div>
		</div>
		<?php
		$fields = $this->form->getFields();

		if (isset($fields['state']))
		{
			$selectedState = $fields['state']->value;
		}

		// Set class mapping to make it works with BS5 in Joomla 4 admin template
		$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
		$bootstrapHelper->getUi()->addClassMapping('control-group', 'control-group')
			->addClassMapping('control-label', 'control-label')
			->addClassMapping('controls', 'controls');

		/* @var RADFormField $field */
		foreach ($fields as $field)
		{
			echo $field->getControlGroup($bootstrapHelper);
		}

		if ($this->item->id)
		{
		?>
            <div class="control-group">
                <div class="control-label">
			        <?php echo  Text::_('EB_REGISTRATION_DATE'); ?>
                </div>
                <div class="controls">
			        <?php echo  HTMLHelper::_('date', $this->item->register_date, $this->config->date_format.' H:i');?>
                </div>
            </div>
        <?php
		}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_AMOUNT'); ?>
		</div>
		<div class="controls">
			<?php
			$input = '<input type="text" class="input-medium form-control" name="total_amount" value="' . ($this->item->total_amount > 0 ? round($this->item->total_amount, $numberDecimals) : '') . '" size="7" />';
			echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
			?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_DISCOUNT_AMOUNT'); ?>
		</div>
		<div class="controls">
			<?php
			$input = '<input type="text" class="input-medium form-control" name="discount_amount" value="' . ($this->item->discount_amount > 0 ? round($this->item->discount_amount, $numberDecimals) : '') . '" size="7" />';
			echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
			?>
		</div>
	</div>
	<?php
	if ($this->item->late_fee > 0)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_LATE_FEE'); ?>
			</div>
			<div class="controls">
				<?php
				$input = '<input type="text" class="input-medium form-control" name="late_fee" value="' . ($this->item->late_fee > 0 ? round($this->item->late_fee, $numberDecimals) : '') . '" size="7" />';
				echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
				?>
			</div>
		</div>
	<?php
	}

	if (EventbookingHelperRegistration::isEUVatTaxRulesEnabled())
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_TAX_RATE'); ?>
			</div>
			<div class="controls">
				<?php
				$input = '<input type="text" class="input-medium form-control" name="tax_rate" value="' . ($this->item->tax_rate > 0 ? round($this->item->tax_rate, $numberDecimals) : '') . '" size="7" />';
				echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
				?>
			</div>
		</div>
	<?php
	}

	if ($this->event->tax_rate > 0 || $this->item->tax_amount > 0)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_TAX_AMOUNT'); ?>
			</div>
			<div class="controls">
				<?php
				$input = '<input type="text" class="input-medium form-control" name="tax_amount" value="' . ($this->item->tax_amount > 0 ? round($this->item->tax_amount, $numberDecimals) : '') . '" size="7" />';
				echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
				?>
			</div>
		</div>
	<?php
	}

	if ($this->showPaymentFee || $this->item->payment_processing_fee > 0)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_PAYMENT_FEE'); ?>
			</div>
			<div class="controls">
				<?php
				$input = '<input type="text" class="input-medium form-control" name="payment_processing_fee" value="' . ($this->item->payment_processing_fee > 0 ? round($this->item->payment_processing_fee, $numberDecimals) : '') . '" size="7" />';
				echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
				?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_GROSS_AMOUNT'); ?>
		</div>
		<div class="controls">
			<?php
			$input = '<input type="text" class="input-medium form-control" name="amount" value="' . ($this->item->amount > 0 ? round($this->item->amount, $numberDecimals) : '') . '" size="7" />';
			echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
			?>
		</div>
	</div>
	<?php
		if ($this->config->activate_deposit_feature && !in_array($this->item->published, [3, 4]))
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_DEPOSIT_AMOUNT'); ?>
				</div>
				<div class="controls">
					<?php
					$input = '<input type="text" class="input-medium form-control" name="deposit_amount" value="' . ($this->item->deposit_amount > 0 ? round($this->item->deposit_amount, $numberDecimals) : '') . '" size="7" />';
					echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
					?>
				</div>
			</div>
			<?php
				if (in_array($this->item->payment_status, [0, 2]) && $this->item->id)
				{
				?>
					<div class="control-group">
						<div class="control-label">
							<?php echo Text::_('EB_DUE_AMOUNT'); ?>
						</div>
						<div class="controls">
							<?php
							if ($this->item->payment_status == 1)
							{
								$dueAmount = 0;
							}
							else
							{
								$dueAmount = $this->item->amount - $this->item->deposit_amount;
							}

							$input = '<input type="text" class="input-medium form-control" name="due_amount" value="' . ($dueAmount > 0 ? round($dueAmount, $numberDecimals) : '') . '" size="7" />';
							echo $bootstrapHelper->getPrependAddon($input, $this->config->currency_symbol);
							?>
						</div>
					</div>
				<?php
				}
			?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_DEPOSIT_PAYMENT_STATUS'); ?>
				</div>
				<div class="controls">
					<?php echo $this->lists['payment_status'];?>
				</div>
			</div>
		<?php
		}

		if ($this->item->id && $this->item->total_amount > 0)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo  EventbookingHelperHtml::getFieldLabel('re_calculate_fee', Text::_('EB_RE_CALCULATE_FEE'), Text::_('EB_RE_CALCULATE_FEE_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<input type="checkbox" value="1" id="re_calculate_fee" name="re_calculate_fee" />
				</div>
			</div>
		<?php
		}

		if (!$this->item->id || $this->item->amount > 0)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_PAYMENT_METHOD'); ?>
				</div>
				<div class="controls">
					<?php echo $this->lists['payment_method']; ?>
				</div>
			</div>
		<?php
		}

		if ($this->item->amount > 0)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_TRANSACTION_ID'); ?>
				</div>
				<div class="controls">
					<input type="text" class="form-control" name="transaction_id" value="<?php echo $this->item->transaction_id;?>" />
				</div>
			</div>
		<?php
		}

		if ($this->item->deposit_payment_transaction_id)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_DEPOSIT_PAYMENT_TRANSACTION_ID'); ?>
				</div>
				<div class="controls">
					<input type="text" class="form-control" name="deposit_payment_transaction_id" value="<?php echo $this->item->deposit_payment_transaction_id;?>" />
				</div>
			</div>
		<?php
		}

		if ($this->item->payment_method == 'os_offline_creditcard')
		{
			$params = new Registry($this->item->params);
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_FIRST_12_DIGITS_CREDITCARD_NUMBER'); ?>
				</div>
				<div class="controls">
					<?php echo $params->get('card_number'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('AUTH_CARD_EXPIRY_DATE'); ?>
				</div>
				<div class="controls">
					<?php echo $params->get('exp_date'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('AUTH_CVV_CODE'); ?>
				</div>
				<div class="controls">
					<?php echo $params->get('cvv'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo Text::_('EB_CARD_HOLDER_NAME'); ?>
				</div>
				<div class="controls">
					<?php echo $params->get('card_holder_name'); ?>
				</div>
			</div>
		<?php
		}

		if ($this->config->activate_checkin_registrants)
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo  Text::_('EB_CHECKED_IN'); ?>
				</div>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getBooleanInput('checked_in', $this->item->checked_in); ?>
				</div>
			</div>
		<?php
		}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('EB_REGISTRATION_STATUS'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['published'] ; ?>
		</div>
	</div>
	<?php

	if ($this->item->published == 2 && (int) $this->item->registration_cancel_date)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_REGISTRATION_CANCEL_DATE'); ?>
			</div>
			<div class="controls">
				<?php echo HTMLHelper::_('date', $this->item->registration_cancel_date, $this->config->date_format . ' H:i'); ?>
			</div>
		</div>
	<?php
	}

	if ($this->config->get('store_user_ip', 1) && $this->item->user_ip)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo  Text::_('EB_USER_IP'); ?>
			</div>
			<div class="controls">
				<?php echo $this->item->user_ip; ?>
			</div>
		</div>
	<?php
	}

	if ($this->config->show_subscribe_newsletter_checkbox && $this->item->id)
	{
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo Text::_('EB_SUBSCRIBE_TO_NEWSLETTER'); ?>
			</div>
			<div class="controls">
				<?php echo $this->item->subscribe_newsletter ? Text::_('JYES') : Text::_('JNO'); ?>
			</div>
		</div>
	<?php
	}

	if (Multilanguage::isEnabled())
	{
	?>
        <div class="control-group">
            <div class="control-label">
				<?php echo  Text::_('EB_LANGUAGE'); ?>
            </div>
            <div class="controls">
				<?php echo $this->lists['language']; ?>
            </div>
        </div>
    <?php
	}

	if ($this->config->collect_member_information && count($this->rowMembers))
	{
	?>
		<h3 class="eb-heading"><?php echo Text::_('EB_MEMBERS_INFORMATION') ; ?> <button type="button" class="btn btn-small btn-success" onclick="addGroupMember();"><span class="icon-new icon-white"></span><?php echo Text::_('EB_ADD_MEMBER'); ?></button></h3>
	<?php
		$n = count($this->rowMembers) + 4;

		for ($i = 0 ; $i < $n ; $i++)
		{
			$currentMemberFormFields = EventbookingHelperRegistration::getGroupMemberFields($this->memberFormFields, $i + 1);

			if (isset($this->rowMembers[$i]))
			{
				$rowMember = $this->rowMembers[$i] ;
				$memberId = $rowMember->id ;
				$memberData = EventbookingHelperRegistration::getRegistrantData($rowMember, $currentMemberFormFields);
				$style = '';
			}
			else
			{
				$memberId = 0;
				$memberData = [];
				$style = ' style="display:none;"';
			}

			if (!isset($memberData['country']))
			{
				$memberData['country'] = $this->config->default_country;
			}

			$form = new RADForm($currentMemberFormFields);
			$form->setEventId($this->item->event_id);
			$form->bind($memberData);
			$form->setFieldSuffix($i+1);
			$form->prepareFormFields('setRecalculateFee();');
			$form->buildFieldsDependency();

			if ($i%2 == 0)
			{
				echo "<div class=\"$rowFluidClass\">\n" ;
			}
			?>
				<div class="<?php echo $span6Class; ?>" id="group_member_<?php echo $i + 1; ?>"<?php echo $style; ?>>
					<h4><?php echo Text::sprintf('EB_MEMBER_INFORMATION', $i + 1); ?><button type="button" class="btn btn-small btn-danger" onclick="removeGroupMember(<?php echo $memberId; ?>);"><span class="icon-remove icon-white"></span><?php echo Text::_('EB_REMOVE'); ?></button></h4>
					<?php
						if ($this->event->has_multiple_ticket_types && $memberId > 0)
						{
							$ticketType = EventbookingHelperRegistration::getGroupMemberTicketTypeData($memberId);

							if ($ticketType)
							{
								$form->handleFieldsDependOnTicketTypes([$ticketType->id]);
							?>
								<div class="control-group">
									<div class="control-label">
										<?php echo Text::_('EB_TICKET_TYPE'); ?>
									</div>
									<div class="controls">
										<?php echo $ticketType->title; ?>
									</div>
								</div>
							<?php
							}
						}

						$fields = $form->getFields();

						foreach ($fields as $field)
						{
							echo $field->getControlGroup($bootstrapHelper);
						}
					?>
					<input type="hidden" name="ids[]" value="<?php echo $memberId; ?>" />
				</div>
			<?php
			if (($i + 1) %2 == 0)
			{
				echo "</div>\n" ;
			}
		}
		if ($i %2 != 0)
		{
			echo '</div>' ;
		}
	}

	// Add support for custom settings layout
	if ($hasCustomSettings)
	{
		echo HTMLHelper::_( 'uitab.endTab');
		echo HTMLHelper::_( 'uitab.addTab', 'registrant', 'custom-settings-page', Text::_('EB_REGISTRANT_CUSTOM_SETTINGS'));
		echo $this->loadTemplate('custom_settings');
		echo HTMLHelper::_( 'uitab.endTab');
		echo HTMLHelper::_( 'uitab.endTabSet');
	}

	$document->addScriptOptions('numberMembers', (int) count($this->rowMembers))
		->addScriptOptions('selectedState', $selectedState)
		->addScript(Uri::root(true) . '/media/com_eventbooking/js/admin-registrant-default.min.js');

	$languageItems = [
		'EB_ADD_MEMBER_MAXIMUM_WARNING',
		'EB_REMOVE_EXISTING_MEMBER_CONFIRM',
		'EB_REFUND_REGISTRANT_CONFIRM',
	];

	EventbookingHelperHtml::addJSStrings($languageItems);
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="group_member_id" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>