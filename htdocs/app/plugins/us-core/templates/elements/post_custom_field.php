<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Custom Field element
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_object_type;

if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	global $us_grid_term;
	$term = $us_grid_term;
	$postID = NULL;
} elseif ( $us_elm_context == 'shortcode' AND ( is_tax() OR is_tag() OR is_category() ) ) {
	$term = get_queried_object();
	$postID = NULL;
} elseif (
	is_404()
	AND ( $page_404_ID = us_get_option( 'page_404' ) ) !== 'default'
) {
	$postID = $page_404_ID;
	$term = NULL;
} elseif (
	is_search()
	AND ( $search_page_ID = us_get_option( 'search_page' ) ) !== 'default'
) {
	$postID = $search_page_ID;
	$term = NULL;
} else {
	$postID = get_the_ID();
	$term = NULL;
}

global $us_predefined_post_custom_fields;
$value = '';
$type = 'text';

// Force type for specific meta keys
if ( $key == 'us_tile_additional_image' ) {
	$type = 'image';
}
if ( $key == 'us_tile_icon' ) {
	$type = 'icon';
}

// Get the value from custom field
if ( $key == 'custom' ) {

	if ( ! empty( $custom_key ) ) {
		if ( $postID ) {
			$value = get_post_meta( $postID, $custom_key, TRUE );
		} elseif ( $term ) {
			$value = get_term_meta( $term->term_id, $custom_key, TRUE );
		}
	}

} elseif ( ! in_array( $key, array_keys( $us_predefined_post_custom_fields ) ) ) {

	// Get ACF value
	if ( function_exists( 'get_field_object' ) ) {
		if ( $postID ) {
			$acf_obj = get_field_object( $key, $postID );
			$value = us_arr_path( $acf_obj, 'value', '' );

			// Force "image" type
			// TODO: Add support for link
			if ( isset( $acf_obj['type'] ) AND $acf_obj['type'] == 'image' ) {
				$type = 'image';
			} elseif ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
		} elseif ( $term ) {
			$value = get_field( $key, $term );

			if ( is_array( $value ) ) {
				// TODO: Add support for link
				if ( isset( $value['type'] ) AND $value['type'] == 'image' ) {
					$type = 'image';
				} else {
					$value = implode( ', ', $value );
				}
			}
		}
	}

} else {
	if ( $postID ) {
		$value = get_post_meta( $postID, $key, TRUE );
	} elseif ( $term ) {
		$value = get_term_meta( $term->term_id, $key, TRUE );
	}

	// Format the value
	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	} elseif ( $type == 'text' ) {
		$value = wpautop( $value ); // add <p> and <br> if custom field has WYSIWYG
	}
}

// Don't output the element, when its value is empty OR it's an object type
if ( ( $hide_empty AND empty( $value ) ) OR is_object( $value ) ) {
	return;
}

// Generate image semantics
if ( $type == 'image' ) {
	global $us_grid_img_size;
	if ( ! empty( $us_grid_img_size ) AND $us_grid_img_size != 'default' ) {
		$thumbnail_size = $us_grid_img_size;
	}

	// Format the value to get image ID
	$value = is_array( $value ) ? $value['id'] : intval( $value );

	$value = wp_get_attachment_image( $value, $thumbnail_size );
	if ( empty( $value ) ) {
		$value = us_get_img_placeholder( $thumbnail_size );
	}
}

// Generate special semantics for Testimonial Rating
if ( $key == 'us_testimonial_rating' ) {
	$rating_value = (int) strip_tags( $value );

	if ( $rating_value == 0 ) {
		return;
	} else {
		$value = '<div class="w-testimonial-rating">';
		for ( $i = 1; $i <= $rating_value; $i ++ ) {
			$value .= '<i></i>';
		}
		$value .= '</div>';
	}
}

// Generate icon specific semantics
if ( $key == 'us_tile_icon' ) {
	$value = us_prepare_icon_tag( $value );
}

// Text before value
$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Text after value
$text_after = ( trim( $text_after ) != '' ) ? ' <span class="w-post-elm-after">' . trim( $text_after ) . ' </span>' : '';

// Link
if ( $link === 'none' ) {
	$link_atts = '';
} elseif ( $link === 'post' ) {
	if ( $postID ) {
		$link_atts = ' href="' . apply_filters( 'the_permalink', get_permalink() ) . '"';
		if ( get_post_format() == 'link' ) {
			$link_atts .= ' target="_blank" rel="noopener"';
		}
	} elseif ( $term ) {
		$link_atts = ' href="' . get_term_link( $term ) . '"';
	}
} elseif ( $link === 'elm_value' AND ! empty( $value ) ) {
	if ( is_email( $value ) ) {
		$link_atts = ' href="mailto:' . $value . '"';
	} else {
		$link_atts = ' href="' . esc_url( $value ) . '"';
	}
} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );
} elseif ( $link === 'onclick' ) {
	$onclick_code = ! empty( $onclick_code ) ? $onclick_code : 'return false';
	$link_atts = ' href="#" onclick="' . esc_js( trim( $onclick_code ) ) . '"';
} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link . '}}|||' );
}

// Force "Open in a new tab" attributes
if ( $link_new_tab AND strpos( $link_atts, 'target="_blank"' ) === FALSE ) {
	$link_atts .= ' target="_blank" rel="noopener nofollow"';
}

// CSS classes & ID
$classes = isset( $classes ) ? $classes : '';
$classes .= ' type_' . $type;
if ( $link != 'none' AND $color_link ) {
	$classes .= ' color_link_inherit';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Output the element
$output = '<' . $tag . ' class="w-post-elm post_custom_field' . $classes . '"' . $el_id . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before;

if ( ! empty( $link_atts ) ) {
	$output .= '<a' . $link_atts . '>';
}
$output .= $value;
if ( ! empty( $link_atts ) ) {
	$output .= '</a>';
}
$output .= $text_after;
$output .= '</' . $tag . '>';

echo $output;
