<?php

// Api class
// Main class for structuring API logic
class Api {

	// Declare properties
	private $request;
	private $response;
	private $service;

	// Constructor
	public function __construct( &$request, &$response, &$service ) {
		// Let's make our Klein objects available to our class
		$this->request = $request;
		$this->response = $response;
		$this->service = $service;
	}

} // End class Api
