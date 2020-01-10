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
		'responsive-spacer'   => [
			'title'    => 'Spacer',
			'supports' => [
				'align' => false,
			],
		],
		'large-quote'         => [
			'title' => 'Large Quote',
		],
		'parallax-image'      => [
			'title'          => 'Parallax Image',
			'enqueue_assets' => function() {
				$block_script = get_template_directory_uri() . '/template-parts/acf-blocks/parallax-image/js/parallax-image.js';
				wp_enqueue_script( 'gsap', '//cdnjs.cloudflare.com/ajax/libs/gsap/3.0.5/gsap.js', array( 'jquery' ), '3.0.5', true );
				wp_enqueue_script( 'scrollmagic', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/ScrollMagic.js', array( 'jquery' ), '2.0.7', true );
				wp_enqueue_script( 'animation-gsap', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/animation.gsap.js', array( 'jquery' ), '2.0.5', true );
				wp_enqueue_script( 'parallax-image-js', $block_script, array(), '1.0.0', true );
			},
		],
		'fact-circle'         => [
			'title' => 'Fact Circle',
		],
		'split-scroll'        => [
			'title'          => 'Split Scrolling Section',
			'supports'       => [
				'align' => [ 'full' ],
			],
			'enqueue_assets' => function() {
				$block_script = get_template_directory_uri() . '/template-parts/acf-blocks/split-scroll/js/split-scroll.js';
				wp_enqueue_script( 'scrollmagic', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/ScrollMagic.min.js', array( 'jquery' ), '1.8.1', true );
				wp_enqueue_script( 'indicators', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/plugins/debug.addIndicators.min.js', array( 'jquery' ), '1.8.1', true );
				// wp_enqueue_script( 'gsap', '//cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/animation.gsap.min.js', array( 'jquery' ), '1.8.1', true );
				wp_enqueue_script( 'split-scroll-js', $block_script, array(), '1.0.0', true );
			},
		],
		'content-image-quote' => [
			'title'    => 'Content / Image / Quote',
			'supports' => [
				'align' => [ 'full' ],
			],
		],
		'infographic'         => [
			'title' => 'Infographic',
		],
		'video-modal'         => [
			'title'    => 'Video Modal',
			'supports' => [
				'align' => [ 'full' ],
			],
		],
		'full-bg-img-content' => [
			'title'    => 'Full Background Image with Content',
			'supports' => [
				'align' => [ 'full' ],
			],
		],
		'image-slider'        => [
			'title'          => 'Image Slider',
			'icon'           => 'images-alt2',
			'description'    => __( 'Image Slider Block' ),
			'enqueue_assets' => function() {
				$block_script = get_template_directory_uri() . '/template-parts/acf-blocks/image-slider/js/image-slider.js';
				wp_enqueue_script( 'slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );
				wp_enqueue_script( 'block-slider', $block_script, array(), '1.0.0', true );
			},
		],
		'split-content'       => [
			'title' => '50 / 50 Image & Content',
		],
	];

	foreach ( $pc_blocks as $block_slug => $block_array ) {
		$render_template = '/template-parts/acf-blocks/' . $block_slug . '/' . $block_slug . '.php';

		$block_type_args = [
			'name'            => $block_slug,
			'render_template' => $render_template,
			'category'        => 'paradowski',
			'keywords'        => array( $block_array['title'], 'paradowski' ),
		];

		if ( $block_array && is_array( $block_array ) ) :
			foreach ( $block_array as $block_key => $block_value ) :
				if ( ! in_array( 'title', $block_array ) ) :
					$block_array['title'] = $block_slug;
				endif;
				$block_type_args[ $block_key ] = $block_array[ $block_key ];
			endforeach;
		endif;
		acf_register_block_type( $block_type_args );
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
