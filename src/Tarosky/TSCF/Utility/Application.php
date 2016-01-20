<?php

namespace Tarosky\TSCF\Utility;
use Tarosky\TSCF\Data\Parser;


/**
 * Application helper.
 *
 * @package Tarosky\TSCF\Utility
 * @property-read Input $input
 * @property-read Parser $parser
 */
trait Application {

	/**
	 * Shorthand for __ and sprintf-ready.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function _s( $string ) {
		$args = func_get_args();
		if ( 1 < count( $args ) ) {
			$args[0] = __( $args[0], 'tscf' );

			return call_user_func_array( 'sprintf', $args );
		} else {
			return __( $string, 'tscf' );
		}
	}

	/**
	 * Short hand for _e
	 *
	 * @param string $string
	 */
	public function _e( $string ) {
		echo call_user_func_array( [ $this, '_s' ], func_get_args() );
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return null|static
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'input':
				return Input::instance();
				break;
			case 'parser':
				return Parser::instance();
				break;
			default:
				return null;
				break;
		}
	}
}
