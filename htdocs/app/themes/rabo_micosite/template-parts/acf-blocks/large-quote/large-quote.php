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
			<img class="lg-quote__headshot" src="<?php echo esc_url( $headshot ); ?>"/>
		<?php endif; ?>
		<?php if ( $bg_img ) : ?>
			<img class="lg-quote__bg" src="<?php echo esc_url( $bg_img ); ?>"/>
		<?php endif; ?>
	</div>
	<?php if ( $alignClass ) : ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
