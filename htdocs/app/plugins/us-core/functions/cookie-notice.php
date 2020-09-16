<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output cookie notice bar
 */
if ( ! function_exists( 'us_cookie_notice_output' ) ) {
	if ( us_get_option( 'cookie_notice', 0 ) ) {
		if ( ! isset( $_COOKIE ) OR ! isset( $_COOKIE['us_cookie_notice_accepted'] ) ) {
			add_action( 'wp_footer', 'us_cookie_notice_output', 90 );
		}
	}

	function us_cookie_notice_output() {
		$output = '';

		$cookie_message = us_get_option( 'cookie_message', '' );

		// Add link to Privacy Policy page
		if ( ! empty( us_get_option( 'cookie_privacy' ) ) ) {
			$cookie_message .= ' ' . get_the_privacy_policy_link();
		}

		// Output bar, only if the message is not empty
		if ( $cookie_message ) {
			$output .= '<div class="l-cookie pos_' . us_get_option( 'cookie_message_pos', 'bottom' ) . '">';
			$output .= '<div class="l-cookie-message">' . $cookie_message . '</div>';

			// Accept button
			$output .= '<a class="w-btn us-btn-style_' . us_get_option( 'cookie_btn_style', '1' ) . ' " id="us-set-cookie" href="javascript:void(0);">';
			$output .= '<span>' . strip_tags( us_get_option( 'cookie_btn_label', 'Ok' ) ) . '</span>';
			$output .= '</a>';

			$output .= '</div>';
		}

		echo $output;
	}
}
