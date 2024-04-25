<?php

namespace Tarosky\TSCF\UI\Fields;

class Url extends Text {
	/**
	 * Add additional class
	 *
	 * @param array $classes
	 */
	protected function filter_class( $classes ) {
		$classes[] = 'tscf__input--url';
		return $classes;
	}


	protected function display_field() {
		?>
		<div class="tscf__url--wrapper">
			<div class="tscf__url--inner">
				<?php parent::display_field(); ?>
			</div>
			<div class="tscf__url--controller">
				<button class="tscf-select-button button-primary">
					<span class="dashicons dashicons-images-alt2"></span>
					<?php _e( 'Select', 'tscf' ); ?>
				</button>
				<a class="tscf-preview-button button" href="#" target="_blank" title="<?php esc_attr_e( 'Preview', 'tscf' ); ?>">
					<span class="dashicons dashicons-visibility"></span>
				</a>
			</div>
		</div>
		<?php
	}


}

