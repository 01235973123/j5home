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

AriKernel::import('Xml.XmlHelper');

class AriDDLManager extends JObject 
{
	var $_tags = array(
		'Entities' => 'entities',
		'Entity' => 'entity',
		'Field' => 'field',
		'ForeignKey' => 'foreignKey',
		'Reference' => 'reference'
	);
	var $_struct;
	var $_version = '1.0';
	var $_typeMapping = array(
		'int' => 'integer',
		'varchar' => 'string',
		'date' => 'string',
		'clob' => 'string',
		'tinyint' => 'integer',
		'char' => 'string',
		'blob' => 'string',
		'enum' => 'string',
		'decimal' => 'float'
	);
	
	function __construct($configFile)
	{
		$this->_struct = $this->_parse($configFile);
	}
	
	function getTag($tagAlias)
	{
		return $this->_tags[$tagAlias];
	}
	
	function _getSystemTypeByType($type)
	{
		return isset($this->_typeMapping[$type])
			? $this->_typeMapping[$type]
			: 'string';
	}
	
	function _parse($configFile)
	{
		$struct = array();
		if (!@file_exists($configFile)) 
			return $struct;
		
		$entitiesTag = $this->getTag('Entities');
		$entityTag = $this->getTag('Entity');
		$xmlDoc = $this->_loadXml($configFile);
		if (empty($xmlDoc)) 
			return $struct;

		$version = AriXmlHelper::getAttribute($xmlDoc, 'version');
		if (!empty($version)) 
			$this->_version = $version;
		$entitiesNode =& AriXmlHelper::getSingleNode($xmlDoc, $entitiesTag);
		if (empty($entitiesNode)) 
			return $struct;
		
		$entityNodeList =& AriXmlHelper::getNode($entitiesNode, $entityTag);
		foreach ($entityNodeList as $entityNode)
		{
			$name = AriXmlHelper::getAttribute($entityNode, 'name');
			if (empty($name)) 
				continue;
			
			$entityInfo = $this->_parseEntity($entityNode);			
			if (!is_array($entityInfo) || count($entityInfo) == 0) 
				continue;
			
			$struct[$name] = $entityInfo;
		}

		return $struct;
	}

	function _parseEntity($entityNode)
	{
		$entityInfo = null;
		if (empty($entityNode)) 
			return $entityInfo;
		
		$virtual = AriXmlHelper::getAttribute($entityNode, 'virtual');
		$virtual = $virtual 
			? AriUtils::parseValueBySample($virtual, false)
			: false;
			
		if ($virtual) 
			return $this->_parseVirtualEntity($entityNode);
		
		$table = AriXmlHelper::getAttribute($entityNode, 'table');
		if (empty($table)) 
			return $entityInfo;
		
		$fields = $this->_parseFields($entityNode);
		if (!is_array($fields) || count($fields) == 0) 
			return $entityInfo;
		
		$references = $this->_parseReferences($entityNode);

		$entityInfo = array(
			'table' => $table, 
			'fields' => $fields, 
			'references' => $references,
			'primary' => $this->_getPrimaryKeys($fields));
		
		return $entityInfo;
	}
	
	function _getPrimaryKeys($fields)
	{
		$primaryKeys = array();
		if (empty($fields)) 
			return $primaryKeys;
		
		foreach ($fields as $name => $fieldInfo)
		{
			if (!empty($fieldInfo['primaryKey'])) 
				$primaryKeys[] = $name;
		}
		
		return $primaryKeys;
	}
	
	function _parseReferences($entityNode)
	{
		$references = array(); 
		if (empty($entityNode)) 
			return $references;

		$references['foreignKeys'] = $this->_parseForeignKeys($entityNode);
		
		return $references;
	}
	
	function _parseForeignKeys($entityNode)
	{
		$foreignKeys = array();
		if (empty($entityNode)) 
			return $foreignKeys;
		
		$foreignKeyTag = $this->getTag('ForeignKey');
		$foreignNodeList =& AriXmlHelper::getNode($entityNode, $foreignKeyTag);
		if (empty($foreignNodeList)) 
			return $foreignKeys;
		
		$refTag = $this->getTag('Reference'); 
		foreach ($foreignNodeList as $foreignNode)
		{
			$foreignEntity = AriXmlHelper::getAttribute($foreignNode, 'foreignEntity');
			if (empty($foreignEntity)) 
				continue;

			$refNode =& AriXmlHelper::getSingleNode($foreignNode, $refTag);
			if (empty($refNode)) 
				continue;
			
			$localField = AriXmlHelper::getAttribute($refNode, 'local');
			$foreignField = AriXmlHelper::getAttribute($refNode, 'foreign');
			
			if (empty($localField) || empty($foreignField)) 
				continue;
			
			$foreignKeys[$localField] = array(
				'foreignEntity' => $foreignEntity, 
				'localField' => $localField, 
				'foreignField' => $foreignField
			);
		}
		
		return $foreignKeys;
	}
	
	function _parseFields($entityNode)
	{
		$fieldsInfo = null;
		if (empty($entityNode)) 
			return $fieldsInfo;
		
		$fieldTag = $this->getTag('Field');
		$fieldNodeList =& AriXmlHelper::getNode($entityNode, $fieldTag);
		
		if (empty($fieldNodeList)) 
			return $fieldInfo;
		
		$fieldsInfo = array();
		foreach ($fieldNodeList as $fieldNode)
		{
			$name = AriXmlHelper::getAttribute($fieldNode, 'name');
			if (empty($name)) 
				continue;
			
			$primaryKey = (bool)AriXmlHelper::getAttribute($fieldNode, 'primaryKey');
			$required = (bool)AriXmlHelper::getAttribute($fieldNode, 'required');
			$size = intval(AriXmlHelper::getAttribute($fieldNode, 'size'), 10);
			$type = AriXmlHelper::getAttribute($fieldNode, 'type');
			$systemType = $this->_getSystemTypeByType($type);
			$default = AriXmlHelper::getAttribute($fieldNode, 'default');
			
			$fieldsInfo[$name] = array(
				'required' => $required, 
				'size' => $size, 
				'type' => $type, 
				'systemType' => $systemType
			);
			if ($primaryKey) 
				$fieldsInfo[$name]['primaryKey'] = $primaryKey;
			if (!is_null($default)) 
				$fieldsInfo[$name]['default'] = AriUtils::parseValue($default, $systemType);
		}
		
		return $fieldsInfo;
	}
	
	function _parseVirtualEntity($entityNode)
	{
		$entityInfo = null;

		$refEntity = AriXmlHelper::getAttribute($entityNode, 'refEntity');
		if (empty($refEntity)) 
			return $entityInfo;

		$entityInfo = array(
			'refEntity' => $refEntity,
			'virtual' => true,
			'references' => $this->_parseReferences($entityNode)
		);
		
		return $entityInfo;
	}
	
	function _loadXml($configFile)
	{
		$xml = AriXmlHelper::getXml($configFile);

		return $xml->document;
	}
	
	function getVersion()
	{
		return $this->_version;
	}
	
	function getStructure()
	{
		return $this->_struct;
	}
	
	function getBaseEntity($entity)
	{
		$entityInfo = isset($this->_struct[$entity])
			? $this->_struct[$entity]
			: null;
			
		if (empty($entityInfo)) 
			return $entity;
		
		if (!empty($entityInfo['virtual']) && isset($entityInfo['refEntity']))
		{
			$entity = $this->getBaseEntity($entityInfo['refEntity']);
		}
		
		return $entity;
	}
	
	function getEntityInfo($entity, $getBase = false)
	{
		if ($getBase) 
			$entity = $this->getBaseEntity($entity);

		return isset($this->_struct[$entity])
			? $this->_struct[$entity]
			: null;
	}

	function getEntityProperty($entity, $property, $getBase = false, $defaultValue = null)
	{
		$entityInfo = $this->getEntityInfo($entity, $getBase);
		
		return isset($entityInfo[$property])
			? $entityInfo[$property]
			: $defaultValue;
	}
	
	function getEntityTable($entity)
	{
		return $this->getEntityProperty($entity, 'table', true);
	}
	
	function getEntityFields($entity)
	{
		return $this->getEntityProperty($entity, 'fields', true);
	}
	
	function getFieldInfo($entity, $field)
	{
		$fields = $this->getEntityFields($entity);
		
		return isset($fields[$field])
			? $fields[$field]
			: null;
	}
	
	function getFieldProperty($entity, $field, $property, $defValue = null)
	{
		$fieldInfo = $this->getFieldInfo($entity, $field);
		
		return isset($fieldInfo[$property])
			? $fieldInfo[$property]
			: $defValue;
	}
	
	function getFieldDefaultValue($entity, $field)
	{
		$default = $this->getFieldProperty($entity, $field, 'default', null);
		if (!is_null($default)) 
			return $default;
		
		$required = $this->getFieldProperty($entity, $field, 'required', false);
		if ($required)
		{
			$default = $this->isNumberField($entity, $field)
				? 0
				: '';
		}
		
		return $default;
	}
	
	function isVirtual($entity)
	{
		$entityInfo = $this->getEntityInfo($entity);

		return !empty($entityInfo['virtual']);
	}
	
	function isBool($entity, $field)
	{
		$isNumber = $this->isNumberField($entity, $field);
		if (!$isNumber) 
			return false;
		
		$fieldInfo = $this->getFieldInfo($entity, $field);
		if (empty($fieldInfo['size'])) 
			return false;
		
		return ($fieldInfo['size'] == 1);
	}
	
	function isNumberField($entity, $field)
	{
		$numberTypes = array('integer', 'double', 'float');
		$type = $this->getFieldProperty($entity, $field, 'systemType', 'string');

		return in_array($type, $numberTypes);
	}
	
	function isStringField($entity, $field)
	{
		return ($this->getFieldProperty($entity, $field, 'systemType', 'string') == 'string');
	}
	
	function getPrimaryFields($entity)
	{
		return $this->getEntityProperty($entity, 'primary', true);
	}
	
	function getForeignReferences($entity)
	{
		$refs = $this->getEntityProperty($entity, 'references', true);
		$foreignKeys = isset($refs['foreignKeys']) ? $refs['foreignKeys'] : array();
		if ($this->isVirtual($entity))
		{
			$vRefs = $this->getEntityProperty($entity, 'references', false);
			$vForeignKeys = isset($vRefs['foreignKeys']) ? $vRefs['foreignKeys'] : array();
			foreach ($vForeignKeys as $key => $value)
			{
				$foreignKeys[$key] = $value;
			}
		}

		return $foreignKeys;
	}
}