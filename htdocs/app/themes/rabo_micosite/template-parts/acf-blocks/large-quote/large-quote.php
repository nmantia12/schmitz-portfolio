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
if ( ! empty( $block['align'] ) ) {
	$alignClass = ' align' . $block['align'];
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
			<svg height="0px" width="0px">
				<defs>
					<clipPath id="headshot_mask">
					<path d="M8.062,33.917A54.735,54.735,0,0,1,28.4,12.327C45.119,2.448,65.368-.77,84.589.124c18.6.894,37.951,5.588,51.585,19,.536.492,1.028,1.028,1.52,1.565,11.13,11.89,15.466,30.441,15.824,46.31.4,19.624-6.124,41.527-20.7,55.25C120.841,133.51,102.872,137.221,87,138.785c-20.741,2.012-42.555,1.207-60.033-11.533C11.951,116.345,3.368,98.778.865,80.719-1.326,64.761.686,47.864,8.062,33.917Z"/>
					</clipPath>
				</defs>
			</svg>
			<div style="clip-path: url(#headshot_mask);
-webkit-clip-path: url(#headshot_mask); background: url(<?php echo esc_url( $headshot ); ?>) no-repeat center center / cover;" class="lg-quote__headshot"><div class="overlay"></div></div>
		<?php endif; ?>
		<?php if ( $bg_img ) : ?>
			<img class="lg-quote__bg" src="<?php echo esc_url( $bg_img ); ?>"/>
		<?php endif; ?>
	</div>
	<?php if ( $alignClass ) : ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
