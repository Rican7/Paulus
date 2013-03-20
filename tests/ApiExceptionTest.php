<?php
// Require our base class
require_once( __DIR__ . '/AbstractPaulusTest.php' );

use \Paulus\Paulus;
use \Paulus\Router;

class ApiExceptionTest extends AbstractPaulusTest {

	public function testNotFoundException() {
		// Setup our request
		$this->setTestURI( '/this-endpoint-doesnt-exist' );

		$output = $this->runAndGetOutput();

		// Assertions
		$this->assertEquals( 404,               $output->meta->status_code );
		$this->assertEquals( 'NOT_FOUND',       $output->meta->status );
		$this->assertEquals( array(),   (array) $output->meta->more_info );
		$this->assertEquals( array(),   (array) $output->data );

		$this->assertStringStartsWith( 'Unable to',   $output->meta->message );
	}

	public function testWrongMethodException() {
		// Setup our request
		$this->setTestURI( '/this-endpoint-requires-POST-or-DELETE' );
		$this->setTestMethod( 'GET' );

        // Define our endpoint
        Router::route(
            array( 'POST', 'DELETE' ),
            '/this-endpoint-requires-POST-or-DELETE',
            function () {
                echo 'fail';
            }
        );

		$output = $this->runAndGetOutput();

		// Assertions
		$this->assertEquals( 405,                           $output->meta->status_code );
		$this->assertEquals( 'METHOD_NOT_ALLOWED',          $output->meta->status );
		$this->assertEquals( array( 'POST', 'DELETE' ),     $output->meta->more_info->possible_methods );
		$this->assertEquals( array(),               (array) $output->data );

		$this->assertStringStartsWith( 'The wrong method',   $output->meta->message );
	}

}
