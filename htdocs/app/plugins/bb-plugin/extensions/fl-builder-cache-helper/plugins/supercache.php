<?php
namespace FLCacheClear;
class Supercache {

	var $name = 'WP Super Cache';
	var $url  = 'https://wordpress.org/plugins/wp-super-cache/';

	static function run() {
		if ( function_exists( '\wp_cache_clear_cache' ) ) {
			\wp_cache_clear_cache();
		}
	}
}
