<?php

/*****************************************/
//          PHP Api Boilerplate          //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

// Set our base directory here
define( 'BASE_DIR', __DIR__ . '/' );

/*
 * Let's load our configuration files
 */
require_once( BASE_DIR . 'libs/Config.php' ); // Load our configuration class
$config = new Config(); // Create our config


/*
 * Load our external libraries
 */
if (isset($config['external-libs'])) {
	foreach( $config['external-libs'] as $lib_path ) {
		require_once( BASE_DIR . 'external-libs/' . $lib_path );
	}
}


/*
 * Create an autoloader and autoload all of our internal classes/libraries
 */
spl_autoload_register( function($class) use ( $config ) {
	// Convert the namespace to a sub-directory path
	if ( strpos( $class, '\\' ) !== false) {
		$class = str_replace( '\\', '/', $class );
	}

	// Loop through each autoload-directory
	foreach( $config['autoload-directories'] as $autoload_directory ) {
		// Define our file path
		$file_path = BASE_DIR . $autoload_directory . $class . '.php';

		if ( file_exists($file_path) ) {
			require_once( $file_path );

			// We don't want to include multiple versions of the same class,
			// so let's just stop once we find one that exists
			break;
		}
	}
});

/*
 * Let's setup ActiveRecord and pass it our configuration
 * ( PHP Closure's can use the "use" keyword to allow the usage of an out-of-closure scope var )
 */
ActiveRecord\Config::initialize( function($cfg) use ( $config ) {
	// Set the directory of our data models
	$cfg->set_model_directory( BASE_DIR . 'models' );

	// Set our connection configuration
	$cfg->set_connections( $config['database']['connections'] );

	// Set our default connection
	$cfg->set_default_connection( $config['database']['default_connection'] );
});

/*
 * Let's use Klein's router to lazily load some objects and make them available elsewhere
 * ( PHP Closure's can use the "use" keyword to allow the usage of an out-of-closure scope var )
 */
respond( function( $request, $response, $app ) use ( $config ) {
	// Let's give all of our routes easy access to our configuration definitions
	$app->config = $config;
});

// Include all of our rest functions for use with the API
require_once( BASE_DIR . 'routes/_rest.php' );

// Include all of our generic routing functions for use with every route
require_once( BASE_DIR . 'routes/_generic.php' );

// Grab all of our routes
foreach( $config['routes'] as $route ) {
	// Define our endpoint base and include path
	$route_base_url = '/' . $route;
	$route_path = BASE_DIR . 'routes/' . $route . '.php';

	with( $route_base_url, $route_path );
}

// To always be RESTful, respond in our designated format ALWAYS
respond( function( $request, $response, $app, $matches ) {
	// If none of our routes were matched
	if ( $matches < 1 ) {
		// Respond with a 404 error... we didn't find it
		$response->abort( 404, NULL, 'Unable to find the endpoint you requested' );
	}

	// ALWAYS respond with our formatting function
	$response->api_respond();
});

// Finally, call "dispatch" to have Klein route the request appropriately
dispatch( $_SERVER['PATH_INFO'] );

