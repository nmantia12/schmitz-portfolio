<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// First add options page
if ( function_exists( 'acf_add_options_page' ) ) {

	acf_add_options_page(
		array(
			'page_title' => 'Site Settings',
			'menu_title' => 'Site Settings',
			'menu_slug'  => 'site-settings',
			'capability' => 'edit_posts',
			'position'   => 2,
			'redirect'   => false,
		)
	);

}

function register_acf_block_types() {

	$pc_blocks = [
		'large-quote'         => 'Large Quote',
		'fullwidth-parallax'  => 'Full Width Image',
		'fact-circle'         => 'Fact Circle',
		'split-scroll'        => 'Split Scrolling Section',
		'content-image-quote' => 'Content / Image / Quote',
		'infographic'         => 'Infographic',
		'hero'                => 'Hero',
		'video-modal'         => 'Video Modal',
		'full-bg-img-content' => 'Full Background Image with Content',
		'image-slider'        => 'Image Slider',
		'split-content'       => '50 / 50 Image & Content',
	];

	foreach ( $pc_blocks as $block_slug => $block_name ) {

		// register a testimonial block.
		acf_register_block_type(
			array(
				'name'            => $block_slug,
				'title'           => $block_name,
				'description'     => __( 'A custom block.' ),
				'render_template' => 'template-parts/acf-blocks/' . $block_slug . '/' . $block_slug . '.php',
				'category'        => 'paradowski',
				'icon'            => 'admin-comments',
				'keywords'        => array( $block_name, 'paradowski' ),
			)
		);
	}
}

// Check if function exists and hook into setup.
if ( function_exists( 'acf_register_block_type' ) ) {
	add_action( 'acf/init', 'register_acf_block_types' );
}

function wedding_gloabal_cf() {
	// define global defaults
	global $acf_defaults;
	$acf_defaults = array(
		'image'   => get_field( 'default_image', 'option' ),
		'title'   => get_field( 'placeholder_title', 'option' ) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		'content' => get_field( 'placeholder_content', 'option' ) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
	);
}
add_action( 'init', 'wedding_gloabal_cf' );
