<?php
 /**
  * MySQL library
  */

	namespace UI;

 /**
  * Build, render and validate forms as a series of objects.
  *
  * @package Framework\Forms
  * @author Daniel Farrelly <daniel@jellystyle.com>
  * @copyright 2014 Daniel Farrelly <daniel@jellystyle.com>
  * @license FreeBSD
  */

	abstract class Field extends \Framework\Core\Object {

	 /**
	  * Array of properties that act as aliases of methods
	  *
	  * @var array
	  */

		protected static $_dynamicProperties = array(
			'id',
			'label',
			'name',
			'section',
			'validation',
			'error',
		);

	 /**
	  *
	  *
	  * @var string
	  */

		private $label = null;

	 /**
	  *
	  */

		public function setLabel( $label ) {
			if( ! is_string( $label ) ) {
				throw new \InvalidArgumentException;
			}
			$this->label = $label;
			if( empty( $this->name ) ) {
				$this->name = underscore( $label, '-' );
			}
		}

	 /**
	  *
	  */

		public function label() {
			return $this->label;
		}

	 /**
	  *
	  */

		public function id() {
			if( ! empty( $this->section->form ) ) {
				return 	$this->section->form->key.'-'.$this->name;
			}
			return $this->name;
		}

	 /**
	  *
	  *
	  * @var string
	  */

		private $name = null;

	 /**
	  *
	  */

		public function setName( $name ) {
			if( ! is_string( $name ) ) {
				throw new \InvalidArgumentException;
			}
			$this->name = $name;
		}

	 /**
	  *
	  */

		public function name( $real_name=false ) {
			if( ! $real_name && ! empty( $this->section->form ) ) {
				return $this->section->form->key.'['.$this->name.']';
			}
			return $this->name;
		}

	 /**
	  *
	  *
	  * @var string
	  */

		public $value = null;

	 /**
	  *
	  *
	  * @var string
	  */

		public $isRequired = false;

	 /**
	  *
	  *
	  * @var string
	  */

		public $section = null;

	 /**
	  *
	  *
	  * @var string
	  */

		private $_validation = null;

	 /**
	  *
	  */

		public function setValidation( $validation ) {
			if( ! is_callable( $validation ) ) {
				throw new \InvalidArgumentException;
			}
			$this->_validation = $validation;
		}

	 /**
	  *
	  *
	  * @var string
	  */

		private $_error = null;

	 /**
	  *
	  */

		public function error() {
			return $this->_error;
		}


//
// Functions to Override
//

	 /**
	  * Render the form field as a HTML string.
	  *
	  * @return string
	  */

		abstract public function render( $value );

	 /**
	  * Validate the value of the field.
	  *
	  * @return bool
	  */

		abstract public function validate( $value );

//
//
//

	 /**
	  *
	  * @return string
	  */

		final public function __construct() {
		}

//
//
//

	 /**
	  *
	  * @return string
	  */

		final public function asString() {
			// Get the value the form considers us to have, default as required
			$value = isset( $this->section->form ) ? $this->section->form->valueForFieldNamed( $this->name(true) ) : null;

			// Render the field
			$html = '<p class="form-field'.( ! $this->validate( $value ) ? ' form-field-invalid' : null ).'">';
			if( ! empty( $this->label ) ) {
				$html .= '<label for="'.$this->id.'">'.$this->label;
				if( $this->isRequired ) {
					$html .= '<span class="required" title="Required field.">*</span>';
				}
				$html .= '</label>';
			}
			$html .= $this->render( $value ?: $this->value );
			$html .= '</p>';

			// Return
			return $html;
		}

	 /**
	  *
	  * @return bool
	  */

		final public function isValid() {
			$default_error = 'The value provided for \''.$this->label.'\' is invalid.';

			// Fetch the value the form considers us to have, DO NOT DEFAULT.
			$value = isset( $this->section->form ) ? $this->section->form->valueForFieldNamed( $this->name(true) ) : null;

			// Basic validation
			$basic_validation = $this->validate( $value );
			if( $basic_validation !== true ) {
				$this->_error = is_string( $basic_validation ) ? $basic_validation : $default_error;
				return false;
			}

			// Custom validation
			if( is_callable( $this->_validation ) ) {
				$custom_validation = call_user_func( $this->_validation, $value );
				if( $custom_validation !== true ) {
					$this->_error = is_string( $custom_validation ) ? $custom_validation : $default_error;
					return false;
				}
			}

			// If we made it this far, the field is valid
			return true;
		}

//
//
//

	}
