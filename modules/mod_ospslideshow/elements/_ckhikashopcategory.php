<?php

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
/**
 * @copyright	Copyright (C) 2016 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
FormHelper::loadFieldClass('cklist');

class JFormFieldCkhikashopcategory extends JFormFieldCklist {

    protected $type = 'ckhikashopcategory';

    protected function getOptions() {
        // if the component is not installed
        if (!Folder::exists(JPATH_ROOT . '/administrator/components/com_hikashop')
                OR !File::exists(JPATH_ROOT . '/modules/mod_slideshowck/helper_hikashop.php')) {
            // add the root item
            $option = new stdClass();
            $option->text = Text::_('MOD_SLIDESHOWCKHIKASHOP_HIKASHOP_NOTFOUND');
            $option->value = '0';
            $options[] = $option;
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);

            return $options;
        }

        // get the categories form the helper
        $params = new Registry();
        require_once JPATH_ROOT . '/modules/mod_maximenuck/helper_hikashop.php';
        $cats = modMaximenuckhikashopHelper::getItems($params);

        // add the root item
        $option = new stdClass();
        $option->text = Text::_('MOD_SLIDESHOWCKHIKASHOP_HIKASHOP_ROOTNODE');
        $option->value = '0';
        $options[] = $option;
        foreach ($cats as $cat) {
            $option = new stdClass();
            $option->text = str_repeat(" - ", $cat->level - 1) . $cat->name;
            $option->value = $cat->id;
            $options[] = $option;
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
