<?php

use	\Paulus\Config,
	\Paulus\Router;

// Main Paulus class
class Paulus {

	/*
	 * Declare properties
	 */
	private $config; // Configuration array
	private $controller; // Let's keep track of a potential route controller

	// Routing variable references
	private $request;
	private $response;
	private $service;

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
	private function parse( $unprocessed_string ) {
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
	private function process_template( $unprocessed_data ) {
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
		$processed_data = objectToArray( $unprocessed_data );

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
		$this->response->header( 'Allow', implode( ', ', ( $this->response->possible_methods ?: $this->config['rest']['defaults']['allowed-methods'] ) ) );

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
			'more_info' => $this->response->more_info ?: null,
		);
		$response_data->data = $this->response->data ?: null;

		// Only add paging data if it exists
		if ( isset($this->response->paging) ) {
			$response_data->paging = $this->response->paging;
		}

		// If global template processing is turned on
		if ( $this->config['template']['global_template_processing'] ) {
			// Process our data for templating
			$response_data = $this->process_template( $response_data );
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

	// Function to easily handle a response for when the wrong method is used to call on a route endpoint
	public function wrong_method( $possible_methods = array() ) {
			// Set our HTTP status code and status
			$this->response->code( 405 );

			// Set our message as our response data
			$this->response->message = 'The wrong method was called on this endpoint.';

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
			$this->response->more_info->possible_methods = $this->response->possible_methods;

			// Log the abort..ion :/
			$this->error_log( $this->response->message );

			// Abort
			$this->api_respond();
	}

	// Handle exceptions RESTfully
	// $this->response->onError( function( $response, $error_message, $error_type, $exception ) {
	// 	// Let's see if we have a current controller instanciated
	// 	if ( is_object( $this->controller ) && !is_null( $this->controller ) ) {

	// 		// Define a callable method as an array
	// 		$callable = array( $this->controller, 'exception_handler' ); // Give the controller class and the name of the method

	// 		// Check if the current controller has a callable error handler of its own
	// 		$check_callable = is_callable( $callable, false ); // Make sure it actually exists

	// 		// If we found a callable handler
	// 		if ( $check_callable ) {
	// 			// Call the found handler
	// 			return call_user_func_array(
	// 				$callable, // The array of the callable
	// 				array( $error_message, $error_type, $exception ) // Arguments
	// 			);
	// 		}
	// 	}

	// 	// Log the error
	// 	$this->error_log( $error_message );

	// 	// Let's handle the exception gracefully
	// 	$this->abort( 500, 'EXCEPTION_THROWN', $error_message );
	// })

	// Function to handle formatting and sending of API error logs
	private function error_log( $error_message ) {
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
