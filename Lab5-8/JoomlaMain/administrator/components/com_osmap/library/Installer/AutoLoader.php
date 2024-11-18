<?php
/**
 * @package   ShackInstaller
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackInstaller.
 *
 * ShackInstaller is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackInstaller is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackInstaller.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Installer;

defined('_JEXEC') or die();

class AutoLoader
{
    /**
     * Associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected static $prefixes = [];

    /**
     * Associative array of prefixes for loading specialized camelCase classes
     * where Uppercase letters in the class name indicate directory structure
     *
     * @var array
     */
    protected static $camelPrefixes = [];

    /**
     * @var AutoLoader
     */
    protected static $instance = null;

    /**
     * @param string $method
     */
    protected static function registerLoader($method)
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        spl_autoload_register([static::$instance, $method]);
    }

    /**
     * Register a psr4 namespace
     *
     * @param string $prefix  The namespace prefix.
     * @param string $baseDir A base directory for class files in the
     *                         namespace.
     * @param ?bool   $prepend If true, prepend the base directory to the stack
     *                         instead of appending it; this causes it to be searched first rather
     *                         than last.
     *
     * @return void
     */
    public static function register(string $prefix, string $baseDir, ?bool $prepend = false)
    {
        if (count(self::$prefixes) == 0) {
            static::registerLoader('loadClass');
        }

        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $baseDir = rtrim($baseDir, '\\/') . '/';

        // initialise the namespace prefix array
        if (empty(self::$prefixes[$prefix])) {
            self::$prefixes[$prefix] = [];
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift(self::$prefixes[$prefix], $baseDir);
        } else {
            array_push(self::$prefixes[$prefix], $baseDir);
        }
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     *
     * @return ?string The mapped file name on success
     */
    protected function loadClass(string $class): ?string
    {
        $prefixes  = explode('\\', $class);
        $className = '';
        while ($prefixes) {
            $className = array_pop($prefixes) . $className;
            $prefix    = join('\\', $prefixes) . '\\';

            if ($filePath = $this->loadMappedFile($prefix, $className)) {
                return $filePath;
            }

            $className = '\\' . $className;
        }

        return null;
    }

    /**
     * Load the mapped file for a namespace prefix and class.
     *
     * @param string $prefix    The namespace prefix.
     * @param string $className The relative class name.
     *
     * @return ?string path that was loaded
     */
    protected function loadMappedFile(string $prefix, string $className): ?string
    {
        // are there any base directories for this namespace prefix?
        if (isset(self::$prefixes[$prefix]) === false) {
            return null;
        }

        // look through base directories for this namespace prefix
        foreach (self::$prefixes[$prefix] as $baseDir) {
            $path = $baseDir . str_replace('\\', '/', $className) . '.php';

            if (is_file($path)) {
                require_once $path;

                return $path;
            }
        }

        return null;
    }

    /**
     * Register a base directory for classes organized using camelCase.
     * Class names beginning with the prefix will be automatically loaded
     * if there is a matching file in the directory tree starting with $baseDir.
     * File names and directory names are all expected to be lower case.
     *
     * Example:
     *
     * $prefix = 'Simplerenew'
     * $baseDir = '/library/joomla'
     *
     * A class name of: SimplerenewViewAdmin
     * Would be in    : /library/joomla/view/admin.php
     *
     * This system is intended for situations where full name spacing is either
     * unavailable or impractical due to integration with other systems.
     *
     * @param string $prefix
     * @param string $baseDir
     *
     * @return void
     * @throws \Exception
     */
    public static function registerCamelBase(string $prefix, string $baseDir)
    {
        if (!is_dir($baseDir)) {
            throw new \Exception("Cannot register '{$prefix}'. The requested base directory does not exist!'");
        }

        if (count(self::$camelPrefixes) == 0) {
            // Register function on first call
            static::registerLoader('loadCamelClass');
        }

        if (empty(self::$camelPrefixes[$prefix])) {
            self::$camelPrefixes[$prefix] = $baseDir;
        }
    }

    /**
     * Autoload a class using the camelCase structure
     *
     * @param string $class
     *
     * @return string
     */
    protected function loadCamelClass(string $class): ?string
    {
        if (!class_exists($class)) {
            foreach (self::$camelPrefixes as $prefix => $baseDir) {
                if (strpos($class, $prefix) === 0) {
                    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($class, strlen($prefix)));

                    $file     = strtolower(join('/', $parts));
                    $filePath = $baseDir . '/' . $file . '.php';

                    if (is_file($filePath)) {
                        require_once $filePath;

                        return $filePath;
                    }
                }
            }
        }

        return null;
    }
}
