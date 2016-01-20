<?php
/*
Plugin Name: TSCF - Tarosky Custom Filed manager
Plugin URI: https://github.com/tarosky/tscf
Description: TaroSky's Custom Field manager. Scalable, Well-Structured and Maintainable. Requires PHP5.4 or later.
Author: Takahashi Fumiki<ftakahashi@tarosky.co.jp>
Version: 1.0
Author URI: https://tarosky.co.jp
Text Domain: tscf
Domain Path: /languages/
License: GPL v3 or later.
*/

defined( 'ABSPATH' ) or die();

if ( version_compare( phpversion(), '5.4.*', '<' ) ) {
	add_action( 'admin_notices', '_tscf_admin_notice' );
} else {
	// Requirements O.K.
	// Add translation.
	load_plugin_textdomain( 'tscf', false, 'tscf/languages' );
	require __DIR__ . '/vendor/autoload.php';
	call_user_func( [ 'Tarosky\\TSCF\\Bootstrap', 'instance' ] );
}

/**
 * Show error on admin screen
 *
 * @internal
 * @return void
 */
function _tscf_admin_notice() {
	printf( '<div class="error"><p>%s</p></div>', esc_html( __( '[Error] TSCF requires PHP version 5.4 or later. Please', 'tscf' ) ) );
}
