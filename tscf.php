<?php
/**
 Plugin Name: TSCF - Tarosky Custom Filed manager
 Plugin URI: https://github.com/tarosky/tscf
 Description: TaroSky's Custom Field manager. Scalable, Well-Structured and Maintainable. Requires PHP5.4 or later.
 Author: TAROSKY INC. <info@tarosky.co.jp>
 Version: 1.1.1
 Author URI: https://tarosky.co.jp
 Text Domain: tscf
 Domain Path: /languages/
 License: GPL v3 or later.
 */

defined( 'ABSPATH' ) || die();

// Register bootstrap.
add_action( 'plugins_loaded', 'tscf_plugins_loaded' );

/**
 * Plugin bootstrap
 *
 * @internal
 */
function tscf_plugins_loaded() {
	// Add translation.
	load_plugin_textdomain( 'tscf', false, 'tscf/languages' );
	// Start.
	if ( version_compare( phpversion(), '5.4.*', '<' ) ) {
		add_action( 'admin_notices', 'tscf_admin_notice' );
	} else {
		// Requirements O.K.
		$path = __DIR__ . '/vendor/autoload.php';
		if ( ! file_exists( $path ) ) {
			trigger_error( __( 'Mmm...TSCF plugin\'s auto loader missing. Did you run composer install?', 'tscf' ), E_USER_WARNING );
		} else {
			require $path;
			call_user_func( array( 'Tarosky\\TSCF\\Bootstrap', 'instance' ) );
		}
	}
}

/**
 * Show error on admin screen
 *
 * @internal
 * @return void
 */
function tscf_admin_notice() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( __( '[Error] TSCF requires PHP version 5.4 and over. Please consider upgrading your PHP.', 'tscf' ) ) );
}
