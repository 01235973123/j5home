<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2025 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

/**
 * Layout variables
 * -----------------
 * @var   string $username
 * @var   string $controlGroupClass
 * @var   string $controlLabelClass
 * @var   string $controlsClass
 */

$params = ComponentHelper::getParams('com_users');
$minimumLength = $params->get('minimum_length', 4);
($minimumLength) ? $minSize = ",minSize[$minimumLength]" : $minSize = '';

$bootstrapHelper   = $this->bootstrapHelper;

$inputClass = 'form-control';

$emailField = $this->form->getField('email');

if ($this->config->use_email_as_username && $emailField)
{
	echo $emailField->getControlGroup($bootstrapHelper);

	// Remove the email field
	$this->form->removeField('email');
}
else
{
?>
	<div class="<?php echo $controlGroupClass;  ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<?php echo  Text::_('EB_USERNAME') ?><span class="required">*</span>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="username" id="username1" class="<?php echo $inputClass; ?> validate[required,minSize[2],ajax[ajaxUserCall]]<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" value="<?php echo $this->escape($this->input->getUsername('username')); ?>" />
		</div>
	</div>
<?php
}
?>
<div class="<?php echo $controlGroupClass;  ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  Text::_('EB_PASSWORD') ?><span class="required">*</span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="password" name="password1" id="password1" class="<?php echo $inputClass; ?> validate[required<?php echo $minSize;?>]<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" value=""/>
	</div>
</div>
<div class="<?php echo $controlGroupClass;  ?>">
	<div class="<?php echo $controlLabelClass; ?>">
		<?php echo  Text::_('EB_RETYPE_PASSWORD') ?><span class="required">*</span>
	</div>
	<div class="<?php echo $controlsClass; ?>">
		<input type="password" name="password2" id="password2" class="<?php echo $inputClass; ?> validate[required,equals[password1]]<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" value="" />
	</div>
</div>