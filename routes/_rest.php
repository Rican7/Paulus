<?php

// Let's define our API's response functions for use in other routes/endpoints
respond( function( $request, $response, $app ) use ( $config ) {

	// Function to process a string as a template variable
	$response->parse = function( $unprocessed_string ) use ( $request, $config ) {
		// Copy the string so we can refer to both the processed and original
		$processed_string = $unprocessed_string;

		// Loop through each template key in the user config and parse
		foreach ( $config['template']['keys'] as $key => $val ) {
			$processed_string = str_replace( '{'. $key .'}', $val, $processed_string );
		}

		// Now for some framework level template keys/values
		$processed_string = str_replace( '{BASE_URL}', $config['app-meta']['base_url'], $processed_string );
		$processed_string = str_replace( '{APP_URL}', $config['app-meta']['app_url'], $processed_string );
		$processed_string = str_replace( '{APP_TITLE}', $config['app-meta']['title'], $processed_string );
		$processed_string = str_replace( '{ENDPOINT}',
			str_replace( $config['app-meta']['base_url'], '', parse_url($request->uri(), PHP_URL_PATH) ),
			$processed_string
		);
		$processed_string = str_replace( '{QUERY_STRING}', parse_url( $request->uri(), PHP_URL_QUERY), $processed_string );
		$processed_string = str_replace( '{URL_HASH}', parse_url( $request->uri(), PHP_URL_FRAGMENT), $processed_string );

		return $processed_string;
	};

	// Function to process an entire array as a template 
	$response->process_template = function( $unprocessed_data ) use ( $response ) {
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
			function( &$raw ) use ( $response ) {
				// If its a string
				if ( is_string($raw) ) {
					// Parse our string
					$raw = $response->parse( $raw );
				}
			}
		);

		return $processed_data;
	};

	// Function to get the status message given a status code
	$response->get_status = function() use ( $response, $config ) {
		// If a status is already set
		if ( isset($response->status) ) {
			return $response->status;
		}
		// If the status code has a corresponding message in our config
		elseif ( array_key_exists($response->code(), $config['rest']['status-codes']) ) {
			return $config['rest']['status-codes'][$response->code()];
		}
		// If there's a default
		elseif ( isset($config['rest']['defaults']['status']) ) {
			return $config['rest']['defaults']['status'];
		}
		else {
			return 'NULL';
		}
	};

	// Function to handle all REST Api responses
	$response->api_respond = function() use ( $request, $response, $app, $config ) { 
		// Is a data response type not defined or not available?
		if ( !isset($response->type) || !array_key_exists($response->type, $config['rest']['mime-types']) ) {
			// Use a short-style ternary (PHP 5.3) to either grab the default or use this hard-coded default
			$response->type = $config['rest']['defaults']['data-type'] ?: 'json';
		}

		// Set our data response header
		$response->header( 'Content-Type', $config['rest']['mime-types'][$response->type] );

		// Send our access control headers
		$response->header( 'Access-Control-Allow-Headers', $config['rest']['http-access-control']['allow-headers']);
		$response->header( 'Access-Control-Allow-Methods', $config['rest']['http-access-control']['allow-methods']);
		$response->header( 'Access-Control-Allow-Origin', $config['rest']['http-access-control']['allow-origin']);

		// Let's build our response data
		$response_data = new stdClass();
		$response_data->meta = (object) array(
			'status_code' => $response->code(),
			'status' => $response->get_status(),
		);
		$response_data->data = $response->data ?: new stdClass();

		// Only add paging data if it exists
		if ( $response->paging ) {
			$response_data->paging = $response->paging;
		}

		// If global template processing is turned on
		if ( $config['template']['global_template_processing'] ) {
			// Process our data for templating
			$response_data = $response->process_template( $response_data );
		}

		// Let's encode our response based on our set type
		if ( $response->type == 'json' ) {
			$response->json( $response_data );
		}
		elseif ( $response->type == 'jsonp' ) {
			$response->json( $response_data, $config['rest']['defaults']['jsonp-padding'] ?: 'callback' );
		}
		// If all else fails, just var_dump
		else {
			var_dump( $response_data );
		}

		// We need to EXIT here, since we want this to be our last output
		exit;
	};

	// Function to handle an abort in the API ( an error response )
	$response->abort = function( $error_code, $status = null, $message = null ) use ( $response ) {
		// If no error code was provided
		if ( empty($error_code) || is_nan($error_code) ) {
			// No error code?! That's not restful!
			throw new Exception('No error code provided.');
		}
		else {
			// Set our HTTP status code and status
			$response->code( $error_code );
			$response->status = $status;

			// If the message isn't null
			if ( !is_null($message) ) {
				// Set our message as our response data
				$response->data = (object) array(
					'message' => $message,
				);
			}

			// Log the abort..ion :/
			$response->error_log( $message );

			// Respond restfully
			$response->api_respond();
		}
	};

	// Function to easily handle a response for when the wrong method is used to call on a route endpoint
	$response->wrong_method = function( $possible_methods = array() ) use ( $response ) {
			// Set our HTTP status code and status
			$response->code( 405 );

			// Set our message as our response data
			$response->data = (object) array(
				'message' => 'The wrong method was called on this endpoint.',
			);

			// Any defined possible methods?
			if ( is_array($possible_methods) && count($possible_methods) > 0 ) {
				// Uppercase whatever was passed, for good measure
				$possible_methods = array_map( 'strtoupper', $possible_methods );

				// Add them to our response
				$response->data->possible_methods = $possible_methods;
			}
			elseif ( is_string($possible_methods) && !empty($possible_methods) ) {
				// Uppercase whatever was passed, for good measure
				$possible_methods = strtoupper($possible_methods);

				// Add it to our response as an array so that it stays consistent
				$response->data->possible_methods = array( $possible_methods );
			}

			// Log the abort..ion :/
			$response->error_log( $response->data->message );

			// Abort
			$response->api_respond();
	};

	// Handle exceptions RESTfully
	$response->onError( function($response, $error_message) {
		// Log the error
		$response->error_log( $error_message );

		// Let's handle the exception gracefully
		$response->abort( 500, 'EXCEPTION_THROWN', $error_message );
	});

	// Function to handle formatting and sending of API error logs
	$response->error_log = function( $error_message ) use ( $request, $response ) {
		// Log the abort..ion :/
		return error_log(
			'API Response Error: Code '
			. $response->code()
			. ' - '
			. $error_message
			. ' at '
			. $request->uri()
		);
	};

});

