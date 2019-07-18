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
		$this->add( $this->wrap( 'small{display:block};', 'style' ) );

		return parent::__toString();
	}

	public function print() {

		$p       = new HtmlTag();
		$map     = array_flip( array_filter( Settings::getMap() ) );
		$list    = $this->getList();
		$first   = current( $list );
		$first   = array_keys( $first );
		$first[] = '#';
		$thead   = $p->thead( [
			$p->tr( array_map( function ( $ldap ) use ( $map, $p ) {
				return $p->th( ( $map[ $ldap ] ?? '' ) . $p->small( $ldap ) );
			}, $first ) ),
		] );


		$tbody = $p->tbody( $this->printTbody($list) );

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

	public function printTbody( $list ) {
		$tbody = [];
		foreach ( $list as $index => $user ) {
			$tbody[] = $this->printUser( $index + 1, $user );
		}

		return $tbody;
	}

	public function printUser( $index, $user ) {
		$p  = new HtmlTag();
		$tr = array_map( function ( $attr ) use ( $p ) {
			return $p->td( $attr );
		}, $user );
		array_unshift( $tr, $p->td( $index ) );

		return $p->tr( $tr );
	}


}