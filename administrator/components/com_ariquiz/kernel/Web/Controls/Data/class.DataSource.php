<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Web.Controls.Data._Templates.DataSourceTemplates');

define('ARI_DATASOURCE_RESPONSE_HTMLTABLE', 'YAHOO.util.DataSource.TYPE_HTMLTABLE');
define('ARI_DATASOURCE_RESPONSE_JSARRAY', 'YAHOO.util.DataSource.TYPE_JSARRAY');
define('ARI_DATASOURCE_RESPONSE_JSON', 'YAHOO.util.DataSource.TYPE_JSON');
define('ARI_DATASOURCE_RESPONSE_TEXT', 'YAHOO.util.DataSource.TYPE_TEXT');
define('ARI_DATASOURCE_RESPONSE_XML', 'YAHOO.util.DataSource.TYPE_XML');

class AriDataSourceControl extends JObject
{
	var $connMethodPost = false;
	var $responseType;
	var $responseShema;
	var $id;
	var $_data;
	
	function __construct($data, $options)
	{
		$this->id = uniqid('ds');
		$this->setProperties($options);
		
		$this->_data = $data;
	}

	function render()
	{
		$def = $this->getDefenition();
		printf('var %s = %s;', $this->id, $def);
	}
	
	function getDefenition()
	{
		return sprintf(ARI_DATASOURCEDEF_TEMPLATE,
			$this->_data,
			$this->responseType,
			$this->responseShema);
	}
}