<?php

namespace WpAdldap2;

use Adldap\Adldap;
use Adldap\Schemas\ActiveDirectory;

class WpAdldap2 extends Adldap {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( array $providers = [] ) {
		parent::__construct();
		$config = [
			Settings::CONFIG_HOSTS    => Settings::getHosts(),
			Settings::CONFIG_BASEDN   => (string) Settings::getBasedn(),
			Settings::CONFIG_USERNAME => (string) Settings::getUsername(),
			Settings::CONFIG_PASSWORD => (string) Settings::getPassword(),
			Settings::CONFIG_PORT     => (string) Settings::getPort(),
		];
		$config = array_merge( [
			// Optional Configuration Options
			'schema'         => ActiveDirectory::class,
			'account_prefix' => '',
			'account_suffix' => '',

			'follow_referrals' => false,
			'use_ssl'          => false,
			'use_tls'          => false,
			'version'          => 3,
			'timeout'          => 5,

			// Custom LDAP Options
			'custom_options'   => [
				// See: http://php.net/ldap_set_option
				LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_HARD
			]
		], $config );

		$this->addProvider( $config );
	}

}