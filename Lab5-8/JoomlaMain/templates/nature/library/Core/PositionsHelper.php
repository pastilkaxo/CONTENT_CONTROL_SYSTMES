<?php
defined('_JEXEC') or die;

class PositionsHelper
{
    private static $instances = [];
    private $_positions = [];

    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function addPositionInfo($moduleId, $attribs)
    {
        $this->_positions[$moduleId] = $attribs;
    }

    public function getPositionInfo($moduleId) {
        if (array_key_exists($moduleId, $this->_positions)) {
            return $this->_positions[$moduleId];
        }
        return null;
    }
}