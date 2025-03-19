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

<div class="aq-message">
<?php echo $this->message; ?>
</div>
<br/><br/>
<a href="#" class="btn aq-button" onclick="YAHOO.ARISoft.page.pageManager.submitForm('gotopage'); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_RETURN'); ?> <i class="icon-circle-arrow-right"></i></a>

<input type="hidden" name="rurl" value="<?php echo $this->returnUrl; ?>" />