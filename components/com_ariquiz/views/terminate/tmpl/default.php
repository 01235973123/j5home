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

$tmpl = JRequest::getCmd('tmpl');
?>

<div class="aq-message">
<?php echo JText::_('COM_ARIQUIZ_MESSAGE_TAKEAGAIN'); ?>
</div>
<br/><br/>
<a href="#" class="btn btn-primary aq-button" onclick="YAHOO.ARISoft.page.pageManager.submitForm('tryAgain'); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_YES'); ?>&nbsp;&nbsp;&nbsp;<i class="icon-ok"></i></a>
<a href="#" class="btn aq-button" onclick="YAHOO.ARISoft.page.pageManager.submitForm('gotopage'); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_NO'); ?>&nbsp;&nbsp;&nbsp;<i class="icon-ban-circle"></i></a>

<input type="hidden" name="rurl" value="<?php echo $this->returnUrl; ?>" />
<input type="hidden" name="quizId" value="<?php echo $this->quizId; ?>" />
<?php if ($tmpl): ?>
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
<?php endif; ?>