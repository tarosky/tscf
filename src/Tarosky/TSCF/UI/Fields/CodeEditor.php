<?php

namespace Tarosky\TSCF\UI\Fields;


class CodeEditor extends TextArea {

	protected $default = [
		'language' => 'css',
		'theme'    => 'xcode',
	];

	protected $default_to_drop = [
		'placeholder',
		'max',
		'min',
	];

	/**
	 * Save code
	 */
	protected function display_field() {
		?>
		<textarea class="tscf__input--ace" rows="<?php echo esc_attr( $this->field['rows'] ); ?>"
				  name="<?php echo esc_attr( $this->field['name'] ); ?>"
				  data-language="<?php echo esc_attr( $this->field['language'] ); ?>"
				  data-theme="<?php echo esc_attr( $this->field['theme'] ); ?>"
		><?php echo esc_textarea( $this->get_data( false ) ); ?></textarea>
		<div class="tscf__ace" id="<?php echo esc_attr( $this->field['name'] ); ?>"><?php echo esc_html( $this->get_data( false ) ); ?></div>
		<?php
	}

}
