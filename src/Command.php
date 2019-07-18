<?php

namespace WpAdldap2;

use WP_CLI;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Traits\TraitHasFactory;

class Command {
	use TraitHasFactory;

	function register() {
		WP_CLI::add_command( strtolower(WPADLDAP2) .' sync', [ $this, 'run' ] );
	}

	function run() {

		$migrate = new LdapToWp();
		$migrate->sync();

	}
}