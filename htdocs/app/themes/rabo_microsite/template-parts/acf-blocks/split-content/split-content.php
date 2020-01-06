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
$id = 'split-content-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'split-content';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

// global defaults
global $acf_defaults;

// Load values and assign defaults.
$img_position = get_field( '50_50_image_position' ) ?: 'left';
$img          = get_field( '50_50_image' ) ?: $acf_defaults['image'];
$title        = get_field( '50_50_title' );
$copy         = get_field( '50_50_content' ) ?: $acf_defaults['content'];
$button       = get_field( '50_50_button' );
?>

<div class="<?php echo $className . ' ' . $img_position; ?>" id="<?php echo $id; ?>">
	<?php if ( 'left' === $img_position ) : ?>
		<div class="split-content__col">
			<div class="split-content__image">
				<img src="<?php echo esc_url( $img ); ?>"/>
			</div>
		</div>
	<?php endif; ?>
	<div class="split-content__col">
		<div class="split-content__info">
			<?php if ( $title ) : ?>
				<h2 class="split-content__title"><?php echo $title; ?></h2>
			<?php endif; ?>
			<p class="split-content__content"><?php echo $copy; ?></p>
			<?php if ( $button ) : ?>
				<a class="button" href="<?php echo $button['url']; ?>" <?php echo $button['target'] ? $button['target'] : ''; ?>><?php echo $button['title']; ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( 'right' === $img_position ) : ?>
		<div class="split-content__col">
			<div class="split-content__image">
				<img src="<?php echo esc_url( $img ); ?>"/>
			</div>
		</div>
	<?php endif; ?>
</div>
