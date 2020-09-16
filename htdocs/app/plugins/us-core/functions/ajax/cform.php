<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for sending contact form via us_cform shortcode
 */
add_action( 'wp_ajax_nopriv_us_ajax_cform', 'us_ajax_cform' );
add_action( 'wp_ajax_us_ajax_cform', 'us_ajax_cform' );
function us_ajax_cform() {
	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	if ( $post_id <= 0 ) {
		wp_send_json_error();
	}
	$post = get_post( $post_id );
	if ( empty( $post ) ) {
		wp_send_json_error();
	}

	$form_index = isset( $_POST['form_index'] ) ? intval( $_POST['form_index'] ) : 1;

	// Retrieving the relevant shortcode from the page to get options
	$post_content = $post->post_content;
	preg_match_all( '~(\[us_cform(.*?)\])((.*?)\[/us_cform\])?~', $post_content, $matches );

	if ( ! isset( $matches[0][ $form_index - 1 ] ) ) {
		wp_send_json_error();
	}

	// Getting the relevant shortcode options
	$shortcode = $matches[1][ $form_index - 1 ];

	// For proper shortcode_parse_atts behaviour
	$shortcode = substr_replace( $shortcode, ' ]', - 1 );
	$shortcode_atts = shortcode_parse_atts( $shortcode );

	// Compatibility with older versions (applying migrations)
	if ( class_exists( 'US_Migration' ) ) {
		foreach ( US_Migration::instance()->translators as $version => $translator ) {
			if ( method_exists( $translator, 'translate_us_cform' ) ) {
				$translator->translate_us_cform( 'us_cform', $shortcode_atts );
			}
		}
	}

	// Take all field types from config
	$available_fields = us_config( 'elements/cform.params.items.params.type.options' );
	$field_types = is_array( $available_fields ) ? array_keys( $available_fields ) : array();

	// Decode shortcode items
	$shortcode_items = json_decode( urldecode( $shortcode_atts['items'] ), TRUE );
	$shortcode_items = $shortcode_items ? $shortcode_items : array();

	// Default shortcode has no content, take it from config
	if ( empty( $shortcode_items ) ) {
		$shortcode_items = json_decode( urldecode( us_config( 'elements/cform.params.items.std' ) ), TRUE );
		$shortcode_items = $shortcode_items ? $shortcode_items : array();
	}

	$sorted_fields = array();
	// Sort shortcode fields
	foreach ( $shortcode_items as $shortcode_item_key => $shortcode_item ) {
		if ( in_array( $shortcode_item['type'], $field_types ) ) {
			// Pass info field
			if ( $shortcode_item['type'] == 'info' ) {
				continue;
			}
			// Set Agreement Box and Captcha to be required
			if ( $shortcode_item['type'] == 'agreement' OR $shortcode_item['type'] == 'captcha' ) {
				$shortcode_item['required'] = 1;
			}
			$existing_fields = isset( $sorted_fields[ $shortcode_item['type'] ] ) ? $sorted_fields[ $shortcode_item['type'] ] : array();
			$field_id = count( $existing_fields ) + 1;
			$shortcode_item['name'] = 'us_form_' . $form_index . '_' . $shortcode_item['type'] . '_' . $field_id;
			$sorted_fields[ $shortcode_item['type'] ][] = $shortcode_item;
		}
	}


	function us_cform_is_valid_captcha( $captcha = NULL ) {
		$fields = array();
		foreach ( $_POST as $key => $field ) {
			if ( preg_match( '~^us_form_\d_([^_]+_)\d_(\w+)$~', $key, $matches ) ) {
				$fields[ $matches[1] . $matches[2] ] = $field;
			} elseif ( preg_match( '~^us_form_\d_([^_]+)_\d$~', $key, $matches ) ) {
				$fields[ $matches[1] ] = $field;
			}
		}

		$captcha_hash = isset( $fields['captcha_hash'] ) ? stripslashes( $fields['captcha_hash'] ) : NULL;
		if ( $captcha_hash === md5( $captcha . NONCE_SALT ) ) {
			return TRUE;
		}

		return FALSE;
	}

	$errors = $headers = array();
	$body_content = '';
	$from_email = '';
	$from_name = '';

	// Validate fields and compose a message
	foreach ( $sorted_fields as $sorted_field_key => $sorted_field ) {
		foreach ( $sorted_field as $field ) {
			$name = isset( $field['name'] ) ? $field['name'] : '';
			$field_type = isset( $field['type'] ) ? $field['type'] : '';
			if ( $field_type == 'email' AND ! empty( $field['is_used_as_from_email'] ) AND ! empty( $_POST[ $name ] ) AND is_email( $_POST[ $name ] ) AND empty( $from_email ) ) {
				$from_email = sanitize_email( $_POST[ $name ] );
			}

			if ( $field_type == 'text' AND ! empty( $field['is_used_as_from_name'] ) AND ! empty( $_POST[ $name ] ) AND empty( $from_name ) ) {
				$from_name = sanitize_text_field( $_POST[ $name ] );
			}

			// Validate fields
			if ( isset( $field['required'] ) ) {
				// Validate Captcha
				if ( $field_type == 'captcha' ) {
					$captcha = isset( $_POST[ $name ] ) ? esc_attr( $_POST[ $name ] ) : NULL;
					if ( ! us_cform_is_valid_captcha( $captcha ) ) {
						if ( ! empty( $name ) ) {
							$errors[ $field['type'] ]['name'][] = $name;
						}
					}
				} elseif ( ! isset( $_POST[ $name ] ) OR ( isset( $_POST[ $name ] ) AND empty( $_POST[ $name ] ) ) ) {
					if ( ! empty( $name ) ) {
						$errors[ $field['type'] ]['name'][] = $name;
					}
				}
			}

			$email_content = isset( $_POST[ $name ] ) ? $_POST[ $name ] : '';

			$skipped_fields = array(
				'captcha',
			);

			// Skip empty and skipped fields
			if ( empty( $email_content ) OR in_array( $field_type, $skipped_fields ) ) {
				continue;
			}
			// Take message body
			if ( $field['type'] == 'agreement' AND ! empty( $field['value'] ) ) {
				$agreement = '<p>' . __( 'The sender has given his consent.', 'us' ) . '<br>';
				$agreement .= __( 'Agreement text', 'us' ) . ': <strong>' . strip_tags( $field['value'], '<a>' ) . '</strong><br>';
				$agreement .= __( 'Agreement date and time', 'us' ) . ': <strong>' . gmdate( 'Y-m-d H:i:s' ) . ' GMT</strong><br>';
				$agreement .= __( 'IP address', 'us' ) . ': <strong>' . us_get_ip() . '</strong></p>';
			} else {
				$body_content .= '<p>';
				if ( ! empty( $field['label'] ) ) {
					$body_content .= sanitize_text_field( $field['label'] ) . ':<br>';
				} elseif ( ! empty( $field['placeholder'] ) ) {
					$body_content .= sanitize_text_field( $field['placeholder'] ) . ':<br>';
				}
				if ( is_array( $email_content ) ) {
					$values_length = count( $email_content );
					$counter = 0;
					foreach ( $email_content as $value ) {
						$body_content .= '<strong>' . sanitize_text_field( stripslashes( $value ) ) . '</strong>';
						$counter ++;
						if ( $counter < $values_length ) {
							$body_content .= '<br>';
						}
					}
				} elseif ( $field['type'] == 'email' ) {
					$body_content .= '<strong>' . sanitize_email( stripslashes( $email_content ) ) . '</strong>';
					$headers[] = 'Reply-To: ' . sanitize_email( stripslashes( $email_content ) );
				} else {
					$body_content .= '<strong>' . sanitize_text_field( stripslashes( $email_content ) ) . '</strong>';
				}
				$body_content .= '</p>';
			}
		}
	}

	if ( ! empty( $from_email ) ) {
		$headers[] = "From: $from_name <$from_email>";
	}
	if ( ! empty( $errors ) ) {
		wp_send_json_error( $errors );
	}

	// Get email receiver
	$email_to = get_option( 'admin_email' );
	if ( ! empty( $shortcode_atts['receiver_email'] ) ) {
		$email_to = array_map( 'sanitize_email', explode( ',', $shortcode_atts['receiver_email'] ) );
	}

	$email_body = '<p>' . __( 'You received a message from', 'us' ) . ' <a href="' . site_url() . '">' . get_bloginfo( 'name' ) . '</a></p>';
	$email_body .= $body_content;

	if ( isset( $agreement ) ) {
		$email_body .= $agreement;
	}

	$email_subject = sprintf( __( 'Message from %s', 'us' ), get_bloginfo( 'name' ) );

	if ( empty( $body_content ) ) {
		wp_send_json_error( __( 'Cannot send empty message. Please fill any of the fields.', 'us' ) );
	}

	if ( is_rtl() ) {
		$email_body = '<div style="direction: rtl; unicode-bidi: embed;">' . $email_body . '</div>';
	}

	add_filter( 'wp_mail_content_type', 'us_set_html_content_type' );
	function us_set_html_content_type() {
		return 'text/html';
	}

	// Send attempt
	$success = wp_mail( $email_to, $email_subject, $email_body, $headers );

	if ( $success ) {
		if ( ! empty( $shortcode_atts['success_message'] ) ) {
			$success_message = trim( strip_tags( $shortcode_atts['success_message'], '<a><br><strong>' ) );
		} else {
			$success_message = us_config( 'elements/cform.params.success_message.std' );
		}
		wp_send_json_success( $success_message );
	} else {
		wp_send_json_error( __( 'Cannot send the message. Please contact the website administrator.', 'us' ) );
	}
}
