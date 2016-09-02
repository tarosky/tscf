<?php

namespace Tarosky\TSCF\UI\Fields;



/**
 * Input UI base
 *
 * @package Tarosky\TSCF\UI\Fields
 */
abstract class Input extends Base{

	protected $type = 'text';

	/**
	 * @var bool Show label?
	 */
	protected $show_label = true;

	protected $default_prototype = [
		'name'        => '',
		'label'       => '',
		'col'         => 1,
		'clear'       => false,
		'required'    => '',
		'default'     => '',
		'placeholder' => '',
		'description' => '',
	    'max'         => '',
	    'min'         => '',
	];

	/**
	 * Get data
	 *
	 * @param bool $filter Default false.
	 *
	 * @return mixed
	 */
	protected function get_data( $filter = true ) {
		switch ( get_class( $this->object ) ) {
			case 'WP_Post':
			default:
				switch ( $this->field['name'] ) {
					case 'menu_order':
						$value = $this->object->menu_order;
						break;
					case 'excerpt':
						$value = $this->object->post_excerpt;
						break;
					default:
						$value = get_post_meta( $this->object->ID, $this->field['name'], true );
						break;
				}
				break;
		}
		if ( $filter ) {
			if ( $this->field['default'] && '' === $value ) {
				$value = $this->field['default'];
			}
			$value = $this->filter( $value );
		}
		return $value;
	}

	/**
	 * Get data
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	protected function filter($data) {
		return $data;
	}

	/**
	 * Render row
	 */
	public function row() {
		?>
		<div class="tscf__group tscf__col<?= $this->field['col'] ?><?= $this->field['clear'] ? ' tscf__col--clear' : ''?>">
			<?php if ( $this->show_label ) : ?>
				<label class="tscf__label" for="<?php echo esc_attr( $this->field['name'] ) ?>">
					<?php echo esc_html( $this->field['label'] ) ?>
					<?php if ( $this->field['unit'] ) :?>
						<small class="tscf__unit"><?= esc_html( $this->field['unit'] ) ?></small>
					<?php endif;?>
					<?php if ( $this->field['required'] ) : ?>
						<small class="tscf__required">* <?php echo esc_attr( $this->_s( 'Required' ) ) ?></small>
					<?php endif; ?>
				</label>
			<?php endif; ?>
			<div class="tscf__fields">
				<?php $this->display_field(); ?>
			</div>
			<?php if ( $this->field['description'] ) : ?>
				<p class="description">
					<?php echo wp_kses( $this->field['description'], [ 'a' => [ 'class', 'href' ] ] ) ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Display field
	 */
	protected function display_field() {
		$classes   = implode( ' ', $this->filter_class( [
			'tscf__input',
			'tscf__input--' . $this->type,
		] ) );
		$data_attr = [];
		foreach ( $this->filter_data_attributes( [] ) as $key => $value ) {
			$data_attr[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
		}
		$data = implode( ' ', $data_attr )
		?>
		<input class="<?php echo esc_attr( $classes ) ?>"
		       name="<?php echo esc_attr( $this->field['name'] ) ?>" id="<?php echo esc_attr( $this->field['name'] ) ?>"
		       type="<?php echo esc_attr( $this->type ) ?>"
			<?php if ( $this->field['placeholder'] ) : ?>
				placeholder="<?php echo esc_attr( $this->field['placeholder'] ) ?>"
			<?php endif; ?>
			   value="<?php echo esc_attr( $this->get_data( false ) ) ?>"
			<?php if ( $data ) : ?>
				<?php echo $data ?>
			<?php endif; ?> />
		<?php
	}

	/**
	 * Filter data attributes
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	protected function filter_data_attributes($data) {
		return $data;
	}

	/**
	 * Filter classes
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	protected function filter_class($classes) {
		return $classes;
	}

	/**
	 * Save data
	 *
	 * @return int
	 */
	public function save_data() {
		$data = $this->normalize_save_data( $this->input->post( $this->field['name'] ) );
		if ( is_a( $this->object, 'WP_Post' ) ) {
			switch ( $this->field['name'] ) {
				case 'menu_order':
				case 'excerpt':
					// Do nothing, because it'll be saved automatically
					return 0;
					break;
				default:
					return update_post_meta( $this->object->ID, $this->field['name'], $data ) ? 1 : 0;
					break;
			}
		} elseif ( is_a( $this->object, 'WP_Term' ) ) {
			return update_term_meta( $this->object->term_id, $this->field['name'], $data ) ? 1 : 0;
		} else {
			// Do nothing.
			return 0;
		}
	}

	/**
	 * Normalize save data.
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	protected function normalize_save_data( $data ) {
		return $data;
	}

	/**
	 * Validate errors
	 *
	 * @return true|\WP_Error
	 */
	protected function validate() {
		$required = array_merge( $this->required_base, $this->required );
		$error    = new \WP_Error();
		// Check required key.
		foreach ( $required as $key ) {
			if ( $key && ! isset( $this->field[ $key ] ) ) {
				$error->add( 'param_error', $this->_s( '%s is required for field %s', $key, $this->field['label'] ) );
			}
		}
		if ( isset( $this->field['min'], $this->field['max'] ) && is_numeric( $this->field['max'] ) && is_numeric( $this->field['max'] ) ) {
			if ( $this->field['min'] > $this->field['max'] ) {
				$error->add( 'param_error', $this->_s( 'min property of %s must be less than max property.' ) );
			}
		}
		return $error->errors ? $error : true;
	}


}
