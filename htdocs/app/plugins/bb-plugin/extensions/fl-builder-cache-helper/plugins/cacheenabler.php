<?php
namespace FLCacheClear;
class Cacheenabler {

	var $name = 'Cache Enabler';
	var $url  = 'https://wordpress.org/plugins/cache-enabler/';

	static function run() {
		if ( class_exists( '\Cache_Enabler' ) ) {
			\Cache_Enabler::clear_total_cache();
		}
	}
}
