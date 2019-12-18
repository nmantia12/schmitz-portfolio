<?php

function theme_setup() {
	/*
	 * Let WordPress manage the document title.
	 * Enable support for Post Thumbnails on posts and pages.
	 */
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);
	/*
	 * Enable navigation elements
	 */
	register_nav_menus(
		array(
			'main-nav'   => __( 'Header Menu' ),
			'footer-nav' => __( 'Footer Menu' ),
			'social-nav' => __( 'Social Links Menu' ),
		)
	);

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 198,
			'width'       => 28,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for Block Styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Enqueue editor styles.
	add_editor_style( '/assets/css/editor.css' );

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	// disbale custom colors
	add_theme_support( 'disable-custom-colors' );

	// Adds support for editor color palette.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Primary Indigo', 'para_theme' ),
				'slug'  => 'blue',
				'color' => '#002664',
			),
			array(
				'name'  => __( 'Teal', 'para_theme' ),
				'slug'  => 'teal',
				'color' => '#90D1E3',
			),
			array(
				'name'  => __( 'Orange', 'para_theme' ),
				'slug'  => 'orange',
				'color' => '#FF6700',
			),
			array(
				'name'  => __( 'Grey', 'para_theme' ),
				'slug'  => 'grey',
				'color' => '#5E6A71',
			),
		)
	);
}

add_action( 'after_setup_theme', 'theme_setup' );
