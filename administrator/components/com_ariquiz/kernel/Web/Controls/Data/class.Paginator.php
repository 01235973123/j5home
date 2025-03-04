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

class AriPaginatorControl extends JObject
{
	var $id;
	var $_options = array(
		'alwaysVisible' => true,
		'containers' => null,
		'containerClass' => 'yui-pg-container',
		'initialPage' => 1,
		'pageLinksStart' => 1,
		'recordOffset' => 0,
		'firstPageLinkLabel' => null, // << first
		'lastPageLinkLabel' => null, // last >>
		'nextPageLinkLabel' => null, // next >
		'previousPageLinkLabel' => null, // < prev
		'pageReportTemplate' => null, //'({currentPage} of {totalPages})'
		'rowsPerPageDropdownClass' => 'text_area',
		'rowsPerPage' => 10,
		'template' => 'Display#: {RowsPerPageDropdown} {FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {CurrentPageReport}',
		'totalRecords' => 0,
		'updateOnChange' => false,
		'rowsPerPageOptions' => array(5, 10, 15, 20, 25, 30, 50, 100)
	);

	function __construct($options = null)
	{
		$this->id = uniqid('pag');
		
		if (is_array($options))
			$this->_options = array_merge($this->_options, $options);
	}

	function getDef()
	{
		return 'new YAHOO.widget.Paginator(' . json_encode($this->_options) . ')';
	}
}