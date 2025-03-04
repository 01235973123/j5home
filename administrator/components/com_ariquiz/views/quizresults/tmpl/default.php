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

<div class="textRight">
	<fieldset class="ari-inline-block aq-results-filter">
		<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_FILTER'); ?></legend>
		<?php echo $this->form->render('filter',  '_default', true, false, array('paramsPerRow' => 2)); ?>
		<br />
		<div class="textRight">
			<input type="button" class="button btn btn-primary" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('ajaxFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" />
		</div>
	</fieldset>
</div>

<?php $this->dtResults->render(); ?>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		aDom = YAHOO.ARISoft.DOM;

	pageManager.registerActionGroup('resultAction', {
		query: {"view": "quizresults"},
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: "<?php echo $this->dtResults->id; ?>",
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: "",
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'resultAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_RESULTDELETE'); ?>'
	});
	pageManager.registerAction('ajaxDeleteAll', {
		group: 'resultAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_RESULTDELETE'); ?>'
	});
	pageManager.registerAction('ajaxFilters', {
		group: 'resultAction'
	});
	pageManager.registerAction('deleteAll', {
		onAction: function() {
			if (confirm('<?php echo JText::_('COM_ARIQUIZ_WARNING_REMOVEALLRESULTS'); ?>'))
				pageManager.triggerAction('ajaxDeleteAll');
		}
	});			

	pageManager.subscribe('afterAction', function(o) {
		if (o.action == 'csvExport' || o.action == 'excelExport' || o.action == 'htmlExport' || o.action == 'wordExport')
			document.getElementById('task').value = '';
	});
});
</script>