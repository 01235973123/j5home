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
?>

<fieldset class="ari-quiz-guest-form">
	<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_USERDETAILS'); ?></legend>
	<div>
		<?php echo $this->form->render('extraData', $this->readOnly ? 'readonly' : '_default', false); ?>
	</div>
</fieldset>