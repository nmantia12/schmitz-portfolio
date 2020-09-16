<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_btn
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode      string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content        string Shortcode's inner content
 * @var $classes        string Extend class names
 *
 * @var $link           string Video link
 * @var $ratio          string Ratio: '16x9' / '4x3' / '3x2' / '1x1'
 * @var $max_width      string Max width in pixels
 * @var $align          string Video alignment: 'left' / 'center' / 'right'
 * @var $css            string Extra css
 * @var $el_id          string element ID
 * @var $el_class       string Extra class name
 * @var $source      string Iframe from custom field
 */

// If ACF field is chosen as a value then parse iframe, get link from value and replace with current
if ( ! empty( $source ) AND $source != 'custom' AND function_exists( 'get_field' ) ) {
	$value = get_field( $source, get_the_ID() );
	preg_match_all( '/src="([^"]+)"+/i', $value, $result );

	if ( ! empty( $result[1][0] ) ) {
		$url = explode( '?', $result[1][0] );
		if ( ! empty( $url[0] ) AND filter_var( $url[0], FILTER_VALIDATE_URL ) !== FALSE ) {
			$link = $url[0];
		}
	} else {
		return; // in case the ACF value is empty or doesn't contain "src", do not output the element
	}
}

$_atts['class'] = 'w-video';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' align_' . $align;
if ( ! empty( $ratio ) ) {
	$_atts['class'] .= ' ratio_' . $ratio;
}
if ( us_design_options_has_property( $css, 'border-radius' ) ) {
	$_atts['class'] .= ' has_border_radius';
}
if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Image Overlay
if ( $overlay_image ) {
	$_atts['class'] .= ' with_overlay';
	$_atts['style'] = sprintf( 'background-image:url(%s);', wp_get_attachment_image_url( $overlay_image, 'full' ) );
}

$embed_html = '';

foreach ( us_config( 'embeds' ) as $provider => $embed ) {
	if ( $embed['type'] != 'video' OR ! preg_match( $embed['regex'], $link, $matches ) ) {
		continue;
	}

	if ( $hide_controls AND $provider == 'youtube' ) {
		$video_title = '?controls=0';
	} elseif ( $hide_video_title AND $provider == 'vimeo' ) {
		$video_title = '&byline=0&title=0';
	} else {
		$video_title = '';
	}

	$video_id = $matches[ $embed['match_index'] ];
	$embed_html = str_replace( '<id>', $video_id, $embed['html'] );
	$embed_html = str_replace( '<video-title>', $video_title, $embed_html );

	break;
}

if ( empty( $embed_html ) ) {

	// Using the default WordPress way
	global $wp_embed;
	$embed_html = $wp_embed->run_shortcode( '[embed]' . $link . '[/embed]' );
}

$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-video-h">' . $embed_html . '</div>';

// Play icon
if ( $overlay_icon ) {
	$output .= '<div class="w-video-icon" style="';
	if ( ! empty( $overlay_icon_size ) ) {
		$output .= 'font-size:' . esc_attr( $overlay_icon_size ) . ';';
	}
	if ( ! empty( $overlay_icon_bg_color ) ) {
		$output .= 'background:' . us_get_color( $overlay_icon_bg_color, /* Gradient */ TRUE ) . ';';
	}
	if ( ! empty( $overlay_icon_text_color ) ) {
		$output .= 'color:' . us_get_color( $overlay_icon_text_color ) . ';';
	}
	$output .= '"></div>';
}
$output .= '</div>';

echo $output;
