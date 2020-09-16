<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Login
 *
 * Class US_Widget_Login
 */
class US_Widget_Login extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		// Output title
		if ( $title ) {
			echo '<h3 class="widgettitle">' . $title . '</h3>';
		}

		$json_data = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'logout_redirect' => $instance['logout_redirect'],
			'login_redirect' => $instance['login_redirect'],
		);

		// Output preloader
		echo '<div class="g-preloader type_1"></div>';

		// Output profile block
		$output = '<div class="w-profile-json hidden"' . us_pass_data_to_js( $json_data ) . '></div>';
		$output .= '<div class="w-profile hidden">';
		$output .= '<a class="w-profile-link for_user" href="' . esc_url( admin_url( 'profile.php' ) ) . '">';
		$output .= '<span class="w-profile-avatar"></span>';
		$output .= '<span class="w-profile-name"></span>';
		$output .= '</a>';
		$output .= '<a class="w-profile-link for_logout" href="">' . us_translate( 'Log Out' ) . '</a>';
		$output .= '</div>';

		echo $output;

		$form_template_vars = array(
			'type' => 'login hidden',
			'action' => site_url( 'wp-login.php' ),
			'method' => 'post',
			'fields' => array(
				'log' => array(
					'type' => 'text',
					'placeholder' => us_translate( 'Username' ),
					'required' => TRUE,
				),
				'pwd' => array(
					'type' => 'password',
					'placeholder' => us_translate( 'Password' ),
					'required' => TRUE,
				),
				'submit' => array(
					'type' => 'submit',
					'title' => us_translate( 'Log In' ),
					'btn_classes' => 'us-btn-style_1',
				),
				'rememberme' => array(
					'type' => 'hidden',
					'value' => 'forever',
				),
				'nonce' => array(
					'type' => 'nonce',
					'action' => 'us_ajax_login_nonce',
					'name' => 'us_login_nonce',
				),
			),
		);
		if ( ! empty( $instance['login_redirect'] ) ) {
			$form_template_vars['fields']['redirect_to'] = array(
				'type' => 'hidden',
				'value' => $instance['login_redirect'],
			);
		}
		if ( $instance['register'] != '' OR $instance['lostpass'] != '' ) {
			$form_template_vars['end_html'] = '<div class="w-form-row for_links">';
			if ( $instance['register'] != '' ) {
				$form_template_vars['end_html'] .= '<a class="w-form-row-link for_register" href="' . esc_url( $instance['register'] ) . '">';
				$form_template_vars['end_html'] .= us_translate( 'Register' );
				$form_template_vars['end_html'] .= '</a>';
			}
			if ( $instance['lostpass'] != '' ) {
				$form_template_vars['end_html'] .= '<a class="w-form-row-link for_lostpass" href="' . esc_url( $instance['lostpass'] ) . '">';
				$form_template_vars['end_html'] .= us_translate( 'Lost your password?' );
				$form_template_vars['end_html'] .= '</a>';
			}
			$form_template_vars['end_html'] .= '</div>';
		}

		// Output form fields
		us_load_template( 'templates/form/form', $form_template_vars );

		// If we are in front end editor mode, apply JS to the widget
		if ( FALSE AND function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
			echo '<script>
			jQuery(function($){
				if (typeof $us !== "undefined" && typeof $us.WLogin === "function") {
					$(".widget_us_login").wUsLogin();
				}
			});
			</script>';
		}

		echo $args['after_widget'];
	}
}
