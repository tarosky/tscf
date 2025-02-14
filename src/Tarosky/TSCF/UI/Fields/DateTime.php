<?php

namespace Tarosky\TSCF\UI\Fields;

/**
 * Datetime picker
 *
 * @package Tarosky\TSCF\UI\Fields
 */
class DateTime extends Input {

	protected $default = array(
		'date_format' => 'yy-mm-dd',
		'time_format' => 'HH:mm:ss',
		'separator'   => ' ',
	);

	/**
	 * Add datetimepicker
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	protected function filter_class( $classes ) {
		$classes[] = 'tscf__datetimepicker';
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
			'data-time-format' => $this->field['time_format'],
			'data-separator'   => $this->field['separator'],
		);
	}
}
