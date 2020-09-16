<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output search element
 *
 * @var $text           string Placeholder Text
 * @var $layout         string Layout: 'simple' / 'modern' / 'fulwidth' / 'fullscreen'
 * @var $width          int Field width
 * @var $design_options array
 * @var $product_search bool Whether to search for WooCommerce products only
 * @var $classes        string
 * @var $id             string
 */

$_atts['class'] = 'w-search';
$_atts['class'] .= isset( $classes ) ? $classes : '';

// Force "Simple" layout for shortcode
if ( $us_elm_context == 'shortcode' ) {
	$layout = 'simple';
}

$_atts['class'] .= ' layout_' . $layout;

if ( us_get_option( 'ripple_effect' ) ) {
	$_atts['class'] .= ' with_ripple';
}
if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';

// Additional block for Fullscreen layout, when Ripple Effect is enabled
if ( $layout == 'fullscreen' AND us_get_option( 'ripple_effect' ) ) {

	$output .= '<div class="w-search-background"';
	$output .= ' style="background:' . (
		! empty( $field_bg_color )
			? us_get_color( $field_bg_color, /* Gradient */ TRUE )
			: us_get_color( 'color_content_bg', /* Gradient */ TRUE )
		) . '">';
	$output .= '</div>';
}

// Add "Open" button
if ( $us_elm_context != 'shortcode' ) {
	$output .= '<a class="w-search-open" href="javascript:void(0);" aria-label="' . us_translate( 'Search' ) . '">';
	if ( ! empty( $icon ) ) {
		$output .= us_prepare_icon_tag( $icon );
	}
	$output .= '</a>';
}

$output .= '<div class="w-search-form">';
$output .= '<form class="w-form-row for_text" action="' . esc_attr( home_url( '/' ) ) . '" method="get">';
$output .= '<div class="w-form-row-field">';

$input_atts = array(
	'type' => 'text',
	'name' => 's',
	'id' => 'us_form_search_s',
	'placeholder' => $text,
	'aria-label' => $text,
	'value' => esc_html( get_query_var( 's', /* Default */ '' ) ),
);

// Add inline colors for shortcodes only
if ( $us_elm_context == 'shortcode' ) {
	$input_atts['style'] = '';

	if ( ! empty( $field_bg_color ) ) {
		$field_bg_color = us_get_color( $field_bg_color, /* Gradient */ TRUE );
		$input_atts['style'] .= sprintf( 'background:%s!important;', $field_bg_color );
	}
	if ( ! empty( $field_text_color ) ) {
		$field_text_color = us_get_color( $field_text_color );
		$input_atts['style'] .= sprintf( 'color:%s!important;', $field_text_color );
	}
}

$output .= '<input ' . us_implode_atts( $input_atts ) . '/>';

// Additional hidden input for search Products only
if ( ! empty( $product_search ) ) {
	$output .= '<input type="hidden" name="post_type" value="product" />';
}

// Additional hidden input for WPML Language code
if ( defined( 'ICL_LANGUAGE_CODE' ) AND ICL_LANGUAGE_CODE != '' ) {
	$output .= '<input type="hidden" name="lang" value="' . esc_attr( ICL_LANGUAGE_CODE ) . '" />';
}

$output .= '</div>';

// Clickable button for "Simple" layout only
if ( $layout == 'simple' ) {
	$button_atts = array(
		'class' => 'w-search-form-btn w-btn',
		'type' => 'submit',
		'aria-label' => us_translate( 'Search' ),
	);

	// Add inline styles for shortcodes only
	if ( $us_elm_context == 'shortcode' ) {
		$button_atts['style'] = '';

		if ( ! empty( $icon_size ) AND $icon_size != '1rem' ) {
			$button_atts['style'] .= sprintf( 'font-size:%s;', $icon_size );
		}
		if ( ! empty( $field_text_color ) ) {
			$field_text_color = us_get_color( $field_text_color );
			$button_atts['style'] .= sprintf( 'color:%s!important;', $field_text_color );
		}
	}

	$output .= '<button ' . us_implode_atts( $button_atts ) . '>';
	if ( ! empty( $icon ) ) {
		$output .= us_prepare_icon_tag( $icon );
	}
	$output .= '</button>';
}

// Add "Close" button
if ( $us_elm_context != 'shortcode' ) {
	$output .= '<a class="w-search-close" href="javascript:void(0);" aria-label="' . us_translate( 'Close' ) . '"></a>';
}

$output .= '</form></div></div>';

echo $output;
