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
			<?php
			$menu_array = get_nav_menu_items_by_location( 'main-nav' );
			if ( $menu_array ) :
				?>
				<div class="hero">
					<?php
					$i = 0;
					foreach ( $menu_array as $hero ) :
						$i++;
						if ( 1 === $i ) {
							$active_class = 'active';
						} else {
							$active_class = '';
						}

						if ( $hero ) :
							// define global defaults
							global $acf_defaults;
							$hero_title       = get_field( 'hero_title', $hero->object_id );
							$hero_subtitle    = get_field( 'hero_subtitle', $hero->object_id );
							$hero_description = get_field( 'hero_description', $hero->object_id );
							$hero_video       = get_field( 'topic_video', $hero->object_id );
							$hero_poster      = get_field( 'topic_video_poster_image', $hero->object_id ) ?: $acf_defaults['image'];
							$hero_link        = get_permalink( $hero->object_id );
							?>
							<div class="hero__body <?php echo $active_class; ?>">
								<div class="hero__overlay"></div>
								<?php
								if ( $hero_video ) :
									?>
									<video class="hero__video" width="320" height="240" autoplay muted loop poster="<?php echo esc_url( $hero_poster['url'] ); ?>">
										<source src="<?php echo esc_url( $hero_video['url'] ); ?>" type="video/mp4">
										Your browser does not support the video tag.
									</video>
									<?php
								else :
									?>
									<img class="hero__image" src="<?php echo esc_url( $hero_poster['url'] ); ?>"/>
								<?php endif; ?>
								<div class="hero__info alignwide">
									<?php
									// subtitle
									if ( $hero_subtitle ) :
										echo '<h4>' . $hero_subtitle . '</h4>';
									endif;

									// title
									if ( $hero_title ) :
										echo '<h1>' . $hero_title . '</h1>';
									endif;

									// description
									if ( $hero_description ) :
										echo '<p>' . $hero_description . '</p>';
									endif;

									// link
									if ( $hero_link ) :
										echo '<a class="button" href="' . esc_url( $hero_link ) . '">' . __( 'Learn More', 'para_theme' ) . '</a>';
									endif;
									?>
								</div>
							</div>
							<?php
						endif; // if hero.
					endforeach; // endofreach menu_array.
					?>
					<div class="hero__nav-bar">
						<?php
						foreach ( $menu_array as $hero_tab ) :
							$hero_tab_id    = $hero_tab->object_id;
							$hero_tab_title = get_the_title( $hero_tab_id );
							?>
							<div class="hero__tab">
								<?php echo $hero_tab_title; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; // if $menu_array. ?>
		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php
get_footer();
