<?php

namespace Tarosky\TSCF\UI\Fields;

class TextArea extends Input {

	protected $default = [
		'rows' => 3,
	];

	protected function display_field() {
		?>
		<textarea class="tscf__input--textarea" rows="<?php echo esc_attr( $this->field['rows'] ); ?>"
				  name="<?php echo esc_attr( $this->field['name'] ); ?>"
				  id="<?php echo esc_attr( $this->field['name'] ); ?>"
			<?php if ( $this->field['placeholder'] ) : ?>
				placeholder="<?php echo esc_attr( $this->field['placeholder'] ); ?>"
			<?php endif; ?>
		><?php echo esc_textarea( $this->get_data( false ) ); ?></textarea>
		<?php
	}


}
