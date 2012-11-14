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

	// Quick method to more intelligently deal with optional parameters in routes
	private static function smart_parameters( &$method, &$route, &$callback ) {
		// Mirror Klein's native behavior
		if ( is_callable( $method ) ) {
			$callback = $method;
			$method = $route = null;
		}
		elseif ( is_callable( $route ) ) {
			$callback = $route;
			$route = $method;
			$method = null;
		}
	}

	// Route method
	// To push to our Klein/Routing library
	public static function route( $method, $route = null, $callback = null ) {
		// Pass off to our Klein/Routing library
		return respond( $method, $route, $callback );
	}

	// Special Route method
	// Checks to see if we have a callable in our app/current controller
	public static function hub( $method, $route = null, $callback = null ) {
		// Make sure that our parameters are what they say they are
		self::smart_parameters( $method, $route, $callback );

		// Route our new callback
		return self::route(
			$method,
			$route,
			function( $request, $response, $service, $matched, $methods_matched ) use ( $callback ) {
				// Get the callable from our app/current controller
				$responder_callable = self::app()->get_route_responder();

				// Call the callable with our callback as the argument (What is this sorcery?!)
				$responder_callable(
					$callback( $request, $response, $service, $matched, $methods_matched )
				);
			}
		);
	}

	// With Klein method alias
	public static function with( $namespace, $routes ) {
		// Pass off to our Klein/Routing library
		return with( $namespace, $routes );
	}

	// Dispatch Klein method alias
	public static function dispatch( $uri = null, $req_method = null, array $params = null, $capture = false ) {
		// Pass off to our Klein/Routing library
		return dispatch( $uri, $req_method, $params, $capture );
	}

	/*
	 * Route aliases for ease of use
	 */

	// All-method route
	public static function any( $route = null, $callback = null ) {
		return self::route( $route, $callback );
	}

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

} // End class Router
