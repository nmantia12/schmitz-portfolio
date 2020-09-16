<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template header
 */

$us_layout = US_Layout::instance();
?>
<!DOCTYPE HTML>
<html class="<?php echo $us_layout->html_classes() ?>" <?php language_attributes( 'html' ) ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php

	wp_head();

	// Theme Options CSS
	if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
		?>
		<style id="us-theme-options-css"><?php echo us_get_theme_options_css() ?></style>
		<?php
	}

	// Header CSS
	if ( $us_layout->header_show != 'never' ) {
		?>
		<style id="us-header-css"><?php echo us_minify_css( us_get_template( 'templates/css-header' ) ) ?></style>
		<?php
	}

	// Custom CSS from Theme Options
	if ( ! us_get_option( 'optimize_assets', 0 ) AND $us_custom_css = us_get_option( 'custom_css', '' ) ) {
		?>
		<style id="us-custom-css"><?php echo us_minify_css( $us_custom_css ) ?></style>
		<?php
	}

	// Custom HTML before </head>
	echo us_get_option( 'custom_html_head', '' );

	// Helper action
	do_action( 'us_before_closing_head_tag' );
	?>
</head>
<body <?php body_class( 'l-body ' . $us_layout->body_classes() );
if ( us_get_option( 'schema_markup' ) ) {
	if ( us_is_faqs_page() ) {
		echo ' itemscope itemtype="https://schema.org/FAQPage"';
	} else {
		echo ' itemscope itemtype="https://schema.org/WebPage"';
	}
} ?>>
<?php
global $us_iframe;
if ( ! ( isset( $us_iframe ) AND $us_iframe ) AND us_get_option( 'preloader' ) != 'disabled' AND defined( 'US_CORE_VERSION' ) ) {
	add_action( 'us_before_canvas', 'us_display_preloader', 100 );
	function us_display_preloader() {
		$preloader_type = us_get_option( 'preloader' );
		if ( ! in_array( $preloader_type, array_merge( us_get_preloader_numeric_types(), array( 'custom' ) ) ) ) {
			$preloader_type = 1;
		}

		if ( $preloader_type == 'custom' AND $preloader_image = us_get_option( 'preloader_image', '' ) ) {
			$img_arr = explode( '|', $preloader_image );
			$preloader_image_html = wp_get_attachment_image( $img_arr[0], 'medium' );
			if ( empty( $preloader_image_html ) ) {
				$preloader_image_html = us_get_img_placeholder( 'medium' );
			}
		} else {
			$preloader_image_html = '';
		}

		?>
		<div class="l-preloader">
			<div class="l-preloader-spinner">
				<div class="g-preloader type_<?php echo $preloader_type ?>">
					<div><?php echo $preloader_image_html ?></div>
				</div>
			</div>
		</div>
		<?php
	}
}

do_action( 'us_before_canvas' ) ?>

<div class="l-canvas <?php echo $us_layout->canvas_classes() ?>">
	<?php
	if ( $us_layout->header_show != 'never' ) {

		do_action( 'us_before_header' );

		us_load_template( 'templates/l-header' );

		do_action( 'us_after_header' );

	} ?>
