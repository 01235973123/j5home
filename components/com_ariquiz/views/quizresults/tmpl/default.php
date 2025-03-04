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

<h1 class="aq-results-title aq-header"><?php echo JText::_('COM_ARIQUIZ_LABEL_QUIZZESRESULTS'); ?></h1>

<?php $this->dtResults->render(); ?>