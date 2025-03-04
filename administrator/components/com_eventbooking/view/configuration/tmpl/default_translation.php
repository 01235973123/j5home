<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Layout variables
 *
 * @var RADConfig                $config
 * @var Joomla\CMS\Editor\Editor $editor
 */

$rootUri = Uri::root(true);

echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'invoice-translation', Text::_('EB_TRANSLATION'));
echo HTMLHelper::_( 'uitab.startTabSet', 'invoice-translation',
	['active' => 'invoice-translation-' . $this->languages[0]->sef, 'recall' => true]);

foreach ($this->languages as $language)
{
	$sef = $language->sef;
	echo HTMLHelper::_( 'uitab.addTab', 'invoice-translation', 'invoice-translation-' . $sef, $language->title . ' <img src="' . $rootUri . '/media/mod_languages/images/' . $language->image . '.gif" />');
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('date_format_' . $sef, Text::_('EB_DATE_FORMAT'), Text::_('EB_DATE_FORMAT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="text" name="date_format_<?php echo $sef; ?>" class="form-control" value="<?php echo $config->{'date_format_' . $sef}; ?>" size="20" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('event_date_format_' . $sef, Text::_('EB_EVENT_DATE_FORMAT'), Text::_('EB_EVENT_DATE_FORMAT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="text" name="event_date_format_<?php echo $sef; ?>" class="form-control" value="<?php echo $config->{'event_date_format_' . $sef}; ?>" size="40" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('event_time_format_' . $sef, Text::_('EB_TIME_FORMAT'), Text::_('EB_TIME_FORMAT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="text" name="event_time_format_<?php echo $sef; ?>" class="form-control" value="<?php echo $config->{'event_time_format_' . $sef} ; ?>" size="40" />
		</div>
	</div>


	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('invoice_format', Text::_('EB_INVOICE_FORMAT'), Text::_('EB_INVOICE_FORMAT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display('invoice_format_' . $sef, $config->{'invoice_format_' . $sef}, '100%', '550', '75', '8');?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('invoice_format_cart', Text::_('EB_INVOICE_FORMAT_CART'), Text::_('EB_INVOICE_FORMAT_CART_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display('invoice_format_cart_' . $sef, $config->{'invoice_format_cart_' . $sef}, '100%', '550', '75', '8');?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('default_ticket_layout_' . $sef, Text::_('EB_DEFAULT_TICKET_LAYOUT'), Text::_('EB_DEFAULT_TICKET_LAYOUT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display('default_ticket_layout_' . $sef, $config->{'default_ticket_layout_' . $sef}, '100%', '550', '75', '8'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo EventbookingHelperHtml::getFieldLabel('certificate_layout_' . $sef, Text::_('EB_DEFAULT_CERTIFICATE_LAYOUT'), Text::_('EB_DEFAULT_CERTIFICATE_LAYOUT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display('certificate_layout_' . $sef, $config->{'certificate_layout_' . $sef}, '100%', '550', '75', '8') ;?>
		</div>
	</div>
	<?php
	echo HTMLHelper::_( 'uitab.endTab');
}

echo HTMLHelper::_( 'uitab.endTabSet');
echo HTMLHelper::_( 'uitab.endTab');