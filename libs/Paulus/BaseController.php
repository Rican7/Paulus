<?php

namespace Paulus;

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
				// Let's abort with the response data
				$this->app->abort( 400, 'INVALID_API_PARAMETERS', 'The posted data did not pass validation' );
			}
			else {
				// Prepare our data for response
				$this->response->data = $result_data;
			}

		}
		else {
			// The response is null, we should abort
			$this->app->abort( 404, null, 'Object does not exist.' );
		}
	}

} // End class BaseController
