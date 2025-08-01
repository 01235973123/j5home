<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()
	->useScript('core')
	->registerAndUseScript('com_osmembership.admin-tax-default', 'media/com_osmembership/js/admin-tax-default.min.js');

$keys = ['OSM_ENTER_TAX_RATE'];
OSMembershipHelperHtml::addJSStrings($keys);
?>
<form action="<?php echo $this->getFormAction(); ?>" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_PLAN'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['plan_id']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_COUNTRY'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['country']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_STATE'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['state']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_TAX_RATE'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="number" name="rate" id="rate" size="5" maxlength="250" value="<?php echo $this->item->rate;?>" />
		</div>
	</div>
	<?php
		if (isset($this->lists['vies']))
		{
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo Text::_('OSM_VIES'); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['vies'];?>
			</div>
		</div>
		<?php
		}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_PUBLISHED'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo HTMLHelper::_('form.token'); ?>
    <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value="" />
</form>