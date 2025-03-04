<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

jimport('joomla.database.table');
AriKernel::import('Joomla.Database.DBUtils');
AriKernel::import('Utils.DateUtility');
AriKernel::import('Utils.ArrayHelper');

define('ARI_TABLE_RELATION_ONETOONE', 1);
define('ARI_TABLE_RELATION_ONETOMANY', 2);

class AriTable extends JTable 
{
	var $_vars;
	var $_dbVars;
	var $_relations = array();
	var $_preparedRelations = array();
	
	function isNew()
	{
		$key = $this->getKeyName();
		
		return empty($this->$key);
	}
	
	function bind($array, $ignore = '') 
	{
		if (is_null($array))
			return false;
	
		if ($this->propertyExists('asset_id') && isset($array['rules']) && (is_array($array['rules']) || is_object($array['rules'])))
		{
			if (is_object($array['rules']))
				$array['rules'] = Joomla\Utilities\ArrayHelper::fromObject($array['rules']);

			$array['rules'] = AriArrayHelper::removeEmptyValues($array['rules']);
			$rules = new JAccessRules($array['rules']);

			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}
	
	function load($keys = null, $reset = true)
	{
		if (!$this->hasRelations())
			if (!is_array($keys))
				return parent::load($keys);
			else if (J1_6)
				return parent::load($keys, $reset);

		if (empty($keys)) 
		{
			$keyName = $this->_tbl_key;
			$keyValue = $this->$keyName;

			if (empty($keyValue))
				return true;

			$keys = array($keyName => $keyValue);
		}
		else if (!is_array($keys)) 
		{
			$keys = array($this->_tbl_key => $keys);
		}
		
		return $this->customLoad(array(&$this, 'loadByKeys'), array($keys));
	}
	
	function loadByKeys($query, $queryParams, $keys)
	{
		$db =& $this->getDBO();
		$tblAlias = $queryParams['tblAlias'];
		foreach ($keys as $field => $value)
		{
			if (!$this->propertyExists($field)) 
			{
				$this->setError(
					sprintf('%1$s: property "%2$s" does not exist',
						__CLASS__ . '::' . __FUNCTION__ . '()',
						$field
					)
				);
				return false;
			}

			$query->where($tblAlias . '.' . (J3_0 ? $db->quoteName($field) : $db->nameQuote($field)) . ' = ' . $db->quote($value));
		}

		return $query;
	}
	
	function customLoad($postQueryHandler = null, $postQueryParams = array(), $offset = 0, $limit = 0)
	{
		$tblAlias = 'T0';
		$db =& $this->getDBO();
		$query = AriDBUtils::getQuery();

		$query->from($this->_tbl . ' ' . $tblAlias);

		$select = $this->getSelectFields($tblAlias, '$0');

		$entities = array();
		$entities[0] =& $this;
		$relations = $this->getRelations();
		$hasOneToMany = false;
		foreach ($relations as $key => $relation)
		{
			$entity = $relation['entity'];
			$query->leftJoin(
				sprintf('%1$s T%2$d ON T%3$s.%4$s = T%2$d.%5$s',
					$entity->getTableName(),
					$key,
					$relation['parent'],
					$relation['key'],
					$relation['relField']//$entity->getKeyName()
				)
			);

			$select = array_merge($select, $entity->getSelectFields('T' . $key, '$' . $key));

			$entityKey = $relation['entityKey'];
			$parentEntity =& $entities[$relation['parent']];
			if ($entityKey == 'Question')
			{
				//print_r($relation);
				//echo $entityKey;
				//print_r($entities);
				//print_r($parentEntity);
				//exit();
			}
			
			if ($relation['type'] == ARI_TABLE_RELATION_ONETOMANY)
			{
				$entities[$key] = $relation['entity'];
			}
			else
				$entities[$key] =& $parentEntity->$entityKey;
			
			if ($relation['type'] == ARI_TABLE_RELATION_ONETOMANY)
				$hasOneToMany = true;
		}

		$query->select(join(',', $select));
		
		if (!is_null($postQueryHandler))
		{
			array_unshift(
				$postQueryParams, 
				$query, 
				array('tblAlias' => $tblAlias, 'relations' => $relations, 'entities' => &$entities));

			$query = call_user_func_array(
				$postQueryHandler, 
				$postQueryParams);
			
			if (is_null($query))
				return false;
		}

		if ($hasOneToMany)
		{
			foreach ($relations as $key => $relation)
			{
				if ($relation['type'] != ARI_TABLE_RELATION_ONETOMANY)
					continue ;
					
				$query->order(
					sprintf('T%1$s.%2$s ASC',
						$relation['parent'],
						$relation['key']
					)
				);
			}
		}

		$db->setQuery((string)$query/*, $offset, $limit*/);
		$data = null;
		if (!$hasOneToMany)
			$data = $db->loadAssoc();
		else 
			$data =	$db->loadAssocList();

		if ($db->getErrorNum()) 
		{
			$this->setError($db->getErrorMsg());
			
			return false;
		}
		
		$result = true;
		if (!$hasOneToMany)
		{
			$result = $this->loadOneToOne($data, $entities);
		}
		else
		{
			$result = $this->loadOneToMany($data, $entities, $relations);
		}
		
		return $result;
	}
	
	function loadOneToMany($data, $entities, $relations)
	{
		if (empty($data) || !is_array($data) || count($data) == 0) 
		{
			$this->setError(
				sprintf('%1$s: empty $data',
					__CLASS__ . '::' . __FUNCTION__ . '()'
				)
			);
			return false;
		}

		$isFirstRow = true;
		foreach ($data as $dataItem)
		{
			$row = $this->normalizeData($dataItem);
			foreach ($row as $key => $rowData)
			{
				if (!isset($entities[$key]))
					return false;

				$entity =& $entities[$key];
				
				if (!is_array($entity))
				{
					if ($isFirstRow && $entity->bind($rowData) === false)
						return false;
				}
				else
				{
					$rel = $relations[$key];
					$entityItem = clone($rel['entity']);
					if ($entityItem->bind($rowData) === false)
						return false;

					$entity[] = $entityItem;
				}

				$isFirstRow = false;
			}
		}
		
		return true;
	}
	
	function loadOneToOne($row, $entities)
	{
		if (empty($row)) 
		{
			$this->setError(
				sprintf('%1$s: empty row',
					__CLASS__ . '::' . __FUNCTION__ . '()'
				)
			);
			return false;
		}

		$row = $this->normalizeData($row);
		foreach ($row as $key => $data)
		{
			if (!isset($entities[$key]))
				return false;

			$entity =& $entities[$key];
			if ($entity->bind($data) === false)
				return false; 
		}
		
		return true;
	}

	function store($updateNulls = null)
	{
		if ($this->autoFillSystemFields())
			$this->fillSystemFields();

		return parent::store($updateNulls);
	}
	
	function autoFillSystemFields()
	{
		return true;
	}
	
	function fillSystemFields()
	{
		$user =& JFactory::getUser();
		$userId = $user->get('id');		
		$now = AriDateUtility::getDbUtcDate();

		if ($this->isNew())
		{
			if ($this->propertyExists('Created') && empty($this->Created))
				$this->Created = $now;
			if ($this->propertyExists('CreatedBy') && empty($this->CreatedBy))
				$this->CreatedBy = $userId;
		}
		else
		{
			if ($this->propertyExists('Modified') && empty($this->Modified))
				$this->Modified = $now;
			if ($this->propertyExists('ModifiedBy') && empty($this->ModifiedBy))
				$this->ModifiedBy = $userId;
		}		
	}
	
	function propertyExists($name)
	{
		if (isset($this->name))
			return true;
		
		if (is_null($this->_vars))
			$this->_vars = get_object_vars($this);

		return array_key_exists($name, $this->_vars);
	}
	
	function getDbProperties()
	{
		if (is_null($this->_dbVars))
		{
			$this->_dbVars = array();
			$vars = get_object_vars($this);
			foreach ($vars as $key => $value)
			{
				if (substr($key, 0, 1) != '_') 
				{
					if (!is_object($value) && !is_array($value)) 
						$this->_dbVars[] = $key; 
				}
			}
		}

		return $this->_dbVars;
	}
	
	function hasRelations()
	{
		return count($this->_relations) > 0;
	}
	
	function addRelation($key, $field, $relationType = ARI_TABLE_RELATION_ONETOONE, $entity = null, $relField = null)
	{
		if (is_null($entity))
		{
			if ($relationType == ARI_TABLE_RELATION_ONETOONE)
				$entity = get_class($this->$field);
		}
		
		if (is_null($relField) && !is_null($entity))
		{
			$db =& $this->getDBO();
			$childEntity = new $entity($db);
			$relField = $childEntity->getKeyName();
		}
		
		$this->_relations[$key] = array(
			'type' => $relationType,
			'field' => $field,
			'entity' => $entity,
			'relField' => $relField
		);
	}
	
	function getRelations($loadChildRelations = true, $parent = 0)
	{
		$db =& $this->getDBO();
		$relations = array();
		$relIdx = $parent + 1;
		foreach ($this->_relations as $key => $rel)
		{
			$relField = $rel['field'];
			
			$entity = $rel['entity'];//$this->$relField;
			$entity = new $entity($db);

			$relation = array(
				'entityKey' => $relField,
				'entity' => $entity,
				'relField' => $rel['relField'],
				'key' => $key,
			 	'parent' => $parent,
				'type' => $rel['type']
			);

			$relations[$relIdx] = $relation;
			if ($loadChildRelations && $entity->hasRelations())
			{
				$childRelations = $entity->getRelations($loadChildRelations, $relIdx);
				foreach ($childRelations as $childKey => $childRelation)
				{
					$relations[$childKey] = $childRelation;
				}
				
				$relIdx += count($childRelations);
			}
			
			++$relIdx;
		}

		return $relations;
	}

	function getSelectFields($tblAlias, $postfix)
	{
		$selectFields = array();
		$fields = $this->getDbProperties();

		foreach ($fields as $key => $field)
			$selectFields[] = $tblAlias . '.' . $field . ' AS ' . $field . $postfix;
		
		return $selectFields;
	}
	
	function normalizeData($data)
	{
		$normalizedData = array();

		foreach ($data as $key => $value)
		{
			list($field, $idx) = explode('$', $key);
			if (!isset($normalizedData[$idx]))
				$normalizedData[$idx] = array();
				
			$normalizedData[$idx][$field] = $value;
		}
		
		return $normalizedData;
	}
	
	function toArray()
	{
		$data = array();
		$fields = $this->getDbProperties();

		foreach ($fields as $key => $field)
		{
			$data[$field] = $this->$field;
		}
		
		return $data;
	}
	
	function getQuery()
	{
		$db =& $this->getDBO();
		
		return $db->getQuery();
	}
}