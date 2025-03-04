<?php 
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

jimport('joomla.language.language');

class AriLanguage extends JLanguage
{
	function __construct($lang = null, $debug = false)
	{
		if (is_null($lang))
		{
			$lang =& JFactory::getLanguage(); 
			$lang = $lang->get('tag');
		}

		parent::__construct($lang, $debug);

		$this->strings = array();
	}
	
	function getMessages()
	{
		return $this->strings;
	}

    public static function getPreferableLanguage()
    {
        $lang = JFactory::getApplication()->getCfg('language');

        $user = JFactory::getUser();
        if ($user->get('id') > 0)
        {
            $userLang = $user->getParam('language');
            if ($userLang)
            {
                $lang = $userLang;
            }
        }

        return $lang;
    }
}