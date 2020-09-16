<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Retrieves and returns the part of current post that can be used as the post's preview.
 *
 * (!) Should be called in WP_Query fetching loop only.
 *
 * @param string $the_content Post content, retrieved with get_the_content() (without 'the_content' filters)
 * @param bool $strip_from_the_content Should the found element be removed from post content not to be duplicated?
 *
 * @return string
 */
function us_get_post_preview( &$the_content, $strip_from_the_content = FALSE ) {
	// Retrieving post format
	$post_format = get_post_format() ? get_post_format() : 'standard';
	$preview_html = '';

	global $us_post_img_ratio;
	if ( ! empty( $us_post_img_ratio ) ) {
		$video_h_style = ' style="padding-bottom:' . $us_post_img_ratio . '%;"';
	} else {
		$video_h_style = '';
	}

	// Retrieving post preview
	if ( $post_format == 'gallery' ) {
		if ( preg_match( '~\[us_image_slider.+?\]|\[gallery.+?\]~', $the_content, $matches ) ) {

			// Replacing with a simple image slider
			$gallery = preg_replace( '~(vc_gallery|gallery)~', 'us_image_slider', $matches[0] );

			global $us_post_slider_size;
			if ( ! empty( $us_post_slider_size ) ) {
				if ( preg_match( '~img_size=\"[^"]+"~', $gallery ) ) {
					$gallery = preg_replace( '~img_size=\"[^"]+"~', 'img_size="' . $us_post_slider_size . '"', $gallery );
				} else {
					$gallery = str_replace( '[us_image_slider', '[us_image_slider img_size="' . $us_post_slider_size . '"', $gallery );
				}

			}
			$preview_html = do_shortcode( $gallery );

			if ( $strip_from_the_content ) {
				$the_content = str_replace( $matches[0], '', $the_content );
			}
		} elseif ( preg_match( '~\[us_grid.+post_type="attachment".+?\]~', $the_content, $matches ) ) {
			// Replacing with a simple image slider
			$gallery = preg_replace( '~us_grid.+post_type="attachment".+images="([^"]+)([^]]+)~', 'us_image_slider ids=$1', $matches[0] );

			global $us_post_slider_size;
			if ( ! empty( $us_post_slider_size ) ) {
				$gallery = str_replace( '[us_image_slider', '[us_image_slider img_size="' . $us_post_slider_size . '"', $gallery );
			}
			$preview_html = do_shortcode( $gallery );

			if ( $strip_from_the_content ) {
				$the_content = str_replace( $matches[0], '', $the_content );
			}
		}

	} elseif ( $post_format == 'video' ) {
		$post_content = preg_replace( '~^\s*(https?://[^\s"]+)\s*$~im', "[embed]$1[/embed]", $the_content );

		if ( preg_match( '~\[embed.+?\]|\[vc_video.+?\]~', $post_content, $matches ) ) {

			global $wp_embed;
			$video = $matches[0];
			$preview_html = do_shortcode( $wp_embed->run_shortcode( $video ) );
			if ( strpos( $preview_html, 'w-video' ) === FALSE ) {
				$preview_html = '<div class="w-video"><div class="w-video-h"' . $video_h_style . '>' . $preview_html . '</div></div>';
			}
			$post_content = str_replace( $matches[0], "", $post_content );
		}
		if ( ! empty( $preview_html ) AND $strip_from_the_content ) {
			$the_content = $post_content;
		}

	} elseif ( $post_format == 'audio' ) {
		$post_content = preg_replace( '~^\s*(https?://[^\s"]+)\s*$~im', "[embed]$1[/embed]", $the_content );

		if ( preg_match( '~\[audio.+?\]\[\/audio\]~', $post_content, $matches ) ) {
			$audio = $matches[0];
			$preview_html = do_shortcode( $audio );

			$post_content = str_replace( $matches[0], "", $post_content );
		} elseif ( preg_match( '~\[embed.+?\]~', $post_content, $matches ) ) {

			global $wp_embed;
			$video = $matches[0];
			$preview_html = do_shortcode( $wp_embed->run_shortcode( $video ) );
			if ( strpos( $preview_html, 'w-video' ) === FALSE ) {
				$preview_html = '<div class="w-video"><div class="w-video-h"' . $video_h_style . '>' . $preview_html . '</div></div>';
			}
			$post_content = str_replace( $matches[0], "", $post_content );
		}
		if ( ! empty( $preview_html ) AND $strip_from_the_content ) {
			$the_content = $post_content;
		}
	}

	$preview_html = apply_filters( 'us_get_post_preview', $preview_html, get_the_ID() );

	return $preview_html;
}

/**
 * Get URL for link post format
 *
 * @param $the_content
 * @param bool|FALSE $strip_from_the_content
 */
function us_get_post_format_link_url( $url, $post ) {

	$post_content = '';
	if ( $post instanceof WP_Post ) {
		if ( get_post_format( $post->ID ) != 'link' ) {
			return $url;
		}
		$post_content = $post->post_content;
	}

	$link = '';

	if ( preg_match( '$(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i', $post_content, $matches ) ) {
		$link = $matches[0];
	}

	if ( $link != '' ) {
		//$post->post_content = str_replace( $link, "", $post->post_content );
		return $link;
	}

	return $url;
}

add_filter( 'post_link', 'us_get_post_format_link_url', 10, 3 );

/**
 * Get information about previous and next post or page (should be used in singular element context)
 *
 * @return array
 */
function us_get_post_prevnext( $invert = FALSE, $in_same_term = FALSE, $taxonomy = 'category' ) {
	$prev = $next = array();

	// Exclude posts with "Link" format
	if ( is_singular( 'post' ) ) {
		global $us_post_prevnext_exclude_ids;
		if ( $us_post_prevnext_exclude_ids === NULL ) {
			global $wpdb;
			$wpdb_query = 'SELECT `object_id` FROM `' . $wpdb->terms . '`, `' . $wpdb->term_relationships . '` ';
			$wpdb_query .= 'WHERE (`slug`=\'post-format-link\' AND `term_id`=`term_taxonomy_id`)';
			$us_post_prevnext_exclude_ids = apply_filters( 'us_get_post_prevnext_exclude_ids', $wpdb->get_col( $wpdb_query ) );
			if ( ! empty( $us_post_prevnext_exclude_ids ) ) {
				add_filter( 'get_next_post_where', 'us_exclude_post_format_link_from_prevnext' );
				add_filter( 'get_previous_post_where', 'us_exclude_post_format_link_from_prevnext' );
			}
		}
	}

	$next_post = get_next_post( $in_same_term, '', $taxonomy );
	$prev_post = get_previous_post( $in_same_term, '', $taxonomy );

	if ( ! empty( $prev_post ) ) {
		$prev = array(
			'id' => $prev_post->ID,
			'link' => get_permalink( $prev_post->ID ),
			'title' => get_the_title( $prev_post->ID ),
		);
	}
	if ( ! empty( $next_post ) ) {
		$next = array(
			'id' => $next_post->ID,
			'link' => get_permalink( $next_post->ID ),
			'title' => get_the_title( $next_post->ID ),
		);
	}

	return ( $invert ) ? array( 'next' => $next, 'prev' => $prev ) : array( 'prev' => $prev, 'next' => $next );
}

function us_exclude_post_format_link_from_prevnext( $where ) {
	global $us_post_prevnext_exclude_ids;
	if ( ! empty( $us_post_prevnext_exclude_ids ) AND is_array( $us_post_prevnext_exclude_ids ) ) {
		$where .= ' AND p.ID NOT IN (' . implode( ',', $us_post_prevnext_exclude_ids ) . ')';
	}

	return $where;
}

// Display specific page when Maintenance Mode is enabled in Theme Options
add_action( 'init', 'us_maintenance_mode' );
function us_maintenance_mode() {
	if ( is_user_logged_in() ) {
		add_action( 'admin_bar_menu', 'us_maintenance_admin_bar_menu', 1000 );

		return FALSE;
	}
	if ( us_get_option( 'maintenance_mode' ) AND us_get_option( 'maintenance_page' ) ) {
		$maintenance_page = get_post( us_get_option( 'maintenance_page' ) );
		if ( $maintenance_page ) {
			if ( function_exists( 'bp_is_active' ) ) {
				add_action( 'template_redirect', 'us_display_maintenance_page', 9 );
			} else {
				add_action( 'template_redirect', 'us_display_maintenance_page', 11 );
			}
		}
	}
}

// Show indication in admin bar when Maintenance Mode is enabled
function us_maintenance_admin_bar_menu( $wp_admin_bar ) {
	if ( us_get_option( 'maintenance_mode' ) AND us_get_option( 'maintenance_page' ) ) {
		$maintenance_page = get_post( us_get_option( 'maintenance_page' ) );
		if ( $maintenance_page ) {

			$wp_admin_bar->add_node(
				array(
					'id' => 'us-maintenance-notice',
					'href' => admin_url() . 'admin.php?page=us-theme-options',
					'title' => __( 'Maintenance Mode', 'us' ),
					'meta' => array(
						'class' => 'us-maintenance',
						'html' => '<style>.us-maintenance a{font-weight:600!important;color:#f90!important;}</style>',
					),
				)
			);
		}
	}
}

// Show specified page when Maintenance Mode is enabled
function us_display_maintenance_page() {
	$maintenance_page = get_post( us_get_option( 'maintenance_page' ) );

	if ( $maintenance_page ) {
		if ( class_exists( 'SitePress' ) ) {
			$maintenance_page = get_post( apply_filters( 'wpml_object_id', $maintenance_page->ID, 'page', TRUE ) );
		}
		us_open_wp_query_context();
		global $wp_query;
		$wp_query = new WP_Query(
			array(
				'p' => $maintenance_page->ID,
				'post_type' => 'page',
			)
		);
		the_post();

		if ( us_get_option( 'maintenance_503', 1 ) == 1 ) {
			header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
			header( 'Status: 503 Service Temporarily Unavailable' );
			header( 'Retry-After: 86400' ); // retry in a day
		}

		$us_layout = US_Layout::instance();
		$us_layout->header_show = 'never';

		get_header();
		?>
		<main id="page-content" class="l-main"<?= ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
			<?php
			do_action( 'us_before_page' );
			echo apply_filters( 'the_content', $maintenance_page->post_content );
			do_action( 'us_after_page' );
			?>
		</main>
		<?php
		global $us_hide_footer;
		$us_hide_footer = TRUE;

		get_footer();
		us_close_wp_query_context();
		exit();
	}
}

if ( ! function_exists( 'us_update_postmeta_for_custom_css' ) ) {
	/**
	 * Add a record of all custom styles to the post metadata
	 *
	 * @param WP_Post $post
	 * @return NULL|array|string
	 */
	function us_update_postmeta_for_custom_css( $post ) {
		$us_jsoncss_data = NULL; // Default value, record checked but no data
		if ( $post instanceof WP_Post AND ! empty( $post->post_content ) ) {
			if ( preg_match_all( '/\s?css="(.*?)"/i', $post->post_content, $matches ) ) {
				$us_jsoncss_data = us_arr_path( $matches, '1', array() );
			}
			update_post_meta( $post->ID, '_us_jsoncss_data', $us_jsoncss_data );
		}
		return $us_jsoncss_data;
	}
}

if ( ! function_exists( 'us_save_post' ) ) {
	add_action( 'save_post', 'us_save_post', 10, 2 );
	/**
	 * Update custom style metadata when saving or updating a post
	 *
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @return void
	 */
	function us_save_post( $post_id, $post ) {
		us_update_postmeta_for_custom_css( $post );
	}
}

if ( ! function_exists( 'us_term_custom_fields' ) AND ! function_exists( 'us_save_term_custom_fields' ) ) {
	add_action(
		'init', function () {

		foreach ( us_get_taxonomies() as $tax_slug => $tax_name ) {
			add_action( "{$tax_slug}_edit_form_fields", 'us_term_custom_fields', 9 );
			add_action( "edited_{$tax_slug}", 'us_save_term_custom_fields', 10, 3 );
		}
		/**
		 * Add custom fields to terms of taxonomies on the "Edit" admin screen
		 *
		 * @param object $term Term object
		 * @return void
		 */
		function us_term_custom_fields( $term ) {

			$misc = us_config( 'elements_misc' );

			$options = array( '__defaults__' => sprintf( '&ndash; %s &ndash;', __( 'As in Theme Options', 'us' ) ) );
			$options = $options + us_get_posts_titles_for( 'us_content_template' );

			// Set default value for "Pages Content template"
			if ( ! $pages_content_id = get_term_meta( $term->term_id, 'pages_content_id', TRUE ) ) {
				$pages_content_id = '__defaults__';
			}

			// Output "Arhive Content template" setting, only if the taxonomy is available for frontend visitors
			if ( $tax = get_taxonomy( $term->taxonomy ) AND $tax->publicly_queryable ) {

				// Set default value for "Arhive Content template"
				if ( ! $archive_content_id = get_term_meta( $term->term_id, 'archive_content_id', TRUE ) ) {
					$archive_content_id = '__defaults__';
				}

			?>
			<!-- Begin Arhive Content template -->
			<tr class="form-field term-display-type-wrap">
				<th scope="row" valign="top">
					<label for="archive_content_id">
						<?= strip_tags( __( 'Archive Content template', 'us' ) ) ?>
					</label>
				</th>
				<td>
					<select id="archive_content_id" name="archive_content_id" class="postform">
						<?php foreach( $options as $value => $name ): ?>
							<option value="<?= esc_attr( $value ) ?>" <?php selected( $value, $archive_content_id ) ?>>
								<?php esc_html_e( $name ) ?>
							</option>
						<?php endforeach ?>
					</select>
					<p class="description">
						<?= sprintf( __( 'Will apply to the "%s" archive page.', 'us' ), $term->name ) ?>
					</p>
				</td>
			</tr>
			<!-- End Arhive Content template -->
			<?php } ?>
			<!-- Begin Pages Content template -->
			<tr class="form-field term-display-type-wrap">
				<th scope="row" valign="top">
					<label for="pages_content_id">
						<?= strip_tags( __( 'Pages Content template', 'us' ) ) ?>
					</label>
				</th>
				<td>
					<select id="pages_content_id" name="pages_content_id" class="postform">
						<?php foreach ( $options as $value => $name ): ?>
							<option value="<?= esc_attr( $value ) ?>" <?php selected( $value, $pages_content_id ) ?>>
								<?php esc_html_e( $name ) ?>
							</option>
						<?php endforeach ?>
					</select>
					<p class="description">
						<?= sprintf( __( 'Will apply to all pages with the "%s" taxonomy.', 'us' ), $term->name ) ?>
						<br>
						<?= $misc['content_description']; ?>
					</p>
				</td>
			</tr>
			<!-- Begin Pages Content template -->
			<?php
		}

		/**
		 * Save terms custom fields
		 *
		 * @param mixed $term_id Term ID being saved.
		 */
		function us_save_term_custom_fields( $term_id ) {
			foreach ( array( 'pages_content_id', 'archive_content_id' ) as $meta_key ) {
				if ( isset( $_POST[ $meta_key ] ) ) {
					update_term_meta( $term_id, $meta_key, esc_attr( $_POST[ $meta_key ] ) );
				}
			}
		}
	}
	);
}
