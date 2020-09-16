<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Product ordering
 *
 * $type
 *
 */
if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Output the element
echo '<div class="w-post-elm product_ordering' . $classes . '"' . $el_id . '>';
if ( function_exists( 'woocommerce_catalog_ordering' ) ) {
	woocommerce_catalog_ordering();
}
echo '</div>';
