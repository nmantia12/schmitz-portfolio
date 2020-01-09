<?php

/**
 * infographic Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'infographic-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'infographic';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}
if ( $is_preview ) {
	$className .= ' is-admin';
}

// Load values and assign defaults.
$info_img = get_field( 'infographic_image' );
?>

<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<img class="infographic__img" id="<?php echo 'infographic__img-' . $block['id']; ?>" src="<?php echo esc_url( $info_img ); ?>"/>
</div>
