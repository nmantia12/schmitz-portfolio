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
$id = 'image-slider-wrap-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'image-slider-wrap';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$alignClass = 'align' . $block['align'];
} else {
	$alignClass = '';
}

// Load values and assign defaults.
if ( have_rows( 'image_slider' ) ) : ?>
	<?php if ( $alignClass ) : ?>
		<div class="<?php echo $alignClass; ?>">
	<?php endif; ?>
	<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
		<div class="image-slider">
			<?php
			while ( have_rows( 'image_slider' ) ) :
				the_row();
				$slide_img = get_sub_field( 'slider_image' );
				$slide_des = get_sub_field( 'slider_image_caption' );
				?>
				<div class="image-slider__slide">
					<div class="image-slider__image">
						<img src="<?php echo esc_url( $slide_img ); ?>"/>
					</div>
					<?php if ( $slide_des ) : ?>
						<p class="image-slider__caption body-2 has-grey-color text-center italic">
							<?php echo $slide_des; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
	<?php if ( $alignClass ) : ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
