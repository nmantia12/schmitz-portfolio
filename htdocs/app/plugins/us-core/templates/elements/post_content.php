<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Content element
 *
 * @var $type string Show: 'excerpt_only' / 'excerpt_content' / 'part_content' / 'full_content'
 * @var $length int Amount of words
 * @var $design_options array
 * @var bool $show_more_toggle
 * @var string $show_more_toggle_height
 *
 * @var $classes string
 * @var $id string
 */

if ( get_post_format() == 'link' OR ( is_admin() AND ( ! defined( 'DOING_AJAX' ) OR ! DOING_AJAX ) ) ) {
	return;
}

// Calculate amount of usage the element with full content to avoid infinite recursion
global $us_full_content_stack;
if ( isset( $us_full_content_stack ) AND $us_full_content_stack > 10 AND $type == 'full_content' ) {
	die( '<h5 style="text-align:center; margin-top:20vh; padding:5%;">Post Content outputs itself infinitely. Fix layout of this page.</h5>' );
}

// Find Post Image element with media preview in Page Block
global $us_page_block_ids;
$strip_from_the_content = FALSE;
if ( ! empty( $us_page_block_ids ) ) {
	$page_block = get_post( $us_page_block_ids[0] );

	// Find Post Image element
	if ( preg_match( '~\[us_post_image.+media_preview="1".+?\]~', $page_block->post_content ) ) {
		$strip_from_the_content = TRUE;
	}
}

us_add_to_page_block_ids( get_the_ID() );

if ( $type == 'full_content' ) {
	$us_full_content_stack = ( empty( $us_full_content_stack ) ) ? 1 : $us_full_content_stack + 1;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';

$post_elm_atts = array();
if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode'  ) {
	$post_elm_atts['id'] = esc_attr( $el_id );
}

// Default case
$the_content = '';

global $us_grid_object_type;
// Get term description as "Excerpt" for Grid terms
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	global $us_grid_term;
	$the_content = $us_grid_term->description;

	// Limit the amount of words for the Excerpt
	if ( intval( $excerpt_length ) > 0 ) {
		$the_content = wp_trim_words( $the_content, intval( $excerpt_length ) );
	}

	// Get term description as "Excerpt" for archive pages
} elseif ( $us_elm_context == 'shortcode' AND ( is_category() OR is_tag() OR is_tax() ) ) {
	$the_content = do_shortcode( term_description() );

	// Post excerpt is not empty
} elseif ( in_array( $type, array( 'excerpt_content', 'excerpt_only' ) ) AND has_excerpt() ) {
	$the_content = do_shortcode( apply_filters( 'the_excerpt', get_the_excerpt() ) );

	// Limit the amount of words for the Excerpt
	if ( intval( $excerpt_length ) > 0 ) {
		$the_content = wp_trim_words( $the_content, intval( $excerpt_length ) );
	}

	// Either the excerpt is empty and we show the content instead or we show the content only
} elseif ( in_array( $type, array( 'excerpt_content', 'part_content', 'full_content' ) ) ) {
	global $us_is_search_page_block;

	if ( get_post_type() == 'attachment' ) {
		$the_content = get_the_content();
	} else {

		// Get WooCommerce Shop Page content
		if ( function_exists( 'is_shop' ) AND is_shop() AND $us_elm_context == 'shortcode' ) {

			if ( ! is_search() AND $shop_page = get_post( wc_get_page_id( 'shop' ) ) ) {
				$the_content = $shop_page->post_content;
			}

		} elseif ( ! empty( $us_is_search_page_block ) AND $us_elm_context == 'shortcode' AND $search_page = get_post( us_get_option( 'search_page' ) ) ) {
			if ( class_exists( 'SitePress' ) ) {
				$search_page = get_post( apply_filters( 'wpml_object_id', $search_page->ID, 'page', TRUE ) );
			}

			// Replacing last post ID at page blocks stack with actual search page template ID
			us_remove_from_page_block_ids();
			us_add_to_page_block_ids( $search_page->ID );

			$the_content = $search_page->post_content;
			$us_is_search_page_block = FALSE;

		} elseif ( is_404() AND $us_elm_context == 'shortcode' AND $page_404 = get_post( us_get_option( 'page_404' ) ) ) {
			if ( class_exists( 'SitePress' ) ) {
				$page_404 = get_post( apply_filters( 'wpml_object_id', $page_404->ID, 'page', TRUE ) );
			}
			$the_content = $page_404->post_content;

		} else {
			$the_content = get_the_content();
		}

		// Remove [vc_row] and [vc_column] if set
		if ( $remove_rows ) {
			$the_content = str_replace( array( '[vc_row]', '[/vc_row]', '[vc_column]', '[/vc_column]' ), '', $the_content );
			$the_content = preg_replace( '~\[vc_row (.+?)]~', '', $the_content );
			$the_content = preg_replace( '~\[vc_column (.+?)]~', '', $the_content );

		// Force fullwidth for all [vc_row] if set
		} elseif ( $force_fullwidth_rows ) {
			$the_content = str_replace( '[vc_row]', '[vc_row width="full"]', $the_content );
			$the_content = str_replace( '[vc_row ', '[vc_row width="full" ', $the_content );
		}

		// Check enabled option show image title and description
		if ( ! $strip_from_the_content AND preg_match( '/\[us_image_slider.+meta="1[^\]]\]/', $the_content ) ) {
			$strip_from_the_content = TRUE;
		}

		// Remove video, audio, slider, gallery from the content for relevant post formats
		us_get_post_preview( $the_content, $strip_from_the_content );

		$the_content = apply_filters( 'the_content', $the_content );

		// Limit the amount of words for the Content
		if ( in_array( $type, array( 'excerpt_content', 'part_content' ) ) AND intval( $length ) > 0 ) {
			$the_content = wp_trim_words( $the_content, intval( $length ) );
		}
	}
}

// Add pagination for Full Content only
if ( $type == 'full_content' ) {
	$the_content .= us_wp_link_pages();
}

// Schema.org markup
if ( us_get_option( 'schema_markup' ) AND $us_elm_context == 'shortcode' ) {
	$post_elm_atts['itemprop'] = 'text';
}

// Add specific class, when "Show More" is enabled
if ( $show_more_toggle ) {
	$classes .= ' with_show_more_toggle';
	$post_elm_atts[ 'data-toggle-height' ] = esc_attr( $show_more_toggle_height );
}

// Output the element
$output = '<div class="w-post-elm post_content' . $classes . '" ' . us_implode_atts( $post_elm_atts ) . '>';

// Additional <div>, when "Show More" is enabled
if ( $show_more_toggle ) {
	$output .= '<div>';
}

$output .= $the_content;

if ( $show_more_toggle ) {
	$output .= '</div>';
	$output .= '<div class="toggle-links align_' . $show_more_toggle_alignment . '">';
	$output .= '<a href="javascript:void(0)" class="toggle-show-more">' . strip_tags( $show_more_toggle_text_more ) . '</a>';
	$output .= '<a href="javascript:void(0)" class="toggle-show-less">' . strip_tags( $show_more_toggle_text_less ) . '</a>';
	$output .= '</div>';
}
$output .= '</div>';

if ( $type == 'full_content' ) {
	$us_full_content_stack --;
}
us_remove_from_page_block_ids();

// Output empty string for "Link" post format OR when no content
// Do not remove to avoid bug https://github.com/upsolution/wp/issues/407
if ( $the_content == '' ) {
	return;
} else {
	echo $output;
}
