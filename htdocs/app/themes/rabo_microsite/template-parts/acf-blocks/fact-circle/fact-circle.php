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
$id = 'fact-circle-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'fact-circle';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

// Load values and assign defaults.
$fact_sub         = get_field( 'fact_subhead' ) ?: 'Fact Subhead';
$fact     = get_field( 'fact' ) ?: 'Fact Goes Here....';
$fact_content = get_field( 'bullet_points' );
?>
<div class="<?php echo $className; ?>" id="<?php echo $id; ?>" >
	<div class="fact-circle__bg">
		<div class="fact-circle__shape">
				<svg class="svg__fill" xmlns="http://www.w3.org/2000/svg" width="609.021" height="553.016" viewBox="0 0 609.021 553.016">
				<path id="Path_366" data-name="Path 366" d="M31.872,134.4c18.266-34.343,44.868-64.083,80.692-85.5C178.891,9.77,259.228-2.976,335.486.565,409.262,4.105,486.052,22.693,540.142,75.8c2.128,1.947,4.079,4.072,6.03,6.2,44.159,47.089,61.361,120.554,62.78,183.4,1.6,77.714-24.3,164.456-82.111,218.8-47.528,44.61-118.821,59.3-181.778,65.5-82.288,7.966-168.832,4.78-238.174-45.672C47.3,460.829,13.251,391.259,3.32,319.741-5.37,256.543,2.61,189.627,31.872,134.4Z" fill="#001e50"/>
			</svg>
			<svg class="svg__stroke" xmlns="http://www.w3.org/2000/svg" width="546.362" height="499.645" viewBox="0 0 546.362 499.645">
				<path id="Path_367" data-name="Path 367" d="M27.022,114.147C42.508,84.979,65.061,59.719,95.432,41.526,151.665,8.3,219.775-2.527,284.427.48c62.547,3.007,127.65,18.794,173.508,63.9,1.8,1.654,3.458,3.458,5.112,5.262,37.438,39.994,52.022,102.391,53.225,155.767,1.353,66.005-20.6,139.679-69.614,185.837-40.295,37.889-100.737,50.369-154.112,55.631-69.764,6.766-143.137,4.06-201.925-38.791C40.1,391.4,11.234,332.311,2.815,271.568-4.553,217.891,2.213,161.058,27.022,114.147Z" transform="translate(15.027 15.009)" fill="none" stroke="#90d1e3" stroke-width="30" stroke-dasharray="1 6.5" opacity="0.7"/>
			</svg>
			<div class="fact-circle__content-wrap">
				<?php if ( $fact_sub ) : ?>
					<h4 class="fact-circle__subhead has-white-color"><?php echo $fact_sub; ?></h4>
				<?php endif; ?>
				<?php if ( $fact ) : ?>
					<h2 class="fact-circle__fact has-white-color"><?php echo $fact; ?></h2>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $fact_content ) : ?>
			<div class="fact-circle__content has-blue-color">
				<?php echo $fact_content; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
