<?php
 /**
  * Framework Core
  *
  * @package Framework\Core
  * @author Daniel Farrelly <daniel@jellystyle.com>
  * @copyright 2014 Daniel Farrelly <daniel@jellystyle.com>
  * @license FreeBSD
  */

	namespace Forms;

 /**
  * Class Manager
  */

	class Element extends \Framework\Core\Object {

	 /**
	  *
	  *
	  * @var string
	  */

		private $_tagName;

	 /**
	  *
	  *
	  * @var array
	  */

		private $_attributes = array();

	 /**
	  *
	  *
	  * @var string
	  */

		private $_value;

	 /**
	  * Constructor
	  *
	  * @param string $tagName The type of HTML element to draw, i.e. "a" or "strong".
	  * @param array $attributes The
	  * @param string $value
	  * @return \Framework\Core\Path The path created using the given relative path.
	  */

		public function __construct( $tagName, $attributes=array(), $value=null ) {
			if( ! is_string( $tagName ) && ! is_array( $attributes ) && ! is_string( $tagName ) ) {
				throw new \InvalidArgumentException;
			}
			$this->_tagName = $tagName;
			$this->_attributes = $attributes;
			$this->value = $value;
		}

	 /**
	  *
	  */

		public function asString() {
			$html = '<'.$this->_tagName;
			foreach( $this->_attributes as $key => $value ) {
				if( $value === true ) {
					$html .= ' '.$key;
				}
				else if( ! empty( strval( $value ) ) ) {
					$html .= ' '.$key.'="'.strval( $value ).'"';
				}
			}
			$html .= ( $this->value === null ) ? ' />' : $this->value.' >';
			return $html;
		}

	 /**
	  * Get the tag name.
	  *
	  * @return string
	  */

		public function tagName() {
			return $this->_tagName;
		}

	 /**
	  * Get the attributes as an array.
	  *
	  * @return array
	  */

		public function attributes() {
			return $this->_attributes;
		}

	 /**
	  * Get the value of the attribute with the given name.
	  *
	  * @param string $name The name of the attribute to get the value for.
	  * @return mixed
	  */

		public function getAttribute( $name ) {
			if( isset( $this->_attributes[$name] ) ) {
				return $this->_attributes[$name];
			}
			return null;
		}

	 /**
	  * Set the value of the attribute with the given name.
	  *
	  * @param string $name The name of the attribute to set the value for.
	  * @param scalar $value The value to give the attribute.
	  * @return void
	  */

		public function setAttribute( $name, $value ) {
			if( ! is_scalar( $value ) ) {
				throw new \InvalidArgumentException;
			}
			$this->_attributes[$name] = $value;
		}

	 /**
	  * Remove the attribute with the given name.
	  *
	  * @param string $name The name of the attribute to remove.
	  * @return void
	  */

		public function removeAttribute( $name ) {
			if( isset( $this->_attributes[$name] ) ) {
				unset( $this->_attributes[$name] );
			}
		}

	 /**
	  * Get the content of the element.
	  *
	  * @return array
	  */

		public function setValue( $value ) {
			if( ! is_scalar( $tagName ) ) {
				throw new \InvalidArgumentException;
			}
			$this->_value = $value;
		}

	 /**
	  * Get the content of the element.
	  *
	  * @return array
	  */

		public function value() {
			return $this->_value;
		}

	}
