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
if ( $is_preview ) {
	$className .= ' is-admin';
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
			<div class="split-content__image parallax-effect">
				<img src="<?php echo esc_url( $img ); ?>" data-parallax="50"/>
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
			<div class="split-content__image parallax-effect">
				<img src="<?php echo esc_url( $img ); ?>" data-parallax="-50"/>
			</div>
		</div>
	<?php endif; ?>
	<div class="svg-wrap parallax-effect">
		<svg viewBox="0 0 1 1" preserveAspectRation="none" data-parallax="50">
			<path d="M 0.05 0.24 C 0.08 0.18 0.13 0.13 0.18 0.09 C 0.29 0.02 0.42 -0.01 0.55 0 C 0.67 0.01 0.8 0.04 0.88 0.14 C 0.89 0.14 0.89 0.14 0.89 0.15 C 0.97 0.23 0.99 0.37 1 0.48 C 1 0.62 0.96 0.78 0.86 0.87 C 0.78 0.95 0.67 0.98 0.56 0.99 C 0.43 1.01 0.29 1 0.18 0.91 C 0.08 0.83 0.02 0.71 0.01 0.58 C -0.01 0.46 0 0.34 0.05 0.24 Z"/>
		</svg>
	</div>
</div>
