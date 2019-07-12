<?php

namespace WpAdldap2\Migrators;

use Adldap\Connections\ProviderInterface;
use Adldap\Models\User;
use BP_XProfile_Field;
use BP_XProfile_ProfileData;
use WpAdldap2\Cache;
use WpAdldap2\Settings;
use WpAdldap2\UserProfile;
use WpAdldap2\WpAdldap2;

class LdapToWp {

	private $provider;
	private $ad;
	private $map;
	private $xprofile_fields;
	private $wp_fields;
	private $match;
	private $filters;
	private $dn;

	public function __construct() {
		$up = UserProfile::factory();
		$this
			->setAd( $ad = new WpAdldap2() )
			->setProvider( $ad->connect() )
			->setMap( Settings::getMap() )
			->setXprofileFields( $up->xprofile_fetch_fields() )
			->setWpFields( $up->getUserFields() )
			->setMatch( Settings::getMatch() )
			->setDn( Settings::getDn() )
			->setFilters( Settings::getFilters() );

	}

	public function sync() {
		$usersLdap = $this->getUsersFromLdap();
		$usersWp   = $this->getUsersFromWp();


		$newUsers = $this->searchNewUsers( $usersLdap, $usersWp );
		$this->addNewUsers( $newUsers );
		$this->updateExistingUsers( $usersLdap );


	}

	/**
	 * @return \Adldap\Query\Collection
	 */
	public function getUsersFromLdap() {

		$dn      = array_reverse( array_filter( $this->getDn() ) );
		$filters = ( array_filter( $this->getFilters() ) );


		if ( ! $results = Cache::get() ) {
			$query = $this->getProvider()->search()->users();
			$dn[]  = $query->getDn();
			$dn    = implode( ',', $dn );
			$query = $query->setDn( $dn );

			foreach ( array_filter( $filters ) as $filter ) {
				$filter = wp_parse_args( $filter, [ 'field' => null, 'operator' => null, 'value' => null ] );
				if ( $filter['operator'] ) {
					$query = $query->where( $filter['field'], $filter['operator'], $filter['value'] );
				}
			}

			$results = $query->get();
			Cache::set( $results );
		}


		return $results;
	}

	/**
	 * @return mixed
	 */
	public function getDn() {
		return $this->dn;
	}

	/**
	 * @param mixed $dn
	 *
	 * @return LdapToWp
	 */
	public function setDn( $dn ) {
		$this->dn = $dn;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * @param mixed $filters
	 *
	 * @return LdapToWp
	 */
	public function setFilters( $filters ) {
		$this->filters = $filters;

		return $this;
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


	public function getUsersFromWp() {
		return get_users();
	}

	public function searchNewUsers( &$usersLdap, $usersWp ) {

		$founds     = array_udiff( $usersLdap, $usersWp, [ $this, 'member_compare' ] );
		$usersLdap2 = array_diff( $usersLdap, $founds );
		$usersLdap  = $usersLdap2;

		return $founds;
	}

	/**
	 * @param User[] $list
	 *
	 * @return array
	 */
	public function addNewUsers( $list = [] ) {
		$wp_users = [];
		foreach ( $list as $AdUser ) {
			$wp_users[] = $this->addWPData( $AdUser );

		}

		return $wp_users;

	}

	public function addWPData( User $adUser ) {
		$user = [];
		foreach ( $this->getWpFields() as $field => $adField ) {
			$user[ $field ] = $adUser->{$adField};
		}
		$user['user_pass'] = wp_generate_password( 12, false );
		$user_id           = wp_insert_user( $user );
		$this->updateXprofileData( $user_id, $adUser );

	}

	/**
	 * @return mixed
	 */
	public function getWpFields() {
		return $this->wp_fields;
	}

	/**
	 * @param mixed $wp_fields
	 *
	 * @return LdapToWp
	 */
	public function setWpFields( $wp_fields ) {
		$this->wp_fields = $wp_fields;

		return $this;
	}

	public function updateXprofileData( $user_id, $adUser ) {
		foreach ( $this->getXprofileFields() as $field => $adField ) {
			$field_id = BP_XProfile_Field::get_id_from_name( $field );
			if ( ! $field_id ) {
				wp_die( "Field $field does not exists" );
			}
			$xdata        = New BP_XProfile_ProfileData( $field_id, $user_id );
			$xdata->value = $adUser->{$adField};
			$xdata->save();
		}
	}

	/**
	 * @return mixed
	 */
	public function getXprofileFields() {
		return $this->xprofile_fields;
	}

	/**
	 * @param mixed $xprofile_fields
	 *
	 * @return LdapToWp
	 */
	public function setXprofileFields( $xprofile_fields ) {
		$this->xprofile_fields = $xprofile_fields;

		return $this;
	}

	public function updateExistingUsers( $list = [] ) {
		$wp_users = [];
		foreach ( $list as $AdUser ) {
			$wp_users[] = $this->updateUserData( $AdUser );

		}

		return $wp_users;

	}

	public function updateUserData( $user_id, $adUser ) {
		$userdata['ID'] = $user_id;
		foreach ( $this->getWpFields() as $field => $adField ) {

			$userdata[ $field ] = $adUser->{$adField};

		}

		return wp_update_user( $userdata );
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

	public function member_compare( $ldapUser, $wpUser ) {
		$bool = true;
		foreach ( $this->getMatch() as $keys ) {
			$bool = $bool && $ldapUser[ $keys['Ldap'] ] == $wpUser[ $keys['Wp'] ];
		}

		return $bool;
	}

	/**
	 * @return mixed
	 */
	public function getMatch() {
		return $this->match;
	}

	/**
	 * @param mixed $match
	 *
	 * @return LdapToWp
	 */
	public function setMatch( $match ) {
		$this->match = $match;

		return $this;
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