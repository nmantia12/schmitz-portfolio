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
$id = 'ciq-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'ciq';
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
$CIQ_img      = get_field( 'cropped_blob_image' );
$CIQ_author   = get_field( 'content_img_quote_quote_author' ) ?: 'Author name';
$CIQ_quote    = get_field( 'content_img_quote_quote' );
$CIQ_content  = get_field( 'content_img_quote' );
$CIQ_button   = get_field( 'content_img_quote_button' );
$CIQ_headshot = get_field( 'ciq_author_headshot' );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<?php if ( $CIQ_img ) : ?>
		<div class="ciq__img-wrap" style="clip-path: url(#mask-<?php echo $id; ?>);
			-webkit-clip-path: url(#mask-<?php echo $id; ?>);">
			<img class="ciq__img" src="<?php echo esc_url( $CIQ_img ); ?>" />
		</div>
		<svg viewBox="0 0 1 1" preserveAspectRation="none">
			<defs>
				<clipPath id="mask-<?php echo $id; ?>" clipPathUnits="objectBoundingBox">
				<path d="M 0.05 0.24 C 0.08 0.18 0.13 0.13 0.18 0.09 C 0.29 0.02 0.42 -0.01 0.55 0 C 0.67 0.01 0.8 0.04 0.88 0.14 C 0.89 0.14 0.89 0.14 0.89 0.15 C 0.97 0.23 0.99 0.37 1 0.48 C 1 0.62 0.96 0.78 0.86 0.87 C 0.78 0.95 0.67 0.98 0.56 0.99 C 0.43 1.01 0.29 1 0.18 0.91 C 0.08 0.83 0.02 0.71 0.01 0.58 C -0.01 0.46 0 0.34 0.05 0.24 Z"/>
				</clipPath>
			</defs>
		</svg>
	<?php endif; ?>
	<?php if ( $CIQ_content ) : ?>
		<div class="ciq__content">
			<?php echo $CIQ_content; ?>
		</div>
	<?php endif; ?>
	<?php if ( $CIQ_button ) : ?>
		<div class="ciq__button-wrap">
			<a class="button" href="<?php echo $CIQ_button['url']; ?>" <?php echo $CIQ_button['target'] ? $CIQ_button['target'] : ''; ?>><?php echo $CIQ_button['title']; ?></a>
		</div>
	<?php endif; ?>
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
