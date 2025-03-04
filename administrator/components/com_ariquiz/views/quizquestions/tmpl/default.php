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
                    <input type="button" class="button btn-primary" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('ajaxFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" />
                    <input type="button" class="button" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('resetFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CLEAR'); ?>" />
                </td>
            </tr>
        </table>
    </div>
</div>

<?php $this->dtQuestions->render(); ?>

<div id="panelAddQuestion" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_NEWQUESTIONSETTINGS'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;">
		<div id="newQuestionTabContainer" class="yui-navset"> 
			<ul class="yui-nav" style="text-align: left;">
				<li class="selected"><a href="#newQuestionQuestionType" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_STANDARDQUESTION'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_STANDARDQUESTION'); ?></em></a></li>
				<li><a href="#newQuestionQuestionTemplate" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_TEMPLATEBASEDQUESTION'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_TEMPLATEBASEDQUESTION'); ?></em></a></li>
			</ul>
			<div class="yui-content">
				<div class="yui-hidden ari-tab" id="newQuestionQuestionType">
					<?php echo $this->typeForm->render('type'); ?>
				</div>
				<div class="yui-hidden ari-tab" id="newQuestionQuestionTemplate">
					<?php echo $this->templateForm->renderSimple('questionTemplate', array('validationGroup' => 'template')); ?>
				</div>
			</div>
		</div>
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
			<b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?> :</b> <?php echo JText::_('COM_ARIQUIZ_LABEL_MASSEDITNOTE'); ?>
		</div>
	</div>
</div>

<div id="panelCSVImport" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_CSVIMPORT'); ?></div>
	<div class="bd" style="text-align: center; overflow: auto;">
		<fieldset>
			<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_UPLOADEXPORTFILE'); ?></legend>
			<div>
				<input type="file" id="importDataCSVFile" name="importDataCSVFile" class="text_area" size="60" />
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

<div id="panelBankCopy" style="visibility: hidden;">   
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_COPYTOBANK'); ?></div>  
	<div class="bd" style="text-align: center;">
		<?php echo $this->copyToBankForm->render('copyToBank'); ?> 
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyBankCopy');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelBankCopy.hide(); return false;" />
		</div>
	</div>
</div>

<div id="panelCopy" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_COPYSETTINGS'); ?></div>  
	<div class="bd" style="text-align: center;">
		<?php echo $this->copyForm->renderSimple('copy', array('validationGroup' => 'copy')); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyCopy');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelCopy.hide(); return false;" />
		</div>
	</div>
</div>

<div id="panelMove" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_MOVESETTINGS'); ?></div>  
	<div class="bd" style="text-align: center;">
		<?php echo $this->moveForm->renderSimple('move', array('validationGroup' => 'move')); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('applyMove');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelMove.hide(); return false;" />
		</div>
	</div>
</div>

<div id="panelBankImport" style="visibility: hidden;">   
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_IMPORTFROMBANK'); ?></div>  
	<div class="bd" style="text-align: center;">
		<fieldset>
			<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_FILTER'); ?></legend>
			<div id="tblBankFilter">
				<?php echo $this->fromBankFilterForm->renderSimple('fromBankFilter', array('paramsPerRow' => 2)); ?>
			</div>
		</fieldset>
		<fieldset>
			<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_SETTINGS'); ?></legend>
			<div id="tblBankSettings">
				<?php echo $this->fromBankSettingsForm->renderSimple('fromBankSettings', array('validationGroup' => 'fromBank', 'paramsPerRow' => 2)); ?>
			</div>
		</fieldset>
		<?php $this->dtBank->render(); ?>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button btn-primary" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_IMPORT'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('importFromBank');" />
			<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelBankImport.hide(); return false;" />
		</div>
	</div>
</div>

<input type="hidden" name="quizId" value="<?php echo $this->quizId; ?>" />
<input type="hidden" id="newQuestionType" name="newQuestionType" value="newQuestionQuestionType" />
<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var AS = YAHOO.ARISoft,
		page = AS.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom,
		aDom = AS.DOM,
		tabs = new YAHOO.widget.TabView('newQuestionTabContainer', {'activeIndex': 0});

	aDom.moveTo('panelBankImport');
	aDom.wrapWithElement('form', 'tblBankSettings', {id: 'frmBankSettings', name: 'frmBankSettings'});
	aDom.wrapWithElement('form', '<?php echo $this->dtBank->id; ?>', {id: 'frmBankData', name: 'frmBankData'});

	YAHOO.util.Dom.getElementsBy(function(el) {
		return (el.tagName.toLowerCase() == 'select');
	}, 'SELECT', 'tblBankFilter', function(el) {
		YAHOO.util.Event.on(el, 'change', function() {
			YAHOO.ARISoft.page.pageManager.triggerAction('apply_bankfilter');
		});
	});

	page.bankFilterManager = new page.dataFilterManager({container: 'tblBankFilter'});
	(function() {
		var dt = YAHOO.ARISoft.widgets.DataTableManager.getTable('<?php echo $this->dtBank->id; ?>'),
			ds = dt.getDataSource(),
			oldHandler = ds.sendRequest; 
		ds.sendRequest = function(oRequest, oCallback, oCaller) {
			var filterValues = YAHOO.ARISoft.page.bankFilterManager.getFilterValues();
			for (var filterKey in filterValues) {
				var filterValue = filterValues[filterKey];
				oRequest += '&' + filterKey + '=' + encodeURIComponent(filterValue);
			}
					
			dt.showTableMessage(dt.get("MSG_LOADING"), dt.CLASS_LOADING);
					
			oldHandler.call(this, oRequest, oCallback, oCaller);
		};
	})();
	
	page.panelBankImport = new YAHOO.widget.Panel("panelBankImport", { 
		width:"950px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelBankImport.render();
	page.panelBankNeedReload = false;
	
	page.panelAddQuestion = new YAHOO.widget.Panel("panelAddQuestion", { 
		width: "550px", 
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

	page.panelBankCopy = new YAHOO.widget.Panel("panelBankCopy", { 
		width:"550px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelBankCopy.render();

	page.panelCopy = new YAHOO.widget.Panel("panelCopy", { 
		width:"450px", 
		visible:false, 
		constraintoviewport:true,
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelCopy.render();
		
	page.panelMove = new YAHOO.widget.Panel("panelMove", { 
		width:"450px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelMove.render();		

	tabs.on('activeIndexChange', function(e) {
		var tab = tabs.getTab(e.newValue),
			contentEl = tab.get('contentEl');

		Dom.get('newQuestionType').value = contentEl.id;
	});

	pageManager.registerAction('apply_bankfilter', {
		onAction: function() {
			page.bankFilterManager.saveFilterValues();
			YAHOO.ARISoft.widgets.DataTableManager.refresh('<?php echo $this->dtBank->id; ?>', true);
		}
	});
			
	pageManager.registerActionGroup('questionAction', {
		query: {"view": "quizquestions"},
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
	pageManager.registerAction('ajaxDelete', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONDELETE', true); ?>'
	});
	pageManager.registerAction('ajaxOrderUp', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_CHANGEQUESTIONORDER', true); ?>'
	});
	pageManager.registerAction('ajaxOrderDown', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_CHANGEQUESTIONORDER', true); ?>'
	});
	pageManager.registerAction('ajaxActivate', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxDeactivate', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONDEACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxSingleActivate', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONACTIVATE', true); ?>'
	});
	pageManager.registerAction('ajaxSingleDeactivate', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONDEACTIVATE', true); ?>'
	});
	pageManager.registerAction('addQuestion', {
		onAction: function() {
			page.panelAddQuestion.show();
		}
	});
	pageManager.registerAction('to_bank', {
		onAction: function() {
			page.panelBankCopy.show();
		}
	});	
	pageManager.registerAction('from_bank', {
		onAction: function() {
			if (page.panelBankNeedReload) {
				page.panelBankNeedReload = false;
				YAHOO.ARISoft.widgets.DataTableManager.refresh('<?php echo $this->dtBank->id; ?>', true);
			}

			page.panelBankImport.show();
		}
	});
				
	pageManager.registerAction('applyAddQuestion', {
		onAction: function() {
			var tabIndex = tabs.get('activeIndex');
			if (tabIndex == 1 && !YAHOO.ARISoft.validators.alertSummaryValidators.validate(['template']))
				return ;
		
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
	pageManager.registerAction('ajaxCopyToBank', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONBANKCOPY', true); ?>'
	});
	pageManager.registerAction('ajaxCopy', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONCOPY', true); ?>'
	});
	pageManager.registerAction('ajaxMove', {
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_QUESTIONMOVE', true); ?>'
	});
	pageManager.registerAction('applyMassEdit', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['massEdit'])) 
				return ;

			page.panelMassEdit.hide();
			pageManager.triggerAction('ajaxMassEdit');
		}
	});
	pageManager.registerAction('ajaxImportFromBank', {
		onAction: function(action, config) {
			config.postData = YAHOO.util.Connect.setForm('frmBankSettings');
			var query = YAHOO.util.Connect.setForm('frmBankData');
			if (query) {
				if (config.postData) 
					config.postData += '&' + query;
				else 
					config.postData = query;
			}
				
			page.actionHandlers.simpleDatatableAction.call(this, action, config);
		},
		group: 'questionAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_IMPORTFROMBANK', true); ?>'
	});
			
	pageManager.registerAction('importFromBank', {
		onAction: function() {
			if (!AS.validators.alertSummaryValidators.validate(['fromBank'])) 
				return ;

			page.panelBankNeedReload = true;
			page.panelBankImport.hide();
			pageManager.triggerAction('ajaxImportFromBank');
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
	pageManager.registerAction('copy', {
		onAction: function() {
			page.panelCopy.show();
		}
	});
	pageManager.registerAction('move', {
		onAction: function() {
			page.panelMove.show();
		}
	});
			
	pageManager.registerAction('doUploadCSVImport', {
		onAction: function() {
			if (AS.validators.alertSummaryValidators.validate(['ImportUpload']))
				pageManager.triggerAction('uploadCSVImport');
		}
	});
	pageManager.registerAction('doImportCSVFromDir', {
		onAction: function() {
			if (AS.validators.alertSummaryValidators.validate(['ImportDir']))
				pageManager.triggerAction('importCSVFromDir');
		}
	});
	pageManager.registerAction('applyBankCopy', {
		onAction: function() {
			page.panelBankCopy.hide();
			pageManager.triggerAction('ajaxCopyToBank');
		}
	});
	pageManager.registerAction('applyCopy', {
		onAction: function() {
			if (!AS.validators.alertSummaryValidators.validate(['copy'])) 
				return ;

			page.panelCopy.hide();
			pageManager.triggerAction('ajaxCopy');
		}
	});
	pageManager.registerAction('applyMove', {
		onAction: function() {
			if (!AS.validators.alertSummaryValidators.validate(['move'])) 
				return ;

			page.panelMove.hide();
			pageManager.triggerAction('ajaxMove');
		}
	});
    pageManager.registerAction('resetFilters', {
        onAction: function() {
            Dom.get('filterId').value = '';

            YAHOO.ARISoft.page.pageManager.triggerAction('ajaxFilters');
        }
    });
    pageManager.subscribe('beforeAction', function(o) {
		if (o.action == 'ajaxDelete') 
			page.panelBankNeedReload = true;
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
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.customValidator('cvMassEditCount',
		function(val) {
			var dt = YAHOO.ARISoft.widgets.DataTableManager.getTable('<?php echo $this->dtBank->id; ?>');
			return dt.isCheckedCheckboxField('yui-dt-col-BankQuestionId');
		}, {
			validationGroups: ['fromBank'], 
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_SELECTATLEASTONEITEM', true); ?>'
		}
	)
);
</script>