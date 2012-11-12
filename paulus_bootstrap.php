<?php

/*****************************************/
//          PHP Api Boilerplate          //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

// Which classes to use
use	\Paulus\Config,
	\Paulus\Router;


// If we haven't disabled our autoloader
if ( !defined( 'PAULUS_INTERNAL_AUTOLOAD_DISABLED' ) ) {

	// Create an autoloader for Paulus
	spl_autoload_register( function( $class ) {
		// Convert the namespace to a sub-directory path
		if ( strpos( $class, '\\' ) !== false) {
			$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
		}

		// Define our file path
		$file_path = PAULUS_LIB_DIR . $class . '.php';

		if ( is_readable($file_path) ) {
			require_once( $file_path );
		}
	});
}

/*
 * Let's load our configuration files
 */
$config = Config::instance(); // Create our config

// Explicitly require our routing library (especially since it can't be autoloaded)
require_once BASE_DIR . 'external-libs/klein/klein.php';

// Use an empty route (catch-all) to initialize our Router class and App
respond( function( $request, $response, $app ) use ( $config ) {
	// Create our App!
	$api_app = new Paulus( $config, $request, $response );

	// Initialize our router
	Router::__init__( $request, $response, $api_app );
});


/*
 * Load our external libraries explicitly
 * ( Great for the libraries that can't be auto-loaded)
 */
if ( isset($config['external-libs']) ) {
	foreach( $config['external-libs'] as $lib_path ) {
		require_once( BASE_DIR . 'external-libs/' . $lib_path );
	}
}

// If we haven't disabled our autoloader
if ( !defined( 'PAULUS_AUTOLOAD_DISABLED' ) ) {

	// Create an autoloader and autoload all of our classes/libraries
	spl_autoload_register( function( $class ) use ( $config ) {
		// Convert the namespace to a sub-directory path
		if ( strpos( $class, '\\' ) !== false) {
			$class = str_replace( '\\', '/', $class );
		}

		// Loop through each autoload-directory
		foreach( $config['autoload-directories'] as $autoload_directory ) {
			// Define our file path
			$file_path = BASE_DIR . $autoload_directory . $class . '.php';

			if ( is_readable($file_path) ) {
				require_once( $file_path );

				// We don't want to include multiple versions of the same class,
				// so let's just stop once we find one that exists
				break;
			}
		}
	});
}


/*
 * Let's setup ActiveRecord and pass it our configuration
 * ( PHP Closure's can use the "use" keyword to allow the usage of an out-of-closure scope var )
 */
// Check to see if ActiveRecord exists... the app/developer might not want to use it
if ( class_exists( 'ActiveRecord\Config', false ) ) { // Set to false to not try and autoload the class
	ActiveRecord\Config::initialize( function($cfg) use ( $config ) {
		// Set the directory of our data models
		$cfg->set_model_directory( $config['database']['model_directory'] );

		// Set our connection configuration
		$cfg->set_connections( $config['database']['connections'] );

		// Set our default connection
		$cfg->set_default_connection( $config['database']['default_connection'] );
	});
}


// Grab all of our routes
if ( $config['routing']['load_all_automatically'] ) {
	// Define our routes directory
	$route_dir = BASE_DIR . 'routes/';

	// Get an array of all of the files in the routes directory
	$found_routes = scandir( $route_dir );

	// Loop through each found route
	foreach( $found_routes as $route ) {
		// Is the route a file?
		if ( is_file( $route_dir . $route ) && ( strpos( $route, '_' ) !== 0 ) ) {
			// Define our endpoint base
			$route_base_url = '/' . basename( $route, '.php' );

			// Instanciate the route's controller... but do it as a responder so it only instanciate's what is needed for that matched response. :)
			Router::with( $route_base_url, function() use ( $route_base_url ) {
				Router::route( function( $request, $respond, $app ) use ( $route_base_url ) {
					// Instanciate the route's controller
					$app->new_route_controller( $route_base_url );
				});
			});

			// Include our routes from namespaced, separate files
			Router::with( $route_base_url, $route_dir . $route );
		}
	}
}
else {
	// Grab all of our manually assigned routes
	foreach( $config['routing']['routes'] as $route ) {
		// Define our endpoint base and include path
		$route_base_url = '/' . $route;
		$route_path = BASE_DIR . 'routes/' . $route . '.php';

		// Instanciate the route's controller... but do it as a responder so it only instanciate's what is needed for that matched response. :)
		Router::with( $route_base_url, function() use ( $route_base_url ) {
			Router::route( function( $request, $respond, $app ) use ( $route_base_url ) {
				// Instanciate the route's controller
				Router::app()->new_route_controller( $route_base_url );
			});
		});

		// Include our routes from namespaced, separate files
		Router::with( $route_base_url, $route_path );
	}
}

// 404 - We didn't match a route
Router::route( '404', function( $request, $response, $app ) {
	// Respond with a 404 error... we didn't match their request
	Router::app()->abort( 404, NULL, 'Unable to find the endpoint you requested' );
});

// To always be RESTful, respond in our designated format ALWAYS
Router::route( function( $request, $response, $app, $matches ) {
	// ALWAYS respond with our formatting function
	Router::app()->api_respond();
});

// Finally, call "dispatch" to have Klein route the request appropriately
Router::dispatch(
	substr(
		$_SERVER['REQUEST_URI'],
		strlen( rtrim( $config['app-meta']['base_url'], '/' ) ) // Remove a potential trailing slash
	)
);

