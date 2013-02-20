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

use	\Paulus\Config,
	\Paulus\AutoLoader,
	\Paulus\Router,
	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Interfaces\ApiVerboseException,
	\Paulus\Exceptions\EndpointNotFound,
	\Paulus\Exceptions\WrongMethod,
	\Paulus\Util,
	\stdClass;

/**
 * Paulus 
 *
 * Main Paulus system class
 * 
 * @package		Paulus
 */
class Paulus {

	/*
	 * Declare properties
	 */
	public	$config; // Configuration array
	public	$controller; // Let's keep track of a potential route controller

	// Routing variable references
	private	$request;
	private	$response;
	private	$service;

	/**
	 * __construct
	 *
	 * Paulus constructor
	 * 
	 * @param mixed $config		Configuration array (or ArrayAccess class) defining Paulus' many options
	 * @param mixed $request	The router's request object
	 * @param mixed $response	The router's response object
	 * @param mixed $service 	The router's service object
	 * @access public
	 * @return Paulus
	 */
	public function __construct( $config = null ) {
		// First things first... get our init time
		if ( !defined( 'PAULUS_START_TIME' ) ) {
			define( 'PAULUS_START_TIME', microtime( true ) );
		}

		// Define our application's constants
		$this->define_constants();

		// Either grab the passed config or use our Singleton Config
		$this->config = $config ?: Config::instance();

		// Create our auto loader
		$autoloader = new AutoLoader( $this->config );

		// Load our all-important routing library
		$autoloader->load_routing_library();

		// Create a "first-hit" route responder to setup our app
		Router::route( function( $request, $response, $service ) {
			// Initialize our router's interval values
			Router::__init__( $request, $response, $service, $this );

			// Set some properties from our router
			$this->request = &$request;
			$this->response = &$response;
			$this->service = &$service;

			// Initialize some response properties
			$this->init_response_properties();

			// Setup our exception handler
			$this->setup_exception_handler();

			// Only pass our "app" to the service register if we've configured it to do so
			if ( $this->config['routing']['pass_app_to_service'] ) {
				// Register our app as a persistent service, for ease of use/accessibility
				$service->app = $this;
			}
		});

		// If there are autoload directories set
		if ( isset( $this->config['autoload-directories'] ) && count( $this->config['autoload-directories'] ) > 0 ) {
			// Register our external autoloader
			$autoloader->register_external_autoloader();
		}

		// Load any set external libraries explicitly
		if ( isset( $this->config['external-libs'] ) && count( $this->config['external-libs'] ) > 0 ) {
			// Load our external libraries explicitly
			$autoloader->explicitly_load_externals();
		}

		// Let's setup our database connection
		$this->setup_db_connection();

		// Load and define all of our routes
		$autoloader->load_routes();
	}

	/**
	 * define_constants
	 *
	 * Define our application constants
	 * 
	 * @access private
	 * @return void
	 */
	private function define_constants() {
		// Set our base directory here
		if ( !defined( 'PAULUS_BASE_DIR' ) ) {
			define( 'PAULUS_BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
		}

		// Quick function to make directory defining easier
		$dir_rel_path = function( $dir_name ) {
			return PAULUS_BASE_DIR . $dir_name . DIRECTORY_SEPARATOR;
		};

		if ( !defined( 'PAULUS_CONFIG_DIR' ) ) {
			define( 'PAULUS_CONFIG_DIR', $dir_rel_path( 'configs' ) );
		}
		if ( !defined( 'PAULUS_LIB_DIR' ) ) {
			define( 'PAULUS_LIB_DIR', $dir_rel_path( 'Paulus' ) );
		}
		if ( !defined( 'PAULUS_APP_DIR' ) ) {
			define( 'PAULUS_APP_DIR',  $dir_rel_path( 'application' ) );
		}
		if ( !defined( 'PAULUS_EXTERNAL_LIB_DIR' ) ) {
			define( 'PAULUS_EXTERNAL_LIB_DIR', $dir_rel_path( 'vendor' ) );
		}
	}

	/**
	 * init_response_properties
	 *
	 * Initialize our API's response properties in the "response" object
	 * 
	 * @access private
	 * @return void
	 */
	private function init_response_properties() {
		// Initialze some properties
		$this->response->status = null;
		$this->response->possible_methods = null;
		$this->response->message = null;
		$this->response->more_info = null;
		$this->response->data = null;
		$this->response->paging = null;
	}

	/**
	 * get_controller_callable
	 *
	 * Check if our current controller has a callable method and return it if it does
	 * 
	 * @param string $method_name	The name of the potential callable method in the controller
	 * @access private
	 * @return callable|false
	 */
	private function get_controller_callable( $method_name ) {
		// Let's see if we have a current controller instanciated
		if ( is_object( $this->controller ) && !is_null( $this->controller ) ) {

			// Define a callable method as an array
			$callable = array( $this->controller, $method_name ); // Give the controller class and the name of the method

			// Check if the method is actually callable/exists
			if ( is_callable( $callable, false ) ) {
				return $callable;
			}
		}

		return false;
	}

	/**
	 * setup_exception_handler
	 *
	 * Setup our Paulus exception handler
	 * 
	 * @access private
	 * @return void
	 */
	private function setup_exception_handler() {
		// Setup our classes default exception handler, in case anything escapes our other catches
		set_exception_handler( function( $exception ) {

			// Paulus - Handle all of our "ApiException"s
			if ( $exception instanceOf ApiException ) {
				// Handle our ApiException interface exceptions from Paulus
				$this->api_exception_handler( $exception );
			}
			// All other exceptions
			else {
				// Log the error
				$this->error_log( $exception->getMessage() );

				// Let's handle the exception gracefully (RESTfully)
				$this->abort( 500, 'EXCEPTION_THROWN', $exception->getMessage() );
			}
		});

		// Register an error handler through our Router's catcher
		$this->response->onError( function( $response, $error_message, $error_type, $exception ) {

			// Check if we have a callable handler in our controller
			$callable = $this->get_controller_callable( 'exception_handler' );

			// Did we actually get a callable?
			if ( $callable !== false ) {
				// Call the found handler
				return call_user_func_array(
					$callable, // The array of the callable
					array( $error_message, $error_type, $exception ) // Arguments
				);
			}
			// We didn't get a callable
			else {
				// Re-throw the exception to catch it in our Paulus-defined exception handler
				throw $exception;
			}
		});
	}

	/**
	 * setup_db_connection
	 *
	 * Setup our DB/ORM and pass it our configuration
	 * 
	 * @access private
	 * @return void
	 */
	private function setup_db_connection() {
		// Check to see if ActiveRecord exists... the app/developer might not want to use it
		if ( class_exists( '\ActiveRecord\Config', true ) ) { // Set to false to not try and autoload the class
			\ActiveRecord\Config::initialize( function( $cfg ) {
				// Set the directory of our data models
				$cfg->set_model_directory( $this->config['database']['model_directory'] );

				// Set our connection configuration
				$cfg->set_connections( $this->config['database']['connections'] );

				// Set our default connection
				$cfg->set_default_connection( $this->config['database']['default_connection'] );
			});
		}
	}

	/**
	 * setup_error_routes
	 *
	 * Setup our default error route responders and register them with our Router
	 * 
	 * @access private
	 * @return void
	 */
	private function setup_error_routes() {
		// 404 - We didn't match a route
		Router::route( '404', function( $request, $response, $service ) {
			// We didn't match the endpoint/route
			Router::app()->endpoint_not_found();
		});

		// 405 - We didn't match a route, but one WOULD have matched with a different method
		Router::route( '405', function( $request, $response, $service, $matches, $methods ) {
			// We didn't match the right method for the endpoint/route
			Router::app()->wrong_method( $methods );
		});
	}

	/**
	 * get_route_responder
	 *
	 * Get our controller's route responder
	 *
	 * This sees if our current controller has a callable method "route_respond", and returns it if possible
	 * 
	 * @access public
	 * @return callable
	 */
	public function get_route_responder() {

		// Check if we have a callable handler in our controller
		$callable = $this->get_controller_callable( 'route_respond' );

		// Did we actually get a callable?
		if ( $callable !== false ) {
			// Call the found handler
			return $callable;
		}

		// Default to just returning a closure
		return function( $arg ) {};
	}

	/**
	 * new_route_controller
	 *
	 * Instanciate a route controller if one exists
	 * 
	 * @param string $namespace		The namespace of the current route
	 * @access public
	 * @return null
	 */
	public function new_route_controller( $namespace ) {
		// Do we want to auto start our controllers?
		if ( $this->config['routing']['auto_start_controllers'] ) {
			// Let's get our class name from the namespace
			$name_parts = explode( '/', $namespace ); 

			// Let's convert our url endpoint name to a legit classname
			$name_parts = array_map(
				function( $string ) {
					$string = str_replace( '-', '_', $string ); // Turn all hyphens to underscores
					$string = str_replace( '_', ' ', $string ); // Turn all underscores to spaces (for easy casing)
					$string = ucwords( $string ); // Uppercase each first letter of each "word"
					$string = str_replace( ' ', '', $string ); // Remove spaces. BOOM! Zend-compatible camel casing

					return $string;
				},
				$name_parts
			);

			// Create a callable classname
			$classname = implode( '\\', $name_parts );
			$class = $this->config['routing']['controller_base_namespace'] . $classname;

			// Does the class exist? (Autoload it if its not loaded/included yet)
			if ( class_exists( $class, true ) ) {
				// Instanciate the controller and keep an easy reference to it
				$this->controller = new $class( $this->request, $this->response, $this->service, $this );
			}
		}

		return null;
	}

	/**
	 * parse
	 *
	 * Process a string as a template variable
	 * 
	 * @param string $unprocessed_string	The string to process and return
	 * @access public
	 * @return string
	 */
	public function parse( $unprocessed_string ) {
		// Copy the string so we can refer to both the processed and original
		$processed_string = $unprocessed_string;

		// Loop through each template key in the user config and parse
		foreach ( $this->config['template']['keys'] as $key => $val ) {
			$processed_string = str_replace( '{'. $key .'}', $val, $processed_string );
		}

		// Now for some framework level template keys/values
		$processed_string = str_replace( '{BASE_URL}', $this->config['app-meta']['base_url'], $processed_string );
		$processed_string = str_replace( '{APP_URL}', $this->config['app-meta']['app_url'], $processed_string );
		$processed_string = str_replace( '{APP_TITLE}', $this->config['app-meta']['title'], $processed_string );
		$processed_string = str_replace( '{ENDPOINT}',
			str_replace( $this->config['app-meta']['base_url'], '', parse_url($this->request->uri(), PHP_URL_PATH) ),
			$processed_string
		);
		$processed_string = str_replace( '{QUERY_STRING}', parse_url( $this->request->uri(), PHP_URL_QUERY), $processed_string );
		$processed_string = str_replace( '{URL_HASH}', parse_url( $this->request->uri(), PHP_URL_FRAGMENT), $processed_string );

		return $processed_string;
	}

	/**
	 * process_template
	 *
	 * Process an entire array/object as a template
	 * 
	 * @param mixed $unprocessed_data	The array/object to run through our template processor
	 * @access public
	 * @return array
	 */
	public function process_template( $unprocessed_data ) {
		// Copy array for the processed data
		$processed_data = (array) Util::object_to_array( $unprocessed_data );

		// Let's walk through each item, recursively
		array_walk_recursive(
			$processed_data,
			// Replace template placeholders
			function( &$raw ) {
				// If its a string
				if ( is_string($raw) ) {
					// Parse our string
					$raw = $this->parse( $raw );
				}
			}
		);

		return $processed_data;
	}

	/**
	 * get_status
	 *
	 * Get the status message of our response, or generate one
	 * 
	 * @access public
	 * @return string
	 */
	public function get_status() {
		// If a status is already set
		if ( isset($this->response->status) && !is_null($this->response->status) ) {
			return $this->response->status;
		}
		// If the status code has a corresponding message in our config
		elseif ( array_key_exists($this->response->code(), $this->config['rest']['status-codes']) ) {
			return $this->config['rest']['status-codes'][$this->response->code()];
		}
		// If there's a default
		elseif ( isset($this->config['rest']['defaults']['status']) ) {
			return $this->config['rest']['defaults']['status'];
		}
		else {
			return 'NULL';
		}
	}

	/**
	 * api_respond
	 *
	 * Handle all REST Api responses by combining our data from our response object into a consistent format
	 * 
	 * @access public
	 * @return void
	 */
	public function api_respond() {
		// Is a data response type not defined or not available?
		if ( !isset($this->response->type) || !array_key_exists($this->response->type, $this->config['rest']['mime-types']) ) {
			// Use a short-style ternary (PHP 5.3) to either grab the default or use this hard-coded default
			$this->response->type = $this->config['rest']['defaults']['data-type'] ?: 'json';
		}

		// Set our data response header
		$this->response->header( 'Content-Type', $this->config['rest']['mime-types'][$this->response->type] );

		// Set our allowed methods response header
		if ( !is_null( $this->response->possible_methods ) ) {
			$this->response->header( 'Allow', implode( ', ', $this->response->possible_methods ) );
		}

		// Send our access control headers
		$this->response->header( 'Access-Control-Allow-Headers', $this->config['rest']['http-access-control']['allow-headers']);
		$this->response->header( 'Access-Control-Allow-Methods', $this->config['rest']['http-access-control']['allow-methods']);
		$this->response->header( 'Access-Control-Allow-Origin', $this->config['rest']['http-access-control']['allow-origin']);

		// Let's build our response data
		$response_data = new stdClass();
		$response_data->meta = (object) array(
			'status_code' => (int) $this->response->code(),
			'status' => (string) $this->get_status(),
			'message' => (string) $this->response->message ?: (string) null,
			'more_info' => $this->response->more_info ?: (object) null,
		);
		$response_data->data = $this->response->data ?: (object) null;

		// Only add paging data if it exists
		if ( isset($this->response->paging) ) {
			$response_data->paging = $this->response->paging;
		}

		// If global template processing is turned on
		if ( $this->config['template']['global_template_processing'] ) {
			// If we only want to process our returned "data"
			if ( $this->config['template']['only_process_returned_data'] ) {
				// Set our data to be processed as the "data" property of our response data
				$raw_data = &$response_data->data;
			}
			else {
				// Otherwise, set our raw data as our entire response data object
				$raw_data = &$response_data;
			}

			// Process our data for templating
			$raw_data = $this->process_template( $raw_data );
		}

		// Let's encode our response based on our set type
		if ( $this->response->type == 'json' ) {
			$this->response->json( $response_data );
		}
		elseif ( $this->response->type == 'jsonp' ) {
			$this->response->json( $response_data, $this->config['rest']['defaults']['jsonp-padding'] ?: 'callback' );
		}
		// If all else fails, just var_dump
		else {
			var_dump( $response_data );
		}

		// Finally, should we include our execution time header?
		if ( defined( 'PAULUS_ALLOW_BENCHMARK_HEADER' ) === true ) {
			// If our benchmark header is set to true
			if ( $this->request->header( PAULUS_BENCHMARK_HEADER_NAME, false ) ) {
				// Set our benchmark header to the time difference between the app start and now
				$this->response->header(
					PAULUS_BENCHMARK_HEADER_NAME,
					( microtime( true) - PAULUS_START_TIME )
				);
			}
		}
		// If its been flagged to always show
		elseif ( defined( 'PAULUS_BENCHMARK_ALWAYS' ) === true ) {
			// Set our benchmark header to the time difference between the app start and now
			$this->response->header(
				PAULUS_BENCHMARK_HEADER_NAME,
				( microtime( true) - PAULUS_START_TIME )
			);
		}

		// We need to EXIT here, since we want this to be our last output
		exit;
	}

	/**
	 * abort
	 *
	 * Handle an abort in the API ( an error response )
	 * 
	 * @param int $error_code	The numeric code of the API error
	 * @param string $status 	The status message/slug of the API error
	 * @param string $message	The human readable error message
	 * @param mixed $more_info	The extraneous info to be returned with the error
	 * @access public
	 * @return void
	 */
	public function abort( $error_code, $status = null, $message = null, $more_info = null ) {
		// If no error code was provided
		if ( empty($error_code) || is_nan($error_code) ) {
			// No error code?! That's not restful!
			throw new Exception('No error code provided.');
		}
		else {
			// Set our HTTP status code and status
			$this->response->code( $error_code );
			$this->response->status = $status;

			// Set our message and more info for verbosity
			$this->response->message = $message;
			$this->response->more_info = $more_info;

			// Log the abort..ion :/
			$this->error_log( $message );

			// Respond restfully
			$this->api_respond();
		}
	}

	/**
	 * api_exception_handler
	 *
	 * Handle our ApiException interface exceptions for easy API error handling via exception throws
	 * 
	 * @param Exception $exception	The exception object instance
	 * @access public
	 * @return void
	 */
	public function api_exception_handler( $exception ) {
		// If we have a verbose exception
		if ( $exception instanceOf ApiVerboseException ) {
			$more_info = $exception->getMoreInfo();
		}
		else {
			$more_info = null;
		}

		// Let's handle the exception gracefully
		$this->abort(
			$exception->getCode(),
			$exception->getSlug(), // ApiException interface method
			$exception->getMessage(),
			$more_info
		);
	}

	/**
	 * endpoint_not_found
	 *
	 * Throws an EndpointNotFound error
	 * 
	 * @access public
	 * @return void
	 */
	public function endpoint_not_found() {
		// We didn't match their request, throw an exception
		throw new EndpointNotFound();
	}

	/**
	 * wrong_method
	 *
	 * Easily handle a response for when the wrong HTTP method is used to call on a route endpoint
	 * 
	 * @param mixed $possible_methods	The string or array containing the possible allowable methods for that route/endpoint
	 * @access public
	 * @return void
	 */
	public function wrong_method( $possible_methods = array() ) {
		// Any defined possible methods?
		if ( is_array($possible_methods) && count($possible_methods) > 0 ) {
			// Uppercase whatever was passed, for good measure
			$possible_methods = array_map( 'strtoupper', $possible_methods );

			// Add them to our response
			$this->response->possible_methods = $possible_methods;
		}
		elseif ( is_string($possible_methods) && !empty($possible_methods) ) {
			// Uppercase whatever was passed, for good measure
			$possible_methods = strtoupper($possible_methods);

			// Add it to our response as an array so that it stays consistent
			$this->response->possible_methods = array( $possible_methods );
		}

		// Also add the possible methods to our "more_info" for readability
		$more_info = array(
			'possible_methods' => $this->response->possible_methods
		);

		// We didn't match their request, create and throw an exception
		$wrong_method_exception = new WrongMethod();
		$wrong_method_exception->set_more_info( $more_info );
		throw $wrong_method_exception;
	}

	/**
	 * error_log
	 *
	 * Handle formatting and sending of API error logs
	 * 
	 * @param string $error_message 
	 * @access public
	 * @return boolean
	 */
	public function error_log( $error_message ) {
		// Log the abort..ion :/
		return error_log(
			'API Response Error: Code '
			. $this->response->code()
			. ' - '
			. $error_message
			. ' at '
			. $this->request->uri()
		);
	}

	/**
	 * run
	 *
	 * Run the app
	 * 
	 * @access public
	 * @return void
	 */
	public function run() {
		// Setup our default error route responders
		$this->setup_error_routes();

		// To always be RESTful, create a last-hit route responder to respond in our designated format ALWAYS
		Router::route( function( $request, $response, $service, $matches ) {
			// ALWAYS respond with our formatting function
			Router::app()->api_respond();
		});

		// Finally, call "dispatch" to have our App's Router route the request appropriately
		Router::dispatch(
			substr(
				$_SERVER['REQUEST_URI'],
				strlen( rtrim( $this->config['app-meta']['base_url'], '/' ) ) // Remove a potential trailing slash
			)
		);
	}

} // End class Paulus
