<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.4
 */

namespace Paulus\Tests;


use \Paulus\Paulus;
use \Paulus\Router;


/**
 * RouterTest 
 * 
 * @uses \Paulus\AbstractPaulusTest
 * @package 
 */
class RouterTest extends AbstractPaulusTest {

	public function testBasicRoute() {
		// Setup our request
		$this->setTestURI( '/basic/route' );

		$paulus = new Paulus( $this->config );

        // Define our endpoint
        Router::route( '/basic/route', function ( $request, $response ) {
			$response->data = array(
				'success' => true,
			);
        });

		$output = $this->runAndGetOutput( $paulus );

		// Assertions
		$this->assertEquals( 200,               $output->meta->status_code );
		$this->assertEquals( 'OK',              $output->meta->status );
		$this->assertEquals( array(),   (array) $output->meta->more_info );
		$this->assertEquals( true,              $output->data->success );
		$this->assertEquals( '',                $output->meta->message );
	}

	public function testMethodMatch() {
		// Setup our request
		$this->setTestURI( '/' );
		$this->setTestMethod( 'DELETE' );

		$paulus = new Paulus( $this->config );

        // Define our endpoint
        Router::delete( '/', function ( $request, $response ) {
			$response->data = array(
				'success' => true,
			);
        });

		$output = $this->runAndGetOutput( $paulus );

		// Assertions
		$this->assertEquals( 200,               $output->meta->status_code );
		$this->assertEquals( 'OK',              $output->meta->status );
		$this->assertEquals( array(),   (array) $output->meta->more_info );
		$this->assertEquals( true,              $output->data->success );
		$this->assertEquals( '',                $output->meta->message );
	}

	public function testMethodCatchallRoute() {
		// Setup our request
		$this->setTestURI( '/' );
		$this->setTestMethod( 'PUT' );

		$paulus = new Paulus( $this->config );

        // Define our endpoint
        Router::put( function ( $request, $response ) {
			$response->data = array(
				'putcatchall' => true,
			);
        });

		$output = $this->runAndGetOutput( $paulus );

		// Assertions
		$this->assertEquals( true, $output->data->putcatchall );
	}

} // End class RouterTest
