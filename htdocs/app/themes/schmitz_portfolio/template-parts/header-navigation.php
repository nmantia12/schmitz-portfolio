<?php
/**
 * The header navigation
 */
?>
<div class="site-branding">
	<?php if ( has_custom_logo() ) : ?>
		<div class="site-logo"><?php the_custom_logo(); ?></div>
	<?php endif; ?>
	<div class="menu-right">
		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Top Menu', 'para_theme' ); ?>">
			<div class="menu-container">
				<!-- <input type="checkbox" role="button" aria-haspopup="true" id="toggle" class="vh">
				<label for="toggle" data-opens-menu>
					<span role="button" class="hamburger"><?php get_template_part( 'assets/img/inline', 'menu_icon.svg' ); ?></span>
					<span class="vh expanded-text">Menu expanded</span><span class="vh collapsed-text">Menu collapsed</span>
				</label> -->
				<div role="menu" data-menu-origin="left">
					<img class="menu-close" src="<?php echo get_stylesheet_directory_uri() . '/assets/img/x.svg'; ?>"/>
					<div class="alignfull overlay-menu-wrap">
						<div class="menu-center">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'main-nav',
								'menu_class'     => 'main-menu',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							)
						);
						?>
						</div>
					</div>
				</div>
			</div>
		</nav><!-- #site-navigation -->
		<?php get_search_form(); ?>
	</div>
</div><!-- .site-branding -->
