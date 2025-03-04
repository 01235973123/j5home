<?php
if (!defined('J4'))
{
    $version = new JVersion();
    define('J4', version_compare($version->getShortVersion(), '4.0.0', '>='));
}

if (J4) {
    require_once dirname(__FILE__) . '/fieldsgroups/j4/fieldsgroups.php';
} else {
    require_once dirname(__FILE__) . '/fieldsgroups/j3/fieldsgroups.php';
}