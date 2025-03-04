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
		<td class="colMin right"><label for="chkCQRandomizeOrder"><?php echo JText::_('COM_ARIQUIZ_LABEL_RANDOMORDER'); ?></label></td>
		<td class="left"><input type="checkbox" id="chkCQRandomizeOrder" name="chkCQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?><?php if ($this->basedOnBank) echo ' disabled="disabled"'; ?>/></td>
	</tr>
</table>
<br/>
<table id="tblQueContainer" class="correlationQuestionContainer questionContainer tblQuestion" style="width: 100%;" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<?php
				if (!$this->basedOnBank):
			?>
			<th style="width: 1%; text-align: center;"><div class="addItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_ADDANSWER'); ?>" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblQueContainer'); return false;">&nbsp;</div></th>
			<?php
				endif;
			?>
			<th style="text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_QUESTION'); ?></th>
			<th style="text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?></th>
			<?php
				if (!$this->basedOnBank):
			?>
			<th style="width: 5%; text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
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
			<td style="text-align:left;">
				<textarea id="tbxLabel" name="tbxLabel" class="text_area" style="width: 99%;" rows="3" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?>></textarea>
				<?php 
					if (!$this->basedOnBank):
				?>
				<br />
				<a class="aq-answer-editor-link" href="#" onclick="javascript:YAHOO.ARISoft.Quiz.switchAnswerToWYSIWYG('trQueTemplate', 'tbxLabel', this); return false;">Open in editor</a>
				<?php
					endif; 
				?>
				<input type="hidden" id="hidLabelId" name="hidLabelId" />
			</td>
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
				<input type="hidden" id="hidAnswerId" name="hidAnswerId" />
			</td>
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
<br />
<table class="questionNote" cellpadding="0" cellspacing="0">
	<tr>
		<td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
		<td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_EMPTYANSWERIGNORED'); ?></td>
	</tr>
</table>
<script type="text/javascript" language="javascript">
YAHOO.ARISoft.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false)); ?>);
<?php 
if (!$this->basedOnBank):
?>
YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var isNotEmpty = false;

				var templates = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblQueContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'trQueTemplate');
				var templateCnt = templates ? templates.length : 0; 
				if (templateCnt > 0)
				{
					for (var i = 0; i < templateCnt; i++)
					{
						var template = templates[i];
					
						var tbxAnswer = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxAnswer');
						var tbxLabel = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxLabel');
						var ans = tbxAnswer.value;
						var lbl = tbxLabel.value;
						if (ans && ans.replace(/^\s+|\s+$/g, '').length > 0 && 
							lbl && lbl.replace(/^\s+|\s+$/g, '').length > 0)
						{
							isNotEmpty = true;
							break;
						}
					}
				}

				if (!isNotEmpty)
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