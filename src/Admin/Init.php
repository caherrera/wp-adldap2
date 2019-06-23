<?php


namespace WpAdldap2\Admin;

use WpAdldap2\Settings;

class Init {
	const ADLDAP2 = 'AD-LDAP2';
	private static $instance;

	private function __construct() {
		self::init();


	}


	public function init() {
		if ( is_admin() ) { // admin actions
			add_action( 'admin_menu', [ $this, 'menu' ] );
			add_action( 'admin_init', [ $this, 'registerSettings' ] );
		}

	}

	static public function factory() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		if ( ! defined( 'ABSPATH' ) ) {
			exit;
		}

		return self::$instance;
	}

	public function registerSettings() {
		register_setting( self::ADLDAP2, self::ADLDAP2 . '_' . Settings::CONFIG_HOSTS );
		register_setting( self::ADLDAP2, self::ADLDAP2 . '_' . Settings::CONFIG_BASEDN );
		register_setting( self::ADLDAP2, self::ADLDAP2 . '_' . Settings::CONFIG_USERNAME );
		register_setting( self::ADLDAP2, self::ADLDAP2 . '_' . Settings::CONFIG_PASSWORD );
		register_setting( self::ADLDAP2, self::ADLDAP2 . '_' . Settings::MAP );

	}

	public function menu() {
		add_menu_page( self::ADLDAP2, self::ADLDAP2, 'edit_users', self::ADLDAP2, [ AdminSettings::class, 'factory' ] );
	}


}