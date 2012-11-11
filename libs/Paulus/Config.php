<?php

namespace Paulus;

use \ArrayAccess;

// Explicitly require our required base abstract class
require_once 'Singleton.php';

// Config class
// Class for loading and defining access to configuration files
class Config extends Singleton implements ArrayAccess {

	// Declare properties
	protected $config = array();
	protected $config_files = array(
		'app-meta',
		'external-libs',
		'autoload-directories',
		'database',
		'rest',
		'routing',
		'template',
	);

	// Constructor
	protected function __construct() {
		// Let's load our configuration files
		$this->load_config();
	}

	// Function to load our configuration files
	protected function load_config() {

		// Loop through each config file
		foreach( $this->config_files as $file ) {
			// Include the file
			require_once( BASE_DIR . 'configs/' . $file . '.php');

			// Set the file's returned configuration in a namespaced key
			$this->config[ $file ] = $load_config();

			// Garbage collect
			unset( $load_config );
		}

	}

	/*
	 * ArrayAccess Methods (MUST implement)
	 */

	// Interface handler for when using the object with an "isset"
	public function offsetExists( $key ) {
		return isset( $this->config[ $key ] );
	}

	// Interface handler for when trying to read a key in the object
	public function offsetGet( $key ) {
		// Make sure its set first
		if ( isset( $this->config[ $key ] ) ) {
			return $this->config[ $key ];
		}

		return null;
	}

	// Interface handler for when trying to write to a key in the object
	public function offsetSet( $key, $value ) {
		// Let's not let them directly write to the config
		error_log( 'Illegal access to Config. Trying to write to a purposefully protected resource.' );
		return false;
	}

	// Interface handler for when trying to write to a key in the object
	public function offsetUnset( $key ) {
		// Let's not let them directly unset/delete anything in the config
		error_log( 'Illegal access to Config. Trying to write to a purposefully protected resource.' );
		return false;
	}

} // End class Config
