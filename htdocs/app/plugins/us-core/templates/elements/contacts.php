<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_contacts
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param  $address		 string Addresss
 * @param  $phone		 string Phone
 * @param  $fax			 string Fax
 * @param  $email		 string Email
 * @param  $el_class	 string Extra class name
 */



$classes = isset( $classes ) ? $classes : '';

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Output the element
$output = '<div class="w-contacts' . $classes . '"' . $el_id . '><div class="w-contacts-list">';
if ( ! empty( $address ) ) {
	$output .= '<div class="w-contacts-item for_address"><span class="w-contacts-item-value">' . $address . '</span></div>';
}
if ( ! empty( $phone ) ) {
	$output .= '<div class="w-contacts-item for_phone"><span class="w-contacts-item-value">' . $phone . '</span></div>';
}
if ( ! empty( $fax ) ) {
	$output .= '<div class="w-contacts-item for_fax"><span class="w-contacts-item-value">' . $fax . '</span></div>';
}
if ( ! empty( $email ) ) {
	$output .= '<div class="w-contacts-item for_email"><span class="w-contacts-item-value">';
	$output .= '<a href="mailto:' . $email . '">' . $email . '</a></span></div>';
}
$output .= '</div></div>';

echo $output;
