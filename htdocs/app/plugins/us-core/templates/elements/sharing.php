<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_sharing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $type             string Type: 'simple' / 'solid' / 'outlined' / 'fixed'
 * @param $align         string Alignment: 'left' / 'center' / 'right'
 * @param $color         string Color Style: 'default' / 'primary' / 'secondary'
 * @param $counters         string Share Counters: 'show' / 'hide'
 * @param $email         bool Is Email button available?
 * @param $facebook         bool Is Facebook button available?
 * @param $twitter         bool Is Twitter button available?
 * @param $gplus         bool Is Google button available?
 * @param $linkedin         bool Is LinkedIn button available?
 * @param $pinterest     bool Is Pinterest button available?
 * @param $vk             bool Is VK button available?
 * @param $url             string Sharing URL
 * @param $el_class         string Extra class name
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ' type_' . $type;
$classes .= ' align_' . $align;
$classes .= ' color_' . $color;

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// The list of available sharing providers and additional in-shortcode data
$providers_list = array(
	'email' => array(
		'title' => __( 'Email this', 'us' ),
	),
	'facebook' => array(
		'title' => __( 'Share this', 'us' ),
	),
	'twitter' => array(
		'title' => __( 'Tweet this', 'us' ),
	),
	'linkedin' => array(
		'title' => __( 'Share this', 'us' ),
	),
	'pinterest' => array(
		'title' => __( 'Pin this', 'us' ),
	),
	'vk' => array(
		'title' => __( 'Share this', 'us' ),
	),
	'whatsapp' => array(
		'title' => __( 'Share this', 'us' ),
	),
	'xing' => array(
		'title' => __( 'Share this', 'us' ),
	),
	'reddit' => array(
		'title' => __( 'Share this', 'us' ),
	),
);

$set_providers = explode( ',', $providers );

// Keeping only the actually used providers
foreach ( $providers_list as $provider => $provider_data ) {
	if ( ! in_array( $provider, $set_providers ) ) {
		unset( $providers_list[ $provider ] );
	}
}
if ( empty( $providers_list ) ) {
	return;
}

if ( empty( $url ) ) {
	// Using the current page URL
	$url  = wp_parse_url( home_url(), PHP_URL_SCHEME ) . '://';
	$url .= wp_parse_url( home_url(), PHP_URL_HOST );
	$url .= str_replace( '?us_iframe=1', '', $_SERVER['REQUEST_URI'] );
}

if ( $counters == 'show' ) {
	$counts = us_get_sharing_counts( $url, array_keys( $providers_list ) );
}

$post_thumbnail = get_the_post_thumbnail_url( NULL, 'large' );
$post_thumbnail = ( $post_thumbnail ) ? $post_thumbnail : '';

// Output the element
$output = '<div class="w-sharing' . $classes . '"' . $el_id . '>';
$output .= '<div class="w-sharing-list" data-sharing-url="' . esc_attr( $url ) . '" data-sharing-image="' . esc_attr( $post_thumbnail ) . '">';
$sharing_list = '';
foreach ( $providers_list as $provider => $provider_data ) {
	$sharing_list .= '<a class="w-sharing-item ' . $provider . '" href="javascript:void(0)"';
	$sharing_list .= ' title="' . esc_attr( $provider_data['title'] ) . '"';
	$sharing_list .= ' aria-label="' . esc_attr( $provider_data['title'] ) . '"';
	$sharing_list .= '>';
	$sharing_list .= '<span class="w-sharing-icon"></span>';
	if ( $counters == 'show' AND isset( $counts[ $provider ] ) AND $counts[ $provider ] != 0 ) {
		$sharing_list .= '<span class="w-sharing-count">' . $counts[ $provider ] . '</span>';
	}
	$sharing_list .= '</a>';
}
$output .= $sharing_list;
$output .= '</div>';

if ( $text_selection ) {
	$sharing_area = ( $text_selection_post ) ? 'post_content' : 'l-main';
	$output .= '<div class="w-sharing-tooltip" style="display: none" data-sharing-area="' . $sharing_area . '">';
	$output .= '<div class="w-sharing-list" data-sharing-url="' . esc_attr( $url ) . '" data-sharing-image="' . esc_attr( $post_thumbnail ) . '">';
	$output .= $sharing_list;
	$output .= '<a class="w-sharing-item copy2clipboard" href="javascript:void(0)"';
	$output .= ' title="' . us_translate( 'Copy' ) . '"';
	$output .= ' aria-label="' . us_translate( 'Copy' ) . '"';
	$output .= '>';
	$output .= '<span class="w-sharing-icon"></span></a>';
	$output .= '</div></div>';
}

$output .= '</div>';

echo $output;
