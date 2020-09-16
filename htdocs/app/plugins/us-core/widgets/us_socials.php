<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Socials
 *
 * Class US_Widget_Socials
 */
class US_Widget_Socials extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$output = $args['before_widget'];
		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}

		$classes  = ' style_' . $instance['style'];
		$classes .= ' hover_' . $instance['hover'];
		$classes .= ' color_' . $instance['color'];
		$classes .= ' shape_' . $instance['shape'];

		$output .= '<div class="w-socials' . $classes . '">';
		$output .= '<div class="w-socials-list"' . us_prepare_inline_css( array( 'font-size' => $instance['size'] ) ) . '>';

		if ( isset( $this->config['params'] ) AND is_array( $this->config['params'] ) ) {
			foreach ( $this->config['params'] as $param_name => $param ) {
				if ( in_array(
					$param_name, array(
					'title',
					'size',
					'style',
					'color',
					'shape',
					'hover',
					'custom_link',
					'custom_title',
					'custom_icon',
					'custom_color',
				)
				) ) {
					// Not all the params are social keys
					continue;
				}
				if ( empty( $instance[$param_name] ) ) {
					continue;
				}
				$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param_name;
				$social_url = $instance[$param_name];
				$social_target = '';
				// Email type
				if ( $param_name == 'email' ) {
					if ( is_email( $social_url ) ) {
						$social_url = 'mailto:' . $social_url;
					}
				// Skype type
				} elseif ( $param_name == 'skype' ) {
					if ( strpos( $social_url, ':' ) === FALSE ) {
						$social_url = 'skype:' . esc_attr( $social_url );
					}
				// All others types
				} else {
					$social_url = esc_url( $social_url );
					$social_target = ' target="_blank"';
				}
				$output .= '<div class="w-socials-item ' . $param_name . '">';
				$output .= '<a class="w-socials-item-link"' . $social_target . ' rel="noopener nofollow" href="' . $social_url . '" aria-label="' . $param['heading'] . '">';
				$output .= '<span class="w-socials-item-link-hover"></span>';
				$output .= '</a>';
				$output .= '<div class="w-socials-item-popup"><span>' . $param['heading'] . '</span></div>';
				$output .= '</div>';
			}
		}

		// Custom Link
		if ( ( ! empty( $instance['custom_link'] ) ) AND ( ! empty( $instance['custom_icon'] ) ) ) {
			$link_style = $hover_style = '';
			if ( ! empty( $instance['custom_color'] ) ) {
				if ( ! empty( $instance['color'] ) AND $instance['color'] == 'brand' ) {
					$link_style = ' style="color: ' . $instance['custom_color'] . ';"';
				}
				$hover_style = ' style="background: ' . $instance['custom_color'] . ';"';
			}

			$output .= '<div class="w-socials-item custom">';
			$output .= '<a class="w-socials-item-link" target="_blank" rel="noopener" href="' . esc_attr( $instance['custom_link'] ) . '" aria-label="' . $instance['custom_title'] . '"' . $link_style . '>';
			$output .= '<span class="w-socials-item-link-hover"' . $hover_style . '></span>';
			$output .= us_prepare_icon_tag( $instance['custom_icon'] );
			$output .= '</a>';
			if ( ! empty( $instance['custom_title'] ) ) {
				$output .= '<div class="w-socials-item-popup"><span>' . $instance['custom_title'] . '</span></div>';
			}
			$output .= '</div>';
		}

		$output .= '</div></div>';
		$output .= $args['after_widget'];

		echo $output;
	}
}
