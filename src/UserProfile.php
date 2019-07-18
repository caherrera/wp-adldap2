<?php

namespace WpAdldap2;

use BP_XProfile_Group;
use WpAdldap2\Traits\TraitHasFactory;

/**
 * Class UserProfile
 *
 * @package WpAdldap2
 * @method static UserProfile factory
 */
class UserProfile {
	use TraitHasFactory;
	const WP_USER_FIELDS = [ 'user_login', 'username', 'email', 'nicename', 'nickname', 'name', 'display_name', 'first_name', 'last_name' ];

	public function getUserFields( $extras = true ) {
		$wordpress_fields = [];
		foreach ( self::WP_USER_FIELDS as $f ) {
			$wordpress_fields [ $f ] = (object) [ 'id' => $f, 'name' => $f, 'type' => 'textbox' ];
		}
		if ( $extras && is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			$xprofile = $this->xprofile_fetch_fields();
		} else {
			$xprofile = [];
		}

		return array_merge( $wordpress_fields, $xprofile );
	}

	public function xprofile_fetch_fields() {
		$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		$fields         = [];
		if ( ! empty( $profile_groups ) ) {
			foreach ( $profile_groups as $profile_group ) {
				if ( ! empty( $profile_group->fields ) ) {
					foreach ( $profile_group->fields as $field ) {
						if ( $field->name != 'name' ) {
							$fields[ $field->name ] = (object) [ 'id' => $field->id, 'name' => $field->name, 'type' => $field->type ];
						}
					}
				}
			}
		}

		return $fields;
	}
}