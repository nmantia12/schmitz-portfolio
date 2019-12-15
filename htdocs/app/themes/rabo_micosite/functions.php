<?php
 /**
  * Include the includes: First the /inc/init folder and then /inc folder
  */
$includeLibs = array_diff(scandir(__DIR__ . '/inc/init/'), ['.', '..']);
foreach ($includeLibs as $file) {
	if (preg_match('/\.php$/', $file)) {
    	require_once locate_template('/inc/init/' . $file);
	}
}

$includes = array_diff(scandir(__DIR__ . '/inc'), ['.', '..']);
foreach ($includes as $file) {
	if (preg_match('/\.php$/', $file)) {
    	require_once locate_template('/inc/' . $file);
	}
}


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
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
    /*
	 * Enable navigation elements
	 */
	register_nav_menus(
			array(
				'main-nav' => __( 'Header Menu' ),
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
			'height'      => 190,
			'width'       => 190,
			'flex-width'  => false,
			'flex-height' => false,
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
	add_editor_style( 'style-editor.css' );

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	// Adds support for editor color palette.
	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Wacky Orange', 'para_theme' ),
			'slug'  => 'wacky-orange',
			'color'	=> '#ffa500',
		),
		array(
			'name'  => __( 'Medium gray', 'para_theme' ),
			'slug'  => 'medium-gray',
			'color' => '#999',
		),
		array(
			'name'  => __( 'Dark gray', 'para_theme' ),
			'slug'  => 'dark-gray',
			'color' => '#333',
		),
	) );

}

add_action( 'after_setup_theme', 'theme_setup');


function get_file_time($path) {
	$filetime = @filemtime( str_replace(get_stylesheet_directory_uri(), get_stylesheet_directory(), $path) );
	return ($filetime) ? $filetime : '';
}

/**
 * Enqueue script and styles.
 */
function site_scripts() {
    // Typekit fonts
    // wp_enqueue_style( 'site-fonts', 'https://use.typekit.net/mum7jvj.css');

    // Theme stylesheet.
	wp_enqueue_style( 'site-style', get_stylesheet_directory_uri() . '/assets/css/main.css');

    // Theme javascript.
	$main_js_file = get_stylesheet_directory_uri() . '/assets/js/main.js';
	wp_enqueue_script( 'site-js', $main_js_file, ['jquery'], get_file_time($main_js_file), true);

}
add_action( 'wp_enqueue_scripts', 'site_scripts');

//removes that pesky jquery migrate console error
add_action('wp_default_scripts', function ($scripts) {
    if (!empty($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
    }
});

/**
 * Prefetch Typekit fonts
 */
function typekit_resource_hints( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type) {
        $urls[] = [
            'href' => '//use.typekit.net'
        ];

        $urls[] = [
            'href' => '//use.typekit.com'
        ];
    }

    return $urls;
}
// add_filter( 'wp_resource_hints', 'typekit_resource_hints', 10, 2);


function cc_mime_types($mimes) {
     $mimes['svg'] = 'image/svg+xml';
     return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

//Function to return excerpt based on post id and character length desired.
function the_excerpt_max_charlength($postID, $charlength) {
	$excerpt = get_the_excerpt($postID);
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$content = mb_substr( $subex, 0, $excut );
		} else {
			$content .= $subex;
		}
		$content .= ' ...';
	} else {
		$content = $excerpt;
    }
    return $content;
}
