<?php
/**
 * Template Name: Topic
 * The styleguide template file
 */

get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<?php
			get_template_part( 'template-parts/content', 'hero' );

			if ( have_posts() ) :
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();
					?>
					<div class="topic-vid-wrap"><?php get_template_part( 'template-parts/acf-blocks/video-modal/video-modal' ); ?></div>
					<?php
					get_template_part( 'template-parts/content', get_post_type() );

				endwhile;

			endif;

			get_template_part( 'template-parts/content', 'topicCTA' );
			?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php
get_footer();
