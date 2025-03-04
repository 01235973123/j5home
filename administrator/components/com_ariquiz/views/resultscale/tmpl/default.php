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

<?php echo $this->form->render('params'); ?>

<table id="tblScaleContainer" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th style="width: 1%; text-align: center;"><div class="addItemIcon" title="+" onclick="YAHOO.ARISoft.widgets.multiplierControls.addItem('tblScaleContainer'); return false;">&nbsp;</div></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th style="width: 5%; text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
	</tr>
	<tbody id="trScaleTemplate">
		<tr valign="top">
			<td colspan="3">
				<?php echo $this->form->render('', 'scaleitem', true, true); ?>
			</td>
			<td style="text-align: center; white-space: nowrap;">
				<div style="text-align: center;">
					<div class="deleteItemIcon" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_REMOVE'); ?>" onclick="if (confirm('<?php echo JText::_('COM_ARIQUIZ_WARNING_REMOVERESULTSCALEITEM'); ?>')) YAHOO.ARISoft.widgets.multiplierControls.removeItem(YAHOO.ARISoft.widgets.multiplierControls.getCurrentTemplateItemId(this, 'trScaleTemplate')); return false;">&nbsp;</div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<hr />
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager;

	YAHOO.ARISoft.widgets.multiplierControls.init('trScaleTemplate', 'tblScaleContainer', 3, <?php echo WebControls_MultiplierControls::dataToJson($this->scaleItemsData); ?>);
	pageManager.subscribe('beforeAction', function(o) {
		if ((o.action == 'save' || o.action == 'apply') && 
			(typeof(o.config["skipValidation"]) == "undefined" || !o.config["skipValidation"])) 
		{
			var task = o.action;
			YAHOO.ARISoft.validators.alertSummaryValidators.asyncValidate({
				"success": function() {
					pageManager.triggerAction(task, {"skipValidation": true});
				}
			});

			return false;
		}
	});
});
</script>