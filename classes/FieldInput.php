<?php
 /**
  * MySQL library
  */

	namespace Forms;

 /**
  * Build, render and validate forms as a series of objects.
  *
  * @package Framework\Forms
  * @author Daniel Farrelly <daniel@jellystyle.com>
  * @copyright 2014 Daniel Farrelly <daniel@jellystyle.com>
  * @license FreeBSD
  */

	class FieldInput extends Field {

	 /**
	  *
	  *
	  * @var string
	  */

		public $type = 'text';

	 /**
	  *
	  *
	  * @var string
	  */

		public $maxLength = null;

	 /**
	  * Render the form field as a HTML string.
	  *
	  * @return string
	  */

		public function render( $value ) {
			$input = new Element( 'input', array(
				'type' => $this->type,
				'id' => $this->id,
				'name' => $this->name,
				'value' => $this->type !== 'password' ? $value : null,
				'required' => $this->isRequired,
				'maxlength' => $this->maxLength > 0 ? $this->maxLength : null,
			));

			return $input;
		}

	 /**
	  * Validate the value of the field.
	  *
	  * @return bool
	  */

		public function validate( $value ) {
			if( $this->isRequired && empty( $value ) ) {
				return 'You didn\'t provide a value for \''.$this->label.'\', which is a required field.';
			}
			if( $this->type === 'email' ) {
				require_once dirname(__FILE__).'/../lib/is_email.php';
				if( ! is_email( $value ) ) {
					return 'The value you provided for \''.$this->label.'\' does not appear to be a valid email address.';
				}
			}
			if( $this->maxLength > 0 && strlen( $value ) > $this->maxLength ) {
				return 'The value you provided for  \''.$this->label.'\' is too long. Only '.$this->maxLength.' characters are allowed.';
			}
			return true;
		}

	}
