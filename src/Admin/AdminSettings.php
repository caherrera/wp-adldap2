<?php

namespace WpAdldap2\Admin;

use BP_XProfile_Group;

class AdminSettings {
	private static $instance;

	static public function factory() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		self::$instance->settingsPage();

		return self::$instance;
	}

	public function settingsPage() {
		if ( $_POST ) {
			$this->saveSettings();
		}
		echo new AdminPage();

	}

	public function saveSettings() {
		foreach ( $_POST as $key => $item ) {
			if ( preg_match( "/" . WPADLDAP2 . "/", $key ) ) {
				update_option( $key, $item );
			}
		}
	}

	public function getUserFields() {
		$wordpress_fields = array_map( function ( $f ) {
			return (object) [ 'id' => $f, 'name' => $f, 'type' => 'textbox' ];
		}, [ 'username', 'email', 'nicename', 'nickname', 'Name', 'display_name', 'first_name', 'last_name' ] );
		if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			$xprofile = static::xprofile_fetch_fields();
		} else {
			$xprofile = [];
		}

		return array_merge( $wordpress_fields, $xprofile );
	}

	function xprofile_fetch_fields() {
		$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		$fields         = [];
		if ( ! empty( $profile_groups ) ) {
			foreach ( $profile_groups as $profile_group ) {
				if ( ! empty( $profile_group->fields ) ) {
					foreach ( $profile_group->fields as $field ) {
						$fields[] = (object) [ 'id' => $field->id, 'name' => $field->name, 'type' => $field->type ];
					}
				}
			}
		}

		return $fields;
	}
}