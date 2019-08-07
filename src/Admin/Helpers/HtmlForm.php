<?php

namespace WpAdldap2\Admin\Helpers;

/**
 * Class HtmlForm
 *
 * @package WpAdldap2\Admin
 */
class HtmlForm extends HtmlTag {


	public function __construct( $attr = [], $content = [] ) {
		if ( $content ) {
			$this->_html = $this->wrap( $content, 'div', $attr );
		} elseif ( is_array( $attr ) ) {
			$this->_html = (array) $attr;
		} elseif ( is_string( $attr ) ) {
			$this->_html = [ $attr ];
		}
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_jquery_ui' ] );


	}


	public function enqueue_jquery_ui() {
		wp_enqueue_script( 'jquery-ui-core' );

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


	public function form( $html ) {
		return $this->wrap( $html, 'form', [ 'method' => "post", 'action' => "" ] );
	}

	public function hidden( array $attr = [] ) {
		return $this->input( 'hidden', $attr );
	}

	public function input( $type = 'text', array $attr = [] ) {
		$attr['type'] = $type;

		return $this->oneWrap( 'input', $attr );
	}


}