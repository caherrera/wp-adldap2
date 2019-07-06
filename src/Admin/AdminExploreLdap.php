<?php

namespace WpAdldap2\Admin;

use Exception;
use WpAdldap2\Admin\Views\AdminPageExploreLdap;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Traits\TraitHasFactory;

class AdminExploreLdap {
	use TraitHasFactory;

	public function settingsPage() {

		$p = new AdminPageExploreLdap();
		$p->add( print_r( $this->getList(), true ) );
		echo $p;

	}

	public function getList() {
		try {
			$ldap  = new LdapToWp();
			$users = $ldap->getUsersFromLdap();

//			$users_attrs = array_map( function ( \Adldap\Models\User $user ) {
//				return $user->getAttributes();
//			}, $users );

			return $users_attrs;

		} catch ( Exception $e ) {
		}
	}


}