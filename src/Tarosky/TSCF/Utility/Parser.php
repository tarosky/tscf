<?php

namespace Tarosky\TSCF\Utility;


use Tarosky\TSCF\Pattern\Singleton;
use Tarosky\TSCF\UI\Base;

/**
 * Config file parser
 *
 * @package Tarosky\TSCF\Data
 */
class Parser extends Singleton {

	protected $initialized = false;

	/**
	 * @var array Data store.
	 */
	private $data = [];

	/**
	 * Get config file path
	 *
	 * @return string
	 */
	public function config_file_path() {
		foreach ( [ get_stylesheet_directory(), get_template_directory() ] as $dir ) {
			if ( file_exists( $dir . DIRECTORY_SEPARATOR . 'tscf.json' ) ) {
				return $dir . DIRECTORY_SEPARATOR . 'tscf.json';
			}
		}

		/**
		 * tscf_config_file_path
		 *
		 * Returns file path to read.
		 *
		 * @package tscf
		 *
		 * @param string $path Default empty string.
		 *
		 * @return string
		 */
		return (string) apply_filters( 'tscf_config_file_path', '' );
	}

	/**
	 * Build this class.
	 */
	public function build() {
		if ( $this->initialized ) {
			return;
		}
		$path = $this->config_file_path();
		if ( ! $path || ! file_exists( $path ) ) {
			return;
		}
		$json = json_decode( file_get_contents( $path ), true );
		if ( is_null( $json ) ) {
			return;
		}
		$this->initialized = true;
		$this->data = wp_parse_args( (array) $json, [
			'name' => '',
		    'label' => '',
		    'type' => 'post',
		    'fields' => [],
		]);
	}

	/**
	 * Register Fields
	 *
	 * @param string $type
	 * @param string $sub_type
	 * @param \WP_Term|\WP_Post $object
	 */
	public function register( $type, $sub_type, $object ) {
		$this->build();
		$valid_data = $this->filter( $type, $sub_type, $object );
		switch ( $type ) {
			default:
				$class_name = 'Tarosky\\TSCF\\UI\\PostMeta';
				break;
		}
		// this is post, so register_meta_box
		foreach ( $valid_data as $data ) {
			new $class_name( $object, $data );
		}
	}

	/**
	 * Prepare field
	 *
	 * @param string $type
	 * @param string $sub_type
	 * @param \WP_Term|\WP_Post $object
	 */
	public function prepare( $type, $sub_type, $object ) {
		$this->build();
		$valid_data = $this->filter( $type, $sub_type, $object );
		switch ( $type ) {
			default:
				$class_name = 'Tarosky\\TSCF\\UI\\PostMeta';
				break;
		}
		// this is post, so register_meta_box
		if ( class_exists( $class_name ) ) {
			foreach ( $valid_data as $data ) {
				/** @var Base $instance */
				$instance = new $class_name( $object, $data );
				$instance->save();
			}
		}
	}

	/**
	 * Filter data.
	 *
	 * @param string $type 'post', 'comment', 'term'.
	 * @param string $sub_type post_type, taxonomy, comment_type.
	 * @param \WP_Post|\WP_Term $object
	 *
	 * @return array
	 */
	public function filter( $type, $sub_type, $object ) {
		$valid_data = [];
		foreach ( $this->data as $data ) {
			$this_type = ! isset( $data['type'] ) || 'post' === $data['type'] ? 'post' : $data['type'];
			if ( $this_type === $type ) {
				switch ( $type ) {
					default:
						// This is post meta, so check post_types.
						$post_types  = isset( $data['post_types'] ) ? (array) $data['post_types'] : [];
						$should_show = ( array_search( $sub_type, $post_types ) !== false );
						break;
				}
				/**
				 * tscf_should_show
				 *
				 * Whether to if show field.
				 *
				 * @param bool $should_show
				 * @param \WP_Post|\WP_Term $object
				 * @param array $data
				 */
				$should_show = apply_filters( 'tscf_should_show', $should_show, $object, $data );
				if ( $should_show ) {
					// Check child values.
					$fields = [];
					foreach ( $data['fields'] as $field ) {
						// Check only_in
						if ( isset( $field['only_in'] ) && false === array_search( $sub_type, (array) $field['only_in'] ) ) {
							continue;
						}
						// Check exclude
						if ( isset( $field['exclude'] ) && false !== array_search( $sub_type, (array) $field['exclude'] ) ) {
							continue;
						}
						$fields[] = $field;
					}
					// If all fields are ready, register it.
					if ( ! empty( $fields ) ) {
						$data['fields'] = $fields;
						$valid_data[]   = $data;
					}
				}
			}
		}

		return $valid_data;
	}

	/**
	 * Get config file's content.
	 *
	 * @return string
	 */
	public function get_content() {
		$path = $this->config_file_path();
		if ( file_exists( $path ) ) {
			return file_get_contents( $path );
		} else {
			return '';
		}
	}

	/**
	 * Save file to JSON.
	 *
	 * @param string $content
	 *
	 * @return true|\WP_Error
	 */
	public function save( $content ) {
		$path = $this->config_file_path();
		if ( ! $path ) {
			$path = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'tscf.json';
		}
		try {
			if ( file_exists( $path ) ) {
				if ( ! is_writable( $path ) ) {
					throw new \Exception( sprintf( __( '%s is not writable.', 'tscf' ), $path ), 403 );
				}
			} else {
				if ( ! is_writable( dirname( $path ) ) ) {
					throw new \Exception( sprintf( __( '%s is not writable.', 'tscf' ), $path ), 403 );
				}
			}
			if ( false === file_put_contents( $path, $content ) ) {
				throw new \Exception( sprintf( __( 'Failed to save data.', 'tscf' ), $path ), 500 );
			}

			return true;
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage(), [ 'status' => $e->getCode() ] );
		}
	}

	/**
	 * Validate config file.
	 *
	 * @return true|\WP_Error
	 */
	public function validate() {
		return true;
	}

}
