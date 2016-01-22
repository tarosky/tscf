<?php

namespace Tarosky\TSCF;


use Tarosky\TSCF\Pattern\Singleton;
use Tarosky\TSCF\Utility\Application;

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
				add_theme_page(
					$this->_s( 'Tarosky Custom Field config file editor' ),
					$this->_s( 'Custom Field Config' ), 'edit_themes', 'tscf',
					[ $this, 'editor' ]
				);
			} );
			// Add scripts.
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
			// Add Ajax save point.
			add_action( 'wp_ajax_tscf', [ $this, 'save_editor' ] );
		}
		// Check if file is valid.
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		// Add hook on edit screen.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );
		// Add hook on save_post
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
	}

	/**
	 * Show message on admin screen.
	 */
	public function admin_notices() {
		$path = $this->parser->config_file_path();
		if ( ! $path || ! file_exists( $path ) ) {
			$message = $this->_s( 'You have no config file. Upload <code>tscf.json</code> to your theme\'s root. ' );
			if ( current_user_can( 'edit_themes' ) ) {
				$message .= sprintf( $this->_s( 'Otherwise, you can edit it <a href="%s">directly</a>.' ), admin_url( 'themes.php?page=tscf' ) );
			}
			printf( '<div class="error"><p>%s</p></div>', $message );
		}
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
	public function save_post( $post_id, $post ){
		$this->parser->prepare('post', $post->post_type, $post);
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
	 * Save json via Ajax
	 */
	public function save_editor() {
		$json = [
			'success' => false,
			'message' => '',
		];
		try {
			// Check nonce.
			if ( ! $this->input->verify_nonce( 'tscf_edit' ) ) {
				throw new \Exception( $this->_s( 'Invalid access.' ), 401 );
			}
			// Check capability.
			if ( ! current_user_can( 'edit_themes' ) ) {
				throw new \Exception( $this->_s( 'Permission denied.' ), 403 );
			}
			// Check data.
			$body = $this->input->post_body();
			$data = json_decode( $body, true );
			if ( is_null( $data ) ) {
				throw new \Exception( $this->_s( 'Data is mall-formed. Nothing saved.' ), 400 );
			}
			// Save check
			$error = $this->parser->save( $body );
			if ( is_wp_error( $error ) ) {
				throw new \Exception( $error->get_error_message(), $error->get_error_code() );
			}
			// Everything O.K.
			$json['success'] = true;
			$json['message'] = $this->_s( 'Config file saved.' );
		} catch ( \Exception $e ) {
			$json['message'] = $e->getMessage();
			$json['code']    = $e->getCode();
			status_header( $e->getCode() );
		}
		wp_send_json( $json );
	}

	/**
	 * Load assets
	 *
	 * @param string $hook_suffix
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		// JSON editor
		$dir = plugin_dir_url( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'assets';
		if ( 'appearance_page_tscf' === $hook_suffix ) {
			wp_register_script( 'ace', $dir . '/lib/ace/ace.js', [], '1.2.3', true );
			wp_enqueue_script( 'tscf-editor', $dir . '/js/editor.js', [
				'jquery-effects-highlight',
				'ace',
			], '1.0.0', true );
			wp_localize_script( 'tscf-editor', 'TSCF', [
				'endpoint' => wp_nonce_url( admin_url( 'admin-ajax.php?action=tscf' ), 'tscf_edit' ),
				'ace'      => $dir . '/lib/ace',
			] );
			wp_enqueue_style( 'tscf-editor', $dir . '/css/tscf-editor.css', [], '1.0.0' );
		} else {
			// every page
			wp_enqueue_style( 'tscf-admin',  $dir.'/css/tscf-admin.css', [], '1.0.0' );
		}
	}

	/**
	 * Show editor
	 */
	public function editor() {
		?>
		<div class="wrap">
			<h2>
				<span class="dashicons dashicons-hammer"></span>
				<?php $this->_e( 'Tarosky Custom Field config file editor' ) ?>
			</h2>
			<pre id="tscf-editor"><?php echo esc_html( $this->parser->get_content() ) ?></pre>
			<p class="submit">
				<?php submit_button( __( 'Save' ), 'primary', 'tscf-submit', false ); ?>
				<span id="tscf-message"></span>
			</p>
			<div style="clear: left;"></div>
		</div><!-- //.wrap -->
		<?php
	}

}
