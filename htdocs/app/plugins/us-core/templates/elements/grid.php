<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_grid
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the element config.
 *
 */
if ( apply_filters( 'us_stop_grid_execution', FALSE ) ) {
	return;
}

global $us_grid_loop_running, $us_grid_no_items_message;

// If we are running US Grid loop already, return nothing
if ( isset( $us_grid_loop_running ) AND $us_grid_loop_running ) {
	return;
}
// DEV NOTE: always change $us_grid_loop_running to FALSE if you interrupt this file execution via return
$us_grid_loop_running = TRUE;

// Set it outside the condition to take a corresponding message
$us_grid_no_items_message = $no_items_message;

if ( ! function_exists( 'us_grid_stop_loop' ) ) {
	function us_grid_stop_loop( $show_message = TRUE ) {
		global $us_grid_loop_running, $us_grid_no_items_message;
		$us_grid_loop_running = FALSE;
		if ( $show_message AND ! empty( $us_grid_no_items_message ) ) {
			echo '<h4 class="w-grid-none">' . strip_tags( $us_grid_no_items_message, '<br><strong>' ) . '</h4>';
		}
	}
}
$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';

// Grid indexes for CSS, start from 1
global $us_grid_index;
$us_grid_index = isset( $us_grid_index ) ? ( $us_grid_index + 1 ) : 1;

// Get the page we are on for AJAX calls
global $us_page_block_ids;
if ( ! empty( $us_page_block_ids ) ) {
	$post_id = $us_page_block_ids[0];
} else {
	$post_id = get_the_ID();
}
if ( ! is_archive() ) {
	$current_post_id = get_the_ID();
} else {
	$current_post_id = $post_id;
}

global $us_is_menu_page_block;
$is_menu = ( isset( $us_is_menu_page_block ) AND $us_is_menu_page_block ) ? TRUE : FALSE;

// Grid indexes for ajax, start from 1
if ( $shortcode_base != 'us_carousel' AND ! $is_menu ) {
	global $us_grid_ajax_indexes;
	$us_grid_ajax_indexes[ $post_id ] = isset( $us_grid_ajax_indexes[ $post_id ] ) ? ( $us_grid_ajax_indexes[ $post_id ] + 1 ) : 1;
} else {
	$us_grid_ajax_indexes = NULL;
}

// Preparing the query
$query_args = $filter_taxonomies = array();
$filter_taxonomy_name = $filter_default_taxonomies = '';
$terms = FALSE; // init this as array in terms case

// Items per page
if ( $items_quantity < 1 ) {
	$items_quantity = 999;
}

/*
 * THINGS TO OUTPUT
 */

// Singulars
if ( in_array( $post_type, array_keys( us_grid_available_post_types( TRUE ) ) ) ) {
	$query_args['post_type'] = explode( ',', $post_type );

	// Posts from selected taxonomies
	$known_post_type_taxonomies = us_grid_available_taxonomies();
	if ( ! empty( $known_post_type_taxonomies[ $post_type ] ) ) {
		foreach ( $known_post_type_taxonomies[ $post_type ] as $taxonomy ) {
			$_taxonomy = str_replace( '-', '_', $taxonomy );
			if ( ! empty( ${'taxonomy_' . $_taxonomy} ) ) {
				if ( ! isset( $query_args['tax_query'] ) ) {
					$query_args['tax_query'] = array();
				}
				$query_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => explode( ',', ${'taxonomy_' . $_taxonomy} ),
				);
			}
		}
	}

	// Media attachments should have some differ arguments
	if ( $post_type == 'attachment' ) {
		if ( ! empty( $images ) ) {
			$ids = explode( ',', $images );
			$query_args['post__in'] = $ids;
		} else {
			$attached_images = get_attached_media( 'image', $current_post_id );
			if ( ! empty( $attached_images ) ) {
				foreach ( $attached_images as $attached_image ) {
					$query_args['post__in'][] = $attached_image->ID;
				}
			}
		}
		$query_args['post_status'] = 'inherit';
		$query_args['post_mime_type'] = 'image';
	} else {
		// Proper post statuses
		$query_args['post_status'] = array( 'publish' => 'publish' );
		$query_args['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
		// Add private states if user is capable to view them
		if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
			$query_args['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
		}
		$query_args['post_status'] = array_values( $query_args['post_status'] );
	}

	// Data for filter
	if ( ! empty( ${'filter_' . $post_type} ) ) {
		$filter_taxonomy_name = ${'filter_' . $post_type};
		$terms_args = array(
			'hierarchical' => FALSE,
			'taxonomy' => $filter_taxonomy_name,
			'number' => 100,
		);
		if ( ! empty( ${'taxonomy_' . $filter_taxonomy_name} ) ) {
			$terms_args['slug'] = explode( ',', ${'taxonomy_' . $filter_taxonomy_name} );
			if ( is_user_logged_in() ) {
				// for logged in users, need to show private posts
				$terms_args['hide_empty'] = FALSE;
			}
			$filter_default_taxonomies = ${'taxonomy_' . $filter_taxonomy_name};
		}
		$filter_taxonomies = get_terms( $terms_args );
		if ( is_user_logged_in() ) {
			// show private posts, but exclude empty posts
			foreach ( $filter_taxonomies as $key => $filter_term ) {
				if ( is_object( $filter_term ) AND $filter_term->count == 0 ) {
					$the_query = new WP_Query(
						array(
							'tax_query' => array(
								array(
									'taxonomy' => $filter_term->taxonomy,
									'field' => 'slug',
									'terms' => $filter_term->slug,
								),
							),
						)
					);
					if ( ! ( $the_query->have_posts() ) ) {
						// unset empty terms
						unset ( $filter_taxonomies[ $key ] );
					}
				}
			}
		}
		if (
			isset( $filter_show_all )
			AND ! $filter_show_all
			AND ! empty( $filter_taxonomies[0] )
			AND $filter_taxonomies[0] instanceof WP_Term
		) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $filter_taxonomy_name,
					'field' => 'slug',
					'terms' => $filter_taxonomies[0]->slug,
				),
			);
		}
	}

	// Specific items by IDs
} elseif ( $post_type == 'ids' ) {
	if ( empty( $ids ) ) {
		us_grid_stop_loop();

		return;
	}

	$ids = explode( ',', $ids );
	$query_args['ignore_sticky_posts'] = 1;
	$query_args['post_type'] = 'any';
	$query_args['post__in'] = array_map( 'trim', $ids );

	// Items with the same taxonomy of current post
} elseif ( $post_type == 'related' ) {
	if ( ! is_singular() OR empty( $related_taxonomy ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$query_args['ignore_sticky_posts'] = 1;
	$query_args['post_type'] = 'any';
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => $related_taxonomy,
			'terms' => wp_get_object_terms( $current_post_id, $related_taxonomy, array( 'fields' => 'ids' ) ),
		),
	);

	// Product upsells (WooCommerce only)
} elseif ( $post_type == 'product_upsells' ) {
	if ( ! is_singular( 'product' ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$upsell_ids = get_post_meta( $current_post_id, '_upsell_ids', TRUE );
	if ( empty( $upsell_ids ) ) {
		// We will pass a negative number to reject random goods
		$upsell_ids = array( - 1 );
	}
	$query_args['post_type'] = array( 'product', 'product_variation' );
	$query_args['post__in'] = (array) $upsell_ids;

	// Product cross-sells (WooCommerce only)
} elseif ( $post_type == 'product_crosssell' ) {
	if ( ! is_singular( 'product' ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$crosssell_ids = get_post_meta( $current_post_id, '_crosssell_ids', TRUE );
	if ( empty( $crosssell_ids ) ) {
		// We will pass a negative number to reject random goods
		$crosssell_ids = array( - 1 );
	}
	$query_args['post_type'] = array( 'product', 'product_variation' );
	$query_args['post__in'] = (array) $crosssell_ids;

	// Child posts of current
} elseif ( $post_type == 'current_child_pages' ) {
	$query_args['post_parent'] = $current_post_id;
	$query_args['post_type'] = 'any';
	$query_args['ignore_sticky_posts'] = 1;

	// Terms of selected (or current) taxonomy
} elseif ( in_array( $post_type, array( 'taxonomy_terms', 'current_child_terms', 'ids_terms' ) ) ) {
	$current_term_id = $parent = 0;
	$hide_empty = TRUE;
	if ( strpos( $terms_include, 'children' ) !== FALSE ) {
		$parent = '';
	}
	if ( strpos( $terms_include, 'empty' ) !== FALSE ) {
		$hide_empty = FALSE;
	}

	// If the current page is taxonomy page, we will output its children terms only
	if ( $post_type == 'current_child_terms' ) {
		if ( ! is_tag() AND ! is_category() AND ! is_tax() ) {
			us_grid_stop_loop( FALSE );

			return;
		}
		$current_term = get_queried_object();
		$related_taxonomy = $current_term->taxonomy;
		if ( strpos( $terms_include, 'children' ) !== FALSE ) {
			$current_term_id = $current_term->term_id;
		} else {
			$parent = $current_term->term_id;
		}
	}

	if ( $terms_orderby != 'rand' ) {
		$terms_args_query = array(
			'taxonomy' => $related_taxonomy,
			'orderby' => $terms_orderby,
			'order' => ( $terms_orderby == 'count' ) ? 'DESC' : 'ASC',
			'number' => $items_quantity,
			'hide_empty' => $hide_empty,
			'child_of' => $current_term_id,
			'parent' => $parent,
		);

		//  Manually selected terms
		if ( $post_type == 'ids_terms' ) {
			if ( empty( $ids_terms ) ) {
				us_grid_stop_loop();

				return;
			} else {
				if ( $terms_orderby == 'menu_order' ) {
					$terms_orderby = 'include';
				}
				$terms_args_query = array(
					'orderby' => $terms_orderby,
					'order' => ( $terms_orderby == 'count' ) ? 'DESC' : 'ASC',
					'number' => $items_quantity,
					'include' => array_map( 'trim', explode( ',', $ids_terms ) ),
				);
			}
		}
		$terms_raw = get_terms( $terms_args_query );
	} else {
		global $wpdb;
		$terms_query_where = '';
		if ( $post_type == 'ids_terms' ) {
			if ( empty( $ids_terms ) ) {
				us_grid_stop_loop();

				return;
			} else {
				$ids_terms = array_map( 'intval', explode( ',', $ids_terms ) );
				$terms_query_where .= ' AND t.term_id IN(' . implode( ',', $ids_terms ) . ')';
			}
		}
		if ( $hide_empty ) {
			$terms_query_where .= ' AND tt.count > 0';
		}
		if ( $parent !== '' ) {
			$terms_query_where .= ' AND tt.parent = ' . intval( $parent );
		}
		$terms_query = "
			SELECT
				t.*, tt.*
			FROM {$wpdb->terms} AS t
			INNER JOIN {$wpdb->term_taxonomy} AS tt
				ON t.term_id = tt.term_id
			WHERE
				tt.taxonomy = %s
				 $terms_query_where
			ORDER BY RAND()
			LIMIT %d
		";
		$terms_query = $wpdb->prepare( $terms_query, $related_taxonomy, $items_quantity );
		$terms_raw = $wpdb->get_results( $terms_query );
	}

	$terms = array();

	// When taxonomy doesn't exist, it returns WP_Error object, so we need to use empty array for further work
	if ( ! is_wp_error( $terms_raw ) ) {

		$ids_terms_map = ( $post_type == 'ids_terms' AND ! empty( $ids_terms ) )
			? array_flip( array_map( 'trim', explode( ',', $ids_terms ) ) )
			: array();

		$available_taxonomy = us_get_taxonomies( TRUE, FALSE );
		foreach ( $terms_raw as $key => $term_item ) {
			// if taxonomy of this term is not available, remove it
			if ( is_object( $term_item ) ) {
				if ( in_array( $term_item->taxonomy, array_keys( $available_taxonomy ) ) ) {
					if ( isset( $ids_terms_map[ $term_item->term_id ] ) ) {
						$terms[ $ids_terms_map[ $term_item->term_id ] ] = $term_item;
					} else {
						$terms[] = $term_item;
					}
				}
			}
		}

		// Apply sorting if it is not by title (name)
		if ( $terms_orderby !== 'name' ) {
			ksort( $terms );
		}
	}

	// Generate query for "Gallery" and "Post Object" types from ACF PRO plugin
} elseif ( strpos( $post_type, 'acf_' ) !== FALSE ) {
	if ( ! is_singular() ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	// ACF Galleries
	if ( strpos( $post_type, 'acf_gallery_' ) !== FALSE ) {
		$key = str_replace( 'acf_gallery_', '', $post_type );

		$query_args['post_type'] = 'attachment';
		$query_args['post_status'] = 'inherit';
		$query_args['post__in'] = get_post_meta( $current_post_id, $key, TRUE );

		// Don't show the Grid, if ACF Gallery has no images
		if ( empty( $query_args['post__in'] ) ) {
			us_grid_stop_loop();

			return;
		}
	}

	// ACF Post objects
	if ( strpos( $post_type, 'acf_posts_' ) !== FALSE ) {
		$key = str_replace( 'acf_posts_', '', $post_type );
		$ids = get_post_meta( $current_post_id, $key, TRUE );

		$query_args['post_type'] = 'any';
		$query_args['ignore_sticky_posts'] = 1;
		$query_args['post__in'] = is_array( $ids ) ? $ids : array( $ids );
	}

}

// Always exclude the current post from the query
if ( is_singular() ) {
	$query_args['post__not_in'] = array( $current_post_id );
}

// Exclude sticky posts
if ( ! empty( $ignore_sticky ) ) {
	$query_args['ignore_sticky_posts'] = 1;
}

// Set Orderby and Order arguments
$order = ( $order_invert ) ? 'ASC' : 'DESC';
switch ( $orderby ) {
	case 'date':
		$query_args['orderby'] = array( 'date' => $order );
		break;
	case 'modified':
		// When sorting by modified date adding creation date in case of bulk post updating
		// First item in orderby array is main param to order by
		$query_args['orderby'] = array( 'modified' => $order, 'date' => $order, );
		break;
	case 'alpha':
		$query_args['orderby'] = array( 'title' => ( $order_invert ) ? 'DESC' : 'ASC' );
		break;
	case 'post__in':
		$query_args['orderby'] = array( 'post__in' => ( $order_invert ) ? 'DESC' : 'ASC' );
		break;
	case 'menu_order':
		// Sort posts order for ids
		if ( $post_type === 'ids' AND ! empty( $query_args[ 'post__in' ] ) ) {
			$query_args[ 'orderby' ] = 'post__in';
		} else {
			$query_args['orderby'] = array( 'menu_order' => ( $order_invert ) ? 'DESC' : 'ASC' );
		}
		break;
	case 'rand':
		$query_args['orderby'] = 'RAND(' . rand() . ')';
		break;
	case 'custom':
		if ( $orderby_custom_type ) {
			$query_args['orderby'] = 'meta_value_num';
		} else {
			$query_args['orderby'] = 'meta_value';
		}
		$query_args['meta_key'] = $orderby_custom_field;
		$query_args['order'] = $order;
		break;
	case 'price':
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_key'] = '_price';
		$query_args['order'] = $order;
		break;
	case 'popularity':
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_key'] = 'total_sales';
		$query_args['order'] = $order;
		break;
	case 'rating':
		$query_args['orderby'] = 'meta_value_num';
		$query_args['meta_key'] = '_wc_average_rating';
		$query_args['order'] = $order;
		break;
	case 'post_views_counter':
	case 'post_views_counter_day':
	case 'post_views_counter_week':
	case 'post_views_counter_month':
		if ( class_exists( 'Post_Views_Counter' ) ) {
			$query_args = array_merge(
				$query_args, array(
					// required by PVC
					'suppress_filters' => FALSE,
					'orderby' => 'post_views',
					'fields' => '',
					'views_query' => array(
						'hide_empty' => FALSE,
					),
				)
			);
		} else {
			$query_args['orderby'] = array( $orderby => $order );
		}
		break;
	default:
		$query_args['orderby'] = array( $orderby => $order );
}

// Order by views per month, week, day
if (
	class_exists( 'Post_Views_Counter' )
	AND in_array( $orderby, array( 'post_views_counter_day', 'post_views_counter_week', 'post_views_counter_month' ) )
) {
	$views_query = array(
		'year' => date( 'Y' ),
		'month' => date( 'm' ),
		'week' => date( 'W' ),
		'day' => date( 'd' ),
	);
	switch ( $orderby ) {

		// Views for last day
		case 'post_views_counter_day':
			unset( $views_query['week'] );
			break;

		// Views for last week
		case 'post_views_counter_week':
			unset( $views_query['day'] );
			break;

		// Views for last month
		case 'post_views_counter_month':
			unset( $views_query['day'], $views_query['week'] );
			break;
	}
	$query_args['views_query'] = array_merge( $query_args['views_query'], $views_query );

	unset( $views_query );
}

// Pagination
if ( $pagination == 'regular' ) {
	$request_paged = is_front_page() ? 'page' : 'paged';
	if ( get_query_var( $request_paged ) ) {
		$query_args['paged'] = get_query_var( $request_paged );
	}
}

// Extra arguments for WooCommerce products
if ( class_exists( 'woocommerce' ) AND in_array(
		$post_type, array(
			'product',
			'product_upsells',
			'product_crosssell',
		)
	) ) {
	$query_args['meta_query'] = array();

	// Exclude out of stock products
	if ( $exclude_items == 'out_of_stock' ) {
		$query_args['meta_query'][] = array(
			'key' => '_stock_status',
			'value' => 'outofstock',
			'compare' => '!=',
		);
	}

	// Show Sale products
	if ( strpos( $products_include, 'sale' ) !== FALSE ) {
		if ( ! empty( wc_get_product_ids_on_sale() ) ) {
			$query_args['post__in'] = wc_get_product_ids_on_sale();
		} else {
			us_grid_stop_loop();

			return;
		}

	}

	// Show Featured products
	if ( strpos( $products_include, 'featured' ) !== FALSE ) {
		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field' => 'name',
			'terms' => 'featured',
			'operator' => 'IN',
		);
	}
}

// Exclude posts of previous grids on the same page
if ( $exclude_items == 'prev' ) {
	global $us_grid_skip_ids;
	if ( ! empty( $us_grid_skip_ids ) AND is_array( $us_grid_skip_ids ) ) {
		if ( empty( $query_args['post__not_in'] ) OR ! is_array( $query_args['post__not_in'] ) ) {
			$query_args['post__not_in'] = array();
		}
		$query_args['post__not_in'] = array_merge( $query_args['post__not_in'], $us_grid_skip_ids );
	}
}

$query_args['posts_per_page'] = $items_quantity;


// Reset query for using on archives
if ( $post_type == 'current_query' ) {
	if ( is_tax( 'tribe_events_cat' ) OR is_post_type_archive( 'tribe_events' ) ) {
		$the_content = apply_filters( 'the_content', get_the_content() );

		// The page may be paginated itself via <!--nextpage--> tags
		$the_pagination = us_wp_link_pages();

		echo $the_content . $the_pagination;
		us_grid_stop_loop( FALSE );

		return;
	} elseif ( is_archive() OR is_search() OR is_home() ) {
		$query_args = NULL;
	} else {
		us_grid_stop_loop( FALSE );

		return;
	}
}

// Load Grid Listing template with given params
$template_vars = array(
	'_us_grid_post_type' => $post_type,
	'classes' => $classes,
	'filter_default_taxonomies' => $filter_default_taxonomies,
	'filter_taxonomies' => $filter_taxonomies,
	'filter_taxonomy_name' => $filter_taxonomy_name,
	'post_id' => $post_id,
	'terms' => $terms,
	'us_grid_ajax_indexes' => $us_grid_ajax_indexes,
	'us_grid_index' => $us_grid_index,
);

// Apply Grid Filter params
global $us_context_layout;
if (
	! is_archive() // For archives, the us_inject_grid_filter_to_archive_page() function will be used
	AND $post_type != 'current_query'
	AND $us_context_layout === 'main'
	AND empty( $filter_post )
) {
	// Use for all but archive pages
	us_apply_grid_filters( $post_id, $query_args );
}

$template_vars['query_args'] = $query_args;

// Add default values for unset variables from Grid config
$default_grid_params = us_shortcode_atts( array(), 'us_grid' );
foreach ( $default_grid_params as $param => $value ) {
	$template_vars[ $param ] = isset( $$param ) ? $$param : $value;
}

// Add default values for unset variables from Carousel config
if ( $shortcode_base == 'us_carousel' ) {
	$default_carousel_params = us_shortcode_atts( array(), 'us_carousel' );
	foreach ( $default_carousel_params as $param => $value ) {
		$template_vars[ $param ] = isset( $$param ) ? $$param : $value;
	}
	$template_vars['type'] = 'carousel'; // force 'carousel' type for us_carousel shortcode
}

us_load_template( 'templates/us_grid/listing', $template_vars );

$us_grid_loop_running = FALSE;
