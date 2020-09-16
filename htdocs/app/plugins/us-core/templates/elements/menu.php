<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Menu element
 *
 * @var $hover_effect    string Hover Effect: 'simple' / 'underline'
 * @var $dropdown_effect string Dropdown Effect
 * @var $vstretch        boolean Stretch menu items vertically to fit the available height
 * @var $indents         int Items Indents
 * @var $mobile_width    int On which screen width menu becomes mobile
 * @var $mobile_behavior boolean Mobile behavior
 * @var $design_options  array
 * @var $id              string
 * @var $classes         string
 * @var $source          string WP Menu source
 */

if ( substr( $source, 0, 9 ) == 'location:' ) {
	$location = substr( $source, 9 );
	$theme_locations = get_nav_menu_locations();
	if ( isset( $theme_locations[$location] ) ) {
		$menu_obj = get_term( $theme_locations[$location], 'nav_menu' );
		if ( $menu_obj ) {
			$source = $menu_obj->slug;
		} else {
			return;
		}
	} else {
		return;
	}
} else {
	$location = NULL;
}

if ( empty( $location ) AND ( empty( $source ) OR ! is_nav_menu( $source ) ) ) {
	return;
}

$_atts['class'] = 'w-nav type_desktop';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ( $vstretch ) ? ' height_full' : '';
$_atts['class'] .= ( $spread ) ? ' spread' : '';
$_atts['class'] .= ' dropdown_' . $dropdown_effect;
$_atts['class'] .= ' m_align_' . $mobile_align;
$_atts['class'] .= ' m_layout_' . $mobile_layout;

if ( $mobile_layout == 'panel' ) {
	$_atts['class'] .= ' m_effect_' . $mobile_effect_p;
}
if ( $mobile_layout == 'fullscreen' ) {
	$_atts['class'] .= ' m_effect_' . $mobile_effect_f;
}
if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}

if ( us_get_option( 'schema_markup' ) ) {
	$_atts['itemscope'] = '';
	$_atts['itemtype'] = 'https://schema.org/SiteNavigationElement';
}

// Output the lement
echo '<nav ' . us_implode_atts( $_atts ) . '>';

echo '<a class="w-nav-control" href="javascript:void(0);" aria-label="' . us_translate( 'Menu' ) . '">';
if ( isset( $mobile_icon_text ) AND $mobile_icon_text == 'left' ) {
	echo '<span>' . us_translate( 'Menu' ) . '</span>';
}
echo '<div class="w-nav-icon"><i></i></div>';
if ( isset( $mobile_icon_text ) AND $mobile_icon_text == 'right' ) {
	echo '<span>' . us_translate( 'Menu' ) . '</span>';
}
echo '</a>';

// Items list
echo '<ul class="w-nav-list level_1 hide_for_mobiles hover_' . $hover_effect . '">';
if ( $location ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container' => FALSE,
			'walker' => new US_Walker_Nav_Menu,
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
} else {
	wp_nav_menu(
		array(
			'menu' => $source,
			'container' => FALSE,
			'walker' => new US_Walker_Nav_Menu,
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
}
echo '<li class="w-nav-close"></li>';
echo '</ul>';

echo '<div class="w-nav-options hidden"';
echo us_pass_data_to_js(
	array(
		'mobileWidth' => intval( $mobile_width ),
		'mobileBehavior' => intval( $mobile_behavior ),
	)
);
echo '></div>';

echo '</nav>';
