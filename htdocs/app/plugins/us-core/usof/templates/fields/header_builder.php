<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: header_builder
 *
 * Advanced header builder.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @var $value array Current value
 */

if ( ! empty( $value ) AND is_string( $value ) AND $value[0] === '{' ) {
	$value = json_decode( $value, TRUE );
}

// Fallback
$value = us_hb_settings_fallback( $value );

$value = us_fix_header_settings( $value );

$output = '<div class="us-bld" data-ajaxurl="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '">';

// States
$output .= '<div class="us-bld-states">';
$output .= '<div class="us-bld-state for_default active">' . us_translate( 'Default' ) . '</div>';
$output .= '<div class="us-bld-state for_tablets">' . __( 'Tablets', 'us' ) . '</div>';
$output .= '<div class="us-bld-state for_mobiles">' . __( 'Mobiles', 'us' ) . '</div>';
$output .= '</div>';

// Workspace
$output .= '<div class="us-bld-workspace for_default">';

// Editor
if ( ! function_exists( 'ushb_get_elms_placeholders' ) ) {
	/**
	 * Prepare HTML for elements list for a certain elements area
	 *
	 * @param array $layout
	 * @param array $data Elements data
	 * @param string $place
	 *
	 * @return string
	 */
	function ushb_get_elms_placeholders( &$layout, &$data, $place ) {
		$output = '';
		if ( ! isset( $layout[ $place ] ) OR ! is_array( $layout[ $place ] ) ) {
			return $output;
		}
		foreach ( $layout[ $place ] as $elm ) {
			if ( substr( $elm, 1, 7 ) == 'wrapper' ) {
				$output .= '<div class="us-bld-editor-wrapper type_' . ( ( $elm[0] == 'h' ) ? 'horizontal' : 'vertical' );
				if ( ! isset( $layout[ $elm ] ) OR empty( $layout[ $elm ] ) ) {
					$output .= ' empty';
				}
				$output .= '" data-id="' . esc_attr( $elm ) . '">';
				$output .= '<div class="us-bld-editor-wrapper-content">';
				$output .= ushb_get_elms_placeholders( $layout, $data, $elm );
				$output .= '</div>';
				$output .= '<div class="us-bld-editor-wrapper-controls">';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_add" title="' . esc_attr( __( 'Add element into wrapper', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_edit" title="' . esc_attr( __( 'Edit wrapper', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_delete" title="' . esc_attr( us_translate( 'Delete' ) ) . '"></a>';
				$output .= '</div>';
				$output .= '</div><!-- .us-bld-editor-wrapper -->';
			} else {

				// Handling standard single element
				$type = strtok( $elm, ':' );
				$values = isset( $data[ $elm ] ) ? $data[ $elm ] : array();
				$elm_icon = us_config( 'elements/' . $type . '.icon', $type );

				$output .= '<div class="us-bld-editor-elm type_' . $type . '" data-id="' . esc_attr( $elm ) . '">';
				$output .= '<div class="us-bld-editor-elm-content">';
				if ( $type == 'text' AND isset( $values['text'] ) AND ( ! empty( $values['text'] ) OR ! empty( $values['icon'] ) ) ) {
					if ( ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					$output .= strip_tags( $values['text'] );
				} elseif ( $type == 'image' ) {
					if ( ! empty( $values['img'] ) ) {
						$upload_image = usof_get_image_src( $values['img'] );
						$output .= '<img src="' . esc_attr( $upload_image[0] ) . '" />';
					} elseif ( ! empty( $elm_icon ) ) {
						$output .= '<i class="' . $elm_icon . '"></i>';
					}
				} elseif ( $type == 'menu' ) {
					if ( ! empty( $elm_icon ) ) {
						$output .= '<i class="' . $elm_icon . '"></i>';
					}
					if ( ! empty( $values['source'] ) ) {
						$nav_menus = us_get_nav_menus();
						if ( isset( $nav_menus[ $values['source'] ] ) ) {
							$output .= $nav_menus[ $values['source'] ];
						} else {
							$output .= $values['source'];
						}
					} else {
						$output .= us_translate( 'Menu' );
					}
				} elseif ( $type == 'additional_menu' ) {
					if ( ! empty( $elm_icon ) ) {
						$output .= '<i class="' . $elm_icon . '"></i>';
					}
					if ( ! empty( $values['source'] ) ) {
						$nav_menus = us_get_nav_menus();
						if ( isset( $nav_menus[ $values['source'] ] ) ) {
							$output .= $nav_menus[ $values['source'] ];
						} else {
							$output .= $values['source'];
						}
					} else {
						$output .= __( 'Simple Menu', 'us' );
					}
				} elseif ( $type == 'search' AND ! empty( $values['text'] ) ) {
					if ( ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					$output .= strip_tags( $values['text'] );
				} elseif ( $type == 'dropdown' AND isset( $values['source'] ) ) {
					if ( ! empty( $values['link_icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['link_icon'] );
					}
					if ( $values['source'] == 'wpml' ) {
						$output .= 'WPML';
					} elseif ( $values['source'] == 'polylang' ) {
						$output .= 'Polylang';
					} else {
						$output .= ( ! empty( $values['link_title'] ) ) ? strip_tags( $values['link_title'] ) : __( 'Dropdown', 'us' );
					}
				} elseif ( $type == 'socials' ) {
					$socialsOutput = '';
					foreach ( $values['items'] as $key => $value ) {
						if ( $value['type'] == 'custom' AND isset( $value['icon'] ) ) {
							$socialsOutput .= us_prepare_icon_tag( $value['icon'] );
						} else {
							$socialsOutput .= '<i class="fab fa-' . $value['type'] . '"></i>';
						}
					}
					$output .= empty( $socialsOutput ) ? __( 'Social Links', 'us' ) : $socialsOutput;
				} elseif ( $type == 'btn' ) {
					if ( ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					if ( ! empty( $values['label'] ) ) {
						$output .= strip_tags( $values['label'] );
					} elseif ( empty( $values['icon'] ) ) {
						$output .= __( 'Button', 'us' );
					}
				} elseif ( $type == 'html' ) {
					$output .= 'HTML';
				} elseif ( $type == 'cart' ) {
					if ( ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					} elseif ( ! empty( $elm_icon ) ) {
						$output .= '<i class="' . $elm_icon . '"></i>';
					}
					$output .= us_translate( 'Cart', 'woocommerce' );
				} else {
					$output .= ucfirst( $type );
				}
				$output .= '</div>';
				$output .= '<div class="us-bld-editor-elm-controls">';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_edit" title="' . esc_attr( __( 'Edit element', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_clone" title="' . esc_attr( __( 'Duplicate', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-bld-editor-control type_delete" title="' . esc_attr( us_translate( 'Delete' ) ) . '"></a>';
				$output .= '</div>';
				$output .= '</div>';
			}
		}

		return $output;
	}
}
$output .= '<div class="us-bld-editor type_';
$output .= ( us_arr_path( $value, 'default.options.orientation', 'hor' ) == 'ver' ) ? 'ver' : 'hor';
$output .= '">';
foreach ( array( 'top', 'middle', 'bottom' ) as $at_y ) {
	$output .= '<div class="us-bld-editor-row at_' . $at_y;
	if ( ( $at_y == 'top' OR $at_y == 'bottom' ) AND ! us_arr_path( $value, 'default.options.' . $at_y . '_show' ) ) {
		$output .= ' disabled';
	}
	$output .= '">';
	$output .= '<div class="us-bld-editor-row-h">';
	foreach ( array( 'left', 'center', 'right' ) as $at_x ) {
		$output .= '<div class="us-bld-editor-cell at_' . $at_x . '">';

		// Output inner widgets
		$output .= ushb_get_elms_placeholders( $value['default']['layout'], $value['data'], $at_y . '_' . $at_x );
		$output .= '<a href="javascript:void(0)" class="us-bld-editor-add" title="' . esc_attr( __( 'Add element', 'us' ) ) . '"></a>';
		$output .= '</div>';
	}
	$output .= '</div>';
	$output .= '</div><!-- .us-bld-editor-row -->';
}

// Outputting hidden elements
$output .= '<div class="us-bld-editor-row for_hidden">';
$output .= '<div class="us-bld-editor-row-desc">' . __( 'Hidden Elements', 'us' ) . '</div>';
$output .= '<div class="us-bld-editor-row-h">';
$output .= ushb_get_elms_placeholders( $value['default']['layout'], $value['data'], 'hidden' );
$output .= '</div>';
$output .= '</div><!-- .us-bld-editor-row.for_hidden -->';
$output .= '</div><!-- .us-bld-editor -->';

// Options
$output .= '<div class="us-bld-options">';
$hb_options_sections = array(
	'global' => __( 'General Header Settings', 'us' ),
	'top' => __( 'Top Area', 'us' ),
	'middle' => __( 'Main Area', 'us' ),
	'bottom' => __( 'Bottom Area', 'us' ),
);

$options_values = us_arr_path( $value, 'default.options', array() );

// Setting starting state to properly handle show_if rules
$options_values['state'] = 'default';
foreach ( $hb_options_sections as $hb_section => $hb_section_title ) {
	$output .= '<div class="us-bld-options-section' . ( ( $hb_section == 'global' ) ? ' active' : '' ) . '" data-id="' . $hb_section . '">';
	$output .= '<div class="us-bld-options-section-title">' . $hb_section_title . '</div>';
	$output .= '<div class="us-bld-options-section-content" style="display: ' . ( ( $hb_section == 'global' ) ? 'block' : 'none' ) . ';">';
	foreach ( us_config( 'header-settings.options.' . $hb_section, array() ) as $field_name => $fld ) {
		if ( ! isset( $fld['type'] ) ) {
			continue;
		}
		$field_html = us_get_template(
			'usof/templates/field', array(
				'name' => $field_name,
				'id' => 'hb_opt_' . $field_name,
				'field' => $fld,
				'values' => $options_values,
			)
		);

		// Changing rows' classes to prevent auto-init of these rows as main fields
		$field_html = preg_replace( '~usof\-form\-(row|wrapper) ~', 'usof-subform-$1 ', $field_html );
		$output .= $field_html;
	}
	$output .= '</div><!-- .us-bld-options-section-content -->';
	$output .= '</div><!-- .us-bld-options-section -->';
}
$output .= ' </div ><!-- .us-bld-options -->';

$output .= '<div class="us-bld-params hidden"';
$output .= us_pass_data_to_js(
	array(
		'navMenus' => us_get_nav_menus(),
		// TODO Default values
	)
);
$output .= '></div>';
$output .= '<div class="us-bld-value hidden"' . us_pass_data_to_js( $value ) . '></div>';

// Elements' default values
$elms_titles = array();
$elms_defaults = array();
foreach ( us_config( 'header-settings.elements', array() ) as $type ) {
	$elm = us_config( 'elements/' . $type );
	$elms_titles[ $type ] = isset( $elm['title'] ) ? $elm['title'] : $type;
	$elms_defaults[ $type ] = us_get_elm_defaults( $type, 'header' );
}
$output .= '<div class="us-bld-defaults hidden"' . us_pass_data_to_js( $elms_defaults ) . '></div>';
$translations = array(
	'template_replace_confirm' => __( 'Selected template will overwrite all your current elements and settings! Are you sure want to apply it?', 'us' ),
	'orientation_change_confirm' => __( 'Are you sure want to change the header orientation? Some of your elements\' positions may be changed', 'us' ),
	'element_delete_confirm' => __( 'Are you sure want to delete the element?', 'us' ),
	'add_element' => __( 'Add element into wrapper', 'us' ),
	'edit_element' => __( 'Edit element', 'us' ),
	'clone_element' => __( 'Duplicate', 'us' ),
	'delete_element' => us_translate( 'Delete' ),
	'edit_wrapper' => __( 'Edit wrapper', 'us' ),
	'delete_wrapper' => us_translate( 'Delete' ),
	'menu' => us_translate( 'Menu' ),
	'additional_menu' => __( 'Simple Menu', 'us' ),
	'dropdown' => __( 'Dropdown', 'us' ),
	'social_links' => __( 'Social Links', 'us' ),
	'button' => __( 'Button', 'us' ),
	'cart' => us_translate( 'Cart', 'woocommerce' ),
);
$output .= '<div class="us-bld-translations hidden"' . us_pass_data_to_js( $translations ) . '></div>';
$output .= '</div>';

// List of elements that can be added
$output .= us_get_template(
	'usof/templates/window_add', array(
	'elements' => us_config( 'header-settings.elements', array() ),
)
);

// Empty editor window for loading the elements afterwards
$output .= us_get_template(
	'usof/templates/window_edit', array(
		'titles' => $elms_titles,
		'body' => '',
	)
);

// Export & Import
$output .= us_get_template(
	'usof/templates/window_export_import', array(
	'title' => __( 'Header Export / Import', 'us' ),
	'text' => __( 'You can export the saved Header by copying the text inside this field. To import another Header replace the text in this field and click "Import Header" button.', 'us' ),
	'save_text' => __( 'Import Header', 'us' ),
)
);

// Empty header templates window for loading the templates afterwards
$output .= us_get_template(
	'usof/templates/window_header_templates', array(
		'body' => '',
	)
);

echo $output;
