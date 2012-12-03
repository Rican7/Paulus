<?php

namespace Paulus;

// PHP 5.3+ Singleton Pattern
abstract class Singleton {

	// Class properties
	protected static $instance; // Keep track of the instance

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
		// If the instance doesn't exist yet
		if ( is_null( static::$instance ) ) {
			// Create it and keep a reference to it
			static::$instance = new static();
		}

		// Always return the instance
		return static::$instance;
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
