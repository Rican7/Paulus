<?php

namespace Paulus;

// BaseController abstract class for Paulus
// To be extended by controllers in the controllers directory
abstract class BaseController {

	// Declare properties
	private $request;
	private $response;
	private $service;
	private $app;

	// Constructor
	public function __construct( &$request, &$response, &$service, &$app ) {
		// Let's make our Klein objects available to our class
		$this->request = $request;
		$this->response = $response;
		$this->service = $service;

		// Make the application object easily accessible
		$this->app = $app;
	}

} // End class BaseController
