<?php

/*------------------------------------------------------------------------
# property.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
/**
 * Supports a modal property picker.
 *
 * @since  2.8.5
 */
class JFormFieldModal_Property extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   2.8.5
	 */
	protected $type = 'Modal_Property';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   2.8.5
	 */
	protected function getInput()
	{
		///$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		HTMLHelper::_('jquery.framework');
		// Add the modal field script to the document head.
		//$wa->useScript('field.modal-fields');
		require_once JPATH_ROOT . '/components/com_osproperty/helpers/helper.php';
		if(OSPHelper::isJoomla4())
		{
			$allowEdit		= false;
			$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

			// Load language
			Factory::getLanguage()->load('com_osproperty', JPATH_ADMINISTRATOR);

			// Build the script.
			$script = array();

			// Select button script
			$script[] = '	function jSelectUser_pro_id(id, title, catid, object) {';
			$script[] = '		document.getElementById("' . $this->id . '_id").value = id;';
			$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';

			if ($allowEdit)
			{
				$script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
			}

			if ($allowClear)
			{
				$script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
			}

			$script[] = '		jQuery("#modalProperty' . $this->id . '").modal("hide");';

			if ($this->required)
			{
				$script[] = '		document.formvalidator.validate(document.getElementById("' . $this->id . '_id"));';
				$script[] = '		document.formvalidator.validate(document.getElementById("' . $this->id . '_name"));';
			}

			$script[] = '	}';

			// Clear button script
			static $scriptClear;

			if ($allowClear && !$scriptClear)
			{
				$scriptClear = true;

				$script[] = '	function jClearProperty(id) {';
				$script[] = '		document.getElementById(id + "_id").value = "";';
				$script[] = '		document.getElementById(id + "_name").value = "' .
					htmlspecialchars(Text::_('OS_SELECT_AN_PROPERTY', true), ENT_COMPAT, 'UTF-8') . '";';
				$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
				$script[] = '		if (document.getElementById(id + "_edit")) {';
				$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
				$script[] = '		}';
				$script[] = '		return false;';
				$script[] = '	}';
			}

			// Add the script to the document head.
			Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Setup variables for display.
			$html	= array();
			$link	= 'index.php?option=com_osproperty&amp;task=properties_list&amp;layout=modal&amp;tmpl=component&amp;function=jSelectUser_pro_id';

			if (isset($this->element['language']))
			{
				$link .= '&amp;forcedLanguage=' . $this->element['language'];
			}

			if ((int) $this->value > 0)
			{
				$db	= Factory::getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('pro_name'))
					->from($db->quoteName('#__osrs_properties'))
					->where($db->quoteName('id') . ' = ' . (int) $this->value);
				$db->setQuery($query);

				try
				{
					$title = $db->loadResult();
				}
				catch (RuntimeException $e)
				{
					//JError::raiseWarning(500, $e->getMessage());
					throw new Exception($e->getMessage(), 500);
				}
			}

			if (empty($title))
			{
				$title = Text::_('OS_SELECT_AN_PROPERTY');
			}
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

			// The active property id field.
			if (0 == (int) $this->value)
			{
				$value = '';
			}
			else
			{
				$value = (int) $this->value;
			}

			$url = $link . '&amp;' . Session::getFormToken() . '=1';
			// The current property display field.
			$html[] = '<span class="input-append btn-group">';
			$html[] = '<input type="text" class="input-medium form-control" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
			$html[] = '<button data-bs-target="#modalProperty' . $this->id . '" class="btn btn-primary" data-bs-toggle="modal" type="button" title="'
				. HTMLHelper::tooltipText('OS_SELECT_AN_PROPERTY') . '">'
				. '<span class="icon-file" aria-hidden="true"></span> '
				. Text::_('JSELECT') . '</button>';

			// Clear property button
			if ($allowClear)
			{
				$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearProperty(\'' .
					$this->id . '\')"><span class="icon-remove"></span>' . Text::_('JCLEAR') . '</button>';
			}

			$html[] = '</span>';

			// The class='required' for client side validation
			$class = '';

			if ($this->required)
			{
				$class = ' class="required modal-value"';
			}

			$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

			$html[] = HTMLHelper::_(
				'bootstrap.renderModal',
				'modalProperty' . $this->id,
				array(
					'url' => $url,
					'title' => Text::_('OS_SELECT_AN_PROPERTY'),
					'width' => '800px',
					'height' => '300px',
					'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
						. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
				)
			);
		}
		else
		{
			$allowEdit		= false;
			$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

			// Load language
			Factory::getLanguage()->load('com_osproperty', JPATH_ADMINISTRATOR);

			// Build the script.
			$script = array();

			// Select button script
			$script[] = '	function jSelectUser_pro_id(id, title, catid, object) {';
			$script[] = '		document.getElementById("' . $this->id . '_id").value = id;';
			$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';

			if ($allowEdit)
			{
				$script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
			}

			if ($allowClear)
			{
				$script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
			}

			$script[] = '		jQuery("#modalProperty' . $this->id . '").modal("hide");';

			if ($this->required)
			{
				$script[] = '		document.formvalidator.validate(document.getElementById("' . $this->id . '_id"));';
				$script[] = '		document.formvalidator.validate(document.getElementById("' . $this->id . '_name"));';
			}

			$script[] = '	}';

			// Clear button script
			static $scriptClear;

			if ($allowClear && !$scriptClear)
			{
				$scriptClear = true;

				$script[] = '	function jClearProperty(id) {';
				$script[] = '		document.getElementById(id + "_id").value = "";';
				$script[] = '		document.getElementById(id + "_name").value = "' .
					htmlspecialchars(Text::_('OS_SELECT_AN_PROPERTY', true), ENT_COMPAT, 'UTF-8') . '";';
				$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
				$script[] = '		if (document.getElementById(id + "_edit")) {';
				$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
				$script[] = '		}';
				$script[] = '		return false;';
				$script[] = '	}';
			}

			// Add the script to the document head.
			Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

			// Setup variables for display.
			$html	= array();
			$link	= 'index.php?option=com_osproperty&amp;task=properties_list&amp;layout=modal&amp;tmpl=component&amp;function=jSelectUser_pro_id';

			if (isset($this->element['language']))
			{
				$link .= '&amp;forcedLanguage=' . $this->element['language'];
			}

			if ((int) $this->value > 0)
			{
				$db	= Factory::getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('pro_name'))
					->from($db->quoteName('#__osrs_properties'))
					->where($db->quoteName('id') . ' = ' . (int) $this->value);
				$db->setQuery($query);

				try
				{
					$title = $db->loadResult();
				}
				catch (RuntimeException $e)
				{
					//JError::raiseWarning(500, $e->getMessage());
					throw new Exception($e->getMessage(), 500);
				}
			}

			if (empty($title))
			{
				$title = Text::_('OS_SELECT_AN_PROPERTY');
			}
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

			// The active property id field.
			if (0 == (int) $this->value)
			{
				$value = '';
			}
			else
			{
				$value = (int) $this->value;
			}

			$url = $link . '&amp;' . Session::getFormToken() . '=1';
			// The current property display field.
			$html[] = '<span class="input-append">';
			$html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
			$html[] = '<a href="#modalProperty' . $this->id . '" class="btn hasTooltip" role="button"  data-toggle="modal" title="'
				. HTMLHelper::tooltipText('OS_SELECT_AN_PROPERTY') . '">'
				. '<span class="icon-file"></span> '
				. Text::_('JSELECT') . '</a>';

			// Clear property button
			if ($allowClear)
			{
				$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearProperty(\'' .
					$this->id . '\')"><span class="icon-remove"></span>' . Text::_('JCLEAR') . '</button>';
			}

			$html[] = '</span>';

			// The class='required' for client side validation
			$class = '';

			if ($this->required)
			{
				$class = ' class="required modal-value"';
			}

			$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

			$html[] = HTMLHelper::_(
				'bootstrap.renderModal',
				'modalProperty' . $this->id,
				array(
					'url' => $url,
					'title' => Text::_('OS_SELECT_AN_PROPERTY'),
					'width' => '800px',
					'height' => '300px',
					'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
						. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
				)
			);
		}
		return implode("\n", $html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   3.4
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_id', parent::getLabel());
	}
}
