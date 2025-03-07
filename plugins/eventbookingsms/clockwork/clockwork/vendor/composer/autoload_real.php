<?php

use Composer\Autoload\ClassLoader;
use Composer\Autoload\ComposerStaticInit64bcb066febeeee4aad48bd38e639b10;
// autoload_real.php @generated by Composer
class ComposerAutoloaderInit64bcb066febeeee4aad48bd38e639b10
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit64bcb066febeeee4aad48bd38e639b10', 'loadClassLoader'), true, true);
        self::$loader = $loader = new ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit64bcb066febeeee4aad48bd38e639b10', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(ComposerStaticInit64bcb066febeeee4aad48bd38e639b10::getInitializer($loader));
        } else {
            $map = require __DIR__ . '/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require __DIR__ . '/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        return $loader;
    }
}
