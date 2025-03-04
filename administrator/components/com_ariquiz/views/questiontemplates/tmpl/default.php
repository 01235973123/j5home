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

<?php $this->dtTemplates->render(); ?>

<div id="panelAddTemplate" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_NEWQUESTIONSETTINGS'); ?></div> 
	<div class="bd" style="text-align: center; overflow: auto;">	
		<?php echo $this->typeForm->render('type'); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CONTINUE'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyAddTemplate');" />
			<input type="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelAddTemplate.hide();" />
		</div>
	</div>
</div>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager;

	page.panelAddTemplate = new YAHOO.widget.Panel("panelAddTemplate", { 
		width: "450px", 
		visible: false, 
		constraintoviewport: true, 
		modal: true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelAddTemplate.render();

	pageManager.registerAction('addTemplate', {
		onAction: function() {
			page.panelAddTemplate.show();
		}
	});
	pageManager.registerAction('applyAddTemplate', {
		onAction: function() {
			page.panelAddTemplate.hide();
		
			YAHOO.ARISoft.page.pageManager.triggerAction('add');
		}
	});
	pageManager.registerActionGroup('templateAction', {
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: '<?php echo $this->dtTemplates->id; ?>',
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: '',
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'templateAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONTEMPLATEDELETE'); ?>'
	});
});
</script>