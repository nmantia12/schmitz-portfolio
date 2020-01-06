<?php

/**
 * Testimonial Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'parallax-image-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'parallax-image';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

// Load values and assign defaults.
$parallax_img = get_field( 'parallax_image' );
$img_height   = get_field( 'image_height' );
?>

<div class="<?php echo $className; ?>" id="<?php echo $id; ?>" style="height: <?php echo $img_height; ?>px;">
	<img src="<?php echo esc_url( $parallax_img ); ?>"/>
</div>
