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

$config = AriQuizHelper::getConfig();
$quizInfo = $this->quizInfo;
$quizStorage = $this->quizStorage;
$ticketId = $quizStorage->getTicketId();
$tmpl = JRequest::getCmd('tmpl');

$user = JFactory::getUser();
$isGuest = $user->get('id') < 1;
$isCanStop = (!$isGuest && $quizStorage->get('CanStop'));
$showPaging = $quizStorage->get('ShowPaging');
?>

<a name="quiz_top" id="aqTop"></a>
<h2 class="aq-header"><?php echo $quizInfo->QuizName; ?></h2>
<div id="ariQueMainAnsContainer" class="aq-loading aq-quizsession-container<?php if (1 == $quizInfo->PageCount): ?> aq-one-page-quiz<?php endif; ?>">
	<div class="aq-loading-message">
		<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>
	</div>
	<div class="aq-status-panel aq-hidden-onloading">
		<div class="aq-timers-panel aq-right-pos aq-hidden-onloading" class="ariQuizTimeCnt" id="ariQuizTimeCnt">
			<h4><?php echo JText::_('COM_ARIQUIZ_LABEL_REMAININGTIME'); ?></h4>
			<div class="aq-question-timer" id="ariQuestionTimer">
				<table class="aq-timer">
					<tr>
						<td>
							<div id="ariQuestionTimerHr" class="aq-time">00</div>
						</td>
						<td>
							<div id="ariQuestionTimerMin" class="aq-time">00</div>
						</td>
						<td>
							<div id="ariQuestionTimerSec" class="aq-time">00</div>
						</td>
					</tr>
					<tr>
						<th class="aq-timer-hr"><?php echo JText::_('COM_ARIQUIZ_DATE_HOURSHORT'); ?></th>
						<th class="aq-timer-min"><?php echo JText::_('COM_ARIQUIZ_DATE_MINUTESHORT'); ?></th>
						<th class="aq-timer-sec"><?php echo JText::_('COM_ARIQUIZ_DATE_SECONDSHORT'); ?></th>
					</tr>
				</table>
			</div>
			<div class="aq-timer-vr"></div>
			<div class="aq-quiz-timer" id="ariQuizTimer">
				<table class="aq-timer">
					<tr>
						<td>
							<div id="ariQuizTimerHr" class="aq-time">00</div>
						</td>
						<td>
							<div id="ariQuizTimerMin" class="aq-time">00</div>
						</td>
						<td>
							<div id="ariQuizTimerSec" class="aq-time">00</div>
						</td>
					</tr>
					<tr>
						<th class="aq-timer-hr"><?php echo JText::_('COM_ARIQUIZ_DATE_HOURSHORT'); ?></th>
						<th class="aq-timer-min"><?php echo JText::_('COM_ARIQUIZ_DATE_MINUTESHORT'); ?></th>
						<th class="aq-timer-sec"><?php echo JText::_('COM_ARIQUIZ_DATE_SECONDSHORT'); ?></th>
					</tr>
				</table>
			</div>
		</div>
		<div class="aq-progress-panel">		
			<h4><?php echo JText::_('COM_ARIQUIZ_LABEL_COMPLETED'); ?></h4>
			<div id="ariQuizProgressWrap" class="aq-progress-bar">
				<div id="ariQuizProgress" class="aq-progress-bar-status"> </div>
			</div>
			<div class="aq-page-status" id="tdQuestionInfo"></div>
		</div>
	</div>

	<br class="clear" />	
	
	<div style="position: relative; width: 100%;" class="ariQuizMainContainer">
		<div class="aq-page-description aq-page-description-hidden aq-hidden-onloading" id="aqPageDescription">
			<div class="aq-page-description-label"><?php echo JText::_('COM_ARIQUIZ_LABEL_DESCRIPTION'); ?></div>
			<div class="aq-page-description-content" id="aqPageDescriptionContent"></div>
		</div>
		<div class="aq-questions aq-hidden-onloading" id="ariQuestions">
		</div>
	</div>
    
    <?php
        if ($showPaging && $quizInfo->PageCount > 1):
    ?>
    <div class="aq-navbutton-panel pagination">
        <div style=""><?php echo JText::_('COM_ARIQUIZ_LABEL_NAVTOPAGE'); ?></div>
        <br />
        <ul>
        <?php
            for ($pageIdx = 0; $pageIdx < $quizInfo->PageCount; $pageIdx++):
                $pageDisabled = false;
                if (isset($quizInfo->PagesStatus[$pageIdx]))
                {
                    $pageStatus = $quizInfo->PagesStatus[$pageIdx];
                    $pageDisabled = !!$pageStatus->Completed;
                }
        ?>
            <li class="aq-navbutton-page<?php echo $pageIdx; ?><?php if ($pageDisabled): ?> disabled<?php endif; ?>"><a href="#" class="disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled') || YAHOO.util.Dom.hasClass(this.parentNode, 'disabled') || YAHOO.util.Dom.hasClass(this.parentNode, 'active')) return false; ariQuizQueManager.goToPage(<?php echo $pageIdx; ?>); return false;"><?php echo $pageIdx + 1; ?></a></li>
        <?php
            endfor;
        ?>
        </ul>
    </div>
    <?php
        endif;
    ?>

	<div class="aq-button-panel">
		<a href="#" class="btn btn-primary disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; if (ariQuizQueManager.validate()) ariQuizQueManager.savePage(); return false;"><i class="icon-pencil"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_SAVE'); ?></a>
		<?php
			if ($quizStorage->get('CanBack')):
		?>	
		<a href="#" class="btn disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; ariQuizQueManager.prevPage(); return false;"><i class="icon-arrow-left"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_BACK'); ?></a>
		<?php
			endif;
		?>
		<?php
			if ($quizStorage->get('CanSkip')):
		?>	
		<a href="#" class="btn disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; ariQuizQueManager.nextPage(); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_NEXT'); ?> <i class="icon-arrow-right"></i></a>
		<?php
			endif;
		?>
		<?php
			if ($quizStorage->get('UseCalculator')):
		?>
		<a href="#" class="btn" id="aCalc_<?php echo $quizInfo->QuizId; ?>"><i class="icon-calendar"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_CALCULATOR'); ?></a>
		<?php
			endif;
		?>
		<?php
			if ($isCanStop):
		?>	
			<a href="#" class="btn disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; ariQuizQueManager.raiseServerEvent('stopExit'); return false;"><i class="icon-stop"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_SAVEANDEXIT'); ?></a>
		<?php
			endif;
		?>
		<?php
			if ($quizStorage->get('CanTerminate')):
		?>	
			<a href="#" class="btn disable-onsubmit" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; ariQuizQueManager.raiseServerEvent('terminate'); return false;"><i class="icon-off"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_TERMINATE'); ?></a>
		<?php
			endif;
		?>
		<?php
			if ($quizStorage->get('ShowExplanation')):
		?>
			<a href="#" class="btn aq-btn-explanation" onclick="if (YAHOO.util.Dom.hasClass(this, 'disabled')) return false; ariQuizQueManager.hideExplanationQuestion(); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_CONTINUE'); ?> <i class="icon-circle-arrow-right"></i></a>			
		<?php
			endif;
		?>
	</div>
</div>

<div id="panelError" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_ALERT'); ?></div>  
	<div class="bd" style="text-align: center;">
		<div class="aq-error" id="ariQuizError">&nbsp;</div>		
	</div>
	<div class="ft">
		<div class="buttons">
			<a href="#" class="btn" onclick="YAHOO.ARISoft.page.panelError.hide(); return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_CLOSE'); ?></a>
		</div>
	</div>
</div>

<?php if ($tmpl): ?>
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
<?php endif; ?>
<input type="hidden" name="pageId" id="hidPageId" value="" />
<input type="hidden" name="ticketId" value="<?php echo $ticketId; ?>" />
<input type="hidden" name="timeOver" id="timeOver" value="0" /> 

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	ariQuizQueManager = new YAHOO.ARISoft.ariQuiz.questionManager({
		autoScroll: <?php echo json_encode((bool)$config->get('AutoScroll')); ?>,
		containerId: 'ariQuestions',
		mainContainerId: 'ariQueMainAnsContainer',
		explanationId: 'ariQuizExplanation',
		correctAnswerId: 'ariQuizCorrectAnswer', 
		errorContainerId: 'ariQuizError', 
		formId: 'quizForm',
		queContainerId: 'ariQueMainContainer',
		questionInfoId: 'tdQuestionInfo',
		timeContainerId: 'ariQuizTimeCnt',
		showCorrectAnswerBtn: <?php echo json_encode((bool)$quizStorage->get('ShowCorrectAnswer')); ?>,
		questionCount: <?php echo $quizInfo->QuestionCount; ?>,
		pageCount: <?php echo $quizInfo->PageCount; ?>,
		completedCount: <?php echo $quizInfo->CompletedPageCount; ?>,
		quizTime: <?php echo json_encode($quizInfo->TotalTime); ?>,
		extraParams: <?php echo json_encode($quizInfo->ExtraParams); ?>,
		MESSAGES: {
			loading: "<?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?>"
		}
	},
	{
		baseUrl: '<?php echo JURI::root(true); ?>/index.php',
		ticketId: '<?php echo $ticketId; ?>',
		quizId: '<?php echo $quizInfo->QuizId; ?>',
		view: '<?php echo $this->getName(); ?>',
		parsePluginTag: <?php echo $quizStorage->get('ParsePluginTag') ? 'true' : 'false'; ?>
	});

	//YAHOO.ARISoft.DOM.moveTo("panelError");
	YAHOO.ARISoft.page.panelError = new YAHOO.widget.Panel("panelError", { 
		width:"300px", 
		visible:false, 
		constraintoviewport:true, 
		modal:true, 
		fixedcenter: "contained", 
		zIndex: 30000
	});
	YAHOO.ARISoft.page.panelError.render();

	ariQuizQueManager.init();
});
</script>