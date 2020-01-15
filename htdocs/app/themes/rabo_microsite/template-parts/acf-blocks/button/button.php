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
$id = 'button-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'button-block';
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
$button = get_field( 'button_link' );
?>

<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<?php if ( $button ) : ?>
		<a class="pc_button" href="<?php echo $button['url']; ?>" <?php echo $button['target'] ? $button['target'] : ''; ?>><?php echo $button['title']; ?></a>
	<?php endif; ?>
</div>
