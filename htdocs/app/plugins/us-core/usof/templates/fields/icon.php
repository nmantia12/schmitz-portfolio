<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Icon
 *
 * Icon field with preview
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 *
 * @var   $value string Current value
 */

$icon_sets = us_config( 'icon-sets', array() );

reset( $icon_sets );
$value = trim( $value );
if ( ! preg_match( '/(fas|far|fal|fad|fab|material)\|[a-z0-9-]/i', $value ) ) {
	$value = $field['std'];
}
$select_value = $input_value = '';
$value_arr = explode('|', $value);
if ( count( $value_arr ) == 2 ) {
	$select_value = $value_arr[0];
	$input_value = $value_arr[1];
}
if ( empty( $select_value ) ) {
	$select_value = key( $icon_sets );
}

?>

<div class="us-icon">
	<input name="<?php echo esc_attr( $name ); ?>" class="us-icon-value" type="hidden" value="<?php echo esc_attr( $value ); ?>">
	<div class="usof-select">
		<select name="icon_set" class="us-icon-select">
			<?php foreach ( $icon_sets as $set_slug => $set_info ) { ?>
				<option value="<?php echo $set_slug ?>"<?php if ( $select_value == $set_slug ) echo ' selected="selected"'; ?> data-info-url="<?php echo $set_info['set_url'] ?>"><?php echo $set_info['set_name'] ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="us-icon-preview">
		<?php echo ( $icon_preview_html = us_prepare_icon_tag( $value ) ) ? $icon_preview_html : '<i class="material-icons"></i>'; ?>
	</div>
	<div class="us-icon-input">
		<input name="icon_name" class="us-icon-text" type="text" value="<?php echo $input_value; ?>">
	</div>
</div>
<div class="us-icon-desc">
	<?php echo '<a class="us-icon-set-link" href="' . $icon_sets[$select_value]['set_url'] . '" target="_blank" rel="noopener">' . __( 'Enter icon name from the list', 'us' ) . '</a>. ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">star</span>, <span class="usof-example">edit</span>, <span class="usof-example">code</span>' ?>
</div>
