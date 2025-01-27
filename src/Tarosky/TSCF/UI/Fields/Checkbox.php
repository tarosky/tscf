<?php

namespace Tarosky\TSCF\UI\Fields;

/**
 * Checkbox UI
 *
 * This save checkbox value to
 *
 * @package Tarosky\TSCF\UI\Fields
 */
class Checkbox extends Radio {

	protected $type = 'checkbox';

	/**
	 * Override parent's method and return array.
	 *
	 * @param bool $filter
	 *
	 * @return array
	 */
	protected function get_data( $filter = true ) {
		$data = array_filter( explode( ',', parent::get_data( false ) ), function ( $var ) {
			return ! empty( $var );
		} );
		if ( $filter ) {
			$data = array_map( function ( $val ) {
				return $this->filter( $val );
			}, $data );
		}

		return $data;
	}


	/**
	 * Get name attribute
	 *
	 * @return string
	 */
	protected function get_name() {
		return $this->field['name'] . '[]';
	}

	/**
	 * Override parent's checked method
	 *
	 * @param mixed $value
	 * @param mixed $current_value
	 *
	 * @return bool
	 */
	protected function checked( $value, $current_value ) {
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		return in_array( $value, $current_value );
	}


	/**
	 * Change array to CSV
	 *
	 * @param mixed $data
	 *
	 * @return string
	 */
	protected function normalize_save_data( $data ) {
		return implode( ',', array_filter(
			array_map(
				function ( $var ) {
					return trim( $var );
				},
				(array) $data
			),
			function ( $var ) {
				return ! empty( $var );
			}
		) );
	}
}
