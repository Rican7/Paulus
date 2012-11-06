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
if ( isset($config['external-libs']) ) {
	foreach( $config['external-libs'] as $lib_path ) {
		require_once( BASE_DIR . 'external-libs/' . $lib_path );
	}
}


/*
 * Create an autoloader and autoload all of our internal classes/libraries
 * ( PHP Closure's can use the "use" keyword to allow the usage of an out-of-closure scope var )
 */
spl_autoload_register( function( $class ) use ( $config ) {
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
// Check to see if ActiveRecord exists... the app/developer might not want to use it
if ( class_exists( 'ActiveRecord\Config', false ) ) { // Set to false to not try and autoload the class
	ActiveRecord\Config::initialize( function($cfg) use ( $config ) {
		// Set the directory of our data models
		$cfg->set_model_directory( BASE_DIR . 'models' );

		// Set our connection configuration
		$cfg->set_connections( $config['database']['connections'] );

		// Set our default connection
		$cfg->set_default_connection( $config['database']['default_connection'] );
	});
}


/*
 * Let's use Klein's router to lazily load some objects and make them available elsewhere
 * ( PHP Closure's can use the "use" keyword to allow the usage of an out-of-closure scope var )
 */
respond( function( $request, $response, $app ) use ( $config ) {
	// Let's give all of our routes easy access to our configuration definitions
	$app->config = $config;

	// Let's keep track of a potential route controller
	$app->controller = null;

	// Define a function for instanciating a route controller if one exists
	$app->new_route_controller = function( $namespace ) use ( $request, $response, $app, $config ) {
		// Do we want to auto start our controllers?
		if ( $config['routing']['auto_start_controllers'] ) {
			// Let's get our class name from the namespace
			$name_parts = explode( '/', $namespace ); 

			// Let's convert our url endpoint name to a legit classname
			$name_parts = array_map(
				function( $string ) {
					$string = str_replace( '-', '_', $string ); // Turn all hyphens to underscores
					$string = str_replace( '_', ' ', $string ); // Turn all underscores to spaces (for easy casing)
					$string = ucwords( $string ); // Uppercase each first letter of each "word"
					$string = str_replace( ' ', '', $string ); // Remove spaces. BOOM! Zend-compatible camel casing

					return $string;
				},
				$name_parts
			);

			// Create a callable classname
			$classname = implode( '\\', $name_parts );
			$class = $config['routing']['controller_base_namespace'] . $classname;

			// Does the class exist? (Autoload it if its not loaded/included yet)
			if ( class_exists( $class, true ) ) {
				// Instanciate the controller and keep an easy reference to it
				$app->controller = new $class( $request, $response, $app );
			}
		}

		return null;
	};
});

// Include all of our rest functions for use with the API
require_once( BASE_DIR . 'routes/_rest.php' );

// Include all of our generic routing functions for use with every route
require_once( BASE_DIR . 'routes/_generic.php' );

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
			with( $route_base_url, function() use ( $route_base_url ) {
				respond( function( $request, $respond, $app ) use ( $route_base_url ) {
					// Instanciate the route's controller
					$app->new_route_controller( $route_base_url );
				});
			});

			// Include our routes from namespaced, separate files
			with( $route_base_url, $route_dir . $route );
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
		with( $route_base_url, function() use ( $route_base_url ) {
			respond( function( $request, $respond, $app ) use ( $route_base_url ) {
				// Instanciate the route's controller
				$app->new_route_controller( $route_base_url );
			});
		});

		// Include our routes from namespaced, separate files
		with( $route_base_url, $route_path );
	}
}

// 404 - We didn't match a route
respond( '404', function( $request, $response, $app ) {
	// Respond with a 404 error... we didn't match their request
	$response->abort( 404, NULL, 'Unable to find the endpoint you requested' );
});

// To always be RESTful, respond in our designated format ALWAYS
respond( function( $request, $response, $app, $matches ) {
	// ALWAYS respond with our formatting function
	$response->api_respond();
});

// Finally, call "dispatch" to have Klein route the request appropriately
dispatch(
	substr(
		$_SERVER['REQUEST_URI'],
		strlen( rtrim( $config['app-meta']['base_url'], '/' ) ) // Remove a potential trailing slash
	)
);

