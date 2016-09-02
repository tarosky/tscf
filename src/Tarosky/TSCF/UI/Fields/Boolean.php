<?php

namespace Tarosky\TSCF\UI\Fields;

class Boolean extends Input {

	protected $show_label = false;

	protected $type = 'checkbox';

	protected $default_to_drop = [ 'max', 'min', 'placeholder' ];

	protected function display_field() {
		?>
		<label class="tscf__label--inline tscf__label--boolean">
			<input type="checkbox" name="<?php echo esc_attr( $this->field['name'] ) ?>"
			       value="1" <?php checked( $this->get_data( false ) ) ?> />
			<?php echo esc_html( $this->field['label'] ) ?>
		</label>
		<?php
	}


}