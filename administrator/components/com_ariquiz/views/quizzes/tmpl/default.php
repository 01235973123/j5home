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
	<div class="ari-inline-block simpleFilter">
		<table>
			<tr>
				<td class="bold"><?php echo JText::_('COM_ARIQUIZ_LABEL_FILTER'); ?></td>
				<td>
					<?php echo $this->filterForm->render('filter', '_default', true, false, array('paramsPerRow' => 2)); ?>
				</td>
				<td>
					<input type="button" class="button btn btn-primary" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('ajaxFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" />
				</td>
			</tr>
		</table>
	</div>
</div>

<?php $this->dtQuizzes->render(); ?>

<div id="panelCopy" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_COPYSETTINGS'); ?></div>  
	<div class="bd" style="text-align: center;">
		<?php echo $this->copyForm->render('copy', '_default', true, false, array('validationGroup' => 'copy')); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyCopy');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelCopy.hide(); return false;" />
		</div>
		<div style="text-align: left;">
			<br/>
			<b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b> <?php echo JText::_('COM_ARIQUIZ_LABEL_COPYQUIZ'); ?>
		</div>
	</div>
</div>

<div id="panelMassEdit" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;" id="tblMassSettings">
		<?php echo $this->massEditform->render('massParams', '_default', true, false, array('paramsPerRow' => 3, 'validationGroup' => 'massEditActive', 'title' => JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'))); ?>
		<hr/>
		<?php echo $this->massEditform->render('massParams', 'results', true, false, array('paramsPerRow' => 2, 'validationGroup' => 'massEditActive', 'title' => JText::_('COM_ARIQUIZ_LABEL_RESULTSSETTINGS'))); ?>
		<hr/>
		<?php echo $this->massEditform->render('massParams', 'security', true, false, array('paramsPerRow' => 3, 'validationGroup' => 'massEditActive', 'title' => JText::_('COM_ARIQUIZ_LABEL_SECURITYSETTINGS'))); ?>
		<hr/>
		<?php echo $this->massEditform->render('massParams', 'extra', true, false, array('paramsPerRow' => 3, 'validationGroup' => 'massEditActive', 'title' => JText::_('COM_ARIQUIZ_LABEL_EXTRASETTINGS'))); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CLEAR'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.massEditSettings.resetSettings();YAHOO.ARISoft.page.settingAdvCheckboxes.renew();" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" />
		</div>
		<div style="text-align: left;">
			<br/>
			<b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?> :</b> <?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDITNOTE'); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		aDom = YAHOO.ARISoft.DOM;

	page.settingAdvCheckboxes = new YAHOO.ARISoft.widgets.advancedCheckbox("panelMassEdit", {});
	aDom.moveTo(aDom.wrapWithElement('form', 'panelMassEdit', {id: 'frmMassEdit', name: 'frmMassEdit'}));

	YAHOO.util.Event.on('frmMassEdit', 'reset', function() {
		setTimeout(function() {
			YAHOO.util.Dom.getElementsByClassName('ari-group-params', 'select', 'frmMassEdit', function(el) {
				if (el.fireEvent)
					el.fireEvent('change');
				else {
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent("change", false, true);
					el.dispatchEvent(evt);
				}
			})
		}, 10);
	});

	page.panelMassEdit = new YAHOO.widget.Panel("panelMassEdit", {
		width:"950px", 
		height:"610px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});
	page.panelMassEdit.render();

	page.panelCopy = new YAHOO.widget.Panel("panelCopy", { 
		width:"540px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelCopy.render();

	page.massEditSettings = new YAHOO.ARISoft.widgets.settingsPanel('panelMassEdit', {});	

	pageManager.registerActionGroup('quizAction', {
		query: {"view": "quizzes"},
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: "<?php echo $this->dtQuizzes->id; ?>",
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: "",
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxActivate', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUIZACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxDeactivate', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUIZDEACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxSingleActivate', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUIZACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxSingleDeactivate', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUIZDEACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_DELETE', true); ?>'
	});
	pageManager.registerAction('ajaxFilters', {
		group: 'quizAction'
	});
	pageManager.registerAction('ajaxMassEdit', {
		onAction: function(action, config) {
			page.settingAdvCheckboxes.renew();
			config.postData = YAHOO.util.Connect.setForm('frmMassEdit');
		
			page.actionHandlers.simpleDatatableAction.call(this, action, config);
		},
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_MASSEDIT', true); ?>'
	});
	pageManager.registerAction('ajaxCopy', {
		group: 'quizAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUIZCOPY', true); ?>'
	});
	pageManager.registerAction('applyMassEdit', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['massEdit'])) 
				return ;

			page.panelMassEdit.hide();
			pageManager.triggerAction('ajaxMassEdit');
		}
	});
	pageManager.registerAction('applyCopy', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['copy'])) 
				return ;

			page.panelCopy.hide();
			pageManager.triggerAction('ajaxCopy');
		}
	});
	pageManager.registerAction('mass_edit', {
		onAction: function() {
			page.panelMassEdit.show();
		}
	});
	pageManager.registerAction('copy', {
		onAction: function() {
			page.panelCopy.show();
		}
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