<?php
defined('_JEXEC') or die;

class ThemeHelper extends stdClass
{
    private static $instance = null;

    private function __construct() { /* ... @return Singleton */ }
    private function __clone() { /* ... @return Singleton */ }

    public static function getInstance() {
        return self::$instance===null ? self::$instance = new static() : self::$instance;
    }
}
