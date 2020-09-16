<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a form's textarea field
 *
 * @var $name        string Field name
 * @var $type        string Field type
 * @var $label       string Field label
 * @var $placeholder string Field placeholder
 * @var $description string Field description
 * @var $value       string Field value
 * @var $required    bool Is the field required?
 * @var $icon        string Field icon
 * @var $field_id    string Field id
 * @var $classes     string Additional field classes
 *
 * @action Before the template: 'us_before_template:templates/form/textarea'
 * @action After the template: 'us_after_template:templates/form/textarea'
 * @filter Template variables: 'us_template_vars:templates/form/textarea'
 */

$default_params = us_config( 'elements/cform.params.items.params' );
foreach ( $default_params as $param => $params ) {
	if ( ! isset( $$param ) ) {
		$$param = $params['std'];
	}
}

global $us_cform_index;
$field_id = isset( $field_id ) ? $field_id : 1;
$field_id = 'us_form_' . $us_cform_index . '_' . $type . '_' . $field_id;

$_atts['class'] = 'w-form-row';
$_atts['class'] .= ' for_' . $type;
if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}

if ( ! empty( $label ) ) {
	$_atts['class'] .= ' has_label';
	if ( $move_label ) {
		$_atts['class'] .= ' move_label';
	}
	$field_atts['aria-label'] = $label;
} elseif ( empty( $label ) AND ! empty( $placeholder ) ) {
	$field_atts['aria-label'] = $placeholder;
} else {
	$field_atts['aria-label'] = $field_id;
}

$field_atts['name'] = isset( $name ) ? $name : $field_id;
if ( $required AND ! empty( $placeholder ) AND empty( $label ) ) {
	$placeholder .= ' *';
}
$field_atts['placeholder'] = $placeholder;

if ( $required ) {
	$_atts['class'] .= ' required';
	$field_atts['data-required'] = 'true';
	$field_atts['aria-required'] = 'true';
	if ( ! empty( $label ) ) {
		$label .= ' <span class="required">*</span>';
	}
}
if ( ! empty( $icon ) ) {
	$_atts['class'] .= ' with_icon';
}
if ( ! empty( $cols ) AND $cols != 1 ) {
	$_atts['class'] .= ' cols_' . $cols;
}

?>
<div <?= us_implode_atts( $_atts ) ?>>
	<?php if ( ! empty( $label ) ) : ?>
		<div class="w-form-row-label">
			<span><?= strip_tags( $label, '<a><br><strong>' ) ?></span>
		</div>
	<?php endif; ?>
	<div class="w-form-row-field">
		<?php do_action( 'us_form_field_start', $vars ) ?>
		<?= us_prepare_icon_tag( $icon ) ?>
		<textarea <?= us_implode_atts( $field_atts ) ?>><?= esc_textarea( $value ) ?></textarea>
		<?php do_action( 'us_form_field_end', $vars ) ?>
	</div>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="w-form-row-description">
			<?= strip_tags( $description, '<a><br><strong>' ) ?>
		</div>
	<?php endif; ?>
	<div class="w-form-row-state"><?php _e( 'Fill out this field', 'us' ) ?></div>
</div>
