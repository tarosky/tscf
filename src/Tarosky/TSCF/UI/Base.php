<?php

namespace Tarosky\TSCF\UI;
use Tarosky\Beckham\Utility\Input;
use Tarosky\TSCF\Utility\Application;

/**
 * Controller Base
 *
 * @package Tarosky\TSCF\UI
 * @property-read string $name
 * @property-read string $label
 * @property-read array $fields
 */
abstract class Base {

	/**
	 * @var array
	 */
	protected $_fields = [];

	/**
	 * @var \WP_Post|\WP_Term
	 */
	protected $object = null;

	/**
	 * Constructor
	 *
	 *
	 * @param \WP_Post|\WP_Term
	 * @param array $fields
	 */
	public function __construct( $object, $fields ) {
		$this->object  = $object;
		$this->_fields = (array) $fields;
	}

	/**
	 * Save posted data
	 */
	public function save() {
		$input_util = Input::instance();
		if ( $input_util->verify_nonce( $this->name, "_{$this->name}nonce" ) ) {
			foreach ( $this->fields as $field ) {
				$class_name = $this->get_field_class( $field );
				if ( class_exists( $class_name ) ) {
					$input = new $class_name( $this->object, $field );
					$input->save_data();
				}
			}
		}
	}

	/**
	 * Render screen.
	 *
	 * @return void
	 */
	public function render() {
		wp_nonce_field( $this->name, "_{$this->name}nonce", false );
		echo '<div class="tscf">';
		foreach ( $this->fields as $field ) {
			$class = $this->get_field_class( $field );
			if ( class_exists( $class ) ) {
				$field = new $class( $this->object, $field );
				$field->row();
			}
		}
		echo '<div style="clear:left;"></div></div>';
	}

	/**
	 * Get class name to parse
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	protected function get_field_class( $field ) {
		$field      = wp_parse_args( $field, [
			'type' => 'text',
		] );
		$lower_name = strtolower( $field['type'] );
		switch ( $lower_name ) {
			case 'separator':
			case 'text':
			case 'hidden':
			case 'text_area':
			case 'password':
			case 'number':
			case 'boolean':
			case 'select':
			case 'radio':
			case 'checkbox':
			case 'date_time';
			case 'date':
				$class_name = 'Tarosky\\TSCF\\UI\\Fields\\' . implode( '', array_map( function ( $seg ) {
					return ucfirst( $seg );
				}, explode( '_', $lower_name ) ) );
				break;
			case 'datetime':
				$class_name = 'Tarosky\\TSCF\\UI\\Fields\\DateTime';
				break;
			case 'textarea':
				$class_name = 'Tarosky\\TSCF\\UI\\Fields\\TextArea';
				break;
			default:
				if ( class_exists( $field['type'] ) ) {
					$class_name = $field['type'];
				} else {
					$class_name = 'Tarosky\\TSCF\\UI\\Fields\\Text';
				}
				break;
		}

		return $class_name;
	}


	/**
	 * Getter
	 *
	 * @param string $key
	 *
	 * @return null
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'fields':
				return isset( $this->_fields['fields'] ) ? (array) $this->_fields['fields'] : [];
				break;
			case 'name':
			case 'label':
				return isset( $this->_fields[ $key ] ) ? (string) $this->_fields[ $key ] : '';
				break;
			default:
				return null;
				break;
		}
	}
}
