<?php

/*------------------------------------------------------------------------
# city.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('JPATH_BASE') or die;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldModal_City extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_City';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		//$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Add the modal field script to the document head.
		//$wa->useScript('field.modal-fields');
		// Load the modal behavior script.
		//HTMLHelper::_('behavior.modal', 'a.modal');
		require_once JPATH_ROOT . '/components/com_osproperty/helpers/helper.php';
		if(OSPHelper::isJoomla4())
		{
			$allowEdit		= false;
			$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

			// Load language
			Factory::getLanguage()->load('com_osproperty', JPATH_ADMINISTRATOR);

			// Build the script.
			$script = array();

			// Build the script.
			$script = array();
			$script[] = '	function jSelectCity_'.$this->id.'(id, title, object) {';
			$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
			$script[] = '		document.getElementById("'.$this->id.'_name").value = title;';
			//$script[] = '		SqueezeBox.close();';

			if ($allowEdit)
			{
				$script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
			}

			if ($allowClear)
			{
				$script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
			}

			$script[] = '		jQuery("#modalCity' . $this->id . '").modal("hide");';

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

				$script[] = '	function jClearCity(id) {';
				$script[] = '		document.getElementById(id + "_id").value = "";';
				$script[] = '		document.getElementById(id + "_name").value = "' .
					htmlspecialchars(Text::_('Select City', true), ENT_COMPAT, 'UTF-8') . '";';
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
			$link	= 'index.php?option=com_osproperty&amp;task=city_list&amp;modal=1&amp;tmpl=component&amp;function=jSelectCity_'.$this->id;

			$db	= Factory::getDBO();
			$db->setQuery(
				'SELECT city' .
				' FROM #__osrs_cities' .
				' WHERE id = '.(int) $this->value
			);
			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				//JError::raiseWarning(500, $e->getMessage());
				throw new Exception($e->getMessage(), 500);
			}

			if (empty($title)) {
				$title = Text::_('Select city');
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
			$html[] = '<button data-bs-target="#modalCity' . $this->id . '"  class="btn btn-primary" data-bs-toggle="modal" type="button" title="'
				. HTMLHelper::tooltipText('Select City') . '">'
				. '<span class="icon-file"></span> '
				. Text::_('JSELECT') . '</button>';

			// Clear property button
			if ($allowClear)
			{
				$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearCity(\'' .
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
				'modalCity' . $this->id,
				array(
					'url' => $url,
					'title' => Text::_('Select City'),
					'width' => '800px',
					'height' => '300px',
					'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">'
						. Text::_("JLIB_HTML_BEHAVIOR_CLOSE") . '</button>'
				)
			);
		}
		else
		{
			// Load the modal behavior script.
			HTMLHelper::_('behavior.modal', 'a.modal');

			// Build the script.
			$script = array();
			$script[] = '	function jSelectCity_'.$this->id.'(id, title, object) {';
			$script[] = '		document.id("'.$this->id.'_id").value = id;';
			$script[] = '		document.id("'.$this->id.'_name").value = title;';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';

			// Add the script to the document head.
			Factory::getDocument()->addScriptDeclaration(implode("\n", $script));


			// Setup variables for display.
			$html	= array();
			$link	= 'index.php?option=com_osproperty&amp;task=city_list&amp;modal=1&amp;tmpl=component&amp;function=jSelectCity_'.$this->id;

			$db	= Factory::getDBO();
			$db->setQuery(
				'SELECT city' .
				' FROM #__osrs_cities' .
				' WHERE id = '.(int) $this->value
			);
			$title = $db->loadResult();

			if ($error = $db->getErrorMsg()) {
				//JError::raiseWarning(500, $error);
				throw new Exception($error, 500);
			}

			if (empty($title)) {
				$title = Text::_('Select city');
			}
			$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

			// The current user display field.
			
			if (version_compare(JVERSION, '3.0', 'lt')) {
				
				$html[] = '<div class="fltlft">';
				$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
				$html[] = '</div>';
		
				// The user select button.
				$html[] = '<div class="button2-left">';
				$html[] = '  <div class="blank">';
				$html[] = '	<a class="modal" title="'.Text::_('Select city').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.Text::_('Select city').'</a>';
				$html[] = '  </div>';
				$html[] = '</div>';
				
			}else{
			
				$html[] = '<span class="input-append">';
				$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
				$html[] = '	<a class="modal btn hasTooltip" title="'.Text::_('Select city').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.Text::_('Select city').'</a>';
				$html[] = '</span>';
				
			}

			// The active article id field.
			if (0 == (int)$this->value) {
				$value = '';
			} else {
				$value = (int)$this->value;
			}

			// class='required' for client side validation
			$class = '';
			if ($this->required) {
				$class = ' class="required modal-value"';
			}

			$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';
		}
		return implode("\n", $html);
	}
}


?>
