<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
?>
	<footer id="colophon" class="site-footer">
		<div class="site-info alignwide">
			<?php if ( has_custom_logo() ) : ?>
				<div class="site-logo"><?php the_custom_logo(); ?></div>
				<?php
			endif;

			if ( has_nav_menu( 'footer-nav' ) ) :
				?>
				<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'twentynineteen' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-nav',
							'menu_class'     => 'footer-nav',
							'depth'          => 1,
						)
					);
					?>
				</nav><!-- .footer-navigation -->
			<?php endif; ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
