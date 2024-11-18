<?php
/**
 * @package NP Library
 */

defined('_JEXEC') or die;

if (version_compare(JVERSION, '3.8', 'lt')) {
    if (!function_exists('np_autoloader')) {
        /**
         * Class autoloader
         * 
         * @param string $class Class path
         *
         * @return bool
         */
        function np_autoloader($class)
        {
            $libaryPath = __DIR__ . '/src';
            $namespace = 'NP';
            $classPath = $class . '.php';
            if (strpos($class, "{$namespace}\\") === 0) {
                $classFilePath = realpath($libaryPath . DIRECTORY_SEPARATOR . substr_replace($classPath, '', 0, strlen($namespace) + 1));
                $found = (bool)include_once $classFilePath;
                return $found;
            }
            return false;
        }
        spl_autoload_register('np_autoloader');
    }
} else {
    JLoader::registerNamespace('NP', __DIR__ . '/src', false, false, 'psr4');
}