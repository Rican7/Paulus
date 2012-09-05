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
$config = array();
$config_files = array(
	'app-meta',
	'external-libs',
	'database',
	'routes',
);

foreach( $config_files as $file ) {
	// Include the file
	require_once( BASE_DIR . 'configs/' . $file . '.php');

	// Set the file's returned configuration in a namespaced key
	$config[ $file ] = $load_config();

	// Garbage collect
	unset( $load_config );
}


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
spl_autoload_register(function($class) {
	// Define our file path
	$file_path = BASE_DIR . 'libraries/' . $class . '.php';

	if ( file_exists($file_path) ) {
		require_once( $file_path );
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

// Grab all of our routes
foreach( $config['routes'] as $route ) {
	// Define our endpoint base and include path
	$route_base_url = '/' . $route;
	$route_path = BASE_DIR . 'routes/' . $route . '.php';

	with( $route_base_url, $route_path );
}

// Finally, call "dispatch" to have Klein route the request appropriately
dispatch();

