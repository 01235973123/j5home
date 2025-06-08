<?php
/**
 * @version               1.22.0
 * @package               Joomla
 * @subpackage            OS Property
 * @author                Tuan Pham Ngoc
 * @copyright             Copyright (C) 2012 - 2023 Ossolution Team
 * @license               GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Cache\Cache;
use Joomla\Registry\Registry;

class plgSystemOspiportcsv extends CMSPlugin
{
	/**
     * Application object.
     *
     * @var    JApplicationCms
     */
    protected $app;

    /**
     * Database object.
     *
     * @var    JDatabaseDriver
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe.
     * @param   array    $config   An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config = [])
    {
        if (!file_exists(JPATH_ROOT . '/components/com_osproperty/osproperty.php')) 
		{
            return;
        }
        parent::__construct($subject, $config);
    }

    /**
     * Send reminder to registrants
     *
     * @return void
     * @throws Exception
     */
    public function onAfterRespond()
    {
		
        if (!$this->app) {
            return;
        }


        if (!$this->canRun()) {
            return;
        }

		include (JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
		include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/images.php');
		include (JPATH_ROOT.'/administrator/components/com_osproperty/helpers/classimage.php');
		include (JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/csvform.php');
		include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/property.php');
		include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/photo.php');
		include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/state.php');
		include (JPATH_ADMINISTRATOR.'/components/com_osproperty/tables/category.php');
		include(JPATH_ROOT.DS."components/com_osproperty/helpers/csv/FileReader.php");
		include(JPATH_ROOT.DS."components/com_osproperty/helpers/csv/CSVReader.php");
		$configClass = OSPHelper::loadConfig();
		$db  = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__osrs_csv_forms WHERE active_cron_import = '1' and published = '1'");
		$csvs = $db->loadObjectList();
		foreach ($csvs as $csv) 
		{
			$csv_file = JPATH_ROOT."/".$csv->csv_file;
			if(File::exists($csv_file))
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
	}

	    

    /**
     * Method to check whether this plugin should be run
     *
     * @return bool
     */
    private function canRun()
    {
        if (!$this->app) 
		{
            return false;
        }

        // If trigger reminder code is set, we will only process sending reminder from cron job
        if (trim($this->params->get('trigger_reminder_code', ''))
            && trim($this->params->get('trigger_reminder_code', '')) != $this->app->input->getString('trigger_reminder_code')) {
            return false;
        }

        return true;
    }
}
