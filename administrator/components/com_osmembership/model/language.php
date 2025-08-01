<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\LanguageHelper;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;

class OSMembershipModelLanguage extends MPFModel
{
	/**
	 * Model list data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  The configuration data for the model
	 */
	public function __construct($config)
	{
		$config['remember_states'] = true;

		parent::__construct($config);

		$this->state->insert('filter_search', 'string')
			->insert('filter_item', 'string', 'com_osmembership')
			->insert('filter_language', 'string', 'en-GB');
	}

	/**
	 * Get language items and store them in an array
	 */
	public function getData()
	{
		$registry     = new Registry();
		$language     = $this->state->filter_language;
		$languageFile = $this->state->filter_item;

		if (str_contains($languageFile, 'admin'))
		{
			$languageFolder = JPATH_ADMINISTRATOR . '/language/';
			$languageFile   = substr($languageFile, 6);
		}
		else
		{
			$languageFolder = JPATH_ROOT . '/language/';
		}

		$path = $languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini';
		$registry->loadFile($path, 'INI');
		$data['en-GB'][$languageFile] = $registry->toArray();

		if ($language != 'en-GB')
		{
			$translatedRegistry = new Registry();
			$translatedPath     = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';

			if (is_file($translatedPath))
			{
				$translatedRegistry->loadFile($translatedPath);
			}

			$data[$language][$languageFile] = $translatedRegistry->toArray();
		}

		return $data;
	}

	/**
	 * Get site languages
	 *
	 * @return array
	 */
	public function getSiteLanguages()
	{
		$result = [];

		foreach (LanguageHelper::getInstalledLanguages(0) as $language)
		{
			$result[] = $language->element;
		}

		return $result;
	}

	/**
	 * Save translation data
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function save($data)
	{
		$language     = $this->state->filter_language;
		$languageFile = $this->state->filter_item;

		if (str_contains($languageFile, 'admin'))
		{
			$languageFolder = JPATH_ADMINISTRATOR . '/language/';
			$languageFile   = substr($languageFile, 6);
		}
		else
		{
			$languageFolder = JPATH_ROOT . '/language/';
		}

		$registry = new Registry();
		$filePath = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';

		if (is_file($filePath))
		{
			$registry->loadFile($filePath, 'INI');
		}
		else
		{
			$registry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
		}

		//Get the current language file and store it to array
		$keys   = explode(',', $data['keys']);
		$values = explode('@@@', $data['values']);

		for ($i = 0, $n = count($keys); $i < $n; $i++)
		{
			$key   = $keys[$i];
			$value = $values[$i];
			$registry->set($key, addcslashes($value, '"'));
		}

		$newKeys   = explode(',', $data['new_keys']);
		$newValues = explode('@@@', $data['new_values']);

		for ($i = 0, $n = count($newKeys); $i < $n; $i++)
		{
			$newKey   = $newKeys[$i];
			$newValue = $newValues[$i];

			if ($newKey && $newValue)
			{
				$registry->set($newKey, addcslashes($newValue, '"'));
			}
		}

		$iniString = $registry->toString('INI');

		File::write($filePath, $iniString);

		return true;
	}
}
