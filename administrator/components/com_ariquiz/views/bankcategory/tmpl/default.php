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
<?php echo $this->commonSettingsForm->render('params'); ?>

<!-- begin ACL definition-->
<?php if (AriQuizHelper::isACLEnabled() && AriQuizHelper::isAuthorise('core.admin')): ?>
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
		<?php echo JHtml::_('sliders.start', 'permissions-sliders-' . $this->itemId, array('useCookie' => 1)); ?>
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

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
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