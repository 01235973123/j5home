<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

$adminPath = dirname(__FILE__) . '/';
require_once $adminPath . 'kernel/class.AriKernel.php';
require_once $adminPath . 'defines.php';
require_once $adminPath . 'helper.php';

function com_uninstall()
{
	$lang = JFactory::getLanguage();
	$lang->load('com_ariquiz');
	
	$cfg = AriQuizHelper::getConfig();
	$cleanUninstall = $cfg->get('CleanUninstall', false);
	
	if (!$cleanUninstall)
		return ;

	$db = JFactory::getDBO();
	$db->setQuery(
		'DROP TABLE IF EXISTS 
			#__ariquiz, 
			#__ariquizaccess, 
			#__ariquizbankcategory,
			#__ariquizcategory,
			#__ariquizconfig,
			#__ariquizmailtemplate,
			#__ariquizquestion,
			#__ariquizquestioncategory,
			#__ariquizquestiontemplate,
			#__ariquizquestiontype,
			#__ariquizquestionversion,
			#__ariquizquizcategory,
			#__ariquizstatistics,
			#__ariquizstatisticsinfo,
			#__ariquizstatistics_attempt,
			#__ariquizstatistics_files,
			#__ariquizstatistics_pages,
			#__ariquiz_file,
			#__ariquiz_file_versions,
			#__ariquiz_folder,
			#__ariquiz_question_version_files,
			#__ariquiz_result_scale,
			#__ariquiz_result_scale_item,
			#__ariquiz_texttemplate,
			#__ariquiz_statistics_extradata,
			#__ariquiz_quiz_questionpool'
	);
	$db->execute();
	
	$cfg->removeCache();
}