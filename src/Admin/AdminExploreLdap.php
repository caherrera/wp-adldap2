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

		$p     = new AdminPageExploreLdap();
		$map   = array_filter( Settings::getMap() );
		$list  = $this->getList();
		$first = current( $list );
		$thead = $p->thead( [
			$p->tr( array_map( function ( $ldap ) use ( $p ) {
				return $p->th( $ldap );
			}, array_keys( $first ) ) ),
		] );
		$tbody = array_map( function ( $user ) use ( $p ) {
			$tr = array_map( function ( $attr ) use ( $p ) {
				return $p->td( $attr );
			}, $user );

			return $p->tr( $tr );
		}, $list );
		$tbody = $p->tbody( $tbody );

		$p->add( $p->table( [
			$thead,
			$tbody
		] ) );
		echo $p;

	}

	public function getList() {
		try {
			$ldap     = new LdapToWp();
			$wp_users = $ldap->getUsersFromWp();
			$users    = [];
			$map      = array_filter( Settings::getMap() );
			foreach ( $ldap->getUsersFromLdap() as $item ) {
				$user         = [];
				$user['wpid'] = array_map( function ( $u ) use ( $ldap, $item ) {
					$bool = true;
					foreach ( $ldap->getMatch() as $keys ) {
						$bool = $bool && $item[ $keys['Ldap'] ] && $item[ $keys['Ldap'] ][0] == $u->{$keys['wp']};
					}

					if ( $bool ) {
						return $u->ID;
					}
				}, $wp_users );

				foreach ( $map as $field ) {
					$user[ $field ] = $item->getAttribute( $field );
				}


				$users[] = $user;
			}


			return $users;

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}


}