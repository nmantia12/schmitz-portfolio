<?php

$current_post_id = get_the_id();
$menu_name       = 'tab-nav';
$locations       = get_nav_menu_locations();
$menu            = wp_get_nav_menu_object( $locations[ $menu_name ] );
$menuitems       = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
$i               = 0;

foreach ( $menuitems as $item ) {
		$menuitems_id_array[] = $item->object_id;
	if ( $item->object_id == $current_post_id ) {
			$prevMenuPosition = $i - 1;
			$nextMenuPosition = $i + 1;
	}
		$i++;
}

if ( $nextMenuPosition > sizeof( $menuitems_id_array ) - 1 ) {
		$next_id = $menuitems_id_array[0];
} else {
		$next_id = $menuitems_id_array[ $nextMenuPosition ];
}

if ( $next_id ) :
	$topic_cta_title    = get_field( 'hero_title', $next_id );
	$topic_cta_subtitle = get_field( 'hero_subtitle', $next_id );
	$topic_cta_video    = get_field( 'topic_video', $next_id );
	$topic_cta_poster   = get_field( 'topic_video_poster_image', $next_id );
	$topic_cta_link     = get_the_permalink( $next_id );
	?>
	<div class="topic-cta">
		<?php if ( $topic_cta_video ) : ?>
			<video class="topic-cta__video" width="320" height="240" muted loop poster="<?php echo esc_url( $topic_cta_poster['url'] ); ?>">
				<source src="<?php echo esc_url( $topic_cta_video['url'] ); ?>" type="video/mp4">
				Your browser does not support the video tag.
			</video>
		<?php else : ?>
			<img class="topic-cta__image" src="<?php echo esc_url( $topic_cta_poster['url'] ); ?>"/>
		<?php endif; ?>

		<div class="topic-cta__info">
			<?php
			if ( $topic_cta_subtitle ) :
				echo '<h4>' . $topic_cta_subtitle . '</h4>';
			endif;

			if ( $topic_cta_title ) :
				echo '<h1>' . $topic_cta_title . '</h1>';
			endif;

			if ( $topic_cta_link ) :
				?>
				<a class="button button__ghost-white" href="<?php echo $topic_cta_link; ?>">Learn More</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
