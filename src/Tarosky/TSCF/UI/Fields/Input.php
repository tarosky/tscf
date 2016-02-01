<?php

namespace Tarosky\TSCF\UI\Fields;

use Tarosky\TSCF\Utility\Application;

/**
 * Input UI base
 *
 * @package Tarosky\TSCF\UI\Fields
 */
abstract class Input {

	use Application;

	protected $type = 'text';

	protected $object = null;

	protected $field = [];

	protected $required_base = [
		'name',
		'label',
	];

	/**
	 * @var bool Show label?
	 */
	protected $show_label = true;

	/**
	 * @var array Required params
	 */
	protected $required = [];

	protected $default_prototype = [
		'name'        => '',
		'label'       => '',
		'col'         => 1,
		'clear'       => false,
		'required'    => '',
		'default'     => '',
		'placeholder' => '',
		'description' => '',
		'unit'        => '',
	    'max'         => '',
	    'min'         => '',
	];

	protected $default = [];

	/**
	 * Constructor
	 *
	 * @param \WP_Term|\WP_Post $object
	 * @param array $field
	 */
	public function __construct( $object, $field ) {
		$this->field = $this->parse_default( $field );
		$this->object = $object;
	}

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
				$value = get_post_meta( $this->object->ID, $this->field['name'], true );
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

	public function row() {
		?>
		<div class="tscf__group tscf__col<?= $this->field['col'] ?><?= $this->field['clear'] ? ' tscf__col--clear' : ''?>">
			<?php if ( $this->show_label ) : ?>
				<label class="tscf__label" for="<?php echo esc_attr( $this->field['name'] ) ?>">
					<?php echo esc_html( $this->field['label'] ) ?>
					<?php if ($this->field['unit']) :?>
						<small class="tscf__unit"><?= esc_html($this->field['unit']) ?></small>
					<?php endif;?>
					<?php if ( $this->field['required'] ) : ?>
						<small class="tscf__required">* <?php echo esc_attr($this->_s('Required') ) ?></small>
					<?php endif; ?>
				</label>
			<?php endif; ?>
			<div class="tscf__fields">
				<?php $this->display_field(); ?>
			</div>
			<?php if ( $this->field['description'] ) : ?>
				<p class="description">
					<?php echo wp_kses( $this->field['description'], [ 'a' => ['class', 'href'] ] ) ?>
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
		$data_attr = [ ];
		foreach ( $this->filter_data_attributes( [ ] ) as $key => $value ) {
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
			<?php if ( $data ): ?>
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
	 */
	public function save_data() {
		$data = $this->normalize_save_data( $this->input->post( $this->field['name'] ) );
		if ( is_a( $this->object, 'WP_Post' ) ) {
			update_post_meta( $this->object->ID, $this->field['name'], $data );
		} elseif ( is_a( $this->object, 'WP_Term' ) ) {
			update_term_meta( $this->object->term_id, $this->field['name'], $data );
		} else {
			// Do nothing.
		}
	}

	/**
	 * Normalize save data.
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	protected function normalize_save_data( $data ){
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

	/**
	 * Parse default arguments
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function parse_default( $field ) {
		return wp_parse_args( (array) $field, wp_parse_args( $this->default, $this->default_prototype ) );
	}


}
