<?php

namespace Tarosky\TSCF\UI\Fields;

class Select extends Radio {


	/**
	 * Show field
	 */
	protected function display_field() {
		printf( '<select name="%1$s" id="%1$s">', esc_attr( $this->get_name() ) );
		parent::display_field();
		echo '</select>';
	}

	/**
	 * Show multiple field
	 *
	 * @param string $value
	 * @param string $label
	 * @param string $current_value
	 */
	protected function show_input( $value, $label, $current_value ) {
		?>
		<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $this->checked( $value, $current_value ) ); ?>><?php echo esc_html( $label ); ?></option>
		<?php
	}
}
