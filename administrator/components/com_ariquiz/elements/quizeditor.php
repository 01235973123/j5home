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

require_once dirname(__FILE__) . '/editor.php';

class JElementQuizeditor extends JElementEditor
{
	var	$_name = 'Quizeditor';

	function getEditor()
	{
        $editorType = null;
        if (!is_null($this->_node))
            $editorType = AriXmlHelper::getAttribute($this->_node, 'editor');

        if (is_null($editorType))
        {
            $config = AriQuizHelper::getConfig();
            $editorType = $config->get('Editor');
        }

		if (!J4) {
			if ($editorType)
			{
				$editorPlgPath = JPATH_PLUGINS . '/editors/' . $editorType . '/' . $editorType . '.php';
				if (!is_file($editorPlgPath))
					$editorType = null;
				else
				{
					require_once $editorPlgPath;
					
					$plugin = JPluginHelper::getPlugin('editors', $editorType);
					if (empty($plugin))
						$editorType = null;
				}
			}
			else
				$editorType = null;
		}
		
		if (J4) {
			return Joomla\CMS\Editor\Editor::getInstance($editorType ? $editorType : 'none');
		}

		return JFactory::getEditor($editorType);
	}
}