<?php

/**
 * @package     OSF
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2009 - 2024 Ossolution Team
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
/**
 * Joomla CMS View List class, used to render list of records from front-end or back-end of your component
 * 
 * @package     OSF
 * @subpackage  View
 * @since 		1.0
 */
class OSFViewList extends OSFViewHtml
{

	/**
	 * The model state
	 *
	 * @var OSFModelState
	 */
	protected $state;
	
	/**
	 * List of records which will be displayed
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var JPagination
	 */
	protected $pagination;
	
	/**
	 * The array which keeps list of "list" options which will used to display as the filter on the list
	 * 
	 * @var Array
	 */
	protected $lists = array();

	/**
	 * Method to instantiate the view.
	 *
	 * @param   array  $config The configuration data for the view
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to display a view
	 * 
	 * @see OSFViewHtml::display()
	 */
	public function display()
	{
		$this->prepareView();		
		parent::display();
	}

	/**
	 * Prepare the view before it is displayed
	 * 
	 */
	protected function prepareView()
	{
		$this->state = $this->model->getState();
		$this->items = $this->model->getData();
		$this->pagination = $this->model->getPagination();
		if ($this->isAdminView)
		{
            $this->lists['filter_state'] = HTMLHelper::_('grid.state', $this->state->filter_state);
            $this->lists['filter_access'] = HTMLHelper::_('access.level', 'filter_access', $this->state->filter_access, 'onchange="submit();"', false);
            $this->lists['filter_language'] = HTMLHelper::_('select.genericlist', HTMLHelper::_('contentlanguage.existing', true, true), 'filter_language',
                ' onchange="submit();" ', 'value', 'text', $this->state->filter_language);

			$helperClass = $this->classPrefix . 'Helper';
			if (is_callable($helperClass . '::addSubmenus'))
			{
				call_user_func(array($helperClass, 'addSubmenus'), $this->name);
			}
			else
			{
				call_user_func(array('OSFHelper', 'addSubmenus'), $this->option, $this->name);
			}
			DonationHelperHtml::renderSubmenu($this->name);
			$this->addToolbar();			
		}
	}
	
	/**
	 * Method to add toolbar buttons
	 * 
	 */
	protected function addToolbar()
	{
		$helperClass = $this->classPrefix . 'Helper';
		if (is_callable($helperClass . '::getActions'))
		{
			$canDo = call_user_func(array($helperClass, 'getActions'), $this->name, $this->state);
		}
		else
		{
			$canDo = call_user_func(array('OSFHelper', 'getActions'), $this->option, $this->name, $this->state);
		}
		ToolbarHelper::title(Text::_(strtoupper($this->languagePrefix . '_MANAGE_' . $this->name)), 'link ' . $this->name);
		if ($canDo->get('core.create'))
		{
			ToolbarHelper::addNew('add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			ToolbarHelper::editList('edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete') && isset($this->items[0]))
		{
			ToolbarHelper::deleteList(Text::_($this->languagePrefix . '_DELETE_CONFIRM'), 'delete');
		}
		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->published) || isset($this->items[0]->state))
			{
				ToolbarHelper::publish('publish', 'JTOOLBAR_PUBLISH', true);
				ToolbarHelper::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH', true);								
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::preferences($this->option);
		}
	}
}
