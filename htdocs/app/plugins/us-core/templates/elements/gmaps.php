<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_gmaps
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @param  $marker_address             string Marker 1 address
 * @param  $marker_text                string Marker 1 text
 * @param  $show_infowindow            bool Show Marker's InfoWindow
 * @param  $custom_marker_img          int Custom marker image (from WordPress media)
 * @param  $custom_marker_size         int Custom marker size
 * @param  $markers                    array Additional Markers
 * @param  $provider                   string Map Provider: 'google' / 'osm'
 * @param  $type                       string Map type: 'roadmap' / 'satellite' / 'hybrid' / 'terrain'
 * @param  $height                     int Map height
 * @param  $zoom                       int Map zoom
 * @param  $hide_controls              bool Hide all map controls
 * @param  $disable_dragging           bool Disable dragging on touch screens
 * @param  $disable_zoom               bool Disable map zoom on mouse wheel scroll
 * @param  $map_bg_color               string Map Background Color
 * @param  $el_class                   string Extra class name
 * @param  $map_style_json             string Map Style
 * @param  $layer_style                string Leaflet Map TileLayer
 *
 * @filter 'us_maps_js_options' Allows to filter options, passed to JavaScript
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

global $us_maps_index;
$us_maps_index = isset( $us_maps_index ) ? $us_maps_index + 1 : 1;

$classes = isset( $classes ) ? $classes : '';
$classes .= ' provider_' . $provider;

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) ) ? ' id="' . esc_attr( $el_id ) . '"' : ' id="us_map_' . esc_attr( $us_maps_index ) . '"';

$classes .= ' us_map_' . $us_maps_index;

// Decoding base64-encoded HTML attributes
if ( ! empty( $marker_text ) ) {
	$marker_text = rawurldecode( base64_decode( $marker_text ) );
}

if ( ! in_array( $zoom, array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20 ) ) ) {
	$zoom = 14;
}

// Form all options needed for JS
$script_options = array();
if ( ! empty( $marker_address ) ) {
	$script_options['address'] = $marker_address;
} else {
	return NULL;
}
$script_options['markers'] = array(
	array_merge(
		$script_options, array(
			'html' => ( ! empty( $marker_text ) ) ? $marker_text : $marker_address,
			'infowindow' => $show_infowindow,
		)
	),
);

if ( empty( $markers ) ) {
	$markers = array();
} else {
	$markers = json_decode( urldecode( $markers ), TRUE );
	if ( ! is_array( $markers ) ) {
		$markers = array();
	}
}

foreach ( $markers as $index => $marker ) {
	/**
	 * Filtering the included markers
	 *
	 * @param $marker ['marker_address'] string Address
	 * @param $marker ['marker_text'] string Marker Text
	 * @param $marker ['marker_img'] string Marker Image
	 * @param $marker ['marker_size'] string Marker Size
	 */
	if ( ! empty( $marker['marker_address'] ) ) {
		$script_options['markers'][] = array(
			'html' => ( ! empty( $marker['marker_text'] ) ) ? $marker['marker_text'] : $marker['marker_address'],
			'address' => $marker['marker_address'],
			'marker_img' => ( ! empty( $marker['marker_img'] ) ) ? wp_get_attachment_image_src( intval( $marker['marker_img'] ), 'thumbnail' ) : NULL,
			'marker_size' => ( ! empty( $marker['marker_size'] ) ) ? array(
				$marker['marker_size'],
				$marker['marker_size'],
			) : NULL,
		);
	}
}

if ( ! empty( $zoom ) ) {
	$script_options['zoom'] = intval( $zoom );
}

if ( ! empty( $type ) AND $provider == 'google' ) {
	$type = strtoupper( $type );
	if ( in_array( $type, array( 'ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN' ) ) ) {
		$script_options['maptype'] = $type;
	}
}

if ( ! empty( $map_bg_color ) ) {
	$script_options['mapBgColor'] = $map_bg_color;
}

if ( $custom_marker_img != '' AND $custom_marker_img_src = wp_get_attachment_image_url( $custom_marker_img, 'thumbnail' ) ) {
	$custom_marker_size = intval( $custom_marker_size );
	$script_options['icon'] = array(
		'url' => $custom_marker_img_src,
		'size' => array( $custom_marker_size, $custom_marker_size ),
	);
}

if ( empty( $height ) ) {
	$height = 400;
}
$script_options['height'] = $height;

if ( $provider == 'osm' ) {
	if ( ! empty( $layer_style ) ) {
		$script_options['style'] = $layer_style;
	} else {
		$script_options['style'] = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'; // default value for empty case
	}
}

if ( $hide_controls ) {
	$script_options['hideControls'] = TRUE;
}

if ( $disable_zoom ) {
	$script_options['disableZoom'] = TRUE;
}

if ( $disable_dragging ) {
	$script_options['disableDragging'] = TRUE;
}

$script_options = apply_filters( 'us_maps_js_options', $script_options, get_the_ID(), $us_maps_index );

// Enqueue relevant scripts
if ( $provider == 'osm' ) {
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-lmap' );
	}
} elseif ( $provider == 'google' ) {
	wp_enqueue_script( 'us-google-maps' );
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-gmap' );
	}
}

// Output the element
$output = '<div class="w-map' . $classes . '"' . $el_id . '>';
$output .= '<div class="w-map-json"' . us_pass_data_to_js( $script_options ) . '></div>';
if ( $provider == 'google' AND $map_style_json != '' ) {
	$output .= '<div class="w-map-style-json" onclick=\'return ' . str_replace( "'", '&#39;', rawurldecode( base64_decode( $map_style_json ) ) ) . '\'></div>';
}
$output .= '</div>';

// If we are in front end editor mode, apply JS to maps
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	if ( $provider == 'osm' ) {
		$output .= '<script>
		jQuery(function($){
			if (typeof $us !== "undefined" && typeof $us.WMaps === "function") {
				var $wLmap = $(".w-map.provider_osm");
				if ($wLmap.length){
					$us.getScript($us.templateDirectoryUri+"/common/js/vendor/leaflet.js", function(){
						$wLmap.WLmaps();
					});
				}
			}
		});
		</script>';
	} else {
		$output .= '<script>
		jQuery(function($){
			if (typeof $us !== "undefined" && typeof $us.WMaps === "function") {
				var $wMap = $(".w-map.provider_google");
				if ($wMap.length){
					$us.getScript($us.templateDirectoryUri+"/common/js/vendor/gmaps.js", function(){
						$wMap.wMaps();
					});
				}
			}
		});
		</script>';
	}

}

echo $output;
