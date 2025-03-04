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

<div class="aq-no-print">
<a href="#" class="btn aq-btn-doprint" onclick="window.print();return false;"><i class="icon-print"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_PRINT'); ?></a>
<br /><br />
</div>
<?php echo $this->content; ?>
<?php
if (isset($this->dtResults)) $this->dtResults->render(array('class' => 'aq-dt-results')); 
?>