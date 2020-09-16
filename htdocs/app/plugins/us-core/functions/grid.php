<?php
/**
 * All methods that apply to Grid and Grid Filter
 *
 */

if ( ! function_exists( 'us_grid_query_offset' ) ) {
	/**
	 * Grid function
	 */
	function us_grid_query_offset( &$query ) {
		if ( ! isset( $query->query['_id'] ) OR $query->query['_id'] !== 'us_grid' ) {
			return;
		}

		global $us_grid_items_offset;

		$posts_per_page = ( ! empty( $query->query['posts_per_page'] ) )
			? $query->query['posts_per_page']
			: get_option( 'posts_per_page' );

		if ( $query->is_paged ) {
			$page_offset = $us_grid_items_offset + ( ( $query->query_vars['paged'] - 1 ) * $posts_per_page );

			// Apply adjust page offset
			$query->set( 'offset', $page_offset );

		} else {
			// This is the first page. Just use the offset...
			$query->set( 'offset', $us_grid_items_offset );

		}

		remove_action( 'pre_get_posts', 'us_grid_query_offset' );
	}
}

if ( ! function_exists( 'us_grid_adjust_offset_pagination' ) ) {
	/**
	 * Grid function
	 */
	function us_grid_adjust_offset_pagination( $found_posts, $query ) {
		if ( ! isset( $query->query['_id'] ) OR $query->query['_id'] !== 'us_grid' ) {
			return $found_posts;
		}

		global $us_grid_items_offset;
		remove_filter( 'found_posts', 'us_grid_adjust_offset_pagination' );

		// Reduce WordPress's found_posts count by the offset...
		return $found_posts - $us_grid_items_offset;
	}
}

if ( ! function_exists( 'us_fix_grid_settings' ) ) {
	/**
	 * Make the provided grid settings value consistent and proper
	 *
	 * @param $value array
	 *
	 * @return array
	 */
	function us_fix_grid_settings( $value ) {
		if ( empty( $value ) OR ! is_array( $value ) ) {
			$value = array();
		}
		if ( ! isset( $value['data'] ) OR ! is_array( $value['data'] ) ) {
			$value['data'] = array();
		}

		$options_defaults = array();
		$elements_defaults = array();
		if ( function_exists( 'usof_get_default' ) ) {
			foreach ( us_config( 'grid-settings.options', array() ) as $option_name => $option_group ) {
				foreach ( $option_group as $option_name => $option_field ) {
					$options_defaults[ $option_name ] = usof_get_default( $option_field );
				}
			}

			foreach ( us_config( 'grid-settings.elements', array() ) as $element_name ) {
				$element_settings = us_config( 'elements/' . $element_name );
				$elements_defaults[ $element_name ] = array();
				foreach ( $element_settings['params'] as $param_name => $param_field ) {
					$elements_defaults[ $element_name ][ $param_name ] = usof_get_default( $param_field );
				}
			}
		}

		foreach ( $options_defaults as $option_name => $option_default ) {
			if ( ! isset( $value['default']['options'][ $option_name ] ) ) {
				$value['default']['options'][ $option_name ] = $option_default;
			}
		}
		foreach ( $value['data'] as $element_name => $element_values ) {
			$element_type = strtok( $element_name, ':' );
			if ( ! isset( $elements_defaults[ $element_type ] ) ) {
				continue;
			}
			foreach ( $elements_defaults[ $element_type ] as $param_name => $param_default ) {
				if ( ! isset( $value['data'][ $element_name ][ $param_name ] ) ) {
					$value['data'][ $element_name ][ $param_name ] = $param_default;
				}
			}
		}

		foreach ( array( 'default' ) as $state ) {
			if ( ! isset( $value[ $state ] ) OR ! is_array( $value[ $state ] ) ) {
				$value[ $state ] = array();
			}
			if ( ! isset( $value[ $state ]['layout'] ) OR ! is_array( $value[ $state ]['layout'] ) ) {
				if ( $state != 'default' AND isset( $value['default']['layout'] ) ) {
					$value[ $state ]['layout'] = $value['default']['layout'];
				} else {
					$value[ $state ]['layout'] = array();
				}
			}
			$state_elms = array();
			foreach ( $value[ $state ]['layout'] as $place => $elms ) {
				if ( ! is_array( $elms ) ) {
					$elms = array();
				}
				foreach ( $elms as $index => $elm_id ) {
					if ( ! is_string( $elm_id ) OR strpos( $elm_id, ':' ) == - 1 ) {
						unset( $elms[ $index ] );
					} else {
						$state_elms[] = $elm_id;
						if ( ! isset( $value['data'][ $elm_id ] ) ) {
							$value['data'][ $elm_id ] = array();
						}
					}
				}
				$value[ $state ]['layout'][ $place ] = array_values( $elms );
			}
			if ( ! isset( $value[ $state ]['layout']['hidden'] ) OR ! is_array( $value[ $state ]['layout']['hidden'] ) ) {
				$value[ $state ]['layout']['hidden'] = array();
			}
			$value[ $state ]['layout']['hidden'] = array_merge( $value[ $state ]['layout']['hidden'], array_diff( array_keys( $value['data'] ), $state_elms ) );
			// Fixing options
			if ( ! isset( $value[ $state ]['options'] ) OR ! is_array( $value[ $state ]['options'] ) ) {
				$value[ $state ]['options'] = array();
			}
			$value[ $state ]['options'] = array_merge( $options_defaults, ( $state != 'default' ) ? $value['default']['options'] : array(), $value[ $state ]['options'] );
		}

		return $value;
	}
}

if ( ! function_exists( 'us_grid_available_post_types' ) ) {
	/**
	 * Get post types for selection in Grid element
	 *
	 * @param bool $reload used when list of available post types should be reloaded
	 *            because data that affects it was changed
	 *
	 * @return array
	 */
	function us_grid_available_post_types( $reload = FALSE ) {
		static $available_posts_types = array();

		if ( empty( $available_posts_types ) OR $reload ) {
			$posts_types_params = array(
				'show_in_menu' => TRUE,
			);
			$skip_post_types = array(
				'us_header',
				'us_page_block',
				'us_content_template',
				'us_grid_layout',
				'shop_order',
				'shop_coupon',
			);
			foreach ( get_post_types( $posts_types_params, 'objects' ) as $post_type_name => $post_type ) {
				if ( in_array( $post_type_name, $skip_post_types ) ) {
					continue;
				}
				$available_posts_types[ $post_type_name ] = $post_type->labels->name . ' (' . $post_type_name . ')';
			}
		}

		return apply_filters( 'us_grid_available_post_types', $available_posts_types );
	}
}

if ( ! function_exists( 'us_grid_available_taxonomies' ) ) {
	/**
	 * Get post taxonomies for selection in Grid element
	 *
	 * @return array
	 */
	function us_grid_available_taxonomies() {
		$available_taxonomies = array();
		$available_posts_types = us_grid_available_post_types();

		foreach ( $available_posts_types as $post_type => $name ) {
			$post_taxonomies = array();
			$object_taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $object_taxonomies as $tax_object ) {
				if ( ( $tax_object->public ) AND ( $tax_object->show_ui ) ) {
					$post_taxonomies[] = $tax_object->name;
				}
			}
			if ( is_array( $post_taxonomies ) AND count( $post_taxonomies ) > 0 ) {
				$available_taxonomies[ $post_type ] = array();
				foreach ( $post_taxonomies as $post_taxonomy ) {
					$available_taxonomies[ $post_type ][] = $post_taxonomy;
				}
			}
		}

		return $available_taxonomies;
	}
}

if ( ! function_exists( 'us_get_filter_taxonomies' ) ) {
	/**
	 * Get grid filter params
	 * @param string|array $prefixes
	 * @param string|array $params (Example: {prefix}_{param}={values}&...)
	 *
	 * @return array
	 */
	function us_get_filter_taxonomies( $prefixes = array(), $params = '' ) {
		// Parameters to check
		$prefixes = is_array( $prefixes )
			? $prefixes
			: array( $prefixes );

		// The resulting parameters as a string or array
		if ( ! empty( $params ) AND is_string( $params ) ) {
			parse_str( $params, $params );
		} else {
			// Get default params
			$params = $_REQUEST;
		}

		// Get all taxonomies
		$available_taxonomy = array();
		foreach ( array_keys( us_get_taxonomies( FALSE, TRUE, '' ) ) as $tax_name ) {
			$available_taxonomy[ $tax_name ] = 'tax';
		}

		// Add WooCommerce related fields
		$available_taxonomy['_price'] = 'cf';

		// Add fields from "Advanced Custom Fields" plugin
		if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
			foreach ( $acf_groups as $group ) {
				foreach ( (array) acf_get_fields( $group['ID'] ) as $field ) {

					// Only specific ACF types
					if ( in_array( $field['type'], array( 'number', 'range', 'select', 'checkbox', 'radio' ) ) ) {
						$available_taxonomy[ $field['name'] ] = 'cf';
					}
				}
			}
		}

		$result = array();
		static $_terms = array();

		foreach ( $prefixes as $prefix ) {
			foreach ( $params as $param => $param_values ) {
				$param = strtolower( $param );
				if ( strpos( $param, $prefix ) === 0 ) {
					// Remove prefix and get parameter name
					$param_name = substr( $param, strlen( $prefix . /* Separator */ '_' ) );

					// Get source prefix
					if ( preg_match( '/(\w+)_(\d+)$/', $param_name, $matches ) AND ! empty( $available_taxonomy[ $matches[1] ] ) ) {
						$source_prefix = $available_taxonomy[ $matches[1] ];
					} elseif ( ! empty( $available_taxonomy[ $param_name ] ) ) {
						$source_prefix = $available_taxonomy[ $param_name ];
					} else {
						continue;
					}

					// The taxonomy validation
					if ( $source_prefix === 'tax' ) {
						if ( ! isset( $_terms[ $param_name ] ) ) {
							$terms_query = array(
								'taxonomy' => $param_name,
								'hide_empty' => TRUE,
							);
							foreach ( get_terms( $terms_query ) as $term ) {
								$_terms[ $param_name ][ $term->term_id ] = $term->slug;
							}
						}
						if ( empty( $_terms[ $param_name ] ) OR ! is_string( $param_values ) ) {
							continue;
						}
					}

					// Formation of an array of parameters
					$param_values = explode( ',', $param_values );
					array_map( 'strtolower', $param_values );
					array_map( 'trim', $param_values );
					foreach ( $param_values as $item_value ) {
						if (
							(
								! empty( $_terms[ $param_name ] )
								AND in_array( $item_value, $_terms[ $param_name ] )
							)
							OR ! empty( $item_value )
						) {
							$result[ $source_prefix . '|' . $param_name ][] = ( string ) $item_value;
						}
					}
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_grid_filter_parse_param' ) ) {
	/**
	 * Parse param for grid filter
	 *
	 * @param string $param_name
	 * @return array
	 */
	function us_grid_filter_parse_param( $param_name ) {
		$result = array();
		if ( strpos( $param_name , '|' ) !== FALSE ) {
			list( $source, $param_name ) = explode( '|', $param_name, 2 );
			$result['source'] = strtolower( $source );
			// The for Advanced Custom Fields
			if (
				$result['source'] === 'cf'
				AND $param_name !== '_price'
				AND preg_match( '/(\w+)_(\d+)/', $param_name, $matches )
			) {
				$result['param_name'] = $matches[1];
				$result['acf_field_id'] = intval( $matches[2] );
			} else {
				$result['param_name'] = $param_name;
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'us_apply_grid_filters' ) ) {
	/**
	 * Apply grid filters to query_args
	 *
	 * @param ineger $post_id
	 * @param array $query_args
	 * @param string $grid_filter_params
	 * @return void
	 */
	function us_apply_grid_filters( $post_id, &$query_args, $grid_filter_params = NULL ) {
		/**
		 * @var array
		 */
		$post_grid_filter_atts = array();

		/**
		 * Get grid filter and load attributes
		 * @param WP_Post $post
		 * @return void
		 */
		$func_grid_filter_atts = function ( $post ) use ( &$post_grid_filter_atts ) {
			if (
				empty( $post_grid_filter_atts )
				AND $post instanceof WP_Post
				AND strpos( $post->post_content, '[us_grid_filter' ) !== FALSE
				AND ! $post_grid_filter_atts = get_post_meta( $post->ID, '_us_grid_filter_atts', TRUE )
			) {
				// Try to Save Grid Filter shortcode attributes if they weren't saved yet
				$post_grid_filter_atts = us_save_post_grid_filter_atts( $post->ID );
			}
		};

		// Recursively search for a grid filter on a page or in templates / page blocks
		$post = get_post( ( int ) $post_id );

		// The search on current page
		if ( is_callable( $func_grid_filter_atts ) ) {
			call_user_func( $func_grid_filter_atts, $post );
		}
		// The search on Page Blocks if they are on the page
		if ( ! empty( $func_grid_filter_atts ) ) {
			us_get_recursive_parse_page_block( $post, $func_grid_filter_atts );
		}
		// The search on templates
		if ( ! empty( $func_grid_filter_atts ) ) {
			foreach ( array( 'header', 'titlebar', 'sidebar', 'content', 'footer' ) as $area ) {
				if ( $area_id = get_post_meta( $post_id, sprintf( 'us_%s_id', $area ), TRUE ) ) {
					if ( $area_id === '__defaults__' ) {
						$area_id = us_get_option( sprintf( '%s_id', $area ) );
					}
					if ( is_numeric( $area_id ) ) {
						us_get_recursive_parse_page_block( get_post( (int) $area_id ), $func_grid_filter_atts );
					}
				}
			}
		}

		$allowed_taxonomies = array();
		foreach ( $post_grid_filter_atts as $filter_atts ) {
			if ( ! empty( $filter_atts['source'] ) AND strpos( $filter_atts['source'], '|' ) !== FALSE ) {
				$filter_atts_source = explode( '|', $filter_atts['source'] );
				$allowed_taxonomies[] = us_arr_path( $filter_atts_source, '1', NULL );
			}
		}

		// Get grid filter params
		$filter_ranges = array();
		$filter_items = us_get_filter_taxonomies( US_GRID_FILTER_PREFIX, $grid_filter_params );

		foreach ( $filter_items as $item_name => $item_values ) {
			if ( is_string( $item_values ) ) {
				$filter_items[ $item_name ] = array( $item_values );
			}
			// The for range values
			if ( count( $item_values ) === 1 AND preg_match( '/(\d+)-(\d+)/', $item_values[0], $matches ) ) {
				$filter_ranges[ $item_name ] = array( /* start value */$matches[1], /* end value */$matches[2] );
				unset( $filter_items[ $item_name ] );
			}
		}

		// Delete the filter by category for the store, this filter is in the tax_query
		if ( ! empty( $query_args['product_cat'] ) ) {
			unset( $query_args['product_cat'] );
		}

		$current_tax_queries = $current_acf_filters = $ranges = array();

		// TODO: Rewrite tax_query processing so as not to lose the passed parameters operator, field, etc.
		// Grouping of the current parameters
		foreach ( us_arr_path( $query_args, 'tax_query', array() ) as $index => $tax ) {
			if ( ! isset( $tax['taxonomy'] ) ) {
				continue;
			}
			$taxonomy = $tax['taxonomy'];
			// Clear allowed taxonomies
			if (
				! empty( $filter_items )
				AND ! empty( $allowed_taxonomies )
				AND in_array( $taxonomy, $allowed_taxonomies )
			) {
				continue;
			}
			if ( ! isset( $current_tax_queries[ $taxonomy ] ) ) {
				$current_tax_queries[ $taxonomy ] = array();
			}
			$terms = is_array( $tax['terms'] ) ? $tax['terms'] : array( $tax['terms'] );
			// If the value is a number, then for reliability we will accept the intval function,
			// since we have the separation of parameters from the grid filter and internal
			foreach ( $terms as &$term ) {
				if ( is_numeric( $term ) ) {
					$term = intval( $term );
				}
			}
			unset( $term );
			$terms = array_unique( array_merge( $current_tax_queries[ $taxonomy ], $terms ) );

			$current_tax_queries[ $taxonomy ] = $terms;

			// Delete index to avoid duplicates
			unset( $query_args['tax_query'][ $index ] );
		}

		// Adding parameters from the filter to the query request
		if ( ! empty( $filter_items ) ) {
			foreach ( $filter_items as $item_name => $item_values ) {

				// Get param_name
				$param = us_grid_filter_parse_param( $item_name );
				$item_source = us_arr_path( $param, 'source' );
				$item_name = us_arr_path( $param, 'param_name', $item_name );

				if (
					in_array( '*', $item_values )
					OR (
						! empty( $post_id )
						AND ! in_array( $item_name, $allowed_taxonomies )
					)
				) {
					continue;
				}

				// The for taxonomies
				if ( $item_source === 'tax' ) {
					if ( ! isset( $current_tax_queries[ $item_name ] ) ) {
						$current_tax_queries[ $item_name ] = array();
					}
					$item_values = array_unique( array_merge( $current_tax_queries[ $item_name ], $item_values ) );
					$current_tax_queries[ $item_name ] = $item_values;


					// The for Advanced Custom Fields
				} else if( $item_source === 'cf' AND $item_name !== '_price' ) {
					$current_acf_filters[ $item_name ] = array(
						'field_id' => us_arr_path( $param, 'acf_field_id', NULL ),
						'values' => array_unique( $item_values )
					);
				}
			}

			// Cleaning filters when not request
		} else if( ! is_null( $grid_filter_params ) AND ! empty( $allowed_taxonomies ) ) {
			foreach ( $allowed_taxonomies as $key ) {
				unset( $current_tax_queries[ $key ] );
			}
		}

		// Creating conditions for taxonomies
		if ( empty( $query_args['tax_query'] ) ) {
			$query_args['tax_query'] = array(
				'relation' => 'AND'
			);
		}

		foreach ( $current_tax_queries as $item_name => $item_values ) {
			$tax_query = array(
				'taxonomy' => $item_name,
				'field' => 'slug',
				'terms' => $item_values,
				'operator' => 'IN',
			);
			// At this stage, it is important to separate the is_int from is_number
			// The number in the string entry is the parameters from the filter
			if ( is_int( $item_values ) OR ( isset( $item_values[0] ) AND is_int( $item_values[ 0 ] ) ) ) {
				unset( $tax_query[ 'field' ] );
			}
			$query_args['tax_query'][] = $tax_query;
		}

		// If a category filter is installed on the category page, then delete `category_name`
		if ( ! empty( $current_tax_queries['category'] ) AND isset( $query_args['category_name'] ) ) {
			unset( $query_args['category_name'] );
		}

		if ( empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query'] = array(
				'relation' => 'AND'
			);
		}

		// Creating conditions for ranges
		foreach ( $filter_ranges as $item_name => $item_values ) {
			$param = us_grid_filter_parse_param( $item_name );
			if ( us_arr_path( $param, 'source' ) !== 'cf' ) {
				continue;
			}

			$meta_query = array(
				'key' => us_arr_path( $param, 'param_name', $item_name ),
				'type' => 'NUMERIC',
			);

			if ( /* min */ $item_values[0] === 0 ) {
				$meta_query = array_merge( array(
					'value' => $item_values[1],
					'compare' => '<=',
				), $meta_query );
			} else if ( /* max */ $item_values[1] == 0 ) {
				$meta_query = array_merge( array(
					'value' => $item_values[0],
					'compare' => '>=',
				), $meta_query );
			} else {
				$meta_query = array_merge( array(
					'value' => $item_values,
					'compare' => 'BETWEEN',
				), $meta_query );
			}
			$query_args['meta_query'][] = $meta_query;
		}

		// Creating conditions for Advanced Custom Fields ( select, radio and checkboxes )
		foreach ( $current_acf_filters as $acf_field_name => $acf_item ) {
			if ( function_exists( 'acf_get_field' ) ) {
				$acf_values = array();
				$acf_field = acf_get_field( $acf_item['field_id'] );

				foreach ( array_keys( us_arr_path( $acf_field, 'choices', array() ) ) as $item ) {
					$item_key = preg_replace( '/\s/', '_', strtolower( $item ) );
					if ( $item_key AND in_array( $item_key, us_arr_path( $acf_item, 'values', array() ) ) ) {
						$acf_values[] = $item;
					}
				}

				$acf_values = array_map( 'trim', $acf_values );
				$acf_values = array_unique( $acf_values );

				$meta_query = array( 'relation' => 'OR' );
				foreach ( $acf_values as $acf_value ) {
					$meta_query[] = array(
						'key' => $acf_field_name,
						'value' => sprintf( '^%s$|"%s"', $acf_value, $acf_value ),
						'compare' => 'RLIKE',
						'type' => 'CHAR',
					);
				}
				$query_args['meta_query'][] = $meta_query;
			}
		}
	}
}

if ( ! function_exists( 'us_define_content_and_apply_grid_filters' ) ) {
	/**
	 * Define content and apply grid filters to query_args
	 *
	 * @param array $query_vars
	 * @param WP_Tax_Query $tax_query
	 * @return boolean
	 */
	function us_define_content_and_apply_grid_filters( &$query_vars, $tax_query = NULL ) {
		global $_us_content_id;
		$grid_filter_found = FALSE;
		if ( ! $_us_content_id ) {
			$_us_content_id = us_get_page_area_id( 'content' );

			// If template for content area is found ...
			if ( $post = get_post( $_us_content_id ) ) {
				// ... first, check if content area has grid filter ...
				if ( strpos( $post->post_content, '[us_grid_filter' ) !== FALSE ) {
					$grid_filter_found = TRUE;

					// ... otherwise search grid filter in Page Blocks.
				} else {
					us_get_recursive_parse_page_block( $post, function( $post ) use ( &$_us_content_id, &$grid_filter_found ) {
						if ( $post instanceof WP_Post AND strpos( $post->post_content, '[us_grid_filter' ) !== FALSE ) {
							$_us_content_id = $post->ID;
							$grid_filter_found = TRUE;
						}
					} );
				}
			}
		}
		if ( $grid_filter_found ) {
			// Update tax_query
			if ( $tax_query instanceof WP_Tax_Query ) {
				$query_vars['tax_query'] = $tax_query->queries;
			}
			us_apply_grid_filters( $_us_content_id, $query_vars );
		}

		return $grid_filter_found;
	}
}

if ( ! function_exists( 'us_grid_filter_get_count_items' ) ) {
	/**
	 * Get the number of records for a filter element
	 *
	 * @param array $query_args
	 * @return int
	 */
	function us_grid_filter_get_count_items( $query_args ) {
		if ( empty( $query_args ) OR ! is_array( $query_args ) ) {
			return 0;
		}
		if (
			! empty( $query_args['post_type'] )
			AND (
					(
						is_array( $query_args['post_type'] )
						AND in_array( 'product', $query_args['post_type'] )
					)
					OR $query_args['post_type'] == 'product'
			)
			AND class_exists( 'woocommerce' )
			AND is_object( wc() )
		) {
			if ( ! isset( $query_args['tax_query'] ) ) {
				$query_args['tax_query'] = array();
			}
			$query_args['tax_query'] = wc()->query->get_tax_query( $query_args['tax_query'] );
		}

		return ( new WP_Query( $query_args ) )->found_posts;
	}
}

if ( ! function_exists( 'us_inject_grid_filter_to_archive_page' ) ) {
	/**
	 * The inject grid filter for archive pages
	 *
	 * @param WP_Query $query
	 * @return void
	 */
	function us_inject_grid_filter_to_archive_page( $query ) {
		global $us_context_layout;
		if (
			is_null( $us_context_layout )
			AND (
				$query->is_tax OR $query->is_tag OR $query->is_archive
			)
		) {
			$grid_filter_found = us_define_content_and_apply_grid_filters( $query->query_vars, $query->tax_query );
			if ( $grid_filter_found AND class_exists( 'woocommerce' ) AND is_object( wc() ) ) {
				$query->query_vars['tax_query'] = wc()->query->get_tax_query( $query->query_vars['tax_query'] );
			}
		}
	}
	add_action( 'pre_get_posts', 'us_inject_grid_filter_to_archive_page', 10, 1 );
}
