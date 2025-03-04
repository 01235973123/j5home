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

$socialLink = new JURI(JURI::current());
$socialLink->setVar('share', '1');
$socialLink = $socialLink->toString();
?>

<?php 
	if ($this->btnEmailVisible): 
?>
<a href="#" id="btnEmail" class="btn aq-btn-email" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('ajaxSendEmail'); return false;"><i class="icon-envelope"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_SENDRESULTS'); ?></a>
<?php
	endif;
	
	if ($this->btnPrintVisible):
?>
<a href="index.php?option=com_ariquiz&view=quizcomplete&task=printResults&ticketId=<?php echo $this->ticketId; ?>&tmpl=component" target="_blank" class="btn aq-btn-print"><i class="icon-print"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_PRINT'); ?></a>
<?php
	endif;
	
	if ($this->btnCertificateVisible):
?>
<a href="#" class="btn aq-btn-certificate" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('certificate'); return false;"><i class="icon-file"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_CERTIFICATE'); ?></a>
<?php
	endif;
?>
<?php
    if ($this->btnTryAgainVisible):
?>
<a href="<?php echo $this->quizLink; ?>" class="btn aq-bnt-tryagain"><i class="icon-repeat"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_TRYAGAIN'); ?></a>
<?php
    endif;
?>
<?php
    if (J3_0 && $this->shareResults && $this->isOwnResult):
?>
<div class="btn-group">
    <a class="btn dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" href="#">
        <i class="icon-globe"></i> <?php echo JText::_('COM_ARIQUIZ_LABEL_SHARERESULTS'); ?><?php if (!J4):?> <span class="caret"></span><?php endif; ?>
    </a>
    <ul class="dropdown-menu">
        <li><a href="#" onclick="window.open('https://www.facebook.com/sharer.php?u=<?php echo urlencode($socialLink); ?>');return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_FACEBOOK'); ?></a></li>
        <li><a href="#" onclick="window.open('https://plusone.google.com/_/+1/confirm?url=<?php echo urlencode($socialLink); ?>');return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_GPLUS'); ?></a></li>
        <li><a href="#" onclick="window.open('https://twitter.com/intent/tweet?original_referer=<?php echo urlencode($socialLink); ?>&url=<?php echo urlencode($socialLink); ?>&text=<?php echo urlencode($this->socialMessage); ?>');return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_TWITTER'); ?></a></li>
    </ul>
</div>
<?php
    endif;
?>

<?php
	if ($this->resultText):
?>
<br/><br/>
<?php echo $this->resultText; ?>
<br/><br/>
<?php
	endif; 
?>

<?php
if (isset($this->dtResults)) 
	$this->dtResults->render(array('class' => 'aq-dt-results')); 
?>

<input type="hidden" name="ticketId" value="<?php echo $this->ticketId; ?>" />
<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
        Event = YAHOO.util.Event,
		Dom = YAHOO.util.Dom;

	pageManager.registerActionGroup('ajaxAction', {
		query: {"view": "quizcomplete", "ticketId": "<?php echo $this->ticketId; ?>"},
		onAction: page.actionHandlers.simpleCtrlAjaxAction,
		enableValidation: true,
		ctrl: 'btnEmail',
		errorMessage: '<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_LABEL_MAILSENT', true); ?>',
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	pageManager.registerAction('ajaxSendEmail', {
		group: "ajaxAction"
	});

    function closePrint() {
        document.body.removeChild(this.__print_container__);
    }

    function setPrint() {
        this.contentWindow.__print_container__ = this;
        this.contentWindow.onbeforeunload = closePrint;
        this.contentWindow.onafterprint = closePrint;
        this.contentWindow.focus();// Required for IE
        this.contentWindow.print();
    }

    function printPage(sURL) {
        var oHiddFrame = document.createElement("iframe");
        oHiddFrame.onload = function() {
            var resultsContainer = this.contentWindow.document.getElementById('dtResults');

            if (!resultsContainer) {
                setPrint.call(this);
            } else {
                var self = this,
                    chkCount = 0,
                    timer,
                    complete = function() {
                        if (timer)
                            clearInterval(timer);

                        setPrint.call(self);
                    };

                timer = setInterval(function() {
                    if (chkCount > 100) {
                        complete();
                        return ;
                    }

                    var yuiDtData = Dom.getElementsByClassName('yui-dt-data', 'tbody', resultsContainer);
                    if (yuiDtData && yuiDtData.length > 0 && yuiDtData[0].hasChildNodes()) {
                        clearInterval(timer);
                        setTimeout(function() {
                            complete();
                        }, 500);
                    } else {
                        ++chkCount;
                    }
                }, 500);
            }
        };
        oHiddFrame.style.visibility = "hidden";
        oHiddFrame.style.position = "fixed";
        oHiddFrame.style.right = "0";
        oHiddFrame.style.bottom = "0";
        oHiddFrame.src = sURL;
        document.body.appendChild(oHiddFrame);
    }

    Dom.getElementsByClassName('aq-btn-print', null, null, function(el) {
        Event.on(el, 'click', function(e) {
            printPage(el.href);

            Event.stopEvent(e);
        });
    });
});
</script>