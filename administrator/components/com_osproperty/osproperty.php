<?php

/*------------------------------------------------------------------------
# admin.osproperty.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Multilanguage;

error_reporting(E_CORE_ERROR | E_ERROR | E_PARSE | E_USER_ERROR | E_COMPILE_ERROR);
//error_reporting(E_ALL);
define('DS', DIRECTORY_SEPARATOR);
Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

jimport('joomla.filesystem.folder');
//Include files from classes folder
$dir = Folder::files(JPATH_COMPONENT_ADMINISTRATOR."/classes");
if(count($dir) > 0)
{
	for($i=0;$i<count($dir);$i++)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/classes".DS.$dir[$i]);
	}
}

//Include files from helpers folder
$dir = Folder::files(JPATH_COMPONENT_ADMINISTRATOR."/helpers");
if(count($dir) > 0)
{
	for($i=0;$i<count($dir);$i++)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers".DS.$dir[$i]);
	}
}

include_once(JPATH_ROOT."/components/com_osproperty/helpers/libraries/libraries.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/jquery.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/bootstrap.php");
include_once(JPATH_ROOT."/components/com_osproperty/helpers/osupload.php");
//require_once JPATH_ROOT . '/components/com_osproperty/helpers/csvlib/autoload.php';
OSLibraries::checkMembership();

HTMLHelper::_('jquery.framework');
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/css/backend_style.css");
if(OSPHelper::isJoomla4())
{
	$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/css/style4.css");
}	
$document->addScript(Uri::root()."media/com_osproperty/assets/js/ajax.js");
$document->addScript(Uri::root()."media/com_osproperty/assets/js/lib.js");
//JRequest::setVar('hidemainmenu',1);

global $_jversion,$configs,$mainframe,$languages,$jinput;
$jinput = Factory::getApplication()->input;
$languages = OSPHelper::getLanguages();
$db = Factory::getDBO();
$db->setQuery("select * from #__osrs_configuration");
$configs = $db->loadOBjectList();
OSPHelper::generateBoostrapVariables();
$version = new Version();
$current_joomla_version = $version->getShortVersion();
$three_first_char = substr($current_joomla_version,0,3);
$mainframe = Factory::getApplication();

$user = Factory::getUser();

global $langArr;
$countryIDArray = array(12,28,35,169,66,193,86,92,130,147,187,152,162,175,71,9,13,18,20,15,51,65,73,90,120,136,167,87,39,47,55,114,138,144,176,181,195,91,149,151,110,41,145,3,5,77,45,82,93,25,135,146,50,206,163,170,192,125,132,198,84,54,185,97,56,191,207,208,209,116,4,30,49,133,158,178,123);
$countryFileArray = array('au_australia','br_brazil','ca_canada','es_spain','fr_france','gb_united','in_india','it_italy','nl_netherlands','pt_portugal','tr_turkey','ru_russia','sg_singapore','se_sweden','de_germany','ar_argentina','at_austria','bb_barbados','be_belgium','bs_bahamas','dk_denmark','fi_finland','gr_greece','ie_ireland','mx_mexico','no_norway','za_southafrica','id_indonesia','cl_chile','hr_croatia','ec_ecuador','my_malaysia','pk_pakistan','pe_peru','ch_switzerland','th_thailand','uy_uruguay','il_israel','qa_qatar','ro_romania','lu_luxembourg','co_colombia','ph_philippines','al_albania','ad_andorra','gt_guatemala','cr_costarica','hn_honduras','jm_jamaica','bo_bolivia','ng_nigeria','pl_poland','cz_czech','mv_maldives','sk_slovakia','sk_srilanka','ae_uae','mo_morocco','nz_newzealand','ve_venezuela','hu_hungary','do_dominican','tt_trinidad','ke_kenya','eg_egypt','uk_ukraine','sl_scotland','nr_northern_ireland','wa_wales','mt_malta','dz_algeria','bg_bulgaria','cy_cyprus','ni_nicaragua','sa_saudiarabia','tw_taiwan','mo_montenegro');

for($i=0;$i<count($countryIDArray);$i++)
{
	$langArr[$i] = new stdClass();
	$langArr[$i]->country_id = $countryIDArray[$i];
	$langArr[$i]->file_name = $countryFileArray[$i].".txt";
}

global $configs,$configClass;

$blacktaskarry = array('properties_showphotosinzipfile','properties_print','extrafield_addfieldoption','extrafield_removefieldoption','extrafield_savechangeoption','upload_ajaxupload','agent_getstate','configuration_connectfb','properties_newupload','properties_install');
$tmpl = $jinput->getString('tmpl','');


$db = Factory::getDBO();
$db->setQuery('SELECT * FROM #__osrs_configuration ');
$configs = array();
$configClass = array();
foreach ($db->loadObjectList() as $config) 
{
	$configs[$config->fieldname] = $config->fieldvalue;
	$configClass[$config->fieldname] = $config->fieldvalue;
}

if((!in_array($task,$blacktaskarry)) and ($tmpl != "component")){

	if (version_compare(JVERSION, '3.0', 'lt')) 
	{
		OSPHelper::loadBootstrap(true);	
	}
	else
	{
		$document->addStyleSheet(Uri::root().'media/jui/css/jquery.searchtools.css');
	}

	if($configClass['load_lazy'])
	{
		?>
		<script src="<?php echo Uri::root(); ?>media/com_osproperty/assets/js/lazy.js" type="text/javascript"></script>
		<?php
	}

	OSPHelper::chosen();

}

/**
 * Multiple languages processing
 */
if (Multilanguage::isEnabled() && !OSPHelper::isSyncronized())
{
	OSPHelper::setupMultilingual();
}

$option = $jinput->getString('option','com_osproperty');
$task = $jinput->getString('task','');
if($task != ""){
	$taskArr = explode("_",$task);
	$maintask = $taskArr[0];
}else{
	//cpanel
	$maintask = "";
	$task = "cpanel_list";
}

//include(JPATH_COMPONENT_ADMINISTRATOR."/helpers/osproperty");
if (version_compare(JVERSION, '3.0', 'ge')) {
}

if($maintask != "ajax")
{
	$fromarray = array('oscalendar');
	$from = $jinput->getString('from','');

	$allowtask = array('cpanel_list','type_list','categories_list','properties_list','fieldgroup_list','extrafield_list','companies_list','agent_list','country_list','state_list','city_list','pricegroup_list','email_list','comment_list','translation_list','amenities_list','theme_list','form_default','csvexport_default','xml_defaultimport','tag_list','plugin_list','transaction_list');

	if(!in_array($task,$blacktaskarry) && !in_array($from,$fromarray) && $tmpl != "component")
	{
		if(in_array($task, $allowtask))
		{
			HelperOspropertyCommon::renderSubmenu($task);
		}

		$db->setQuery("Select count(id) from #__osrs_properties");
		$count_properties = $db->loadResult();
		
		$db->setQuery("Select count(id) from #__osrs_agents");
		$count_agents = $db->loadResult();

		$db->setQuery("Select count(id) from #__osrs_categories");
		$count_categories = $db->loadResult();

		$db->setQuery("Select count(id) from #__osrs_types");
		$count_types = $db->loadResult();

		if($count_properties == 0 && $count_agents == 0 && $count_categories == 0 && $count_types == 0)
		{
			$msg = sprintf(Text::_('OS_YOU_DO_NOT_HAVE_ANY_DATA_CLICK_TO_INSTALL_SAMPLE_DATA'),'<a href="'.Uri::base().'index.php?option=com_osproperty&task=properties_prepareinstallsample">here</a>');
			Factory::getApplication()->enqueueMessage($msg, 'message');
		}
	}
}



switch ($maintask){
	default:
	case "cpanel":
		OspropertyCpanel::cpanel($option);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "plugin":
		if (!Factory::getUser()->authorise('plugin_list', 'com_osproperty')) {
			//return JError::raise(E_WARNING, 404, Text::_('JERROR_ALERTNOAUTHOR'));
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyPlugin::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "fieldgroup":
		if (!Factory::getUser()->authorise('extrafieldgroups', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyFieldgroup::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "extrafield":
		if (!Factory::getUser()->authorise('extrafields', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyExtrafield::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "categories":
		if (!Factory::getUser()->authorise('categories', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCategories::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "properties":
		if ((!Factory::getUser()->authorise('properties', 'com_osproperty')) and ($task != "properties_reGeneratePictures") and ($task != "properties_sefoptimize")) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyProperties::display($option,$task);
		if($task != "properties_newupload"){
			HelperOspropertyCommon::loadFooter($option);
		}
	break;
	case "amenities":
		if (!Factory::getUser()->authorise('convenience', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyAmenities::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "type":
		if (!Factory::getUser()->authorise('propertytypes', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyType::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "pricegroup":
		if (!Factory::getUser()->authorise('pricelists', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyPricegroup::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "companies":
		if (!Factory::getUser()->authorise('companies', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCompanies::display($option,$task);
	break;
	case "country":
		if (!Factory::getUser()->authorise('location', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCountry::display($option,$task);
	break;
	case "state":
		if (!Factory::getUser()->authorise('location', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyState::display($option,$task);
	break;
	case "agent":
		if ((!Factory::getUser()->authorise('agents', 'com_osproperty')) and ($tmpl != "component")){
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyAgent::display($option,$task);
	break;
	case "coupon":
		OspropertyCoupon::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case 'comment':
		if (!Factory::getUser()->authorise('comments', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyComment::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case 'configuration':
		if (!Factory::getUser()->authorise('configuration', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyConfiguration::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case 'email':
		if (!Factory::getUser()->authorise('email', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyEmailBackend::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "transaction":
		if (!Factory::getUser()->authorise('transaction', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyTransaction::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "form":
		if (!Factory::getUser()->authorise('csv', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCsvform::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "city":
		if (!Factory::getUser()->authorise('location', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCity::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "translation":
		if (!Factory::getUser()->authorise('translation', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyTranslation::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "theme":
		if (!Factory::getUser()->authorise('themes', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyTheme::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "csvexport":
		if (!Factory::getUser()->authorise('csv', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyCsvExport::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "report":
		if (!Factory::getUser()->authorise('report', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyReport::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "tag":
		if (!Factory::getUser()->authorise('tags', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyTag::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "upload":
		OspropertyUpload::display($option,$task);
	break;
	case "xml":
		if (!Factory::getUser()->authorise('xml', 'com_osproperty')) {
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
		OspropertyXml::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
	case "request":
		OspropertyRequest::display($option,$task);
		HelperOspropertyCommon::loadFooter($option);
	break;
}
if((!in_array($task,$blacktaskarry)) and ($tmpl != "component")){
	if($configClass['load_lazy']){
		?>
		<script type="text/javascript">
		jQuery(function() {
			jQuery("img.oslazy").lazyload();
		});
		</script>
		<?php
	}
}
?>
