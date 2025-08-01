<?php
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Multilanguage;
/**
 * @package     OSF
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2009 - 2023 Ossolution Team
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Admin model class. It will handle tasks such as add, update, delete...items
 *
 * @package		OSF
 * @subpackage	Model
 * @since		1.0
 */
class OSFModelAdmin extends OSFModel
{

	/**
	 * Context, used to get user session data and also trigger plugin
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var string
	 */
	protected $languagePrefix = null;

	/**
	 * This model trigger events or not. By default, set it to No to improve performance
	 * 
	 * @var boolean
	 */
	protected $triggerEvents = false;

	/**
	 * The event to trigger after deleting the data.
	 *
	 * @var string
	 */
	protected $eventAfterDelete = null;

	/**
	 * The event to trigger after saving the data.
	 *
	 * @var string
	 */
	protected $eventAfterSave = null;

	/**
	 * The event to trigger before deleting the data.
	 *
	 * @var string
	 */
	protected $eventBeforeDelete = null;

	/**
	 * The event to trigger before saving the data.
	 *
	 * @var string
	 */
	protected $eventBeforeSave = null;

	/**
	 * The event to trigger after changing the published state of the data.
	 *
	 * @var string
	 */
	protected $eventChangeState = null;

	/**
	 * Name of plugin group which will be loaded to process the triggered event.
	 * Default is component name
	 *
	 * @var string
	 */
	protected $pluginGroup = null;
	
	/**
	 * Data for the item
	 * 
	 * @var JTable
	 */
	protected $data = null;

	/**
	 * Constructor.
	 *
	 * @param array $config An optional associative array of configuration settings.
	 *        	
	 * @see OSFModel
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->context = $this->option . '.' . $this->name;
		//Insert the default model states for admin
		$this->state->insert('id', 'int', 0)->insert('cid', 'array', array());
		if ($this->triggerEvents)
		{
			$name = ucfirst($this->name);
			if (isset($config['plugin_group']))
			{
				$this->pluginGroup = $config['plugin_group'];
			}
			elseif (empty($this->pluginGroup))
			{
				//Plugin group should default to component name
				$this->pluginGroup = substr($this->option, 4);
			}
			//Initialize the events
			if (isset($config['event_after_delete']))
			{
				$this->eventAfterDelete = $config['event_after_delete'];
			}
			elseif (empty($this->eventAfterDelete))
			{
				$this->eventAfterDelete = 'on' . $name . 'AfterDelete';
			}
			
			if (isset($config['event_after_save']))
			{
				$this->eventAfterSave = $config['event_after_save'];
			}
			elseif (empty($this->eventAfterSave))
			{
				$this->eventAfterSave = 'on' . $name . 'AfterSave';
			}
			
			if (isset($config['event_before_delete']))
			{
				$this->eventBeforeDelete = $config['event_before_delete'];
			}
			elseif (empty($this->eventBeforeDelete))
			{
				$this->eventBeforeDelete = 'on' . $name . 'BeforeDelete';
			}
			
			if (isset($config['event_before_save']))
			{
				$this->eventBeforeSave = $config['event_before_save'];
			}
			elseif (empty($this->eventBeforeSave))
			{
				$this->eventBeforeSave = 'on' . $name . 'BeforeSave';
			}
			
			if (isset($config['event_change_state']))
			{
				$this->eventChangeState = $config['event_change_state'];
			}
			elseif (empty($this->eventChangeState))
			{
				$this->eventChangeState = 'on' . $name . 'ChangeState';
			}
		}
		
		// JText message prefix. Defaults to the name of component.
		if (isset($config['language_prefix']))
		{
			$this->languagePrefix = strtoupper($config['language_prefix']);
		}
		elseif (empty($this->languagePrefix))
		{
			$this->languagePrefix = strtoupper(substr($this->option, 4));
		}
	}

	/**
	 * Method to get the record data
	 *
	 * @return object
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			if (count($this->state->cid))
			{				
				$this->state->id = (int) $this->state->cid[0];
			}
			if ($this->state->id)
			{
				$this->loadData();
			}
			else
			{				
				$this->initData();
			}
		}
		
		return $this->data;
	}

	/**
	 * Method to store a record
	 *
	 * @param OSFInput $input the input data which users entered
	 *
	 */
	public function store($input, $ignore = array())
	{
		if ($this->triggerEvents)
		{
			//$dispatcher = JDispatcher::getInstance();;
			PluginHelper::importPlugin($this->pluginGroup);
		}
		$row = $this->getTable();
		$data = $input->getData();
		$isNew = true;
		if ($data['id'])
		{
			$row->load($data['id']);
			$isNew = false;
		}
		$row->bind($data, $ignore);
		$this->prepareTable($row, $input->get('task'));
		if (!$row->check())
		{
			throw new Exception($row->getError());
		}
		if ($this->triggerEvents)
		{
			$result = Factory::getApplication()->triggerEvent($this->eventBeforeSave, array($this->context, $row, $isNew));
			if (in_array(false, $result, true))
			{
				throw new Exception($row->getError());
			}
		}
		if (!$row->store())
		{
			throw new Exception($row->getError());
			return false;
		}
		if ($this->triggerEvents)
		{
			Factory::getApplication()->triggerEvent($this->eventAfterSave, array($this->context, $row, $isNew));
		}
		$input->set('id', $row->id);
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param array $cid
	 *        	
	 * @return boolean True if successful, false if an error occurs.
	 *        
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			if ($this->triggerEvents)
			{
				//$dispatcher = JDispatcher::getInstance();;
				PluginHelper::importPlugin($this->pluginGroup);
			}
			$row = $this->getTable();
			foreach ($cid as $id)
			{
				if ($row->load($id))
				{
					if ($this->triggerEvents)
					{
						$result = Factory::getApplication()->triggerEvent($this->eventBeforeDelete, array($this->context, $row));
						if (in_array(false, $result, true))
						{
							throw new Exception($row->getError());
						}
					}
					if (!$row->delete())
					{
						throw new Exception($row->getError());
					}
					if ($this->triggerEvents)
					{
						Factory::getApplication()->triggerEvent($this->eventAfterDelete, array($this->context, $row));
					}
				}
				else
				{
					throw new Exception($row->getError());
				}
			}
			$this->cleanCache();
		}
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param array $pks A list of the primary keys to change.
	 *        	
	 * @param integer $value The value of the published state.
	 */
	public function publish($pks, $value = 1)
	{
		$row = $this->getTable();
		$pks = (array) $pks;
		// Attempt to change the state of the records.
		if (!$row->publish($pks, $value, Factory::getApplication()->getIdentity()->id))
		{
			throw new Exception($row->getError());
		}
		if ($this->triggerEvents)
		{
			// Trigger the eventChangeState event.
			PluginHelper::importPlugin($this->pluginGroup);
			$result = Factory::getApplication()->triggerEvent($this->eventChangeState, array($this->context, $pks, $value));
		}
		// Clear the component's cache
		$this->cleanCache();
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param integer $pks The ID of the primary key to move.
	 *        	
	 * @param integer $delta Increment, usually +1 or -1
	 *        
	 */
	public function reorder($pks, $delta = 0)
	{
		$row = $this->getTable();
		$pks = (array) $pks;
		foreach ($pks as $pk)
		{
			if ($row->load($pk))
			{
				$where = $this->getReorderConditions($row);
				if (!$row->move($delta, $where))
				{
					throw new Exception($row->getError());
				}
			}
			else
			{
				throw new Exception($row->getError());
			}
		}
		
		// Clear the component's cache
		$this->cleanCache();
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param array $pks An array of primary key ids.
	 *        	
	 * @param array $order An array contain ordering value of item corresponding with $pks array
	 *        	
	 * @return mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		$row = $this->getTable();
		$conditions = array();
		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$row->load((int) $pk);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					throw new Exception($row->getError());
				}
				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($row);
				$found = false;
				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$conditions[] = array($row->id, $condition);
				}
			}
		}
		
		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}
		
		// Clear the component's cache
		$this->cleanCache();
		
		return true;
	}

	/**
	 * Load the record from database
	 *
	 */
	protected function loadData()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($this->table)
			->where('id = ' . (int) $this->state->id);
		$db->setQuery($query);
		$this->data = $db->loadObject();
	}

	/**
	 * Init the record dara object
	 */
	protected function initData()
	{				
		$this->data = $this->getTable();			
	}

	/**
	 * Method to change the title & alias, usually used on save2copy method
	 * 
	 * @param $row the object being saved             
	 * 
	 * @param string $alias The alias.
	 *        	
	 * @param string $title The title.
	 *        	
	 * @return array Contains the modified title and alias.
	 */
	protected function generateNewTitle($row, $alias, $title)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')->from($this->table);
		$conditions = $this->getReorderConditions($row);
		while (true)
		{
			$query->where('alias=' . $db->quote($alias));
			if (count($conditions))
			{
				$query->where($conditions);
			}
			$db->setQuery($query);
			$found = (int) $db->loadResult();
			if ($found)
			{
				$title = StringHelper::increment($title);
				$alias = StringHelper::increment($alias, 'dash');
				$query->clear('where');
			}
			else
			{
				break;
			}
		}
		
		return array($title, $alias);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param JTable $row A JTable object.
	 *        	
	 * @return array An array of conditions to add to ordering queries.
	 *        
	 */
	protected function getReorderConditions($row)
	{
		$conditions = array();
		if (property_exists($row, 'catid'))
		{
			$conditions[] = 'catid = ' . (int) $row->catid;
		}
		return $conditions;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param JTable $row A reference to a JTable object.
	 *        	
	 * @return void
	 *
	 */
	protected function prepareTable($row, $task)
	{		
		$user = Factory::getApplication()->getIdentity();
		if (property_exists($row, 'title'))
		{
			$titleField = 'title';
		}
		elseif (property_exists($row, 'name'))
		{
			$titleField = 'name';
		}				
		if (($task == 'save2copy') && $titleField)
		{			
			if (property_exists($row, 'alias'))
			{
				//Need to generate new title and alias
				list ($title, $alias) = $this->generateNewTitle($row, $row->alias, $row->{$titleField});
				$row->{$titleField} = $title;
				$row->alias = $alias;
			}
			else 
			{				
				$row->{$titleField} = StringHelper::increment($row->{$titleField});
			}			
		}
						
		if (property_exists($row, 'title'))
		{
			$row->title = htmlspecialchars_decode($row->title, ENT_QUOTES);
		}
		
		if (property_exists($row, 'name'))
		{
			$row->name = htmlspecialchars_decode($row->name, ENT_QUOTES);
		}
		
		if (property_exists($row, 'alias'))
		{
			if (empty($row->alias))
			{
				$row->alias = $row->{$titleField};
			}
			$row->alias = ApplicationHelper::stringUrlSafe($row->alias);

			// Handle alias for extra languages
			if (Multilanguage::isEnabled())
			{
				// Build alias alias for other languages
				$languages = DonationHelper::getLanguages();
				if (count($languages))
				{
					foreach ($languages as $language)
					{
						$sef = $language->sef;
						if (!$row->{'alias_' . $sef})
						{
							$row->{'alias_' . $sef} = ApplicationHelper::stringUrlSafe($row->{$titleField . '_' . $sef});
						}
						else
						{
							$row->{'alias_' . $sef} = ApplicationHelper::stringUrlSafe($row->{'alias_' . $sef});
						}
					}
				}
			}
		}
		if (empty($row->id))
		{
			// Set ordering to the last item if not set
			if (property_exists($row, 'ordering') && empty($row->ordering))
			{
				$db = Factory::getContainer()->get('db');
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName($this->table));
				$conditions = $this->getReorderConditions($row);
				if (count($conditions))
				{
					$query->where($conditions);
				}
				$db->setQuery($query);
				$max = $db->loadResult();
				$row->ordering = $max + 1;
			}
			
			if (property_exists($row, 'created_date') && !$row->created_date)
			{
				$row->created_date = Factory::getDate()->toSql();
			}
			
			if (property_exists($row, 'created_by') && !$row->created_by)
			{
				$row->created_by = $user->get('id');
			}
		}
		
		if (property_exists($row, 'modified_date') && !$row->modified_date)
		{
			$row->modified_date = Factory::getDate()->toSql();
		}
		if (property_exists($row, 'modified_by') && !$row->modified_by)
		{
			$row->modified_by = $user->get('id');
		}
		
		if (property_exists($row, 'params') && is_array($row->params))
		{
			$row->params = json_encode($row->params);
		}
	}
}
