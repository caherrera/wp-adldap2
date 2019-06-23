<?php

namespace WpAdldap2\Migrators;

use Adldap\Connections\ProviderInterface;
use WP_User_Query;
use WpAdldap2\Settings;
use WpAdldap2\WpAdldap2;

class LdapToWp {

	private $provider;
	private $ad;
	private $map;
	private $conditions;

	public function __construct() {
		$this
			->setAd( $ad = new WpAdldap2() )
			->setProvider( $ad->connect() )
			->setMap( Settings::getMap() )
			->setConditions( Settings::getBasedn() );

	}

	/**
	 * @return mixed
	 */
	public function getMap() {
		return $this->map;
	}

	/**
	 * @param mixed $map
	 *
	 * @return LdapToWp
	 */
	public function setMap( $map ) {
		$this->map = $map;

		return $this;
	}

	public function sync( $what = 'all' ) {
		$usersLdap = $this->getUsersFromLdap();
		$usersWp   = $this->getUsersFromWp();

		if ( $what == 'all' or $what = 'new' ) {
			$found = $this->searchNewUsers();
			$this->addNewUsers( $found );
		}

	}

	public function addNewUsers() {

	}

	public function getUsersFromLdap() {
		$query = $this->getProvider()->search();
		foreach ( $this->getConditions() as $condition ) {
			$condition = explode( '=', $condition );
			$query     = $query->where( $condition[0], '=', $condition[1] );
		}
		$results = $query->get();

		return $results;
	}

	/**
	 * @return ProviderInterface
	 */
	public function getProvider(): ProviderInterface {
		return $this->provider;
	}

	/**
	 * @param ProviderInterface $provider
	 *
	 * @return LdapToWp
	 */
	public function setProvider( ProviderInterface $provider ): LdapToWp {
		$this->provider = $provider;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getConditions() {
		return $this->conditions;
	}

	/**
	 * @param mixed $conditions
	 *
	 * @return LdapToWp
	 */
	public function setConditions( $conditions ) {
		$this->conditions = $conditions;

		return $this;
	}

	public function getUsersFromWp() {
		$users       = new WP_User_Query();
		$users_found = $users->get_results();

		return $users_found;
	}

	public function searchNewUsers( $source, $destination, $keySource, $keyDestination ) {
		$founds = array_filter( $source, function ( $source ) use ( $keyDestination, $keySource, $destination ) {
			return $source[ $keySource ] == $destination[ $keyDestination ];
		} );

		return $founds;
	}

	/**
	 * @return mixed
	 */
	public function getAd() {
		return $this->ad;
	}

	/**
	 * @param mixed $ad
	 *
	 * @return LdapToWp
	 */
	public function setAd( $ad ) {
		$this->ad = $ad;

		return $this;
	}

}