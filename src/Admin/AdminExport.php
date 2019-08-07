<?php

namespace WpAdldap2\Admin;

use Exception;
use WpAdldap2\Admin\Views\AdminPageExploreLdap;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Settings;
use WpAdldap2\Traits\TraitHasFactory;

class AdminExploreLdap {
	use TraitHasFactory;

	public function settingsPage() {

		$p = new AdminPageExploreLdap();
		$p->setList( $this->getList() );

		echo $p;


	}

	public function getList() {
		try {
			$ldap     = new LdapToWp();
			$users    = $ldap->syncListUsers();


			return $users;

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}


}