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
                    <input type="button" class="button btn" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('resetFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CLEAR'); ?>" />
				</td>
			</tr>
		</table>
	</div>
</div>

<?php $this->dtQuestions->render(); ?>

<div id="panelAddQuestion" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_NEWQUESTIONSETTINGS'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;">	
		<?php echo $this->typeForm->render('type'); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CONTINUE'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyAddQuestion');" />
			<input type="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelAddQuestion.hide();" />
		</div>
	</div>
</div>

<div id="panelMassEdit" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDIT'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;" id="tblMassSettings">
		<?php echo $this->massEditform->renderSimple('massParams', array('validationGroup' => 'massEditActive')); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMassEdit');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CLEAR'); ?>" onclick="this.form.reset();YAHOO.ARISoft.page.massEditSettings.resetSettings();" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelMassEdit.hide(); return false;" />
		</div>
		<div style="text-align: left;">
			<br/>
			<b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b> <?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDITNOTE'); ?>
		</div>
	</div>
</div>

<div id="panelCSVImport" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_CSVIMPORT'); ?></div>
	<div class="bd" style="text-align: center; overflow: auto;">
		<fieldset>
			<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_UPLOADEXPORTFILE'); ?></legend>
			<div>
				<input type="file" id="importDataCSVFile" name="importDataCSVFile" class="text_area" size="50" />
				<input type="button" class="button btn-primary" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('doUploadCSVImport'); return false;" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_IMPORT'); ?>" />
			</div>
		</fieldset>
		<fieldset>
			<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_IMPORTEXPORTFILEFROMDIR'); ?></legend>
			<div>
				<input type="text" id="importDataCSVDir" name="importDataCSVDir" class="text_area" size="50" value="<?php echo JPATH_ROOT; ?>" />
				<input type="button" class="button btn-primary" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('doImportCSVFromDir'); return false;" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_IMPORT'); ?>" />
			</div>
		</fieldset>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelCSVImport.hide(); return false;" />
		</div>
	</div>
</div>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom,
		aDom = YAHOO.ARISoft.DOM;

	page.panelAddQuestion = new YAHOO.widget.Panel("panelAddQuestion", { 
		width: "450px", 
		visible: false, 
		constraintoviewport: true, 
		modal: true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelAddQuestion.render();

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

	page.panelCSVImport = new YAHOO.widget.Panel("panelCSVImport", { 
		width:"600px", 
		visible:false,
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelCSVImport.render();

	pageManager.registerActionGroup('questionAction', {
		query: {"view": "bankquestions"},
		onAction: page.actionHandlers.simpleDatatableAction,
		dataTable: "<?php echo $this->dtQuestions->id; ?>",
		enableValidation: true,
		errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
		completeMessage: "",
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxFilters', {
		group: 'questionAction'
	});
	pageManager.registerAction('addQuestion', {
		onAction: function() {
			page.panelAddQuestion.show();
		}
	});
	pageManager.registerAction('applyAddQuestion', {
		onAction: function() {
			page.panelAddQuestion.hide();
		
			YAHOO.ARISoft.page.pageManager.triggerAction('add');
		}
	});

	pageManager.registerAction('ajaxMassEdit', {
		onAction: function(action, config) {
			page.settingAdvCheckboxes.renew();
			config.postData = YAHOO.util.Connect.setForm('frmMassEdit');
		
			page.actionHandlers.simpleDatatableAction.call(this, action, config);
		},
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_MASSEDIT', true); ?>'
	});
	pageManager.registerAction('ajaxDelete', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONDELETE', true); ?>'
	});
	pageManager.registerAction('applyMassEdit', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['massEdit'])) 
				return ;

			page.panelMassEdit.hide();
			pageManager.triggerAction('ajaxMassEdit');
		}
	});
    pageManager.registerAction('resetFilters', {
       onAction: function() {
           Dom.get('filterId').value = '';
           Dom.get('filterCategoryId').value = '0';

           YAHOO.ARISoft.page.pageManager.triggerAction('ajaxFilters');
       }
    });
	pageManager.registerAction('mass_edit', {
		onAction: function() {
			page.panelMassEdit.show();
		}
	});

	pageManager.registerAction('csv_import', {
		onAction: function() {
			page.panelCSVImport.show();
		}
	});
	pageManager.registerAction('csv_export', {
		onAction: function() {
			pageManager.triggerAction('exportCSV');
		}
	});
	pageManager.registerAction('doUploadCSVImport', {
		onAction: function() {
			if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['ImportUpload']))
				pageManager.triggerAction('uploadCSVImport');
		}
	});
	pageManager.registerAction('doImportCSVFromDir', {
		onAction: function() {
			if (YAHOO.ARISoft.validators.alertSummaryValidators.validate(['ImportDir']))
				pageManager.triggerAction('importCSVFromDir');
		}
	});
});

YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.requiredValidator('importDataCSVFile', {
		"validationGroups": ['ImportUpload'], 
		"errorMessage": '<?php echo JText::_('COM_ARIQUIZ_MESSAGE_IMPORTFILEREQUIRED', true); ?>'
	})
);
YAHOO.ARISoft.validators.validatorManager.addValidator(	
	new YAHOO.ARISoft.validators.requiredValidator('importDataCSVDir', {
		"errorMessage":'<?php echo JText::_('COM_ARIQUIZ_MESSAGE_IMPORTDIRREQUIRED', true); ?>',
		"validationGroups":["ImportDir"]
	})
);
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