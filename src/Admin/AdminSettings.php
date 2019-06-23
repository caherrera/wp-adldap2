<?php

namespace WpAdldap2\Admin;

use WpAdldap2\Admin\Views\AdminPage;
use WpAdldap2\Traits\TraitHasFactory;

class AdminSettings {
	use TraitHasFactory;

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
}