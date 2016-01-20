<?php

namespace Tarosky\TSCF\Data;


use Tarosky\TSCF\Pattern\Singleton;

/**
 * Config file parser
 *
 * @package Tarosky\TSCF\Data
 */
class Parser extends Singleton {

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
