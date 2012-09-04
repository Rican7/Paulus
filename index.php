<?php

/*****************************************/
//          PHP Api Boilerplate          //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

/*
 * Let's load our configuration files
 */
$config = array();
$config_files = array(
	'external-libs',
	//'database',
);

foreach( $config_files as $file ) {
	// Include the file
	require_once( 'configs/' . $file . '.php');

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
		require_once( 'external-libs/' . $lib_path );
	}
}
