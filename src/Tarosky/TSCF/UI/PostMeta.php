<?php

namespace Tarosky\TSCF\UI;


class PostMeta extends Base {

	/**
	 * PostMeta constructor.
	 *
	 * @param $object
	 * @param array $fields
	 */
	public function __construct( $object, array $fields ) {
		parent::__construct( $object, $fields );
		if ( is_admin() ) {
			$fields = wp_parse_args( $fields, [
				'context'  => 'advanced',
				'priority' => 'low',
			] );
			add_meta_box(
				$this->name,
				$this->label,
				[ $this, 'render' ],
				$object->post_type,
				$fields['context'],
				$fields['priority']
			);
		}
	}

}
