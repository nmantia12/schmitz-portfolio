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
$id = 'hero-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'hero-block pc_block';
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
$image = get_field( 'hero_background_image' );
$title = get_field( 'hero_title' );
$excerpt = get_field( 'hero_excerpt' );
$button = get_field( 'button' );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>" style="background: url(<?php echo $image['url']; ?>) no-repeat center center / cover;">
	<div class="overlay"></div>
	<?php if ( $title ) : ?>
		<h1 class="hero-block__title"><?php echo $title; ?></h1>
	<?php endif; ?>
		<?php if ( $excerpt ) : ?>
			<p class="hero-block__des"><?php echo $title; ?></p>
	<?php endif; ?>
	<?php if ( $button ) : ?>
		<a class="pc_button pc_button__white" href="<?php echo $button['url']; ?>" <?php echo $button['target'] ? $button['target'] : ''; ?>><?php echo $button['title']; ?></a>
	<?php endif; ?>
</div>
