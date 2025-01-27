<?php

namespace Tarosky\TSCF\UI\Fields;

class Separator extends Base {

	protected $label = '';

	/**
	 * Constructor
	 *
	 * @param \WP_Term|\WP_Post $object
	 * @param array $field
	 */
	public function __construct( $object, $field ) {
		$this->label = isset( $field['label'] ) ? $field['label'] : '';
	}

	/**
	 * Show label.
	 */
	public function row() {
		printf( '<div class="tscf__separator">%s</div>', esc_html( $this->label ) );
	}

	/**
	 * Do nothing because it's just a separator
	 */
	public function save_data() {
		// Do nothing.
	}
}
