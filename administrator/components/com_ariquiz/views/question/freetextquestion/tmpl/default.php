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
?>

<table id="tblQueContainer" class="questionContainer tblQuestion" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<?php
				if (!$this->basedOnBank):
			?>
			<th class="colMin"><div class="addItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_ADDANSWER'); ?>" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblQueContainer'); return false;">&nbsp;</div></th>
			<?php
				endif;
			?>
			<th><?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?></th>
			<th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_PERCENTSCORE'); ?></th>
			<th class="colMin"><?php if (empty($baseOnBank)) { ?><a href="javascript:void(0);" onclick="YAHOO.ARISoft.page.freetextQuestion.switchCI(); return false;"><?php } ?><?php echo JText::_('COM_ARIQUIZ_LABEL_CITEXT'); ?><?php if (empty($baseOnBank)) { ?><input type="checkbox" id="chkCISwitcher" class="hidSwitcher" /></a><?php } ?></th>
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
			<td><input type="text" id="tbxAnswer" name="tbxAnswer" class="text_area" style="width: 95%;" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> /></td>
			<td><input type="text" size="5" name="tbxScore" id="tbxScore" class="ftqScoreControl text_area" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> />&nbsp;<?php echo JText::_('COM_ARIQUIZ_LABEL_PERCENT'); ?>&nbsp;<?php if ($this->basedOnBank) { ?><input type="checkbox" id="chkOverride" name="chkOverride" value="1" alt="Override" title="Override" onclick="YAHOO.ARISoft.page.freetextQuestion.overrideScore(this);" /><input type="hidden" id="hidScore" name="hidScore" /><?php } ?></td>
			<td><input type="checkbox" class="ftqChkCI" id="cbCI" name="cbCI" value="1" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> />
				<input type="hidden" id="hidQueId" name="hidQueId" />
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
<br/>
<table class="questionNote" cellpadding="0" cellspacing="0">
	<tr>
		<td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
		<td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_FTQNOTE'); ?></td>
	</tr>
</table>
<script type="text/javascript">
	YAHOO.ARISoft.page.freetextQuestion =
	{
		CONTAINER_ID: 'tblQueContainer',

		CI_CLASS: 'ftqChkCI',

		CI_SWITCHER_ID: 'chkCISwitcher',
		
		SCORE_CONTROL_CLASS: 'ftqScoreControl',

		switchCI: function()
		{
			var chkSwitcher = YAHOO.util.Dom.get(this.CI_SWITCHER_ID);
			chkSwitcher.checked = !chkSwitcher.checked;

			this.switchAll(chkSwitcher.checked, this.CONTAINER_ID, this.CI_CLASS, 'input');
		},

		switchAll: function(status, cont, className, tagName)
		{
			YAHOO.util.Dom.getElementsByClassName(className, tagName, cont, function(chk)
			{
				chk.checked = status;
			});
		},
		
		overrideScore: function(chkOverride)
		{
			var tbxScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'tbxScore');
			var hidScore = YAHOO.ARISoft.widgets.multiplierControls.getTemplateElement(chkOverride, 'trQueTemplate', 'hidScore');

			tbxScore.disabled = !chkOverride.checked;
			tbxScore.value = hidScore.value;
		}
	};

	YAHOO.ARISoft.widgets.multiplierControls.init('trQueTemplate', 'tblQueContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($specificQuestion->getDataFromXml($questionData, false, $questionOverridenData)); ?>, function()
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
	});
	
	YAHOO.ARISoft.validators.validatorManager.addValidator(
		new YAHOO.ARISoft.validators.customValidator(null,
			function(val)
			{
				var isValid = true;
				var tbxScore = YAHOO.util.Dom.getElementsByClassName(YAHOO.ARISoft.page.freetextQuestion.SCORE_CONTROL_CLASS, 'input', 'tblQueContainer', function(tbxScore)
				{
					if (isValid)
					{
						var sScore = YAHOO.lang.trim(tbxScore.value);
						if (sScore.length > 0)
						{
							var score = parseInt(sScore, 10);
							if (sScore != score || score < 0 || score > 100)
							{
								isValid = false;
							}
						}
					}
				}); 

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
				var isValid = false;
				
				var tbxAnswerList = YAHOO.ARISoft.DOM.getChildElementsByAttribute('tblQueContainer', YAHOO.ARISoft.widgets.multiplierControls.originalIdAttr, 'tbxAnswer');
				if (tbxAnswerList && tbxAnswerList.length)
				{
					for (var i = 0; i < tbxAnswerList.length; i++)
					{
						var value = tbxAnswerList[i].value;
						if (value && value.replace(/^\s+|\s+$/g, '').length > 0)
						{
							isValid = true;
							break;
						}
					}
				}
				
				return isValid;
			},
			{emptyValidate : true, errorMessage : '<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>'}));
<?php
endif;
?>
</script>