<?php
class AriApplication {
    static public function getApplication() {
        return Joomla\CMS\Factory::getApplication();
    }

    static public function getInput() {
        $app = self::getApplication();

        return J4 ? $app->getInput() : $app->input;
    }

    static public function isAdmin() {
		return self::getApplication()->isClient('administrator');
	}
}