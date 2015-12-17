<?php
 /**
  * MySQL library
  */

	namespace UI;

	use \Framework\App\Request;

 /**
  * Build, render and validate forms as a series of objects.
  *
  * @package Framework\Forms
  * @author Daniel Farrelly <daniel@jellystyle.com>
  * @copyright 2014 Daniel Farrelly <daniel@jellystyle.com>
  * @license FreeBSD
  */

	class Form extends \Framework\Core\Object {

	 /**
	  * Array of properties that act as aliases of methods
	  *
	  * @var array
	  */

		protected static $_dynamicProperties = array(
			'key',
			'sections',
		);

	 /**
	  *
	  *
	  * @var string
	  */

		private $_method = 'post';

	 /**
	  *
	  *
	  * @var string
	  */

		private $_key = null;

	 /**
	  *
	  */

		public function key() {
			return $this->_key;
		}

	 /**
	  *
	  *
	  * @var string
	  */

		public $_nonce = null;

	 /**
	  *
	  *
	  * @var array
	  */

		private $_sections = array();

	 /**
	  *
	  *
	  * @var array
	  */

		private $_buttons = array();

	 /**
	  * Constructor.
	  *
	  * @return self
	  */

		public function __construct( $method='post', $key=null ) {
			$this->_method = $method ?: $this->_method;
			$this->_key = strlen( $key ) > 0 ? $key : md5( url()->path );
		}

	 /**
	  * Render the form as a string.
	  *
	  * @return string
	  */

		public function asString() {
			// Generate and store a nonce in the session values
			session_start();
			if( ! isset( $_SESSION['nonce_'.$this->_key] ) || empty( $this->_nonce = $_SESSION['nonce_'.$this->_key] ) ) {
		        $this->_nonce = md5( Request::server('REMOTE_ADDR').$this->_key.uniqid( mt_rand(), true ) );
		        $_SESSION['nonce_'.$this->_key] = $this->_nonce;
			}
	        session_write_close();

			// Render the form with a nonce input
			$html = '<form method="'.$this->_method.'">'."\n";
			$html .= '<input type="hidden" name="form_'.$this->_key.'" value="'.$this->_nonce.'" />'."\n";
			$html .= '<input type="hidden" name="form_referrer" value="'.Request::server('HTTP_REFERER').'" />'."\n";

			// Render the errors
			if( ( $errors = $this->errors() ) ) {
				$html .= '<div class="form-errors">';
				foreach( $errors as $error ) {
					$html .= '<p class="form-errors">'.$error.'</p>';
				}
				$html .= '</div>'."\n";
			}

			// Render the sections
			$html .= implode( "\n", $this->_sections )."\n";

			// Always ensure we have buttons
			if( count( $this->_buttons ) == 0 ) {
				$this->addButton( 'submit', 'Submit', array( 'highlighted' ) );
			}

			// Render the buttons
			$html .= '<p class="form-buttons">';
			foreach( $this->_buttons as $button ) {
				$class = ! empty( $button[2] ) ? ' class="'.$button[2].'"' : null;
				$html .= '<button type="'.$button[0].'"'.$class.'>'.$button[1].'</button>';
			}
			$html .= '</p>'."\n";

			// We're all done here
			$html .= '</form>';
			return $html;
		}

//
// Sections
//

	 /**
	  *
	  * @return void
	  */

		public function sections() {
			return $this->_sections;
		}

	 /**
	  *
	  * @param string $title
	  * @return \Forms\Section
	  */

		public function addSection( $title=null ) {
			$section = new Section;
			$section->title = $title;
			$section->form = $this;
			return $this->_sections[] = $section;
		}

	 /**
	  *
	  * @return void
	  */

		public function addField( Field $field ) {
			if( count( $this->_sections ) === 0 ) {
				$this->addSection( null );
			}
			return end( $this->_sections )->addField( $field );
		}

	 /**
	  *
	  * @param string $type
	  * @param string $title
	  * @param array $classes
	  * @return \Forms\Section
	  */

		public function addButton( $type='submit', $title='Submit', $classes=array() ) {
			$this->_buttons[] = array( strtolower( $type ), $title, implode( ' ', $classes ) );
		}

//
// Performing Validation
//

	 /**
	  *
	  * @return bool
	  */

		public function isValid() {
			// Retrieve the nonce
			session_start();
			$session_nonce = isset( $_SESSION['nonce_'.$this->_key] ) ? $_SESSION['nonce_'.$this->_key] : null;
			$request_nonce = isset( $_REQUEST['form_'.$this->_key] ) ? $_REQUEST['form_'.$this->_key] : null;
	        session_write_close();

			// Compare the nonce values. If the nonce matches, validate the rest of the form.
			if( $request_nonce !== null && $request_nonce === $session_nonce ) {
				return count( array_filter( $this->_sections, function( $section ) { return ! $section->isValid(); }) ) === 0;
			}

			// Default to false
			return false;
		}

	 /**
	  *
	  *
	  * @param callable $callback
	  * @return bool
	  */

		public function whenValid( $callback ) {
			$referrer = isset( $_REQUEST['form_referrer'] ) ? new \Framework\Core\URL( $_REQUEST['form_referrer'] ) : null;
			return $this->isValid && call_user_func( $callback, $this->values(), $referrer );
		}

//
// Getting the Values
//

	 /**
	  *
	  *
	  * @var array
	  */

		private $_values = array();

	 /**
	  *
	  *
	  * @param callable $callback
	  * @return bool
	  */

		public function values() {
			// Get the defaults and merge in the request values
			if( count( $this->_values ) === 0 ) {
				if( count( $this->_sections ) > 0 ) foreach( $this->_sections as $section ) foreach( $section->fields as $field ) {
					$this->_values[$field->name(true)] = $field->value;
				}
				if( ! empty( $_REQUEST[$this->key] ) ) {
					$this->_values = array_merge( $this->_values, $_REQUEST[$this->key] );
				}
			}

			// Return
			return $this->_values;
		}

	 /**
	  *
	  *
	  * @param callable $callback
	  * @return bool
	  */

		public function valueForFieldNamed( $name ) {
			$values = $this->values();
			return isset( $values[$name] ) ? $values[$name] : null;
		}

//
// Getting the Errors
//

	 /**
	  *
	  *
	  * @var array
	  */

		private $_errors = array();

	 /**
	  *
	  *
	  * @param callable $callback
	  * @return bool
	  */

		public function errors() {
			// Get the defaults and merge in the request values
			if( count( $this->_errors ) === 0 && count( $this->_sections ) > 0 ) {
				foreach( $this->_sections as $section ) foreach( $section->fields as $field ) if( $field->error !== null ) {
					$this->_errors[$field->name(true)] = $field->error;
				}
			}

			// Return
			return array_unique( $this->_errors );
		}

	}
