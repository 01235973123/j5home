<?php
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
/**
 * @copyright	Copyright (C) 2016 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCkfileList extends JFormFieldCklist
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'CkfileList';

	/**
	 * Method to get the list of files for the field options.
	 * Specify the target directory with a directory attribute
	 * Attributes allow an exclude mask and stripping of extensions from file name.
	 * Default attribute may optionally be set to null (no file) or -1 (use a default).
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$filter = (string) $this->element['filter'];
		$exclude = (string) $this->element['exclude'];
		$stripExt = (string) $this->element['stripext'];
		$hideNone = (string) $this->element['hide_none'];
		$hideDefault = (string) $this->element['hide_default'];
		$default = (string) $this->element['default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		if (!is_dir($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}

		// Prepend some default options based on field attributes.
		if (!$hideNone)
		{
			$options[] = HTMLHelper::_('select.option', '-1', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault)
		{
			$options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of files in the search path with the given filter.
		$files = Folder::files($path, $filter);

		// Build the options list from the list of files.
		if (is_array($files))
		{
			foreach ($files as $file)
			{

				// Check to see if the file is in the exclude mask.
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $file))
					{
						continue;
					}
				}

				// If the extension is to be stripped, do it.
				if ($stripExt)
				{
					$file = File::stripExt($file);
				}

				$options[] = HTMLHelper::_('select.option', $file, $file);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
