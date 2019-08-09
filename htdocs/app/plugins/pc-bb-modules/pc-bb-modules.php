<?php
/**
 * Plugin Name: Paradowski Modules for Beaver Builder
 * Plugin URI:
 * Description: Custom Beaver Builder modules for use by Paradowski Creative
 * Version: 2.0
 * Author: Paradowski Creative
 * Author URI: http://paradowski.com/
 */
define( 'PC_MODULES_DIR', plugin_dir_path( __FILE__ ) );
define( 'PC_MODULES_URL', plugins_url( '/', __FILE__ ) );

require_once PC_MODULES_DIR . 'classes/class-pc-bb-modules-loader.php';