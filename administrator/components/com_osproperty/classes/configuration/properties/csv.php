<?php 
/*------------------------------------------------------------------------
# csv.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

?>
<fieldset class="form-horizontal options-form">
	<legend><?php echo TextOs::_('CSV setting')?></legend>
	<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
				<span class="editlinktip hasTip hasTooltip" title="<?php echo TextOs::_( 'CSV fields Seperator' );?>::<?php echo TextOs::_('CSV fields Seperator explain'); ?>">
				 	<label for="configuration[bussiness_name]">
						<?php echo TextOs::_( 'CSV fields Seperator' ).':'; ?>
					</label>
				</span>
			</td>
			<td>
				<?php
				$option_seperator = array();
				$option_seperator[] = HTMLHelper::_('select.option',',',TextOs::_('Comma'));
				$option_seperator[] = HTMLHelper::_('select.option',';',TextOs::_('Semicolon'));
				echo HTMLHelper::_('select.genericlist',$option_seperator,'configuration[csv_seperator]','class="form-select input-large ilarge"','value','text',isset($configs['csv_seperator'])? $configs['csv_seperator']:'');
				?>
			</td>
		</tr>
	</table>
</fieldset>
