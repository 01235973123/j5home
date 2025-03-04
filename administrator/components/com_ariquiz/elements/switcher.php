<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die ('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_ariquiz/kernel/class.AriKernel.php';

//require_once dirname(__FILE__) . '/../libraries/legacy/joomla/html/parameter/element/list.php';

AriKernel::import('Xml.XmlHelper');
AriKernel::import('Joomla.Html.Parameter');

(new AriParameter())->loadElement('list');

class JElementSwitcher extends JElementList
{
	var	$_name = 'Switcher';

	function fetchElement($name, $value, &$node, $control_name)
	{
        if (!J4) {
            return parent::fetchElement($name, $value, $node, $control_name);
        }

        $ctrlName = $control_name . '[' . $name .']';
        $id = $control_name . $name;
        $options = $this->_getOptions($node);
        $displayData = array(
            'options' => $options,
            'name' => $ctrlName,
            'id' => $id,
            'value' => $value,
        );

        $layout = new Joomla\CMS\Layout\FileLayout('joomla.form.field.radio.switcher');
        return $layout->render($displayData);
	}
}