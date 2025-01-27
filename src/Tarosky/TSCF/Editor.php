<?php
namespace Tarosky\TSCF;

use Tarosky\TSCF\Pattern\Singleton;
use Tarosky\TSCF\Utility\Application;

/**
 * Api Class
 * @package Tarosky\TSCF
 */
class Editor extends Singleton {

	use Application;

	/**
	 * Register Actions
	 */
	protected function on_construct() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'admin_init', function () {
				// Add Ajax save point.
				add_action( 'wp_ajax_tscf_save', array( $this, 'save_editor' ) );
				// Add Field changer
				add_action( 'wp_ajax_tscf_field', array( $this, 'get_field' ) );
				// Add template endpoint
				add_action( 'wp_ajax_tscf_template', array( $this, 'get_template' ) );
			} );
		}
		// Check if file is valid.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Show message on admin screen.
	 */
	public function admin_notices() {
		$path = $this->parser->config_file_path();
		if ( ! $path || ! file_exists( $path ) ) {
			$message = __( 'You have no config file. Upload <code>tscf.json</code> to your theme\'s root.', 'tscf' );
			if ( current_user_can( 'manage_options' ) ) {
				$message .= sprintf( __( 'Otherwise, you can edit it <a href="%s">directly</a>.', 'tscf' ), admin_url( 'options-general.php?page=tscf' ) );
			}
			printf( '<div class="error"><p>%s</p></div>', $message );
		}
	}

	/**
	 * Register scripts
	 *
	 * @param string $hook_suffix
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( $this->is_editor( $hook_suffix ) ) {
			// Register assets
			wp_register_script( 'angular', $this->url . '/lib/angular/angular.min.js', array(), '1.5.8' );
			wp_register_script( 'angular-ui-sortable', $this->url . '/lib/angular/sortable.min.js', array( 'angular', 'jquery-ui-sortable' ), '1.2.6' );
			wp_enqueue_script( 'tscf-editor', $this->url . '/js/dist/editor.js', array(
				'angular-ui-sortable',
			), '1.0.3', true );
			// Register scripts
			wp_localize_script( 'tscf-editor', 'TSCF', $this->js_vars() );
			// Register CSS
			wp_enqueue_style( 'tscf-editor', $this->url . '/css/tscf-editor.css', array(), '1.0.0' );
		}
	}

	/**
	 * Variable pass to JS
	 *
	 * @return array
	 */
	public function js_vars() {
		// Check current validity.
		$errors   = array();
		$editable = $this->parser->editable();
		// Is editable?
		if ( is_wp_error( $editable ) ) {
			$errors = array_merge( $errors, $editable->get_error_messages() );
		}
		// Is valid?
		if ( is_wp_error( ( $validation = $this->parser->validate() ) ) ) {
			$errors = array_merge( $errors, $validation->get_error_messages() );
		}
		// Settings
		$settings = json_decode( $this->parser->get_content(), true ) ?: array();
		// Post types
		$post_types = array();
		foreach ( get_post_types( array(), OBJECT ) as $post_type ) {
			switch ( $post_type->name ) {
				case 'revision':
				case 'nav_menu_item':
					// Skip object.
					break;
				default:
					$post_types[] = $post_type;
					break;
			}
		}
		return array(
			'endpoint'  => array(
				'save'     => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'tscf_edit' ) . '&action=tscf_save',
				'field'    => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'tscf_edit' ) . '&action=tscf_field',
				'template' => admin_url( 'admin-ajax.php' ) . '?action=tscf_template',
			),
			'message'   => array(
				'delete' => __( 'Are you sure to delete this item?', 'tscf' ),
			),
			'errors'    => $errors,
			'settings'  => $settings,
			'new'       => __( 'New Field', 'tscf' ),
			'cols'      => array(
				array(
					'label' => sprintf( __( '%d col', 'tscf' ), 1 ),
					'value' => 1,
				),
				array(
					'label' => sprintf( __( '%d cols', 'tscf' ), 2 ),
					'value' => 2,
				),
				array(
					'label' => sprintf( __( '%d cols', 'tscf' ), 3 ),
					'value' => 3,
				),
			),
			'context'   => array(
				array(
					'label' => __( 'Normal', 'tscf' ),
					'value' => 'normal',
				),
				array(
					'label' => __( 'Side', 'tscf' ),
					'value' => 'side',
				),
				array(
					'label' => __( 'Advanced', 'tscf' ),
					'value' => 'advanced',
				),
			),
			'priority'  => array(
				array(
					'label' => __( 'High', 'tscf' ),
					'value' => 'high',
				),
				array(
					'label' => __( 'Default', 'tscf' ),
					'value' => 'default',
				),
				array(
					'label' => __( 'Core', 'tscf' ),
					'value' => 'core',
				),
				array(
					'label' => __( 'Low', 'tscf' ),
					'value' => 'low',
				),
			),
			'postTypes' => $post_types,
			'types'     => $this->parser->available_types(),
		);
	}

	/**
	 * Save editor content
	 */
	public function save_editor() {
		$json = array(
			'success' => false,
			'message' => '',
			'errors'  => array(),
		);
		try {
			// Check nonce.
			if ( ! $this->input->verify_nonce( 'tscf_edit' ) ) {
				throw new \Exception( __( 'Invalid access.', 'tscf' ), 401 );
			}
			// Check capability.
			if ( ! current_user_can( 'edit_themes' ) ) {
				throw new \Exception( __( 'Permission denied.', 'tscf' ), 403 );
			}
			// Check data.
			$body = $this->input->post_body();
			$data = json_decode( $body, true );
			if ( is_null( $data ) ) {
				throw new \Exception( __( 'Data is mall-formed. Nothing saved.', 'tscf' ), 400 );
			}
			// Save check
			$error = $this->parser->save( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
			if ( is_wp_error( $error ) ) {
				throw new \Exception( $error->get_error_message(), $error->get_error_code() );
			}
			// Everything O.K.
			$json['success'] = true;
			$json['message'] = __( 'Config file saved.', 'tscf' );
		} catch ( \Exception $e ) {
			$json['errors'][] = $e->getMessage();
			status_header( $e->getCode() );
		}
		wp_send_json( $json );
	}

	/**
	 * Return angular template
	 */
	public function get_template() {
		try {
			if ( ! current_user_can( 'edit_themes' ) ) {
				throw new \Exception( __( 'You have no permission.', 'tscf' ), 401 );
			}
			$templates = array();
			$base      = $this->root_dir;
			foreach ( scandir( $base . '/assets/html' ) as $file ) {
				if ( preg_match( '/^([^.].*)\.php$/u', $file, $match ) ) {
					$templates[] = $match[1];
				}
			}
			$request = $this->input->get( 'file' );
			if ( false === array_search( $request, $templates ) ) {
				throw new \Exception( __( 'Such templalte doesn\'t exist.', 'tscf' ), 404 );
			}
			include $base . sprintf( '/assets/html/%s.php', $request );
			exit;
		} catch ( \Exception $e ) {
			status_header( $e->getCode() );
			wp_send_json( array(
				'error'   => true,
				'message' => $e->getMessage(),
			) );
		}
	}

	/**
	 * Get field properties
	 */
	public function get_field() {
		$json = array(
			'success' => false,
			'message' => '',
			'errors'  => array(),
		);
		try {
			// Check nonce.
			if ( ! $this->input->verify_nonce( 'tscf_edit' ) ) {
				throw new \Exception( __( 'Invalid access.', 'tscf' ), 401 );
			}
			// Check capability.
			if ( ! current_user_can( 'edit_themes' ) ) {
				throw new \Exception( __( 'Permission denied.', 'tscf' ), 403 );
			}
			// Check data.
			$type  = $this->input->get( 'field' );
			$field = $this->parser->get_field( $type );
			if ( is_wp_error( $field ) ) {
				throw new \Exception( $field->get_error_message(), 400 );
			}
			// Everything O.K.
			$json['success'] = true;
			$json['field']   = $field;
			$json['message'] = sprintf( __( 'Field %s is available.', 'tscf' ), $type );
		} catch ( \Exception $e ) {
			$json['errors'][] = $e->getMessage();
			status_header( $e->getCode() );
		}
		wp_send_json( $json );
	}
}
