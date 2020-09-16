<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_pricing
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $style         string Table style: '1' / '2'
 * @param $items         string Pricing table items
 * @param $el_class         string Extra class name
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= $items_html = '';

if ( empty( $items ) ) {
	$items = array();
} else {
	$items = json_decode( urldecode( $items ), TRUE );
	if ( ! is_array( $items ) ) {
		$items = array();
	}
}

if ( ! empty( $style ) ) {
	$classes .= ' style_' . $style;
}
if ( count( $items ) > 0 ) {
	$classes .= ' items_' . count( $items );
}

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

foreach ( $items as $index => $item ) {
	/**
	 * Filtering the included items
	 *
	 * @param $item ['title'] string Item title
	 * @param $item ['type'] string Item type: 'default' / 'featured'
	 * @param $item ['price'] string Item price
	 * @param $item ['substring'] string Price substring
	 * @param $item ['features'] string Comma-separated list of features
	 * @param $item ['btn_text'] string Button label
	 * @param $item ['btn_link'] string Button link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
	 * @param $item ['btn_style'] string Button Style
	 * @param $item ['btn_size'] string Button size
	 * @param $item ['btn_icon'] string Button icon
	 * @param $item ['btn_iconpos'] string Icon position: 'left' / 'right'
	 */
	$item['type'] = ( isset( $item['type'] ) AND $item['type'] ) ? 'featured' : 'default';
	$item['btn_icon'] = ( isset( $item['btn_icon'] ) ) ? $item['btn_icon'] : '';
	$item['btn_text'] = ( isset( $item['btn_text'] ) ) ? $item['btn_text'] : '';
	$item['btn_link'] = ( isset( $item['btn_link'] ) ) ? $item['btn_link'] : '';
	$item['btn_iconpos'] = ( isset( $item['btn_iconpos'] ) ) ? $item['btn_iconpos'] : 'left';

	$items_html .= '<div class="w-pricing-item type_' . $item['type'] . '"><div class="w-pricing-item-h"><div class="w-pricing-item-header">';
	if ( ! empty( $item['title'] ) ) {
		$items_html .= '<div class="w-pricing-item-title">' . $item['title'] . '</div>';
	}
	$items_html .= '<div class="w-pricing-item-price">';
	if ( ! empty( $item['price'] ) ) {
		$items_html .= $item['price'];
	}
	if ( ! empty( $item['substring'] ) ) {
		$items_html .= '<small>' . $item['substring'] . '</small>';
	}
	$items_html .= '</div></div>';
	if ( ! empty( $item['features'] ) ) {
		$items_html .= '<ul class="w-pricing-item-features">';
		$features = explode( "\n", trim( $item['features'] ) );
		foreach ( $features as $feature ) {
			$items_html .= '<li class="w-pricing-item-feature">' . $feature . '</li>';
		}
		$items_html .= '</ul>';
	}
	if ( ! empty( $item['btn_text'] ) ) {
		$btn_classes = $icon_html = '';

		// Check existence of Button Style, if not, set the default
		$btn_styles = us_get_btn_styles();
		if ( ! array_key_exists( $item['btn_style'], $btn_styles ) ) {
			$item['btn_style'] = '1';
		}

		$btn_classes .= ' us-btn-style_' . $item['btn_style'];

		if ( ! empty( $item['btn_size'] ) ) {
			$btn_inline_css = us_prepare_inline_css(
				array(
					'font-size' => $item['btn_size'],
				)
			);
		} else {
			$btn_inline_css = '';
		}


		if ( ! empty( $item['btn_icon'] ) ) {
			$icon_html = us_prepare_icon_tag( $item['btn_icon'] );
			$btn_classes .= ' icon_at' . $item['btn_iconpos'];
		}
		$btn_link_atts = us_generate_link_atts( $item['btn_link'] );

		$items_html .= '<div class="w-pricing-item-footer">';
		$items_html .= '<a class="w-btn' . $btn_classes . '"' . $btn_link_atts . $btn_inline_css . '>';
		$items_html .= ( $item['btn_iconpos'] == 'left' ) ? $icon_html : '';
		$items_html .= '<span class="w-btn-label">' . strip_tags( $item['btn_text'], '<br>' ) . '</span>';
		$items_html .= ( $item['btn_iconpos'] == 'right' ) ? $icon_html : '';
		$items_html .= '</a>';

		$items_html .= '</div>';
	}
	$items_html .= '</div></div>';
}

// Output the element
$output = '<div class="w-pricing' . $classes . '"' . $el_id . '>' . $items_html . '</div>';

echo $output;
