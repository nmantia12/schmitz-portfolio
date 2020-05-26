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
<!-- Jira Issue tracker -->
<?php
$env = getenv( 'WP_ENV' );
if ( defined( 'WP_ENV' ) && $env === 'prod' ) {
	?>
	<style>.atlwdg-trigger { z-index: 99999 !important; }</style>
	<script type="text/javascript" src="https://paradowskicreative.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/-40wwlj/b/20/a44af77267a987a660377e5c46e0fb64/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=fb7d38ba"></script>
<?php } ?>

</body>
</html>
