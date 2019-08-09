<?php

/**
 * Misc functions for compatibility with other plugins.
 */

/**
 * Support for tinyPNG.
 *
 * Runs cropped photos stored in cache through tinyPNG.
 */
function fl_builder_tinypng_support( $cropped_path, $editor ) {

	if ( class_exists( 'Tiny_Settings' ) ) {
		try {
			$settings = new Tiny_Settings();
			$settings->xmlrpc_init();
			$compressor = $settings->get_compressor();
			if ( $compressor ) {
				$compressor->compress_file( $cropped_path['path'], false, false );
			}
		} catch ( Exception $e ) {
			//
		}
	}
}
add_action( 'fl_builder_photo_cropped', 'fl_builder_tinypng_support', 10, 2 );

/**
 * Support for WooCommerce Memberships.
 *
 * Makes sure builder content isn't rendered for protected posts.
 */
function fl_builder_wc_memberships_support() {

	if ( function_exists( 'wc_memberships_is_post_content_restricted' ) ) {

		function fl_builder_wc_memberships_maybe_render_content( $do_render, $post_id ) {

			if ( wc_memberships_is_post_content_restricted() ) {

				// check if user has access to restricted content
				if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
					$do_render = false;
				} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
					$do_render = false;
				}
			}

			return $do_render;
		}
		add_filter( 'fl_builder_do_render_content', 'fl_builder_wc_memberships_maybe_render_content', 10, 2 );
	}
}
add_action( 'plugins_loaded', 'fl_builder_wc_memberships_support', 11 );

/**
 * Support for Option Tree.
 *
 * Older versions of Option Tree don't declare the ot_get_media_post_ID
 * function on the frontend which is needed for the media uploader and
 * throws an error if it doesn't exist.
 */
function fl_builder_option_tree_support() {

	if ( ! function_exists( 'ot_get_media_post_ID' ) ) {

		function ot_get_media_post_ID() { // @codingStandardsIgnoreLine

			// Option ID
			$option_id = 'ot_media_post_ID';

			// Get the media post ID
			$post_id = get_option( $option_id, false );

			// Add $post_ID to the DB
			if ( false === $post_id ) {

				global $wpdb;

				// Get the media post ID
				$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE `post_title` = 'Media' AND `post_type` = 'option-tree' AND `post_status` = 'private'" );

				// Add to the DB
				add_option( $option_id, $post_id );
			}

			return $post_id;
		}
	}
}
add_action( 'after_setup_theme', 'fl_builder_option_tree_support' );

/**
 * If FORCE_SSL_ADMIN is enabled but the frontend is not SSL fixes a CORS error when trying to upload a photo.
 * `add_filter( 'fl_admin_ssl_upload_fix', '__return_false' );` will disable.
 *
 * @since 1.10.2
 */
function fl_admin_ssl_upload_fix() {
	if ( defined( 'FORCE_SSL_ADMIN' ) && ! is_ssl() && is_admin() && FLBuilderAJAX::doing_ajax() ) {
		if ( isset( $_POST['action'] ) && 'upload-attachment' === $_POST['action'] && true === apply_filters( 'fl_admin_ssl_upload_fix', true ) ) {
			force_ssl_admin( false );
		}
	}
}
add_action( 'plugins_loaded', 'fl_admin_ssl_upload_fix', 11 );

/**
 * Disable support Buddypress pages since it's causing conflicts with `the_content` filter
 *
 * @param bool $is_editable Wether the post is editable or not
 * @param $post The post to check from
 * @return bool
 */
function fl_builder_bp_pages_support( $is_editable, $post = false ) {

	// Frontend check
	if ( ! is_admin() && class_exists( 'BuddyPress' ) && ! bp_is_blog_page() ) {
		$is_editable = false;
	}

	// Admin rows action link check and applies to page list
	if ( is_admin() && class_exists( 'BuddyPress' ) && $post && 'page' == $post->post_type ) {

		$bp = buddypress();
		if ( $bp->pages ) {
			foreach ( $bp->pages as $page ) {
				if ( $post->ID == $page->id ) {
					$is_editable = false;
					break;
				}
			}
		}
	}

	return $is_editable;
}
add_filter( 'fl_builder_is_post_editable', 'fl_builder_bp_pages_support', 11, 2 );

/**
 * There is an issue with Jetpack Photon and circle cropped photo module
 * returning the wrong image sizes from the bb cache folder.
 * This filter disables photon for circle cropped photo module images.
 */
function fl_photo_photon_exception( $val, $src, $tag ) {

	// Make sure its a bb cached image.
	if ( false !== strpos( $src, 'bb-plugin/cache' ) ) {

		// now make sure its a circle cropped image.
		if ( false !== strpos( basename( $src ), '-circle' ) ) {
			return apply_filters( 'fl_photo_photon_exception', true );
		}
	}
	// return original val
	return $val;
}
add_filter( 'jetpack_photon_skip_image', 'fl_photo_photon_exception', 10, 3 );

/**
 * WordPress pre 4.5 we need to make sure that ui-core|widget|mouse are loaded before sortable.
 */
function fl_before_sortable_enqueue_callback() {

	if ( version_compare( get_bloginfo( 'version' ), '4.5', '<' ) ) {
		wp_deregister_script( 'jquery-ui-widget' );
		wp_deregister_script( 'jquery-ui-mouse' );
		wp_deregister_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-core', site_url( '/wp-includes/js/jquery/ui/core.min.js' ), array( 'jquery' ), '1.8.12' );
		wp_enqueue_script( 'jquery-ui-widget', site_url( '/wp-includes/js/jquery/ui/widget.min.js' ), array( 'jquery' ), '1.8.12' );
		wp_enqueue_script( 'jquery-ui-mouse', site_url( '/wp-includes/js/jquery/ui/mouse.min.js' ), array( 'jquery' ), '1.8.12' );
	}
}
add_action( 'fl_before_sortable_enqueue', 'fl_before_sortable_enqueue_callback' );

/**
 * Try to unserialize data normally.
 * Uses a preg_callback to fix broken data caused by serialized data that has broken offsets.
 *
 * @since 1.10.6
 * @param string $data unserialized string
 * @return array
 */
function fl_maybe_fix_unserialize( $data ) {
	// @codingStandardsIgnoreStart
	$unserialized = @unserialize( $data );
	// @codingStandardsIgnoreEnd
	if ( ! $unserialized ) {
		$unserialized = unserialize( preg_replace_callback( '!s:(\d+):"(.*?)";!', 'fl_maybe_fix_unserialize_callback', $data ) );
	}
	return $unserialized;
}

/**
 * Callback function for fl_maybe_fix_unserialize()
 *
 * @since 1.10.6
 */
function fl_maybe_fix_unserialize_callback( $match ) {
	return ( strlen( $match[2] ) == $match[1] ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
}

/**
 * Filter rendered module content and if safemode is active safely display a message.
 * @since 1.10.7
 */
function fl_builder_render_module_content_filter( $contents, $module ) {
	if ( isset( $_GET['safemode'] ) && FLBuilderModel::is_builder_active() ) {
		return sprintf( '<h3>[%1$s] %2$s %3$s</h3>', __( 'SAFEMODE', 'fl-builder' ), $module->name, __( 'module', 'fl-builder' ) );
	} else {
		return $contents;
	}
}

add_filter( 'fl_builder_render_module_content', 'fl_builder_render_module_content_filter', 10, 2 );

/**
 * Duplicate posts plugin fixes when cloning BB template.
 *
 * @since 1.10.8
 * @param int $meta_id The newly added meta ID
 * @param int $object_id ID of the object metadata is for.
 * @param string $meta_key Metadata key
 * @param string $meta_value Metadata value
 * @return void
 */
function fl_builder_template_meta_add( $meta_id, $object_id, $meta_key, $meta_value ) {
	global $pagenow;

	if ( 'admin.php' != $pagenow ) {
		return;
	}

	if ( ! isset( $_REQUEST['action'] ) || 'duplicate_post_save_as_new_post' != $_REQUEST['action'] ) {
		return;
	}

	$post_type = get_post_type( $object_id );
	if ( 'fl-builder-template' != $post_type || '_fl_builder_template_id' != $meta_key ) {
		return;
	}

	// Generate new template ID;
	$template_id = FLBuilderModel::generate_node_id();

	update_post_meta( $object_id, '_fl_builder_template_id', $template_id );
}
add_action( 'added_post_meta', 'fl_builder_template_meta_add', 10, 4 );

/**
 * Stop bw-minify from optimizing when builder is open.
 * @since 1.10.9
 */
function fl_bwp_minify_is_loadable_filter( $args ) {
	if ( FLBuilderModel::is_builder_active() ) {
		return false;
	}
	return $args;
}
add_filter( 'bwp_minify_is_loadable', 'fl_bwp_minify_is_loadable_filter' );

/**
* Fixes an issue on search archives if one of the results contains same shortcode
* as is currently trying to render.
*
* @since 1.10.9
* @param bool $render Render shortcode.
* @param array $attrs Shortcode attributes.
* @param array $args Passed to FLBuilder::render_query
* @return bool
*/
function fl_builder_insert_layout_render_search( $render, $attrs, $args ) {
	global $post, $wp_query;

	if ( is_search() && is_object( $post ) && is_array( $wp_query->posts ) ) {
		foreach ( $wp_query->posts as $queried_post ) {
			if ( $post->ID === $queried_post->ID ) {
				preg_match( '#(?<=fl_builder_insert_layout).*[id|slug]=[\'"]?([0-9a-z-]+)#', $post->post_content, $matches );
				if ( isset( $matches[1] ) ) {
					return false;
				}
			}
		}
	}
	return $render;
}
add_action( 'fl_builder_insert_layout_render', 'fl_builder_insert_layout_render_search', 10, 3 );

/**
* Fixes ajax issues with Event Espresso plugin when builder is open.
* @since 2.1
*/
function fl_ee_suppress_notices() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'FHEE__EE_Front_Controller__display_errors', '__return_false' );
	}
}
add_action( 'wp', 'fl_ee_suppress_notices' );

/**
 * Stops ee from outputting HTML into our ajax responses.
 * @since 2.1
 */
function fl_ee_before_ajax() {
	add_filter( 'FHEE__EE_Front_Controller__display_errors', '__return_false' );
}
add_action( 'fl_ajax_before_call_action', 'fl_ee_before_ajax' );


/**
* Plugin Enjoy Instagram loads its js and css on all frontend pages breaking the builder.
* @since 2.0.1
*/
add_action( 'template_redirect', 'fix_aggiungi_script_instafeed_owl', 1000 );
function fix_aggiungi_script_instafeed_owl() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_enqueue_scripts', 'aggiungi_script_instafeed_owl' );
	}
}

/**
* Siteground cache captures shutdown and breaks our dynamic js loading.
* @since 2.0.4.2
*/
add_action( 'plugins_loaded', 'fl_fix_sg_cache', 9 );
function fl_fix_sg_cache() {
	if ( isset( $_GET['fl_builder_load_settings_config'] ) ) {
		remove_action( 'plugins_loaded', 'sg_cachepress_start' );
	}
}

/**
 * Remove Activemember360 shortcodes from saved post content to stop them rendering twice.
 * @since 2.0.6
 */
add_filter( 'fl_builder_editor_content', 'fl_activemember_shortcode_fix' );
function fl_activemember_shortcode_fix( $content ) {
	return preg_replace( '#\[mbr.*?\]#', '', $content );
}

/**
 * Remove iMember360 shortcodes from saved post content to stop them rendering twice.
 * @since 2.0.6
 */
add_filter( 'fl_builder_editor_content', 'fl_imember_shortcode_fix' );
function fl_imember_shortcode_fix( $content ) {
	return preg_replace( '#\[i4w.*?\]#', '', $content );
}

/**
 * Fix javascript issue caused by nextgen gallery when adding modules in the builder.
 * @since 2.0.6
 */
add_action( 'plugins_loaded', 'fl_fix_nextgen_gallery' );
function fl_fix_nextgen_gallery() {
	if ( isset( $_GET['fl_builder'] ) || isset( $_POST['fl_builder_data'] ) || FLBuilderAJAX::doing_ajax() ) {
		define( 'NGG_DISABLE_RESOURCE_MANAGER', true );
	}
}

/**
 * Fix Tasty Recipes compatibility issues with the builder.
 * @since 2.0.6
 */
add_action( 'template_redirect', 'fl_fix_tasty_recipes' );
function fl_fix_tasty_recipes() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_enqueue_editor', array( 'Tasty_Recipes\Assets', 'action_wp_enqueue_editor' ) );
		remove_action( 'media_buttons', array( 'Tasty_Recipes\Editor', 'action_media_buttons' ) );
	}
}

/**
 * Dequeue GeneratePress fa5 js when builder is open.
 * @since 2.1
 */
add_action( 'template_redirect', 'fl_fix_generatepress_fa5' );
function fl_fix_generatepress_fa5() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'generate_fontawesome_essentials', '__return_true' );
	}
}

/**
 * Try to render Ninja Forms JS templates when rendering an AJAX layout
 * in case the layout includes one of their shortcodes. This won't do
 * anything if no templates need to be rendered.
 * @since 2.1
 */
add_filter( 'fl_builder_ajax_layout_response', 'fl_render_ninja_forms_js' );
function fl_render_ninja_forms_js( $response ) {
	if ( class_exists( 'NF_Display_Render' ) ) {
		ob_start();
		NF_Display_Render::output_templates();
		$response['html'] .= ob_get_clean();
	}
	return $response;
}

/**
 * Reorder font awesome css.
 * @since 2.1
 */
function fl_builder_fa_fix() {

	global $wp_styles;

	$queue = $wp_styles->queue;

	$fa4 = array_search( 'font-awesome', $queue );
	$fa5 = array_search( 'font-awesome-5', $queue );

	// if fa4 is disabled and both are detected, load fa4 FIRST.
	if ( false !== $fa4 && false !== $fa5 && $fa4 > $fa5 && ! in_array( 'font-awesome', FLBuilderModel::get_enabled_icons() ) ) {
		unset( $wp_styles->queue[ $fa4 ] );
		array_unshift( $wp_styles->queue, 'font-awesome' );
	}
	// If fa4 is detected, add a compatibility layer in the footer.
	// This fixes various theme/themer issues.
	if ( false !== $fa4 ) {
			add_action( 'wp_footer', 'fl_builder_fa_fix_callback' );
	}
}
add_action( 'wp_enqueue_scripts', 'fl_builder_fa_fix', 99999 );

function fl_builder_fa_fix_callback() {
	echo '<style>[class*="fa fa-"]{font-family: FontAwesome !important;}</style>';
}


/**
 * Turn off Hummingbird minification
 * @since 2.1
 */
add_action( 'template_redirect', 'fl_fix_hummingbird' );
function fl_fix_hummingbird() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'wp_hummingbird_is_active_module_minify', '__return_false', 500 );
	}
}

/**
 * Fix Enjoy Instagram feed on website with WordPress Widget and Shortcode issues with the builder.
 * @since 2.0.6
 */
add_action( 'template_redirect', 'fl_fix_enjoy_instagram' );
function fl_fix_enjoy_instagram() {
	if ( FLBuilderModel::is_builder_active() ) {
		remove_action( 'wp_head', 'funzioni_in_head' );
	}
}

/**
 * Fix Event Calendar widget not loading assets when added as a widget module.
 * @since 2.1.5
 */
add_action( 'tribe_events_pro_widget_render', 'fl_tribe_events_pro_widget_render_fix', 10, 3 );
function fl_tribe_events_pro_widget_render_fix( $class, $args, $instance ) {
	if ( isset( $args['widget_id'] ) && false !== strpos( $args['widget_id'], 'fl_builder_widget' ) ) {
		if ( class_exists( 'Tribe__Events__Pro__Mini_Calendar' ) ) {
			if ( method_exists( Tribe__Events__Pro__Mini_Calendar::instance(), 'register_assets' ) ) {
				Tribe__Events__Pro__Mini_Calendar::instance()->register_assets();
			} else {
				if ( class_exists( 'Tribe__Events__Pro__Widgets' ) && method_exists( 'Tribe__Events__Pro__Widgets', 'enqueue_calendar_widget_styles' ) ) {
					Tribe__Events__Pro__Widgets::enqueue_calendar_widget_styles();
				}
			}
		}
	}
}

/**
 * Fix for Enfold theme always loading wp-mediaelement
 * @since 2.1.5
 */
add_filter( 'avf_enqueue_wp_mediaelement', 'fl_builder_not_load_mediaelement', 10, 2 );
function fl_builder_not_load_mediaelement( $condition, $options ) {
	if ( FLBuilderModel::is_builder_active() ) {
		$condition = true;
	}
	return $condition;
}

/**
 * Fix issue with Templator plugin.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_templator' );
function fl_builder_fix_templator() {
	if ( FLBuilderModel::is_builder_active() && class_exists( 'Templator_Import' ) ) {
		remove_action( 'media_buttons', array( Templator_Import::get_instance(), 'import_template_button' ) );
	}
}

/**
 * Fix issue with Prevent Direct Access Gold.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_protector_gold' );
function fl_builder_fix_protector_gold() {
	if ( FLBuilderModel::is_builder_active() && class_exists( 'Prevent_Direct_Access_Gold' ) && ! function_exists( 'get_current_screen' ) ) {
		function get_current_screen() {
			$args         = new StdClass;
			$args->id     = 'Beaver';
			$args->action = 'Builder';
			return $args;
		}
	}
}

/**
 * Fix issue with WPMUDEV Smush It.
 * @since 2.1.6
 */
add_action( 'template_redirect', 'fl_builder_fix_smush_it' );
function fl_builder_fix_smush_it() {
	if ( FLBuilderModel::is_builder_active() ) {
		add_filter( 'wp_smush_enqueue', '__return_false' );
	}
}

/**
 * Whitelist files in bb-theme and bb-theme-builder in PHPCompatibility Checker plugin.
 * @since 2.1.6
 */
add_filter( 'phpcompat_whitelist', 'fl_builder_bbtheme_compat_fix' );
function fl_builder_bbtheme_compat_fix( $folders ) {

	$folders[] = '*/bb-theme/includes/vendor/Less/*';
	$folders[] = '*/bb-theme-builder/includes/post-grid-default-html.php';
	$folders[] = '*/bb-theme-builder/includes/post-grid-default-css.php';
	return $folders;
};
