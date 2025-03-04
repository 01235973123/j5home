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

<div class="aq-quiz-result">
<?php $this->dtQuestions->render(); ?>
</div>

<input type="hidden" name="StatisticsInfoId" value="<?php echo $this->sid; ?>" />