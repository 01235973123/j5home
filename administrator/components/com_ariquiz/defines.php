<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

define('ARIQUIZ_VERSION', '3.10.21');

define('ARIQUIZ_TEXTTEMPLATE_SUCCESSFUL', 'QuizSuccessful');
define('ARIQUIZ_TEXTTEMPLATE_FAILED', 'QuizFailed');
define('ARIQUIZ_TEXTTEMPLATE_EMAILSUCCESSFUL', 'QuizSuccessfulEmail');
define('ARIQUIZ_TEXTTEMPLATE_EMAILFAILED', 'QuizFailedEmail');
define('ARIQUIZ_TEXTTEMPLATE_PRINTSUCCESSFUL', 'QuizSuccessfulPrint');
define('ARIQUIZ_TEXTTEMPLATE_PRINTFAILED', 'QuizFailedPrint');
define('ARIQUIZ_TEXTTEMPLATE_CERTIFICATESUCCESSFUL', 'QuizSuccessfulCertificate');
define('ARIQUIZ_TEXTTEMPLATE_CERTIFICATEFAILED', 'QuizFailedCertificate');
define('ARIQUIZ_TEXTTEMPLATE_ADMINEMAIL', 'QuizAdminEmail');

define('ARIQUIZ_FOLDER_IMAGES', 'images');

if (!defined('ARIQUIZ_TEXTTEMPLATE_SUMMARYBYCATEGORIES_TEMPLATE'))
define('ARIQUIZ_TEXTTEMPLATE_SUMMARYBYCATEGORIES_TEMPLATE',
<<<SUMMARYBYCATEGORIES
{repeater}
	{headertemplate}
		<table class="aq-summary-by-cat">
	{/headertemplate}
	{rowtemplate itemCount="1" rowClass="odd;even"}
		<tr>
			{celltemplate}
				<td class="aq-cat">{\$data:CategoryName}</td>
				<td class="aq-score">{\$data:UserScore} / {\$data:MaxScore} ({\$data:PercentScore}%)</td>
        	{/celltemplate}
        	{emptycelltemplate}
				<td colspan="2" class="aq-empty">&nbsp;</td>
			{/emptycelltemplate}
		</tr>
	{/rowtemplate}
	{footertemplate}
		</table>
	{/footertemplate}
	{emptytemplate}{/emptytemplate}
{/repeater}
SUMMARYBYCATEGORIES
);