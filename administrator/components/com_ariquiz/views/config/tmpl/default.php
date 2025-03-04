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

<div id="configTabContainer" class="yui-navset"> 
	<ul class="yui-nav">
		<li class="selected"><a href="#mainSettingsTab" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_MAINSETTINGS'); ?></em></a></li>
        <li><a href="#socialSettingsTab" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_SOCIALSETTINGS'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_SOCIALSETTINGS'); ?></em></a></li>
    </ul>
	<div class="yui-content">
		<div class="yui-hidden ari-tab" id="mainSettingsTab">
			<?php echo $this->form->render('params'); ?>
		</div>
        <div class="yui-hidden ari-tab" id="socialSettingsTab">
            <?php echo $this->form->render('params', 'social'); ?>
        </div>
	</div>
</div>
<input type="hidden" id="hidConfigActiveTab" name="quizActiveTab" value="<?php echo $this->activeTab; ?>" />
<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var Dom = YAHOO.util.Dom,
		page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		tabs = new YAHOO.widget.TabView('configTabContainer', {'activeIndex': <?php echo $this->activeTab; ?>});
	tabs.on('activeIndexChange', function(e) {
		Dom.get('hidConfigActiveTab').value = e.newValue;
	});

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