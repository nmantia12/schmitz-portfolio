<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Custom HTML output for "Menu" element in Headers
class US_Walker_Nav_Menu extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		// depth dependent classes
		$level = ( $depth + 2 ); // because it counts the first submenu as 0

		// build html
		$output .= '<ul class="w-nav-list level_' . $level . '">';
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "</ul>";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$level = ( $depth + 1 ); // because it counts the first submenu as 0

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'w-nav-item';
		$classes[] = 'level_' . $level;
		$classes[] = 'menu-item-' . $item->ID;

		// Add columns value
		if ( ! empty( $item->mega_menu_cols ) ) {
			$classes[] = 'columns_' . $item->mega_menu_cols;
		}

		// Add mobile behavior value
		if ( ! empty( $item->mobile_behavior ) ) {
			$classes[] = 'mobile-drop-by_' . $item->mobile_behavior;
		}

		// Removing active classes for scroll links, so they could be handled by JavaScript instead
		if ( isset( $item->url ) AND strpos( $item->url, '#' ) !== FALSE ) {
			$classes = array_diff(
				$classes,
				array(
					'current-menu-item',
					'current-menu-ancestor',
					'current-menu-parent',
				)
			);
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= '<li' . $id . $class_names .'>';

		// Output Page Block content
		if ( $item->object == 'us_page_block' ) {

			if ( $page_block = get_post( $item->object_id ) ) {
				global $us_is_menu_page_block;
				$us_is_menu_page_block = TRUE;

				$page_block_content = $page_block->post_content;

				// Remove Row and Column shortcodes, if set in the item
				if ( get_post_meta( $item->ID, '_menu_item_remove_rows', TRUE ) ) {
					$page_block_content = str_replace( array( '[vc_row]', '[/vc_row]', '[vc_column]', '[/vc_column]' ), '', $page_block_content );
					$page_block_content = preg_replace( '~\[vc_row (.+?)]~', '', $page_block_content );
					$page_block_content = preg_replace( '~\[vc_column (.+?)]~', '', $page_block_content );
				}
				us_add_page_shortcodes_custom_css( $page_block->ID );

				//use filter for page block content
				$output .= apply_filters( 'us_page_block_the_content', $page_block_content );

				$us_is_menu_page_block = FALSE;
			}

			// Output Menu Items
		} else {
			$anchor_atts = array( 'class' => 'w-nav-anchor level_' . $level );

			// Add Button Styles
			if ( $depth === 0 AND $btn_style = get_post_meta( $item->ID, '_menu_item_btn_style', TRUE ) ) {
				$anchor_atts['class'] .= ' w-btn us-btn-style_' . $btn_style;
			}

			if ( ! empty( $item->url ) ) {
				$anchor_atts['href'] = $item->url;
			}
			if ( ! empty( $item->attr_title ) ) {
				$anchor_atts['title'] = $item->attr_title;
			}
			if ( ! empty( $item->target ) ) {
				$anchor_atts['target'] = $item->target;
			}
			if ( ! empty( $item->xfn ) ) {
				$anchor_atts['rel'] = $item->xfn;
			}

			$item_output = $args->before;
			$item_output .= '<a ' . us_implode_atts( $anchor_atts ) . '>';
			$item_output .= $args->link_before;
			$item_output .= '<span class="w-nav-title">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span>';
			$item_output .= '<span class="w-nav-arrow"></span>';
			$item_output .= $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>";
	}
}

add_filter( 'wp_nav_menu_objects', 'us_dropdown_wp_nav_menu_objects' );
function us_dropdown_wp_nav_menu_objects( $sorted_menu_items ) {
	foreach ( $sorted_menu_items as $index => $item ) {

		// IF it is a first level item or if it is a fake last item
		if ( $item->menu_item_parent == 0 ) {
			$dropdown_settings = get_post_meta( $item->ID, 'us_mega_menu_settings', TRUE );

			if ( is_array( $dropdown_settings ) ) {

				// Set columns value
				if ( ! empty( $dropdown_settings['columns'] ) AND intval( $dropdown_settings['columns'] ) > 1 ) {
					$item->mega_menu_cols = intval( $dropdown_settings['columns'] );
				}

				// Set mobile dropdown behavior
				if ( ! empty( $dropdown_settings['override_settings'] ) AND $dropdown_settings['override_settings'] ) {
					$item->mobile_behavior = $dropdown_settings['mobile_behavior'];
				}
			}

			$sorted_menu_items[$index] = $item;
		}
	}

	return $sorted_menu_items;
}

// Add fallback menu location, which can be used in plugins
add_action( 'init', 'register_us_menu' );
function register_us_menu() {
	register_nav_menus(
		array(
			'us_main_menu' => __( 'Custom Menu', 'us' ),
		)
	);
}
