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
<fieldset id="fsQuestion">
	<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?></legend>
	<?php echo $this->form->render('params'); ?>
	<?php echo $this->form->render('qv_params', 'questionversion'); ?>
</fieldset>
<fieldset>
	<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_QUESTIONSETTINGS'); ?></legend>
	<?php
		$this->questionView->displayView($this->questionViewParams); 
	?>
</fieldset>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom,
		Event = YAHOO.util.Event;
	
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

	Dom.getElementsByClassName('question_type', 'SELECT', 'fsQuestion', function(el) {
		Event.on(el, 'change', function(e) {
			YAHOO.ARISoft.page.pageManager.submit('changeQuestionType');
		});
	});
});
</script>