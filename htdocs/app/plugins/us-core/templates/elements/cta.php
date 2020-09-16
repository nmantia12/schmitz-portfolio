<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_cta
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $title		 string ActionBox title
 * @param $title_tag	 string Title HTML tag: 'div' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'p'
 * @param $title_size	 string Title Size
 * @param $color		 string ActionBox color style: 'primary' / 'secondary' / 'light' / 'custom'
 * @param $bg_color		 string Background color
 * @param $text_color	 string Text color
 * @param $controls		 string Button(s) location: 'right' / 'bottom'
 * @param $btn_label	 string Button 1 label
 * @param $btn_link		 string Button 1 link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $btn_style	 string Button 2 Style: 'primary' / 'secondary' / 'light' / 'contrast' / 'black' / 'white'
 * @param $btn_size		 string Button 1 size
 * @param $btn_icon		 string Button 1 icon
 * @param $btn_iconpos	 string Button 1 icon position: 'left' / 'right'
 * @param $second_button bool Has second button?
 * @param $btn2_label	 string Button 2 label
 * @param $btn2_link	 string Button 2 link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $btn2_style	 string Button 2 Style: 'primary' / 'secondary' / 'light' / 'contrast' / 'black' / 'white'
 * @param $btn2_size	 string Button 2 size
 * @param $btn2_icon	 string Button 2 icon
 * @param $btn2_iconpos	 string Button 2 icon position: 'left' / 'right'
 * @param $el_class		 string Extra class name
 * @param $el_id		 string Element ID
 * @param $css			 string CSS box
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ' color_' . $color;
$classes .= ' controls_' . $controls;

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Button keys that will be parsed
$btn_prefixes = array( 'btn' );
if ( $second_button ) {
	$btn_prefixes[] = 'btn2';
}

// Preparing buttons
$buttons = array();
foreach ( $btn_prefixes as $prefix ) {
	if ( empty( ${$prefix . '_label'} ) ) {
		continue;
	}

	// Check existence of Button Style, if not, set the default
	$btn_styles = us_get_btn_styles();
	if ( ! array_key_exists( ${$prefix . '_style'}, $btn_styles ) ) {
		${$prefix . '_style'} = '1';
	}
	$btn_classes = ' us-btn-style_' . ${$prefix . '_style'};

	$btn_inline_css = us_prepare_inline_css(
		array(
			'font-size' => ${$prefix . '_size'},
		)
	);

	// Icon
	$icon_html = '';
	if ( ! empty( ${$prefix . '_icon'} ) ) {
		$btn_classes .= ' icon_at' . ${$prefix . '_iconpos'};
		$icon_html = us_prepare_icon_tag( ${$prefix . '_icon'} );

		// Swap icon position for RTL
		if ( is_rtl() ) {
			${$prefix . '_iconpos'} = ( ${$prefix . '_iconpos'} == 'left' ) ? 'right' : 'left';
		}
	}

	$btn_link_atts = us_generate_link_atts( ${$prefix . '_link'} );

	$buttons[ $prefix ] = '<a class="w-btn' . $btn_classes . '"' . $btn_link_atts . $btn_inline_css . '>';
	$buttons[ $prefix ] .= ( ${$prefix . '_iconpos'} == 'left' ) ? $icon_html : '';
	$buttons[ $prefix ] .= '<span class="w-btn-label">' . wptexturize( ${$prefix . '_label'} ) . '</span>';
	$buttons[ $prefix ] .= ( ${$prefix . '_iconpos'} == 'right' ) ? $icon_html : '';
	$buttons[ $prefix ] .= '</a>';
}

// Output the element
$output = '<div class="w-actionbox' . $classes . '"' . $el_id . '><div class="w-actionbox-text">';
if ( ! empty( $title ) ) {
	$title_inline_css = us_prepare_inline_css(
		array(
			'font-size' => $title_size,
		)
	);
	$output .= '<' . $title_tag . $title_inline_css . '>' . wptexturize( $title ) . '</' . $title_tag . '>';
}
if ( ! empty( $content ) ) {
	$output .= do_shortcode( wpautop( $content ) );
}
$output .= '</div>';
if ( ! empty( $buttons ) ) {
	$output .= '<div class="w-actionbox-controls">' . implode( '', $buttons ) . '</div>';
}
$output .= '</div>';

echo $output;
