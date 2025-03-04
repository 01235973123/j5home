<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/defines.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/models/quiz.php';
require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/tables/quiz.php';

AriKernel::import('Data.DataFilter');
AriKernel::import('Xml.XmlHelper');
AriKernel::import('Joomla.Html.HtmlHelper');
AriKernel::import('Web.HtmlHelper');

if (!J4) 
    JHtml::_('behavior.modal', 'a.modal');
else
    Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');

class JElementSelectquiz extends JElement
{
    protected $type = 'Selectquiz';

    function fetchElement($name, $value, &$node, $control_name)
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_ariquiz.sys', JPATH_ADMINISTRATOR);

        $id = $control_name . $name;
        $lblId = $id . '_label';
        $btnLbl = AriXmlHelper::getAttribute($node, 'btn_label', 'COM_ARIQUIZ_LABEL_SELECT');
        $btnLbl = JText::_($btnLbl);
        $showClearBtn = (bool)AriXmlHelper::getAttribute($node, 'hide_clear_btn', true);
        $btnClearLbl = AriXmlHelper::getAttribute($node, 'btn_clear_label', 'COM_ARIQUIZ_LABEL_CLEAR');
        $btnClearLbl = JText::_($btnClearLbl);
        $lbl = $emptyLabel = JText::_(AriXmlHelper::getAttribute($node, 'empty_label', ''));
        $ignoreQuizId = AriXmlHelper::getAttribute($node, 'ignore_quiz', '');
        $modalId = uniqid('modal-');

        $quizId = 0;
        if ($value)
        {
            $quizId = intval($value, 10);
            $model = AriModel::getInstance('Quiz', 'AriQuizModel');
            $quiz = $model->getQuiz($quizId);
            if ($quiz)
            {
                $lbl = $quiz->QuizName;
            }
            else
            {
                $quizId = 0;
            }
        }

        $this->registerScripts($name, $id, $lblId, $emptyLabel, $modalId);

        $selectQuizUrl = sprintf(
            'index.php?option=com_ariquiz&view=selectquiz&tmpl=component&callback=selectQuiz_%1$s_init&ignoreQuizId=%2$s',
            $name,
            $ignoreQuizId
        );

        if (J4) {
            $modalParams = array(
                'title' => JText::_('QUIZ_CATEGORY_LAYOUT_SELECT_QUIZ'),
                'url' => $selectQuizUrl,
                'height' => 400,
                'width' => 700,
            );
            AriJoomlaHtmlHelper::renderModal($modalId, $modalParams);

            return sprintf(
                '<div class="input-group"><input class="form-control input-medium" type="text" id="%5$s" value="%6$s" readonly="readonly" disabled="disabled" /><button type="button" class="btn btn-success"%9$s><i class="icon-edit"></i> %7$s</button>%8$s</div>
                <input type="hidden" name="%2$s[%1$s]" id="%3$s" value="%4$s" />
                ',
                $name,
                $control_name,
                $id,
                $quizId,
                $lblId,
                $lbl,
                $btnLbl,
                $showClearBtn
                    ? sprintf(
                    '<button onclick="selectQuiz_%1$s_clear();return false;" href="#" title="" class="btn btn-danger button-clear"><span class="icon-times"></span></button>',
                    $name
                )
                    : '',
                AriHtmlHelper::getAttrStr(
                    AriJoomlaHtmlHelper::modalLinkAttrs($modalId)
                )
            );
        }

        return sprintf(
            '<span class="input-append"><input class="input-medium" type="text" id="%5$s" value="%6$s" readonly="readonly" disabled="disabled" /><a target="_blank" class="modal btn" rel="{handler: \'iframe\',size: {x: 700, y: 400}}" href="%9$s"><i class="icon-edit"></i> %7$s</a>%8$s</span>
            <input type="hidden" name="%2$s[%1$s]" id="%3$s" value="%4$s" />
            ',
            $name,
            $control_name,
            $id,
            $quizId,
            $lblId,
            $lbl,
            $btnLbl,
            $showClearBtn
                ? sprintf(
                '<a onclick="selectQuiz_%1$s_clear();return false;" href="#" title="" class="btn hasTooltip"><i class="icon-remove"></i></a>',
                $name
            )
                : '',
            $selectQuizUrl
        );
    }

    function registerScripts($name, $ctrlId, $lblId, $emptyLbl, $modalId)
    {
        $doc = JFactory::getDocument();

        $doc->addScriptDeclaration(
            sprintf(
                ';function selectQuiz_%1$s_init(context) {
					var quizManager = context.YAHOO.ARISoft.page.pageManager.quizManager;

					quizManager.quizSelectedEvent.subscribe(function(event, data) {
						var quizData = data[0];

						document.getElementById("%2$s").value = quizData.Id;
						document.getElementById("%3$s").value = quizData.Name;

                        if (typeof SqueezeBox !== "undefined")
						    SqueezeBox.close();

                        if (typeof jQuery !== "undefined" && jQuery.fn.modal)
                            jQuery("#%5$s").modal("hide");
					});
				};
				;function selectQuiz_%1$s_clear() {
				    document.getElementById("%2$s").value = "0";
					document.getElementById("%3$s").value = "%4$s";
				};',
                $name,
                $ctrlId,
                $lblId,
                $emptyLbl,
                $modalId
            )
        );
    }
}