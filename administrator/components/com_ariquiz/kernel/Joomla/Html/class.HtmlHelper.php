<?php
(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class AriJoomlaHtmlHelper {
    static public function loadJQuery() {
        Joomla\CMS\HTML\HTMLHelper::_('jquery.framework');
    }

    static public function renderModal($modalId, $modalParams) {
        echo Joomla\CMS\HTML\HTMLHelper::_('bootstrap.renderModal', $modalId, $modalParams);
    }

    static public function modalLinkAttrs($modalId) {
        return array(
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#' . $modalId,
        );
    }
}