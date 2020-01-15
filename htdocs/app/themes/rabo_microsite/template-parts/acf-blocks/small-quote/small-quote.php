<?php
/**
 * standard-quote Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'standard-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'standard';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' alignfull';
}
if ( $is_preview ) {
	$className .= ' is-admin';
}

// Load values and assign defaults.
$CIQ_author   = get_field( 'standard_author' ) ?: 'Author name';
$CIQ_quote    = get_field( 'standard_quote' );
$CIQ_headshot = get_field( 'standard_headshot' );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<?php if ( $CIQ_quote ) : ?>
		<div class="ciq__quote-wrap">
			<h2 class="ciq__quote has-blue-color"><?php echo $CIQ_quote; ?></h2>
			<div class="ciq__author-wrap">
				<?php if ( $CIQ_author ) : ?>
					<h4 class="ciq__author has-grey-color"><?php echo $CIQ_author; ?></h4>
				<?php endif; ?>
				<?php if ( $CIQ_headshot ) : ?>
					<svg viewBox="0 0 1 1" preserveAspectRation="none">
						<defs>
							<clipPath id="headshot_mask-<?php echo $id; ?>" clipPathUnits="objectBoundingBox">
							<path d="M 0.05 0.24 C 0.08 0.18 0.13 0.13 0.18 0.09 C 0.29 0.02 0.42 -0.01 0.55 0 C 0.67 0.01 0.8 0.04 0.88 0.14 C 0.89 0.14 0.89 0.14 0.89 0.15 C 0.97 0.23 0.99 0.37 1 0.48 C 1 0.62 0.96 0.78 0.86 0.87 C 0.78 0.95 0.67 0.98 0.56 0.99 C 0.43 1.01 0.29 1 0.18 0.91 C 0.08 0.83 0.02 0.71 0.01 0.58 C -0.01 0.46 0 0.34 0.05 0.24 Z"/>
							</clipPath>
						</defs>
					</svg>
					<div style="clip-path: url(#headshot_mask-<?php echo $id; ?>);
					-webkit-clip-path: url(#headshot_mask-<?php echo $id; ?>);" class="ciq__headshot">
						<div class="overlay"></div>
						<img src="<?php echo esc_url( $CIQ_headshot ); ?>"/>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
