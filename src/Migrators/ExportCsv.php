<?php

namespace WpAdldap2\Admin;

use Exception;
use WpAdldap2\Migrators\LdapToWp;
use WpAdldap2\Traits\TraitHasFactory;

class AdminExport {
	use TraitHasFactory;

	public function settingsPage() {

		$ldap  = new LdapToWp();
		$users = $ldap->getUsersFromWp();

		$this->download_send_headers( "data_export_" . date( "Y-m-d" ) . ".csv" );
		echo $this->array2csv( $users );
		die();


	}

	function download_send_headers( $filename ) {
		// disable caching
		$now = gmdate( "D, d M Y H:i:s" );
		header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
		header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
		header( "Last-Modified: {$now} GMT" );

		// force download
		header( "Content-Type: application/force-download" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Type: application/download" );

		// disposition / encoding on response body
		header( "Content-Disposition: attachment;filename={$filename}" );
		header( "Content-Transfer-Encoding: binary" );
	}

	function array2csv( array &$array ) {
		if ( count( $array ) == 0 ) {
			return null;
		}
		ob_start();
		$df = fopen( "php://output", 'w' );
		fputcsv( $df, array_keys( reset( $array ) ) );
		foreach ( $array as $row ) {
			fputcsv( $df, $row );
		}
		fclose( $df );

		return ob_get_clean();
	}

	public function getList() {
		try {
			$ldap  = new LdapToWp();
			$users = $ldap->syncListUsers();


			return $users;

		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}


}