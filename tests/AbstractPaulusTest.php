<?php

namespace Paulus\Tests;

use \PHPUnit_Framework_TestCase;

use \Paulus\Paulus;

abstract class AbstractPaulusTest extends PHPUnit_Framework_TestCase {

	/**
	 * Class properties
	 */
	protected $paulus_config;
	protected static $http_host = 'phpunit.paulus.dev';

	public function setUp() {
		// Grab our config
		global $paulus_config;
		$this->config = $paulus_config;

		$this->setTestHost( self::$http_host );
		$this->setTestURI( '/' );
		$this->setTestMethod( 'GET' );
		$this->setQueryString( '' );
	}

	protected function setTestHost( $http_host ) {
		return $_SERVER[ 'HTTP_HOST' ] = $http_host;
	}

	protected function setTestURI( $uri ) {
		return $_SERVER[ 'REQUEST_URI' ] = $uri;
	}

	protected function setTestMethod( $http_method ) {
		return $_SERVER[ 'REQUEST_METHOD' ] = strtoupper( $http_method );
	}

	protected function setQueryString( $query ) {
		if ( is_array( $query ) || is_object( $query ) ) {
			$query = http_build_query( $query );
		}

		return $_SERVER[ 'QUERY_STRING' ] = $query;
	}

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

}
