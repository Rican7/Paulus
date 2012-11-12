<?php

use \Paulus\Config;

// Main Paulus class
class Paulus {

	// Declare properties
	private $config;
	private $router;
	private $status;
	private $possible_methods;
	private $message;
	private $more_info;
	private $data;
	private $paging;

	// Constructor
	public function __construct( $config = null, $router = null ) {
		// Either grab the passed config or use our Singleton Config
		$this->config = $config ?: Config::instance();

		// Either grab the passed router_class, or default to our built-in router
		// $this->router = $router ?: '';
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
			str_replace( $this->config['app-meta']['base_url'], '', parse_url($request->uri(), PHP_URL_PATH) ),
			$processed_string
		);
		$processed_string = str_replace( '{QUERY_STRING}', parse_url( $request->uri(), PHP_URL_QUERY), $processed_string );
		$processed_string = str_replace( '{URL_HASH}', parse_url( $request->uri(), PHP_URL_FRAGMENT), $processed_string );

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
					$raw = $response->parse( $raw );
				}
			}
		);

		return $processed_data;
	}

	// Function to get the status message given a status code
	private function get_status() {
		// If a status is already set
		if ( isset($response->status) && !is_null($response->status) ) {
			return $response->status;
		}
		// If the status code has a corresponding message in our config
		elseif ( array_key_exists($response->code(), $this->config['rest']['status-codes']) ) {
			return $this->config['rest']['status-codes'][$response->code()];
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
	private function api_respond() { 
		// Is a data response type not defined or not available?
		if ( !isset($response->type) || !array_key_exists($response->type, $this->config['rest']['mime-types']) ) {
			// Use a short-style ternary (PHP 5.3) to either grab the default or use this hard-coded default
			$response->type = $this->config['rest']['defaults']['data-type'] ?: 'json';
		}

		// Set our data response header
		$response->header( 'Content-Type', $this->config['rest']['mime-types'][$response->type] );

		// Set our allowed methods response header
		$response->header( 'Allow', implode( ', ', ( $response->possible_methods ?: $this->config['rest']['defaults']['allowed-methods'] ) ) );

		// Send our access control headers
		$response->header( 'Access-Control-Allow-Headers', $this->config['rest']['http-access-control']['allow-headers']);
		$response->header( 'Access-Control-Allow-Methods', $this->config['rest']['http-access-control']['allow-methods']);
		$response->header( 'Access-Control-Allow-Origin', $this->config['rest']['http-access-control']['allow-origin']);

		// Let's build our response data
		$response_data = new stdClass();
		$response_data->meta = (object) array(
			'status_code' => (int) $response->code(),
			'status' => (string) $response->get_status(),
			'message' => (string) $response->message ?: (string) null,
			'more_info' => $response->more_info ?: null,
		);
		$response_data->data = $response->data ?: null;

		// Only add paging data if it exists
		if ( isset($response->paging) ) {
			$response_data->paging = $response->paging;
		}

		// If global template processing is turned on
		if ( $this->config['template']['global_template_processing'] ) {
			// Process our data for templating
			$response_data = $response->process_template( $response_data );
		}

		// Let's encode our response based on our set type
		if ( $response->type == 'json' ) {
			$response->json( $response_data );
		}
		elseif ( $response->type == 'jsonp' ) {
			$response->json( $response_data, $this->config['rest']['defaults']['jsonp-padding'] ?: 'callback' );
		}
		// If all else fails, just var_dump
		else {
			var_dump( $response_data );
		}

		// We need to EXIT here, since we want this to be our last output
		exit;
	}

	// Function to handle an abort in the API ( an error response )
	private function abort( $error_code, $status = null, $message = null, $more_info = null ) {
		// If no error code was provided
		if ( empty($error_code) || is_nan($error_code) ) {
			// No error code?! That's not restful!
			throw new Exception('No error code provided.');
		}
		else {
			// Set our HTTP status code and status
			$response->code( $error_code );
			$response->status = $status;

			// Set our message and more info for verbosity
			$response->message = $message;
			$response->more_info = $more_info;

			// Log the abort..ion :/
			$response->error_log( $message );

			// Respond restfully
			$response->api_respond();
		}
	}

	// Function to easily handle a response for when the wrong method is used to call on a route endpoint
	private function wrong_method( $possible_methods = array() ) {
			// Set our HTTP status code and status
			$response->code( 405 );

			// Set our message as our response data
			$response->message = 'The wrong method was called on this endpoint.';

			// Any defined possible methods?
			if ( is_array($possible_methods) && count($possible_methods) > 0 ) {
				// Uppercase whatever was passed, for good measure
				$possible_methods = array_map( 'strtoupper', $possible_methods );

				// Add them to our response
				$response->possible_methods = $possible_methods;
			}
			elseif ( is_string($possible_methods) && !empty($possible_methods) ) {
				// Uppercase whatever was passed, for good measure
				$possible_methods = strtoupper($possible_methods);

				// Add it to our response as an array so that it stays consistent
				$response->possible_methods = array( $possible_methods );
			}

			// Also add the possible methods to our "more_info" for readability
			$response->more_info->possible_methods = $response->possible_methods;

			// Log the abort..ion :/
			$response->error_log( $response->message );

			// Abort
			$response->api_respond();
	}

	// Handle exceptions RESTfully
	// $response->onError( function( $response, $error_message, $error_type, $exception ) {
	// 	// Let's see if we have a current controller instanciated
	// 	if ( is_object( $app->controller ) && !is_null( $app->controller ) ) {

	// 		// Define a callable method as an array
	// 		$callable = array( $app->controller, 'exception_handler' ); // Give the controller class and the name of the method

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
	// 	$response->error_log( $error_message );

	// 	// Let's handle the exception gracefully
	// 	$response->abort( 500, 'EXCEPTION_THROWN', $error_message );
	// })

	// Function to handle formatting and sending of API error logs
	private function error_log( $error_message ) {
		// Log the abort..ion :/
		return error_log(
			'API Response Error: Code '
			. $response->code()
			. ' - '
			. $error_message
			. ' at '
			. $request->uri()
		);
	}

} // End class Paulus
