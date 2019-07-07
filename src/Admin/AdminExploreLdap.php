<?php

namespace WpAdldap2\Admin;

use Adldap\Models\User;
use Exception;
use WpAdldap2\Admin\Views\AdminPageExploreLdap;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Settings;
use WpAdldap2\Traits\TraitHasFactory;

class AdminExploreLdap {
	use TraitHasFactory;

	public function settingsPage() {

		$p   = new AdminPageExploreLdap();
		$map = array_filter( Settings::getMap() );

		$thead = $p->thead( [
			$p->tr( array_map( function ( $ldap ) use ( $p ) {
				return $p->th( $ldap );
			}, $map ) ),
			$p->tr( array_map( function ( $ldap ) use ( $p ) {
				return $p->th( $ldap );
			}, array_keys( $map ) ) ),
		] );
		$tbody = array_map( function ( User $user ) use ( $p, $map ) {
			$tr = array_map( function ( $attr ) use ( $p, $user ) {
				return $p->td( $user->getAttribute( $attr ) );
			}, $map );

			return $p->tr( $tr );
		}, $this->getList() );
		$tbody = $p->tbody( $tbody );

		$p->add( $p->table( [
			$thead,
			$tbody
		] ) );
		echo $p;

	}

	public function getList() {
		try {
			$ldap  = new LdapToWp();
			$users = [];
			foreach ( $ldap->getUsersFromLdap() as $item ) {
				$users[] = $item;
			}


			return $users;

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}


}