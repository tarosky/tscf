<?php
/**
 * Utility functions
 *
 * @package tscf
 */

/**
 * Get plugin version
 *
 * @since 1.0.0
 * @return string
 */
function tscf_version() {
	static $info = null;
	if ( is_null( $info ) ) {
		$info = get_file_data( __DIR__ . '/tscf.php', [
			'version' => 'Version'
		] );
	}
	return $info['version'];
}


function tscf( $key, $object ) {

}

/**
 * Get meta for post
 *
 * @param string $key
 * @param null|int|WP_Post $post
 *
 * @return mixed
 */
function tscfp( $key, $post = null ) {
	$post = get_post( $post );
	return $post ? get_post_meta( $post->ID, $key, true ) : '';
}

function tscft( $key, $term = null ) {

}

function tscfc( $key, $comment = null ) {

}

/**
 * Get post list
 *
 * @param string           $key
 * @param null|int|WP_post $post
 *
 * @return array
 */
function tscf_post_list( $key, $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return [];
	}
	$post_ids = array_filter( explode( ',', get_post_meta( $post->ID, $key, true ) ) );
	if ( empty( $post_ids ) ) {
		return [];
	}
	$args = [
		'post_type' => 'any',
		'post__in' => $post_ids,
	    'post_status' => 'publish',
	    'suppress_filters' => false,
	    'orderby' => 'post__in',
	];
	/**
	 * tscf_post_list_args
	 *
	 * @package tscf
	 * @since 1.0.0
	 * @param array   $args
	 * @param string  $key
	 * @param WP_Post $post
	 */
	$args = apply_filters( 'tscf_post_list_args', $args, $key, $post );
	return get_posts( $args );
}

/**
 * Return group as array
 *
 * @param string $group
 * @param null|int|WP_Post $post
 *
 * @return array
 */
function tscf_repeat_field( $group, $post = null ) {
	$post = get_post( $post );
	$atts = [];
	foreach ( get_post_custom( $post->ID ) as $key => $values ) {
		if ( preg_match( "#{$group}_(.*)_([0-9]+)#u", $key, $matches ) ) {
			if ( ! isset( $atts[ $matches[2] ] ) ) {
				$atts[ $matches[2] ] = [];
			}
			$atts[ $matches[2] ][ $matches[1] ] = current( $values );
		}
	}
	ksort( $atts );

	return $atts;
}

/**
 * Get images
 *
 * @param string $key
 * @param null|int|WP_Post $post
 *
 * @return array
 */
function tscf_images( $key, $post = null ) {
	$post      = get_post( $post );
	$image_ids = array_filter( array_map( 'trim', explode( ',', get_post_meta( $post->ID, $key, true ) ) ) );

	return get_posts( [
		'post_type'      => 'attachment',
		'post__in'       => $image_ids,
		'orderby'        => 'post__in',
		'posts_per_page' => - 1,
	] );
}
