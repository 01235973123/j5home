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

<?php
	if (empty($this->quizzes)): 
?>
	<?php echo JText::_('COM_ARIQUIZ_LABEL_NOQUIZZES'); ?>
<?php 
	else:
		$prevCatLevel = -1;
		foreach ($this->categories as $category):
			if ($category->level == 0)
				continue;

			if ($prevCatLevel > -1 && $prevCatLevel == $category->level):
?>
	</div>
<?php
			elseif ($prevCatLevel > -1 && $prevCatLevel > $category->level):
?>
	</div></div>
<?php
			endif;
?>
	<div class="aq-cat-quizzes aq-cat-quizzes-lvl-<?php echo $category->level; ?><?php if ($prevCatLevel == -1): ?> aq-cat-quizzes-lvl-first<?php endif; ?>">
		<div class="aq-cat-name"><?php echo $category->CategoryName; ?></div>
		<?php
			if ($this->showDescription && !empty($category->Description)): 
		?>
		<div class="aq-cat-description"><?php echo $category->Description; ?></div>
		<?php
			endif; 
		?>
<?php
		$catQuizzes = isset($this->quizzes[$category->CategoryId]) ? $this->quizzes[$category->CategoryId] : null;
		if (is_array($catQuizzes)):
?>
		<ul class="aq-quizzes">
<?php
			foreach ($catQuizzes as $quiz):
?>
			<li><a class="aq-quiz-link" href="index.php?option=com_ariquiz&view=quiz&quizId=<?php echo $quiz->QuizId; ?><?php if ($this->itemId):?>&Itemid=<?php echo $this->itemId; ?><?php endif; ?>"><?php echo $quiz->QuizName; ?></a></li>
<?php
			endforeach;
?>
		</ul>
<?php
		endif;
?>
		<br/><br/>
<?php
		$prevCatLevel = $category->level;
		endforeach;

	echo str_repeat('</div>', $prevCatLevel);

	endif; 
?>