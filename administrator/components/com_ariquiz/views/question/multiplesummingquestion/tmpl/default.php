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
		<td class="colMin right"><label for="chkMSQRandomizeOrder"><?php echo JText::_('COM_ARIQUIZ_LABEL_RANDOMORDER'); ?></label></td>
		<td class="left"><input type="checkbox" id="chkMSQRandomizeOrder" name="chkMSQRandomizeOrder" value="1" <?php if (!empty($extraData['randomizeOrder'])) echo 'checked="checked" '; ?><?php if ($this->basedOnBank) echo ' disabled="disabled"'; ?>/></td>
	</tr>
</table>
<br/>
<table id="tblQueContainer" class="questionContainer tblQuestion" cellpadding="0" cellspacing="0">
	<thead>
		<tr id="trMQHeader">
			<?php
				if (!$this->basedOnBank):
			?>
			<th class="colMin"><div class="addItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_ADDANSWER'); ?>" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblQueContainer'); return false;">&nbsp;</div></th>
			<?php
				endif;
			?>
			<th><?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?></th>
			<th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_SCORE'); ?></th>
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
		<tr id="trQueTemplate" class="mqTemplate">
			<?php
				if (!$this->basedOnBank):
			?>
			<td>&nbsp;</td>
			<?php
				endif;
			?>
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
			</td>
			<td>
				<input type="text" name="tbxMSQScore" id="tbxMSQScore" class="text_area" size="4" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> />
				<?php if (!empty($baseOnBank) && false): ?>
					<input type="checkbox" id="chkOverride" name="chkOverride" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.multipleSummingQuestion.overrideScore(this);" /><input type="hidden" id="hidScore" name="hidScore" />
				<?php endif; ?>
			</td>
			<?php
				if (!$this->basedOnBank)
				{
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
				}
			?>
		</tr>
	</tbody>
</table>
<br/>
<table class="questionNote" cellpadding="0" cellspacing="0">
	<tr>
		<td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
		<td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_MSQNOTE'); ?></td>
	</tr>
</table>
<script type="text/javascript" language="javascript">
	YAHOO.ARISoft.page.multipleSummingQuestion =
	{
		overrideScore: function(chkOverride)
		{
			var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'tbxMSQScore');
			var hidScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'hidScore');

			tbxScore.disabled = !chkOverride.checked;
			tbxScore.value = hidScore.value;
		}
	};

	YAHOO.ARISoft.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false)); ?>);

<?php 
if (!$this->basedOnBank):
?>
	YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var isNotCorrectScore = false;
				var isNotEmpty = false;

				var templates = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblQueContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'trQueTemplate');
				var templateCnt = templates ? templates.length : 0; 
				if (templateCnt > 0) {
					for (var i = 0; i < templateCnt; i++) {
						var template = templates[i],
							tbxScore = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxMSQScore'),
							tbxAnswer = YAHOO.ARISoft.DOM.getChildElementByAttribute(template, YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxAnswer'),
							answer = YAHOO.lang.trim(tbxAnswer.value),
							sScore = YAHOO.lang.trim(tbxScore.value);

						if (answer.length) 
							isNotEmpty = true;

						if (sScore.length > 0) {
							var score = parseFloat(sScore);
							if (sScore != score) {
								isNotCorrectScore = true;
								break;
							}
						}
					}
					
					if (isNotCorrectScore) {
						this.errorMessage = YAHOO.ARISoft.core.getNormalizeValue('<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONSCORE', true); ?>');
						isValid = false;
					} else if (!isNotEmpty) {
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