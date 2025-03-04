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

<?php $this->dtScales->render(); ?>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager;

	pageManager.registerActionGroup('scaleAction', {
		query: {"view": "resultscales"},
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: "<?php echo $this->dtScales->id; ?>",
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: "",
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'scaleAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_SCALEDELETE', true); ?>'
	});
});
</script>