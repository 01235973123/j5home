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

<div id="categoryTabContainer" class="yui-navset"> 
	<ul class="yui-nav">
		<li class="selected"><a href="#categoryMainSettingsTab" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?></em></a></li>
		<li><a href="#categoryQuestionsPoolTab" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_QUESTIONPOOL'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_QUESTIONPOOL'); ?></em></a></li>
	</ul>
	<div class="yui-content">
		<div class="yui-hidden ari-tab" id="categoryMainSettingsTab">
			<?php echo $this->commonSettingsForm->render('params'); ?>
		</div>
		<div class="yui-hidden ari-tab" id="categoryQuestionsPoolTab">
			
			<table id="tblPoolContainer" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<th style="width: 1%; text-align: center;"><div class="addItemIcon" title="+" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblPoolContainer'); return false;">&nbsp;</div></th>
					<th>&nbsp;</th>
					<th style="width: 5%; text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
				</tr>
				<tbody id="trPool">
					<tr>
						<td colspan="2">
							<?php echo str_replace(array('poolParams[BankCategoryId]', 'poolParams[QuestionCount]'), array('poolParamsBankCategoryId', 'poolParamsQuestionCount'), $this->questionPoolForm->render('poolParams')); ?>
						</td>
						<td>
							<div class="deleteItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_REMOVE'); ?>" onclick="if (confirm('<?php echo JText::_('COM_ARIQUIZ_WARNING_REMOVERESULTSCALEITEM'); ?>')) YAHOO.ARISoft.widgets.multiplierControls.removeItem(YAHOO.ARISoft.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trPool')); return false;">&nbsp;</div>
						</td>
					</tr>
					<tr>
						<td colspan=3"">
							<hr />
						</td>
					</tr>
				</tbody>
			</table>
			<br/>
			<table class="questionNote" cellpadding="0" cellspacing="0">
				<tr>
					<td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
					<td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_POOLNOTE'); ?></td>
				</tr>
			</table>

		</div>
	</div>
</div>

<!-- begin ACL definition-->    
<?php if (!J1_5 && AriQuizHelper::isACLEnabled() && AriQuizHelper::isAuthorise('core.admin')): ?>
<?php
	if (J3_0): 
?>    
<div class="tab-pane" id="permissions">
	<fieldset>
		<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_RULES'); ?></legend>
		<?php echo $this->commonSettingsForm->render('params', 'rules'); ?>
	</fieldset>
</div>
<?php
	else: 
?>    
<div class="clr"></div>    
	<div class="width-100 fltlft">
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-' . $this->quizId, array('useCookie' => 1)); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_ARIQUIZ_LABEL_RULES'), 'access-rules'); ?>
		<fieldset class="panelform">
			<?php echo $this->commonSettingsForm->render('params', 'rules'); ?>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
<?php 
	endif;
endif; 
?>
<!-- end ACL definition-->

<input type="hidden" id="hidCategoryActiveTab" name="categoryActiveTab" value="<?php echo $this->activeTab; ?>" />
<?php
if ($this->quizId > 0): 
?>
<input type="hidden" name="quizId" value="<?php echo $this->quizId; ?>" />
<?php
endif; 
?>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var Dom = YAHOO.util.Dom,
		page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		tabs = new YAHOO.widget.TabView('categoryTabContainer', {'activeIndex': <?php echo $this->activeTab; ?>});
	tabs.on('activeIndexChange', function(e) {
		Dom.get('hidCategoryActiveTab').value = e.newValue;
	});

	YAHOO.ARISoft.widgets.multiplierControls.init(
		'trPool', 
		'tblPoolContainer', 
		3, 
		<?php echo WebControls_MultiplierControls::dataToJson($this->getQuestionPoolData($this->questionPool)); ?>
	);
	YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val) {
				var isValid = true,
					templates = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblPoolContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'trPool'),
					templateCnt = templates ? templates.length : 0; 
				if (templateCnt == 0)
					return isValid;

				for (var i = 0; i < templateCnt; i++) {
					var template = templates[i],
						ddlCategory = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'poolParamsBankCategoryId'),
						tbxQuestionCount = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'poolParamsQuestionCount');

					categoryId = parseInt(ddlCategory.value, 10);
					if (categoryId > 0) {
						var sQuestionCount = YAHOO.lang.trim(tbxQuestionCount.value);
							questionCount = parseInt(sQuestionCount, 10);
						if (sQuestionCount != questionCount || questionCount < 0) {
							isValid = false;
							break;
						}
					}
				}

				return isValid;			
			}, {
				emptyValidate: true, 
				errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_POOLQUESTIONCOUNT', true); ?>'
			}
		)
	); 
		
	YAHOO.ARISoft.page.pageManager.subscribe('beforeAction', function(o) {
		if ((o.action == 'save' || o.action == 'apply') && 
			(typeof(o.config["skipValidation"]) == "undefined" || !o.config["skipValidation"])) 
		{
			var task = o.action;
			YAHOO.ARISoft.validators.alertSummaryValidators.asyncValidate({
				"success": function() {
					YAHOO.ARISoft.page.pageManager.triggerAction(task, {"skipValidation": true});
				}
			});

			return false;
		}
	});
});
</script>