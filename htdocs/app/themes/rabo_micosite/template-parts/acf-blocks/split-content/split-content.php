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
$id = 'testimonial-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'testimonial';
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
$copy         = get_field( '50_50_content' ) ?: $acf_defaults['content'];
$button       = get_field( '50_50_button' );

