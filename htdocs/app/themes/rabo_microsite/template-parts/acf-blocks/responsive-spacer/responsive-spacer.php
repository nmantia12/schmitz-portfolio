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
$id = 'responsive-spacer-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'responsive-spacer';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}

$mobile_space  = get_field( 'mobile_spacer_height' );
$tablet_space  = get_field( 'tablet_spacer_height' );
$desktop_space = get_field( 'desktop_spacer_height' );
?>

<style>
	<?php echo '#responsive-spacer-' . $block['id']; ?> {
		height: <?php echo $mobile_space . 'px'; ?>;
	}
	@media (min-width: 768px) {
		<?php echo '#responsive-spacer-' . $block['id']; ?> {
			height: <?php echo $tablet_space . 'px'; ?>;
		}
	}
	@media (min-width: 992px) {
		<?php echo '#responsive-spacer-' . $block['id']; ?> {
			height: <?php echo $desktop_space . 'px'; ?>;
		}
	}
</style>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>"></div>
