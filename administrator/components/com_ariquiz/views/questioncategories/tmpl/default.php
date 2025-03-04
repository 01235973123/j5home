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

<?php $this->dtCategories->render(); ?>

<div id="panelMassEdit" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;" id="tblMassSettings">
		<?php echo $this->massEditform->render('massParams', '_default', true, false, array('validationGroup' => 'massEditActive')); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CLEAR'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.pageManager.massEditSettings.resetSettings();" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" />
		</div>
		<div style="text-align: left;">
			<br/>
			<b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?> :</b> <?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDITNOTE'); ?>
		</div>
	</div>
</div>


<?php
if ($this->quizId > 0): 
?>
<input type="hidden" name="quizId" value="<?php echo $this->quizId; ?>" />
<?php
endif; 
?>
<input type="hidden" name="categoryId" id="questionCategoryId" value="" />
<input type="hidden" id="deleteQuestions" name="deleteQuestions" value="0" />

<script type="text/javascript">
function submitqcategory(catId) {
	document.getElementById('questionCategoryId').value = catId;
	
	YAHOO.ARISoft.page.pageManager.triggerAction('categoryedit');
};
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		aDom = YAHOO.ARISoft.DOM;

	page.settingAdvCheckboxes = new YAHOO.ARISoft.widgets.advancedCheckbox("panelMassEdit", {});
	aDom.moveTo(aDom.wrapWithElement('form', 'panelMassEdit', {id: 'frmMassEdit', name: 'frmMassEdit'}));

	page.panelMassEdit = new YAHOO.widget.Panel("panelMassEdit", {
		width:"510px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});
	page.panelMassEdit.render();
	page.massEditSettings = new YAHOO.ARISoft.widgets.settingsPanel('panelMassEdit', {});

	pageManager.registerActionGroup('categoryAction', {
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: '<?php echo $this->dtCategories->id; ?>',
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: '',
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'categoryAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_CATEGORYDELETE', true); ?>'
	});
	pageManager.registerAction('ajaxMassEdit', {
		onAction: function(action, config) {
			page.settingAdvCheckboxes.renew();
			config.postData = YAHOO.util.Connect.setForm('frmMassEdit');
		
			page.actionHandlers.simpleDatatableAction.call(this, action, config);
		},
		group: 'categoryAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_MASSEDIT', true); ?>'
	});
	pageManager.registerAction('applyMassEdit', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['massEdit'])) 
				return ;

			page.panelMassEdit.hide();
			pageManager.triggerAction('ajaxMassEdit');
		}
	});
	pageManager.registerAction('mass_edit', {
		onAction: function() {
			page.panelMassEdit.show();
		}
	});
	YAHOO.ARISoft.page.pageManager.subscribe('beforeAction', function(o) {
		if (o.action != 'ajaxDelete')
			return true;

		var isDeleteQuestions = confirm('<?php echo JText::_('COM_ARIQUIZ_WARNING_DELETEQUESTIONFROMCAT', true); ?>');
		YAHOO.util.Dom.get('deleteQuestions').value = isDeleteQuestions ? '1' : '0'; 
	});
});

YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.customValidator('cvMassEditActive',
		function(val) {
			var validators = YAHOO.ARISoft.validators.validatorManager.validators;
			for (var i = 0; i < validators.length; i++) {
				var validator = validators[i];
				if (!validator.inValidationGroup(['massEditActive'])) 
					continue;
					
				validator.enabled = !YAHOO.ARISoft.page.massEditSettings.isDisabledSettingEl(validator.ctrlId);	
			}
				
			var failedValidators = YAHOO.ARISoft.validators.validatorManager.getFailedValidator(['massEditActive']);
			if (failedValidators.length == 0) 
				return true;

			val.errorMessage = failedValidators[0].errorMessage;
				
			return false;
		},
		{
			validationGroups: ['massEdit'], 
			errorMessage : ''
		}
	)
);
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.customValidator('cvMassEditSettings',
		function(val) {
			return YAHOO.ARISoft.page.massEditSettings.getActiveElementsCount() > 0;
		},
		{
			validationGroups: ['massEdit'], 
			errorMessage : '<?php echo JText::_('COM_ARIQUIZ_MESSAGE_MASSEDITSETTINGSREQUIRED', true); ?>'
		}
	)
);
</script>