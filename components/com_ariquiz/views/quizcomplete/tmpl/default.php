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

<?php 
	if ($this->btnEmailVisible): 
?>
<a href="#" id="btnEmail" class="btn aq-btn-email" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('ajaxSendEmail'); return false;"><i class="icon-envelope"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_SENDRESULTS'); ?></a>
<?php
	endif;
	
	if ($this->btnPrintVisible):
?>
<a href="#" class="btn aq-btn-print" onclick="window.open('index.php?option=com_ariquiz&view=quizcomplete&task=printResults&ticketId=<?php echo $this->ticketId; ?>&tmpl=component','blank'); return false;"><i class="icon-print"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_PRINT'); ?></a>
<?php
	endif;
	
	if ($this->btnCertificateVisible):
?>
<a href="#" class="btn aq-btn-certificate" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('certificate'); return false;"><i class="icon-file"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_CERTIFICATE'); ?></a>
<?php
	endif;
?>

<?php
	if ($this->resultText):
?>
<br/><br/>
<?php echo $this->resultText; ?>
<br/><br/>
<?php
	endif; 
?>

<?php
if (isset($this->dtResults)) 
	$this->dtResults->render(array('class' => 'aq-dt-results')); 
?>

<input type="hidden" name="ticketId" value="<?php echo $this->ticketId; ?>" />
<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom;

	pageManager.registerActionGroup('ajaxAction', {
		query: {"view": "quizcomplete"},
		onAction: page.actionHandlers.simpleCtrlAjaxAction,
		enableValidation: true,
		ctrl: 'btnEmail',
		errorMessage: '<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_LABEL_MAILSENT', true); ?>',
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxSendEmail', {
		group: "ajaxAction"
	});
});
</script>