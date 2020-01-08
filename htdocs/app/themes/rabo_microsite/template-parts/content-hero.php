<header class="hero">
	<?php
		global $acf_defaults;
		$string_length    = 140;
		$hero_title       = get_field( 'hero_title' );
		$hero_subtitle    = get_field( 'hero_subtitle' );
		$hero_description = get_field( 'hero_description' );
	if ( strlen( $hero_description ) > $string_length ) {
		$hero_description = substr( $hero_description, 0, $string_length ) . '...';
	}
		$hero_video  = get_field( 'topic_video' );
		$hero_poster = get_field( 'topic_video_poster_image' );
		$hero_link   = get_the_permalink();
	?>
	<div class="hero__body active initial">
		<div class="hero__overlay"></div>
		<?php
		if ( $hero_video ) :
			?>
			<video class="hero__video" width="320" height="240" muted loop poster="<?php echo esc_url( $hero_poster['url'] ); ?>">
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
				echo '<h2>' . $hero_description . '</h2>';
			endif;

			// link
			if ( $hero_link && is_front_page() ) :
				echo '<a class="button button__inline" href="' . esc_url( $hero_link ) . '">' . __( 'Learn More', 'para_theme' ) . '</a>';
			endif;
			?>
		</div>
	</div>
<?php
	$menu_array = get_nav_menu_items_by_location( 'tab-nav' );
if ( $menu_array ) :
	$i = 0;
	foreach ( $menu_array as $hero ) :
		$i++;
		if ( $hero ) :
			// define global defaults
			global $acf_defaults;
			$hero_title       = get_field( 'hero_title', $hero->object_id );
			$hero_subtitle    = get_field( 'hero_subtitle', $hero->object_id );
			$hero_description = get_field( 'hero_description', $hero->object_id );
			if ( strlen( $hero_description ) > $string_length ) {
				$hero_description = substr( $hero_description, 0, $string_length ) . '...';
			}
			$hero_video  = get_field( 'topic_video', $hero->object_id );
			$hero_poster = get_field( 'topic_video_poster_image', $hero->object_id );
			$hero_link   = get_the_permalink( $hero->object_id );
			?>
				<div class="hero__body" data-hero-index="<?php echo $hero->object_id; ?>" data-tab-num="<?php echo $i; ?>">
					<div class="hero__overlay"></div>
				<?php
				if ( $hero_video ) :
					?>
						<video class="hero__video" width="320" height="240" muted loop poster="<?php echo esc_url( $hero_poster['url'] ); ?>">
							<source src="<?php echo esc_url( $hero_video['url'] ); ?>" type="video/mp4">
							Your browser does not support the video tag.
						</video>
						<?php
					else :
						?>
						<img class="hero__image" src="<?php echo esc_url( $hero_poster['url'] ); ?>"/>
					<?php endif; ?>
					<div class="hero__info">
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
							echo '<h2>' . $hero_description . '</h2>';
						endif;

						// link
						if ( $hero_link && is_front_page() ) :
							echo '<a class="button button__inline" href="' . esc_url( $hero_link ) . '">' . __( 'Learn More', 'para_theme' ) . '</a>';
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
		$i = 0;
		foreach ( $menu_array as $hero_tab ) :
			$i++;
			$hero_tab_id    = $hero_tab->object_id;
			$hero_tab_title = get_the_title( $hero_tab_id );
			$hero_tab_link  = get_the_permalink( $hero_tab_id );
			?>
				<div class="hero__tab" data-tab-index="<?php echo $hero_tab_id; ?>">
					<a data-topic="<?php echo $hero_tab_id; ?>" href="<?php echo esc_url( $hero_tab_link ); ?>"><?php echo $hero_tab_title; ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; // if $menu_array. ?>
</header><!-- hero -->
