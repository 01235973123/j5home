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

$tmpl = JRequest::getCmd('tmpl');
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
?>
    <ul class="aq-quizzes">
<?php
		foreach ($this->quizzes as $quiz):
			$quizClass = array('aq-quiz-link');
			if ($this->userId > 0)
				if (isset($this->statusList[$quiz->QuizId]))
				{
					$status = $this->statusList[$quiz->QuizId];
					$passed = $status->Passed;

					if (is_null($passed))
						$quizClass[] = 'aq-quiz-link-nottry';
					else if ($passed == '1')
						$quizClass[] = 'aq-quiz-link-passed';
					else
						$quizClass[] = 'aq-quiz-link-notpassed';
				}
				else
					$quizClass[] = 'aq-quiz-link-nottry';		
?>
	<li>
        <a class="<?php echo join(' ', $quizClass); ?>" href="index.php?option=com_ariquiz&view=quiz&quizId=<?php echo $quiz->QuizId; ?><?php if ($this->itemId):?>&Itemid=<?php echo $this->itemId; ?><?php endif; ?><?php if ($tmpl):?>&tmpl=<?php echo $tmpl; ?><?php endif; ?>"><?php echo $quiz->QuizName; ?></a>
    </li>
<?php
		endforeach;
?>
    </ul>
<?php
	endif; 
?>