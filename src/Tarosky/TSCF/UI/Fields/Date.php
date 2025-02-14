<?php

namespace Tarosky\TSCF\UI\Fields;

/**
 * Datepicker
 *
 * @package Tarosky\TSCF\UI\Fields
 */
class Date extends Input {

	protected $default = array(
		'date_format' => 'yy-mm-dd',
	);

	protected function filter_class( $classes ) {
		$classes[] = 'tscf__datepicker';
		return $classes;
	}

	/**
	 * Add data attributes
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function filter_data_attributes( $data ) {
		return array(
			'data-date-format' => $this->field['date_format'],
		);
	}
}
