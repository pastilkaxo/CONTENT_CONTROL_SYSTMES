<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/**
 *  Amaz Store Singleton Pattern.
 *
 * @package Hunk Companion
 */

/**
 * Class amaz_store_Singleton
 */
class amaz_store_Singleton{
	/**
	 * Call this method to get singleton
	 */
	public static function instance() {
		static $instance = false;
		if ( $instance === false ) {
			// Late static binding (PHP 5.3+)
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Make constructor private, so nobody can call "new Class".
	 */
	private function __construct() {}

	/**
	 * Make clone magic method private, so nobody can clone instance.
	 */
	private function __clone() {}

	/**
	 * Make sleep magic method private, so nobody can serialize instance.
	 */
	public function __sleep() {}

	/**
	 * Make wakeup magic method private, so nobody can unserialize instance.
	 */
	public function __wakeup() {}

}
