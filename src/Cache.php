<?php

namespace WpAdldap2;

class Cache {

	static function set( $users, $key = 'users' ) {
		wp_cache_set( $key, $users, WPADLDAP2, Settings::getCacheExpire() );
	}

	static function get( $key = 'users' ) {
		return wp_cache_get( $key, WPADLDAP2 );
	}

	static function flush() {
		return wp_cache_delete_group( WPADLDAP2 );
	}
}
