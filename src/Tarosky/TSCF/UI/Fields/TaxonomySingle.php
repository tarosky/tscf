<?php

namespace Tarosky\TSCF\UI\Fields;


class TaxonomySingle extends Select {

	protected $type = 'taxonomy_single';

	protected $default = array(
		'taxonomy'    => 'category',
		'only_child'  => true,
		'allow_empty' => false,
	);

	protected $default_to_drop = array( 'options', 'max', 'min' );

	/**
	 * Show field
	 */
	protected function display_field() {
		printf( '<select name="%1$s" id="%1$s">', esc_attr( $this->get_name() ) );
		if ( $this->field['allow_empty'] ) {
			printf( '<option value="">%s</option>', esc_html__( 'Not set', 'tscf' ) );
		}
		$current_value = $this->get_data( false );
		$taxonomy      = $this->field['taxonomy'];
		if ( $this->field['only_child'] ) {
			$parents = get_terms( array(
				'taxonomy'   => $taxonomy,
				'parent'     => 0,
				'hide_empty' => false,
			) );
			if ( $parents && ! is_wp_error( $parents ) ) {
				foreach ( $parents as $parent ) {
					printf( '<optgroup label="%s">', esc_attr( $parent->name ) );
					$children = get_terms( array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
						'parent'     => $parent->term_id,
					) );
					if ( $children && ! is_wp_error( $children ) ) {
						foreach ( $children as $child ) {
							$this->show_input( $child->term_id, $child->name, $current_value );
						}
					}
					echo '</optgroup>';
				}
			}
		} else {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			) );
			foreach ( $terms as $term ) {
				$this->show_input( $term->term_id, $term->name, $current_value );
			}
		}
		echo '</select>';
	}

	/**
	 * Show multiple field
	 *
	 * @param string $value
	 * @param string $label
	 * @param array  $current_value
	 */
	protected function show_input( $value, $label, $current_value ) {
		?>
		<option value="<?php echo esc_attr( $value ); ?>" <?php selected( in_array( $value, $current_value, true ) ); ?>>
			<?php echo esc_html( $label ); ?>
		</option>
		<?php
	}


	/**
	 * Get current value
	 *
	 * @param bool $filter
	 *
	 * @return array
	 */
	protected function get_data( $filter = true ) {
		$taxonomy = $this->field['taxonomy'];
		$terms    = get_the_terms( $this->object->ID, $taxonomy );
		return ( ! $terms || is_wp_error( $terms ) ) ? array() : array_map( function ( $term ) {
			return (int) $term->term_id;
		}, $terms );
	}

	/**
	 * Save taxonomy
	 *
	 * @return int
	 */
	public function save_data() {
		$data     = $this->normalize_save_data( $this->input->post( $this->field['name'] ) );
		$taxonomy = $this->field['taxonomy'];
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return 0;
		}
		if ( $data ) {
			wp_set_object_terms( $this->object->ID, $data, $taxonomy );
		} else {
			wp_delete_object_term_relationships( $this->object->ID, $taxonomy );
		}
		clean_post_cache( $this->object );
		return count( $data );
	}

	/**
	 * Filter taxonomy data
	 *
	 * @param string $data
	 *
	 * @return array
	 */
	protected function normalize_save_data( $data ) {
		if ( is_array( $data ) ) {
			$data = implode( ',', $data );
		}
		return array_map( 'intval', array_filter( array_map( 'trim', explode( ',', $data ) ) ) );
	}
}
