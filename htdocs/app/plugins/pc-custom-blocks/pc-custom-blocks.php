<?php
/*
*   Plugin Name: Paradowski Creative's Custom Gutenberg Blocks
*/

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/writing-your-first-block-type/
 */

function movie_block_init() {
    $block_slug = 'movie';
    $editor_css = '/assets/css/blocks.css';
    $style_css = '/assets/css/main.css';

    wp_register_script(
        'movie-block-editor',
        get_template_directory_uri() . '/assets/js/' . $block_slug . '.js',
        array(
            'wp-editor',
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-data',
            'wp-components',
        )
    );

    wp_register_style(
        'movie-block-editor',
        get_template_directory_uri() . $editor_css,
        array()
    );

    wp_register_style(
        'movie-block',
        get_template_directory_uri() . $editor_css,
        array()
    );

    register_block_type( 'movie/main', array(
        'editor_script' => 'movie-block-editor',
        'editor_style'  => 'movie-block-editor',
        'style'         => 'movie-block',
    ) );
}
add_action( 'init', 'movie_block_init' );

function book_block_init() {
    $block_slug = 'book';
    $editor_css = '/assets/css/blocks.css';
    $style_css = '/assets/css/main.css';

    wp_register_script(
        'book-block-editor',
        get_template_directory_uri() . '/assets/js/' . $block_slug . '.js',
        array(
            'wp-editor',
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-data',
            'wp-components',
        )
    );

    wp_register_style(
        'book-block-editor',
        get_template_directory_uri() . $editor_css,
        array()
    );

    wp_register_style(
        'book-block',
        get_template_directory_uri() . $editor_css,
        array()
    );

    register_block_type( 'book/main', array(
        'editor_script' => 'book-block-editor',
        'editor_style'  => 'book-block-editor',
        'style'         => 'book-block',
    ) );
}
add_action( 'init', 'book_block_init' );
