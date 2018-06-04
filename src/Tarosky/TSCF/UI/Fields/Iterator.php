<?php

namespace Tarosky\TSCF\UI\Fields;

use Tarosky\TSCF\UI\Base as UIBase;

class Iterator extends Base {

	/**
	 * @var array Required fields.
	 */
	protected $required = [ 'fields' ];

	protected $default = [
		'col'         => 1,
		'clear'       => false,
		'max'         => 5,
	    'description' => '',
	    'fields'       => [],
	];

	/**
	 * Render row
	 */
	public function row() {
		?>
		<div class="tscf__group tscf__col--clear tscf--iterator"
		     data-max="<?php echo esc_attr( $this->field['max'] ) ?>"
		     data-prefix="<?php echo esc_attr( $this->field['name'] ) ?>">
			<div class="tscf__label--iterator">
				<?= esc_html( $this->field['label'] ) ?>
				<a class="button tscf__add" href="#">追加</a>
			</div>
			<?php if ( $desc = $this->field['description'] ) : ?>
			<p class="description"><?php echo esc_html( $desc ) ?></p>
			<?php endif; ?>
			<div class="tscf__childList">
				<?php
				$counter = 0;
				foreach ( $this->get_field_indexes() as $index ) {
					echo $this->single_row( $index );
				}
				?>
			</div>
			<script type="text/template" class="tscf__template">
				<?= $this->single_row( 9999 ) ?>
			</script>
			<input type="hidden" class="tscf__index" name="_index_of_<?php echo $this->field['name'] ?>"
			       value="<?php echo esc_attr( $counter ) ?>"/>
		</div>
		<?php
	}


	/**
	 * Get field.
	 *
	 * @param int $index
	 *
	 * @return string
	 */
	public function single_row( $index ) {
		ob_start();
		?>
		<div class="tscf__child">
			<?php
			foreach ( $this->field['fields'] as $field ) {
				$field['name'] = "{$this->field['name']}_{$field['name']}_{$index}";
				$class_name    = UIBase::get_field_class( $field );
				if ( class_exists( $class_name ) ) {
					$input = new $class_name( $this->object, $field );
					$input->row();
				}
			}
			?>
			<div style="clear: left;"></div>
			<a href="#" class="tscf__button tscf__button--delete"><i class="dashicons dashicons-dismiss"></i></a>
			<a href="#" class="tscf__button tscf__button--move"><i class="dashicons dashicons-sort"></i></a>

		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Get registered keys
	 *
	 * @return array
	 */
	public function get_field_indexes() {
		global $wpdb;
		$key = $this->field['name'] . '\_%';

		switch ( get_class( $this->object ) ) {
			case 'WP_Post':
				$object_key = 'post_id';
				$object_id  = $this->object->ID;
				$table      = $wpdb->postmeta;
				break;
			case 'WP_Term':
				$object_key = 'term_id';
				$object_id  = $this->object->ID;
				$table      = $wpdb->termmeta;
				break;
			default:
				return [ ];
				break;
		}
		$query   = <<<SQL
			SELECT meta_key FROM {$table}
			WHERE {$object_key} = %d
			  AND meta_key LIKE %s
SQL;
		$keys    = $wpdb->get_col( $wpdb->prepare( $query, $object_id, $key ) );
		$indexes = [];
		foreach ( $keys as $k ) {
			if ( preg_match( '#_([0-9]+)$#u', $k, $matches ) ) {
				if ( false === array_search( $matches[1], $indexes ) ) {
					$indexes[] = $matches[1];
				}
			}
		}
		sort( $indexes );
		return $indexes;
	}

	/**
	 * Delete all registered field and save them.
	 *
	 * @return int
	 */
	public function save_data() {
		// Delete all data
		$this->delete_all_field();
		// Clear cache
		if ( is_a( $this->object, 'WP_Post' ) ) {
			clean_post_cache( $this->object );
		} elseif ( is_a( $this->object, 'WP_Term' ) ) {
			clean_term_cache( $this->object->term_id, $this->object->taxonomy );
		}
		// Save it all
		$saved = 0;
		$length = $this->input->post( "_index_of_{$this->field['name']}" );
		for ( $index = 1; $index <= $length; $index ++ ) {
			foreach ( $this->field['fields'] as $field ) {
				$field['name'] = "{$this->field['name']}_{$field['name']}_{$index}";
				$class_name    = UIBase::get_field_class( $field );
				if ( class_exists( $class_name ) ) {
					$input = new $class_name( $this->object, $field );
					$save = $input->save_data();
					$saved += $save;
				}
			}
		}
		return $saved;
	}

	/**
	 * Delete all related table.
	 *
	 * @return false|int|void
	 */
	protected function delete_all_field() {
		global $wpdb;
		if ( is_a( $this->object, 'WP_Post' ) ) {
			$table   = $wpdb->postmeta;
			$id_name = 'post_id';
			$id      = $this->object->ID;
		} elseif ( is_a( $this->object, 'WP_Term' ) ) {
			$table   = $wpdb->termmeta;
			$id_name = 'term_id';
			$id      = $this->object->term_id;
		} else {
			// Do nothing.
			return;
		}
		$query = <<<SQL
			DELETE FROM {$table}
			WHERE meta_key LIKE %s
			  AND {$id_name} = %d
SQL;

		return $wpdb->query( $wpdb->prepare( $query, "{$this->field['name']}\_%", $id ) );

	}
}
