<?php

namespace WpAdldap2\Admin;

use WpAdldap2\Admin\Views\AdminPageExploreLdap;
use WpAdldap2\Traits\TraitHasFactory;

class AdminExploreLdap {
	use TraitHasFactory;

	public function settingsPage() {

		echo new AdminPageExploreLdap();

	}


}