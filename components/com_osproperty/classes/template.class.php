<?php

/*------------------------------------------------------------------------
# template.class.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OspropertyTemplate {
	/**
    * Constructor.
    * @param string $path path to template files
    * @param string $cache_id unique cache identifier
    * @param int $expire number of seconds the cache will live
    * @return void
    */
	var $path = "";
	
	public function OspropertyTemplate($path='', $expire = 0 ) {
		global $mainframe;
		$db = Factory::getDbo();
		$themename = OSPHelper::getThemeName();
		$this->path = ($path == "") ?  JPATH_ROOT.'/components/com_osproperty/templates/'.$themename.'/' : $path;
		$this->default_path = JPATH_ROOT.'/components/com_osproperty/templates/default';
	}
	
	static function realPath()
	{
		$db = Factory::getDbo();
        $themename = OSPHelper::getThemeName();
		return JPATH_ROOT.'/components/com_osproperty/templates/'.$themename;
	}
	
	static function livePath()
	{
		$db = Factory::getDbo();
        $themename = OSPHelper::getThemeName();
		return Uri::root().'/components/com_osproperty/templates/'.$themename;
	}

	/**
    * Set the path to the template files.
    *
    * @param string $path path to template files
    *
    * @return void
    */
	public function set_path($path) {
		$this->path = $path;
	}

	/**
    * Set a template variable.
    *
    * @param string $name name of the variable to set
    * @param mixed $value the value of the variable
    *
    * @return void
    */
	public function set($name, $value) {
		$this->vars[$name] = $value;
	}

	/**
    * Set a bunch of variables at once using an associative array.
    *
    * @param array $vars array of vars to set
    * @param bool $clear whether to completely overwrite the existing vars
    *
    * @return void
    */
	public function set_vars($vars, $clear = false) {
		if($clear) {
			$this->vars = $vars;
		}
		else {
			if(is_array($vars)) {
				$this->vars = array_merge($this->vars, $vars);
			}
		}
	}
	/**
	 * Returns the value of a configuration parameter of this theme
	 *
	 * @param string $var
	 * @param mixed $default
	 * @return mixed
	 */
	public function get_cfg( $var, $default='' ) {

		return $this->config->get( $var, $default );
	}
	
	/**
	 * Sets the configuration parameter of this theme
	 *
	 * @param string $var
	 * @param mixed $value
	 */
	public function set_cfg( $var, $value ) {
		if( is_a( $this->config, 'vmParameters' )) {
			$this->config->set( $var, $value );
		}
	}
	
	/**
    * Open, parse, and return the template file.
    *
    * @param string string the template file name
    *
    * @return string
    */
	public function fetch($file) 
	{
		$mainframe = Factory::getApplication();
		extract($this->vars);          // Extract the vars to local namespace
		if($this->path == '' && $this->default_path == '')
		{
			if($this->vars['path'] != '')
			{
				$this->path = $this->vars['path'];
			}
		}
		if($this->path == '')
		{
			if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/'.$file))
			{
				$this->path = JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/';
			}
			else
			{
				$this->path = JPATH_ROOT.'/components/com_osproperty/helpers/layouts/';
			}
		}

		ob_start();                    // Start output buffering

		if(substr($this->path, strlen($this->path) -1) != "/")
		{
			$this->path .= "/";
		}
		if( is_file( $this->path . $file ) ) 
		{
			include($this->path . $file);  // Include the file
		} 
		elseif( is_file( $this->default_path .DS. $file ) ) 
		{

			include( $this->default_path .DS. $file );
		}
		$contents = ob_get_contents(); // Get the contents of the buffer
		ob_end_clean();                // End buffering and discard
		return $contents;              // Return the contents
	}
}

// Check if there is an extended class in the Themes and if it is allowed to use them
// If settings are loaded, extended Classes are allowed and the class exisits...

// Otherwise we have to use the original classname to extend the core-class
class os_OspropertyTemplate extends OspropertyTemplate 
{
	function os_OspropertyTemplate() 
	{
		parent::OspropertyTemplate();
	}
}
?>