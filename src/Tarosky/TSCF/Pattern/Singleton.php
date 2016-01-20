<?php

namespace Tarosky\TSCF\Pattern;

/**
 * Singleton pattern
 *
 * @package Tarosky\TSCF\Pattern
 */
class Singleton {

	private static $instances = [];

	/**
	 * Singleton constructor.
	 */
	final protected function __construct() {
		$this->on_construct();
	}

	/**
	 * Executed in constructor.
	 */
	protected function on_construct() {
	}

	/**
	 * Get instance
	 *
	 * @return static
	 */
	public static function instance() {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name();
		}

		return self::$instances[ $class_name ];
	}
}
