<?php

namespace Tarosky\TSCF\Utility;


/**
 * Application helper.
 *
 * @package Tarosky\TSCF\Utility
 * @property-read Input $input
 * @property-read Parser $parser
 * @property-read string $root_dir
 * @property-read string $url
 */
trait Application {

	/**
	 * Shorthand for __ and sprintf-ready.
	 *
	 * @deprecated 1.0.0
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
	 * @deprecated 1.0.0
	 * @param string $string
	 */
	public function _e( $string ) {
		echo call_user_func_array( [ $this, '_s' ], func_get_args() );
	}

	/**
	 * WordPress allows file edit?
	 *
	 * @return bool
	 */
	protected function file_editable() {
		
		return ( ! defined( 'DISALLOW_FILE_EDIT' ) || ! DISALLOW_FILE_EDIT );
	}

	/**
	 * Detect if current page's suffix is editor's.
	 *
	 * @param string $hook_suffix
	 *
	 * @return bool
	 */
	protected function is_editor( $hook_suffix ) {
		return 'settings_page_tscf' === $hook_suffix;
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return null|static|string
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'input':
				return Input::instance();
				break;
			case 'parser':
				return Parser::instance();
				break;
			case 'root_dir':
				return dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );
				break;
			case 'url':
				if ( false !== strpos( $this->root_dir, 'wp-content/plugins' ) ) {
					return plugin_dir_url( $this->root_dir . '/assets' ) . 'assets';
				} else {
					return str_replace( ABSPATH, home_url( '/' ), $this->root_dir ) . '/assets';
				}
				break;
			default:
				return null;
				break;
		}
	}
}
