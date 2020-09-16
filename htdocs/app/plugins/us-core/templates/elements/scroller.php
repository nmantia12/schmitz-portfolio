<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_scroller
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param $speed          string Scroll Speed
 * @param $dots           bool Show navigation dots?
 * @param $dots_pos       string Dots Position
 * @param $dots_size      string Dots Size
 * @param $dots_color     string Dots color value
 * @param $disable_width  string Dots color value
 * @param $el_class       string Extra class name
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

$classes = isset( $classes ) ? $classes : '';
$classes = $data_atts = '';

$classes .= ' style_' . $dots_style . ' pos_' . $dots_pos;

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

if ( $speed != '' ) {
	$data_atts = ' data-speed="' . $speed . '"';
}
if ( $disable_width != '' ) {
	$data_atts .= ' data-disablewidth="' . intval( $disable_width ) . '"';
}

if ( $include_footer ) {
	$data_atts .= ' data-footer-dots="true"';
}

$dots_color = us_get_color( $dots_color );
$dot_inline_css = us_prepare_inline_css(
	array(
		'font-size' => $dots_size,
		'box-shadow' => empty( $dots_color ) ? '' : '0 0 0 2px ' . $dots_color,
		'background' => $dots_color,
	)
);

// Output the element
$output = '<div class="w-scroller' . $classes . '"' . $el_id . $data_atts . ' aria-hidden="true">';
if ( $dots ) {
	$output .= '<div class="w-scroller-dots">';
	$output .= '<a href="javascript:void(0);" class="w-scroller-dot"><span' . $dot_inline_css . '></span></a>';
	$output .= '</div>';
}
$output .= '</div>';

echo $output;
