<?php

/**
 * WP Cli commands for Beaver Builder.
 */
class FLbuilder_WPCLI_Command extends WP_CLI_Command {

	/**
	 * Deletes preview, draft and live CSS/JS asset cache for all posts.
	 *
	 * ## OPTIONS
	 *
	 * [--network]
	 * Clears the page builder cache for all sites on a network.
	 *
	 * [--all]
	 * Clears plugin and bb-theme cache.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver clearcache
	 * 		- Clears the page builder cache for all the posts on the site.
	 * 2. wp beaver clearcache --network
	 * 		- Clears the page builder cache for all the posts on a network.
	*/
	public function clearcache( $args, $assoc_args ) {

		$network = false;
		$all     = false;

		if ( isset( $assoc_args['network'] ) && true == $assoc_args['network'] && is_multisite() ) {
			$network = true;
		}

		if ( isset( $assoc_args['all'] ) ) {

			// make sure theme functions are loaded.
			if ( class_exists( 'FLCustomizer' ) ) {
				$all = true;
			} else {
				WP_CLI::error( __( '--all switch used but bb-theme is not active. If using multisite bb-theme must be active on the root site.', 'fl-builder' ) );
			}
		}

		if ( class_exists( 'FLBuilderModel' ) ) {

			if ( true == $network ) {

				if ( function_exists( 'get_sites' ) ) {
					$blogs = get_sites();
				} else {
					$blogs = wp_get_sites();
				}

				foreach ( $blogs as $keys => $blog ) {

					// Cast $blog as an array instead of WP_Site object
					if ( is_object( $blog ) ) {
						$blog = (array) $blog;
					}

					$blog_id = $blog['blog_id'];
					switch_to_blog( $blog_id );
					FLBuilderModel::delete_asset_cache_for_all_posts();
					WP_CLI::success( sprintf( _x( 'Cleared the page builder cache for blog %s', 'current blog name', 'fl-builder' ), get_option( 'home' ) ) );
					if ( $all ) {
						FLCustomizer::refresh_css();
						WP_CLI::success( sprintf( _x( 'Rebuilt the theme cache for blog %s', 'current blog name', 'fl-builder' ), get_option( 'home' ) ) );
					}
					restore_current_blog();
				}
			} else {
				FLBuilderModel::delete_asset_cache_for_all_posts();
				WP_CLI::success( __( 'Cleared the page builder cache', 'fl-builder' ) );
				if ( $all ) {
					FLCustomizer::refresh_css();
					WP_CLI::success( __( 'Rebuilt the theme cache', 'fl-builder' ) );
				}
			}
			do_action( 'fl_builder_cache_cleared' );
		}
	}


	/**
	 * Activate domain using Beaver Builder license key.
	 *
	 * ## OPTIONS
	 *
	 * [--deactivate]
	 * Deactivate this domain and remove license.
	 *
	 * [--license]
	 * License key to use.
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp beaver register --license=01234567890
	 * 		- Register this domain using license 01234567890
	 * 2. wp beaver register --deactivate
	 * 		- Removes domain from domain manager and clears saved license info.
	 * 3. wp beaver register
	 *    - If license is defined in wp-config.php using FL_LICENSE_KEY global.
	*/
	public function register( $args, $assoc_args ) {

		$license = '';

		if ( isset( $assoc_args['deactivate'] ) ) {
			FLUpdater::save_subscription_license( '' );
			WP_CLI::success( 'deactivated' );
			return false;
		}

		if ( defined( 'FL_LICENSE_KEY' ) ) {
			$license = FL_LICENSE_KEY;
			WP_CLI::log( 'Found license using FL_LICENSE_KEY global.' );
		}
		if ( isset( $assoc_args['license'] ) && '' != $assoc_args['license'] ) {
			$license = $assoc_args['license'];
		}

		if ( ! $license ) {
			WP_CLI::error( 'No license info found.' );
		}

		WP_CLI::log( sprintf( 'Using license [ %s ] to register %s', $license, network_home_url() ) );

		$response = FLUpdater::save_subscription_license( $license );

		if ( is_object( $response ) && isset( $response->error ) ) {
			WP_CLI::error( $response->error );
		} else {
			WP_CLI::success( $response->success );
		}
	}
}

WP_CLI::add_command( 'beaver', 'FLbuilder_WPCLI_Command' );
