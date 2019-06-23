<?php

namespace WpAdldap2\Admin\Views;

use WpAdldap2\Admin\Helpers\HtmlForm;
use WpAdldap2\Admin\Helpers\HtmlTab;


class AdminPage extends HtmlForm {

	protected $_html = [];

	public function __construct( $attr = [], $content = [] ) {
		if ( $content ) {
			parent::__construct( $attr, $content );
		} else {
			$this->add( $this->page( [
				$this->title( 'WP-ADLDAP2' ),
				$this->p( 'Sync your Active Directory With Wordpress' ),
				$this->tabs()
			] ) );
		}

	}

	public function page( $html ) {
		return $this->wrap( $this->form( $html ), 'div', [ 'class' => 'wrap' ] );
	}

	public function tabs() {
		$tabs = [
			new HtmlTab( 'LDAP Account', ( new AdminForm() )->printLdapSettings() ),
			new HtmlTab( 'Filters', ( new AdminForm() )->printLdapFilters() ),
			new HtmlTab( 'Fields to Sync', ( new AdminForm() )->printFieldsToSync() ),
		];

		$ul   = array_map( function ( HtmlTab $tab ) {
			$a = ( new HtmlForm )->a( $tab->getTabName(), [ 'href' => '#' . $tab->getId() ] );

			return ( new HtmlForm )->li( $a );
		}, $tabs );
		$tabs = (string) new HtmlForm( $tabs );

		return $this->wrap( [
			$this->ul( $ul ),
			$tabs,
			$this->submit( 'Save Changes' )

		], 'div', [ 'id' => 'tabs' ] );
	}


}