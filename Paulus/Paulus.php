<?php

namespace Paulus;

use	\Paulus\Config,
	\Paulus\Router,
	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Interfaces\ApiVerboseException,
	\Paulus\Exceptions\EndpointNotFound,
	\Paulus\Exceptions\WrongMethod,
	\stdClass;

// Main Paulus class
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

	// Constructor
	public function __construct( $config = null, $request = null, $response = null, $service = null ) {
		// Either grab the passed config or use our Singleton Config
		$this->config = $config ?: Config::instance();

		// Grab our Router's variables and make quick/easy references to them
		$this->request = &$request;
		$this->response = &$response;
		$this->service = &$service;

		// Initialize some response properties
		$this->init_response_properties();

		// Setup our exception handler
		$this->setup_exception_handler();
	}

	// Function to initialize the response properties
	private function init_response_properties() {
		// Initialze some properties
		$this->response->status = null;
		$this->response->possible_methods = null;
		$this->response->message = null;
		$this->response->more_info = null;
		$this->response->data = null;
		$this->response->paging = null;
	}

	// Function to check if our current controller has a callable method and return it if it does
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

	// Function to setup our Paulus exception handler
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

	// Function to get our controller's route responder
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

	// Function for instanciating a route controller if one exists
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

	// Function to process a string as a template variable
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

	// Function to process an entire array as a template 
	public function process_template( $unprocessed_data ) {
		// Quickly create a function to convert the object to an array
		// Source: http://goo.gl/uTLGf
		function objectToArray($d) {
			if (is_object($d)) {
				// Gets the properties of the given object
				// with get_object_vars function
				$d = get_object_vars($d);
			}

			if (is_array($d)) {
				/*
				 * Return array converted to object
				 * Using __FUNCTION__ (Magic constant)
				 * for recursive call
				 */
				return array_map(__FUNCTION__, $d);
			}
			else {
				// Return array
				return $d;
			}
		}

		// Copy array for the processed data
		$processed_data = (array) objectToArray( $unprocessed_data );

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

	// Function to get the status message given a status code
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

	// Function to handle all REST Api responses
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

	// Function to handle an abort in the API ( an error response )
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

	// Function to handle our ApiException interface exceptions
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

	// Function to handle an endpoint not being found
	public function endpoint_not_found() {
		// We didn't match their request, throw an exception
		throw new EndpointNotFound();
	}

	// Function to easily handle a response for when the wrong method is used to call on a route endpoint
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

	// Function to handle formatting and sending of API error logs
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

} // End class Paulus
