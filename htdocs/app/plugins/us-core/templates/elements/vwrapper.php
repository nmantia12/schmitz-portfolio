<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Vertical Wrapper
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ' align_' . $alignment;
$classes .= ' valign_' . $valign;
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

$style_attr = '';
if ( trim( $inner_items_gap ) != '0.7rem' ) {
	$style_attr = ' style="--vwrapper-gap: ' . esc_attr( $inner_items_gap ) . '"';
}

// Output the element
$output = '<div class="w-vwrapper' . $classes . '"' . $el_id . $style_attr . '>';
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
