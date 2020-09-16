<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output cart element
 *
 * @var $icon           int
 * @var $dropdown_effect string Dropdown Effect
 * @var $icon_size      int
 * @var $design_options array
 * @var $classes        string
 * @var $id             string
 */

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$classes .= ' dropdown_' . $dropdown_effect;

if ( $hide_empty ) {
	$classes .= ' hide_empty';
}
if ( $vstretch ) {
	$classes .= ' height_full';
}

$quantity_inline_css = us_prepare_inline_css(
	array(
		'background' => us_get_color( $quantity_color_bg, /* Gradient */ TRUE ),
		'color' => us_get_color( $quantity_color_text ),
	)
);

echo '<div class="w-cart' . $classes . ' empty">';
echo '<div class="w-cart-h">';
echo '<a class="w-cart-link" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="' . us_translate( 'Cart', 'woocommerce' ) . '">';
echo '<span class="w-cart-icon">';

if ( ! empty( $icon ) ) {
	echo us_prepare_icon_tag( $icon );
}

echo '<span class="w-cart-quantity"' . $quantity_inline_css . '></span></span></a>';
echo '<div class="w-cart-notification"><div>';
echo sprintf( us_translate_n( '%s has been added to your cart.', '%s have been added to your cart.', 1, 'woocommerce' ), '<span class="product-name">' . us_translate( 'Product', 'woocommerce' ) . '</span>' );
echo '</div></div>';
echo '<div class="w-cart-dropdown">';

the_widget( 'WC_Widget_Cart', 'title=0' ); // This widget being always filled with products via AJAX

echo '</div>';
echo '</div>';
echo '</div>';
