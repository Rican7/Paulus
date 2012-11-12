<?php

// Api class
// Main class for structuring API logic
class Api {

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

} // End class Api
