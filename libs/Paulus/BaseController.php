<?php

namespace Paulus;

use	\Paulus\Exceptions\InvalidApiParameters,
	\Paulus\Exceptions\ObjectNotFound;

// BaseController abstract class for Paulus
// To be extended by controllers in the controllers directory
abstract class BaseController {

	/*
	 * Declare properties
	 */
	protected	$config;

	// Klein objects
	private	$request;
	private	$response;
	private	$service;

	// Reference to our parent application
	private	$app;


	// Constructor
	public function __construct( &$request, &$response, &$service, &$app ) {
		// Let's make our Klein objects available to our class
		$this->request = $request;
		$this->response = $response;
		$this->service = $service;

		// Make the application object easily accessible
		$this->app = $app;

		// Let's keep a quick reference to our config for accessibility
		$this->config = &$app->config;
	}

	// Route responder for filtering routes directed through a controller
	public function route_respond( $result_data ) {
		// Logic depends on the contents/state of result_data
		if ( !is_null( $result_data ) ) {

			// True response
			if ( $result_data === true ) {
				// True case WITHOUT any returned data
			}
			elseif ( $result_data === false ) {
				// Throw an exception
				throw new InvalidApiParameters( 'The posted data did not pass validation' );
			}
			else {
				// Prepare our data for response
				$this->response->data = $result_data;
			}

		}
		else {
			// The response is null, throw an exception
			throw new ObjectNotFound( 'Object does not exist.' );
		}
	}

	// Function to handle exceptions from the API
	public function exception_handler( $error_message, $error_type, $exception ) {
		// Log the error
		$this->app->error_log( $error_type . ' - ' .$error_message );

		// Let's do different things, based on the type of the error

		// Paulus - Handle all of our "ApiException"s
		if ( strstr( $error_type, 'Paulus\Exceptions' ) !== false ) {
			// Define a slug variable for passing to our API response
			$error_slug = null;

			// Let's try and get the slug
			try {
				// This should work, if the exception implements our interface correctly
				$error_slug = $exception->getSlug();
			}
			catch ( Exception $e ) {
				// Pass the new exception right back to this handler
				$this->exception_handler( $e->getMessage(), get_class( $e ), $e );
			}

			// Let's handle the exception gracefully
			$this->app->abort(
				$exception->getCode(),
				$error_slug,
				$exception->getMessage()
			);
		}
		// ActiveRecord - RecordNotFound
		elseif ( $error_type === 'ActiveRecord\RecordNotFound' ) {
			// Let the api_respond method handle it
			$this->app->abort( 404, null, 'Object does not exist.' );
		}
		// ActiveRecord - DatabaseException
		elseif ( $error_type === 'ActiveRecord\DatabaseException' ) {
			// Let's handle the exception gracefully
			$this->app->abort( 502, null, 'There was an error connecting to or retrieving from the Database' );
		}
		// Any other exceptions
		else {
			// Let's handle the exception gracefully
			$this->app->abort( 500, 'EXCEPTION_THROWN', $error_message );
		}
	}

} // End class BaseController
