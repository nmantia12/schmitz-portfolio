<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for grids ajax pagination.
 */
add_action( 'wp_ajax_nopriv_us_ajax_grid', 'us_ajax_grid' );
add_action( 'wp_ajax_us_ajax_grid', 'us_ajax_grid' );
function us_ajax_grid() {

	if ( class_exists( 'WPBMap' ) AND method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
		WPBMap::addAllMappedShortcodes();
	}

	// Filtering $template_vars, as is will be extracted to the template as local variables
	$template_vars = shortcode_atts(
		array(
			'query_args' => array(),
			'post_id' => FALSE,
			'us_grid_index' => FALSE,
			'us_grid_ajax_index' => FALSE,
			'_us_grid_post_type' => NULL,
			'exclude_items' => 'none',
			'items_offset' => 0,
			'items_layout' => 'blog_1',
			'type' => 'grid',
			'columns' => 2,
			'img_size' => 'default',
			'lang' => FALSE,
			'overriding_link' => 'none',
			'pagination' => 'regular',
			'us_grid_filter_params' => NULL,
			'filters_query_args' => NULL,
		), us_maybe_get_post_json( 'template_vars' )
	);

	// Retrieving query arguments to get the quantity for each parameter
	$filters_query_args = ! empty( $template_vars[ 'filters_query_args' ] )
		? $template_vars[ 'filters_query_args' ]
		: array();
	unset( $template_vars[ 'filters_query_args' ] );

	if ( class_exists( 'SitePress' ) AND $template_vars['lang'] ) {
		global $sitepress;
		$sitepress->switch_lang( $template_vars['lang'] );
	}

	$post_id = isset( $template_vars['post_id'] )
		? intval( $template_vars['post_id'] )
		: 0;

	if ( $post_id > 0 ) {
		$post = get_post( $post_id );
		if ( empty( $post ) ) {
			wp_send_json_error();
		}

		$us_grid_ajax_index = isset( $template_vars['us_grid_ajax_index'] )
			? intval( $template_vars['us_grid_ajax_index'] )
			: 1;

		// Retrieving the relevant shortcode from the page to get options
		$post_content = $post->post_content;

		// If there is no grid then we will return an error
		preg_match_all( '/\[us_grid((?!\_)([^\]]+)?)/', $post_content, $matches );
		if ( ! isset( $matches[0][ $us_grid_ajax_index - 1 ] ) ) {
			wp_send_json_error();
		}

		// Getting the relevant shortcode options
		$shortcode = $matches[0][ $us_grid_ajax_index - 1 ];

		// For proper shortcode_parse_atts behaviour
		$shortcode = substr_replace( $shortcode, ' ]', - 1 );
		$shortcode_atts = shortcode_parse_atts( $shortcode );

		$shortcode_atts = shortcode_atts(
			array(
				'post_type' => '',
			), $shortcode_atts
		);

		if ( $shortcode_atts['post_type'] == 'current_query' ) {
			$allowed_post_types = NULL;
		} elseif ( in_array( $shortcode_atts['post_type'], array( 'ids', 'related', 'current_child_pages' ) ) ) {
			$allowed_post_types = array( 'any' );
		} elseif ( $shortcode_atts['post_type'] == '' ) {
			$shortcode_atts['post_type'] = 'post';
			$allowed_post_types = array( 'post' );

			// If ACF Gallery field used, set post type to attachment
		} elseif ( strpos( $shortcode_atts['post_type'], 'acf_gallery' ) !== FALSE ) {
			$template_vars['query_args']['post_type'] = array( 'attachment' );
			$allowed_post_types = array( 'attachment' );
		} else {
			$allowed_post_types = array( $shortcode_atts['post_type'] );
		}

		if (
			$shortcode_atts['post_type'] == 'current_query'
			AND isset( $template_vars['query_args']['post_type'] )
			AND (
					(
						is_array( $template_vars['query_args']['post_type'] )
						AND in_array( 'product', $template_vars['query_args']['post_type'] )
					)
					OR $template_vars['query_args']['post_type'] == 'product'
			)
		) {
			$add_wc_hooks = TRUE;
		}
	}

	// Filtering query_args
	if ( isset( $template_vars['query_args'] ) AND is_array( $template_vars['query_args'] ) ) {

		// Query Args keys, that won't be filtered
		$allowed_query_keys = array(

			// Grid listing shortcode requests
			'author_name',
			'us_portfolio_category',
			'us_portfolio_tag',
			'category_name',
			'tax_query',

			// Archive requests
			'year',
			'monthnum',
			'day',
			'cat',
			'tag',
			'product_cat',
			'product_tag',

			// Search requests
			's',

			// Pagination
			'paged',
			'order',
			'orderby',
			'posts_per_page',
			'post__not_in',
			'post__in',
			'post_parent',

			// For excluding 'out of stock' products
			'meta_query',

			// For products sorting
			'order',
			'meta_key',

			// Custom users' queries
			'post_type',
		);

		$taxonomies = us_get_taxonomies( TRUE );
		foreach ( $taxonomies as $taxonomy_name => $taxonomy_title ) {
			if ( ! in_array( $taxonomy_name, $allowed_query_keys ) ) {
				$allowed_query_keys[] = $taxonomy_name;
			}
		}

		// Delete unavailable parameters, only allowed parameters can be applied in the query to the database
		foreach ( $template_vars['query_args'] as $query_key => $query_val ) {
			if ( ! in_array( $query_key, $allowed_query_keys ) ) {
				unset( $template_vars['query_args'][ $query_key ] );
			}
		}

		// Get grid available post types as allowed for ajax
		$allowed_post_types = array_keys( us_grid_available_post_types() );

		// Exclude inaccessible post types for search
		if ( ! empty( $template_vars['query_args'] ) AND isset( $template_vars['query_args']['s'] ) ) {
			$exclude_post_types = us_get_option( 'exclude_post_types_in_search', array() );
			foreach ( $allowed_post_types as $key => $item ) {
				if ( in_array( $item, $exclude_post_types ) ) {
					unset( $allowed_post_types[ $key ] );
				}
			}
		}

		if ( isset( $template_vars['query_args']['post_type'] ) ) {
			$is_allowed_post_type = TRUE;
			if ( is_array( $template_vars['query_args']['post_type'] ) ) {
				foreach ( $template_vars['query_args']['post_type'] as $post_type ) {
					if ( ! in_array( $post_type, $allowed_post_types ) ) {
						$is_allowed_post_type = FALSE;
						break;
					}
				}
			} elseif ( ! in_array( $template_vars['query_args']['post_type'], $allowed_post_types ) ) {
				$is_allowed_post_type = FALSE;
			}

			if ( ! $is_allowed_post_type ) {
				unset( $template_vars['query_args']['post_type'] );
			}
		}
		// For grid related post_type
		if (
			! empty( $template_vars['_us_grid_post_type'] )
			AND in_array( $template_vars['_us_grid_post_type'], array( 'related', 'ids' ) )
		) {
			$template_vars['query_args']['post_type'] = 'any';
		}
		if ( ! isset( $template_vars['query_args']['s'] ) AND ! isset( $template_vars['query_args']['post_type'] ) ) {
			$template_vars['query_args']['post_type'] = 'post';
		}

		// Providing proper post statuses
		if ( ! empty( $post_type ) AND ( $template_vars['query_args']['post_type'] == 'attachment' ) OR ( is_array( $template_vars['query_args']['post_type'] ) AND in_array( 'attachment', $template_vars['query_args']['post_type'] ) AND count( $template_vars['query_args']['post_type'] ) == 1 ) ) {
			$template_vars['query_args']['post_status'] = 'inherit';
			$template_vars['query_args']['post_mime_type'] = 'image';
		} else {
			$template_vars['query_args']['post_status'] = array( 'publish' => 'publish' );
			$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
			// Add private states if user is capable to view them
			if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
				$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
			}
			$template_vars['query_args']['post_status'] = array_values( $template_vars['query_args']['post_status'] );
		}

		// Exclude sticky posts from rand query after 1 page
		if ( isset( $template_vars['query_args']['orderby'] ) AND ! ( is_array( $template_vars['query_args']['orderby'] ) ) ) {
			if ( ( substr( $template_vars['query_args']['orderby'], 0, 4 ) == 'rand' ) AND ( $template_vars['query_args']['paged'] > '1' ) ) {
				$sticky_posts = get_option( 'sticky_posts' );
				$template_vars['query_args']['ignore_sticky_posts'] = TRUE;
				foreach ( $sticky_posts as $post_id ) {
					$template_vars['query_args']['post__not_in'][] = $post_id;
				}
			}
		}

		// Show hide empty for Post views counter
		if (
			isset( $template_vars['query_args']['orderby'] )
			AND $template_vars['query_args']['orderby'] == 'post_views'
			AND class_exists( 'Post_Views_Counter' )
		) {
			$template_vars['query_args']['views_query']['hide_empty'] = FALSE;
		}
	}

	// Grid Filter
	if ( $post_id > 0 AND ! is_null( $template_vars['us_grid_filter_params'] ) ) {
		// Apply parameters received through AJAX
		us_apply_grid_filters( $post_id, $template_vars['query_args'], $template_vars['us_grid_filter_params'] );
	}

	// Passing values that were filtered due to post protocol
	global $us_grid_loop_running;
	$us_grid_loop_running = TRUE;

	// Apply WooCommerce product ordering if set
	if ( ! empty( $add_wc_hooks ) AND class_exists( 'woocommerce' ) AND is_object( wc() ) ) {
		foreach ( array( 'order', 'orderby' ) as $param ) {
			if ( ! isset( $_GET[ $param ] ) AND ! empty( $template_vars[ 'query_args' ][ $param ] ) ) {
				$_GET[ $param ] = (string) $template_vars[ 'query_args' ][ $param ];
			}
		}

		add_action( 'pre_get_posts', array( wc()->query, 'product_query' ) );
	}

	if ( ! function_exists( 'us_pre_get_posts_for_search' ) ) {
		/**
		 * Check order for search
		 *
		 * @param WP_Query $wp_query
		 *
		 * @return void
		 */
		function us_pre_get_posts_for_search( $wp_query ) {
			$query_order = ! empty( $wp_query->query['order'] )
				? $wp_query->query['order']
				: NULL;
			if (
				$search = $wp_query->get( 's' )
				AND $wp_query->get( 'order' ) !== $query_order
			) {
				$wp_query->set( 'order', $query_order );
			}
		}

		add_action( 'pre_get_posts', 'us_pre_get_posts_for_search', 101, 1 );
	}

	if ( ! function_exists( 'us_posts_orderby_for_search' ) ) {
		/**
		 * Verification and preparation of search queries
		 *
		 * @param string $orderby
		 * @param WP_Query $wp_query
		 *
		 * @return string orderby
		 */
		function us_posts_orderby_for_search( $orderby, $wp_query ) {
			global $wpdb;
			// Adjust search query to match from internal wp_query regeneration
			if (
				$wp_query->is_search
				AND $search = $wp_query->get( 's' )
				AND $order = $wp_query->get( 'order' )
			) {
				$order = esc_sql( $order );
				$search = esc_sql( $wpdb->esc_like( $search ) );
				$orderby = "{$wpdb->posts}.post_title LIKE '%{$search}%' {$order}, {$wpdb->posts}.post_date {$order}";

			}

			return $orderby;
		}

		add_filter( 'posts_orderby', 'us_posts_orderby_for_search', 10, 2 );
	}

	us_load_template( 'templates/us_grid/listing', $template_vars );
	$us_grid_loop_running = FALSE;

	// If the parameters were passed from the filter, then recount the number of items
	if ( ! empty( $filters_query_args ) ) {
		foreach ( $filters_query_args as &$items ) {
			foreach ( $items as &$item_query_args ) {
				// Add options from Grid Filter
				if ( ! is_null( $template_vars['us_grid_filter_params'] ) ) {
					us_apply_grid_filters( NULL, $item_query_args, $template_vars['us_grid_filter_params'] );
				}
				$item_query_args = us_grid_filter_get_count_items( $item_query_args );
			}
			unset( $item_query_args );
		}
		unset( $items, $item_query_args );
		echo '<div class="w-grid-filter-json-data hidden"'. us_pass_data_to_js( $filters_query_args ) .'></div>';
	}

	// We don't use JSON to reduce data size
	die;
}
