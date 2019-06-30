<?php

namespace WpAdldap2;

use Adldap\Adldap;

class WpAdldap2 extends Adldap {

	/**
	 * {@inheritdoc}
	 */
	public function __construct( array $providers = [] ) {
		$config = [
			Settings::CONFIG_HOSTS    => Settings::getHosts(),
			Settings::CONFIG_BASEDN   => (string) Settings::getBasedn(),
			Settings::CONFIG_USERNAME => (string) Settings::getUsername(),
			Settings::CONFIG_PASSWORD => (string) Settings::getPassword(),
			Settings::CONFIG_PORT     => (string) Settings::getPort(),
		];
		parent::__construct( [ 'default' => $config ] );
	}

}