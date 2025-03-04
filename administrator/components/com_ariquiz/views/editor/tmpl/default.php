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

<?php echo $this->editor->display('editor', null, '99%', '300', 60, 30); ?>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom;

	pageManager.editorManager = {
		setContent: function(content) {
			<?php
				if (J4) {
			?>
			setTimeout(function() { Joomla.editors.instances['editor'].setValue(content); }, 300);
			<?php
				} else {
					echo $this->editor->setContent('editor', 'content');
				}
			?>
		},

		applyContentEvent: new YAHOO.util.CustomEvent('applyContent'),

		applyContent: function() {
			var content = <?php if (J4) { ?>Joomla.editors.instances['editor'].getValue();<?php } else { echo $this->editor->getContent('editor', 'content'); } ?>;

			this.applyContentEvent.fire({'Content': content});
		}
	};
});
</script>