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
		$this->field = $this->parse_default( $field );
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
		return wp_parse_args( (array) $field, wp_parse_args( $this->default, $this->default_prototype ) );
	}

	/**
	 * Save data
	 *
	 * @return int
	 */
	abstract public function save_data();
}
