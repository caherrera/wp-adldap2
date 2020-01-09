<?php

namespace WpAdldap2;

use WP_CLI;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Traits\TraitHasFactory;

class Command {
	use TraitHasFactory;

	protected $assoc_args = [];

	function register() {
		WP_CLI::add_command( strtolower( WPADLDAP2 ) . ' sync', [ $this, 'run' ], [] );
	}

	function printUserAfterProcess( $wpUser ) {
		WP_CLI::print_value( $wpUser, $this->assoc_args );

	}

	function run( $args, $assoc_args ) {

		$this->assoc_args = $assoc_args;

		$migrate = new LdapToWp();

		$ID = $args[0] ?? null;
		if ( $ID ) {
			if ( is_numeric( $ID ) ) {
				if ( $user = get_userdata( $ID ) ) {
					$mail = $user->user_login;
				} else {
					WP_CLI::error( "ID $ID does not exist" );
				}
			} else {
				$mail = $ID;
				if ( $user = get_user_by_email( $mail ) ) {
					$ID = $user->ID;
				} else {
					WP_CLI::line( "Mail $mail does not exist on WP" );
					$ID = null;
				}

			}
		} else {
			$mail = null;
		}
		add_action( 'wp_adldap2_after_sync_ldap_user', [ $this, 'printUserAfterProcess' ], 10, 1 );
		add_action( 'wp_adldap_after_disable_wp_user', [ $this, 'printUserAfterProcess' ], 10, 1 );

		$filterWP = $mail ? [ 'include' => $ID ] : [];


		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'insert', true ) ) {
			$migrate->sync( $mail ? [ [ 'field' => 'mail', 'operator' => '=', 'value' => $mail ] ] : [], $filterWP );
		}
		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'revoke', true ) ) {
			$migrate->deleteWpUsers( $filterWP );
		}

	}
}