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

$specificQuestion = $this->specificQuestion;
$questionData = $this->questionData;
$questionOverridenData = $this->questionOverridenData;
?>
<style type="text/css">
    TBODY>TR:first-child>TD>.ari-cloner-moveup-item,
    TBODY>TR:last-child>TD>.ari-cloner-movedown-item {display:none;}
</style>
<table id="tblQuestions" data-cloner-control-key="answers" class="ari-cloner-container multipleDropdownQuestionContainer questionContainer tblQuestion table" style="width: 100%;" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <?php
        if (!$this->basedOnBank):
            ?>
            <th style="width: 1%; text-align: center;"><a href="#" class="btn ari-cloner-add-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_ADDANSWER'); ?>"><i class="icon-plus"></i></a></th>
        <?php
        endif;
        ?>
        <th style="text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_PLACEHOLDER'); ?></th>
        <th style="text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?></th>
        <?php
        if (!$this->basedOnBank):
            ?>
            <th style="width: 5%; text-align: center;"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
        <?php
        endif;
        ?>
    </tr>
    </thead>
    <tbody>
    <tr class="ari-cloner-template">
        <?php
        if (!$this->basedOnBank):
            ?>
            <td></td>
        <?php
        endif;
        ?>
        <td style="text-align:left;">
            <textarea data-cloner-control-key="question" class="text_area" style="width: 95%;margin:0;" rows="3" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?>></textarea>
            <input type="hidden" data-cloner-control-key="questionId" />
        </td>
        <td style="text-align:left;">
            <table cellpadding="0" cellspacing="0" style="width:100%" data-cloner-control-key="answers" class="ari-cloner-container table table-bordered" data-cloner-opt-items="1">
                <thead>
                <tr>
                    <th class="colMin">
                        <?php
                        if (!$this->basedOnBank):
                            ?>
                            <a href="#" class="btn ari-cloner-add-item"><i class="icon-plus"></i></a>
                        <?php
                        endif;
                        ?>
                    </th>
                    <th>
                        <?php echo JText::_('COM_ARIQUIZ_LABEL_ANSWER'); ?>
                    </th>
                    <th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_SCORE'); ?></th>
                    <th class="colMin"><?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONS'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr class="ari-cloner-template">
                    <td></td>
                    <td>
                        <input type="text" class="text_area" data-cloner-control-key="answer" style="width: 95%;margin:0;" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> />
                        <input type="hidden"  data-cloner-control-key="answerId" />
                    </td>
                    <td><input type="text"  data-cloner-control-key="score" size="5" value="0" <?php if ($this->basedOnBank) echo 'disabled="true"'; ?> /></td>
                    <td>
                        <?php
                        if (!$this->basedOnBank):
                            ?>
                            <a href="#" class="btn btn-mini ari-cloner-remove-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_REMOVE'); ?>"><i class="icon-remove"></i></a>
                            <!--a href="#" class="btn btn-mini ari-cloner-moveup-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_UP'); ?>"><i class="icon-arrow-up"></i></a>
                            <a href="#" class="btn btn-mini ari-cloner-movedown-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_DOWN'); ?>"><i class="icon-arrow-down"></i></a-->
                        <?php
                        endif;
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <?php
        if (!$this->basedOnBank):
            ?>
            <td>
                <a href="#" class="btn btn-mini ari-cloner-remove-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_REMOVE'); ?>"><i class="icon-remove"></i></a>
                <!--a href="#" class="btn btn-mini ari-cloner-moveup-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_UP'); ?>"><i class="icon-arrow-up"></i></a>
                <a href="#" class="btn btn-mini ari-cloner-movedown-item" title="<?php echo JText::_('COM_ARIQUIZ_LABEL_DOWN'); ?>"><i class="icon-arrow-down"></i></a-->
            </td>
        <?php
        endif;
        ?>
    </tr>
    </tbody>
</table>
<input type="hidden" id="hidQuestionData" name="questionData" />
<br/>
<table class="questionNote" cellpadding="0" cellspacing="0">
    <tr>
        <td class="colMin noWrap"><b><?php echo JText::_('COM_ARIQUIZ_LABEL_NOTE'); ?></b>&nbsp;&nbsp;</td>
        <td><?php echo JText::_('COM_ARIQUIZ_MESSAGE_MFTNOTE'); ?></td>
    </tr>
</table>
<script type="text/javascript">
    jQuery(function($) {
        $('#tblQuestions').ariCloner({}, {'answers': <?php echo json_encode($specificQuestion->getDataFromXml($questionData, false)); ?>});

        var oldSubmitHandler = Joomla.submitform;
        Joomla.submitform = function() {
            var data = $('#tblQuestions').ariCloner().getData();
            data = data['answers'];

            $('#hidQuestionData').val(JSON.stringify(data));

            oldSubmitHandler.apply(this, arguments);
        }
    });
<?php 
if (!$this->basedOnBank):
?>
    YAHOO.ARISoft.validators.validatorManager.addValidator(
        new YAHOO.ARISoft.validators.customValidator(null,
            function(val)
            {
                var isValid = true,
                    isNotEmpty = false,
                    data = jQuery('#tblQuestions').ariCloner().getData();

                data = data['answers'];
                for (var i = 0; i < data.length; i++) {
                    var dataItem = data[i],
                        answers = dataItem['answers'];

                    if (dataItem['question'].replace(/^\s+|\s+$/g, '').length == 0)
                        continue ;

                    for (var j = 0; j < answers.length; j++) {
                        var answerData = answers[j];

                        if (answerData['answer'].replace(/^\s+|\s+$/g, '').length > 0) {
                            isNotEmpty = true;
                            break;
                        }
                    }
                }

                if (!isNotEmpty) {
                    this.errorMessage = YAHOO.ARISoft.core.getNormalizeValue('<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>');
                    isValid = false;
                }

                return isValid;
            },
            {emptyValidate : true, errorMessage : '<?php echo JText::_('COM_ARIQUIZ_ERROR_QUESTIONNOTANSWER', true); ?>'}));

    YAHOO.ARISoft.validators.validatorManager.addValidator(
        new YAHOO.ARISoft.validators.customValidator(null,
            function(val) {
                var isValid = true,
                    data = jQuery('#tblQuestions').ariCloner().getData();

                data = data['answers'];
                for (var i = 0; i < data.length; i++) {
                    var dataItem = data[i],
                        answers = dataItem['answers'];

                    if (dataItem['question'].replace(/^\s+|\s+$/g, '').length == 0)
                        continue ;

                    for (var j = 0; j < answers.length; j++) {
                        var answerData = answers[j],
                            sScore = answerData['score'].replace(/^\s+|\s+$/g, '');

                        if (sScore.length > 0) {
                            var score = parseFloat(sScore, 10);
                            if (sScore != score) {
                                isValid = false;
                                break;
                            }
                        }
                    }
                }

                return isValid;
            },
            {emptyValidate : true, errorMessage : '<?php echo JText::_('COM_ARIQUIZ_ERROR_SCORE', true); ?>'}));
<?php
endif;
?>
</script>