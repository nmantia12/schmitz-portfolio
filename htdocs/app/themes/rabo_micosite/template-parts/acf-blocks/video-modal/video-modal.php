<?php

/**
 * Testimonial Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

if ( isset( $block ) ) {
	// Create id attribute allowing for custom "anchor" value.
	$id = 'video-modal-' . $block['id'];
	if ( ! empty( $block['anchor'] ) ) {
		$id = $block['anchor'];
	}

	// Create class attribute allowing for custom "className" and "align" values.
	$className = 'video-modal';
	if ( ! empty( $block['className'] ) ) {
		$className .= ' ' . $block['className'];
	}
	if ( ! empty( $block['align'] ) ) {
		$className .= ' align' . $block['align'];
	}
} else {
	$id        = '';
	$className = 'video-modal';
}

// Load values and assign defaults.
$show_vid = get_field( 'show_video_modal' );
$vid_type = get_field( 'video_type' );
$vid_url  = get_field( 'youtube_or_vimeo_url' );
if ( $vid_type === 'upload' ) {
	$vid_url = get_field( 'video_upload_url' );
}

$vid_image = get_field( 'vid_poster_image' );
if ( $show_vid ) : ?>
	<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
		<a class="video-modal__link" href="<?php echo esc_url( $vid_url ); ?>">
			<img class="video-modal__image" src="<?php echo esc_url( $vid_image ); ?>"/>
		</a>
	</div>
<?php endif; ?>
