<?php

namespace Paulus;

// Router class that controls our routing library (Klein)
class Router {

	// Declare properties
	protected static $request;
	protected static $response;
	protected static $service;
	protected static $app;

	// Initializer
	public static function __init__( &$request, &$response, &$service, &$app ) {
		// Let's make our Klein objects available to our class
		self::$request = $request;
		self::$response = $response;
		self::$service = $service;
		self::$app = $app;
	}

	/*
	 * Public getters
	 */
	public static function request() {
		return self::$request;
	}
	public static function response() {
		return self::$response;
	}
	public static function app() {
		return self::$app;
	}

	// Route method
	// To push to our Klein/Routing library
	public static function route( $method, $route = null, $callback = null ) {
		// Mirror klein's ease of use by making multiple params optional
		// if ( is_callable( $method ) ) {
		// 	$callback = $method;
		// 	$method = $route = null;
		// 	$count_match = false;
		// }
		// elseif ( is_callable( $route ) ) {
		// 	$callback = $route;
		// 	$route = $method;
		// 	$method = null;
		// }

		// Pass off to our Klein/Routing library
		return respond( $method, $route, $callback );
	}

	// With Klein method alias
	public static function with( $namespace, $routes ) {
		return with( $namespace, $routes );
	}

	// Dispatch Klein method alias
	public static function dispatch( $uri = null, $req_method = null, array $params = null, $capture = false ) {
		return dispatch( $uri, $req_method, $params, $capture );
	}

	/*
	 * Route aliases for ease of use
	 */

	// GET route
	public static function get( $route = null, $callback = null ) {
		return self::route( 'GET', $route, $callback );
	}

	// POST route
	public static function post( $route = null, $callback = null ) {
		return self::route( 'POST', $route, $callback );
	}

	// PUT route
	public static function put( $route = null, $callback = null ) {
		return self::route( 'PUT', $route, $callback );
	}

	// DELETE route
	public static function delete( $route = null, $callback = null ) {
		return self::route( 'DELETE', $route, $callback );
	}

	// HEAD route
	public static function head( $route = null, $callback = null ) {
		return self::route( 'HEAD', $route, $callback );
	}

	// OPTIONS route
	public static function options( $route = null, $callback = null ) {
		return self::route( 'OPTIONS', $route, $callback );
	}

}
