<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.3
 */

namespace Paulus\Tests;


use \PHPUnit_Framework_TestCase;

use \Paulus\Paulus;


/**
 * AbstractPaulusTest
 *
 * Base test class for PHP Unit testing
 * 
 * @uses PHPUnit_Framework_TestCase
 * @abstract
 * @package Paulus\Tests
 */
abstract class AbstractPaulusTest extends PHPUnit_Framework_TestCase {

	/*
	 * Declare properties
	 */
	protected $paulus_config;
	protected static $http_host = 'phpunit.paulus.dev';

	/**
	 * setUp
	 *
	 * Setup our test
	 * (runs before each test)
	 * 
	 * @access public
	 * @return void
	 */
	public function setUp() {
		// Grab our config
		global $paulus_config;
		$this->config = $paulus_config;

		$this->setTestHost( self::$http_host );
		$this->setTestURI( '/' );
		$this->setTestMethod( 'GET' );
		$this->setQueryString( '' );
	}

	/**
	 * setTestHost
	 *
	 * Set our fake HTTP host name for testing
	 * 
	 * @param string $http_host 
	 * @access protected
	 * @return void
	 */
	protected function setTestHost( $http_host ) {
		return $_SERVER[ 'HTTP_HOST' ] = $http_host;
	}

	/**
	 * setTestURI
	 *
	 * Set our fake test URI (to match against)
	 * 
	 * @param string $uri 
	 * @access protected
	 * @return void
	 */
	protected function setTestURI( $uri ) {
		return $_SERVER[ 'REQUEST_URI' ] = $uri;
	}

	/**
	 * setTestMethod
	 *
	 * Set our fake HTTP Method for testing/matching
	 * 
	 * @param string $http_method 
	 * @access protected
	 * @return void
	 */
	protected function setTestMethod( $http_method ) {
		return $_SERVER[ 'REQUEST_METHOD' ] = strtoupper( $http_method );
	}

	/**
	 * setQueryString
	 *
	 * Set our fake URI/request's query string
	 * 
	 * @param string $query
	 * @access protected
	 * @return void
	 */
	protected function setQueryString( $query ) {
		if ( is_array( $query ) || is_object( $query ) ) {
			$query = http_build_query( $query );
		}

		return $_SERVER[ 'QUERY_STRING' ] = $query;
	}

	/**
	 * getJSONOutput
	 *
	 * Gets the json output of our ran app
	 * 
	 * @param mixed $output
	 * @access protected
	 * @return mixed
	 */
	protected function getJSONOutput( $output = null ) {
		if ( is_null( $output ) ) {
			$output = ob_get_contents();
		}

		$decodedOutput = json_decode( $output );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( 'JSON output invalid' );
		}

		return $decodedOutput;
	}

	/**
	 * runAndGetOutput
	 *
	 * Run's our app and captures our output, returning our API result
	 * 
	 * @param Paulus $app_context If no context is passed, a new Paulus app 
	 * will be instanciated for us
	 * @access protected
	 * @return mixed
	 */
	protected function runAndGetOutput( $app_context = null ) {
		if ( is_null( $app_context ) ) {
			// New Paulus app
			$app_context = new Paulus( $this->config );
		}

		// Start a NEW output buffer (so we can grab JUST our app's output)
		ob_start();

		$app_context->run();

		// Grab our output from our buffer
		$output = $this->getJSONOutput( ob_get_contents() );

		// End and clean our buffer, so it doesn't show up in our Unit Test output
		ob_end_clean();

		return $output;
	}

} // End class AbstractPaulusTest
