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

<div class="ash-install">
<div><?php echo JText::_('COM_ARIQUIZ_LABEL_INSTALLAPPS'); ?></div>
<?php
	$plgIdx = 0;
	foreach ($this->addons as $addonGroup): 
?>
	<h3><?php echo $addonGroup['label']; ?> [<a href="#" onclick="YAHOO.util.Dom.getElementsByClassName('chk-<?php echo $addonGroup['group']; ?>', null, 'appsContainer<?php echo $addonGroup['group']; ?>', function(el) {el.checked = true;}); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_SELECTALL'); ?></a> | <a href="#" onclick="YAHOO.util.Dom.getElementsByClassName('chk-<?php echo $addonGroup['group']; ?>', null, 'appsContainer<?php echo $addonGroup['group']; ?>', function(el) {el.checked = false;}); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_UNSELECTALL'); ?></a>]</h3>
	<ul id="appsContainer<?php echo $addonGroup['group']; ?>">
	<?php
		foreach ($addonGroup['apps'] as $app): 
	?>
		<li><input type="checkbox" class="chk-<?php echo $addonGroup['group']; ?>" name="<?php echo $addonGroup['group']; ?>[]" id="chkPlg<?php echo $plgIdx; ?>" value="<?php echo $app['package']; ?>"<?php if (!empty($app['checked'])): ?> checked="checked"<?php endif; ?> /><label for="chkPlg<?php echo $plgIdx; ?>"><?php echo $app['name']?> (<?php echo JText::_('COM_ARIQUIZ_LABEL_VER'); ?> <?php echo $app['version']; ?><?php if (!empty($app['currentVersion'])): ?> | <?php echo JText::_('COM_ARIQUIZ_LABEL_INSTALLEDVER'); ?> <?php echo $app['currentVersion']; ?><?php endif; ?>)</label><div class="ash-plg-descr"><?php echo $app['description']; ?></div></li>
	<?php
			++$plgIdx;
		endforeach; 
	?>
	</ul>
<?php 
	endforeach;
?>
<br /><br />
<button class="btn btn-large btn-primary" onclick="Joomla.submitbutton('install')"><?php echo JText::_('COM_ARIQUIZ_LABEL_COMPLETE'); ?> <span class="icon-arrow-right-4"></span></button>
</div>
