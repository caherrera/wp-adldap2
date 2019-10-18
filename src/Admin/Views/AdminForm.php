<?php

namespace WpAdldap2\Admin\Views;

use WpAdldap2\Admin\Helpers\HtmlForm;
use WpAdldap2\Settings;
use WpAdldap2\UserProfile;

class AdminForm extends HtmlForm {

	protected $_html = [];

	public function __toString() {

		return $this->form( parent::__toString() ); // TODO: Change the autogenerated stub
	}


	public function printLdapSettings() {
		$hosts = (array) Settings::getHosts();

		return $this->table( [
			$this->inputSetting( Settings::getConfigNameOfHosts() . '[0]', 'Host 1', isset( $hosts[0] ) ? $hosts[0] : '', '' ),
			$this->inputSetting( Settings::getConfigNameOfHosts() . '[1]', 'Host 2', isset( $hosts[1] ) ? $hosts[1] : '', '' ),
			$this->inputSetting( Settings::getConfigNameOfHosts() . '[2]', 'Host 3', isset( $hosts[2] ) ? $hosts[2] : '', '' ),
			$this->inputSetting( Settings::getConfigNameOfHosts() . '[3]', 'Host 4', isset( $hosts[3] ) ? $hosts[3] : '', '' ),

			$this->inputSetting( Settings::getConfigNameOfPort(), 'Port', Settings::getPort(), '' ),
			$this->inputSetting( Settings::getConfigNameOfUsername(), 'Username', Settings::getUsername(), '' ),
			$this->inputSetting( Settings::getConfigNameOfPassword(), 'Password', Settings::getPassword(), '' ),
		] );
	}

	public function printFieldsToSync() {
		//username	email	nicename	nickname	Name	display_name	first_name	last_name	Cargo	Sucursal	Ubicación	Orden	Dependencia	Dependencia 2	Aniversario en Renta Nacional	Cumpleaños	Anexo	Asistente
		$rows = array_map( function ( $f ) {
			$name = $f->name;

			return $this->inputSetting( Settings::getConfigNameOfMap() . "[$name]", $name, Settings::getADField( $name ), '' );

		}, ( new UserProfile )->getUserFields() );

		return $this->table( [
			$this->thead( $this->tr( [ $this->th( 'Wordpress' ), $this->th( 'Ldap' ) ] ) ),
			$this->tbody( $rows )
		] );
	}

	public function printHierarchy() {
		$hierarchy = Settings::getHierarchy();

		return $this->table( [
			$this->inputSetting( Settings::getConfigNameOfHierarchy(), 'Hierarchy', $hierarchy ?: '', '' )
		] );
	}

	public function printFieldsToMatch() {
		$match   = (array) Settings::getMatch();
		$filters = (array) Settings::getFilters();

		return $this->table( [
			$this->inputMultiples( Settings::getConfigNameOfMatch(), "Match", $match, [ 'Ldap' => '', 'wp' => '' ] ),
			$this->inputMultiples( Settings::getConfigNameOfFilters(), "Extra Conditions", $filters, [ 'field' => '', 'operator' => '', 'value' => '' ] )


		] );
	}

	public function printLdapFilters() {
		$basedn = Settings::getBasedn();
		$expire = Settings::getCacheExpire();
		$dns    = (array) Settings::getDn();


		$rows = [
			$this->inputSetting( Settings::getConfigNameOfCacheExpire(), 'Cache Expire time in Seconds', $expire ?: '', '3600' ),
			$this->inputSetting( Settings::getConfigNameOfBasedn(), 'Base dn', $basedn ?: '', '' ),

		];
		for ( $i = 0; $i < 4; $i ++ ) {
			$dn     = $dns[ $i ] ?? '';
			$rows[] = $this->inputSetting( Settings::getConfigNameOfDn() . '[' . $i . ']', 'DN ' . ( $i + 1 ), $dn, '' );

		}


		return $this->table( $rows );
	}


}