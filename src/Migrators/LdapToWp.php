<?php

namespace WpAdldap2\Migrators;

use Adldap\Connections\ProviderInterface;
use Adldap\Models\User;
use BP_XProfile_Field;
use WpAdldap2\Cache;
use WpAdldap2\Settings;
use WpAdldap2\UserProfile;
use WpAdldap2\WpAdldap2;

class LdapToWp {

	/**
	 * @var \WP_User[]
	 */
	protected $wp_users;
	private $provider;
	private $ad;
	private $map;
	private $xprofile_fields;
	private $xprofile_fields_in_map;
	private $wp_fields;
	private $wp_fields_in_map;
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
			->setWpFields( $up->getUserFields( false ) )
			->setMatch( Settings::getMatch() )
			->setDn( Settings::getDn() )
			->setFilters( Settings::getFilters() )
			->setXprofileFieldsInMap( $this->getFieldsInMap( $this->getXprofileFields() ) )
			->setwpFieldsInMap( $this->getFieldsInMap( $this->getWpFields() ) );

	}

	public function getFieldsInMap( $list ) {
		$fields = [];
		$map    = $this->getMap();
		foreach ( $list as $name => $field ) {
			foreach ( $map as $item => $value ) {
				if ( $name == $item ) {
					$fields[ $name ] = $value;
				}
			}
		}

		return array_filter( $fields );
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

	public function sync() {

		$this->syncListUsers( true, true );


	}

	public function syncListUsers( $updateMatches = false, $insertNews = false ) {
		$users = [];
		$map   = array_filter( $this->getMap() );
		foreach ( $this->getUsersFromLdap() as $item ) {
			$isNew = false;
			$user  = [];
			$wpid  = $this->searchWPUser( $item );
			if ( $insertNews && ! $wpid ) {
				$wpid  = $this->createWpUser( $item );
				$isNew = true;
			}
			$user['wpid'] = $wpid;

			if ( $updateMatches ) {
				if ( ! $isNew ) {
					$this->updateUserData( $wpid, $item );
				}
				$this->updateXprofileData( $wpid, $item );
			}


			foreach ( $map as $field ) {
				if ( $field === 'manager' ) {
					if ( $manager = $this->getManager( $item ) ) {
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

	public function searchWPUser( $item ) {
		foreach ( $this->getUsersFromWp() as $u ) {
			$bool = true;
			foreach ( $this->getMatch() as $keys ) {
				$bool = $bool && $item[ $keys['Ldap'] ] && $item[ $keys['Ldap'] ][0] == $u->{$keys['wp']};
			}

			if ( $bool ) {
				return $u->ID;
			}
		}
	}

	public function getUsersFromWp() {
		if ( ! $wp_users = $this->wp_users ) {
			$wp_users = $this->setWpUsers( get_users() )->wp_users;
		}

		return $wp_users;
	}

	/**
	 * @param \WP_User[] $wp_users
	 *
	 * @return LdapToWp
	 */
	public function setWpUsers( array $wp_users ): LdapToWp {
		$this->wp_users = $wp_users;

		return $this;
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

	public function createWpUser( User $adUser ) {
		$user = [];
		foreach ( $this->getWpFieldsInMap() as $field => $adField ) {
			$user[ $field ] = $this->getAttributeFromAdUser( $adUser, $adField );
		}
		$user['user_pass'] = wp_generate_password( 12, false );
		$user_id           = wp_insert_user( $user );


		return $user_id;

	}

	/**
	 * @return mixed
	 */
	public function getWpFieldsInMap() {
		return $this->wp_fields_in_map;
	}

	/**
	 * @param mixed $wp_fields_in_map
	 *
	 * @return LdapToWp
	 */
	public function setWpFieldsInMap( $wp_fields_in_map ) {
		$this->wp_fields_in_map = $wp_fields_in_map;

		return $this;
	}

	public function getAttributeFromAdUser( $adUser, $attribute ) {
		$value = $adUser->getAttribute( $attribute );
		$value = $value[0] ?? '';

		return $value;
	}

	public function updateUserData( $user_id, $adUser ) {
		$userdata['ID'] = $user_id;
		foreach ( $this->getWpFieldsInMap() as $field => $adField ) {
			$value              = $this->getAttributeFromAdUser( $adUser, $adField );
			$userdata[ $field ] = $value;

		}

		return wp_update_user( $userdata );
	}

	public function updateXprofileData( $user_id, $adUser ) {
		foreach ( $this->getXprofileFieldsInMap() as $xprofile_field => $adField ) {
			$field_id = BP_XProfile_Field::get_id_from_name( $xprofile_field );
			if ( ! $field_id ) {
				wp_die( "Field $xprofile_field does not exists" );
			}

			if ( $xprofile_field === 'manager' ) {
				if ( $manager = $this->getManager( $adUser ) ) {
					$value = $manager->getAttribute( 'displayname' );
				} else {
					$value = '';
				}
			} else {
				$value = $this->getAttributeFromAdUser( $adUser, $adField );
			}


			xprofile_set_field_data( $field_id, $user_id, $value );
		}

	}

	/**
	 * @return mixed
	 */
	public function getXprofileFieldsInMap() {
		return $this->xprofile_fields_in_map;
	}

	/**
	 * @param mixed $xprofile_fields_in_map
	 *
	 * @return LdapToWp
	 */
	public function setXprofileFieldsInMap( $xprofile_fields_in_map ) {
		$this->xprofile_fields_in_map = $xprofile_fields_in_map;

		return $this;
	}

	public function getManager( User $user ) {
		$managerDn = $user->getAttribute( 'manager' );
		$ad        = new WpAdldap2();
		$provider  = $ad->connect();
		$manager   = $provider->search()->users()->setDn( $managerDn[0] )->first();

		return $manager;
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