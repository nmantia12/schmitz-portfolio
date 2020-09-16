<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output dropdown element
 *
 * @var $source            string Source: 'own' / 'sidebar' / 'wpml' / 'polylang'
 * @var $link_title        string
 * @var $link_icon         string
 * @var $sidebar_id        string
 * @var $links             array
 * @var $wpml_switcher     array
 * @var $dropdown_open     string 'click' / 'hover'
 * @var $dropdown_dir      string 'left' / 'right'
 * @var $dropdown_effect   string
 * @var $size              int
 * @var $size_tablets      int
 * @var $size_mobiles      int
 * @var $design_options    array
 * @var $classes           string
 * @var $id                string
 */

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$classes .= ' source_' . $source;
$classes .= ' dropdown_' . $dropdown_effect;
$classes .= ' drop_to_' . $dropdown_dir;
$classes .= ' open_on_' . $dropdown_open;

$data = array(
	'current' => array(),
	'list' => array(),
);

if ( $source == 'wpml' AND ! function_exists( 'icl_get_languages' ) ) {
	return;
}
if ( $source == 'polylang' AND ! function_exists( 'pll_the_languages' ) ) {
	return;
}

// Custom Links
if ( $source == 'own' AND is_array( $links ) ) {
	foreach ( $links as $link ) {
		$link_atts = usof_get_link_atts( $link['url'] ); // TODO: change it to us_generate_link_atts
		if ( ! isset( $link_atts['href'] ) ) {
			$link_atts['href'] = '';
		}
		$icon = ( ! empty( $link['icon'] ) ) ? us_prepare_icon_tag( $link['icon'] ) : '';
		$data['list'][] = array(
			'icon' => $icon,
			'title' => $link['label'],
			'url' => ( substr( $link_atts['href'], 0, 4 ) == 'http' OR substr( $link_atts['href'], 0, 1 ) == '/' OR strpos( $link_atts['href'], '#' ) !== FALSE OR strpos( $link_atts['href'], '?' ) !== FALSE ) ? $link_atts['href'] : ( '//' . $link_atts['href'] ),
			'target' => ( isset( $link_atts['target'] ) ) ? $link_atts['target'] : NULL,
		);
	}
// WPML Language Switcher
} elseif ( $source == 'wpml' AND function_exists( 'icl_get_languages' ) ) {
	$languages = apply_filters( 'wpml_active_languages', NULL );
	foreach ( $languages as $language ) {
		$data_language = array();
		$data_language['title'] = $data_language['icon'] = '';
		if ( in_array( 'native_lang', $wpml_switcher ) ) {
			$data_language['title'] = $language['native_name'];
			if ( in_array( 'display_lang', $wpml_switcher ) AND ( $language['native_name'] != $language['translated_name'] ) ) {
				$data_language['title'] .= ' (' . $language['translated_name'] . ')';
			}
		} elseif ( in_array( 'display_lang', $wpml_switcher ) ) {
			$data_language['title'] = $language['translated_name'];
		}
		if ( in_array( 'flag', $wpml_switcher ) ) {
			$data_language['flag'] = $language['country_flag_url'];
			$data_language['code'] = $language['language_code'];
		}
		if ( $language['active'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $language['url'];
			$data['list'][] = $data_language;
		}
	}
// Polylang Language Switcher
} elseif ( $source == 'polylang' AND function_exists( 'pll_the_languages' ) ) {
	$pll_langs = pll_the_languages( array( 'raw' => 1 ) );
	foreach ( $pll_langs as $pll_lang ) {
		$data_language = array(
			'title' => $pll_lang['name'],
			'flag' => $pll_lang['flag'],
			'code' => $pll_lang['slug'],
			'icon' => '', // set empty icon
		);
		if ( $pll_lang['current_lang'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $pll_lang['url'];
			$data['list'][] = $data_language;
		}
	}
}
if ( in_array( $source, array( 'sidebar', 'own' ) ) ) {
	$data['current']['title'] = $link_title;
	$data['current']['icon'] = us_prepare_icon_tag( $link_icon );
}

// Output the element
$output = '<div class="w-dropdown' . $classes . '"><div class="w-dropdown-h">';
if ( ! empty( $data['current'] ) ) {
	$output .= '<div class="w-dropdown-current"><a class="w-dropdown-item" href="javascript:void(0)">';
	if ( ! empty( $data['current']['flag'] ) ) {
		$output .= '<img src="' . $data['current']['flag'] . '" alt="' . $data['current']['code'] . '" />';
	}
	$output .= $data['current']['icon'];
	$output .= '<span class="w-dropdown-item-title';
	if ( ! empty( $data['current']['title_class'] ) ) {
		$output .= ' ' . esc_attr( $data['current']['title_class'] );
	}
	$output .= '">' . strip_tags( $data['current']['title'] ) . '</span>';
	$output .= '</a></div>';
}
$output .= '<div class="w-dropdown-list"><div class="w-dropdown-list-h">';
if ( $source == 'sidebar' ) {
	ob_start();
	dynamic_sidebar( $sidebar_id );
	$output .= ob_get_clean();
} else {
	foreach ( $data['list'] as $link ) {
		$output .= '<a class="w-dropdown-item smooth-scroll" href="' . esc_url( $link['url'] ) . '"';
		$output .= ( ! empty( $link['target'] ) ) ? ' target="' . esc_attr( $link['target'] ) . '"' : '';
		$output .= '>';
		if ( ! empty( $link['flag'] ) ) {
			$output .= '<img src="' . $link['flag'] . '" alt="' . $link['code'] . '" />';
		}
		$output .= $link['icon'];
		$output .= '<span class="w-dropdown-item-title';
		if ( ! empty( $link['title_class'] ) ) {
			$output .= ' ' . esc_attr( $link['title_class'] );
		}
		$output .= '">' . strip_tags( $link['title'] ) . '</span>';
		$output .= '</a>';
	}
}
$output .= '</div></div>';
$output .= '</div></div>';

echo $output;
