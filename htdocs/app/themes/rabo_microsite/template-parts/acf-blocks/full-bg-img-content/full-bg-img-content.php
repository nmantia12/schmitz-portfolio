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
$className .= ' alignfull parallax-effect';


// Load values and assign defaults.
$fw_align   = get_field( 'content_alignment' );
$fw_bg      = get_field( 'full_width_background_image' );
$fw_accent  = get_field( 'accent_image' );
$fw_content = get_field( 'full_width_bg_content' ) ?: '<p>Your content here...</p>';
if ( ! empty( $fw_align ) ) {
	$className .= ' ' . $fw_align;
}
?>

<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<img src="<?php echo esc_url( $fw_bg ); ?>" data-parallax="200"/>
	<div class="fwb__overlay"></div>
	<?php if ( $fw_content ) : ?>
		<div class="fwb__content alignwide">
			<?php echo $fw_content; ?>
		</div>
	<?php endif; ?>
	<svg viewBox="0 0 1 1" preserveAspectRation="none">
		<defs>
			<clipPath id="bee_mask" clipPathUnits="objectBoundingBox">
				<path d="M 0.05 0.24 C 0.08 0.18 0.13 0.13 0.18 0.09 C 0.29 0.02 0.43 -0.01 0.55 0 C 0.67 0.01 0.8 0.04 0.89 0.14 C 0.89 0.14 0.89 0.14 0.9 0.15 C 0.97 0.23 1 0.37 1 0.48 C 1 0.62 0.96 0.78 0.87 0.88 C 0.79 0.96 0.67 0.98 0.57 0.99 C 0.43 1.01 0.29 1 0.18 0.91 C 0.08 0.83 0.02 0.71 0.01 0.58 C -0.01 0.46 0 0.34 0.05 0.24 Z"/>
			</clipPath>
		</defs>
	</svg>
	<div class="fwb__accent parallax-effect">
		<img style="clip-path: url(#bee_mask); -webkit-clip-path: url(#bee_mask);" src="<?php echo esc_url( $fw_accent ); ?>" data-parallax="500"/>
	</div>
</div>
