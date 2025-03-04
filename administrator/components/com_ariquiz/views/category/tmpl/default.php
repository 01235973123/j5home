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
		<li><a href="#categoryMetadataTab" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_METADATA'); ?>"><em><?php echo JText::_('COM_ARIQUIZ_LABEL_METADATA'); ?></em></a></li>
	</ul>
	<div class="yui-content">
		<div class="yui-hidden ari-tab" id="categoryMainSettingsTab">
			<?php echo $this->commonSettingsForm->render('params'); ?>
		</div>
		<div class="yui-hidden ari-tab" id="categoryMetadataTab">
			<?php echo $this->commonSettingsForm->render('metadata_params', 'metadata'); ?>
		</div>
	</div>
</div>
<input type="hidden" id="hidCategoryActiveTab" name="categoryActiveTab" value="<?php echo $this->activeTab; ?>" />

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
	var Dom = YAHOO.util.Dom,
	page = YAHOO.ARISoft.page,
	pageManager = page.pageManager,
	tabs = new YAHOO.widget.TabView('categoryTabContainer', {'activeIndex': <?php echo $this->activeTab; ?>});
	tabs.on('activeIndexChange', function(e) {
		Dom.get('hidCategoryActiveTab').value = e.newValue;
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