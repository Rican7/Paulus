<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.2
 */

namespace Paulus;

use \ArrayAccess;

/**
 * Config 
 *
 * Class for loading and defining access to configuration files
 * 
 * @uses Singleton
 * @uses ArrayAccess
 * @package		Paulus
 */
class Config extends Singleton implements ArrayAccess {

	/*
	 * Declare properties
	 */
	protected $config = array();

	/**
	 * __construct
	 *
	 * Config constructor
	 * Load's our configuration immediately upon instanciation
	 * 
	 * @access protected
	 * @return Config
	 */
	protected function __construct() {
		// Let's load our configuration files's data
		$this->config = ( new FileArrayLoader( PAULUS_CONFIG_DIR, '_', 'load_config' ) )->load();
	}

	/*
	 * ArrayAccess Methods (MUST implement)
	 */

	/**
	 * offsetExists
	 *
	 * Interface handler for when using the object with an "isset"
	 * 
	 * @param mixed $key 
	 * @access public
	 * @return boolean
	 */
	public function offsetExists( $key ) {
		return isset( $this->config[ $key ] );
	}

	/**
	 * offsetGet
	 *
	 * Interface handler for when trying to read a key in the object
	 * 
	 * @param mixed $key 
	 * @access public
	 * @return mixed|null
	 */
	public function offsetGet( $key ) {
		// Make sure its set first
		if ( isset( $this->config[ $key ] ) ) {
			return $this->config[ $key ];
		}

		return null;
	}

	/**
	 * offsetSet
	 *
	 * Interface handler for when trying to write to a key in the object
	 * Denies access to external services writing directly to the configuration array
	 * 
	 * @param mixed $key 
	 * @param mixed $value 
	 * @access public
	 * @return boolean
	 */
	public function offsetSet( $key, $value ) {
		// Let's not let them directly write to the config
		error_log( 'Illegal access to Config. Trying to write to a purposefully protected resource.' );
		//TODO: Convert to exception.... "errors" are just ghetto
		return false;
	}

	/**
	 * offsetUnset
	 *
	 * Interface handler for when trying to write to a key in the object
	 * Denies access to external services writing directly to the configuration array
	 * 
	 * @param mixed $key 
	 * @access public
	 * @return boolean
	 */
	public function offsetUnset( $key ) {
		// Let's not let them directly unset/delete anything in the config
		error_log( 'Illegal access to Config. Trying to write to a purposefully protected resource.' );
		//TODO: Convert to exception.... "errors" are just ghetto
		return false;
	}

} // End class Config
