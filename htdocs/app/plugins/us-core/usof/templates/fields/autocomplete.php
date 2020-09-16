<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Autocomplete
 *
 * @var $field array All passed parameters for the field
 * @var $field ['options'] array Initial Parameter List
 * @var $field ['ajax_query_args'] strung  Parameters to be passed in Ajax request
 * @var $field ['multiple'] boolean Multi Select Support
 * @var $field ['sortable'] boolean Drag and drop
 *
 * The Visual composer
 * @var $name string The name field
 * @var $value string The value of the selected parameters
 * @var $options array Initial Parameter List
 * @var $ajax_query_args array Parameters to be passed in Ajax request
 * @var $multiple boolean Multi Select Support
 * @var $sortable boolean Drag and drop
 */

$name = isset( $name ) ? $name : '';
if ( ! isset( $classes ) ) {
	$classes = isset( $field['classes'] ) ? $field['classes'] : '';
}
if ( ! isset( $multiple ) ) {
	$multiple = isset( $field['multiple'] ) ? $field['multiple'] : FALSE;
}
if ( ! isset( $sortable ) ) {
	$sortable = isset( $field['sortable'] ) ? $field['sortable'] : FALSE;
}
if ( ! isset( $params_separator ) ) {
	$params_separator = isset( $field['params_separator'] ) ? $field['params_separator'] : ',';
}
if ( ! isset( $options ) ) {
	$options = isset( $field['options'] ) ? $field['options'] : array();
}
if ( ! isset( $value ) ) {
	$value = isset( $field['value'] ) ? $field['value'] : '';
}
// Additional parameters that may be needed in Ajax request
if ( ! isset( $ajax_query_args ) ) {
	$ajax_query_args = isset( $field['ajax_query_args'] ) ? $field['ajax_query_args'] : array();
}

/**
 * Create options list
 *
 * @param array $options The options
 * @param int $level
 * @return string
 */
$func_create_options_list = function ( $options ) use ( &$func_create_options_list ) {
	$output = '';
	foreach ( $options as $value => $name ) {
		if ( is_array( $name ) ) {
			$output .= '<div class="usof-autocomplete-list-group" data-group="' . esc_attr( $value ) . '">';
			$output .= $func_create_options_list( $name );
			$output .= '</div>';
		} else {
			$atts = array(
				'data-value' => esc_attr( $value ),
				'data-text' => esc_attr( str_replace( ' ', '', strtolower( $name ) ) ),
				'tabindex' => '3',
			);
			$output .= '<div ' . us_implode_atts( $atts ) . '>' .  $name . '</div>';
		}
	}

	return $output;
};

// Export settings
$export_settings = array(
	'ajax_query_args' => $ajax_query_args,
	'multiple' => $multiple,
	'sortable' => $sortable,
	'no_results_found' => us_translate( 'No results found.' ),
	'params_separator' => $params_separator,
);

// Input atts
$atts = array(
	'type' => 'hidden',
	'class' => 'usof-autocomplete-value ' . esc_attr( $classes ),
	'value' => $value,
);

// Additional options to support Visual Composer
if ( strpos( $classes, 'wpb_vc_param_value' ) !== FALSE ) {
	$atts[ 'name' ] = esc_attr( $name );
}

// Output HTML
$output = '<div class="usof-autocomplete"' . us_pass_data_to_js( $export_settings ) . '>';
$output .= '<input ' . us_implode_atts( $atts ) . '>';
$output .= '<div class="usof-autocomplete-toggle">';
$output .= '<div class="usof-autocomplete-options">';
$output .= '<input type="text" autocomplete="off" placeholder="' . us_translate_x( 'Search &hellip;', 'placeholder' ) . '" tabindex="2">';
$output .= '</div>';
$output .= '<div class="usof-autocomplete-list">' . $func_create_options_list( $options ) . '</div>';
$output .= '<div class="usof-autocomplete-message hidden"></div>';
$output .= '</div>';
$output .= '</div>';

echo $output;
