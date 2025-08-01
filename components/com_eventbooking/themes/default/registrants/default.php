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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('formbehavior.chosen', 'select');

EventbookingHelperHtml::addOverridableScript('media/com_eventbooking/js/admin-registrants-default.min.js');
Text::script('EB_SELECT_EVENT_TO_ADD_REGISTRANT', true);

$return = base64_encode(Uri::getInstance()->toString());
$cols   = 3;

if (in_array('last_name', $this->coreFields))
{
	$showLastName = true;
}
else
{
	$showLastName = false;
}

$rootUri         = Uri::root(true);
$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
$hiddenPhone     = $bootstrapHelper->getClassMapping('hidden-phone');
$hasTickets      = count($this->tickets) > 0;

if ($this->params->get('show_page_heading', 1))
{
?>
	<h1 class="eb-page-heading"><?php echo $this->params->def('page_heading', Text::_('EB_MANAGE_REGISTRANTS')); ?></h1>
<?php
}

if (EventbookingHelper::isValidMessage($this->params->get('intro_text')))
{
?>
	<div class="eb-description eb-registrants-management-intro-text"><?php echo $this->params->get('intro_text');?></div>
<?php
}
?>
<div id="eb-registrants-management-page" class="eb-container eb-joomla4-container">
	<div class="btn-toolbar" id="btn-toolbar">
		<?php echo Toolbar::getInstance('toolbar')->render(); ?>
	</div>
<form action="<?php Route::_('index.php?option=com_eventbooking&view=registrants&Itemid=' . $this->Itemid);?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="filters btn-toolbar clearfix mt-2 mb-2">
		<?php echo $this->loadTemplate('search_bar'); ?>
	</div>
<?php
	if (count($this->items))
	{
	?>
		<table class="<?php echo $bootstrapHelper->getClassMapping('table table-striped table-bordered table-hover'); ?>">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="list_first_name eb-first-name-column">
					<?php echo HTMLHelper::_('grid.sort', Text::_('EB_FIRST_NAME'), 'tbl.first_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
					if ($showLastName && $this->config->get('rm_show_last_name', 1))
					{
						$cols++;
					?>
						<th class="list_last_name eb-last-name-column <?php echo $hiddenPhone; ?>">
							<?php echo HTMLHelper::_('grid.sort', Text::_('EB_LAST_NAME'), 'tbl.last_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
				?>
				<th class="list_event eb-event-column">
					<?php echo HTMLHelper::_('grid.sort', Text::_('EB_EVENT'), 'ev.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
				if ($this->config->show_event_date)
				{
					$cols++;
				?>
					<th class="list_event_date eb-event-date-column <?php echo $hiddenPhone; ?>">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_EVENT_DATE'), 'ev.event_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->get('rm_show_email', 1))
				{
					$cols++;
				?>
					<th class="list_email eb-email-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->get('rm_show_number_registrants', 1))
				{
					$cols++;
				?>
					<th class="list_registrant_number eb-number-registrants-column <?php echo $hiddenPhone; ?>">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_REGISTRANTS'), 'tbl.number_registrants', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($hasTickets)
				{
					$cols++;
				?>
					<th width="10%" class="title eb-tickets-column" nowrap="nowrap">
						<?php echo Text::_('EB_TICKETS'); ?>
					</th>
				<?php
				}

				if ($this->config->get('rm_show_registration_date', 1))
				{
					$cols++;
				?>
					<th class="<?php echo $hiddenPhone; ?> eb-registration-date-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_REGISTRATION_DATE'), 'tbl.register_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->get('rm_show_amount', 1))
				{
					$cols++;
				?>
					<th class="list_amount eb-amount-column <?php echo $hiddenPhone; ?>">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_GROSS_AMOUNT'), 'tbl.amount', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				foreach ($this->fields as $field)
				{
					$cols++;

					if ($field->is_core || $field->is_searchable)
					{
					?>
						<th class="title <?php echo $hiddenPhone; ?> eb-<?php echo $field->name; ?>column">
							<?php echo HTMLHelper::_('grid.sort', Text::_($field->title), 'tbl.' . $field->name, $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
					else
					{
					?>
						<th class="title <?php echo $hiddenPhone; ?> eb-<?php echo $field->name; ?>column"><?php echo $field->title; ?></th>
					<?php
					}
				}

				if ($this->config->activate_deposit_feature)
				{
					$cols++;
				?>
					<th class="eb-payment-status <?php echo $hiddenPhone; ?>" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_DEPOSIT_PAYMENT_STATUS'), 'tbl.payment_status', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->activate_tickets_pdf)
				{
					$cols++;
				?>
					<th width="8%" class="center eb-ticket-number-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_TICKET_NUMBER'), 'tbl.ticket_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				 <?php
				}

				if ($this->config->get('rm_show_registration_status', 1))
				{
					$cols++;
				?>
					<th class="list_id <?php echo $hiddenPhone; ?> eb-registration-status-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_REGISTRATION_STATUS'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->enable_coupon)
				{
					$cols++;
				?>
					<th class="<?php echo $hiddenPhone; ?> eb-coupon-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_COUPON'), 'cp.code', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->activate_checkin_registrants)
				{
					$cols++;
				?>
					<th class="list_id <?php echo $hiddenPhone; ?> eb-checked-in-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_CHECKED_IN'), 'tbl.checked_in', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->activate_invoice_feature
					&& $this->config->get('rm_show_invoice', 1))
				{
					$cols++;
				?>
					<th width="8%" class="<?php echo $hiddenPhone; ?> eb-invoice-number-column">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_INVOICE_NUMBER'), 'tbl.invoice_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->show_certificate_sent_status)
				{
					$cols++;
				?>
					<th class="center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_CERTIFICATE_SENT'), 'tbl.certificate_sent', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}

				if ($this->config->rm_show_transaction_id)
				{
					$cols++;
				?>
                    <th class="title <?php echo $hiddenPhone; ?> eb-transaction-id-column" nowrap="nowrap">
	                    <?php echo HTMLHelper::_('grid.sort', Text::_('EB_TRANSACTION_ID'), 'tbl.transaction_id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
                    </th>
                <?php
				}

				if ($this->config->get('rm_show_id', 1))
				{
					$cols++;
				?>
					<th width="3%" class="title <?php echo $hiddenPhone; ?> eb-id-column" nowrap="nowrap">
						<?php echo HTMLHelper::_('grid.sort', Text::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?php
					if ($this->pagination->total > $this->pagination->limit)
					{
					?>
						<td colspan="<?php echo $cols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					<?php
					}
				?>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;

		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$row      = $this->items[$i];
			$link     = Route::_('index.php?option=com_eventbooking&view=registrant&id=' . $row->id . '&Itemid=' . $this->Itemid . '&return=' . $return);
			$isMember = $row->group_id > 0;
			$img    = $row->checked_in ? 'tick.png' : 'publish_x.png';
			$alt    = $row->checked_in ? Text::_('EB_CHECKED_IN') : Text::_('EB_NOT_CHECKED_IN');
			$action = $row->checked_in ? Text::_('EB_UN_CHECKIN') : Text::_('EB_CHECKIN');
			$task   = $row->checked_in ? 'registrant.reset_check_in' : 'registrant.check_in_webapp';
			$checked 	= HTMLHelper::_('grid.id', $i, $row->id);
			?>
			<tr class="<?php echo "row$k"; if ($row->is_group_billing) echo ' eb-row-group-billing'; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>" class="eb-first-name-column">
						<?php echo $row->first_name ?>
					</a>
					<?php
					if ($row->is_group_billing)
					{
						echo '<br />' ;
						echo Text::_('EB_GROUP_BILLING');
					}
					if ($isMember)
					{
						$groupLink = Route::_('index.php?option=com_eventbooking&view=registrant&id=' . $row->group_id . '&Itemid=' . $this->Itemid . '&return=' . $return);
					?>
						<br />
						<?php echo Text::_('EB_GROUP'); ?><a href="<?php echo $groupLink; ?>"><?php echo $row->group_name ;  ?></a>
					<?php
					}
					?>
				</td>
				<?php
					if ($showLastName && $this->config->get('rm_show_last_name', 1))
					{
					?>
						<td class="<?php echo $hiddenPhone; ?> eb-last-name-column">
							<?php echo $row->last_name ; ?>
						</td>
					<?php
					}
				?>
				<td class="eb-event-column">
					<?php echo $row->title ; ?>
				</td>
				<?php
				if ($this->config->show_event_date)
				{
				?>
					<td class="<?php echo $hiddenPhone; ?> eb-event-date-column">
						<?php
						if ($row->event_date == EB_TBC_DATE)
						{
							echo Text::_('EB_TBC');
						}
						else
						{
							echo HTMLHelper::_('date', $row->event_date, $this->config->date_format . ' H:i', null);
						}
						?>
					</td>
				<?php
				}

				if ($this->config->get('rm_show_email', 1))
				{
				?>
					<td class="eb-email-column">
						<?php echo $row->email; ?>
					</td>
				<?php
				}

				if ($this->config->get('rm_show_number_registrants', 1))
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-number-registrants-column" style="font-weight: bold;">
						<?php echo $row->number_registrants; ?>
					</td>
				<?php
				}

				if ($hasTickets)
				{
				?>
					<td>
						<?php echo implode('<br />', $this->getRegistrantTicketOutput($row)); ?>
					</td>
				<?php
				}

				if ($this->config->get('rm_show_registration_date', 1))
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-registration-date-column">
						<?php echo HTMLHelper::_('date', $row->register_date, $this->config->date_format . ' H:i'); ?>
					</td>
				<?php
				}

				if ($this->config->get('rm_show_amount', 1))
				{
				?>
					<td align="right" class="<?php echo $hiddenPhone; ?> eb-amount-column">
						<?php echo EventbookingHelper::formatAmount($row->amount, $this->config); ?>
					</td>
				<?php
				}

				foreach ($this->fields as $field)
				{
					$fieldValue = $this->fieldsData[$row->id][$field->id] ?? '';

					if ($fieldValue && $field->fieldtype == 'File')
					{
						$fieldValue = '<a href="' . Route::_('index.php?option=com_eventbooking&task=controller.download_file&file_name=' . $fieldValue) . '">' . $fieldValue . '</a>';
					}
				?>
					<td class="<?php echo $hiddenPhone; ?> eb-<?php echo $field->name; ?>column">
						<?php echo $fieldValue; ?>
					</td>
				<?php
				}

				if ($this->config->activate_deposit_feature)
				{
				?>
					<td class="<?php echo $hiddenPhone; ?>">
						<?php
						// Do not show deposit payment status for waiting list
						if (!in_array($row->published, [3, 4]))
						{
							if ($row->payment_status == 1)
							{
								echo Text::_('EB_FULL_PAYMENT');
							}
							elseif ($row->payment_status == 2)
							{
								echo Text::_('EB_DEPOSIT_PAID');
							}
							else
							{
								echo Text::_('EB_PARTIAL_PAYMENT');
							}
						}
						?>
					</td>
				<?php
				}

				if ($this->config->activate_tickets_pdf)
				{
				?>
					<td class="center eb-ticket-number-column">
						<?php
						if ($row->ticket_code)
						{
						?>
							<a href="<?php echo Route::_('index.php?option=com_eventbooking&task=registrant.download_ticket&download_code=' . $row->registration_code); ?>" title="<?php echo Text::_('EB_DOWNLOAD'); ?>"><?php echo $row->ticket_number ? EventbookingHelperTicket::formatTicketNumber($row->ticket_prefix, $row->ticket_number, $this->config) : Text::_('EB_DOWNLOAD_TICKETS');?></a>
						<?php
						}
						?>
					</td>
				<?php
				}

				if ($this->config->get('rm_show_registration_status', 1))
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-registration-status-column">
						<?php
						switch ($row->published)
						{
							case 0 :
								echo Text::_('EB_PENDING');
								break;
							case 1 :
								echo Text::_('EB_PAID');
								break;
							case 2 :
								echo Text::_('EB_CANCELLED');
								break;
							case 3:
								echo Text::_('EB_WAITING_LIST');
								break;
							case 4:
								echo Text::_('EB_WAITING_LIST_CANCELLED');
								break;
						}
						?>
					</td>
				<?php
				}

				if ($this->config->enable_coupon)
				{
				?>
					<td class="eb-coupon-column">
						<?php echo $row->coupon_code ; ?>
					</td>
				<?php
				}

				if ($this->config->activate_checkin_registrants)
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-checked-in-column">
						<a href="<?php echo Route::_('index.php?option=com_eventbooking&task=' . $task . '&id=' . $row->id . '&' . JSession::getFormToken() . '=1' . '&Itemid=' . $this->Itemid); ?>"><img src="<?php echo $rootUri . '/media/com_eventbooking/assets/images/' . $img; ?>" alt="<?php echo $alt; ?>" /></a>

						<?php
						if ($row->checked_in && (int) $row->checked_in_at)
						{
						?>
							<br /><?php echo Text::sprintf('EB_CHECKED_IN_AT', HTMLHelper::_('date', $row->checked_in_at, $this->config->date_format . ' H:i:s')); ?>
						<?php
						}

						if (!$row->checked_in && (int) $row->checked_out_at)
						{
						?>
							<br /><span style="color: red;"><?php echo Text::sprintf('EB_CHECKED_OUT_AT', HTMLHelper::_('date', $row->checked_out_at, $this->config->date_format . ' H:i:s')); ?></span>
						<?php
						}
						?>
					</td>
				<?php
				}

				if ($this->config->activate_invoice_feature && $this->config->get('rm_show_invoice', 1))
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-invoice-number-column">
						<?php
						if ($row->invoice_number)
						{
						?>
							<a href="<?php echo Route::_('index.php?option=com_eventbooking&task=registrant.download_invoice&id=' . ($row->cart_id ?: ($row->group_id ?: $row->id))); ?>" title="<?php echo Text::_('EB_DOWNLOAD'); ?>"><?php echo EventbookingHelper::callOverridableHelperMethod('Helper', 'formatInvoiceNumber', [$row->invoice_number, $this->config, $row]) ; ?></a>
						<?php
						}
						?>
					</td>
				<?php
				}

				if ($this->config->show_certificate_sent_status)
				{
				?>
					<td class="center">
						<?php echo $row->certificate_sent ? Text::_('JYES') : Text::_('JNO'); ?>
					</td>
				<?php
				}
				
				if ($this->config->rm_show_transaction_id)
				{
				?>
                    <td class="eb-transaction-id-column"><?php echo $row->transaction_id; ?></td>
				<?php
				}

				if ($this->config->get('rm_show_id', 1))
				{
				?>
					<td class="center <?php echo $hiddenPhone; ?> eb-id-column">
						<?php echo $row->id; ?>
					</td>
				<?php
				}
				?>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>
	<?php
		echo HTMLHelper::_(
			'bootstrap.renderModal',
			'collapseModal',
			[
				'title' => Text::_('EB_MASS_MAIL'),
				'footer' => $this->loadTemplate('batch_footer'),
			],
			$this->loadTemplate('batch_body')
		);

		if (count($this->exportTemplates))
		{
			echo HTMLHelper::_(
				'bootstrap.renderModal',
				'collapseModal_Export_Template',
				[
					'title' => Text::_('EB_EXPORT_REGISTRANTS'),
					'footer' => $this->loadTemplate('batch_export_footer'),
				],
				$this->loadTemplate('batch_export_body')
			);
		}
	}
	else
	{
	?>
		<div class="eb-message"><?php echo Text::_('EB_NO_REGISTRATION_RECORDS');?></div>
	<?php
	}
?>
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<form action="<?php Route::_('index.php?option=com_eventbooking&view=registrants&Itemid=' . $this->Itemid);?>" method="post" name="registrantsExportForm" id="registrantsExportForm">
	<input type="hidden" name="task" value=""/>
	<input type="hidden" id="export_filter_search" name="filter_search"/>
	<input type="hidden" id="export_filter_from_date" name="filter_from_date" value="">
	<input type="hidden" id="export_filter_to_date" name="filter_to_date" value="">
	<input type="hidden" id="export_filter_event_id" name="filter_event_id" value="">
	<input type="hidden" id="export_filter_published" name="filter_published" value="">
	<input type="hidden" id="export_cid" name="cid" value="">
	<?php
	if ($this->config->activate_checkin_registrants)
	{
		?>
		<input type="hidden" id="export_filter_checked_in" name="filter_checked_in"  value="">
		<?php
	}
	?>
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>
