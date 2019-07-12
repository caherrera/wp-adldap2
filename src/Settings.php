<?php

namespace WpAdldap2;

/**
 * Class Settings
 *
 * @package WpAdldap2
 */
class Settings {
	const CONFIG_HOSTS = 'hosts';
	const CONFIG_BASEDN = 'base_dn';
	const CONFIG_USERNAME = 'username';
	const CONFIG_PASSWORD = 'password';
	const CONFIG_PORT = 'port';
	const MAP = 'map';
	const MATCH = 'match';
	const FILTERS = 'filters';
	const DN = 'dn';
	const CACHE_EXPIRE = 'cache_expire';


	public static function getHosts() {
		return get_option( static::getConfigNameOfHosts() );
	}

	public static function getConfigNameOfHosts() {
		return WPADLDAP2 . '_' . Settings::CONFIG_HOSTS;
	}


	public static function getFilters() {
		return get_option( static::getConfigNameOfFilters() );
	}

	public static function getConfigNameOfFilters() {
		return WPADLDAP2 . '_' . Settings::FILTERS;
	}

	public static function getDn() {
		return get_option( static::getConfigNameOfDn() );
	}

	public static function getConfigNameOfDn() {
		return WPADLDAP2 . '_' . Settings::DN;
	}

	public static function getCacheExpire() {
		return get_option( static::getConfigNameOfCacheExpire() );
	}

	public static function getConfigNameOfCacheExpire() {
		return WPADLDAP2 . '_' . Settings::CACHE_EXPIRE;
	}


	public static function getMatch() {
		$match = get_option( static::getConfigNameOfMatch() );

		return array_filter( $match, function ( $m ) {
			return $m['Ldap'] && $m['wp'];
		} );
	}

	public static function getConfigNameOfMatch() {
		return WPADLDAP2 . '_' . Settings::MATCH;
	}


	public static function getBasedn() {
		return get_option( static::getConfigNameOfBasedn() );
	}

	public static function getConfigNameOfBasedn() {
		return WPADLDAP2 . '_' . Settings::CONFIG_BASEDN;
	}

	public static function getUsername() {
		return get_option( static::getConfigNameOfUsername() );
	}

	public static function getConfigNameOfUsername() {
		return WPADLDAP2 . '_' . Settings::CONFIG_USERNAME;
	}

	public static function getPort() {
		return get_option( static::getConfigNameOfPort() );
	}

	public static function getConfigNameOfPort() {
		return WPADLDAP2 . '_' . Settings::CONFIG_PORT;
	}

	public static function getPassword() {
		return get_option( static::getConfigNameOfPassword() );
	}

	public static function getConfigNameOfPassword() {
		return WPADLDAP2 . '_' . Settings::CONFIG_PASSWORD;
	}

	public static function getADField( $field ) {
		$map = static::getMap();

		return isset( $map[ $field ] ) ? $map[ $field ] : null;
	}

	public static function getMap() {
		return get_option( static::getConfigNameOfMap() );
	}

	public static function getConfigNameOfMap() {
		return WPADLDAP2 . '_' . Settings::MAP;
	}


}
