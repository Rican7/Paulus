<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.10.0
 */

namespace Paulus;

/**
 * Router 
 *
 * Router class that controls our routing library (Klein)
 * 
 * @package		Paulus
 */
class Router {

	/*
	 * Declare properties
	 */
	protected static $request;
	protected static $response;
	protected static $service;
	protected static $app;

	/**
	 * __init__
	 *
	 * Static initializer
	 * 
	 * @param mixed $request	The router's request object
	 * @param mixed $response	The router's response object
	 * @param mixed $service 	The router's service object
	 * @param mixed $app		Reference to the current application context
	 * @static
	 * @access public
	 * @return void
	 */
	public static function __init__( &$request, &$response, &$service, &$app ) {
		// Let's make our Klein objects available to our class
		self::$request = $request;
		self::$response = $response;
		self::$service = $service;
		self::$app = $app;
	}

	/**
	 * request
	 *
	 * Provides quick access to the Router's request object
	 * 
	 * @static
	 * @access public
	 * @return _Request
	 */
	public static function request() {
		return self::$request;
	}

	/**
	 * response
	 *
	 * Provides quick access to the Router's response object
	 * 
	 * @static
	 * @access public
	 * @return _Response
	 */
	public static function response() {
		return self::$response;
	}

	/**
	 * app
	 *
	 * Provides quick access to the Router's app object
	 * 
	 * @static
	 * @access public
	 * @return _App
	 */
	public static function app() {
		return self::$app;
	}

	/**
	 * smart_parameters
	 *
	 * Quick method to more intelligently deal with optional parameters in routes
	 * 
	 * @param mixed $method		The HTTP method used in the request
	 * @param mixed $route		The matched "route" found by the routing engine
	 * @param mixed $callback	The callback function definition
	 * @static
	 * @access private
	 * @return void
	 */
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

	/**
	 * route
	 *
	 * Route method to push to our Klein/Routing library
	 * 
	 * @param mixed $method		The HTTP method used in the request
	 * @param mixed $route		The matched "route" found by the routing engine
	 * @param mixed $callback	The callback function definition
	 * @static
	 * @access public
	 * @return callable
	 */
	public static function route( $method, $route = null, $callback = null ) {
		// Pass off to our Klein/Routing library
		return respond( $method, $route, $callback );
	}

	/**
	 * channel
	 *
	 * Special Route method
	 * Checks to see if we have a callable in our app/current controller
	 * and (if it does have one) channels the callback through that callable
	 * 
	 * @param mixed $method		The HTTP method used in the request
	 * @param mixed $route		The matched "route" found by the routing engine
	 * @param mixed $callback	The callback function definition
	 * @static
	 * @access public
	 * @return callable
	 */
	public static function channel( $method, $route = null, $callback = null ) {
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

	/**
	 * with
	 *
	 * With Klein method alias
	 * 
	 * @param mixed $namespace	The namespace grouping of the passed route definitions
	 * @param mixed $routes		The defined route callable or route file
	 * @static
	 * @access public
	 * @return void
	 */
	public static function with( $namespace, $routes ) {
		// Pass off to our Klein/Routing library
		return with( $namespace, $routes );
	}

	/**
	 * dispatch
	 *
	 * Dispatch Klein method alias
	 * 
	 * @param string $uri			The parseable URI with which to match against
	 * @param string $req_method	The HTTP method used in the request
	 * @param array $params			The request parameters
	 * @param boolean $capture		Whether or not the output should be captured by the buffer
	 * @static
	 * @access public
	 * @return boolean | void
	 */
	public static function dispatch( $uri = null, $req_method = null, array $params = null, $capture = false ) {
		// Pass off to our Klein/Routing library
		return dispatch( $uri, $req_method, $params, $capture );
	}

	/*
	 * Route aliases for ease of use
	 */

	// TODO: Convert these to "inherit" the docs

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
