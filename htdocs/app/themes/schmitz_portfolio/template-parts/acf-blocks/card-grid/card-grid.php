<?php

/**
 * Testimonial Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'card-grid-block-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'card-grid-block pc_block';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}
if ( $is_preview ) {
	$className .= ' is-admin';
}

// Load values and assign defaults.
$title = get_field( 'cg_section_title' );
$type = get_field( 'cg_select_post_type' );
$categories = get_field( 'cg_select_taxonomy' );
$number = get_field( 'cg_number_of_posts' );
$button = get_field( 'cg_section_link' );
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args = [
	'post_type'      => $type,
	'posts_per_page' => $number,
	'post_status' => 'publish',
	'paged' => $paged,
	'orderby'        => 'date',
	'order'          => 'ASC',
];

if ( $categories ) :
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $categories,
		),
	);
endif;

$query = new WP_Query( $args );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>">
	<?php if ( $title ) : ?>
		<h3 class="pc_block__title"><?php echo $title; ?></h3>
	<?php endif; ?>
	<?php if ( $button ) : ?>
		<a class="pc_button pc_button__inline" href="<?php echo $button['url']; ?>" <?php echo $button['target'] ? $button['target'] : ''; ?>><?php echo $button['title']; ?></a>
	<?php endif; ?>
	<?php if ( $query->have_posts() ) : ?>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
					echo get_the_title();
					get_template_part( 'template-parts/content', 'card' );
			endwhile;?>
			<?php
			// Previous/next page navigation.
			// $GLOBALS['wp_query']->max_num_pages = $query->max_num_pages;
			// $GLOBALS['wp_query']->query_vars['paged'] = $query->query_vars['paged'];
			// get_template_part( 'template-parts/pagination' );
			// If no content, include the "No posts found" template.
			wp_reset_postdata();?>
		<?php endif; ?>
</div>
