<?php

namespace Tarosky\TSCF;


use Tarosky\TSCF\Pattern\Singleton;
use Tarosky\TSCF\Utility\Application;

/**
 * Bootstrap file
 *
 * @package tscf
 */
class Bootstrap extends Singleton {

	use Application;

	/**
	 * Register hooks.
	 */
	protected function on_construct() {
		// IF file is editable,
		// show admin screen.
		if ( $this->file_editable() ) {
			add_action( 'admin_menu', function () {
				add_options_page(
					__( 'Tarosky Custom Field config file editor', 'tscf' ),
					__( 'Custom Field Config', 'tscf' ), 'manage_options', 'tscf',
					function() {
						include $this->root_dir . '/admin.php';
					}
				);
			} );
		}
		// Add scripts.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		// Register Ajax.
		Editor::instance();
		// Register REST API.
		Rest::instance();
		// Add hook on edit screen.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );
		// Add hook on save_post.
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
	}
	
	/**
	 * Load text domain.
	 */
	public function load_text_domain() {
		$mo = sprintf( 'hashboard-%s.mo', get_user_locale() );
		return load_textdomain( 'tscf', $this->root_dir . '/languages/' . $mo );
	}

	/**
	 * Register meta boxes
	 *
	 * @param string $post_type
	 * @param \WP_Post $post
	 */
	public function add_meta_boxes( $post_type, $post ) {
		$this->parser->register( 'post', $post_type, $post );
	}

	/**
	 * Register save hook action
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save_post( $post_id, $post ) {
		$this->parser->prepare( 'post', $post->post_type, $post );
	}

	/**
	 * Load assets
	 *
	 * @param string $hook_suffix
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( ! $this->is_editor( $hook_suffix ) ) {
			// Register MP6 if not exists.
			wp_register_style( 'jquery-ui-mp6', "{$this->url}/lib/jquery-ui-mp6/css/jquery-ui.css", [], '1.0.2' );
			wp_register_style( 'jquery-ui-timepicker-addon', "{$this->url}/lib/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.css", [ 'jquery-ui-mp6' ], '1.5.5' );
			wp_register_script( 'jquery-ui-timepicker-addon', "{$this->url}/lib/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js", [
				'jquery-ui-datepicker',
				'jquery-ui-slider',
			], '1.5.5', true );
			// Check language.
			$lang         = explode( '_', get_locale() );
			$file_name    = '/lib/jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-%s.js';
			$base         = $this->root_dir . '/assets' . $file_name;
			$file_to_load = '';
			if ( count( $lang ) > 1 ) {
				$path = sprintf( $base, "{$lang[0]}-{$lang[1]}" );
				if ( file_exists( $path ) ) {
					$file_to_load = "{$lang[0]}-{$lang[1]}";
				}
			}
			if ( ! $file_to_load ) {
				$path = sprintf( $base, $lang[0] );
				if ( file_exists( $path ) ) {
					$file_to_load = $lang[0];
				}
			}
			if ( ! $file_to_load ) {
				$i18n_file = '/lib/jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-addon-i18n.min.js';
			} else {
				$i18n_file = sprintf( $file_name, $file_to_load );
			}
			wp_register_script( 'jquery-ui-timepicker-addon-i18n', $this->url . $i18n_file, [ 'jquery-ui-timepicker-addon' ], '1.5.5', true );
			// Check language and find if exists.
			wp_register_script( 'jquery-live-preview', "{$this->url}/lib/jquery-live-preview/jquery-live-preview.min.js", [ 'jquery' ], '1.1.0', true );
			wp_register_style( 'jquery-live-preview', "{$this->url}/css/livepreview.css", [], '1.1.0', 'screen' );
			// Select2.
			$has_select2_locale = false;
			wp_register_script( 'select2', "{$this->url}/lib/select2/js/select2.min.js", [ 'jquery' ], '4.0.3', true );
			if ( 0 !== strpos( get_locale(), 'en' ) ) {
				list( $lang ) = explode( '_', strtolower( get_locale() ) );
				if ( file_exists( "{$this->root_dir}/assets/lib/select2/i18n/{$lang}.js" ) ) {
					$has_select2_locale = true;
					wp_register_script( 'select2-local', "{$this->url}/lib/select2/js/i18n/{$lang}.js", [ 'select2' ], '4.0.3', true );
				}
			}
			wp_register_style( 'select2', "{$this->url}/lib/select2/css/select2.min.css", [], '4.0.3' );
			// AceEditor.
			wp_register_script( 'ace-editor', "{$this->url}/lib/ace/ace.js", [], '1.2.6', true );
			// Every page.
			wp_enqueue_style( 'tscf-admin', $this->url . '/css/tscf-admin.css', [
				'jquery-ui-timepicker-addon',
				'jquery-live-preview',
			    'select2',
			], tscf_version() );
			wp_enqueue_script( 'tscf-helper', $this->url . '/js/dist/tscf-helper.js', [
				'jquery-ui-timepicker-addon-i18n',
				'jquery-effects-highlight',
				'jquery-ui-sortable',
				'jquery-live-preview',
				( $has_select2_locale ? 'select2-local' : 'select2' ),
			    'ace-editor',
			], tscf_version(), true );
			wp_localize_script( 'tscf-helper', 'TSCF', [
				'delete' => __( 'Delete', 'tscf' ),
				'select' => __( 'Select', 'tscf' ),
			    'nonce'  => wp_create_nonce( 'wp_rest' ),
			    'root'   => rest_url( '/tscf/v1' ),
			] );
		} // End if().
	}
}
