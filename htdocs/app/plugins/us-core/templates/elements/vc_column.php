<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_column
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode      string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content        string Shortcode's inner content
 * @var $classes        string Extend class names
 *
 * @var $width          string Width in format: 1/2 (is set by WPBakery Page Builder renderer)
 * @var $text_color     string Text color
 * @var $animate        string Animation type: '' / 'fade' / 'afc' / 'afl' / 'afr' / 'afb' / 'aft' / 'hfc' / 'wfc'
 * @var $animate_delay  float Animation delay (in seconds)
 * @var $el_id          string element ID
 * @var $el_class       string Additional class
 * @var $offset         string WPBakery Page Builder classes for responsive behaviour
 * @var $css            string Custom CSS
 */

$inner_classes = $el_id_string = $link_html = $wrapper_inline_css = '';

if ( function_exists( 'wpb_translateColumnWidthToSpan' ) ) {
	$width = wpb_translateColumnWidthToSpan( $width );

} elseif ( function_exists( 'us_wpb_translateColumnWidthToSpan' ) ) {
	$width = us_wpb_translateColumnWidthToSpan( $width );
}

if ( function_exists( 'vc_column_offset_class_merge' ) ) {
	$width = vc_column_offset_class_merge( $offset, $width );

} elseif ( function_exists( 'us_vc_column_offset_class_merge' ) ) {
	$width = us_vc_column_offset_class_merge( $offset, $width );
}
$classes = isset( $classes ) ? ( $classes . ' ' ) : '';
$classes .= $width . ' wpb_column vc_column_container';

// Move us_custom_* class to external container
if ( preg_match( '/(\sus_custom_\w+)\s/', $classes, $matches ) ) {
	$classes = str_replace( $matches[0], '', $classes );
	$inner_classes .= $matches[0];
}

// When bg color or border is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, array( 'background-color', 'background-image' ) ) ) {
	$classes .= ' has-fill';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

if ( ! empty( $animate ) ) {
	$classes .= ' animate_' . $animate;
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Link
if ( $link_atts = us_generate_link_atts( $link ) ) {
	$classes .= ' has-link';
	$link_html = '<a class="vc_column-link smooth-scroll"' . $link_atts . '></a>';
}

// Sticky Column
if ( $sticky ) {
	$inner_classes .= ' type_sticky';
	$wrapper_inline_css = us_prepare_inline_css( array( 'top' => $sticky_pos_top ) );
}

// Stretched Column
if ( $stretch ) {
	$classes .= ' stretched';
}

$inline_css = us_prepare_inline_css(
	array(
		'animation-delay' => empty( $animate_delay ) ? '' : floatval( $animate_delay ) . 's',
	)
);

// Output the element
$output = '<div class="' . $classes . '"' . $el_id . $inline_css . '>';
$output .= '<div class="vc_column-inner' . $inner_classes . '">';
$output .= '<div class="wpb_wrapper"' . $wrapper_inline_css . '>' . do_shortcode( $content ) . '</div>';
$output .= $link_html;
$output .= '</div></div>';

echo $output;
