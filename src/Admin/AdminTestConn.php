<?php

namespace WpAdldap2\Admin;

use Exception;
use WpAdldap2\Admin\Views\AdminPageTestConn;
use WpAdldap2\Settings;
use WpAdldap2\Traits\TraitHasFactory;
use WpAdldap2\WpAdldap2;

class AdminTestConn {
	use TraitHasFactory;

	public function settingsPage() {

		$page    = new AdminPageTestConn();
		$results = $this->testResults();
		$rows    = [];
		foreach ( $results as $v ) {
			$icon   = $v['ok'] == true ? '<span class="dashicons dashicons-yes" style="color:green"></span>' : '<span class="dashicons dashicons-dismiss" style="color: red"></span>';
			$rows[] = $page->row( [ $page->th( $v['title'], [ 'style' => 'width:65%;padding: 10px 10px 0 0' ] ), $page->td( $icon . $v['result'],[ 'style' => 'padding: 10px 10px 0 0' ] ) ] );
		}
		$page->add( $page->table( $rows ) );
		echo $page;

	}

	public function testResults() {
		$results = [];

		$results[] = [ 'title' => 'Has hosts      ', 'result' => ( $ok = ! empty( Settings::getHosts() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has base_dn    ', 'result' => ( $ok = ! empty( Settings::getBasedn() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has username   ', 'result' => ( $ok = ! empty( Settings::getUsername() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has password   ', 'result' => ( $ok = ! empty( Settings::getPassword() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has port       ', 'result' => ( $ok = ! empty( Settings::getPort() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has map declared between ldap fields and wordpress fields        ', 'result' => ( $ok = ! empty( Settings::getMap() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
		$results[] = [ 'title' => 'Has declared filter map      ', 'result' => ( $ok = ! empty( Settings::getMatch() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];
//		$results[] = [ 'title' => 'Records Founds', 'result' => ( $ok = ! empty( Settings::getMatch() ) ) ? 'OK' : 'Missing', 'ok' => $ok ];


		foreach ( Settings::getHosts() as $h => $host ) {
			try {
				$assert = "Connection to Host ($h) : $host";
				if ( $host ) {
					$ad        = new WpAdldap2();
					$provider  = $ad->connect();
					$results[] = [ 'title' => $assert, 'result' => 'OK', 'ok' => true ];
				} else {
					$assert    = "Connection to Host ($h)";
					$results[] = [ 'title' => $assert, 'result' => 'Host empty', 'ok' => true ];
				}
			} catch ( Exception $e ) {
				$results[] = [ 'title' => $assert, 'result' => $e->getMessage(), 'ok' => false ];


			}
		}

		return $results;


	}


}