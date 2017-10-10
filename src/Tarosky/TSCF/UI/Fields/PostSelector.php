<?php

namespace Tarosky\TSCF\UI\Fields;

/**
 * Class PostSelector
 * @package tscf
 */
class PostSelector extends Input {

	protected $default = [
		'post_type' => 'post',
	    'max'     => 0,
	];

	protected $required = [
		'post_type',
	];

	protected $default_to_drop = [
		'placeholder',
	    'min',
	    'default'
	];

	/**
	 * Show data
	 */
	protected function display_field() {
		?>
		<input type="hidden" name="<?php echo esc_attr( $this->field['name'] ) ?>" value="<?= esc_attr( $this->get_data( false ) ) ?>" />
		<select class="tscf__input tscf__input--token"
				data-post-type="<?= esc_attr( $this->field['post_type'] ) ?>"
				data-limit="<?= esc_attr( $this->field['max'] ) ?>"
				id="<?php echo esc_attr( $this->field['name'] ) ?>"
				<?php if ( 1 != $this->field['max'] ) : ?>
					multiple="multiple"
				<?php endif; ?>
		>
			<?php foreach ( array_filter( explode( ',', $this->get_data() ), function( $id ) {
				return is_numeric( $id );
			}  ) as $post_id ) : ?>
				<option value="<?= esc_attr( $post_id ) ?>" selected>
                    <?= esc_html( sprintf(
                            '%1$s(%2$s)',
                            get_the_title( $post_id ),
                            tscf_post_status( $post_id )
                    ) ) ?>
                </option>
			<?php endforeach; ?>
		</select>
		<?php if ( $this->field['max'] ) : ?>
			<p class="description">
				<?php
				printf(
					__( 'You can choose %s.', 'tscf'),
					sprintf(
						_n( '%d post', '%d posts', $this->field['max'], 'tscf'),
						$this->field['max']
					)
				);
				?>
			</p>
		<?php endif; ?>
		<?php
	}


}
