<?php

namespace Tarosky\TSCF;


use Tarosky\TSCF\Pattern\Singleton;

class Rest extends Singleton {

	const NONCE_NAME = 'tscf_nonce';

	protected function on_construct() {
		add_action( 'rest_api_init', [$this, 'rest_api_init'] );
	}

	public function rest_api_init() {
		register_rest_route( 'tscf/v1', '/posts', [
			[
				'method' => 'GET',
			    'permission_callback' => function() {
					return current_user_can( 'edit_posts' );
			    },
			    'args' => [
			    	'q' => [
			    		'validate_callback' => function($var) {
							return ! empty( $var );
					    },
				    ],
			        'post_type' => [
			        	'validate_callback' => function($var) {
			        		foreach ( explode( ',', $var ) as $post_type ) {
			        			if ( 'any' == $post_type ) {
			        				continue;
			        			} else {
			        				if ( ! post_type_exists( $post_type ) ) {
			        					return false;
			        				}
			        			}
			        		}
							return true;
				        },
			        ],
			    ],
			    'callback' => [ $this, 'handle_post' ],
			]
		] );
	}

	/**
	 * Handle search request
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function handle_post( $request ) {
		/**
		 * tscf_post_search_on_rest
		 *
		 * @param bool $user_can
		 * @param \WP_REST_Request $request
		 * @return bool
		 */
		$user_can = apply_filters( 'tscf_post_search_on_rest', true, $request );
		if ( ! $user_can ) {
			return new \WP_Error( 'no_permission', __( 'No post found.', 'tscf' ), [ 'response' => 403 ] );
		}
		$query = new \WP_Query( [
			'post_status' => 'any',
			'post_type' => explode( ',', $request['post_type'] ),
		    's' => trim( str_replace( '+', ' ', $request['q'] ) ),
		    'posts_per_page' => 10,
		] );
		if ( ! $query->have_posts() ) {
			return new \WP_Error( 'no_posts', __( 'No post found.', 'tscf' ), [ 'response' => 404 ] );
		}
		return new \WP_REST_Response( [
			'posts' => array_map( function( $post ) use ( $request ) {
				$text = sprintf( '%1$s(%2$s)', get_the_title( $post ), tscf_post_status( $post ) );
				/**
				 * tscf_post_search_response_text
				 *
				 * @param string $text
				 * @param null|int|WP_post $post
				 * @param \WP_REST_Request $request
				 * @return string
				 */
				$text = apply_filters( 'tscf_post_search_response_text', $text, $post, $request );
				return [
					'id' => (int) $post->ID,
					'text' => $text,
				];
			}, $query->posts ),
		    'total' => $query->found_posts,
		] );
	}
}
