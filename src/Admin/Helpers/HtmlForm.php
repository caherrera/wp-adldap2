<?php

namespace WpAdldap2\Admin\Helpers;

/**
 * Class HtmlForm
 *
 * @package WpAdldap2\Admin
 * @method HtmlForm p( $content, Array $attr = [] )
 * @method HtmlForm td( $content, Array $attr = [] )
 * @method HtmlForm th( $content, Array $attr = [] )
 */
class HtmlForm {

	protected $_html = [];

	public function __construct( $attr = [], $content = [] ) {
		if ( $content ) {
			$this->_html = $this->wrap( $content, 'div', $attr );
		} elseif ( is_array( $attr ) ) {
			$this->_html = (array) $attr;
		} elseif ( is_string( $attr ) ) {
			$this->_html = [ $attr ];
		}
	}

	public function wrap( $content, $tag = 'div', $attr = [] ) {
		$array_to_attr = $this->arrayToAttr( $attr );

		return sprintf( "<%s %s>%s</%s>", $tag, $array_to_attr, $content instanceof HtmlForm ? $content : new HtmlForm( $content ), $tag );

	}

	private function arrayToAttr( $attr = [] ) {
		$callback = function ( $key, $value ) {
			return sprintf( "%s=\"%s\"", $key, $value );
		};

		$arr = array_map( $callback, array_keys( $attr ), $attr );
		$arr = implode( " ", $arr );

		return $arr;
	}

	public function title( $title ) {
		return "<h2>$title</h2>";
	}

	public function subtitle( $title ) {
		return "<h3>$title</h3>";
	}

	public function row( $row = [] ) {
		return $this->wrap( $row, 'tr' );
	}

	public function inputSetting( $name, $title, $value, $default ) {
		$html = sprintf( "<tr class=\"form-field\"><th scope=\"row\"><label>%s</label></th>", $title );
		$html .= sprintf( "<td><input name=\"%s\" value=\"%s\" placeholder=\"%s\"/></td></tr>", $name, $value ?: $default, $default );

		return $html;
	}

	public function inputMultiples( $name, $title, $values, $default ) {
		$rows = [];

		for ( $i = 0; $i < 4; $i ++ ) {
			$value  = $values[ $i ] ?? [];
			$value  = wp_parse_args( $value, $default );
			$rows[] = $this->_inputMultiples( $name . '[' . $i . ']', ( $i + 1 ), $value, $default );

		}

		$th = [ $this->th( '', [ 'style' => 'padding-left:10px' ] ) ];
		foreach ( array_keys( $default ) as $k ) {
			$th[] = $this->th( ucwords( $k ), [ 'style' => 'padding-left:10px' ] );
		}

		$html = sprintf( "<tr class=\"form-field\"><th scope=\"row\"><label>%s</label></th><td>%s</td></tr>", $title, $this->table( [
			$this->thead( $this->tr( $th ) ),
			$this->tbody( $rows )
		], [ 'border' => 1 ] ) );

		return $html;
	}

	public function _inputMultiples( $name, $title, $values, $default ) {
		$html = sprintf( "<th scope=\"row\" style='width: auto;'><label>%s</label></th>", $title );
		foreach ( $values as $key => $value ) {
			if ( isset( $default[ $key ] ) ) {
				$def  = $default[ $key ];
				$html .= sprintf( "<td><input name=\"%s[%s]\" value=\"%s\" placeholder=\"%s\"/></td>", $name, $key, $value ?: $def, $def );
			}
		}


		return $this->wrap( $html, 'tr', [ 'class' => 'form-field' ] );
	}

	public function table( $rows = [] ) {
		return $this->wrap( $rows, 'table', [ 'class' => 'form-table' ] );
	}

	public function submit( $title ) {
		return sprintf( "<p class='submit'><input type='submit' class='button button-primary' value='%s'></p>", $title );
	}

	function __toString() {
		// TODO: Implement __toString() method.
		$string = implode( '', array_map( function ( $html ) {
			return (string) $html;
		}, (array) $this->_html ) );

		return $string;
	}

	public function form( $html ) {
		return $this->wrap( $html, 'form', [ 'method' => "post", 'action' => "" ] );
	}

	public function add( $tag ) {
		array_push( $this->_html, $tag );
	}

	public function __call( $name, $arguments ) {
		$content = array_shift( $arguments );

		return ( new self() )->wrap( $content, $name, $arguments ? $arguments[0] : [] );
	}
}