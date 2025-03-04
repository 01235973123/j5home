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

$specificQuestion = $this->specificQuestion;
$questionData = $this->questionData;
$questionOverridenData = $this->questionOverridenData;
$extraData = $specificQuestion->getExtraDataFromXml($questionData);
?>

<table class="questionContainer" cellpadding="0" cellspacing="0">
	<tr>
		<td class="colMin right"><label for="chkSQRandomizeOrder"><?php echo JText::_('COM_ARIQUIZ_LABEL_RANDOMORDER'); ?></label></td>
		<td class="left"><input type="checkbox" id="chkSQRandomizeOrder" name="chkSQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?><?php if ($this->basedOnBank) echo ' disabled="disabled"'; ?>/></td>
	</tr>
	<tr>
		<td class="right"><label for="ddlSQView"><?php echo JText::_('COM_ARIQUIZ_LABEL_VIEWTYPE'); ?></label></td>
		<td class="left">
			<select class="text_area" id="ddlSQView" name="ddlSQView"<?php if ($this->basedOnBank) echo ' disabled="disabled"'; ?>>
				<option value="<?php echo ARIQUIZ_SINGLEQUESTION_VIEWTYPE_RADIO; ?>"><?php echo JText::_('COM_ARIQUIZ_LABEL_VIEWTYPE_RADIO'); ?></option>
				<option value="<?php echo ARIQUIZ_SINGLEQUESTION_VIEWTYPE_DROPDOWN; ?>"<?php if (!empty($extraData['view']) && $extraData['view'] == ARIQUIZ_SINGLEQUESTION_VIEWTYPE_DROPDOWN) echo ' selected="selected"'; ?>><?php echo JText::_('COM_ARIQUIZ_LABEL_VIEWTYPE_DROPDOWN'); ?></option>
			</select>
		</td>
	</tr>
</table>
<br/>
<table id="tblQueContainer" class="singleQuestionContainer questionContainer tblQuestion" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<?php
				if (!$this->basedOnBank):
			?>
			<th class="colMin"><div class="addItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_ADDANSWER'); ?>" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblQueContainer'); YAHOO.ARISoft.page.singleQuestion.updateHidCorrect();return false;">&nbsp;</div></th>
			<?php
				endif;
			?>
			<th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_CORRECT'); ?></th>
			<th><?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?></th>
			<th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_PERCENT'); ?></th>
			<?php
				if (!$this->basedOnBank):
			?>
			<th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
			<?php
				endif;
			?>
		</tr>
	</thead>
	<tbody>
		<tr id="trQueTemplate">
			<?php
				if (!$this->basedOnBank):
			?>
			<td>&nbsp;</td>
			<?php
				endif;
			?>
			<td><input type="radio" onclick="YAHOO.ARISoft.page.singleQuestion.updateHidCorrect();" name="rbCorrect" id="rbCorrect" value="true" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> /></td>
			<td style="text-align:left;">
				<textarea id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 99%;" rows="3" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?>></textarea>
				<?php 
					if (!$this->basedOnBank):
				?>
				<br />
				<a class="aq-answer-editor-link" href="#" onclick="javascript:YAHOO.ARISoft.Quiz.switchAnswerToWYSIWYG('trQueTemplate', 'tbxAnswer', this); return false;">Open in editor</a>
				<?php
					endif; 
				?>
				<input type="hidden" id="hidQueId" name="hidQueId" />
				<input type="hidden" id="hidCorrect" name="hidCorrect" />
			</td>
			<td><input type="text" size="5" name="tbxScore" id="tbxScore" class="text_area" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> />&nbsp;<?php echo JText::_('COM_ARIQUIZ_LABEL_PERCENT'); ?>&nbsp;<?php if ($this->basedOnBank) { ?><input type="checkbox" id="chkOverride" name="chkOverride" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.singleQuestion.overrideScore(this);" /><input type="hidden" id="hidScore" name="hidScore" /><?php } ?></td>
			<?php
				if (!$this->basedOnBank):
			?>
			<td>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>
							<div class="deleteItemIcon" onclick="if (confirm('<?php echo JText::_('COM_ARIQUIZ_WARNING_QUESTIONANSWERREMOVE'); ?>')) YAHOO.ARISoft.widgets.multiplierControls.removeItem(YAHOO.ARISoft.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trQueTemplate')); return false;" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_REMOVE'); ?>">&nbsp;</div>
						</td>
						<td>
							<div class="upItemIcon" onclick="YAHOO.ARISoft.widgets.multiplierControls.moveUpItem(this, 'trQueTemplate'); return false;" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_UP'); ?>">&nbsp;</div>
						</td>
						<td>
							<div class="downItemIcon" onclick="YAHOO.ARISoft.widgets.multiplierControls.moveDownItem(this, 'trQueTemplate', 'tblQueContainer'); return false;" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_DOWN'); ?>">&nbsp;</div>
						</td>
					</tr>
				</table>
			</td>
			<?php
				endif;
			?>
		</tr>
	</tbody>
</table>
<br/>
<table class="questionNote" cellpadding="0" cellspacing="0">
	<tr>
		<td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
		<td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_SQNOTE'); ?></td>
	</tr>
</table>
<script type="text/javascript">
	YAHOO.ARISoft.page.singleQuestion =
	{
		overrideScore: function(chkOverride)
		{
			var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'tbxScore');
			var hidScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'hidScore');
			
			tbxScore.disabled = !chkOverride.checked;
			tbxScore.value = hidScore.value;   
		},
		
		updateHidCorrect: function()
		{
			var rbCorrectList = YAHOO.ARISoft.widgets.multiplierControls.getContainerElements('tblQueContainer', 'rbCorrect');
			if (rbCorrectList)
			{
				for (var i = 0; i < rbCorrectList.length; i++)
				{
					rbCorrectList[i].onclick = this.setCorrect;
					rbCorrectList[i].onchange = this.setCorrect;
				}
			}
		},
		
		setCorrect: function(e)
		{
			e = e || event;
			var ctrl = e.srcElement || e.target;
		
			var hidCorrectList = YAHOO.ARISoft.widgets.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect');
			for (var i = 0; i < hidCorrectList.length; i++)
			{
				hidCorrectList[i].value = '';
			};
				
			var curHidCorrect = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(ctrl, 'trQueTemplate', 'hidCorrect');
			if (curHidCorrect)
			{
				curHidCorrect.defaultValue = 'true';
				curHidCorrect.value = 'true';
			}
			
			YAHOO.ARISoft.page.singleQuestion.updateCorrectScore(ctrl, true);
		},
		
		updateCorrectScore: function(corEl, clearPrev)
		{
			if (clearPrev)
			{
				var tbxScoreList = YAHOO.ARISoft.widgets.multiplierControls.getContainerElements('tblQueContainer', 'tbxScore');
				for (var i = 0; i < tbxScoreList.length; i++)
				{
					var curTbxScore = tbxScoreList[i];
					if (curTbxScore.disabled)
					{
						curTbxScore.value = '';
						curTbxScore.disabled = false;
					}
				}
			}
		
			var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(corEl, 'trQueTemplate', 'tbxScore');
			
			tbxScore.value = 100;
			tbxScore.disabled = true;
			
			var chkOverride = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(corEl, 'trQueTemplate', 'chkOverride');
			if (chkOverride) chkOverride.disabled = true;
		}
	};

	YAHOO.ARISoft.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false, $questionOverridenData)); ?>,
		function()
		{
			var chkOverrideList = YAHOO.ARISoft.widgets.multiplierControls.getContainerElements('tblQueContainer', 'chkOverride');
			for (var i = 0; i < chkOverrideList.length; i++)
			{
				if (chkOverrideList[i].checked)
				{
					var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverrideList[i], 'trQueTemplate', 'tbxScore');
					tbxScore.disabled = false;
				}
			};

			var hidCorrectList = YAHOO.ARISoft.widgets.multiplierControls.getContainerElements('tblQueContainer', 'hidCorrect');
			for (var i = 0; i < hidCorrectList.length; i++)
			{
				if (hidCorrectList[i].value == 'true')
				{
					var selRbCorrect = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(hidCorrectList[i], 'trQueTemplate', 'rbCorrect');
					selRbCorrect.defaultChecked = true;
					selRbCorrect.checked = true;
					
					YAHOO.ARISoft.page.singleQuestion.updateCorrectScore(selRbCorrect);
					break;
				}
			};
			
			YAHOO.ARISoft.page.singleQuestion.updateHidCorrect();
		});
		
	YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var templates = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblQueContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'trQueTemplate');
				var templateCnt = templates ? templates.length : 0;  
				if (templateCnt > 0)
				{
					for (var i = 0; i < templateCnt; i++)
					{
						var template = templates[i];
						var tbxScore = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxScore');
						var sScore = YAHOO.lang.trim(tbxScore.value);
						if (sScore.length == 0) continue;

						var score = parseInt(sScore, 10);
						if (sScore != score || score < 0 || score > 100)
						{
							isValid = false;
							break;
						}
					}
				}

				return isValid;
			},
			{emptyValidate : true, errorMessage : '<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTSETPERCENTSCORE', true); ?>'}));
<?php 
if (!$this->basedOnBank):
?>
	YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var isSetCorrect = false;
				var isNotEmpty = false;

				var templates = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblQueContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'trQueTemplate');
				var templateCnt = templates ? templates.length : 0; 
				if (templateCnt > 0)
				{
					for (var i = 0; i < templateCnt; i++)
					{
						var template = templates[i];
						var rbCorrect = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'rbCorrect');
						if (rbCorrect && rbCorrect.checked)
						{
							isSetCorrect = true;						
							var tbxAnswer = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxAnswer');
							var value = tbxAnswer.value;
							if (value && value.replace(/^\s+|\s+$/g, '').length > 0)
							{
								isNotEmpty = true;
								break;
							}
						}
					}
					
					if (!isSetCorrect)
					{
						this.errorMessage = YAHOO.ARISoft.core.getNormalizeValue('<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTCORRECT', true); ?>');
						isValid = false;
					}
					else if (!isNotEmpty)
					{
						this.errorMessage = YAHOO.ARISoft.core.getNormalizeValue('<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>');
						isValid = false;
					}
				}
				else
				{
					this.errorMessage = YAHOO.ARISoft.core.getNormalizeValue('<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>');
					isValid = false;
				}
				
				return isValid;
			},
			{emptyValidate : true, errorMessage : '<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>'}));
<?php
endif;
?>
</script>