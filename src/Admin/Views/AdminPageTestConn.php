<?php

namespace WpAdldap2\Admin\Views;

use WpAdldap2\Admin\Helpers\HtmlForm;
use WpAdldap2\Migrators\LdapToWp;


class AdminPageTestConn extends HtmlForm {

	protected $_html = [];

	public function __construct( $attr = [], $content = [] ) {
		if ( $content ) {
			parent::__construct( $attr, $content );
		} else {
			$this->add( $this->page( [
				$this->title( 'Test Connection' ),

			] ) );
		}

	}

	public function page( $html ) {
		return $this->wrap( 'list', 'div', [ 'class' => 'wrap' ] );
	}




}