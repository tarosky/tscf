<?php

namespace Tarosky\TSCF\UI\Fields;

class Radio extends Input {

	protected $type = 'radio';

	protected $default = array(
		'options' => array(),
	);

	protected $default_to_drop = array( 'min', 'max', 'placeholder' );

	/**
	 * Show field
	 */
	protected function display_field() {
		$current_value = $this->get_data( false );
		$options       = (array) $this->field['options'];
		if ( isset( $this->field['src'] ) ) {
			foreach ( get_posts( $this->field['src'] ) as $p ) {
				$options[ $p->ID ] = get_the_title( $p );
			}
		}
		foreach ( $options as $value => $label ) {
			$this->show_input( $value, $label, $current_value );
		}
	}

	/**
	 * Get name attribute
	 *
	 * @return string
	 */
	protected function get_name() {
		return $this->field['name'];
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
		<label class="tscf__label--inline tscf__label--multiple">
			<input type="<?php echo esc_attr( $this->type ); ?>" name="<?php echo esc_attr( $this->get_name() ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				<?php checked( $this->checked( $value, $current_value ) ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
		<?php
	}

	/**
	 * Detect if value is checked
	 *
	 * @param mixed $value
	 * @param mixed $current_value
	 *
	 * @return bool
	 */
	protected function checked( $value, $current_value ) {
		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		return $value == $current_value || ( '' === $current_value && $value === $this->field['default'] );
	}
}
