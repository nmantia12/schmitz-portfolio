<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Define where the local JSON is saved
 *
 * @return string
 */
function pc_local_json_path() {
	return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'pc_local_json_path' );

/**
 * Add our path for the local JSON
 *
 * @param array $paths
 *
 * @return array
 */
function add_local_json_path( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';

	return $paths;
}
add_filter( 'acf/settings/load_json', 'add_local_json_path' );

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

/*
  Create PC Block Category
*/
function pc_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'paradowski',
				'title' => __( 'Paradowski Blocks', 'paradowski' ),
			),
		)
	);
}
add_filter( 'block_categories', 'pc_block_category', 10, 2 );

/*
  Register Block Types
*/
function register_acf_block_types() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

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

/*
  Check if function exists and hook into setup.
*/
if ( function_exists( 'acf_register_block_type' ) ) {
	add_action( 'acf/init', 'register_acf_block_types' );
}

/*
   Global default placeholders
*/
function define_gloabal_cf() {
	global $acf_defaults;
	$acf_defaults = array(
		'image'   => get_field( 'default_image', 'option' ),
		'title'   => get_field( 'placeholder_title', 'option' ) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		'content' => get_field( 'placeholder_content', 'option' ) ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
	);
}
add_action( 'init', 'define_gloabal_cf' );
