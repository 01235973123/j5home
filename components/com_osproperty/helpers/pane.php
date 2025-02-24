<?php

/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2023 joomdonation.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Log\Log;
/**
 * JPane abstract class
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 * @deprecated  12.1    Use HTMLHelper::_ static helpers
 */
abstract class JPane extends CMSObject
{
	public $useCookies = false;

	/**
	 * Returns a JPanel object.
	 *
	 * @param   string  $behavior  The behavior to use.
	 * @param   array   $params    Associative array of values.
	 *
	 * @return  object
	 *
	 * @deprecated    12.1
	 * @since   11.1
	 *
	 */
	public static function getInstance($behavior = 'Tabs', $params = array())
	{
		// Deprecation warning.
		Log::add('JPane::getInstance is deprecated.', Log::WARNING, 'deprecated');

		$classname = 'JPane' . $behavior;
		$instance = new $classname($params);

		return $instance;
	}

	/**
	 * Creates a pane and creates the javascript object for it.
	 *
	 * @param   string  $id  The pane identifier.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	abstract public function startPane($id);

	/**
	 * Ends the pane.
	 *
	 * @since   11.1
	 *
	 * @return  string
	 *
	 * @deprecated    12.1
	 */
	abstract public function endPane();

	/**
	 * Creates a panel with title text and starts that panel.
	 *
	 * @param   string  $text  The panel name and/or title.
	 * @param   string  $id    The panel identifer.
	 *
	 * @return  string
	 *
	 * @deprecated  12.1
	 * @since   11.1
	 */
	abstract public function startPanel($text, $id);

	/**
	 * Ends a panel.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated    12.1
	 */
	abstract public function endPanel();

	/**
	 * Load the javascript behavior and attach it to the document.
	 *
	 * @return  void
	 *
	 * @deprecated    12.1
	 * @since   11.1
	 */
	abstract protected function _loadBehavior();
}

/**
 * JPanelTabs class to to draw parameter panes.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 * @deprecated  Use HTMLHelper::_ static helpers
 */
class JPaneTabs extends JPane
{
	/**
	 * Constructor.
	 *
	 * @param   array  $params  Associative array of values
	 *
	 * @since   11.1
	 */
	public function __construct($params = array())
	{
		// Deprecation warning.
		Log::add('JPaneTabs is deprecated.', Log::WARNING, 'deprecated');

		static $loaded = false;

		parent::__construct($params);

		if (!$loaded)
		{
			$this->_loadBehavior($params);
			$loaded = true;
		}
	}

	/**
	 * Creates a pane and creates the javascript object for it.
	 *
	 * @param   string  $id  The pane identifier.
	 *
	 * @return  string  HTML to start the pane dl
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function startPane($id)
	{

		// Deprecation warning.
		Log::add('JPane::startPane is deprecated.', Log::WARNING, 'deprecated');

		return '<dl class="tabs" id="' . $id . '">';
	}

	/**
	 * Ends the pane.
	 *
	 * @return  string  HTML to end the pane dl
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function endPane()
	{
		// Deprecation warning.
		Log::add('JPaneTabs::endPane is deprecated.', Log::WARNING, 'deprecated');

		return "</dl>";
	}

	/**
	 * Creates a tab panel with title text and starts that panel.
	 *
	 * @param   string  $text  The name of the tab
	 * @param   string  $id    The tab identifier
	 *
	 * @return  string  HTML for the dt tag.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function startPanel($text, $id)
	{
		// Deprecation warning.
		Log::add('JPaneTabs::startPanel is deprecated.', Log::WARNING, 'deprecated');

		return '<dt class="' . $id . '"><span>' . $text . '</span></dt><dd>';
	}

	/**
	 * Ends a tab page.
	 *
	 * @return  string   HTML for the dd tag.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function endPanel()
	{
		// Deprecation warning.
		Log::add('JPaneTabs::endPanel is deprecated.', Log::WARNING, 'deprecated');

		return "</dd>";
	}

	/**
	 * Load the javascript behavior and attach it to the document.
	 *
	 * @param   array  $params  Associative array of values
	 *
	 * @return  void
	 *
	 * @since   11.1
	 * @deprecated    12.1
	 */
	protected function _loadBehavior($params = array())
	{
		// Deprecation warning.
		Log::add('JPaneTabs::_loadBehavior is deprecated.', Log::WARNING, 'deprecated');

		// Include mootools framework
		//HTMLHelper::_('behavior.framework', true);

		$document = Factory::getDocument();

		$options = '{';
		$opt['onActive'] = (isset($params['onActive'])) ? $params['onActive'] : null;
		$opt['onBackground'] = (isset($params['onBackground'])) ? $params['onBackground'] : null;
		$opt['display'] = (isset($params['startOffset'])) ? (int) $params['startOffset'] : null;
		foreach ($opt as $k => $v)
		{
			if ($v)
			{
				$options .= $k . ': ' . $v . ',';
			}
		}
		if (substr($options, -1) == ',')
		{
			$options = substr($options, 0, -1);
		}
		$options .= '}';

		$js = '	window.addEvent(\'domready\', function(){ $$(\'dl.tabs\').each(function(tabs){ new JTabs(tabs, ' . $options . '); }); });';

		$document->addScriptDeclaration($js);
		HTMLHelper::_('script', 'system/tabs.js', false, true);
	}
}

/**
 * JPanelSliders class to to draw parameter panes.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 *
 * @deprecated  Use HTMLHelper::_ static helpers
 */
class JPaneSliders extends JPane
{
	/**
	 * Constructor.
	 *
	 * @param   array  $params  Associative array of values.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function __construct($params = array())
	{
		// Deprecation warning.
		Log::add('JPanelSliders::__construct is deprecated.', Log::WARNING, 'deprecated');

		static $loaded = false;

		parent::__construct($params);

		if (!$loaded)
		{
			$this->_loadBehavior($params);
			$loaded = true;
		}
	}

	/**
	 * Creates a pane and creates the javascript object for it.
	 *
	 * @param   string  $id  The pane identifier.
	 *
	 * @return  string  HTML to start the slider div.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function startPane($id)
	{
		// Deprecation warning.
		Log::add('JPaneSliders::startPane is deprecated.', Log::WARNING, 'deprecated');

		return '<div id="' . $id . '" class="pane-sliders">';
	}

	/**
	 * Ends the pane.
	 *
	 * @return  string  HTML to end the slider div.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function endPane()
	{
		// Deprecation warning.
		Log::add('JPaneSliders::endPane is deprecated.', Log::WARNING, 'deprecated');

		return '</div>';
	}

	/**
	 * Creates a tab panel with title text and starts that panel.
	 *
	 * @param   string  $text  The name of the tab.
	 * @param   string  $id    The tab identifier.
	 *
	 * @return  string  HTML to start the tab panel div.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function startPanel($text, $id)
	{
		// Deprecation warning.
		Log::add('JPaneSliders::startPanel is deprecated.', Log::WARNING, 'deprecated');

		return '<div class="panel">' . '<h3 class="pane-toggler title" id="' . $id . '"><a href="javascript:void(0);"><span>' . $text
			. '</span></a></h3>' . '<div class="pane-slider content">';
	}

	/**
	 * Ends a tab page.
	 *
	 * @return  string  HTML to end the tab divs.
	 *
	 * @since   11.1
	 *
	 * @deprecated    12.1
	 */
	public function endPanel()
	{
		// Deprecation warning.
		Log::add('JPaneSliders::endPanel is deprecated.', Log::WARNING, 'deprecated');

		return '</div></div>';
	}

	/**
	 * Load the javascript behavior and attach it to the document.
	 *
	 * @param   array  $params  Associative array of values.
	 *
	 * @return  void
	 *
	 * @since 11.1
	 *
	 * @deprecated    12.1
	 */
	protected function _loadBehavior($params = array())
	{
		// Deprecation warning.
		Log::add('JPaneSliders::_loadBehavior is deprecated.', Log::WARNING, 'deprecated');

		// Include mootools framework.
		HTMLHelper::_('behavior.framework', true);

		$document = Factory::getDocument();

		$options = '{';
		$opt['onActive'] = 'function(toggler, i) { toggler.addClass(\'pane-toggler-down\');' .
			' toggler.removeClass(\'pane-toggler\');i.addClass(\'pane-down\');i.removeClass(\'pane-hide\'); }';
		$opt['onBackground'] = 'function(toggler, i) { toggler.addClass(\'pane-toggler\');' .
			' toggler.removeClass(\'pane-toggler-down\');i.addClass(\'pane-hide\');i.removeClass(\'pane-down\'); }';
		$opt['duration'] = (isset($params['duration'])) ? (int) $params['duration'] : 300;
		$opt['display'] = (isset($params['startOffset']) && ($params['startTransition'])) ? (int) $params['startOffset'] : null;
		$opt['show'] = (isset($params['startOffset']) && (!$params['startTransition'])) ? (int) $params['startOffset'] : null;
		$opt['opacity'] = (isset($params['opacityTransition']) && ($params['opacityTransition'])) ? 'true' : 'false';
		$opt['alwaysHide'] = (isset($params['allowAllClose']) && (!$params['allowAllClose'])) ? 'false' : 'true';
		foreach ($opt as $k => $v)
		{
			if ($v)
			{
				$options .= $k . ': ' . $v . ',';
			}
		}
		if (substr($options, -1) == ',')
		{
			$options = substr($options, 0, -1);
		}
		$options .= '}';

		$js = '	window.addEvent(\'domready\', function(){ new Fx.Accordion($$(\'.panel h3.pane-toggler\'), $$(\'.panel div.pane-slider\'), '
			. $options . '); });';

		$document->addScriptDeclaration($js);
	}
}
