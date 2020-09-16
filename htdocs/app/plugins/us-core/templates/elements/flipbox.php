<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Flipbox
 */

$front_inline_css = $back_inline_css = array();

// When rotating cubetilt in diagonal direction, we're actually doing a cube flip animation instead
if ( in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
	if ( $animation == 'cubetilt' ) {
		$animation = 'cubeflip';
	}
	if ( $animation == 'cubeflip' AND $link_type == 'btn' ) {
		$direction = 'n'; // disable diagonal directions, when back side has a button
	}
}

// Main element classes
$classes = isset( $classes ) ? $classes : '';
$classes .= ' animation_' . $animation;
$classes .=' direction_' . $direction;

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

// Extract some properties from "css" attribute to use them inside flipbox for correct appearance
if ( ! empty( $css ) AND $jsoncss = json_decode( urldecode( $css ), TRUE ) ) {
	$jsoncss = us_arr_path( $jsoncss, 'default', array() );
	$moved_props = array(
		'height',
		'border-style',
		'border-bottom-width',
		'border-left-width',
		'border-right-width',
		'border-top-width',
		'border-color',
		'padding-bottom',
		'padding-left',
		'padding-right',
		'padding-top',
	);
	foreach ( $moved_props as $prop ) {
		if ( isset( $jsoncss[ $prop ] ) AND $jsoncss[ $prop ] != '' ) {
			$front_inline_css[ $prop ] = $back_inline_css[ $prop ] = trim( $jsoncss[ $prop ] );
		}
	}
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Link
$tag = 'div';
$btn_html = '';
$link_atts = us_generate_link_atts( $link );

// Check existence of Button Style, if not, set the default
$btn_styles = us_get_btn_styles();
if ( ! array_key_exists( $btn_style, $btn_styles ) ) {
	$btn_style = '1';
}

if ( ! empty( $link_atts ) ) {
	if ( $link_type == 'container' ) {
		$tag = 'a';
	} elseif ( $link_type == 'btn' ) {
		$btn_html .= '<a class="w-btn us-btn-style_' . $btn_style . '"';
		$btn_html .= $link_atts;
		$btn_html .= us_prepare_inline_css( array( 'font-size' => $btn_size ) );
		$btn_html .= '>';
		$btn_html .= '<span>' . strip_tags( $btn_label ) . '</span>';
		$btn_html .= '</a>';
	}
}

// Output the element
$output = '<' . $tag . ' class="w-flipbox' . $classes . '"' . $el_id . $link_atts . '>';
$helper_classes = ' easing_' . $easing;
$helper_inline_css = us_prepare_inline_css(
	array(
		'transition-duration' => floatval( $duration ) . 's',
	)
);
$output .= '<div class="w-flipbox-h' . $helper_classes . '"' . $helper_inline_css . '>';
$output .= '<div class="w-flipbox-hh">';

if ( $animation == 'cubeflip' AND in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
	$output .= '<div class="w-flipbox-hhh">';
}

// Front Side
$front_inline_css['background'] = us_get_color( $front_bgcolor, /* Gradient */ TRUE );
$front_inline_css['color'] = us_get_color( $front_textcolor );

if ( $front_bgimage_src = wp_get_attachment_image_url( $front_bgimage, $front_bgimage_size ) ) {
	$front_inline_css['background-image'] = $front_bgimage_src;
}

$output .= '<div class="w-flipbox-front"' . us_prepare_inline_css( $front_inline_css ) . '><div class="w-flipbox-front-h">';
$output_front_icon = '';
if ( $front_icon_type == 'font' ) {
	$icon_inline_css = array(
		'font-size' => $front_icon_size,
		'background' => us_get_color( $front_icon_bgcolor, /* Gradient */ TRUE ),
		'color' => us_get_color( $front_icon_color ),
	);
	$output_front_icon .= '<div class="w-flipbox-front-icon style_' . $front_icon_style . '"' . us_prepare_inline_css( $icon_inline_css ) . '>';
	$output_front_icon .= us_prepare_icon_tag( $front_icon_name );
	$output_front_icon .= '</div>';
} elseif ( $front_icon_type == 'image' ) {
	$icon_inline_css = array(
		'width' => $front_icon_image_width,
	);

	$output_front_icon .= '<div class="w-flipbox-front-icon type_image"' . us_prepare_inline_css( $icon_inline_css ) . '>';
	$front_icon_image_html = wp_get_attachment_image( $front_icon_image, 'medium' );
	if ( empty( $front_icon_image_html ) ) {
		$front_icon_image_html = us_get_img_placeholder( 'medium' );
	}
	$output_front_icon .= $front_icon_image_html;
	$output_front_icon .= '</div>';
}
$output_front_title = '';
if ( ! empty( $front_title ) ) {
	$output_front_title .= '<' . $front_title_tag . ' class="w-flipbox-front-title"';
	$output_front_title .= us_prepare_inline_css(
		array(
			'font-size' => $front_title_size,
			'color' => us_get_color( $front_textcolor ),
		)
	);
	$output_front_title .= '>' . esc_html( $front_title ) . '</' . $front_title_tag . '>';
}
$output_front_desc = '';
if ( ! empty( $front_desc ) ) {
	$output_front_desc .= '<div class="w-flipbox-front-desc">' . wpautop( $front_desc ) . '</div>';
}
if ( $front_icon_pos == 'below_title' ) {
	$output .= $output_front_title . $output_front_icon . $output_front_desc;
} elseif ( $front_icon_pos == 'below_desc' ) {
	$output .= $output_front_title . $output_front_desc . $output_front_icon;
} else/*if ( $front_icon_pos == 'above_title' )*/ {
	$output .= $output_front_icon . $output_front_title . $output_front_desc;
}
$output .= '</div></div>';

// Back Side
$back_inline_css['display'] = 'none';
$back_inline_css['background'] = us_get_color( $back_bgcolor, /* Gradient */ TRUE );
$back_inline_css['color'] = us_get_color( $back_textcolor );

if ( $back_bgimage_src = wp_get_attachment_image_url( $back_bgimage, $back_bgimage_size ) ) {
	$back_inline_css['background-image'] = $back_bgimage_src;
}

$output .= '<div class="w-flipbox-back"' . us_prepare_inline_css( $back_inline_css ) . '><div class="w-flipbox-back-h">';
if ( ! empty( $back_title ) ) {
	$output .= '<' . $back_title_tag . ' class="w-flipbox-back-title"';
	$output .= us_prepare_inline_css(
		array(
			'font-size' => $back_title_size,
			'color' => us_get_color( $back_textcolor ),
		)
	);
	$output .= '>' . esc_html( $back_title ) . '</' . $back_title_tag . '>';
}
if ( ! empty( $back_desc ) ) {
	$output .= '<div class="w-flipbox-back-desc">' . wpautop( $back_desc ) . '</div>';
}
$output .= $btn_html;
$output .= '</div></div>';

// We need additional dom-elements for 'cubeflip' animations (:before / :after won't suit)
if ( $animation == 'cubeflip' ) {

	$front_bgcolor = ( ! empty( $front_bgcolor ) )
		? us_get_color( $front_bgcolor, /* Gradient */ TRUE )
		: us_get_color( 'color_content_bg_alt', TRUE );

	// Top & bottom flank with shaded color
	if ( in_array( $direction, array( 'ne', 'e', 'se', 'sw', 'w', 'nw' ) ) ) {
		$shaded_color = us_shade_color( us_get_color( $front_bgcolor, /* Gradient */ TRUE ) );
		$output .= '<div class="w-flipbox-yflank"' . us_prepare_inline_css( array( 'display' => 'none', 'background' => $shaded_color ) ) . '></div>';
	}

	// Left & right flank with shaded color
	if ( in_array( $direction, array( 'n', 'ne', 'se', 's', 'sw', 'nw' ) ) ) {
		$shaded_color = us_shade_color( us_get_color( $front_bgcolor, /* Gradient */ TRUE ), 0.1 );
		$output .= '<div class="w-flipbox-xflank"' . us_prepare_inline_css( array( 'display' => 'none', 'background' => $shaded_color ) ) . '></div>';
	}
}

if ( $animation == 'cubeflip' AND in_array( $direction, array( 'ne', 'se', 'sw', 'nw' ) ) ) {
	$output .= '</div>';
}

$output .= '</div></div>';
$output .= '</' . $tag . '>';

echo $output;
