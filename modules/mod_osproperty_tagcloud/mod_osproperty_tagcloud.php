<?php
use Joomla\CMS\Factory;
/*------------------------------------------------------------------------
# mod_osproperty_tagcloud.php - mod_osproperty_tagcloud
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2010 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

//no direct access
defined('_JEXEC') or die('Restricted Access');
error_reporting(0);
// Include the syndicate functions only once
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
include_once (JPATH_ROOT.'/components/com_osproperty/router.php');
include_once (JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
include_once (JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
include_once (JPATH_ROOT.'/components/com_osproperty/helpers/route.php');


$helper = new modOSTagCloudHelper();
$user = Factory::getUser();

$limit = $params->get('limit');
//set restrictions on properties returned
$where  = '';
$where .= ' AND p.published = 1 AND c.published = 1 AND p.approved = 1';
if(intval($user->id) == 0){ //is non-registered user
	$where .= ' AND p.access = 0';
}

$cols = array();
if($params->get('coladdress'))      $cols[] = 'p.address';
if($params->get('colptitle'))       $cols[] = 'p.pro_name';
if($params->get('colpshort'))       $cols[] = 'p.pro_small_desc';
if($params->get('colpdesc'))        $cols[] = 'p.pro_full_desc';
if($params->get('colpcity'))        $cols[] = 'h.city as city_name';
if($params->get('colpprovince'))    $cols[] = 'g.state_name';
if($params->get('colctitle'))       $cols[] = 'c.category_name';
if($params->get('colttitle'))       $cols[] = 'd.type_name';
if($params->get('colpcountry'))     $cols[] = 'e.country_name';

$cols = implode(',', $cols);
$data = $helper->getWords($cols, $where, $limit, $cat);
//The magic..
$realWordList   = $helper->filterWords($data, $params->get('excludelist'));
$wordArray      = $helper->parseString($realWordList, $params->get('tagcount'));
//output it all.
echo '<div class="iptagcloud">';
$helper->outputWords($wordArray, $params->get('minsize'), $params->get('maxsize'), $params->get('fontcolor'));
echo '</div>';
