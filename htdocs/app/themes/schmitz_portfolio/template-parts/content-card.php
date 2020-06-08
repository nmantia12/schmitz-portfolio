<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package para_theme
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if (has_post_thumbnail()) {
		para_theme_post_thumbnail();
	} else {
		?>
		<div class="post-thumbnail">
			<?php echo wp_get_attachment_image(get_field('default_image', 'option')['ID'], 'full', false); ?>
		</div>
	<?php
	}
?>
<div class="entry">
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			para_theme_posted_on();
			para_theme_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<?php para_theme_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</div>

</article><!-- #post-<?php the_ID(); ?> -->
