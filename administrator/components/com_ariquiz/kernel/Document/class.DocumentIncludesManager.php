<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die('Direct Access to this location is not allowed.');

AriKernel::import('Document.DocumentHelper');
AriKernel::import('Utils.Utils');

class AriDocumentIncludesManager extends JObject
{
	var $_initState = null;
	
	function __construct($saveInitState = true)
	{
		if ($saveInitState) 
			$this->saveInitState();
	}
	
	function saveInitState()
	{
		$this->_initState = $this->getCurrentState();
	}

	function getInitState()
	{
		return $this->_initState;
	}
	
	function getCurrentState()
	{
		$currentState = array();
		$document=& JFactory::getDocument();
		if ($document->getType() != 'html') 
			return $currentState; 

		$currentState = $document->getHeadData();

		return $currentState;
	}
	
	function deleteState()
	{
		$this->_initState = null;
	}
	
	function getDifferences($deleteState = true)
	{
		$differences = array();
		$initState = $this->getInitState();

		$currentState = $this->getCurrentState();
		if ($currentState)
		{
			if (!empty($currentState['styleSheets']))
			{
				foreach ($currentState['styleSheets'] as $style => $styleInfo)
				{
					if (!array_key_exists($style, $initState['styleSheets']))
						$differences[] = sprintf('<link rel="stylesheet" href="%s" type="%s" />', $style, $this->getMimeType($styleInfo, 'text/css'));
				}
			}

			if (!empty($currentState['style']))
			{
				foreach ($currentState['style'] as $type => $style) 
				{
					if (!empty($initState['style'][$type]))
					{
						$difStyle = '';
						if (is_array($style)) {
							$initStyles = $initState['style'][$type];
							foreach ($style as $styleKey => $styleCode) {
								if (!array_key_exists($styleKey, $initStyles)) {
									$difStyle .= $styleCode;
								}
							}
						} else {
							if (strpos($style, $initState['style'][$type]) === 0)
								$difStyle = trim(substr($style, strlen($initState['style'][$type])));
						}

						if (!empty($difStyle))
							$differences[] = sprintf('<style type="%s">%s</style>', $type, $difStyle);
					}
					else
					{
						$differences[] = sprintf('<style type="%s">%s</style>', $type, is_array($style) ? join('', array_values($style)) : $style);
					}
				}
			}
			
			if (!empty($currentState['scripts']))
			{
				foreach ($currentState['scripts'] as $script => $type)
				{
					if (!array_key_exists($script, $initState['scripts']))
					{
                        if (is_array($type))
                            $type = $this->getMimeType($type, 'text/javascript');

						$differences[] = sprintf('<script type="%s" src="%s"></script>', $type, $script);
					}
				}
			}
			
			if (!empty($currentState['script']))
			{
				foreach ($currentState['script'] as $type => $script) 
				{
					if (!empty($initState['script'][$type]))
					{
						$difScript = '';
						
						if (J4) {
							$initScript = $initState['script'][$type];
							$difScripts = array();

							foreach ($script as $key => $scriptCode) {
								if (!array_key_exists($key, $initScript)) {
									$difScripts[] = $scriptCode;
								}
							}

							if (count($difScripts) > 0) {
								$differences[] = sprintf('<script type="%s">%s</script>', $type, join(';', $difScripts));								
							}
						} else {
							if (strpos($script, $initState['script'][$type]) === 0)
								$difScript = trim(substr($script, strlen($initState['script'][$type])));
								
							if (!empty($difScript))
								$differences[] = sprintf('<script type="%s">%s</script>', $type, $difScript);
						}
					}
					else
					{
						$differences[] = sprintf('<script type="%s">%s</script>', $type, J4 ? join(';', $script) : $script);
					}
				}
			}
			
			if (!empty($currentState['custom']))
			{
				foreach ($currentState['custom'] as $customTag)
				{
					if (!in_array($customTag, $initState['custom']))
						$differences[] = $customTag;
				}
			}			
		}

		if ($deleteState) $this->deleteState();

		return $differences;
	}

    private function getMimeType($itemMeta, $defaultType = '')
    {
        if (!empty($itemMeta['mime']))
            return $itemMeta['mime'];

        if (!empty($itemMeta['type']))
            return $itemMeta['type'];

        return $defaultType;
    }
}