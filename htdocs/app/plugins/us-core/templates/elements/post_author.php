<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Author element
 *
 * @var $link string Link type: 'post' / 'author' / 'custom' / 'none'
 * @var $custom_link array
 * @var $color string Custom color
 * @var $icon string Icon name
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_object_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() AND ! is_author() ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
if ( $color_link ) {
	$classes .= ' color_link_inherit';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

$classes .= ' vcard author'; // needed for Google structured data

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

$author_id = get_the_author_meta( 'ID' );

// Generate anchor semantics
$link_start_tag = $link_end_tag = '';
if ( $link === 'author_page' ) {
	$link_atts = ' href="' . get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename' ) ) . '"';
} elseif ( $link === 'author_website' ) {
	$link_atts = ' href="' . get_the_author_meta('url') . '" target="_blank" rel="noopener nofollow"';
} elseif ( $link === 'post' ) {
	$link_atts = ' href="' . apply_filters( 'the_permalink', get_permalink() ) . '"';
	if ( get_post_format() == 'link' ) {
		$link_atts .= ' target="_blank" rel="noopener"';
	}
} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );
} else {
	$link_atts = '';
}
if ( ! empty( $link_atts ) ) {
	$link_start_tag = '<a class="fn"' . $link_atts . ' aria-hidden="true">';
	$link_end_tag = '</a>';
}

if ( $avatar ) {
	$classes .= ' with_ava avapos_' . $avatar_pos;
}

// Output the element
$output = '<div class="w-post-elm post_author' . $classes . '"' . $el_id . '>';
$output .= us_prepare_icon_tag( $icon );

// Avatar
if ( $avatar ) {
	$args = array(
		'force_display' => TRUE, // always show avatar
	);
	$output .= $link_start_tag;
	$output .= '<div class="post-author-ava"' . us_prepare_inline_css( array( 'font-size' => $avatar_width ) ) . '>';
	$avatar_width = intval( $avatar_width );
	$output .= get_avatar( $author_id, $avatar_width, NULL, '', $args );
	$output .= '</div>';
	$output .= $link_end_tag;
}

$output .= '<div class="post-author-meta">';

// Name
if ( ! empty( $link_atts ) ) {
	$output .= '<a class="post-author-name fn"' . $link_atts . '>' . get_the_author() . '</a>';
} else {
	$output .= '<div class="post-author-name">' . get_the_author() . '</div>';
}

if ( ! empty( $link_atts ) ) {
	$output .= '</a>';
}

// Posts count
if ( $posts_count ) {
	$author_posts_amount = count_user_posts( $author_id, 'post', TRUE );
	$output .= '<div class="post-author-posts">';
	$output .= sprintf( _n( '%s post', '%s posts', $author_posts_amount, 'us' ), $author_posts_amount );
	$output .= '</div>';
}
// Website
if ( $website AND $website_url = get_the_author_meta( 'url' ) ) {
	$output .= '<a class="post-author-website" href="' . $website_url . '" target="_blank" rel="noopener nofollow">';
	$output .= $website_url;
	$output .= '</a>';
}
// Bio Info
if ( $info AND $info_text = get_the_author_meta( 'description' ) ) {
	$output .= '<div class="post-author-info">' . $info_text . '</div>';
}

$output .= '</div>';
$output .= '</div>';

echo $output;
