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
$id = 'lg-quote-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'lg-quote';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( $is_preview ) {
	$className .= ' is-admin';
}

if ( ! empty( $block['align'] ) ) {
	$alignClass = 'align' . $block['align'];
} else {
	$alignClass = '';
}

// Load values and assign defaults.
$quote    = get_field( 'lg_quote_quote' ) ?: 'Your quote here...';
$author   = get_field( 'lg_quote_author' ) ?: 'Author name';
$headshot = get_field( 'lg_quote_headshot' );
$bg_img   = get_field( 'lg_quote_background_image' );

if ( $quote ) : ?>
	<?php if ( $alignClass ) : ?>
		<div class="<?php echo $alignClass; ?>">
	<?php endif; ?>
	<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
		<?php if ( $quote ) : ?>
			<h2 class="lg-quote__quote has-blue-color"><?php echo $quote; ?></h2>
		<?php endif; ?>
		<?php if ( $author ) : ?>
			<h4 class="lg-quote__author has-white-color"><?php echo $author; ?></h4>
		<?php endif; ?>
		<?php if ( $headshot ) : ?>
			<svg viewBox="0 0 1 1" preserveAspectRation="none">
				<defs>
					<clipPath id="headshot_mask-<?php echo $id; ?>" clipPathUnits="objectBoundingBox">
					<path d="M 0.05 0.24 C 0.08 0.18 0.13 0.13 0.18 0.09 C 0.29 0.02 0.42 -0.01 0.55 0 C 0.67 0.01 0.8 0.04 0.88 0.14 C 0.89 0.14 0.89 0.14 0.89 0.15 C 0.97 0.23 0.99 0.37 1 0.48 C 1 0.62 0.96 0.78 0.86 0.87 C 0.78 0.95 0.67 0.98 0.56 0.99 C 0.43 1.01 0.29 1 0.18 0.91 C 0.08 0.83 0.02 0.71 0.01 0.58 C -0.01 0.46 0 0.34 0.05 0.24 Z"/>
					</clipPath>
				</defs>
			</svg>
			<div style="clip-path: url(#headshot_mask-<?php echo $id; ?>);
			-webkit-clip-path: url(#headshot_mask-<?php echo $id; ?>);" class="lg-quote__headshot">
				<div class="overlay"></div>
				<img src="<?php echo esc_url( $headshot ); ?>"/>
			</div>
		<?php endif; ?>
		<?php if ( $bg_img ) : ?>
			<div class="lg-quote__bg" style="background: url(<?php echo esc_url( $bg_img ); ?>) no-repeat center center / cover;"></div>
		<?php endif; ?>
	</div>
	<?php if ( $alignClass ) : ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
