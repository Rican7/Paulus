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
	//'database',
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
