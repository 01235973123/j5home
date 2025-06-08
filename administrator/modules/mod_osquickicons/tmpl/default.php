<?php
;/**
; * @version	$Id: default.php $
; * @package	OS Property
; * @author		Dang Thuc Dam http://www.joomdonation.com
; * @copyright	Copyright (c) 2007 - 2024 Joomdonation. All rights reserved.
; * @license	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
; */


// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$db = Factory::getDbo();
?>
<div class="clr"></div>

<?php if($modLogo): ?>
<div id="osQuickIconsTitle">
	<a href="<?php echo Route::_('index.php?option=com_osproperty'); ?>" title="<?php echo Text::_('OS Property'); ?>">
		<span>OS Property</span>
	</a>
</div>
<?php endif; ?>

<div id="osQuickIcons" <?php if(!$modLogo): ?> class="osNoLogo"<?php endif; ?>>
    <?php
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=configuration_list', 'setting.png', Text::_('OS_CONFIGURATION'));
    if (Factory::getUser()->authorise('categories', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=categories_list', 'categories.png', Text::_('OS_MANAGE_CATEGORIES'));
	}
	if (Factory::getUser()->authorise('type', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=type_list', 'type.png', Text::_('OS_MANAGE_PROPERTY_TYPES'));
	}
	if (Factory::getUser()->authorise('convenience', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=amenities_list', 'convenience.png', Text::_('OS_MANAGE_CONVENIENCE'));
	}
	if (Factory::getUser()->authorise('properties', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=properties_list', 'property.png', Text::_('OS_MANAGE_PROPERTIES'));
	}
	if (Factory::getUser()->authorise('pricelists', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=pricegroup_list', 'price.png', Text::_('OS_MANAGE_PRICELIST'));
	}
	if (Factory::getUser()->authorise('agents', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=agent_list', 'users.png', Text::_('OS_MANAGE_AGENTS'));
	}
	if (Factory::getUser()->authorise('companies', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=companies_list', 'company.png', Text::_('OS_MANAGE_COMPANIES'));
	}
	if (Factory::getUser()->authorise('extrafieldgroups', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=fieldgroup_list', 'group.png', Text::_('OS_MANAGE_FIELD_GROUPS'));
	}
	if (Factory::getUser()->authorise('extrafields', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=extrafield_list', 'fields.png', Text::_('OS_MANAGE_EXTRA_FIELDS'));
	}
	if (Factory::getUser()->authorise('location', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=state_list', 'state.png', Text::_('OS_MANAGE_STATES'));
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=city_list', 'city.png', Text::_('OS_MANAGE_CITY'));
	}
	if (Factory::getUser()->authorise('email', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=email_list', 'email.png', Text::_('OS_MANAGE_EMAIL_FORMS'));
	}
	if (Factory::getUser()->authorise('comments', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=comment_list', 'comment.png', Text::_('OS_MANAGE_COMMENTS'));
	}
	if (Factory::getUser()->authorise('tags', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=tag_list', 'tag.png', Text::_('OS_MANAGE_TAGS'));
	}
	if (Factory::getUser()->authorise('themes', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=theme_list', 'theme.png', Text::_('OS_MANAGE_THEMES'));
	}
	if (Factory::getUser()->authorise('transaction', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=transaction_list', 'order.png', Text::_('OS_MANAGE_TRANSACTION'));
	}
	if (Factory::getUser()->authorise('plugin_list', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=plugin_list', 'payment_plugin.png', Text::_('OS_PAYMENT_PLUGINS'));
	}
	if (Factory::getUser()->authorise('csv', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=form_default', 'csv.png', Text::_('OS_CSV_FORM'));
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=csvexport_default', 'csvexport.png', Text::_('OS_EXPORT_CSV'));
	}
	if (Factory::getUser()->authorise('xml', 'com_osproperty')) {
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=xml_default', 'xmlexport.png', Text::_('OS_EXPORT_XML'));
		ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=xml_defaultimport', 'xmlimport.png', Text::_('OS_IMPORT_XML'));
	}
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=properties_backup', 'export.png', Text::_('OS_BACKUP'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=properties_restore', 'restore.png', Text::_('OS_RESTORE'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=theme_list', 'theme.png', Text::_('OS_MANAGE_THEMES'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=translation_list', 'translate.png', Text::_('OS_TRANSLATION_LIST'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=form_default', 'csv.png', Text::_('OS_CSV_FORM'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=csvexport_default', 'csvexport.png', Text::_('OS_EXPORT_CSV'));
	ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=xml_default', 'xmlexport.png', Text::_('OS_EXPORT_XML'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=xml_defaultimport', 'xmlimport.png', Text::_('OS_IMPORT_XML'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=transaction_list', 'order.png', Text::_('OS_MANAGE_TRANSACTION'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&task=properties_prepareinstallsample', 'install.png', Text::_('OS_INSTALLSAMPLEDATA'));
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=properties_sefoptimize', 'icon-48-sef.png', Text::_('OS_OPTIMIZE_SEF_URLS'));
    $translatable = JLanguageMultilang::isEnabled() && count((array)$languages);
    if($translatable){
        ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=properties_syncdatabase', 'sync.png', Text::_('OS_SYNC_MULTILINGUAL_DATABASE'));
    }
    if($configClass['enable_report'] == 1){
        $db->setQuery("Select count(id) from #__osrs_report where is_checked = '0'");
        $count_report = $db->loadResult();
        if($count_report > 0){
            ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=report_listing', 'notice_new.png', Text::_('OS_USER_REPORT'));
        }else{
            ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=report_listing', 'notice.png', Text::_('OS_USER_REPORT'));
        }
    }
    ModOsquickiconsHelper::quickiconButton('index.php?option=com_osproperty&amp;task=tag_list', 'tag.png', Text::_('OS_MANAGE_TAGS'));
    ?>
    <div style="clear: both;"></div>
</div>
