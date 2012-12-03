<?php

/*****************************************/
//                Paulus                 //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

// First things first... get our init time
define( 'PAULUS_START_TIME', microtime( true ) );


/*
 * Set some low-level configurations that we
 * may want to change before loading our
 * application's "config"
 */

// Set our base directory here
define( 'BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR );


// Quick function to make directory defining easier
function dir_rel_path( $dir_name ) {
	return BASE_DIR . $dir_name . DIRECTORY_SEPARATOR;
}


// Set some Paulus directories here
define( 'PAULUS_CONFIG_DIR', dir_rel_path( 'configs' ) );
define( 'PAULUS_LIB_DIR', dir_rel_path( 'libs' ) );
define( 'PAULUS_EXTERNAL_LIB_DIR', dir_rel_path( 'external-libs' ) );
define( 'PAULUS_ROUTES_DIR', dir_rel_path( 'routes' ) );

// Set some important include/require locations explicitly here
define( 'PAULUS_AUTOLOADER_LOCATION', PAULUS_LIB_DIR . 'Paulus/AutoLoader.php' );
define( 'PAULUS_ROUTER_LOCATION', PAULUS_EXTERNAL_LIB_DIR . 'klein/klein.php' );

// Define our benchmark header (for both request and response here)
define( 'PAULUS_BENCHMARK_HEADER_NAME', 'X-Script-Benchmark' );

// Optional settings here (uncomment them to enable)
// define( 'PAULUS_INTERNAL_AUTOLOAD_DISABLED', null );
// define( 'PAULUS_AUTOLOAD_DISABLED', null );
// define( 'PAULUS_ALLOW_BENCHMARK_HEADER', null );
// define( 'PAULUS_BENCHMARK_ALWAYS', null );


// Let's bootstrap all the things
require_once( BASE_DIR . 'paulus_bootstrap.php' );
