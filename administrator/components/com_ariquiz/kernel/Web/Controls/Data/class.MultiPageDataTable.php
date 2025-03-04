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

AriKernel::import('Web.Controls.Data.DataTable');

class AriMultiPageDataTableControl extends AriDataTableControl
{
	var $_specOptions;
	
	function __construct($id, $columns, $options = null, $paginatorOptions = null, $scrolling = null, $mainTable = true)
	{
		if (!isset($options['']))
		$this->_specOptions = $options;

		if (!$paginatorOptions) $paginatorOptions = array();
		$paginatorOptions['containers'] = $id . '_pag';
		$paginator = new AriPaginatorControl($paginatorOptions);
		$dataSource = $this->_createDataSource($columns);

		$initialRequest = $this->_getOptionValue('initialRequest', '&adtInit=1');
		$initialRequest .= '&t=' . time();
		
		parent::__construct(
			$id, 
			$columns, 
			$dataSource,
			array(
				'mainTable' => $mainTable,
				'initialRequest' => $initialRequest,
				'generateRequest' => $this->_getOptionValue('generateRequest', 'YAHOO.ARISoft.widgets.DataTable.prototype.generateRequest'),
				'paginationEventHandler' => $this->_getOptionValue('paginationEventHandler', 'YAHOO.widget.DataTable.handleDataSourcePagination'),
				'width' => $this->_getOptionValue('width'),
				'height' => $this->_getOptionValue('height'),
				'disableHighlighting' => $this->_getOptionValue('disableHighlighting', false),
				'MSG_EMPTY' => $this->_getOptionValue('MSG_EMPTY', JText::_('ARIDATATABLE_LABEL_DTNORECORDS'))),
			$paginator,
			$scrolling);
	}
	
	function _createDataSource($columns)
	{
		$dataFields = $this->_getOptionValue('dataFields', null);
		if (is_null($dataFields) && is_array($columns))
		{
			$dataFields = array();
			foreach ($columns as $column)
			{
				$dataFields[] = $column->getConfigValue('key');
			}
		}
		
		if (!is_array($dataFields)) $dataFields = array();
		
		$dataSource = new AriDataSourceControl('"' . $this->_getOptionValue('dataUrl', '') . '"',
			array(
				'responseType' => ARI_DATASOURCE_RESPONSE_JSON,
				'responseShema' => '{resultsList: "records", fields: ' . json_encode($dataFields) . ', metaFields: {totalRecords: "totalRecords", paginationRecordOffset: "startIndex", paginationRowsPerPage: "limit", sortKey: "sort", sortDir: "dir"}}'));
			
		return $dataSource;
	}
	
	function _getOptionValue($key, $defaultValue = null)
	{
		return isset($this->_specOptions[$key]) ? $this->_specOptions[$key] : $defaultValue; 
	}
	
	function _renderHtml($attrs = array())
	{
		$attrs['class'] = !empty($attrs['class']) ? $attrs['class'] . ' ' : '';
		$attrs['class'] .= 'yui-skin-sam';
		
		printf('<div%2$s><div id="%1$s"></div><div id="%1$s_pag"></div></div>',
			$this->id,
			AriHtmlHelper::getAttrStr($attrs));
	}

	static public function createDataInfo($data, $filter, $cnt)
	{
		return array('records' => $data,
				'totalRecords' => intval($cnt), 
				'startIndex' => intval($filter->getConfigValue('startOffset')),
				'limit' => intval($filter->getConfigValue('limit')),
				'sort' => $filter->getConfigValue('sortField'), 
				'dir' => $filter->getConfigValue('sortDirection'));
	}
}