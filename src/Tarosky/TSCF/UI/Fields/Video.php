<?php

namespace Tarosky\TSCF\UI\Fields;


class Video extends Hidden {

	/**
	 * @var array Default values
	 */
	protected $default = [
		'limit' => 1,
	];

	/**
	 * Display field with video selector
	 */
	protected function display_field() {
		parent::display_field();
		wp_enqueue_media();
		$media_ids = array_filter( explode( ',', $this->get_data( false ) ), function( $id ) {
			return is_numeric( $id );
		} );
		?>
		<div class="tscf__placeholder" data-limit="<?php echo esc_attr( $this->field['limit'] ); ?>">
			<?php foreach ( $media_ids as $media_id ) : ?>
				<div class="tscf__video">
					<div class="tscf__video">
						<video data-video-id="<?php echo $media_id; ?>" class="tscf__video--object" src="<?php echo wp_get_attachment_url( $media_id ); ?>"></video>
					</div>
					<a class="button tscf__video--delete" href="#"><?php esc_html_e( 'Delete', 'tscf' ); ?></a>
				</div>
			<?php endforeach; ?>
			<div class="tscf__placeholder--limit"><?php sprintf( __( 'You can select %s.', 'tscf' ), sprintf( _n( '%d video', '%d videos', $this->field['limit'], 'tscf' ), $this->field['limit'] ) ); ?></div>
		</div>
		<a class="button tscf__video--add" href="#"><?php esc_html_e( 'Select or Upload', 'tscf' ); ?></a>
		<?php
	}


}
