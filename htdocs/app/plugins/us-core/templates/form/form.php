<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single form
 *
 * @var $type          string Form type: 'contact' / 'search' / 'comment' / 'protectedpost' / ...
 * @var $action        string Form action
 * @var $method        string Form method: 'post' / 'get'
 * @var $fields        array Form fields (see any of the fields template header for details)
 * @var $json_data     array Json data to pass to JavaScript
 * @var $classes       string Additional classes to append to form
 * @var $start_html    string HTML to append to the form's start
 * @var $end_html      string HTML to append to the form's end
 *
 * @action Before the template: 'us_before_template:templates/form/form'
 * @action After the template:  'us_after_template:templates/form/form'
 * @filter Template variables:  'us_template_vars:templates/form/form'
 */

$fields = isset( $fields ) ? (array) $fields : array();
$start_html = isset( $start_html ) ? $start_html : '';
$end_html = isset( $end_html ) ? $end_html : '';

// Repeatable fields IDs start from 1
$repeatable_fields = array(
	'text' => 1,
	'email' => 1,
	'textarea' => 1,
	'select' => 1,
	'agreement' => 1,
	'checkboxes' => 1,
	'radio' => 1,
);

foreach ( $fields as $field_name => $field ) {
	if ( isset( $field['type'] ) ) {
		$fields[ $field_name ]['type'] = $field['type'];
		if ( in_array( $field['type'], array_keys( $repeatable_fields ) ) ) {
			$fields[ $field_name ]['field_id'] = $repeatable_fields[ $field['type'] ];
			$repeatable_fields[ $field['type'] ] += 1;
		}
	} else {
		$fields[ $field_name ]['type'] = 'text';
	}
}

$json_data = array(
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
);

global $us_cform_index;

$_atts['class'] = 'w-form';
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}
if ( ! empty( $type ) ) {
	$_atts['class'] .= ' for_' . $type;
}
if ( ! empty( $us_cform_index ) ) {
	$_atts['class'] .= ' us_form_' . $us_cform_index;
}

// Fallback for forms without layout class
if ( strpos( $_atts['class'], 'layout_' ) === FALSE ) {
	$_atts['class'] .= ' layout_ver';
}

// Set CSS inline var for gap between fields
if ( isset( $fields_gap ) AND trim( $fields_gap ) != '1rem' ) {
	$_atts['style'] = '--fields-gap:' . esc_attr( $fields_gap );
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Populate <form> attributes
$form_atts['class'] = 'w-form-h';
$form_atts['autocomplete'] = 'off';
$form_atts['action'] = isset( $action ) ? $action : site_url( $_SERVER['REQUEST_URI'] );
$form_atts['method'] = isset( $method ) ? $method : 'post';

?>
<div <?= us_implode_atts( $_atts ) ?>>
	<form <?= us_implode_atts( $form_atts ) ?>>
		<?php
		echo $start_html;
		foreach ( $fields as $field ) {
			us_load_template( 'templates/form/' . $field['type'], $field );
		}
		echo $end_html;
		?>
	</form>
	<div class="w-form-message"></div>
	<div class="w-form-json hidden"<?= us_pass_data_to_js( $json_data ) ?>></div>
</div>