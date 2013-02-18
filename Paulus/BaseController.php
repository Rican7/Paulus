<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.2
 */

namespace Paulus;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\InvalidApiParameters,
	\Paulus\Exceptions\ObjectNotFound;

/**
 * BaseController
 *
 * BaseController abstract class for Paulus
 * To be extended by controllers in the controllers directory
 * 
 * @abstract
 * @package		Paulus
 */
abstract class BaseController {

	/*
	 * Declare properties
	 */
	protected	$config;

	// Klein objects
	protected	$request;
	protected	$response;
	protected	$service;

	// Reference to our parent application
	protected	$app;


	/**
	 * __construct
	 *
	 * A controller's default/parent constructor
	 * 
	 * @param mixed $request	The router's request object
	 * @param mixed $response	The router's response object
	 * @param mixed $service 	The router's service object
	 * @param mixed $app		Reference to the current application context
	 * @access public
	 * @return void
	 */
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

	/**
	 * route_respond
	 *
	 * Route responder for filtering routes directed through a controller
	 * 
	 * @param mixed $result_data	The data returned from a route's "response"
	 * callback to be evaluated in this method
	 * @access public
	 * @return void
	 */
	public function route_respond( $result_data ) {
		// Logic depends on the contents/state of result_data
		if ( !is_null( $result_data ) ) {

			// True response
			if ( $result_data === true ) {
				// True case WITHOUT any returned data
			}
			elseif ( $result_data === false ) {
				// Throw an exception
				throw new InvalidApiParameters();
			}
			else {
				// Prepare our data for response
				$this->response->data = $result_data;
			}

		}
		else {
			// The response is null, throw an exception
			throw new ObjectNotFound();
		}
	}

	/**
	 * exception_handler
	 *
	 * Function to handle exceptions from the API
	 * 
	 * @param string $error_message	The error message of the exception
	 * @param string $error_type	The exception class
	 * @param Exception $exception	The actual exception object itself
	 * @access public
	 * @return void
	 */
	public function exception_handler( $error_message, $error_type, $exception ) {
		// Log the error
		$this->app->error_log( $error_type . ' - ' .$error_message );

		// Let's do different things, based on the type of the error

		// Paulus - Handle all of our "ApiException"s
		if ( $exception instanceOf ApiException ) {
			// Handle our ApiException interface exceptions from Paulus
			$this->app->api_exception_handler( $exception );
		}
		// ActiveRecord - RecordNotFound
		elseif ( $error_type === 'ActiveRecord\RecordNotFound' ) {
			// Let the api_respond method handle it
			$this->app->abort( 404, null, 'Object does not exist.' );
		}
		// ActiveRecord - DatabaseException
		elseif ( $error_type === 'ActiveRecord\DatabaseException' ) {
			// Let's handle the exception gracefully
			$this->app->abort( 502, null, 'There was an error connecting to or retrieving from the Database' );
		}
		// Any other exceptions
		else {
			// Let's handle the exception gracefully
			$this->app->abort( 500, 'EXCEPTION_THROWN', $error_message );
		}
	}

} // End class BaseController
