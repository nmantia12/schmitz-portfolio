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
$id = 'fact-circle-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'fact-circle';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

// Load values and assign defaults.
$fact         = get_field( 'fact_subhead' ) ?: 'Fact Subhead';
$fact_sub     = get_field( 'fact' ) ?: 'Fact Goes Here....';
$fact_content = get_field( 'bullet_points' );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>" >
	<div class="fact-circle__shape">
		<?php if ( $fact_sub ) : ?>
			<h4 class="fact-circle__subhead has-white-color"><?php echo $fact_sub; ?></h4>
		<?php endif; ?>
		<?php if ( $fact ) : ?>
			<h2 class="fact-circle__fact has-white-color"><?php echo $fact; ?></h2>
		<?php endif; ?>
	</div>
	<?php if ( $fact_content ) : ?>
		<div class="fact-circle__content">
			<?php echo $fact_content; ?>
		</div>
	<?php endif; ?>
</div>
