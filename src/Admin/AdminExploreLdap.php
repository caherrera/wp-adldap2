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
						return '<a href="'. get_edit_user_link( $u->ID ) .'">'. esc_attr( $u->user_nicename ) .'</a>';
					}
				}, $wp_users );

				foreach ( $map as $field ) {
					if ( $field === 'manager' ) {
						if ( $manager = $ldap->getManager( $item ) ) {
							$user[ $field ] = $manager->getAttribute( 'displayname' );
						} else {
							$user[ $field ] = '';
						}
					} else {
						$user[ $field ] = $item->getAttribute( $field );
					}

				}


				$users[] = $user;
			}


			return $users;

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}


}