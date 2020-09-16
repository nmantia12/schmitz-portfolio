<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Title element
 *
 * @var $link string Link type: 'post' / 'custom' / 'none'
 * @var $custom_link array
 * @var $tag string 'h1' / 'h2' / 'h3' / 'h4' / 'h5' / 'h6' / 'p' / 'div'
 * @var $color string Custom color
 * @var $icon string Icon name
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 */

// Do not display a Post title, when it's being output as shortcode inside of grid item (e.g. via Post Content element)
global $us_grid_listing_outputs_items;
if ( ! empty( $us_grid_listing_outputs_items ) AND $us_elm_context == 'shortcode' ) {
	return;
}

// Overriding the type of an object based on the availability of terms
global $us_grid_object_type;
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	global $us_grid_term;
	$title = $us_grid_term->name;

} elseif ( $us_elm_context == 'shortcode' ) {

	// Get title based on page type
	if ( is_home() ) {
		if ( ! is_front_page() ) {
			// Get Posts Page Title
			$title = get_the_title( get_option( 'page_for_posts', TRUE ) );
		} else {
			$title = us_translate( 'All Posts' );
		}
	} elseif ( is_search() ) {
		$title = sprintf( us_translate( 'Search results for &#8220;%s&#8221;' ), get_search_query() );
	} elseif ( is_author() ) {
		$title = sprintf( us_translate( 'Posts by %s' ), get_the_author() );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', FALSE );
	} elseif ( is_category() ) {
		$title = single_cat_title( '', FALSE );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', FALSE );
	} elseif ( function_exists( 'is_shop' ) AND is_shop() ) {
		$title = woocommerce_page_title( '', FALSE );
	} elseif ( is_archive() ) {
		$title = get_the_archive_title();
	} elseif ( is_404() ) {
		$title = us_translate( 'Page not found' );
		// The Events Calendar
	} elseif ( $queried_object = get_queried_object() AND $queried_object->post_type === 'tribe_events' ) {
		$title = $queried_object->post_title;
	} else {
		$title = get_the_title();
	}

} else {
	$title = get_the_title();
}

$classes = isset( $classes ) ? $classes : '';

if ( $align != 'none' ) {
	$classes .= ' align_' . $align;
}
if ( $us_elm_context == 'grid' AND get_post_type() == 'product' ) {
	$classes .= ' woocommerce-loop-product__title'; // needed for adding to cart
} else {
	$classes .= ' entry-title'; // needed for Google structured data
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Link
if ( $link === 'none' ) {
	$link_atts = '';
} elseif ( $link === 'post' ) {

	// Terms of selected taxonomy in Grid
	if ( isset( $us_grid_term ) ) {
		$link_atts = ' href="' . get_term_link( $us_grid_term ) . '"';
	} else {
		$link_atts = ' href="' . apply_filters( 'the_permalink', get_permalink() ) . '"';

		// Force opening in a new tab for "Link" post format
		if ( get_post_format() == 'link' ) {
			$link_atts .= ' target="_blank" rel="noopener"';
		}
	}
} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );
} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link . '}}|||' );
}

// Extra class for link color
if ( ! empty( $link_atts ) AND $color_link ) {
	$classes .= ' color_link_inherit';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

// Schema.org markup
$schema_markup = '';
if ( us_get_option( 'schema_markup' ) AND $us_elm_context == 'shortcode' ) {
	$schema_markup = ' itemprop="headline"';
}

// Output the element
$output = '<' . $tag . ' class="w-post-elm post_title' . $classes . '"' . $el_id . $schema_markup . '>';

if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}

// Force "Open in a new tab" attributes
if ( $link_new_tab AND strpos( $link_atts, 'target="_blank"' ) === FALSE ) {
	$link_atts .= ' target="_blank" rel="noopener nofollow"';
}

if ( ! empty( $link_atts ) ) {
	$output .= '<a' . $link_atts . '>';
}

$output .= wptexturize( $title );

if ( ! empty( $link_atts ) ) {
	$output .= '</a>';
}
$output .= '</' . $tag . '>';

echo $output;
