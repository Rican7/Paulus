<?php

// Api class
// Main class for structuring API logic
class Api {

	// Declare properties
	private $request;
	private $response;
	private $app;

	// Constructor
	public function __construct( &$request, &$response, &$app ) {
		// Let's make our Klein objects available to our class
		$this->request = $request;
		$this->response = $response;
		$this->app = $app;
	}

} // End class Api
