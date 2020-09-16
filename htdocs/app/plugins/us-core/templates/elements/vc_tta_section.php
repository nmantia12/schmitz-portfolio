<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_tta_section
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode         string Current shortcode name
 * @var $shortcode_base    string The original called shortcode name (differs if called an alias)
 * @var $content           string Shortcode's inner content
 * @var $classes           string Extend class names
 *
 * @var $title             string Section title
 * @var $tab_id            string Section slug
 * @var $icon              string Icon
 * @var $i_position        string Icon position: 'left' / 'right'
 * @var $active            bool Tab is opened when page loads
 * @var $indents           string Indents type: '' / 'none'
 * @var $bg_color          string Background color
 * @var $text_color        string Text color
 * @var $c_position        string Control position (inherited from wrapping vc_tta_tabs shortcode): 'left' / 'right'
 * @var $title_tag         string Title HTML tag (inherited from wrapping vc_tta_tabs shortcode): 'div' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'p'
 * @var $title_size        string Title Size
 * @var $el_class          string Extra class name
 */

global $us_tabs_atts, $us_tab_index, $us_is_faqs_page;

// Tab indexes start from 1
$us_tab_index = isset( $us_tab_index ) ? ( $us_tab_index + 1 ) : 1;

// We could overload some of the atts at vc_tabs implementation, so apply them here as well
if ( isset( $us_tabs_atts[ $us_tab_index - 1 ] ) ) {
	foreach ( $us_tabs_atts[ $us_tab_index - 1 ] as $_key => $_value ) {
		${$_key} = $_value;
	}
}

$content_html = do_shortcode( $content );

$_atts['class'] = 'w-tabs-section';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( $icon ) {
	$_atts['class'] .= ' with_icon';
}
if ( $indents == 'none' ) {
	$_atts['class'] .= ' no_indents';
}
if ( $active ) {
	$_atts['class'] .= ' active';
}
if ( ! empty( $text_color ) ) {
	$_atts['class'] .= ' has_text_color';
}

// Hide the section with empty content
if ( $content_html == '' ) {
	$_atts['class'] .= ' content-empty';
}
if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( empty( $tab_id ) ) {
	$tab_id = uniqid();
}

if ( empty( $tab_link ) ) {
	$tab_link = 'javascript:void(0)';
}

$_atts['id'] = $tab_id;

$inline_css = us_prepare_inline_css(
	array(
		'background' => us_get_color( $bg_color, /* Gradient */ TRUE ),
		'color' => us_get_color( $text_color ),
	)
);

// If a custom class is installed, then transfer this class to w-tabs-section-content
$content_classes = '';
$_atts['class'] = preg_replace_callback( '/(\sus_custom_\w+)/', function( $matches ) use ( &$content_classes ) {
	$content_classes .= $matches[1];
	return '';
}, $_atts['class'] );

$title_atts = array(
	'class' => 'w-tabs-section-title',
);
$content_atts = array(
	'class' => 'w-tabs-section-content ' . $content_classes,
	'id' => 'content-' . $tab_id,
	'aria-expanded' => $active ? 'true' : 'false',
);
$content_h_atts = array(
	'class' => 'w-tabs-section-content-h i-cf',
);

// Add atts for FAQs page
if ( $us_is_faqs_page ) {
	$_atts['itemscope'] = '';
	$_atts['itemprop'] = 'mainEntity';
	$_atts['itemtype'] = 'https://schema.org/Question';
	$title_atts['itemprop'] = 'name';
	$content_atts['itemscope'] = '';
	$content_atts['itemprop'] = 'acceptedAnswer';
	$content_atts['itemtype'] = 'https://schema.org/Answer';
	$content_h_atts['itemprop'] = 'text';
}

// Apply filters to title text
$title = us_replace_dynamic_value( $title, 'any' );
$title = wptexturize( $title );

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . $inline_css . '>';

$output .= '<button class="w-tabs-section-header' . ( $active ? ' active' : '' ) . '"' . us_prepare_inline_css( array( 'font-size' => $title_size ) ) . ' aria-controls="content-' . $tab_id . '">';
$output .= '<div class="w-tabs-section-header-h">';
if ( $c_position == 'left' ) {
	$output .= '<div class="w-tabs-section-control"></div>';
}
if ( $icon AND $i_position == 'left' ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= '<' . $title_tag . ' ' . us_implode_atts( $title_atts ) . '>' . $title . '</' . $title_tag . '>';
if ( $icon AND $i_position == 'right' ) {
	$output .= us_prepare_icon_tag( $icon );
}
if ( $c_position == 'right' ) {
	$output .= '<div class="w-tabs-section-control"></div>';
}
$output .= '</div></button>';
$output .= '<div ' . us_implode_atts( $content_atts ) . '>';
$output .= '<div ' . us_implode_atts( $content_h_atts ) . '>' . $content_html . '</div>';
$output .= '</div></div>';

echo $output;
