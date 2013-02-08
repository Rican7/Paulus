<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.10.0
 */

namespace Paulus;

// PHP 5.3+ Singleton Pattern
abstract class Singleton {

	// Constructor
	private function __construct() {
		// Do nothing
	}

	// Don't allow cloning
	final private function __clone() {
		// Do nothing
	}

	// Get the instance of the class
	final public static function instance() {
		// Create an instance var to keep track of the instance
		static $instance = null;

		// If the instance doesn't exist yet
		if ( is_null( $instance ) ) {
			// Create it and keep a reference to it
			$instance = new static();
		}

		// Always return the instance
		return $instance;
	}

	// Alias the instance method
	final public static function getInstance() {
		return static::instance();
	}

	// Alias the instance method
	final public static function get_instance() {
		return static::instance();
	}

} // End class Singleton
