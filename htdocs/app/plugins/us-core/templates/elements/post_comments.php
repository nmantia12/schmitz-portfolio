<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Comments
 */

if ( is_admin() AND ( ! defined( 'DOING_AJAX' ) OR ! DOING_AJAX ) ) {
	return;
}

global $us_grid_object_type;

// Cases when the Comments shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() ) {
	return;
} elseif ( get_post_format() == 'link' ) {
	return;
} elseif ( ! ( ( get_post() AND comments_open() ) OR get_comments_number() ) ) {
	return;
}

// Exclude 'comments_template' layout for Grid context
if ( $us_elm_context != 'shortcode' ) {
	$layout = 'amount';
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ' layout_' . $layout;

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

if ( $layout == 'amount' ) {

	// Link
	if ( $link === 'post' ) {
		if ( get_post_type() == 'product' ) {
			$link_atts = ' href="' . apply_filters( 'the_permalink', get_permalink() ) . '#reviews"';
		} else {
			$link_atts = ' href="' . get_comments_link() . '"';
		}
	} elseif ( $link === 'custom' ) {
		$link_atts = us_generate_link_atts( $custom_link );
	} else {
		$link_atts = '';
	}
	if ( $color_link ) {
		$classes .= ' color_link_inherit';
	}

	// When text color is set in Design Options, add the specific class
	if ( us_design_options_has_property( $css, 'color' ) ) {
		$classes .= ' has_text_color';
	}

	// Define no comments indication
	$comments_none = '0';
	if ( ! $number ) {
		$classes .= ' with_word';
		$comments_none = us_translate( 'No Comments' );
	}

	$comments_number = get_comments_number();

	// "Hide this element if no comments"
	if ( $hide_zero AND empty( $comments_number ) ) {
		return;
	}
}

// Output the element
$output = '<div class="w-post-elm post_comments' . $classes . '"' . $el_id . '>';

if ( $layout == 'comments_template' ) {
	wp_enqueue_script( 'comment-reply' );

	ob_start();
	comments_template();
	$output .= ob_get_clean();

} else {
	if ( ! empty( $icon ) ) {
		$output .= us_prepare_icon_tag( $icon );
	}
	if ( ! empty( $link_atts ) ) {
		$output .= '<a class="smooth-scroll"' . $link_atts . '>';
	}

	if ( class_exists( 'woocommerce' ) AND get_post_type() == 'product' ) {
		$output .= sprintf( us_translate_n( '%s customer review', '%s customer reviews', $comments_number, 'woocommerce' ), '<span class="count">' . esc_html( $comments_number ) . '</span>' );
	} else {
		ob_start();
		$comments_label = sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $comments_number ), $comments_number );
		comments_number( $comments_none, $comments_label, $comments_label );
		$output .= ob_get_clean();
	}

	if ( ! empty( $link_atts ) ) {
		$output .= '</a>';
	}
}

$output .= '</div>';

echo $output;
