<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Gravity Forms Support
 *
 * @link http://www.gravityforms.com/
 */

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_gravityforms_enqueue_styles', 14 );
}
function us_gravityforms_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	$min_ext = defined( 'US_DEV' ) ? '' : '.min';
	wp_enqueue_style( 'us-gravityforms', $us_template_directory_uri . '/common/css/plugins/gravityforms' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
}
