<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Horizontal Wrapper
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ' align_' . $alignment;
$classes .= ' valign_' . $valign;
$classes .= ( $wrap ) ? ' wrap' : '';

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

$style_attr = '';
if ( trim( $inner_items_gap ) != '1.2rem' ) {
	$style_attr = ' style="--hwrapper-gap: ' . esc_attr( $inner_items_gap ) . '"';
}

// Output the element
$output = '<div class="w-hwrapper' . $classes . '"' . $el_id . $style_attr . '>';
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
