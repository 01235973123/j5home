<?php
/**
 * @package     RAD
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2015 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseQuery;
use Joomla\Registry\Registry;

/**
 * Base class for a Joomla Model
 *
 * @package       RAD
 * @subpackage    Model
 * @since         2.0
 */
class RADModel
{
	/**
	 * Data returned by getData method
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Total number of records
	 *
	 * @var int
	 */
	protected $total;

	/**
	 * The query object
	 *
	 * @var DatabaseQuery
	 */
	protected $query;

	/**
	 * name of clean cache event
	 *
	 * @var string
	 */
	protected $eventCleanCache;
	/**
	 * Full name of the component com_foobar
	 *
	 * @var string
	 */
	protected $option;

	/**
	 * The model name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	/**
	 * The database driver.
	 *
	 * @var \Joomla\Database\DatabaseDriver
	 */
	protected $db;

	/**
	 * Model configuration data
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The name of the database table
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Ignore request or not. If set to Yes, model states won't be set when it is created
	 *
	 * @var boolean
	 */
	public $ignoreRequest = false;

	/**
	 * Remember model states value in session
	 *
	 * @var boolean
	 */
	public $rememberStates = false;

	/**
	 * Model parameters
	 *
	 * @var Registry
	 */
	protected $params;

	/**
	 * Special handle for state
	 *
	 * @var array
	 */
	protected $choicesStates = [];

	/**
	 * @param   string  $name    The name of model to instantiate
	 *
	 * @param   string  $prefix  Prefix for the model class name, ComponentnameModel
	 *
	 * @param   array   $config  Configuration array for model
	 *
	 * @return RADModel A model object
	 */
	public static function getInstance($name, $prefix, $config = [])
	{
		$name          = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$class         = ucfirst($prefix) . ucfirst($name);
		$overrideClass = ucfirst($prefix) . 'Override' . ucfirst($name);

		if (class_exists($overrideClass))
		{
			$class = $overrideClass;
		}

		if (!class_exists($class))
		{
			$class = $config['default_model_class'] ?? 'RADModel';
		}

		return new $class($config);
	}

	/**
	 * Get temp instance of a model, do not populate states from request and do not remember states
	 *
	 * @param   string  $name
	 * @param   string  $prefix
	 * @param   array   $config
	 */
	public static function getTempInstance($name, $prefix, $config = [])
	{
		$config['ignore_request']  = true;
		$config['remember_states'] = false;

		return static::getInstance($name, $prefix, $config);
	}

	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options
	 *
	 * @throws Exception
	 */
	public function __construct($config = [])
	{
		if (isset($config['option']))
		{
			$this->option = $config['option'];
		}
		else
		{
			$className = get_class($this);
			$pos       = strpos($className, 'Model');

			if ($pos !== false)
			{
				$this->option = 'com_' . strtolower(substr($className, 0, $pos));
			}
			else
			{
				throw new Exception(Text::_('Could not detect the component for model'), 500);
			}
		}

		// Set the model name
		if (isset($config['name']))
		{
			$this->name = $config['name'];
		}
		else
		{
			$className = get_class($this);

			// Do not use Override in name of class
			$className = str_replace('ModelOverride', 'Model', $className);

			$pos = strpos($className, 'Model');

			if ($pos !== false)
			{
				$this->name = substr($className, $pos + 5);
			}
			else
			{
				throw new Exception(Text::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}
		}

		$this->db = $config['db'] ?? Factory::getContainer()->get('db');

		// Set the model state
		$this->state = $config['state'] ?? new RADModelState();

		if (isset($config['params']) && $config['params'] instanceof Registry)
		{
			$this->params = $config['params'];
		}
		else
		{
			$this->params = new Registry();
		}

		$component = substr($this->option, 4);

		$config['class_prefix'] ??= ucfirst($component);

		$config['language_prefix'] ??= strtoupper($component);

		if (isset($config['table']))
		{
			$this->table = $config['table'];
		}
		else
		{
			$tablePrefix = $config['table_prefix'] ?? '#__' . $component . '_';

			$this->table = $tablePrefix . strtolower(RADInflector::pluralize($this->name));
		}

		if (isset($config['ignore_request']))
		{
			$this->ignoreRequest = $config['ignore_request'];
		}

		if (isset($config['remember_states']))
		{
			$this->rememberStates = $config['remember_states'];
		}

		$this->config = $config;

		//Add include path to find table class
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $this->option . '/table');
	}

	/**
	 * Populate model state from input
	 *
	 * @param   RADInput  $input
	 */
	public function populateState($input)
	{
		$properties = $this->state->getProperties();

		if (count($properties))
		{
			$stateData = [];

			if ($this->rememberStates)
			{
				$context = $this->option . '.' . $input->get('view', $this->config['default_view']) . '.';

				foreach ($properties as $property)
				{
					$newState = $this->getUserStateFromRequest($input, $context . $property, $property);

					if ($newState != null)
					{
						$stateData[$property] = $newState;
					}
				}
			}
			else
			{
				foreach ($properties as $property)
				{
					$newState = $input->get($property, null, 'none');

					if ($newState != null)
					{
						$stateData[$property] = $newState;
					}
				}
			}

			$this->setState($stateData);
		}
	}

	/**
	 * Get JTable object for the model
	 *
	 * @param   string  $name
	 *
	 * @return \Joomla\CMS\Table\Table
	 */
	public function getTable($name = '')
	{
		if (!$name)
		{
			$name = RADInflector::singularize($this->name);
		}

		$db        = $this->getDbo();
		$className = $this->config['class_prefix'] . 'Table' . ucfirst($name);

		return new $className($db);
	}

	/**
	 * Set the model state properties
	 *
	 * @param   string|array  $property  The    name of the property, an array
	 *
	 * @param   mixed         $value     The value of the property
	 *
	 * @return $this
	 */
	public function setState($property, $value = null)
	{
		$changed = false;

		if (is_array($property))
		{
			foreach ($property as $key => $value)
			{
				if (isset($this->state->$key) && $this->state->$key != $value)
				{
					$changed = true;
					break;
				}
			}

			$this->state->setData($property);
		}
		else
		{
			if (isset($this->state->$property) && $this->state->$property != $value)
			{
				$changed = true;
			}

			$this->state->$property = $value;
		}

		if ($changed)
		{
			// Reset the data
			$this->data  = null;
			$this->total = null;
		}

		return $this;
	}

	/**
	 * Get the model state properties
	 *
	 * If no property name is given then the function will return an associative array of all properties.
	 *
	 * @param   string  $property  The name of the property
	 *
	 * @param   string  $default   The default value
	 *
	 * @return mixed <string, RADModelState>
	 */
	public function getState($property = null, $default = null)
	{
		$result = $default;

		if (is_null($property))
		{
			$result = $this->state;
		}
		else
		{
			if (isset($this->state->$property))
			{
				$result = $this->state->$property;
			}
		}

		return $result;
	}

	/**
	 * Reset all cached data and reset the model state to it's default
	 *
	 * @param   boolean If TRUE use defaults when resetting. Default is TRUE
	 *
	 * @return RADModel
	 */
	public function reset($default = true)
	{
		$this->data  = null;
		$this->total = null;
		$this->state->reset($default);
		$this->query = $this->db->getQuery(true);

		return $this;
	}

	/**
	 * Get the dbo
	 *
	 * @return \Joomla\Database\DatabaseDriver
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Get name of the model
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Method to get menu parameters
	 *
	 * @return Registry
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Method to set menu parameters
	 *
	 * @param   Registry  $params
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * Supports a simple form Fluent Interfaces.
	 * Allows you to set states by
	 * using the state name as the method name.
	 *
	 * For example : $model->filter_order('name')->filter_order_Dir('DESC')->limit(10)->getData();
	 *
	 * @param   string  $method  Method name
	 *
	 * @param   array   $args    Array containing all the arguments for the original call
	 *
	 * @return RADModel
	 */
	public function __call($method, $args)
	{
		if (isset($this->state->$method))
		{
			return $this->set($method, $args[0]);
		}

		return;
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   RADInput  $input    The input object
	 * @param   string    $key      The key of the user state variable.
	 * @param   string    $request  The name of the variable passed in a request.
	 * @param   string    $default  The default value for the variable if not found. Optional.
	 * @param   string    $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  object  The request user state.
	 */
	protected function getUserStateFromRequest($input, $key, $request, $default = null, $type = 'none')
	{
		$app = Factory::getApplication();

		// Special handle for dropdown filter using choices JS, fix clear options bugs
		if (in_array($request, $this->choicesStates) && $input->getMethod() === 'POST')
		{
			$currentState = $default;
		}
		else
		{
			$currentState = $app->getUserState($key, $default);
		}

		$newState = $input->get($request, null, $type);

		// Save the new value only if it was set in this request.
		if ($newState !== null)
		{
			$app->setUserState($key, $newState);
		}
		else
		{
			$newState = $currentState;
		}

		return $newState;
	}

	/**
	 * Clean the cache
	 *
	 * @param   string  $group  The cache group
	 *
	 * @return  void
	 */
	protected function cleanCache($group = null)
	{
		$app     = Factory::getApplication();
		$options = [
			'defaultgroup' => $group ?: $this->option,
			'cachebase'    => $app->get('cache_path', JPATH_CACHE),
		];

		$cache = Cache::getInstance('callback', $options);
		$cache->clean();

		// Trigger the onContentCleanCache event.
		if (!empty($this->eventCleanCache))
		{
			$app->triggerEvent($this->eventCleanCache, $options);
		}
	}
}
