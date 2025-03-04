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

<fieldset>
	<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?></legend>
	<?php echo $this->commonSettingsForm->renderSimple('params', array('validationGroup' => array('', 'questionTemplate'))); ?>
</fieldset>
<fieldset>
	<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_QUESTIONSETTINGS'); ?></legend>
	<?php
		$this->questionView->displayView($this->questionViewParams); 
	?>
</fieldset>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	YAHOO.ARISoft.page.pageManager.subscribe('beforeAction', function(o) {
		if ((o.action == 'save' || o.action == 'apply') && 
			(typeof(o.config["skipValidation"]) == "undefined" || !o.config["skipValidation"])) 
		{
			var task = o.action,
				selDisableValidation = YAHOO.util.Dom.get('paramsDisableValidation'),
				disableValidation = (selDisableValidation.value == '1');
			YAHOO.ARISoft.validators.alertSummaryValidators.asyncValidate({
					"success": function() {
						YAHOO.ARISoft.page.pageManager.triggerAction(task, {"skipValidation": true});
					}
				},
				[disableValidation ? 'questionTemplate' : '']
			);

			return false;
		}
	});
});
</script>