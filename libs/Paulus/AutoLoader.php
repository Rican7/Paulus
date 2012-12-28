<?php

namespace Paulus;

// AutoLoader class that makes autoloading ALL THE THINGS easier/cleaner
class AutoLoader {

	// Declare properties
	protected $config;

	// Constructor
	public function __construct() {
		//
	}

	// Function to set our AutoLoader's configuration
	private function config( $config = null ) {
		// Either grab the passed config or use our Singleton Config
		return $this->config = $config ?: Config::instance();
	}

	// Function to verify a config is set and get it if it is
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

	// Function to convert the passed classname (with Namespace) into an include path
	public function classname_to_path( $classname ) {
		// Convert the namespace to a sub-directory path
		if ( strpos( $classname, '\\' ) !== false) {
			$classname = str_replace( '\\', DIRECTORY_SEPARATOR, $classname );
		}

		return $classname;
	}

	// Function to register an autoloader from this class
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
	protected function internal_autoloader( $classname ) {
		// Convert the namespace to a sub-directory path
		$classname = $this->classname_to_path( $classname );

		// Define our file path
		$file_path = PAULUS_LIB_DIR . $classname . '.php';

		// If the file is readable
		if ( is_readable($file_path) ) {
			// Require... just once. ;)
			require_once( $file_path );
		}
	}

	// External autoloader for all external libs
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

	// Register our internal autoloader
	public function register_internal_autoloader() {
		// Register the autoloader with this class and method named...
		return $this->register_autoloader( 'internal_autoloader' );
	}

	// Register our external libs autoloader
	public function register_external_autoloader( $config = null ) {
		// Set our config
		$this->config( $config );

		// Register the autoloader with this class and method named...
		return $this->register_autoloader( 'external_autoloader' );
	}

	// Explicitly load our external libraries
	public function explicitly_load_externals( $config = null ) {
		// Set our config
		$this->config( $config );

		// For each external-libs path
		foreach( $this->get_config('external-libs') as $lib_path ) {
			// Require that external library
			require_once( PAULUS_EXTERNAL_LIB_DIR . $lib_path );
		}
	}

	// Function to load all of the defined routes
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
