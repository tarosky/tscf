<?php

namespace Tarosky\TSCF\UI\Fields;

use Tarosky\TSCF\Utility\Application;

/**
 * Class Base
 *
 * @package Tarosky\TSCF\UI\Fields
 */
abstract class Base {

	use Application;

	protected $field = [];

	protected $object = null;

	protected $default = [];

	protected $default_prototype = [];

	protected $default_to_drop = [];

	protected $required_base = [
		'name',
		'label',
	];

	/**
	 * @var array Required params
	 */
	protected $required = [];


	/**
	 * Constructor
	 *
	 * @param \WP_Term|\WP_Post $object
	 * @param array $field
	 */
	public function __construct( $object, $field ) {
		$this->field  = $this->parse_default( $field );
		$this->object = $object;
	}

	abstract function row();

	/**
	 * Parse default arguments
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function parse_default( $field ) {
		$default = wp_parse_args( $this->default, $this->default_prototype );
		foreach ( $this->default_to_drop as $key ) {
			if ( isset( $default[ $key ] ) ) {
				unset( $default[ $key ] );
			}
		}
		return wp_parse_args( (array) $field, $default );
	}

	/**
	 * Save data
	 *
	 * @return int
	 */
	abstract public function save_data();

	/**
	 * Get field data.
	 *
	 * @return array
	 */
	public static function get_field_list() {
		$field = new static( null, [] );
		$names = [];
		foreach ( $field->parse_default( [] ) as $key => $value ) {
			switch ( $key ) {
				case 'options':
					$names[ $key ] = (object) null;
					break;
				default:
					$names[ $key ] = $value;
					break;
			}
		}
		return $names;
	}
}
