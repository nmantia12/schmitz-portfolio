<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_get_header_option' ) ) {
	/**
	 * Get header option for the specified state
	 *
	 * @param string $name Option name
	 * @param string $state Header state: 'default' / 'tablets' / 'mobiles'
	 * @param string $default
	 *
	 * @return string
	 */
	function us_get_header_option( $name, $state = 'default', $default = NULL ) {
		global $us_header_settings;
		us_load_header_settings_once();

		// These options are available in Default state only
		$shared_options = array(
			'top_fullwidth',
			'top_bg_color',
			'top_text_color',
			'top_text_hover_color',
			'top_transparent_bg_color',
			'top_transparent_text_color',
			'top_transparent_text_hover_color',
			'middle_fullwidth',
			'middle_bg_color',
			'middle_text_color',
			'middle_text_hover_color',
			'middle_transparent_bg_color',
			'middle_transparent_text_color',
			'middle_transparent_text_hover_color',
			'bottom_fullwidth',
			'bottom_bg_color',
			'bottom_text_color',
			'bottom_text_hover_color',
			'bottom_transparent_bg_color',
			'bottom_transparent_text_color',
			'bottom_transparent_text_hover_color',
		);

		if (
			$state != 'default'
			AND ( ! isset( $us_header_settings[ $state ]['options'][ $name ] )
			OR in_array( $name, $shared_options ) )
		) {
			$state = 'default';
		}

		if ( ! empty( $us_header_settings[ $state ]['options'][ $name ] ) ) {
			return $us_header_settings[ $state ]['options'][ $name ];
		}

		/*
		 * Default settings from the config
		 * @var array
		 */
		static $default_header_settings = array();
		if ( is_null( $default ) AND empty( $default_header_settings ) ) {
			foreach ( us_config( 'header-settings.options', array() ) as $group ) {
				if ( ! is_array( $group ) ) {
					continue;
				}
				foreach ( $group as $param_name => $options ) {
					if ( us_arr_path( $options, 'type' ) == 'color' AND ! empty( $options['std'] ) ) {
						$default_header_settings[ $param_name ] = $options['std'];
					}
				}
			}
		}

		if ( is_null( $default ) AND ! empty( $default_header_settings[ $name ] ) ) {
			return $default_header_settings[ $name ];
		}

		return $default;
	}
}


/**
 * Get header layout for the specified state
 *
 * @param $state
 *
 * @return array
 */
function us_get_header_layout( $state = 'default' ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$layout = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	if ( $state != 'default' AND isset( $us_header_settings['default']['layout'] ) AND is_array( $us_header_settings['default']['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings['default']['layout'] );
	}
	if ( isset( $us_header_settings[ $state ]['layout'] ) AND is_array( $us_header_settings[ $state ]['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings[ $state ]['layout'] );
	}

	return $layout;
}

/**
 * Load the current header settings for all possible responsive states
 */
function us_load_header_settings_once() {

	global $us_header_settings;

	if ( isset( $us_header_settings ) ) {
		return;
	}
	// Basic structure
	$us_header_settings = array(
		'default' => array( 'options' => array(), 'layout' => array() ),
		'tablets' => array( 'options' => array(), 'layout' => array() ),
		'mobiles' => array( 'options' => array(), 'layout' => array() ),
		'data' => array(),
	);
	$us_header_settings = apply_filters( 'us_load_header_settings', $us_header_settings );
}

/**
 * Recursively output elements of a certain state / place
 *
 * @param array $settings Current layout
 * @param string $state Current state
 * @param string $place Outputted place
 * @param string $context 'header' / 'grid'
 * @param string $grid_object_type 'post' / 'term'
 */
function us_output_builder_elms( &$settings, $state, $place, $context = 'header', $grid_object_type = 'post' ) {

	$layout = &$settings[ $state ]['layout'];
	$data = &$settings['data'];
	if ( ! isset( $layout[ $place ] ) OR ! is_array( $layout[ $place ] ) ) {
		return;
	}

	// Set 3 states for header and 1 for other contexts, like Grid Layouts
	$_states = ( $context === 'header' ) ? array( 'default', 'tablets', 'mobiles' ) : array( 'default' );

	$visible_elms = array();
	foreach ( $_states as $_state ) {
		$visible_elms[ $_state ] = us_get_builder_shown_elements_list( us_arr_path( $settings, $_state . '.layout', array() ) );
	}

	foreach ( $layout[ $place ] as $elm ) {
		$classes = '';
		if ( $context === 'header' ) {
			if ( isset( $data[ $elm ] ) ) {
				if ( us_arr_path( $data[ $elm ], 'hide_for_sticky', FALSE ) ) {
					$classes .= ' hide-for-sticky';
				}
				if ( us_arr_path( $data[ $elm ], 'hide_for_not_sticky', FALSE ) ) {
					$classes .= ' hide-for-not-sticky';
				}
			}
		}
		foreach ( $_states as $_state ) {
			if ( ! in_array( $elm, $visible_elms[ $_state ] ) ) {
				$classes .= ' hidden_for_' . $_state;
			}
		}
		if ( $context === 'header' ) {
			$classes .= ' ush_' . str_replace( ':', '_', $elm );
		} elseif ( $context === 'grid' ) {
			$classes .= ' usg_' . str_replace( ':', '_', $elm );
		}
		if ( substr( $elm, 1, 7 ) == 'wrapper' ) {

			// Wrapper
			$style_attr = '';
			$type = strtok( $elm, ':' );
			if ( isset( $data[ $elm ] ) ) {
				if ( isset( $data[ $elm ]['alignment'] ) ) {
					$classes .= ' align_' . $data[ $elm ]['alignment'];
				}
				if ( isset( $data[ $elm ]['valign'] ) ) {
					$classes .= ' valign_' . $data[ $elm ]['valign'];
				}
				if ( isset( $data[ $elm ]['wrap'] ) AND $data[ $elm ]['wrap'] ) {
					$classes .= ' wrap';
				}
				if ( isset( $data[ $elm ]['el_class'] ) ) {
					$classes .= ' ' . $data[ $elm ]['el_class'];
				}
				if ( ! empty( $data[ $elm ]['inner_items_gap'] ) ) {
					$inner_items_gap = esc_attr( trim( $data[ $elm ]['inner_items_gap'] ) );
					if ( strpos( $elm, 'hwrapper' ) !== FALSE ) {

						// Set CSS var for Horizontal wrapper, if the value is not default
						if ( $inner_items_gap != '1.2rem' ) {
							$style_attr = ' style="--hwrapper-gap: ' . $inner_items_gap . ';"';
						}

						// Set CSS var for Vertical wrapper, if the value is not default
					} elseif ( $inner_items_gap != '0.7rem' ) {
						$style_attr = ' style="--vwrapper-gap: ' . $inner_items_gap . ';"';
					}
				}
			}
			echo '<div class="w-' . $type . $classes . '"' . $style_attr . '>';
			us_output_builder_elms( $settings, $state, $elm, $context );
			echo '</div>';
		} else {

			// Element
			$type = strtok( $elm, ':' );
			$defaults = us_get_elm_defaults( $type, $context );
			if ( ! isset( $data[ $elm ] ) ) {
				$data[ $elm ] = us_get_elm_defaults( $type, $context );
			}
			$values = array_merge( $defaults, array_intersect_key( $data[ $elm ], $defaults ) );
			$values['id'] = $elm;
			$values['classes'] = ( isset( $values['classes'] ) ? $values['classes'] : '' ) . $classes;
			$values['us_elm_context'] = $context;
			$values['us_grid_object_type'] = $grid_object_type;

			// Adding special classes
			us_load_template( 'templates/elements/' . $type, $values );
		}
	}
}

/**
 * Get default value for an element
 *
 * @param string $type
 * @param string $context 'header' or 'grid'
 *
 * @return mixed
 */
function us_get_elm_defaults( $type, $context = 'header' ) {
	global $us_elm_defaults, $usof_options;
	if ( ! isset( $us_elm_defaults ) ) {
		$us_elm_defaults = array();
	}
	if ( ! isset( $us_elm_defaults[ $context ] ) ) {
		$us_elm_defaults[ $context ] = array();
	}
	if ( ! isset( $us_elm_defaults[ $context ][ $type ] ) ) {
		$us_elm_defaults[ $context ][ $type ] = array();
		$elm_config = us_config( 'elements/' . $type, array() );
		foreach ( us_arr_path( $elm_config, 'params', array() ) as $field_name => $field ) {
			$value = isset( $field['std'] ) ? $field['std'] : '';
			// Check if context specific standard value is set
			$value = isset( $field[ $context . '_std' ] ) ? $field[ $context . '_std' ] : $value;
			if ( $context === 'header' ) {
				// Some default header values may be based on main theme options' values
				if ( function_exists( 'usof_load_options_once' ) ) {
					usof_load_options_once();
				}
				if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' AND isset( $usof_options[ substr( $value, 1 ) ] ) ) {
					$value = $usof_options[ substr( $value, 1 ) ];
				}
			}
			$us_elm_defaults[ $context ][ $type ][ $field_name ] = $value;
		}
		if ( isset( $elm_config['deprecated_params'] ) ) {
			foreach ( $elm_config['deprecated_params'] as $field_name ) {
				$us_elm_defaults[ $context ][ $type ][ $field_name ] = '';
			}
		}
	}

	return us_arr_path( $us_elm_defaults, array( $context, $type ), array() );
}

// Backward compability with older HB versions
if ( ! function_exists( 'us_get_header_elm_defaults' ) ) {
	function us_get_header_elm_defaults( $type ) {
		return us_get_elm_defaults( $type, 'header' );
	}
}

/**
 * Get elements
 *
 * @param string $type
 * @param bool $key_as_class Should the keys of the resulting array be css classes instead of elms ids?
 *
 * @return array
 */
function us_get_header_elms_of_a_type( $type, $key_as_class = TRUE ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$defaults = us_get_elm_defaults( $type, 'header' );
	$result = array();
	if ( ! is_array( $us_header_settings['data'] ) ) {
		return $result;
	}
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( strtok( $elm_id, ':' ) != $type ) {
			continue;
		}
		$key = $key_as_class ? ( 'ush_' . str_replace( ':', '_', $elm_id ) ) : $elm_id;
		$result[ $key ] = array_merge( $defaults, array_intersect_key( $elm, $defaults ) );
	}

	return $result;
}

/**
 * Make the provided header settings value consistent and proper
 *
 * @param $value array
 *
 * @return array
 */
function us_fix_header_settings( $value ) {
	if ( empty( $value ) OR ! is_array( $value ) ) {
		$value = array();
	}
	if ( ! isset( $value['data'] ) OR ! is_array( $value['data'] ) ) {
		$value['data'] = array();
	}
	$options_defaults = array();
	foreach ( us_config( 'header-settings.options', array() ) as $group => $opts ) {
		foreach ( $opts as $opt_name => $opt ) {
			$options_defaults[ $opt_name ] = isset( $opt['std'] ) ? $opt['std'] : '';
		}
	}
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
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

	foreach ( $value['data'] as $elm_id => $values ) {
		$type = strtok( $elm_id, ':' );
		$defaults = us_get_elm_defaults( $type, 'header' );
		$value['data'][ $elm_id ] = array_merge( $defaults, array_intersect_key( $value['data'][ $elm_id ], $defaults ) );
	}

	return $value;
}

function us_fix_header_template_settings( $value ) {

	if ( isset( $value['title'] ) ) {
		// Don't need this in data processing
		unset( $value['title'] );
	}
	$template_structure = array(
		'default' => array( 'options' => array(), 'layout' => array() ),
		'tablets' => array( 'options' => array(), 'layout' => array() ),
		'mobiles' => array( 'options' => array(), 'layout' => array() ),
		'data' => array(),
	);
	$value = us_array_merge( $template_structure, $value );
	$layout_structure = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
		// Options
		$value[ $state ]['options'] = array_merge( ( $state == 'default' ) ? array() : $value['default']['options'], $value[ $state ]['options'] );
		// Layout
		$value[ $state ]['layout'] = array_merge( $layout_structure, ( $state == 'default' ) ? array() : $value['default']['layout'], $value[ $state ]['layout'] );
	}
	$value = us_fix_header_settings( $value );

	return $value;
}

/**
 * Get list of user registered nav menus with theirs proper names, in a format sutable for usof select field
 *
 * @return array
 */
function us_get_nav_menus() {
	$menus = array();
	foreach ( get_terms( 'nav_menu', array( 'hide_empty' => TRUE ) ) as $menu ) {
		$menus[ $menu->slug ] = $menu->name;
	}

	// Adding us_main_menu location if it is filled with mene
	$theme_locations = get_nav_menu_locations();
	if ( isset( $theme_locations['us_main_menu'] ) ) {
		$menu_obj = get_term( $theme_locations['us_main_menu'], 'nav_menu' );
		if ( $menu_obj AND is_object( $menu_obj ) AND isset ( $menu_obj->name ) ) {
			$menus['location:us_main_menu'] = $menu_obj->name . ' (' . __( 'Custom Menu', 'us' ) . ')';
		}
	}

	return $menus;
}

/**
 * Get the list of header elements that are shown in the certain layout listing
 *
 * @param array $list Euther layout or separate list
 *
 * @return array
 */
function us_get_builder_shown_elements_list( $list ) {
	$shown = array();
	foreach ( $list as $key => $sublist ) {
		if ( $key != 'hidden' ) {
			$shown = array_merge( $shown, $sublist );
		}
	}

	return $shown;
}

// Changing ordering to avoid JavaScript errors with NextGEN Gallery plugin
add_action( 'wp_footer', 'us_pass_header_settings_to_js', - 2 );
function us_pass_header_settings_to_js() {
	global $us_header_settings;
	us_load_header_settings_once();
	$header_settings = $us_header_settings;
	if ( isset( $header_settings['data'] ) ) {
		unset( $header_settings['data'] );
	}
	echo '<script>';
	echo 'if ( window.$us === undefined ) window.$us = {};';
	echo '$us.headerSettings = ' . json_encode( $header_settings ) . ';';
	echo '</script>';
}

/**
 * Get the header design options css for all the fields
 *
 * @return string
 */
function us_get_header_design_options_css() {
	global $us_header_settings;
	us_load_header_settings_once();
	$tablets_breakpoint = ( isset( $us_header_settings['tablets']['options']['breakpoint'] ) ) ? intval( $us_header_settings['tablets']['options']['breakpoint'] ) : 900;
	$mobiles_breakpoint = ( isset( $us_header_settings['mobiles']['options']['breakpoint'] ) ) ? intval( $us_header_settings['mobiles']['options']['breakpoint'] ) : 600;
	$device_sizes = array(
		'default' => '',
		'tablets' => '(min-width: ' . $mobiles_breakpoint . 'px) and (max-width: ' . ( $tablets_breakpoint - 1 ) . 'px)',
		'mobiles' => '(max-width: ' . ( $mobiles_breakpoint - 1 ) . 'px)',
	);

	$jsoncss_collection = array();
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( ! isset( $elm['css'] ) OR empty( $elm['css'] ) OR ! is_array( $elm['css'] ) ) {
			continue;
		}
		foreach ( array_keys( $device_sizes ) as $device_type ) {
			if ( $css_options = us_arr_path( $elm, 'css.' . $device_type, FALSE ) ) {
				$class_name = 'ush_' . str_replace( ':', '_', $elm_id );
				$css_options = apply_filters( 'us_output_design_css_options', $css_options, $device_type );
				$jsoncss_collection[ $device_type ][ $class_name ] = $css_options;
			}
		}
	}

	return us_jsoncss_compile( $jsoncss_collection, $device_sizes );
}

/**
 * Add link to Theme Options to Admin Bar
 *
 * @param $wp_admin_bar
 */
function us_admin_bar_theme_options_link( $wp_admin_bar ) {
	$wp_admin_bar->add_node(
		array(
			'id' => 'us_theme_otions',
			'title' => __( 'Theme Options', 'us' ),
			'href' => admin_url( 'admin.php?page=us-theme-options' ),
			'parent' => 'site-name',
		)
	);
}

add_action( 'init', 'us_admin_bar_theme_options_link_init' );
function us_admin_bar_theme_options_link_init() {
	if ( ! is_admin() AND function_exists( 'current_user_can' ) AND function_exists( 'wp_get_current_user' ) AND current_user_can( 'administrator' ) ) {
		add_action( 'admin_bar_menu', 'us_admin_bar_theme_options_link', 99 );
	}
}


/**
 * Add "no-touch" class to html on desktops
 */
add_action( 'wp_head', 'us_output_no_touch_script' );
function us_output_no_touch_script() {
	?>
	<script>
		if ( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
			var root = document.getElementsByTagName( 'html' )[ 0 ]
			root.className += " no-touch";
		}
	</script>
	<?php
}

if ( ! function_exists( 'us_admin_bar_menu' ) ) {
	add_action( 'admin_bar_menu', 'us_admin_bar_menu', 500 );
	/**
	 * Add link to Admin bar to edit the current header, content template and page blocks
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The admin bar
	 * @return void
	 */
	function us_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
		global $pagenow;
		if ( current_user_can( 'administrator' ) AND $pagenow === 'index.php' ) {
			$area_ids = array();
			$received_posts = array();
			$edit_menu = array();

			$post_args = array(
				'post_type' => array( 'us_header', 'us_content_template', 'us_page_block' ),
				'post__in' => array(),
			);
			$area_names = array(
				'header' => _x( 'Header', 'site top area', 'us' ),
				'content' => __( 'Content template', 'us' ),
				'page_blocks' => __( 'Page Blocks', 'us' ),
				'footer' => __( 'Footer', 'us' ),
			);

			/**
			 * Get recursively all page block IDs
			 *
			 * @param array $acc
			 * @param string $content
			 * @param integer $current_level
			 * @return void
			 */
			// TODO: Replace with us_get_recursive_parse_page_block()
			$func_get_page_block_ids = function ( &$acc = array(), $content, $current_level = 1 ) use ( &$func_get_page_block_ids, &$received_posts ) {
				if ( $current_level >= 15 ) {
					return;
				}
				$shortcode_regex = get_shortcode_regex( array( 'us_page_block' ) );
				if ( $content AND preg_match_all( '/' . $shortcode_regex . '/s', $content, $matches ) ) {
					foreach ( us_arr_path( $matches, '3', array() ) as $atts ) {
						$atts = shortcode_parse_atts( $atts );
						$id = intval( us_arr_path( $atts, 'id', 0 ) );
						if ( $id ) {
							$acc[] = $id;
							if ( $post = get_post( $id ) AND ! empty( $post->post_content ) ) {
								$received_posts[ $id ] = $post;
								$func_get_page_block_ids( $acc, $post->post_content, ++ $current_level );
							}
						}
					}
				}
			};
			// Search for page block in the current page object
			$current_page = get_queried_object();
			if ( $current_page instanceof WP_Post AND strpos( $current_page->post_content, '[us_page_block' ) !== FALSE ) {
				$func_get_page_block_ids( $area_ids['page_blocks'], $current_page->post_content );
			}
			unset( $current_page );
			// Get all ids
			foreach ( array_keys( $area_names ) as $area ) {
				if ( $area == 'page_blocks' AND $content = us_get_current_page_block_content() ) {
					$func_get_page_block_ids( $area_ids[ $area ], $content );
					if ( ! empty( $area_ids[ $area ] ) AND is_array( $post_args['post__in'] ) ) {
						$post_args['post__in'] = array_merge( $post_args['post__in'], $area_ids[ $area ] );
					}
				} elseif ( $area_id = us_get_page_area_id( $area ) ) {
					$post_args['post__in'][] = $area_ids[ $area ] = $area_id;

					// If there are WPML translations add identifiers to $area_ids
					if ( $wpml_area_id = apply_filters( 'wpml_object_id', $area_id, 'post' ) ) {
						$post_args['post__in'][] = $area_ids[ $area ] = $wpml_area_id;
					}
				}
				$edit_menu[ $area ] = array(
					'id' => sprintf( 'us-%s', $area ),
					'parent' => 'edit',
					'title' => us_arr_path( $area_names, $area ),
					'meta' => array(
						'class' => 'us-admin-bar',
						'onclick' => 'return false',
						'html' => '',
					),
				);
			}
			// Delete already received posts from the request
			if ( ! empty( $area_ids['page_blocks'] ) ) {
				foreach ( $area_ids['page_blocks'] as $id ) {
					if ( isset( $received_posts[ $id ] ) ) {
						$key = array_search( $id, $post_args['post__in'], TRUE );
						if ( $key !== FALSE ) {
							unset( $post_args['post__in'][ $key ] );
						}
					}
				}
			}
			// Get all posts
			if ( ! empty( $post_args['post__in'] ) AND $posts = get_posts( $post_args ) ) {
				if ( ! empty( $received_posts ) ) {
					$posts = array_merge( $posts, $received_posts );
				}
				foreach ( $posts as $post ) {
					if (
						$post->post_type === 'us_page_block'
						AND ! empty( $area_ids['page_blocks'] )
						AND in_array( $post->ID, $area_ids['page_blocks'] )
					) {
						$key = 'page_blocks';
					} else {
						$keys = $area_ids;
						unset( $keys['page_blocks'] );
						$keys = array_flip( $keys );
						$key = $keys[ $post->ID ];
						unset( $keys );
					}
					$edit_menu[ $key ]['meta']['html'] .= sprintf(
						'<a href="%s">%s</a>',
						admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
						strip_tags( $post->post_title )
					);
				}
			}
			if ( ! empty( $edit_menu ) ) {

				// US Admin bar styles
				$style = us_minify_css(
					'
					.us-admin-bar {
						margin-bottom: 6px !important;
						white-space: nowrap;
						max-width: 300px;
						overflow: hidden;
					}
					.us-admin-bar > .ab-item {
						font-weight: 600 !important;
						color: #fff !important;
					}
					.us-admin-bar > * {
						line-height: 24px !important;
						height: 24px !important;
					}
				'
				);
				echo '<style id="us-admin-bar">' . $style . '</style>';

				foreach ( $edit_menu as $area => $values ) {
					if ( ! empty( $values['meta']['html'] ) ) {
						$wp_admin_bar->add_menu( $values );
					}
				}
			}
			unset( $area_ids, $area_names, $post_args, $received_posts, $edit_menu );
		}
	}
}

if ( ! function_exists( 'us_hb_settings_fallback' ) ) {
	/**
	 * Apply fallback changes for old header settings on update
	 *
	 * @param $header_settings
	 * @return array
	 */
	function us_hb_settings_fallback( $header_settings ) {
		global $usof_options;

		// Check if the settings are empty and abort following execution in this case
		if ( ! is_array( $header_settings ) OR empty( $header_settings['default'] ) ) {
			return $header_settings;
		}

		// Fallback for options
		if ( ! isset( $header_settings['default']['options']['top_transparent_text_hover_color'] ) ) {
			$header_settings['default']['options']['top_transparent_text_hover_color'] =
				isset( $usof_options['color_header_bottom_text_hover'] ) ? '_header_transparent_text_hover' : '_header_top_transparent_text_hover';
		}

		if ( ! isset( $header_settings['default']['options']['bottom_bg_color'] ) ) {
			$header_settings['default']['options']['bottom_bg_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_bg', '_header_middle_bg' );
		}

		if ( ! isset( $header_settings['default']['options']['bottom_text_hover_color'] ) ) {
			$header_settings['default']['options']['bottom_text_hover_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_text_hover', '_header_middle_text_hover' );
		}

		if ( ! isset( $header_settings['default']['options']['bottom_text_color'] ) ) {
			$header_settings['default']['options']['bottom_text_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_text', '_header_middle_text' );
		}

		// Fallback for elements
		foreach ( $header_settings['data'] as $elm_id => $elm_data ) {

			// Menu
			if ( substr( $elm_id, 0, 4 ) == 'menu' ) {
				if ( ! isset( $elm_data['color_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_active_bg'] =
						us_arr_path( $usof_options, 'color_menu_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_active_text'] =
						us_arr_path( $usof_options, 'color_menu_active_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_transparent_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_transparent_active_bg'] =
						us_arr_path( $usof_options, 'color_menu_transparent_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_transparent_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_transparent_active_text'] =
						us_arr_path( $usof_options, 'color_menu_transparent_active_text', '_header_transparent_text_hover' );
				}
				if ( ! isset( $elm_data['color_hover_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_hover_bg'] =
						us_arr_path( $usof_options, 'color_menu_hover_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_hover_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_hover_text'] =
						us_arr_path( $usof_options, 'color_menu_hover_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_drop_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_bg'] =
						us_arr_path( $usof_options, 'color_drop_bg', '_header_middle_bg' );
				}
				if ( ! isset( $elm_data['color_drop_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_text'] =
						us_arr_path( $usof_options, 'color_drop_text', '_header_middle_text' );
				}
				if ( ! isset( $elm_data['color_drop_hover_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_hover_bg'] =
						us_arr_path( $usof_options, 'color_drop_hover_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_drop_hover_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_hover_text'] =
						us_arr_path( $usof_options, 'color_drop_hover_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_drop_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_active_bg'] =
						us_arr_path( $usof_options, 'color_drop_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_drop_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_active_text'] =
						us_arr_path( $usof_options, 'color_drop_active_text', '_header_middle_text_hover' );
				}
			}

			// Search
			if ( substr( $elm_id, 0, 5 ) == 'search' ) {
				if ( ! isset( $elm_data['field_bg_color'] ) ) {
					$header_settings['data'][ $elm_id ]['field_bg_color'] =
						us_arr_path( $usof_options, 'color_header_search_bg', '' );
				}
				if ( ! isset( $elm_data['field_text_color'] ) ) {
					$header_settings['data'][ $elm_id ]['field_text_color'] =
						us_arr_path( $usof_options, 'color_header_search_text', '' );
				}
			}

		}

		return $header_settings;
	}
}
