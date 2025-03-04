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

AriKernel::import('Xml.XmlHelper');

class JElementRules extends JElement
{
	var	$_name = 'Rules';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$assetField = AriXmlHelper::getAttribute($node, 'asset_field', 'asset_id');
		$assetId = $this->_parent->get($assetField);
		
		$comp = AriXmlHelper::getAttribute($node, 'component');

		$attrs = array();
		foreach ($node->attributes() as $key => $value)
		{
			$attrs[] = $key . '="' . $value . '"';
		}
		$attrs = join(' ', $attrs);

		$rulesField = 
		'<field 
			' . $attrs . '
		/>
		<field
			name="' . $assetField . '"
			type="hidden"
			label=""
			description=""
		/>';

		jimport('joomla.form.form');
		
		$rulesForm = JForm::getInstance($comp . '.rules_' . uniqid('', false), '<form><fieldset>' . $rulesField . '</fieldset></form>', array('control' => $control_name));
		$rulesForm->bind(array($assetField => $assetId));

		return $rulesForm->getInput($name);
	}
}