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

} // End class BaseController
