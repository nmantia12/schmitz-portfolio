<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WPML Support
 *
 * @link https://wpml.org/
 */

if ( ! ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) ) ) {
	return;
}

// Add class to body in admin pages in non-default language
global $sitepress;
$default_language = $sitepress->get_default_language();

if ( $default_language != ICL_LANGUAGE_CODE ) {
	global $pagenow;
	// Exception: do not add class on Theme Options page
	if ( ! ( $pagenow == 'admin.php' AND ! empty( $_GET['page'] ) AND $_GET['page'] == 'us-theme-options' ) ) {
		function us_admin_add_wpml_nondefault_class( $class ) {
			return $class . ' us_wpml_non_default';
		}

		add_filter( 'admin_body_class', 'us_admin_add_wpml_nondefault_class' );
	} else {
		// For Theme Options page adding redirect to default language
		wp_redirect( admin_url() . 'admin.php?page=us-theme-options&lang=' . $default_language );
		die();
	}
}

// Remove select2 CSS to avoid overlapping with theme styles
add_action( 'admin_init', 'us_dequeue_wpml_select2' );
function us_dequeue_wpml_select2() {
	global $pagenow;

	if ( ( $pagenow == 'admin.php' AND isset( $_GET['page'] ) AND $_GET['page'] == 'us-theme-options' ) OR ( $pagenow === 'post.php' AND isset( $_GET['post'] ) AND ( get_post_type( $_GET['post'] ) === 'us_header' OR get_post_type( $_GET['post'] ) === 'us_grid_layout' ) ) ) {
		wp_dequeue_style( 'wpml-select-2' );
	}
}

// Add support for encoded shortcodes
add_filter( 'wpml_pb_shortcode_encode', 'wpml_pb_shortcode_encode_us_urlencoded_json', 10, 3 );
function wpml_pb_shortcode_encode_us_urlencoded_json( $string, $encoding, $original_string ) {
	if ( $encoding !== 'us_urlencoded_json' ) {
		return $string;
	}

	$output = array();
	foreach ( $original_string as $combined_key => $value ) {
		$parts = explode( '_', $combined_key );
		$i = array_pop( $parts );
		$key = implode( '_', $parts );
		$output[ $i ][ $key ] = $value;
	}

	return urlencode( json_encode( $output ) );

}

add_filter( 'wpml_pb_shortcode_decode', 'wpml_pb_shortcode_decode_us_urlencoded_json', 10, 3 );
function wpml_pb_shortcode_decode_us_urlencoded_json( $string, $encoding, $original_string ) {
	if ( $encoding !== 'us_urlencoded_json' ) {
		return $string;
	}

	$fields_to_translate = array(
		'title',
		'label',
		'description',
		'placeholder',
		'price',
		'substring',
		'features',
		'btn_text',
		'btn_link',
		'image',
		'link',
		'url',
		'marker_address',
		'marker_text',
		'value',
	);
	$rows = json_decode( urldecode( $original_string ), TRUE );
	$result = array();
	foreach ( $rows as $i => $row ) {
		foreach ( $row as $key => $value ) {
			if ( in_array( $key, $fields_to_translate ) ) {
				$result[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => TRUE );
			} else {
				$result[ $key . '_' . $i ] = array( 'value' => $value, 'translate' => FALSE );
			}
		}
	}

	return $result;
}

