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

<h1 class="aq-category-title aq-header"><?php echo AriUtils::getParam($this->category, 'CategoryName'); ?></h1>
<div class="aq-category-description">
<?php echo AriUtils::getParam($this->category, 'Description'); ?>
</div>
<br/>
<?php
	if (empty($this->quizzes)): 
?>
	<?php echo JText::_('COM_ARIQUIZ_LABEL_NOQUIZZES'); ?>
<?php 
	else:
		foreach ($this->quizzes as $quiz):
?>
	<a class="aq-quiz-link" href="index.php?option=com_ariquiz&view=quiz&quizId=<?php echo $quiz->QuizId; ?><?php if ($this->itemId):?>&Itemid=<?php echo $this->itemId; ?><?php endif; ?>"><?php echo $quiz->QuizName; ?></a>
	<br/>
<?php
		endforeach;
	endif; 
?>