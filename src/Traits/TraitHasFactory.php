<?php

namespace WpAdldap2\Traits;

trait TraitHasFactory {
	private static $instance;

	static public function factory() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}