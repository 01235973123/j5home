<?php
/*
 * ARI Extensions Joomla! plugin
 *
 * @package		ARI Extensions Joomla! plugin
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

class plgSystemariextensionsInstallerScript
{
	private $db;

	function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	function install($parent) 
	{
		$this->extendTables();
	}

	function uninstall($parent) 
	{
		$this->restoreModulesTable();
		$this->restoreExtensionsTable();
	}

	function extendTables()
	{
		$this->extendModulesTable();
		$this->extendExtensionsTable();
	}
	
	function extendModulesTable()
	{
		if ($this->isDbFieldExists('#__modules', 'extra_params'))
			return ;
			
		$config = JFactory::getConfig();
		$dbType = $config->get('dbtype');

		if ($dbType == 'postgresql')
		{
			$this->db->setQuery('ALTER TABLE #__modules ADD COLUMN extra_params text NOT NULL DEFAULT \'\'');
		}
		else
		{
			if ($this->isDbSupportDefaultExpression()) {
				$this->db->setQuery('ALTER TABLE #__modules ADD COLUMN extra_params MEDIUMTEXT NOT NULL DEFAULT (\'\')');
			} else {
				$this->db->setQuery('ALTER TABLE #__modules ADD COLUMN extra_params VARCHAR(10000) NOT NULL DEFAULT \'\'');
			}
		}

		$this->db->execute();
	}

	function extendExtensionsTable()
	{
		if ($this->isDbFieldExists('#__extensions', 'extra_params'))
			return ;
			
		$config = JFactory::getConfig();
		$dbType = $config->get('dbtype');

		if ($dbType == 'postgresql')
		{
			$this->db->setQuery('ALTER TABLE #__extensions ADD COLUMN extra_params text NOT NULL DEFAULT \'\'');
		}
		else
		{
			if ($this->isDbSupportDefaultExpression()) {
				$this->db->setQuery('ALTER TABLE #__extensions ADD COLUMN extra_params MEDIUMTEXT NOT NULL DEFAULT (\'\')');
			} else {
				$this->db->setQuery('ALTER TABLE #__extensions ADD COLUMN extra_params VARCHAR(10000) NOT NULL DEFAULT \'\'');
			}
		}
		$this->db->execute();
	}
	
	function restoreModulesTable()
	{
		if (!$this->isDbFieldExists('#__modules', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__modules DROP COLUMN extra_params');
		$this->db->execute();
	}
	
	function restoreExtensionsTable()
	{
		if (!$this->isDbFieldExists('#__extensions', 'extra_params'))
			return ;
			
		$this->db->setQuery('ALTER TABLE #__extensions DROP COLUMN extra_params');
		$this->db->execute();
	}
	
	function isDbFieldExists($table, $field)
	{
		$config = JFactory::getConfig();
		$dbName = $config->get('db');
		$tblPrefix = $config->get('dbprefix');
		
		$db = JFactory::getDBO();

		$this->db->setQuery(
			sprintf('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %1$s AND column_name = %2$s AND (table_catalog = %3$s OR table_schema = %3$s)',
				$this->db->Quote(str_replace('#__', $tblPrefix, $table)),
				$this->db->Quote($field),
				$this->db->Quote($dbName)
			)
		);
		$cnt = $this->db->loadResult();
		
		return (!empty($cnt) && $cnt > 0);
	}

    function getDbFieldType($table, $field) {
        $config = JFactory::getConfig();
        $dbName = $config->get('db');
        $tblPrefix = $config->get('dbprefix');

        $this->db->setQuery(
            sprintf('SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %1$s AND column_name = %2$s AND (table_catalog = %3$s OR table_schema = %3$s)',
                $this->db->Quote(str_replace('#__', $tblPrefix, $table)),
                $this->db->Quote($field),
                $this->db->Quote($dbName)
            )
        );

        return $this->db->loadResult();
    }

	private function getDbVersion() {
		$this->db->setQuery('SELECT VERSION()');
		$version = $this->db->loadResult();
		
		return $version;
	}

	private function isDbSupportDefaultExpression() {
		$dbVersion = $this->getDbVersion();

		return version_compare($dbVersion, '8.0.13', '>=');
	}
}