<?php
// Load our autoloader, and add our Test class namespace
$autoloader = require( __DIR__ . '/../vendor/autoload.php' );
$autoloader->add( 'Paulus\Tests', __DIR__ );

// Setup our test configuration
$paulus_config = array(
    'database' => array(
        'model_directory' => '',
    ),
    'routing' => array(
        'load_all_automatically' => false,
    ),
	'rest' => array(
		'exit_after_rest_response' => false,
	),
);


// Overwrite Klein's "_Headers" class for PHPUnit testing
// (PHPUnit doesn't like header output...)
class HeadersEcho extends _Headers {
    public function header($key, $value = null) {
        //echo $this->_header($key, $value) . "\n";
    }
}
_Request::$_headers = _Response::$_headers = new HeadersEcho;
