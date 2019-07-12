<?php

namespace WpAdldap2;

class Cache {

	static function set( $users ) {
		wp_cache_set( 'users', $users, WPADLDAP2, Settings::getCacheExpire() );
	}

	static function get() {
		return wp_cache_get( 'users', WPADLDAP2 );
	}

	static function flush() {
		return wp_cache_delete_group( WPADLDAP2 );
	}
}
