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

<div class="textRight">
    <div class="ari-inline-block simpleFilter">
        <table>
            <tr>
                <td class="bold"><?php echo JText::_('COM_ARIQUIZ_LABEL_FILTER'); ?></td>
                <td>
                    <?php echo $this->filterForm->render('filter', '_default', true, false, array('paramsPerRow' => 3)); ?>
                </td>
                <td>
                    <input type="button" class="button btn" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('changeFilters');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_APPLY'); ?>" />
                </td>
            </tr>
        </table>
    </div>
</div>

<?php $this->dtQuizzes->render(); ?>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
    var page = YAHOO.ARISoft.page,
        pageManager = page.pageManager,
        DTM = YAHOO.ARISoft.widgets.DataTableManager,
        dt = DTM.getTable('<?php echo $this->dtQuizzes->id; ?>'),
        ds = dt.getDataSource(),
        oldHandler = ds.sendRequest;

    ds.sendRequest = function(oRequest, oCallback, oCaller) {
        var ctrlCategory = Dom.get('filterCategoryId'),
            ctrlStatus = Dom.get('filterStatus');

        oRequest += '&filter[CategoryId]=' + encodeURIComponent(ctrlCategory.value) + '&filter[Status]=' + encodeURIComponent(ctrlStatus.value);

        dt.showTableMessage(dt.get("MSG_LOADING"), dt.CLASS_LOADING);

        oldHandler.call(this, oRequest, oCallback, oCaller);
    };

    pageManager.registerActionGroup('quizAction', {
        query: {"view": "selectquiz"},
        onAction: page.actionHandlers.simpleDatatableAction,
        dataTable: "<?php echo $this->dtQuizzes->id; ?>",
        enableValidation: true,
        errorMessage: "<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>",
        completeMessage: "",
        loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
    });
    pageManager.registerAction('changeFilters', {
        onAction: function() {
            DTM.refresh("<?php echo $this->dtQuizzes->id; ?>");
        }
    });

    pageManager.quizManager = {
        quizSelectedEvent: new YAHOO.util.CustomEvent('quizSelected'),

        quizSelected: function(id, name) {
            this.quizSelectedEvent.fire({'Id': id, 'Name': name});
        },

        changeFilters: function() {
            pageManager.triggerAction("changeFilters");
        }
    };

    <?php
        if ($this->callback):
    ?>
    var context = (window.top != window.self) ? window.top : window.self;
    if (context["<?php echo $this->callback; ?>"])
        context["<?php echo $this->callback; ?>"](window.self);
    <?php
        endif;
    ?>
});
</script>