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

$tmpl = JRequest::getString('tmpl');
?>

<h1 class="aq-quiz-title aq-header"><?php echo $this->quiz->QuizName; ?></h1>
<br/>
<?php
	if (empty($this->isError) && $this->formView):
		$this->formView->displayView($this->quiz); 
?>
<br/><br/>
<?php
	endif; 
?>
<?php
	if ($this->quiz->Description): 
?>
<div class="aq-quiz-description"><?php echo $this->quiz->Description; ?></div>
<br/><br/>
<?php
	endif; 
?>

<?php
	if (empty($this->isError)): 
?>
<a href="#" onclick="if (YAHOO.ARISoft.validators.alertSummaryValidators.validate()) YAHOO.ARISoft.page.pageManager.submitForm(); return false;" class="btn aq-btn-continue"><?php echo JText::_('COM_ARIQUIZ_LABEL_CONTINUE'); ?> <i class="icon-circle-arrow-right"></i></a>
<?php
	else: 
?>
	<div class="aq-message-error"><?php echo $this->errorMessage; ?></div>
	<br/><br/>
<?php
	endif; 
?>

<input type="hidden" name="quizId" value="<?php echo $this->quiz->QuizId; ?>" />
<?php
	if ($tmpl): 
?>
<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
<?php
	endif; 
?>