<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.0
 */

namespace Paulus;

/**
 * Singleton 
 *
 * PHP 5.3+ Singleton Pattern
 * 
 * @abstract
 * @package		Paulus
 */
abstract class Singleton {

	/**
	 * __construct
	 *
	 * Singleton constructor
	 * 
	 * @access private
	 * @return void
	 */
	private function __construct() {
		// Do nothing
	}

	/**
	 * __clone
	 *
	 * Don't allow cloning
	 * 
	 * @final
	 * @access private
	 * @return void
	 */
	final private function __clone() {
		// Do nothing
		//TODO: Possibly throw an exception here..
	}

	/**
	 * instance
	 *
	 * Get the instance of the class, or create one if one doesn't exist yet
	 * 
	 * @static
	 * @final
	 * @access public
	 * @return Singleton
	 */
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


	/**
	 * getInstance
	 *
	 * Alias the instance method
	 * 
	 * @static
	 * @final
	 * @access public
	 * @return Singleton
	 */
	final public static function getInstance() {
		return static::instance();
	}

	/**
	 * get_instance
	 *
	 * Alias the instance method
	 * 
	 * @static
	 * @final
	 * @access public
	 * @return Singleton
	 */
	final public static function get_instance() {
		return static::instance();
	}

} // End class Singleton
