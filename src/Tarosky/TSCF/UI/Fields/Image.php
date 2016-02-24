<?php

namespace Tarosky\TSCF\UI\Fields;


class Image extends Hidden {

	/**
	 * @var array Default values
	 */
	protected $default = [
		'limit' => 1,
	];

	/**
	 * Display field with image selector
	 */
	protected function display_field() {
		parent::display_field();
		$media_ids = array_filter( explode(',', $this->get_data( false ) ), function( $id ){
			return is_numeric($id);
		} );
		?>
		<div class="tscf__placeholder" data-limit="<?php echo esc_attr( $this->field['limit'] ) ?>">
			<?php foreach( $media_ids as $media_id ) : ?>
				<div class="tscf__image">
					<?php echo wp_get_attachment_image( $media_id, 'thumbnail', false, [
						'data-image-id' => $media_id,
					    'tscf__image--object'
					] ) ?>
					<a class="button tscf__image--delete" href="#"><?php $this->_e('Delete') ?></a>
				</div>
			<?php endforeach; ?>
			<div class="tscf__placeholder--limit"><?php $this->_e('You can select %d images.', $this->field['limit']) ?></div>
		</div>
		<a class="button tscf__image--add" href="#"><?php $this->_e('Select or Upload') ?></a>
		<?php
	}


}
