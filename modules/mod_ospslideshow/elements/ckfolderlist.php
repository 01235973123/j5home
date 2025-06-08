<?php
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
/**
 * @copyright	Copyright (C) 2016 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('cklist');

class JFormFieldCkfolderList extends JFormFieldCklist
{

	public $type = 'CkfolderList';

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$filter			= (string) $this->element['filter'];
		$exclude		= (string) $this->element['exclude'];
		$hideNone		= (string) $this->element['hide_none'];
		$hideDefault	= (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		if (!is_dir($path)) {
			$path = JPATH_ROOT.'/'.$path;
		}

		// Prepend some default options based on field attributes.
		if (!$hideNone) {
			$options[] = HTMLHelper::_('select.option', '-1', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault) {
			$options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of folders in the search path with the given filter.
		$folders = Folder::folders($path, $filter);

		// Build the options list from the list of folders.
		if (is_array($folders)) {
			foreach($folders as $folder) {

				// Check to see if the file is in the exclude mask.
				if ($exclude) {
					if (preg_match(chr(1).$exclude.chr(1), $folder)) {
						continue;
					}
				}

				$options[] = HTMLHelper::_('select.option', $folder, $folder);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
