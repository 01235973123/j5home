<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

$translatable = Multilanguage::isEnabled() && count($this->languages);
$editor       = Editor::getInstance(Factory::getApplication()->get('editor'));
$config       = $this->config;

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()->useScript('showon')
	->addInlineStyle('.hasTip{display:block !important}');

/* @var EventbookingViewConfigurationHtml $this */
?>
<div class="row-fluid">
	<form action="index.php?option=com_eventbooking&view=configuration" method="post" name="adminForm" id="adminForm"
		  class="form-horizontal eb-configuration">
		<?php
		echo HTMLHelper::_( 'uitab.startTabSet', 'configuration', ['active' => 'general-page', 'recall' => true]);

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'general-page', Text::_('EB_GENERAL'));
		echo $this->loadTemplate('general', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'theme-page', Text::_('EB_THEMES'));
		echo $this->loadTemplate('themes', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'sef-setting-page', Text::_('EB_SEF_SETTING'));
		echo $this->loadTemplate('sef', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'PDF_SETTINGS', Text::_('EB_PDF_SETTINGS'));
		echo $this->loadTemplate('pdf_settings');
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'invoice-page', Text::_('EB_INVOICE_SETTINGS'));
		echo $this->loadTemplate('invoice', ['config' => $config, 'editor' => $editor]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'tickets-page', Text::_('EB_TICKETS_SETTINGS'));
		echo $this->loadTemplate('tickets', ['config' => $config, 'editor' => $editor]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'certificate-page', Text::_('EB_CERTIFICATE_SETTINGS'));
		echo $this->loadTemplate('certificate', ['config' => $config, 'editor' => $editor]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'submit-event-fields-page', Text::_('EB_SUBMIT_EVENT_FIELDS'));
		echo $this->loadTemplate('submit_event_fields', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'backend-submit-event-fields-page', Text::_('EB_BACKEND_SUBMIT_EVENT_FIELDS'));
		echo $this->loadTemplate('backend_submit_event_fields', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'export-settings-page', Text::_('EB_EXPORT_REGISTRANTS_SETTINGS'));
		echo $this->loadTemplate('export_fields', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'common-tags-page', Text::_('EB_COMMON_TAGS'));
		echo $this->loadTemplate('common_tags', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		if ($translatable)
		{
			echo $this->loadTemplate('translation', ['config' => $config, 'editor' => $editor]);
		}

		if ($config->event_custom_field)
		{
			echo $this->loadTemplate('event_fields');
		}

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'eu-tax-rules-page', Text::_('EB_EU_TAX_RULES_SETTINGS'));
		echo $this->loadTemplate('eu_tax_rules', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'css-classes-map-page', Text::_('EB_CSS_CLASSES_MAP'));
		echo $this->loadTemplate('css_classes_map', ['config' => $config]);
		echo HTMLHelper::_( 'uitab.endTab');

		echo $this->loadTemplate('custom_css');

		// Add support for custom settings layout
		if (file_exists(__DIR__ . '/default_custom_settings.php'))
		{
			echo HTMLHelper::_( 'uitab.addTab', 'configuration', 'custom-settings-page', Text::_('EB_CUSTOM_SETTINGS'));
			echo $this->loadTemplate('custom_settings', ['config' => $config, 'editor' => $editor]);
			echo HTMLHelper::_( 'uitab.endTab');
		}

		echo HTMLHelper::_( 'uitab.endTabSet');
		?>
		<div class="clearfix"></div>
		<input type="hidden" name="task" value=""/>
	</form>
</div>