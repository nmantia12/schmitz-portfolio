<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax methods for us_login widget
 */
add_action( 'wp_ajax_nopriv_us_ajax_user_info', 'us_ajax_user_info' );
add_action( 'wp_ajax_us_ajax_user_info', 'us_ajax_user_info' );
function us_ajax_user_info() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}
	$current_user = wp_get_current_user();
	$logout_redirect = ( isset( $_POST['logout_redirect'] ) ) ? $_POST['logout_redirect'] : '';
	$logout_redirect = str_replace( '&amp;', '&', wp_logout_url( esc_url( $logout_redirect ) ) );

	$result = array(
		'name' => $current_user->display_name,
		'avatar' => get_avatar( get_current_user_id(), '64' ),
		'logout_url' => $logout_redirect,
	);
	wp_send_json_success( $result );
}

add_action( 'init', 'us_ajax_login_init' );
function us_ajax_login_init() {
	if ( ! is_user_logged_in() ) {
		add_action( 'wp_ajax_nopriv_us_ajax_login', 'us_ajax_login' );
		add_action( 'wp_ajax_us_ajax_login', 'us_ajax_login' );
	}
}

function us_ajax_login() {
	// Check form nonce
	check_ajax_referer( 'us_ajax_login_nonce', 'us_login_nonce' );
	// Get form data
	$info = array(
		// Don't trust but pass as is, it will be sanitized by WordPress
		'user_login' => $_POST['username'],
		'user_password' => $_POST['password'],
		'remember' => TRUE,
	);

	// Logging
	$user_signon = wp_signon( $info, is_ssl() );
	$message = $user_signon->get_error_message();
	$error_code = $user_signon->get_error_code();
	// Format error message to cut a link and leading ERROR, NOTICE etc words
	$pattern = '#^(<strong>[^>]+>:\s)?((?:(?! <a href).)+)([\s\S]+)#i';
	$message = ucfirst( preg_replace( $pattern, '$2', $message ) );
	$result = array(
		'message' => $message,
		'code' => $error_code,
	);
	if ( is_wp_error( $user_signon ) ) {
		wp_send_json_error( $result );
	} else {
		wp_send_json_success();
	}
}


