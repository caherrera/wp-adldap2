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
			Settings::CONFIG_BASE_DN  => Settings::getBasedn(),
			Settings::CONFIG_USERNAME => Settings::getUsername(),
			Settings::CONFIG_PASSWORD => Settings::getPassword(),
		];
		parent::__construct( [ 'default' => $config ] );
	}

}