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
	const MAP = 'map';


	public static function getHosts() {
		return get_option( static::getConfigNameOfHosts() );
	}

	public static function getConfigNameOfHosts() {
		return WPADLDAP2 . '_' . Settings::CONFIG_HOSTS;
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
