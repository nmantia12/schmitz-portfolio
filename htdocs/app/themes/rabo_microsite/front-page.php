<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 */

get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<?php get_template_part( 'template-parts/content', 'hero' ); ?>
		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php
get_footer();
