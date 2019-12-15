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
