<?php

// Let's define our API's response functions for use in other routes/endpoints
respond( function( $request, $response, $app ) use ( $config ) {
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
		$response_data = (object) array(
			'status_code' => $response->code(),
			'status' => $response->get_status(),
			'data' => $response->data ?: new stdClass,
		);

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

			// Respond restfully
			$response->api_respond();
		}
	};

	// Handle exceptions RESTfully
	$response->onError( function($response, $error_message) {
		// Log the error
		error_log( $error_message );

		// Let's handle the exception gracefully
		$response->abort( 500, 'EXCEPTION_THROWN', $error_message );
	});
});

