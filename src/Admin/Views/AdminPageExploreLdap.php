<?php

namespace WpAdldap2\Admin\Views;

use WpAdldap2\Admin\Helpers\HtmlForm;
use WpAdldap2\Admin\Helpers\HtmlTag;
use WpAdldap2\Settings;


class AdminPageExploreLdap extends HtmlForm {

	protected $_html = [];

	protected $list = [];

	public function __toString() {
		$this->print();
		$this->add( $this->wrap( 'small{display:block};','style' ) );

		return parent::__toString();
	}

	public function print() {

		$p     = new HtmlTag();
		$map   = array_flip( array_filter( Settings::getMap() ) );
		$list  = $this->getList();
		$first = current( $list );
		$thead = $p->thead( [
			$p->tr( array_map( function ( $ldap ) use ( $map, $p ) {
				return $p->th( ( $map[ $ldap ] ?? '' ) . $p->small( $ldap ) );
			}, array_keys( $first ) ) ),
		] );
		$tbody = array_map( function ( $user ) use ( $p ) {
			$tr = array_map( function ( $attr ) use ( $p ) {
				return $p->td( $attr );
			}, $user );

			return $p->tr( $tr );
		}, $list );
		$tbody = $p->tbody( $tbody );

		$this->add( $p->table( [
			$thead,
			$tbody
		] ) );


	}

	/**
	 * @return array
	 */
	public function getList(): array {
		return $this->list;
	}

	/**
	 * @param array $list
	 *
	 * @return AdminPageExploreLdap
	 */
	public function setList( array $list ): AdminPageExploreLdap {
		$this->list = $list;

		return $this;
	}


}