<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Register the element class with the loader.
JLoader::register('JElement', dirname(__FILE__) . '/parameter/element.php');

class JParameterLegacy extends JRegistry
{
	/**
	 * @var    string  The raw params string
	 * @since  11.1
	 */
	protected $_raw = null;

	/**
	 * @var    object  The XML params element
	 * @since  11.1
	 */
	protected $_xml = null;

	/**
	 * @var    array  Loaded elements
	 * @since  11.1
	 */
	protected $_elements = array();

	/**
	 * @var    array  Directories, where element types can be stored
	 * @since  11.1
	 */
	protected $_elementPath = array();

	/**
	 * Constructor
	 *
	 * @param   string  $data  The raw parms text.
	 * @param   string  $path  Path to the XML setup file.
	 *
	 */
	public function __construct($data = '', $path = '')
	{
		parent::__construct('_default');

		// Set base path.
		$this->_elementPath[] = dirname(__FILE__) . '/parameter/element';

		if ($data = trim($data ?? ''))
		{
			if (strpos($data, '{') === 0)
			{
				$this->loadString($data);
			}
			else
			{
				$this->loadINI($data);
			}
		}

		if ($path)
		{
			$this->loadSetupFile($path);
		}

		$this->_raw = $data;
	}

	/**
	 * Sets a default value if not alreay assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   string  $default  An optional value for the parameter.
	 * @param   string  $group    An optional group for the parameter.
	 *
	 * @return  string  The value set, or the default if the value was not previously set (or null).
	 *
	 */
	public function def($key, $default = '', $group = '_default')
	{
		$value = $this->get($key, (string) $default, $group);

		return $this->set($key, $value);
	}

	/**
	 * Sets the XML object from custom XML files.
	 *
	 * @param   JSimpleXMLElement  &$xml  An XML object.
	 *
	 * @return  void
	 *
	 */
	public function setXML(&$xml)
	{
		if (is_object($xml))
		{
			$group = isset($xml['group']) ? (string)$xml['group'] : null;
			if ($group)
			{
				$this->_xml[$group] = $xml;
			}
			else
			{
				$this->_xml['_default'] = $xml;
			}

			$dir = isset($xml['addpath']) ? (string)$xml['addpath'] : null;
			if ($dir)
			{
				$this->addElementPath(JPATH_ROOT . str_replace('/', DS, $dir));
			}
		}
	}

	/**
	 * Bind data to the parameter.
	 *
	 * @param   mixed   $data   An array or object.
	 * @param   string  $group  An optional group that the data should bind to. The default group is used if not supplied.
	 *
	 * @return  boolean  True if the data was successfully bound, false otherwise.
	 */
	public function bind($data, $group = '_default')
	{
		if (is_array($data))
		{
			return $this->loadArray($data);
		}
		elseif (is_object($data))
		{
			return $this->loadObject($data);
		}
		else
		{
			return $this->loadString($data);
		}
	}

	/**
	 * Render the form control.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 *
	 * @return  string  HTML
	 */
	public function renderInt($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$params = $this->getParams($name, $group);
		$html = array(
		);

		$description = isset($this->_xml['addpath']) ? (string)$this->_xml['description'] : null;
		if ($description)
		{
			// Add the params description to the display
			$desc = JText::_($description);
			$html[] = '<p class="paramrow_desc">' . $desc . '</p>';
		}

		foreach ($params as $param)
		{
			if (!$param[0]) {
				$html[] = $param[1];
			} else {
				if (J4) {
					$html[] = '<div class="control-group">';
					$html[] = '	<div class="control-label">' . $param[0] . '</div>';
					$html[] = '	<div class="controls">' . $param[1] . '</div>';
					$html[] = '</div>';
				} else {
					$html[] = $param[0];
					$html[] = $param[1];	
				}
			}
		}

		if (count($params) < 1)
		{
			$html[] = "<p class=\"noparams\">" . JText::_('JLIB_HTML_NO_PARAMETERS_FOR_THIS_ITEM') . "</p>";
		}

		return implode(PHP_EOL, $html);
	}

	/**
	 * Render all parameters to an array.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 *
	 * @return  array
	 */
	public function renderToArray($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}
		$results = array();
		foreach ($this->_xml[$group]->children() as $param)
		{
			$result = $this->getParam($param, $name, $group);
			$results[$result[5]] = $result;
		}
		return $results;
	}

	/**
	 * Return the number of parameters in a group.
	 *
	 * @param   string  $group  An optional group. The default group is used if not supplied.
	 *
	 * @return  mixed  False if no params exist or integer number of parameters that exist.
	 */
	public function getNumParams($group = '_default')
	{
		if (!isset($this->_xml[$group]) || !count($this->_xml[$group]->children()))
		{
			return false;
		}
		else
		{
			return count($this->_xml[$group]->children());
		}
	}

	/**
	 * Get the number of params in each group.
	 *
	 * @return  array  Array of all group names as key and parameters count as value.
	 */
	public function getGroups()
	{
		if (!is_array($this->_xml))
		{
			return false;
		}

		$results = array();
		foreach ($this->_xml as $name => $group)
		{
			$results[$name] = $this->getNumParams($name);
		}
		return $results;
	}

	/**
	 * Render all parameters.
	 *
	 * @param   string  $name   An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group  An optional group to render.  The default group is used if not supplied.
	 *
	 * @return  array  An array of all parameters, each as array of the label, the form element and the tooltip.
	 */
	public function getParams($name = 'params', $group = '_default')
	{
		if (!isset($this->_xml[$group]))
		{
			return false;
		}

		$results = array();
		foreach ($this->_xml[$group]->children() as $param)
		{
			$results[] = $this->getParam($param, $name, $group);
		}
		return $results;
	}

	/**
	 * Render a parameter type.
	 *
	 * @param   object  &$node         A parameter XML element.
	 * @param   string  $control_name  An optional name of the HTML form control. The default is 'params' if not supplied.
	 * @param   string  $group         An optional group to render.  The default group is used if not supplied.
	 *
	 * @return  array  Any array of the label, the form element and the tooltip.
	 */
	public function getParam(&$node, $control_name = 'params', $group = '_default')
	{
		// Get the type of the parameter.
		$type = (string)$node['type'];
		$element = $this->loadElement($type);

		// Check for an error.
		if ($element === false)
		{
			$result = array();
			$result[0] = (string)$node['name'];
			$result[1] = JText::_('Element not defined for type') . ' = ' . $type;
			$result[5] = $result[0];
			return $result;
		}

		// Get value.
		$value = $this->get((string)$node['name'], (string)$node['default'], $group);

		return $element->render($node, $value, $control_name);
	}

	/**
	 * Loads an XML setup file and parses it.
	 *
	 * @param   string  $path  A path to the XML setup file.
	 *
	 * @return  object
	 */
	public function loadSetupFile($path)
	{
		$result = false;

		if ($path)
		{
			$xmlDoc = simplexml_load_file($path);
			if ($xmlDoc)
			{
				$xml = new stdClass();
				$xml->document = $xmlDoc;
				if ($params = $xml->document->params)
				{
					foreach ($params as $param)
					{
						$this->setXML($param);
						$result = true;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Loads an element type.
	 *
	 * @param   string   $type  The element type.
	 * @param   boolean  $new   False (default) to reuse parameter elements; true to load the parameter element type again.
	 *
	 * @return  object
	 */
	public function loadElement($type, $new = false)
	{
		$signature = md5($type);

		if ((isset($this->_elements[$signature]) && !($this->_elements[$signature] instanceof __PHP_Incomplete_Class)) && $new === false)
		{
			return $this->_elements[$signature];
		}

		$elementClass = 'JElement' . $type;
		if (!class_exists($elementClass))
		{
			if (isset($this->_elementPath))
			{
				$dirs = $this->_elementPath;
			}
			else
			{
				$dirs = array();
			}

			$file = JFilterInput::getInstance()->clean(str_replace('_', DS, $type) . '.php', 'path');

			jimport('joomla.filesystem.path');
			if ($elementFile = JPath::find($dirs, $file))
			{
				include_once $elementFile;
			}
			else
			{
				$false = false;
				return $false;
			}
		}

		if (!class_exists($elementClass))
		{
			$false = false;
			return $false;
		}

		$this->_elements[$signature] = new $elementClass($this);

		return $this->_elements[$signature];
	}

	/**
	 * Add a directory where JParameter should search for element types.
	 *
	 * You may either pass a string or an array of directories.
	 *
	 * JParameter will be searching for a element type in the same
	 * order you added them. If the parameter type cannot be found in
	 * the custom folders, it will look in
	 * JParameter/types.
	 *
	 * @param   mixed  $path  Directory (string) or directories (array) to search.
	 *
	 * @return  void
	 */
	public function addElementPath($path)
	{
		// Just force path to array.
		settype($path, 'array');

		// Loop through the path directories.
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed.
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs.
			array_unshift($this->_elementPath, $dir);
		}
	}
}