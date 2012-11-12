<?php

namespace Paulus;

// Explicitly require our routing library (especially since it can't be autoloaded)
require_once BASE_DIR . 'external-libs/klein/klein.php';

// Use an empty route (catch-all) to initialize our Router class
respond( function( $request, $response, $app ) {
	Router::__init__( $request, $response, $app );
});

// Router class that controls our routing library (Klein)
class Router {

	// Declare properties
	private static $request;
	private static $response;
	private static $app;

	// Initializer
	public static function __init__( &$request, &$response, &$app ) {
		// Let's make our Klein objects available to our class
		self::$request = $request;
		self::$response = $response;
		self::$app = $app;
	}

	/*
	 * Public getters
	 */
	public static function request() {
		return self::$request;
	}
	public static function response() {
		return self::$request;
	}
	public static function app() {
		return self::$request;
	}
}
