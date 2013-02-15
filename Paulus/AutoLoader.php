<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.1
 */

namespace Paulus;

/**
 * AutoLoader
 *
 * Paulus' autoloader class in charge of defining and registering the app's autoloading
 * 
 * @package		Paulus
 */
class AutoLoader {

	/*
	 * Declare properties
	 */
	protected $config;

	/**
	 * __construct
	 *
	 * AutoLoader constructor
	 * 
	 * @access public
	 * @return AutoLoader
	 */
	public function __construct() {
		//
	}

	/**
	 * config
	 *
	 * Set our AutoLoader's configuration
	 * 
	 * @param mixed $config		Configuration array (or ArrayAccess class) defining this class's behaviors/options
	 * @access private
	 * @return void
	 */
	private function config( $config = null ) {
		// Either grab the passed config or use our Singleton Config
		return $this->config = $config ?: Config::instance();
	}

	/**
	 * get_config
	 *
	 * Verify a config is set and get it if it is
	 * 
	 * @param string $config_name	The name of our config option/property
	 * @access private
	 * @return mixed
	 */
	private function get_config( $config_name ) {
		// Check that we have our config_name defined in our config
		if ( isset( $this->config[ $config_name ] ) !== true ) {
			throw new \Exception(
				'No "' . $config_name . '" config set. I don\'t know what to load!'
			);
		}
		else {
			// If its set, just hand it over
			return $this->config[ $config_name ];
		}
	}

	/**
	 * classname_to_path
	 *
	 * Convert the passed classname (with Namespace) into an include path
	 * 
	 * @param string $classname		The classname to convert
	 * @access public
	 * @return string
	 */
	public function classname_to_path( $classname ) {
		// Convert the namespace to a sub-directory path
		if ( strpos( $classname, '\\' ) !== false) {
			$classname = str_replace( '\\', DIRECTORY_SEPARATOR, $classname );
		}

		return $classname;
	}

	/**
	 * get_namespace_parts
	 *
	 * Get the parts of a namespace of a given classname
	 * 
	 * @param string $classname 	The classname to convert
	 * @param boolean $first_only	Only grab the first part of the namespace
	 * @access public
	 * @return string|array
	 */
	public function get_namespace_parts( $classname, $first_only = false ) {
		// Trim any left-slashes from our string
		$classname = ltrim( $classname, '\\' );

		if ( $first_only === true ) {
			return substr( $classname, 0, strpos( $classname, '\\' ) );
		}

		return explode( '\\', $classname );
	}

	// private register_autoloader(method_name) {{{ 
	/**
	 * register_autoloader
	 *
	 * Register an autoloader from this class
	 * 
	 * @param string $method_name	The name of this class's method
	 * @access private
	 * @return boolean
	 */
	private function register_autoloader( $method_name ) {
		// Register a new autoloader with the Standard PHP Library
		return spl_autoload_register(
			array(
				$this,
				$method_name,
			)
		);
	}

	// Paulus' internal autoloader (necessary for all Paulus internal libs)
	/**
	 * internal_autoloader
	 *
	 * Paulus' internal autoloader (necessary for all Paulus internal libs)
	 * 
	 * @param string $classname		The name of the class (with namespace)
	 * @access protected
	 * @return void
	 */
	protected function internal_autoloader( $classname ) {
		// Convert the namespace to a sub-directory path
		$classname = $this->classname_to_path( $classname );

		// Define our file path
		$file_path = $classname . '.php';

		// If the file is readable
		if ( is_readable($file_path) ) {
			// Require... just once. ;)
			require_once( $file_path );
		}
	}

	/**
	 * application_autoloader
	 *
	 * The application autoloader for all app-level classes/libraries
	 * 
	 * @param string $classname		The name of the class (with namespace)
	 * @access protected
	 * @return void
	 */
	protected function application_autoloader( $classname ) {
		// Convert the namespace to a sub-directory path
		$classname = $this->classname_to_path( $classname );

		// Define our file path
		$file_path = PAULUS_APP_DIR . $classname . '.php';

		// If the file is readable
		if ( is_readable($file_path) ) {
			// Require... just once. ;)
			require_once( $file_path );
		}
	}

	/**
	 * external_autoloader
	 *
	 * External autoloader for all external libs
	 * 
	 * @param string $classname		The name of the class (with namespace)
	 * @access protected
	 * @return void
	 */
	protected function external_autoloader( $classname ) {
		// Convert the namespace to a sub-directory path
		$classname = $this->classname_to_path( $classname );

		// Loop through each autoload-directory
		foreach( $this->get_config('autoload-directories') as $autoload_directory ) {
			// Define our file path
			$file_path = BASE_DIR . $autoload_directory . $classname . '.php';

			if ( is_readable($file_path) ) {
				require_once( $file_path );

				// We don't want to include multiple versions of the same class,
				// so let's just stop once we find one that exists
				break;
			}
		}
	}

	/**
	 * register_internal_autoloader
	 *
	 * Register our internal autoloader
	 * 
	 * @access public
	 * @return boolean
	 */
	public function register_internal_autoloader() {
		// Register the autoloader with this class and method named...
		return $this->register_autoloader( 'internal_autoloader' );
	}

	/**
	 * register_application_autoloader
	 * 
	 * Register our application autoloader
	 *
	 * @access public
	 * @return boolean
	 */
	public function register_application_autoloader() {
		// Register the autoloader with this class and method named...
		return $this->register_autoloader( 'application_autoloader' );
	}

	/**
	 * register_external_autoloader
	 *
	 * Register our external libs autoloader
	 * 
	 * @param mixed $config		Configuration array (or ArrayAccess class) defining our behaviors/options
	 * @access public
	 * @return boolean
	 */
	public function register_external_autoloader( $config = null ) {
		// Set our config
		$this->config( $config );

		// Register the autoloader with this class and method named...
		return $this->register_autoloader( 'external_autoloader' );
	}

	/**
	 * explicitly_load_externals
	 *
	 * Explicitly load our external libraries
	 * 
	 * @param mixed $config		Configuration array (or ArrayAccess class) defining our behaviors/options
	 * @access public
	 * @return boolean
	 */
	public function explicitly_load_externals( $config = null ) {
		// Set our config
		$this->config( $config );

		// For each external-libs path
		foreach( $this->get_config('external-libs') as $lib_path ) {
			// Require that external library
			require_once( PAULUS_EXTERNAL_LIB_DIR . $lib_path );
		}
	}

	/**
	 * load_routes
	 *
	 * Function to load all of the defined routes
	 * 
	 * @param mixed $config		Configuration array (or ArrayAccess class) defining our behaviors/options
	 * @access public
	 * @return void
	 */
	public function load_routes( $config = null ) {
		// Set our config
		$this->config( $config );

		// Get our routing config
		$routing_config = $this->get_config('routing');

		// If we want to load them all automatically
		if ( $routing_config['load_all_automatically'] ) {
			// Define our routes directory
			$route_dir = PAULUS_ROUTES_DIR;

			// Get an array of all of the files in the routes directory
			$found_routes = scandir( $route_dir );

			// Loop through each found route
			foreach( $found_routes as $route ) {
				// Is the route a file?
				if ( is_file( $route_dir . $route ) && ( strpos( $route, '_' ) !== 0 ) ) {
					// Define our endpoint base
					$route_base_url = '/' . basename( $route, '.php' );
					$route_namespace_url = $route_base_url;
					$route_name = ltrim( $route_namespace_url, '/' );

					// Is this our top-level route?
					if ( $route_name == $routing_config['top_level_route'] ) {
						// Set our base url to the top level
						$route_base_url = null;
					}

					// Instanciate the route's controller... but do it as a responder so it only instanciate's what is needed for that matched response. :)
					Router::with( $route_base_url, function() use ( $route_namespace_url ) {
						Router::route( function( $request, $respond, $service ) use ( $route_namespace_url ) {
							// Instanciate the route's controller
							Router::app()->new_route_controller( $route_namespace_url );
						});
					});

					// Include our routes from namespaced, separate files
					Router::with( $route_base_url, $route_dir . $route );
				}
			}
		}
		// Or we'll do it manually (based on what we have defined in our config)
		else {
			// Grab all of our manually assigned routes
			foreach( $routing_config['routes'] as $route ) {
				// Define our endpoint base and include path
				$route_base_url = '/' . $route;
				$route_namespace_url = $route_base_url;
				$route_path = PAULUS_ROUTES_DIR . $route . '.php';

				// Is this our top-level route?
				if ( $route == $routing_config['top_level_route'] ) {
					// Set our base url to the top level
					$route_base_url = null;
				}

				// Instanciate the route's controller... but do it as a responder so it only instanciate's what is needed for that matched response. :)
				Router::with( $route_base_url, function() use ( $route_namespace_url ) {
					Router::route( function( $request, $respond, $service ) use ( $route_namespace_url ) {
						// Instanciate the route's controller
						Router::app()->new_route_controller( $route_namespace_url );
					});
				});

				// Include our routes from namespaced, separate files
				Router::with( $route_base_url, $route_path );
			}
		}
	}

} // End class AutoLoader 
