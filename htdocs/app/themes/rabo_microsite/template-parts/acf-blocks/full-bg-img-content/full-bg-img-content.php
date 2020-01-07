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
$id = 'fwb-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'fwb';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' alignfull';
}

// Load values and assign defaults.
$fw_align   = get_field( 'content_alignment' );
$fw_bg      = get_field( 'full_width_background_image' );
$fw_accent  = get_field( 'accent_image' );
$fw_content = get_field( 'full_width_bg_content' ) ?: '<p>Your content here...</p>';
if ( ! empty( $fw_align ) ) {
	$className .= ' ' . $fw_align;
}
?>

<div class="<?php echo $className; ?>" id="<?php echo $id; ?>" style="background: url(<?php echo esc_url( $fw_bg ); ?>) no-repeat center center / cover;">
	<div class="fwb__overlay"></div>
	<?php if ( $fw_content ) : ?>
		<div class="fwb__content alignwide">
			<?php echo $fw_content; ?>
		</div>
	<?php endif; ?>
</div>
