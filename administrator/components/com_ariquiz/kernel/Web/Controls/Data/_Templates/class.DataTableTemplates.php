<?php
/*
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

define('ARI_DATATABLE_TEMPLATE',
<<<ARI_DATATABLE_TEMPLATE_
<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var %1\$s = new YAHOO.ARISoft.widgets.%6\$s("%1\$s", %2\$s, %3\$s, %4\$s);%5\$s
});
</script>
ARI_DATATABLE_TEMPLATE_
);