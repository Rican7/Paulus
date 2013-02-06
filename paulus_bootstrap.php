<?php

/*****************************************/
//                Paulus                 //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

// Which classes to use
use	\Paulus\AutoLoader,
	\Paulus\Config,
	\Paulus\Paulus,
	\Paulus\Router;


// Load our autoloader... since it can't be autoloaded itself. ... WTF?!?!
require_once( PAULUS_AUTOLOADER_LOCATION );

// Instanciate our AutoLoader (regardless of settings)
$autoloader = new AutoLoader();

// If we haven't disabled our autoloader
if ( defined( 'PAULUS_INTERNAL_AUTOLOAD_DISABLED' ) !== true ) {

	// Register our internal autoloader (Paulus' autoloader)
	$autoloader->register_internal_autoloader();
}

// If we haven't disabled our autoloader
if ( defined( 'PAULUS_AUTOLOAD_DISABLED' ) !== true ) {

	// Register our external library autoloader (and pass our configuration)
	$autoloader->register_external_autoloader();
}

/*
 * Let's load our configuration files
 */
$config = Config::instance();

// Explicitly require our routing library (especially since it can't be autoloaded)
require_once( PAULUS_ROUTER_LOCATION );

// Use an empty route (catch-all) to initialize our Router class and App
respond( function( $request, $response, $service ) use ( $config ) {
	// Create our App!
	$app = new Paulus( $config, $request, $response, $service );

	// Initialize our router
	Router::__init__( $request, $response, $service, $app );

	// Only pass our "app" to the service register if we've configured it to do so
	if ( $config['routing']['pass_app_to_service'] ) {
		// Register our app as a persistent service, for ease of use/accessibility
		$service->app = $app;
	}
});


/*
 * Load our external libraries explicitly
 * ( Great for the libraries that can't be auto-loaded)
 */
if ( isset( $config['external-libs'] ) ) {
	// Load our external libraries explicitly
	$autoloader->explicitly_load_externals();
}


/*
 * Let's setup ActiveRecord and pass it our configuration
 */
// Check to see if ActiveRecord exists... the app/developer might not want to use it
if ( class_exists( 'ActiveRecord\Config', false ) ) { // Set to false to not try and autoload the class
	ActiveRecord\Config::initialize( function( $cfg ) use ( $config ) {
		// Set the directory of our data models
		$cfg->set_model_directory( $config['database']['model_directory'] );

		// Set our connection configuration
		$cfg->set_connections( $config['database']['connections'] );

		// Set our default connection
		$cfg->set_default_connection( $config['database']['default_connection'] );
	});
}


// Load and define all of our routes
$autoloader->load_routes();


// 404 - We didn't match a route
Router::route( '404', function( $request, $response, $service ) {
	// We didn't match the endpoint/route
	Router::app()->endpoint_not_found();
});

// 405 - We didn't match a route, but one WOULD have matched with a different method
Router::route( '405', function( $request, $response, $service, $matches, $methods ) {
	// We didn't match the right method for the endpoint/route
	Router::app()->wrong_method( $methods );
});

// To always be RESTful, respond in our designated format ALWAYS
Router::route( function( $request, $response, $service, $matches ) {
	// ALWAYS respond with our formatting function
	Router::app()->api_respond();
});

// Finally, call "dispatch" to have our App's Router route the request appropriately
Router::dispatch(
	substr(
		$_SERVER['REQUEST_URI'],
		strlen( rtrim( $config['app-meta']['base_url'], '/' ) ) // Remove a potential trailing slash
	)
);
