<?php

namespace WpAdldap2\Migrators;

use FullCsv\CsvWriter;
use WpAdldap2\Traits\TraitHasFactory;

class ExportCsv {
	use TraitHasFactory;

	public function export() {
		$users = apply_filters( 'wpadldap2_wp_users_list', [] );
		if ( ! $users ) {
			$ldap  = new LdapToWp();
			$users = $ldap->getUsersFromWp();
		}

		$this->download_send_headers( "data_export_" . date( "Y-m-d" ) . ".csv" );
		$this->array2csv( $users );
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

		$csv = new CsvWriter( $filename = wp_tempnam( 'adldap2_user_export-csv', wp_get_upload_dir()['basedir'].'/' ) );
		$csv->open();
		reset( $array );
		$keys = array_keys( (array) current( $array ) );
		reset( $array );
		$csv->setHeader( $keys );
		foreach ( $array as $row ) {
			$csv->addRow( (array) $row );
		}
		$csv->close();
		echo file_get_contents( $filename );


	}


}