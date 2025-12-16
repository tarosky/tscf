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
			'version' => 'Version',
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
 * Recursively build iterator tree (supports nested iterator levels)
 *
 * メタキー:
 * group_child_1_grandchild_2_field_3
 * のようなパターンを
 * [ 1 => [ 'child' => [ 2 => [ 'grandchild' => [ 3 => [ 'field' => value ] ] ] ] ] ]
 * のような入れ子配列に変換する
 *
 * @param string              $group Group (iterator root) meta key prefix.
 * @param null|int|WP_Post    $post  Post object.
 * @return array
 */
function tscf_iterator( $group, $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return [];
	}

	$tree = [];
	$meta = get_post_custom( $post->ID );

	foreach ( $meta as $key => $values ) {
		if ( 0 !== strpos( $key, $group . '_' ) ) {
			continue;
		}

		$value  = current( $values );
		$suffix = substr( $key, strlen( $group ) + 1 );
		if ( '' === $suffix ) {
			continue;
		}

		$parts = explode( '_', $suffix );
		$count = count( $parts );
		if ( $count < 2 ) {
			continue;
		}

		$cursor =& $tree;

		for ( $i = 0; $i < $count; $i += 2 ) {
			if ( ! isset( $parts[ $i + 1 ] ) ) {
				break;
			}
			$name  = $parts[ $i ];
			$index = (int) $parts[ $i + 1 ];

			if ( ! isset( $cursor[ $index ] ) || ! is_array( $cursor[ $index ] ) ) {
				$cursor[ $index ] = [];
			}

			// 最後のペアであれば値を代入して終了
			if ( $i + 2 >= $count ) {
				$cursor[ $index ][ $name ] = $value;
				break;
			}

			if ( ! isset( $cursor[ $index ][ $name ] ) || ! is_array( $cursor[ $index ][ $name ] ) ) {
				$cursor[ $index ][ $name ] = [];
			}

			$cursor =& $cursor[ $index ][ $name ];
		}

		unset( $cursor );
	}

	return tscf_iterator_sort_recursive( $tree );
}

/**
 * Recursively sort iterator by numeric index keys.
 * iterator の配列を数字キー順でソートする
 *
 * @param mixed $node Node
 *
 * @return mixed
 */
function tscf_iterator_sort_recursive( $node ) {
	// 配列であれば再帰
	if ( ! is_array( $node ) ) {
		return $node;
	}

	$keys       = array_keys( $node );
	$numeric    = array_filter(
		$keys,
		function( $k ) {
			return is_int( $k ) || ctype_digit( (string) $k );
		}
	);
	$is_numeric = count( $numeric ) === count( $keys );

	if ( $is_numeric ) {
		ksort( $node );
	}

	foreach ( $node as $k => $v ) {
		if ( is_array( $v ) ) {
			$node[ $k ] = tscf_iterator_sort_recursive( $v );
		}
	}

	return $node;
}

/**
 * Get images
 *
 * @package tscf
 * @param string           $key  Post meta key.
 * @param null|int|WP_Post $post Post object.
 *
 * @return array
 */
function tscf_images( $key, $post = null ) {
	$post      = get_post( $post );
	$image_ids = array_filter( array_map( 'trim', explode( ',', get_post_meta( $post->ID, $key, true ) ) ) );
	if ( ! $image_ids )  {
		return [];
	}
	return get_posts( [
		'post_type'      => 'attachment',
		'post__in'       => $image_ids,
		'orderby'        => 'post__in',
		'posts_per_page' => - 1,
	] );
}

/**
 * Get post status label.
 *
 * @package tscf
 * @since   1.0.3
 * @param null|int|WP_Post $post Post object.
 * @return string
 */
function tscf_post_status( $post = null ) {
    $status = get_post_status( $post );
    if ( ! $status ) {
        return '';
    }
    $status_obj = get_post_status_object( $status );
    return $status_obj ? $status_obj->label : '';
}

/**
 * Get referencing posts
 *
 * @param string           $key
 * @param array            $args
 * @param null|int|WP_Post $post
 * @return array
 */
function tscf_referencing_posts( $key, $args = [], $post = null ) {
    global $wpdb;
    $post = get_post( $post );
    $query = <<<SQL
      SELECT DISTINCT post_id FROM {$wpdb->postmeta}
      WHERE meta_key = %s
        AND FIND_IN_SET(%s, meta_value)
SQL;
    $post_ids = $wpdb->get_col( $wpdb->prepare( $query, $key, $post->ID ) );
    if ( ! $post_ids ) {
        return [];
    }
	$args = array_merge( [
		'post_type' => 'any',
		'post__in' => $post_ids,
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'suppress_filters' => false,
	], $args );
	return get_posts( $args );
}
