<?php
$options = [
	'menu_icon'     => 'dashicons-carrot',
	'menu_position' => 20,
	'supports'      => [
		'title',
		'editor',
		'thumbnail',
		'revisions'
	],
	'has_archive' => true,
	'rewrite' => [
		'slug' => 'projects',
		'with_front' => false
	],
	'show_in_rest' => true,
];

$labels = [
	'post_type_name' => 'project',
	'singular'       => 'Project',
	'plural'         => 'Projects',
];

$product = new CPT($labels, $options);
$product->register_taxonomy('category');
$product->register_taxonomy('post_tag');

// ACF Product Fields
// require_once( dirname(__FILE__).'/libs/acf-product-fields.php' );
