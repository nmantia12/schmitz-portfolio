<?php

/**
 * split-scroll Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'split-scroll-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'split-scroll';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}
if ( $is_preview ) {
	$className .= ' is-admin';
}

if ( have_rows( 'split_scrolling_sections' ) ) :
	?>
	<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
		<div class="duration-bar-wap">
			<div class="duration-bar"></div>
		</div>
		<div class="split-scroll__mobile">
			<?php
			while ( have_rows( 'split_scrolling_sections' ) ) :
				the_row();
				$section_title   = get_sub_field( 'section_title' );
				$section_content = get_sub_field( 'section_content' );
				$section_img     = get_sub_field( 'section_image' );
				?>
				<div class="split-scroll__col">
					<?php if ( $section_img ) : ?>
						<div class="split-scroll__img-wrap">
							<img class="split-scroll__img" src="<?php echo esc_url( $section_img ); ?>" />
						</div>
					<?php endif; ?>
					<div class="split-scroll__content-wrap">
						<?php if ( $section_title ) : ?>
							<div class="split-scroll__title">
								<?php echo $section_title; ?>
							</div>
						<?php endif; ?>
						<?php if ( $section_content ) : ?>
							<div class="split-scroll__content">
								<?php echo $section_content; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php	endwhile; ?>
		</div>
		<div class="split-scroll__inner">
			<div class="split-scroll__col content">
				<?php
				while ( have_rows( 'split_scrolling_sections' ) ) :
					the_row();
					$section_title   = get_sub_field( 'section_title' );
					$section_content = get_sub_field( 'section_content' );
					$section_img     = get_sub_field( 'section_image' );
					?>
					<div class="split-scroll__content-wrap">
						<?php if ( $section_title ) : ?>
							<div class="split-scroll__title">
								<?php echo $section_title; ?>
							</div>
						<?php endif; ?>
						<?php if ( $section_content ) : ?>
							<div class="split-scroll__content">
								<?php echo $section_content; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php	endwhile; ?>
			</div>
			<div class="split-scroll__col">
				<?php
				while ( have_rows( 'split_scrolling_sections' ) ) :
					the_row();
					$section_title   = get_sub_field( 'section_title' );
					$section_content = get_sub_field( 'section_content' );
					$section_img     = get_sub_field( 'section_image' );
					?>
						<?php if ( $section_img ) : ?>
							<img class="split-scroll__img" src="<?php echo esc_url( $section_img ); ?>" />
						<?php endif; ?>
					<?php	endwhile; ?>
				</div>
			</div>
		</div>
	<?php
endif;
