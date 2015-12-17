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

	class Section extends \Framework\Core\Object {

	 /**
	  * Array of properties that act as aliases of methods
	  *
	  * @var array
	  */

		protected static $_dynamicProperties = array(
			'fields',
		);

	 /**
	  *
	  *
	  * @var string
	  */

		private $title = array();

	 /**
	  *
	  *
	  * @var array
	  */

		private $_fields = array();

	 /**
	  *
	  *
	  * @var string
	  */

		public $form = null;

//
//
//

	 /**
	  * Render the section as a string.
	  *
	  * @return string
	  */

		public function asString() {
			if( ! empty( $this->title ) ) {
				$html = '<fieldset><legend>'.$this->title.'</legend>'."\n";
				$html .= implode( "\n", $this->_fields );
				$html .= '</fieldset>'."\n";
				return $html;
			}
			else {
				return implode( "\n", $this->_fields );
			}
		}

//
// Fields
//

	 /**
	  *
	  * @return void
	  */

		public function fields() {
			return $this->_fields;
		}

	 /**
	  *
	  * @return void
	  */

		public function addField( Field $field ) {
			$field->section = $this;
			$this->_fields[] = $field;
		}

//
// Performing Validation
//

	 /**
	  *
	  * @return bool
	  */

		public function isValid() {
			return count( array_filter( $this->_fields, function( $field ) { return ! $field->isValid(); }) ) === 0;
		}

	}
