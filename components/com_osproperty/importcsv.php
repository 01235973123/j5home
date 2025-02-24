<?php
/*------------------------------------------------------------------------
# csvimport.php - OS Property 
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
// defined('_JEXEC') or die;
//define('_JEXEC', true);
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
if (file_exists(dirname(__FILE__) . '/defines.php')) {
    include_once dirname(__FILE__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(dirname(__DIR__)));
    require_once JPATH_BASE.'/includes/defines.php';
}

define('REPLACE_PATH', str_replace(JPATH_BASE,'',dirname(__FILE__)));

require_once JPATH_BASE.'/includes/framework.php';

jimport('joomla.database.database');
jimport('joomla.application.input');
jimport('joomla.event.dispatcher');
jimport('joomla.application.input');
jimport('joomla.event.dispatcher');
jimport('joomla.environment.response');
jimport('joomla.environment.uri');
jimport('joomla.log.log');
jimport('joomla.application.component.helper');
jimport('joomla.methods');
jimport('joomla.factory');
jimport( 'joomla.filesystem.stream' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$app = JFactory::getApplication('site');
$app->initialise();
$jinput = $app->input;
//$jinput    = JFactory::getApplication()->input;
include (JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/images.php');
include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/classimage.php');
include (JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/csvform.php');
include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/property.php');
include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/photo.php');
include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/state.php');
include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/category.php');
include(JPATH_ROOT.DS."components".DS."com_osproperty".DS."helpers".DS."csv".DS."FileReader.php");
include(JPATH_ROOT.DS."components".DS."com_osproperty".DS."helpers".DS."csv".DS."CSVReader.php");
$configClass = OSPHelper::loadConfig();
$db  = JFactory::getDbo();
$db->setQuery("SELECT * FROM #__osrs_csv_forms WHERE active_cron_import = '1' and published = '1'");
$csvs = $db->loadObjectList();
foreach ($csvs as $csv) 
{
    $csv_file = JPATH_ROOT."/".$csv->csv_file;
    if(JFile::exists($csv_file))
    {

        $max_size  = $csv->max_file_size * 1024 * 1024;
        $size = filesize($csv_file);
        if($size <= $max_size)
        {
            $reader = new CSVReader( new FileReader($csv_file));
            $reader->setSeparator( $configClass['csv_seperator'] );
            $rs = 0;
            $j = 0;
            $import_utf = 0;
            while( false != ( $cell = $reader->next() ) ){
                if($rs > 0){
                    $isImport = OspropertyCsvform::importCell($csv,$cell,$import_utf);
                }
                $rs++;
            }
        }
    }
}

?>